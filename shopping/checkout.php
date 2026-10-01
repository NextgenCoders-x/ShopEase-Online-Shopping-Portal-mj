<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
    $_SESSION['redirect_url'] = 'checkout.php';
    include('includes/header-new.php');
    ?>
    <div class="se-container se-section text-center" style="padding: 100px 20px;">
        <i class="bi bi-lock" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 15px;">Login Required</h2>
        <p style="color: var(--gray-600); max-width: 500px; margin: 0 auto 30px auto; font-size: 16px;">
            You must login or create an account before purchasing this product.
        </p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="login.php" class="se-btn se-btn-primary se-btn-lg">Login</a>
            <a href="login.php" class="se-btn se-btn-outline se-btn-lg">Create Account</a>
        </div>
    </div>
    <?php
    include('includes/footer-new.php');
    exit;
}

if(empty($_SESSION['cart']) && empty($_SESSION['buy_now'])) {
    setFlash('warning', 'Your cart is empty.');
    header('location: my-cart.php');
    exit;
}

$uid = $_SESSION['id'];

// Get User details
$userQuery = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_array($userQuery);

// Handle Place Order
if(isset($_POST['place_order'])) {
    // Server-side: block guest order submission even via direct POST
    if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
        $_SESSION['redirect_url'] = 'checkout.php';
        header('location: login.php');
        exit;
    }
    
    $shipName = $_POST['shipName'];
    $shipEmail = $_POST['shipEmail'];
    $shipMobile = $_POST['shipMobile'];
    $shipAddress = $_POST['shipAddress'];
    $shipCity = $_POST['shipCity'];
    $shipState = $_POST['shipState'];
    $shipPincode = $_POST['shipPincode'];
    $paymentMethod = $_POST['paymentMethod'];
    
    // Save address as default if requested
    if(isset($_POST['saveAddress'])) {
        $upd = mysqli_prepare($con, "UPDATE users SET shippingAddress=?, shippingCity=?, shippingState=?, shippingPincode=? WHERE id=?");
        mysqli_stmt_bind_param($upd, "ssssi", $shipAddress, $shipCity, $shipState, $shipPincode, $uid);
        mysqli_stmt_execute($upd);
    }
    
    mysqli_begin_transaction($con);
    try {
        // Recalculate totals from database prices to avoid tampering
        $subtotal = 0;
        $shipping = 0;
        
        $cartSource = isset($_SESSION['buy_now']) ? $_SESSION['buy_now'] : $_SESSION['cart'];
        
        $sql = "SELECT id, productPrice, shippingCharge, stock_quantity, productName, productImage1 FROM products WHERE id IN (";
        foreach($cartSource as $id => $value) {
            $sql .= $id . ",";
        }
        $sql = substr($sql, 0, -1) . ")";
        $result = mysqli_query($con, $sql);
        
        $orderItems = [];
        
        while($row = mysqli_fetch_array($result)) {
            $pid = $row['id'];
            $qty = intval($cartSource[$pid]['quantity']);
            
            // Check stock
            if($row['stock_quantity'] < $qty) {
                throw new Exception("Product '" . $row['productName'] . "' is out of stock or insufficient quantity available.");
            }
            
            $itemTotal = $row['productPrice'] * $qty;
            $subtotal += $itemTotal;
            $shipping += $row['shippingCharge'];
            
            $orderItems[] = [
                'pid' => $pid,
                'name' => $row['productName'],
                'image' => $row['productImage1'],
                'price' => $row['productPrice'],
                'qty' => $qty,
                'total' => $itemTotal
            ];
        }
        
        $grandTotal = $subtotal + $shipping;
        $orderNumber = generateOrderNumber();
        $orderStatus = 'In Process';
        
        // Insert order
        $insertOrder = mysqli_prepare($con, "INSERT INTO orders (orderNumber, userId, subtotal, shippingCharge, grandTotal, paymentMethod, paymentStatus, orderStatus, shipName, shipEmail, shipMobile, shipAddress, shipCity, shipState, shipPincode) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Fixed type string from 15 to 14 characters
        mysqli_stmt_bind_param($insertOrder, "sidddsssssssss", $orderNumber, $uid, $subtotal, $shipping, $grandTotal, $paymentMethod, $orderStatus, $shipName, $shipEmail, $shipMobile, $shipAddress, $shipCity, $shipState, $shipPincode);
        mysqli_stmt_execute($insertOrder);
        
        $orderId = mysqli_insert_id($con);
        
        // Insert Order Items and Update Stock
        $insertItem = mysqli_prepare($con, "INSERT INTO order_items (orderId, productId, productName, productImage, productPrice, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $updateStock = mysqli_prepare($con, "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        
        foreach($orderItems as $item) {
            mysqli_stmt_bind_param($insertItem, "iissdid", $orderId, $item['pid'], $item['name'], $item['image'], $item['price'], $item['qty'], $item['total']);
            mysqli_stmt_execute($insertItem);
            
            mysqli_stmt_bind_param($updateStock, "ii", $item['qty'], $item['pid']);
            mysqli_stmt_execute($updateStock);
        }
        
        // Add order history tracking entry
        $track = mysqli_prepare($con, "INSERT INTO ordertrackhistory(orderId, status, remark) VALUES(?, 'In Process', 'Order Placed successfully.')");
        mysqli_stmt_bind_param($track, "i", $orderId);
        mysqli_stmt_execute($track);
        
        mysqli_commit($con);
        
        // Success
        if(isset($_SESSION['buy_now'])) {
            unset($_SESSION['buy_now']);
        } else {
            unset($_SESSION['cart']);
        }
        header("location: order-success.php?id=$orderId");
        exit;
        
    } catch(Exception $e) {
        mysqli_rollback($con);
        setFlash('error', $e->getMessage());
        header('location: checkout.php');
        exit;
    }
}

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-cart.php">Cart</a> <span>/</span> <span class="current">Checkout</span>
    </div>
    
    <div class="se-checkout-steps">
        <div class="se-checkout-step completed"><div class="num"><i class="bi bi-check"></i></div> Cart</div>
        <div class="se-checkout-step active"><div class="num">2</div> Checkout</div>
        <div class="se-checkout-step"><div class="num">3</div> Confirmation</div>
    </div>
    
    <form method="post">
        <div class="grid-2" style="grid-template-columns: 2fr 1fr; gap: var(--space-xl);">
            
            <!-- Shipping Form -->
            <div>
                <div class="se-card" style="margin-bottom: var(--space-lg);">
                    <div class="se-card-header">
                        Shipping Information
                    </div>
                    <div class="se-card-body">
                        <div class="grid-2">
                            <div class="se-form-group">
                                <label>Full Name <span class="required">*</span></label>
                                <input type="text" name="shipName" class="se-input" required value="<?php echo e($user['name']); ?>">
                            </div>
                            <div class="se-form-group">
                                <label>Mobile Number <span class="required">*</span></label>
                                <input type="tel" name="shipMobile" class="se-input" required value="<?php echo e($user['contactno']); ?>" pattern="[0-9]{10}">
                            </div>
                        </div>
                        
                        <div class="se-form-group">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" name="shipEmail" class="se-input" required value="<?php echo e($user['email']); ?>">
                        </div>
                        
                        <div class="se-form-group">
                            <label>Address <span class="required">*</span></label>
                            <textarea name="shipAddress" class="se-textarea" required style="min-height: 80px;"><?php echo e($user['shippingAddress']); ?></textarea>
                        </div>
                        
                        <div class="grid-2">
                            <div class="se-form-group">
                                <label>City <span class="required">*</span></label>
                                <input type="text" name="shipCity" class="se-input" required value="<?php echo e($user['shippingCity']); ?>">
                            </div>
                            <div class="se-form-group">
                                <label>State <span class="required">*</span></label>
                                <input type="text" name="shipState" class="se-input" required value="<?php echo e($user['shippingState']); ?>">
                            </div>
                        </div>
                        
                        <div class="se-form-group">
                            <label>Pincode <span class="required">*</span></label>
                            <input type="text" name="shipPincode" class="se-input" required value="<?php echo e($user['shippingPincode']); ?>" pattern="[0-9]+">
                        </div>
                        
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; user-select: none;">
                            <input type="checkbox" name="saveAddress" value="1" checked> Save this address to my profile
                        </label>
                    </div>
                </div>
                
                <div class="se-card">
                    <div class="se-card-header">Payment Method</div>
                    <div class="se-card-body">
                        <label style="display: flex; align-items: center; gap: 12px; padding: 15px; border: 1px solid var(--primary); border-radius: var(--radius-md); background: var(--primary-50); cursor: pointer; margin-bottom: 10px;">
                            <input type="radio" name="paymentMethod" value="COD" checked>
                            <div>
                                <div style="font-weight: 600;">Cash on Delivery (COD)</div>
                                <div style="font-size: 12px; color: var(--gray-600);">Pay when your order arrives</div>
                            </div>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 12px; padding: 15px; border: 1px solid var(--gray-200); border-radius: var(--radius-md); cursor: not-allowed; opacity: 0.6;">
                            <input type="radio" name="paymentMethod" value="Card" disabled>
                            <div>
                                <div style="font-weight: 600;">Credit / Debit Card</div>
                                <div style="font-size: 12px; color: var(--gray-600);">Coming soon (Requires Stripe/Razorpay)</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="se-card" style="height: fit-content; position: sticky; top: calc(var(--header-height) + 20px);">
                <div class="se-card-header">Order Summary</div>
                <div class="se-card-body">
                    
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px; padding-right: 5px;">
                    <?php
                    $totalprice = 0;
                    $totalshipping = 0;
                    $cartSource = isset($_SESSION['buy_now']) ? $_SESSION['buy_now'] : $_SESSION['cart'];
                    
                    $sql = "SELECT * FROM products WHERE id IN (";
                    foreach($cartSource as $id => $value) $sql .= $id . ",";
                    $sql = substr($sql, 0, -1) . ")";
                    $query = mysqli_query($con, $sql);
                    
                    while($row = mysqli_fetch_array($query)) {
                        $qty = $cartSource[$row['id']]['quantity'];
                        $totalprice += ($row['productPrice'] * $qty);
                        $totalshipping += $row['shippingCharge'];
                    ?>
                        <div style="display: flex; gap: 10px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-100);">
                            <div style="width: 50px; height: 50px; background: var(--gray-50); border-radius: 4px; padding: 3px;">
                                <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo e($row['productImage1']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 600; line-height: 1.3; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo e($row['productName']); ?>
                                </div>
                                <div style="font-size: 12px; color: var(--gray-500);">
                                    Qty: <?php echo $qty; ?> x <?php echo formatPrice($row['productPrice']); ?>
                                </div>
                            </div>
                            <div style="font-weight: 600; font-size: 13px; text-align: right;">
                                <?php echo formatPrice($row['productPrice'] * $qty); ?>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--gray-600);">
                        <span>Subtotal</span>
                        <span style="font-weight: 600; color: var(--gray-900);"><?php echo formatPrice($totalprice); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: var(--gray-600);">
                        <span>Shipping</span>
                        <span style="font-weight: 600; color: var(--gray-900);">
                            <?php echo $totalshipping > 0 ? formatPrice($totalshipping) : 'Free'; ?>
                        </span>
                    </div>
                    
                    <div style="border-top: 1px dashed var(--gray-300); margin: 15px 0;"></div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 18px; font-weight: 700;">
                        <span>Grand Total</span>
                        <span style="color: var(--primary);"><?php echo formatPrice($totalprice + $totalshipping); ?></span>
                    </div>
                    
                    <button type="submit" name="place_order" class="se-btn se-btn-primary se-btn-full se-btn-lg">
                        Place Order <i class="bi bi-check2-circle"></i>
                    </button>
                </div>
            </div>
            
        </div>
    </form>
</div>

<?php include('includes/footer-new.php'); ?>
