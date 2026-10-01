<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Update Cart Quantity
if(isset($_POST['update_cart'])) {
    if(!empty($_SESSION['cart'])) {
        foreach($_POST['quantity'] as $key => $val) {
            $val = intval($val);
            if($val <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
        setFlash('success', 'Your cart has been updated.');
    }
}

// Remove Item from Cart
if(isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    if(isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        setFlash('success', 'Product removed from cart.');
    }
    header('location: my-cart.php');
    exit;
}

// Checkout Redirect
if(isset($_POST['checkout'])) {
    header('location: checkout.php');
    exit;
}

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">Shopping Cart</span>
    </div>
    
    <h2 class="se-section-title" style="margin-bottom: var(--space-xl);">My Shopping Cart</h2>
    
    <?php if(empty($_SESSION['cart'])): ?>
        <div class="se-empty">
            <i class="bi bi-cart-x" style="font-size: 64px; color: var(--gray-300);"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="index.php" class="se-btn se-btn-primary se-btn-lg mt-3">Start Shopping</a>
        </div>
    <?php else: ?>
        <form method="post">
            <div class="grid-2" style="grid-template-columns: 2.5fr 1fr; gap: var(--space-xl);">
                <!-- Cart Items -->
                <div class="se-card">
                    <div class="se-table-responsive" style="overflow-x: auto;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalprice = 0;
                                $totalshipping = 0;
                                
                                $sql = "SELECT * FROM products WHERE id IN (";
                                foreach($_SESSION['cart'] as $id => $value) {
                                    $sql .= $id . ",";
                                }
                                $sql = substr($sql, 0, -1) . ") ORDER BY id ASC";
                                $query = mysqli_query($con, $sql);
                                
                                while($row = mysqli_fetch_array($query)) {
                                    $quantity = $_SESSION['cart'][$row['id']]['quantity'];
                                    $subtotal = $quantity * $row['productPrice'];
                                    $shipping = $row['shippingCharge'];
                                    
                                    $totalprice += $subtotal;
                                    $totalshipping += $shipping;
                                ?>
                                <tr>
                                    <td style="min-width: 250px;">
                                        <div style="display: flex; gap: 15px; align-items: center;">
                                            <div style="width: 80px; height: 80px; background: var(--gray-50); padding: 5px; border-radius: var(--radius-md);">
                                                <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo e($row['productImage1']); ?>" alt="<?php echo e($row['productName']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                            <div>
                                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 5px; line-height: 1.3;">
                                                    <a href="product-details.php?pid=<?php echo $row['id']; ?>" style="color: var(--gray-900);"><?php echo e($row['productName']); ?></a>
                                                </h4>
                                                <?php if($row['stock_quantity'] > 0): ?>
                                                    <span style="font-size: 12px; color: var(--success);"><i class="bi bi-check-circle"></i> In Stock</span>
                                                <?php else: ?>
                                                    <span style="font-size: 12px; color: var(--error);"><i class="bi bi-x-circle"></i> Out of Stock</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo formatPrice($row['productPrice']); ?></div>
                                    </td>
                                    <td>
                                        <div class="se-qty-control">
                                            <button type="button" onclick="const q=document.getElementById('qty_<?php echo $row['id']; ?>'); if(q.value>1)q.value--">-</button>
                                            <input type="number" id="qty_<?php echo $row['id']; ?>" name="quantity[<?php echo $row['id']; ?>]" value="<?php echo $quantity; ?>" min="1" max="<?php echo max(1, $row['stock_quantity']); ?>">
                                            <button type="button" onclick="const q=document.getElementById('qty_<?php echo $row['id']; ?>'); if(q.value<<?php echo max(1, $row['stock_quantity']); ?>)q.value++">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--primary);"><?php echo formatPrice($subtotal); ?></div>
                                    </td>
                                    <td>
                                        <a href="my-cart.php?remove=<?php echo $row['id']; ?>" class="se-btn se-btn-icon se-btn-outline" style="color: var(--error); border-color: var(--error-light);" onclick="return confirm('Remove this item?');" title="Remove Item">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="se-card-body" style="border-top: 1px solid var(--gray-100); display: flex; justify-content: space-between;">
                        <a href="index.php" class="se-btn se-btn-outline"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
                        <button type="submit" name="update_cart" class="se-btn se-btn-accent"><i class="bi bi-arrow-clockwise"></i> Update Cart</button>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="se-card" style="height: fit-content; position: sticky; top: calc(var(--header-height) + 20px);">
                    <div class="se-card-header">Order Summary</div>
                    <div class="se-card-body">
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
                            <span>Total</span>
                            <span style="color: var(--primary);"><?php echo formatPrice($totalprice + $totalshipping); ?></span>
                        </div>
                        
                        <button type="submit" name="checkout" class="se-btn se-btn-primary se-btn-full se-btn-lg">
                            Proceed to Checkout <i class="bi bi-arrow-right"></i>
                        </button>
                        
                        <div style="text-align: center; margin-top: 15px; font-size: 12px; color: var(--gray-500);">
                            <i class="bi bi-shield-lock"></i> Secure Checkout
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include('includes/footer-new.php'); ?>