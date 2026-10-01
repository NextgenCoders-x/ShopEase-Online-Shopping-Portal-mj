<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
    $_SESSION['redirect_url'] = 'track-order.php?id=' . intval($_GET['id']);
    include('includes/header-new.php');
    ?>
    <div class="se-container se-section text-center" style="padding: 100px 20px;">
        <i class="bi bi-lock" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 15px;">Login Required</h2>
        <p style="color: var(--gray-600); max-width: 500px; margin: 0 auto 30px auto; font-size: 16px;">
            You must login or create an account to track your order.
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
    setFlash('error', 'Order not found.');
    header('location: my-orders.php');
    exit;
}

$orderNum = $order['orderNumber'] ?: 'ORD-000'.$order['id'];
$currentStatus = $order['orderStatus'] ?: 'Pending';

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <a href="my-orders.php">My Orders</a> <span>/</span> <span class="current">Track Order</span>
    </div>
    
    <div class="se-card" style="max-width: 800px; margin: 0 auto;">
        <div class="se-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Track Order #<?php echo $orderNum; ?></span>
            <a href="order-details.php?id=<?php echo $orderId; ?>" style="font-size: 13px; font-weight: 500;">View Order Details</a>
        </div>
        <div class="se-card-body" style="padding: var(--space-2xl);">
            
            <?php if($currentStatus == 'Cancelled'): ?>
                <div class="se-empty" style="padding: 20px;">
                    <i class="bi bi-x-circle" style="color: var(--error); font-size: 48px;"></i>
                    <h3 style="color: var(--error); margin-top: 15px;">Order Cancelled</h3>
                    <p>This order has been cancelled. If this was a mistake or you need help, please contact support.</p>
                </div>
            <?php else: ?>
            
                <div class="se-timeline">
                    <?php 
                    // Track History
                    $trackQuery = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$orderId' ORDER BY postingDate DESC");
                    
                    if(mysqli_num_rows($trackQuery) > 0) {
                        $first = true;
                        while($track = mysqli_fetch_array($trackQuery)) {
                            $isCompleted = ($track['status'] == 'Delivered');
                            $isActive = $first && !$isCompleted;
                    ?>
                    <div class="se-timeline-item <?php echo $isActive ? 'active' : ''; ?> <?php echo $isCompleted ? 'completed' : ''; ?>">
                        <div class="time"><?php echo date('d M Y, h:i A', strtotime($track['postingDate'])); ?></div>
                        <div class="title"><?php echo e($track['status']); ?></div>
                        <div class="desc"><?php echo e($track['remark']); ?></div>
                    </div>
                    <?php 
                            $first = false;
                        }
                    } else {
                        // Fallback if no tracking history is found yet
                    ?>
                    <div class="se-timeline-item active">
                        <div class="time"><?php echo date('d M Y, h:i A', strtotime($order['orderDate'])); ?></div>
                        <div class="title">Order Placed</div>
                        <div class="desc">Your order has been placed successfully and is waiting for processing.</div>
                    </div>
                    <?php } ?>
                </div>
                
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php include('includes/footer-new.php'); ?>