<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['id'])) {
    header('location: login.php');
    exit;
}

$orderId = intval($_GET['id']);
$uid = $_SESSION['id'];

// Get order details
$query = mysqli_query($con, "SELECT * FROM orders WHERE id='$orderId' AND userId='$uid'");
$order = mysqli_fetch_array($query);

if(!$order) {
    header('location: index.php');
    exit;
}

include('includes/header-new.php');
?>

<div class="se-container se-section" style="max-width: 800px;">
    
    <div style="text-align: center; margin-bottom: var(--space-2xl);">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background-color: var(--success-light); color: var(--success); font-size: 40px; margin-bottom: var(--space-md);">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2 style="color: var(--gray-800); margin-bottom: 10px;">Order Placed Successfully!</h2>
        <p style="color: var(--gray-600); font-size: 16px;">Thank you for your purchase. Your order has been received and is being processed.</p>
    </div>
    
    <div class="se-card" style="margin-bottom: var(--space-xl);">
        <div class="se-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Order Summary</span>
            <span style="font-size: 14px; font-weight: normal; color: var(--primary);">Order #<?php echo e($order['orderNumber']); ?></span>
        </div>
        
        <div class="se-card-body" style="padding: 0;">
            <div style="padding: var(--space-lg);">
                <?php
                $itemQuery = mysqli_query($con, "SELECT * FROM order_items WHERE orderId='$orderId'");
                while($item = mysqli_fetch_array($itemQuery)) {
                ?>
                <div style="display: flex; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-100);">
                    <div style="width: 60px; height: 60px; background: var(--gray-50); border-radius: var(--radius-md); padding: 5px;">
                        <img src="admin/productimages/<?php echo $item['productId']; ?>/<?php echo e($item['productImage']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; margin-bottom: 5px;"><?php echo e($item['productName']); ?></div>
                        <div style="font-size: 14px; color: var(--gray-500);">Qty: <?php echo $item['quantity']; ?> x <?php echo formatPrice($item['productPrice']); ?></div>
                    </div>
                    <div style="font-weight: 600;">
                        <?php echo formatPrice($item['subtotal']); ?>
                    </div>
                </div>
                <?php } ?>
                
                <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; width: 250px; color: var(--gray-600);">
                        <span>Subtotal:</span>
                        <span><?php echo formatPrice($order['subtotal']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; width: 250px; color: var(--gray-600);">
                        <span>Shipping:</span>
                        <span><?php echo formatPrice($order['shippingCharge']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; width: 250px; font-weight: 700; font-size: 18px; color: var(--gray-800); border-top: 1px solid var(--gray-200); padding-top: 10px;">
                        <span>Grand Total:</span>
                        <span style="color: var(--primary);"><?php echo formatPrice($order['grandTotal']); ?></span>
                    </div>
                </div>
            </div>
            
            <div style="background: var(--gray-50); padding: var(--space-lg); border-top: 1px solid var(--gray-200); display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-xl);">
                <div>
                    <h4 style="font-size: 14px; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Shipping Details</h4>
                    <div style="font-weight: 600; margin-bottom: 4px;"><?php echo e($order['shipName']); ?></div>
                    <div style="color: var(--gray-600); font-size: 14px; line-height: 1.5;">
                        <?php echo nl2br(e($order['shipAddress'])); ?><br>
                        <?php echo e($order['shipCity']); ?>, <?php echo e($order['shipState']); ?> <?php echo e($order['shipPincode']); ?><br>
                        <i class="bi bi-telephone"></i> <?php echo e($order['shipMobile']); ?>
                    </div>
                </div>
                
                <div>
                    <h4 style="font-size: 14px; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Order Details</h4>
                    <div style="display: grid; gap: 8px; font-size: 14px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--gray-500);">Payment Method:</span>
                            <span style="font-weight: 600;"><?php echo e($order['paymentMethod']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--gray-500);">Payment Status:</span>
                            <span class="se-badge <?php echo $order['paymentStatus'] == 'Paid' ? 'se-badge-success' : 'se-badge-warning'; ?>">
                                <?php echo e($order['paymentStatus']); ?>
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--gray-500);">Order Status:</span>
                            <span style="font-weight: 600; color: var(--primary);"><?php echo e($order['orderStatus']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: flex; justify-content: center; gap: 15px;">
        <a href="track-order.php" class="se-btn se-btn-outline"><i class="bi bi-geo-alt"></i> Track Order</a>
        <a href="my-orders.php" class="se-btn se-btn-outline"><i class="bi bi-bag"></i> View My Orders</a>
        <a href="index.php" class="se-btn se-btn-primary"><i class="bi bi-cart"></i> Continue Shopping</a>
    </div>
    
</div>

<?php include('includes/footer-new.php'); ?>
