<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
    $_SESSION['redirect_url'] = 'order-details.php?id=' . intval($_GET['id']);
    include('includes/header-new.php');
    ?>
    <div class="se-container se-section text-center" style="padding: 100px 20px;">
        <i class="bi bi-lock" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 15px;">Login Required</h2>
        <p style="color: var(--gray-600); max-width: 500px; margin: 0 auto 30px auto; font-size: 16px;">
            You must login or create an account to view your order details.
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

$uid = $_SESSION['id'];
$orderId = intval($_GET['id']);

// Fetch Order
$query = mysqli_prepare($con, "SELECT * FROM orders WHERE id=? AND userId=?");
mysqli_stmt_bind_param($query, "ii", $orderId, $uid);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$order = mysqli_fetch_array($result);

if(!$order) {
    setFlash('error', 'Order not found or you do not have permission to view it.');
    header('location: my-orders.php');
    exit;
}

$orderNum = $order['orderNumber'] ?: 'ORD-000'.$order['id'];
$status = $order['orderStatus'] ?: 'Pending';
$badgeClass = 'info';
if($status == 'Delivered') $badgeClass = 'success';
elseif($status == 'Cancelled') $badgeClass = 'danger';
elseif($status == 'In Process') $badgeClass = 'warning';

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <a href="my-orders.php">My Orders</a> <span>/</span> <span class="current">Order #<?php echo $orderNum; ?></span>
    </div>
    
    <div class="d-flex justify-between align-center mb-3">
        <h2 class="se-section-title" style="margin: 0;">Order Details</h2>
        <a href="track-order.php?id=<?php echo $orderId; ?>" class="se-btn se-btn-primary"><i class="bi bi-truck"></i> Track Order</a>
    </div>
    
    <div class="grid-2" style="grid-template-columns: 2fr 1fr; gap: var(--space-xl);">
        
        <!-- Order Items -->
        <div>
            <div class="se-card" style="margin-bottom: var(--space-lg);">
                <div class="se-card-header">
                    Items in your order
                </div>
                <div class="se-card-body" style="padding: 0;">
                    <div class="se-table-responsive" style="overflow-x: auto;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $itemsQuery = mysqli_query($con, "SELECT * FROM order_items WHERE orderId='$orderId'");
                                if(mysqli_num_rows($itemsQuery) > 0) {
                                    while($item = mysqli_fetch_array($itemsQuery)) {
                                ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; gap: 12px; align-items: center;">
                                            <div style="width: 50px; height: 50px; background: var(--gray-50); padding: 5px; border-radius: var(--radius-sm);">
                                                <img src="admin/productimages/<?php echo $item['productId']; ?>/<?php echo e($item['productImage']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                            <a href="product-details.php?pid=<?php echo $item['productId']; ?>" style="font-weight: 500; color: var(--gray-800); font-size: 13px;">
                                                <?php echo e($item['productName']); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td><?php echo formatPrice($item['productPrice']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td style="font-weight: 600;"><?php echo formatPrice($item['subtotal']); ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    // Fallback for old orders where we didn't have order_items populated yet
                                    // Though migration script should have fixed this.
                                    echo "<tr><td colspan='4' class='text-center py-4'>Please check older order history system for items.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="se-card">
                    <div class="se-card-header">Shipping Address</div>
                    <div class="se-card-body" style="font-size: 14px; line-height: 1.6;">
                        <div style="font-weight: 600; font-size: 15px; margin-bottom: 5px;"><?php echo e($order['shipName'] ?: $order['name']); ?></div>
                        <div><?php echo e($order['shipAddress']); ?></div>
                        <div><?php echo e($order['shipCity']); ?>, <?php echo e($order['shipState']); ?> <?php echo e($order['shipPincode']); ?></div>
                        <div><?php echo e($order['shipCountry']); ?></div>
                        <div style="margin-top: 10px; color: var(--gray-600);"><i class="bi bi-telephone"></i> <?php echo e($order['shipMobile']); ?></div>
                    </div>
                </div>
                
                <div class="se-card">
                    <div class="se-card-header">Payment Info</div>
                    <div class="se-card-body" style="font-size: 14px; line-height: 1.6;">
                        <div><span style="color: var(--gray-500);">Method:</span> <b><?php echo e($order['paymentMethod']); ?></b></div>
                        <div><span style="color: var(--gray-500);">Status:</span> <b><?php echo e($order['paymentStatus']); ?></b></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary Info -->
        <div>
            <div class="se-card">
                <div class="se-card-header">Order Summary</div>
                <div class="se-card-body">
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--gray-600);">
                        <span>Order Number:</span>
                        <span style="font-weight: 600; color: var(--gray-900);"><?php echo $orderNum; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--gray-600);">
                        <span>Date Placed:</span>
                        <span style="font-weight: 600; color: var(--gray-900);"><?php echo date('d M Y', strtotime($order['orderDate'])); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; color: var(--gray-600);">
                        <span>Status:</span>
                        <span class="se-badge se-badge-<?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                    </div>
                    
                    <div style="border-top: 1px dashed var(--gray-300); margin: 15px 0;"></div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--gray-600);">
                        <span>Subtotal</span>
                        <span style="font-weight: 600; color: var(--gray-900);"><?php echo formatPrice($order['subtotal']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--gray-600);">
                        <span>Shipping</span>
                        <span style="font-weight: 600; color: var(--gray-900);">
                            <?php echo $order['shippingCharge'] > 0 ? formatPrice($order['shippingCharge']) : 'Free'; ?>
                        </span>
                    </div>
                    <?php if($order['discount'] > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--success);">
                        <span>Discount</span>
                        <span style="font-weight: 600;">-<?php echo formatPrice($order['discount']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div style="border-top: 1px dashed var(--gray-300); margin: 15px 0;"></div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 18px; font-weight: 700;">
                        <span>Grand Total</span>
                        <span style="color: var(--primary);"><?php echo formatPrice($order['grandTotal']); ?></span>
                    </div>
                    
                </div>
            </div>
            
            <a href="my-orders.php" class="se-btn se-btn-outline se-btn-full mt-3"><i class="bi bi-arrow-left"></i> Back to Orders</a>
        </div>
        
    </div>
</div>

<?php include('includes/footer-new.php'); ?>