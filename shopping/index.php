<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Handle Add to Cart
if(isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']);
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
        setFlash('success', 'Product quantity updated in cart.');
    } else {
        $sql_p = "SELECT * FROM products WHERE id={$id}";
        $query_p = mysqli_query($con, $sql_p);
        if(mysqli_num_rows($query_p) != 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']] = array("quantity" => 1, "price" => $row_p['productPrice']);
            setFlash('success', 'Product added to cart successfully.');
        } else {
            setFlash('error', 'Invalid product.');
        }
    }
    header('location: index.php');
    exit;
}

// Handle Buy Now
if(isset($_GET['action']) && $_GET['action'] == "buynow") {
    $id = intval($_GET['id']);
    $sql_p = "SELECT * FROM products WHERE id={$id}";
    $query_p = mysqli_query($con, $sql_p);
    if(mysqli_num_rows($query_p) != 0) {
        $row_p = mysqli_fetch_array($query_p);
        $_SESSION['buy_now'] = array($row_p['id'] => array("quantity" => 1, "price" => $row_p['productPrice']));
    }
    header('location: checkout.php');
    exit;
}

// Handle Add to Wishlist
if(isset($_GET['action']) && $_GET['action'] == "wishlist" && isset($_GET['id'])) {
    if(empty($_SESSION['login']) || empty($_SESSION['id'])) {
        $_SESSION['redirect_url'] = 'index.php?action=wishlist&id=' . intval($_GET['id']);
        include('includes/header-new.php');
        ?>
        <div class="se-container se-section text-center" style="padding: 100px 20px;">
            <i class="bi bi-lock" style="font-size: 64px; color: var(--primary); margin-bottom: 20px;"></i>
            <h2 style="margin-bottom: 15px;">Login Required</h2>
            <p style="color: var(--gray-600); max-width: 500px; margin: 0 auto 30px auto; font-size: 16px;">
                You must login or create an account to add products to your wishlist.
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
    $pid = intval($_GET['id']);
    $uid = $_SESSION['id'];
    $query = mysqli_query($con, "SELECT id FROM wishlist WHERE productId='$pid' AND userId='$uid'");
    if(mysqli_num_rows($query) > 0) {
        setFlash('info', 'Product already in your wishlist.');
    } else {
        mysqli_query($con, "INSERT INTO wishlist(userId, productId) VALUES('$uid', '$pid')");
        setFlash('success', 'Product added to your wishlist.');
    }
    header('location: index.php');
    exit;
}

include('includes/header-new.php');
?>

<!-- Hero Section -->
<section class="se-hero">
    <div class="se-container" style="position: relative; z-index: 10;">
        <h1>Discover the Best<br>Products Online</h1>
        <p>Shop the latest trends in electronics, fashion, home essentials and more. Quality guaranteed with 30-day returns and free shipping on orders over ₹600.</p>
        <a href="#products" class="se-btn se-btn-lg se-btn-accent">Shop Now <i class="bi bi-arrow-right"></i></a>
    </div>
</section>

<!-- Features -->
<div style="background: #fff; padding: var(--space-xl) 0; border-bottom: 1px solid var(--gray-100);">
    <div class="se-container">
        <div class="se-stat-grid">
            <div class="se-stat-card" style="box-shadow: none; border: none;">
                <div class="se-stat-icon purple"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">Secure Payment</div>
                    <div style="font-size: 12px; color: var(--gray-500);">100% secure checkout</div>
                </div>
            </div>
            <div class="se-stat-card" style="box-shadow: none; border: none;">
                <div class="se-stat-icon green"><i class="bi bi-truck"></i></div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">Free Shipping</div>
                    <div style="font-size: 12px; color: var(--gray-500);">On orders over ₹600</div>
                </div>
            </div>
            <div class="se-stat-card" style="box-shadow: none; border: none;">
                <div class="se-stat-icon orange"><i class="bi bi-arrow-counterclockwise"></i></div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">30 Days Return</div>
                    <div style="font-size: 12px; color: var(--gray-500);">If goods have problems</div>
                </div>
            </div>
            <div class="se-stat-card" style="box-shadow: none; border: none;">
                <div class="se-stat-icon blue"><i class="bi bi-headset"></i></div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">24/7 Support</div>
                    <div style="font-size: 12px; color: var(--gray-500);">Dedicated support</div>
                </div>
            </div>
        </div>
    </div>
</div>

<main id="products" class="se-container se-section">
    <h2 class="se-section-title">Featured Products</h2>
    
    <div class="se-product-grid">
        <?php
        $ret = mysqli_query($con, "SELECT * FROM products ORDER BY id DESC LIMIT 12");
        while ($row = mysqli_fetch_array($ret)) {
            $discount = calcDiscount($row['productPriceBeforeDiscount'], $row['productPrice']);
            $stockStatus = getStockStatus($row['stock_quantity']);
            $isOutOfStock = $row['stock_quantity'] <= 0;
        ?>
            <div class="se-product-card">
                <?php if($discount > 0): ?>
                    <span class="se-product-badge">-<?php echo $discount; ?>%</span>
                <?php elseif(time() - strtotime($row['postingDate']) < 604800): ?>
                    <span class="se-product-badge new">NEW</span>
                <?php endif; ?>
                
                <a href="index.php?action=wishlist&id=<?php echo $row['id']; ?>" class="se-product-wishlist-btn" title="Add to Wishlist">
                    <i class="bi bi-heart"></i>
                </a>
                
                <a href="product-details.php?pid=<?php echo $row['id']; ?>" class="se-product-image">
                    <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo e($row['productImage1']); ?>" alt="<?php echo e($row['productName']); ?>" loading="lazy">
                </a>
                
                <div class="se-product-info">
                    <div class="se-product-brand"><?php echo e($row['productCompany']); ?></div>
                    <h3 class="se-product-name"><a href="product-details.php?pid=<?php echo $row['id']; ?>"><?php echo e($row['productName']); ?></a></h3>
                    
                    <?php 
                    $rt = mysqli_query($con, "SELECT COUNT(*) as count, AVG(quality) as avg FROM productreviews WHERE productId='".$row['id']."'");
                    $rev = mysqli_fetch_array($rt);
                    ?>
                    <div class="se-product-rating">
                        <div class="stars">
                            <?php 
                            $rating = $rev['avg'] ? round($rev['avg']) : 5;
                            for($i=1; $i<=5; $i++) echo $i <= $rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; 
                            ?>
                        </div>
                        <span class="count">(<?php echo $rev['count']; ?>)</span>
                    </div>
                    
                    <div class="se-product-price">
                        <span class="current"><?php echo formatPrice($row['productPrice']); ?></span>
                        <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                            <span class="original"><?php echo formatPrice($row['productPriceBeforeDiscount']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="se-product-stock <?php echo $stockStatus['class']; ?>">
                        <i class="bi bi-<?php echo $isOutOfStock ? 'x-circle' : 'check-circle'; ?>"></i> <?php echo $stockStatus['text']; ?>
                    </div>
                    
                    <div class="se-product-actions">
                        <a href="product-details.php?pid=<?php echo $row['id']; ?>" class="se-btn se-btn-outline" style="flex: 1; padding: 8px;" title="View Details"><i class="bi bi-eye"></i></a>
                        
                        <?php if(!$isOutOfStock): ?>
                            <a href="index.php?action=add&id=<?php echo $row['id']; ?>" class="se-btn se-btn-primary" style="flex: 1; padding: 8px;" title="Add to Cart"><i class="bi bi-cart-plus"></i></a>
                            <a href="index.php?action=buynow&id=<?php echo $row['id']; ?>" class="se-btn se-btn-accent" style="flex: 2; padding: 8px;">Buy Now</a>
                        <?php else: ?>
                            <button class="se-btn se-btn-outline" disabled style="flex: 3; padding: 8px;">Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

<?php include('includes/footer-new.php'); ?>