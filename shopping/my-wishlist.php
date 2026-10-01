<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
    $_SESSION['redirect_url'] = 'my-wishlist.php';
    include('includes/header-new.php');
    ?>
    <div class="se-container se-section text-center" style="padding: 100px 20px;">
        <i class="bi bi-lock" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
        <h2 style="margin-bottom: 15px;">Login Required</h2>
        <p style="color: var(--gray-600); max-width: 500px; margin: 0 auto 30px auto; font-size: 16px;">
            You must login or create an account to view your wishlist.
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

// Remove from Wishlist
if(isset($_GET['del'])) {
    $wid = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM wishlist WHERE id='$wid' AND userId='$uid'");
    setFlash('success', 'Item removed from wishlist.');
    header('location: my-wishlist.php');
    exit;
}

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <span class="current">My Wishlist</span>
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
                <a href="my-orders.php"><i class="bi bi-box"></i> My Orders</a>
                <a href="my-wishlist.php" class="active"><i class="bi bi-heart"></i> Wishlist</a>
                <a href="manage-addresses.php"><i class="bi bi-geo-alt"></i> Addresses</a>
                <a href="change-password.php"><i class="bi bi-key"></i> Change Password</a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        
        <!-- Content -->
        <main>
            <div class="se-card">
                <div class="se-card-header">
                    My Wishlist
                </div>
                <div class="se-card-body" style="padding: 0;">
                    
                    <?php 
                    $query = mysqli_query($con, "SELECT products.*, wishlist.id as wid FROM wishlist JOIN products ON products.id=wishlist.productId WHERE wishlist.userId='$uid'");
                    $num = mysqli_num_rows($query);
                    
                    if($num > 0) {
                    ?>
                    <div class="se-table-responsive" style="overflow-x: auto;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($row = mysqli_fetch_array($query)) {
                                    $isOutOfStock = $row['stock_quantity'] <= 0;
                                ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; gap: 12px; align-items: center;">
                                            <div style="width: 60px; height: 60px; background: var(--gray-50); padding: 5px; border-radius: var(--radius-sm);">
                                                <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo e($row['productImage1']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                            <a href="product-details.php?pid=<?php echo $row['id']; ?>" style="font-weight: 500; color: var(--gray-800); font-size: 14px;">
                                                <?php echo e($row['productName']); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td style="font-weight: 600;">
                                        <?php echo formatPrice($row['productPrice']); ?>
                                    </td>
                                    <td>
                                        <?php if(!$isOutOfStock): ?>
                                            <span class="se-badge se-badge-success"><i class="bi bi-check-circle"></i> In Stock</span>
                                        <?php else: ?>
                                            <span class="se-badge se-badge-danger"><i class="bi bi-x-circle"></i> Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <?php if(!$isOutOfStock): ?>
                                                <a href="index.php?action=add&id=<?php echo $row['id']; ?>" class="se-btn se-btn-primary se-btn-sm" title="Add to Cart"><i class="bi bi-cart-plus"></i></a>
                                            <?php endif; ?>
                                            <a href="my-wishlist.php?del=<?php echo $row['wid']; ?>" class="se-btn se-btn-danger se-btn-sm" title="Remove" onclick="return confirm('Remove from wishlist?');"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                        <div class="se-empty" style="padding: var(--space-3xl);">
                            <i class="bi bi-heart" style="font-size: 48px; color: var(--gray-300);"></i>
                            <h3 style="margin: 15px 0 10px; font-size: 18px; color: var(--gray-700);">Your wishlist is empty</h3>
                            <div style="color: var(--gray-500); margin-bottom: 20px;">Save items you love here to buy them later.</div>
                            <a href="index.php" class="se-btn se-btn-primary">Explore Products</a>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
            
        </main>
        
    </div>
</div>

<?php include('includes/footer-new.php'); ?>