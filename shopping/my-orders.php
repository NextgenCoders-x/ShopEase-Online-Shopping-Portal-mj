<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
    $_SESSION['redirect_url'] = 'my-orders.php';
    include('includes/header-new.php');
    ?>
    <div class="se-container se-section text-center" style="padding: 100px 20px;">
        <i class="bi bi-lock" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 15px;">Login Required</h2>
        <p style="color: var(--gray-600); max-width: 500px; margin: 0 auto 30px auto; font-size: 16px;">
            You must login or create an account to view your orders.
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

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <span class="current">My Orders</span>
    </div>
    
    <div class="se-account-layout">
        
        <!-- Sidebar -->
        <aside class="se-account-sidebar">
            <div class="user-info">
                <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                <div class="name"><?php echo e($_SESSION['username']); ?></div>
                <div class="email"><?php echo e($_SESSION['login']); ?></div>
            </div>
            <nav>
                <a href="my-account.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="my-profile.php"><i class="bi bi-person"></i> Profile</a>
                <a href="my-orders.php" class="active"><i class="bi bi-box"></i> My Orders</a>
                <a href="my-wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
                <a href="manage-addresses.php"><i class="bi bi-geo-alt"></i> Addresses</a>
                <a href="change-password.php"><i class="bi bi-key"></i> Change Password</a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        
        <!-- Content -->
        <main>
            <div class="se-card">
                <div class="se-card-header">
                    My Order History
                </div>
                <div class="se-card-body" style="padding: 0;">
                    <div class="se-table-responsive" style="overflow-x: auto;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Fetch all orders for this user
                                $query = mysqli_query($con, "SELECT * FROM orders WHERE userId='$uid' ORDER BY id DESC");
                                if(mysqli_num_rows($query) > 0) {
                                    while($row = mysqli_fetch_array($query)) { 
                                ?>
                                <tr>
                                    <td>
                                        <b style="color: var(--primary);"><?php echo e($row['orderNumber'] ?: 'ORD-000'.$row['id']); ?></b>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($row['orderDate'])); ?></td>
                                    <td><?php echo e($row['paymentMethod']); ?></td>
                                    <td style="font-weight: 600;"><?php echo formatPrice($row['grandTotal']); ?></td>
                                    <td>
                                        <?php 
                                        $status = $row['orderStatus'] ?: 'Pending';
                                        $badgeClass = 'info';
                                        if($status == 'Delivered') $badgeClass = 'success';
                                        elseif($status == 'Cancelled') $badgeClass = 'danger';
                                        elseif($status == 'In Process') $badgeClass = 'warning';
                                        ?>
                                        <span class="se-badge se-badge-<?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                    </td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $row['id']; ?>" class="se-btn se-btn-outline se-btn-sm" title="View Details"><i class="bi bi-eye"></i></a>
                                        <a href="track-order.php?id=<?php echo $row['id']; ?>" class="se-btn se-btn-primary se-btn-sm" title="Track Order"><i class="bi bi-truck"></i></a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: var(--space-xl);">
                                        <i class="bi bi-box" style="font-size: 48px; color: var(--gray-300);"></i>
                                        <h3 style="margin: 15px 0 10px; font-size: 18px; color: var(--gray-700);">No orders found</h3>
                                        <div style="color: var(--gray-500); margin-bottom: 20px;">You haven't placed any orders yet.</div>
                                        <a href="index.php" class="se-btn se-btn-primary">Start Shopping</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </main>
        
    </div>
</div>

<?php include('includes/footer-new.php'); ?>
