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
    header('location: sub-category.php?scid=' . intval($_GET['scid']));
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

$scid = intval($_GET['scid']);

// Fetch Subcategory Name and Category Name
$query = mysqli_query($con, "SELECT subcategory, category.id as catid, category.categoryName FROM subcategory JOIN category ON subcategory.categoryid=category.id WHERE subcategory.id='$scid'");
$catRow = mysqli_fetch_array($query);
$subcategoryName = $catRow['subcategory'];
$categoryName = $catRow['categoryName'];
$categoryId = $catRow['catid'];

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="shop-categories.php">Categories</a> <span>/</span> <a href="category.php?catid=<?php echo $categoryId; ?>"><?php echo e($categoryName); ?></a> <span>/</span> <span class="current"><?php echo e($subcategoryName); ?></span>
    </div>

    <div class="d-flex justify-between align-center mb-3">
        <h2 class="se-section-title" style="margin: 0;"><?php echo e($subcategoryName); ?></h2>
    </div>

    <div class="se-product-grid">
        <?php
        $ret = mysqli_query($con, "SELECT * FROM products WHERE subCategory='$scid'");
        $num = mysqli_num_rows($ret);
        
        if($num > 0) {
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
                            <a href="sub-category.php?scid=<?php echo $scid; ?>&action=add&id=<?php echo $row['id']; ?>" class="se-btn se-btn-primary" style="flex: 1; padding: 8px;" title="Add to Cart"><i class="bi bi-cart-plus"></i></a>
                            <a href="sub-category.php?scid=<?php echo $scid; ?>&action=buynow&id=<?php echo $row['id']; ?>" class="se-btn se-btn-accent" style="flex: 2; padding: 8px;">Buy Now</a>
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
                <h3 style="margin-top: 15px;">No Products Found</h3>
                <p style="color: var(--gray-500);">There are currently no products available in this subcategory.</p>
                <a href="category.php?catid=<?php echo $categoryId; ?>" class="se-btn se-btn-primary mt-3">Back to <?php echo e($categoryName); ?></a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include('includes/footer-new.php'); ?>