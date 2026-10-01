<?php
session_start();
error_reporting(0);
include('includes/config.php');
$searchTerm = isset($_POST['product']) ? trim($_POST['product']) : (isset($_GET['q']) ? trim($_GET['q']) : '');

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
    header('location: search-result.php?q=' . urlencode($searchTerm));
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

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">Search Results</span>
    </div>

    <div class="d-flex justify-between align-center mb-3">
        <h2 class="se-section-title" style="margin: 0;">Search Results for "<?php echo e($searchTerm); ?>"</h2>
    </div>

    <div class="se-product-grid">
        <?php
        // Securely bind param for search
        $searchTermWildcard = '%' . $searchTerm . '%';
        $query = mysqli_prepare($con, "SELECT * FROM products WHERE productName LIKE ? OR productCompany LIKE ?");
        mysqli_stmt_bind_param($query, "ss", $searchTermWildcard, $searchTermWildcard);
        mysqli_stmt_execute($query);
        $ret = mysqli_stmt_get_result($query);
        
        $num = mysqli_num_rows($ret);
        
        if($num > 0 && !empty($searchTerm)) {
            while ($row = mysqli_fetch_array($ret)) {
                $discount = calcDiscount($row['productPriceBeforeDiscount'], $row['productPrice']);
                $stockStatus = getStockStatus($row['stock_quantity']);
                $isOutOfStock = $row['stock_quantity'] <= 0;
        ?>
            <div class="se-product-card">
                <?php if($discount > 0): ?>
                    <span class="se-product-badge">-<?php echo $discount; ?>%</span>
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
                            <a href="search-result.php?q=<?php echo urlencode($searchTerm); ?>&action=add&id=<?php echo $row['id']; ?>" class="se-btn se-btn-primary" style="flex: 1; padding: 8px;" title="Add to Cart"><i class="bi bi-cart-plus"></i></a>
                            <a href="search-result.php?q=<?php echo urlencode($searchTerm); ?>&action=buynow&id=<?php echo $row['id']; ?>" class="se-btn se-btn-accent" style="flex: 2; padding: 8px;">Buy Now</a>
                        <?php else: ?>
                            <button class="se-btn se-btn-outline" disabled style="flex: 3; padding: 8px;">Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
        ?>
            <div style="grid-column: 1 / -1; padding: var(--space-3xl) 0;" class="se-empty">
                <i class="bi bi-search" style="font-size: 48px; color: var(--gray-300);"></i>
                <h3 style="margin-top: 15px;">No Results Found</h3>
                <p style="color: var(--gray-500);">We couldn't find any products matching your search for "<?php echo e($searchTerm); ?>".</p>
                <a href="index.php" class="se-btn se-btn-primary mt-3">Continue Shopping</a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include('includes/footer-new.php'); ?>