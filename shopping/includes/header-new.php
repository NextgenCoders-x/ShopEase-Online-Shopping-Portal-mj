<?php 
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include_once(__DIR__ . '/config.php');
}

$cartCount = getCartCount();
$cartTotal = getCartTotal($con);
$wishlistCount = 0;
if (isset($_SESSION['id'])) {
    $wishlistCount = getWishlistCount($con, $_SESSION['id']);
}

// Get categories for nav
$catQuery = mysqli_query($con, "SELECT * FROM category ORDER BY categoryName ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/shopease.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<!-- Flash Messages -->
<?php $flash = getFlash(); if ($flash): ?>
<div class="se-flash-container">
    <div class="se-alert se-alert-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'error' : ($flash['type'] === 'warning' ? 'warning' : 'info')); ?>">
        <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
        <?php echo e($flash['message']); ?>
    </div>
</div>
<script>setTimeout(function(){ document.querySelector('.se-flash-container').style.display='none'; }, 4000);</script>
<?php endif; ?>

<!-- Header -->
<header class="se-header">
    <!-- Top Bar -->
    <div class="se-header-top">
        <div class="se-container">
            <div><i class="bi bi-truck"></i> Free shipping on orders over ₹600</div>
            <div>
                <?php if(isset($_SESSION['login'])): ?>
                    <a href="my-account.php"><i class="bi bi-person"></i> <?php echo e($_SESSION['username'] ?? 'My Account'); ?></a>
                    <a href="my-orders.php"><i class="bi bi-box"></i> Orders</a>
                    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php"><i class="bi bi-person"></i> Login</a>
                    <a href="login.php"><i class="bi bi-person-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="se-header-main">
        <div class="se-container">
            <button class="se-mobile-toggle" onclick="document.querySelector('.se-header-nav').classList.toggle('open')">
                <i class="bi bi-list"></i>
            </button>

            <a href="index.php" class="se-logo">
                <i class="bi bi-bag-check-fill"></i> Shop<span>Ease</span>
            </a>

            <form class="se-search" method="post" action="search-result.php">
                <input type="text" name="product" placeholder="Search for products, brands, categories..." required>
                <button type="submit" name="search"><i class="bi bi-search"></i></button>
            </form>

            <div class="se-header-actions">
                <a href="my-wishlist.php" class="se-header-action">
                    <i class="bi bi-heart"></i>
                    <span>Wishlist</span>
                    <?php if($wishlistCount > 0): ?>
                        <span class="badge"><?php echo $wishlistCount; ?></span>
                    <?php endif; ?>
                </a>

                <a href="my-cart.php" class="se-header-action">
                    <i class="bi bi-cart3"></i>
                    <span>Cart</span>
                    <?php if($cartCount > 0): ?>
                        <span class="badge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>

                <?php if(isset($_SESSION['login'])): ?>
                    <a href="my-account.php" class="se-header-action">
                        <i class="bi bi-person-circle"></i>
                        <span>Account</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="se-header-action">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="se-header-nav">
        <div class="se-container">
            <a href="index.php" class="se-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="bi bi-house"></i> Home
            </a>
            <?php if($catQuery): while($cat = mysqli_fetch_assoc($catQuery)): ?>
                <a href="category.php?catid=<?php echo $cat['id']; ?>" class="se-nav-link">
                    <?php echo e($cat['categoryName']); ?>
                </a>
            <?php endwhile; endif; ?>
            <a href="shop-categories.php" class="se-nav-link">
                <i class="bi bi-grid"></i> All Categories
            </a>
        </div>
    </nav>
</header>
