<?php 
session_start();
error_reporting(0);
include('includes/config.php');

$pid = intval($_GET['pid']);

// Handle Add to Cart
if(isset($_POST['add_to_cart'])) {
    $qty = max(1, intval($_POST['quantity']));
    if(isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid]['quantity'] += $qty;
    } else {
        $sql_p = "SELECT productPrice FROM products WHERE id={$pid}";
        $query_p = mysqli_query($con, $sql_p);
        if(mysqli_num_rows($query_p) > 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$pid] = array("quantity" => $qty, "price" => $row_p['productPrice']);
        }
    }
    setFlash('success', 'Product added to cart successfully.');
    header("location: product-details.php?pid=$pid");
    exit;
}

// Handle Buy Now (Redirect to checkout directly)
if(isset($_POST['buy_now'])) {
    $qty = max(1, intval($_POST['quantity']));
    
    // Store in buy_now session instead of cart
    $sql_p = "SELECT productPrice FROM products WHERE id={$pid}";
    $query_p = mysqli_query($con, $sql_p);
    if(mysqli_num_rows($query_p) > 0) {
        $row_p = mysqli_fetch_array($query_p);
        $_SESSION['buy_now'] = array($pid => array("quantity" => $qty, "price" => $row_p['productPrice']));
    }
    
    // Redirect to checkout, where authentication will be checked
    header('location: checkout.php');
    exit;
}

include('includes/header-new.php');

// Fetch product details
$ret = mysqli_query($con, "SELECT products.*, category.categoryName as catname, subcategory.subcategory as subcatname FROM products 
    LEFT JOIN category ON category.id=products.category 
    LEFT JOIN subcategory ON subcategory.id=products.subCategory 
    WHERE products.id='$pid'");
    
if(mysqli_num_rows($ret) == 0) {
    echo '<div class="se-container se-section"><div class="se-empty"><h3>Product Not Found</h3><a href="index.php" class="se-btn se-btn-primary">Back to Home</a></div></div>';
    include('includes/footer-new.php');
    exit;
}

$row = mysqli_fetch_array($ret);
$stockStatus = getStockStatus($row['stock_quantity']);
$isOutOfStock = $row['stock_quantity'] <= 0;
$discount = calcDiscount($row['productPriceBeforeDiscount'], $row['productPrice']);
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> 
        <a href="category.php?catid=<?php echo $row['category']; ?>"><?php echo e($row['catname']); ?></a> <span>/</span>
        <a href="sub-category.php?scid=<?php echo $row['subCategory']; ?>"><?php echo e($row['subcatname']); ?></a> <span>/</span>
        <span class="current"><?php echo e($row['productName']); ?></span>
    </div>
    
    <div class="grid-2" style="gap: var(--space-3xl); margin-top: var(--space-md);">
        
        <!-- Product Images -->
        <div>
            <div class="se-card" style="padding: var(--space-xl); text-align: center; margin-bottom: var(--space-md); position: relative;">
                <?php if($discount > 0): ?>
                    <span class="se-product-badge">-<?php echo $discount; ?>%</span>
                <?php endif; ?>
                <img id="mainImage" src="admin/productimages/<?php echo $row['id']; ?>/<?php echo e($row['productImage1']); ?>" alt="<?php echo e($row['productName']); ?>" style="max-height: 400px; margin: 0 auto;">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <?php 
                for($i=1; $i<=3; $i++) {
                    $img = $row["productImage$i"];
                    if($img): 
                ?>
                    <div class="se-card" style="padding: 10px; cursor: pointer; flex: 1; text-align: center;" onclick="document.getElementById('mainImage').src='admin/productimages/<?php echo $row['id']; ?>/<?php echo e($img); ?>'">
                        <img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo e($img); ?>" style="max-height: 80px; margin: 0 auto;">
                    </div>
                <?php 
                    endif;
                }
                ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div>
            <div style="margin-bottom: var(--space-lg);">
                <div class="se-product-brand" style="font-size: 13px;"><?php echo e($row['productCompany']); ?></div>
                <h1 style="font-size: 28px; line-height: 1.3; margin-bottom: 15px;"><?php echo e($row['productName']); ?></h1>
                
                <?php 
                $rt = mysqli_query($con, "SELECT COUNT(*) as count, AVG(quality) as avg FROM productreviews WHERE productId='".$row['id']."'");
                $rev = mysqli_fetch_array($rt);
                ?>
                <div class="se-product-rating" style="font-size: 16px;">
                    <div class="stars">
                        <?php 
                        $rating = $rev['avg'] ? round($rev['avg']) : 5;
                        for($i=1; $i<=5; $i++) echo $i <= $rating ? '<i class="bi bi-star-fill"></i> ' : '<i class="bi bi-star"></i> '; 
                        ?>
                    </div>
                    <span class="count" style="font-size: 14px; margin-left: 8px;">(<?php echo $rev['count']; ?> Reviews)</span>
                </div>
            </div>
            
            <div style="padding: var(--space-lg) 0; border-top: 1px solid var(--gray-200); border-bottom: 1px solid var(--gray-200); margin-bottom: var(--space-lg);">
                <div class="se-product-price" style="display: flex; align-items: baseline; gap: 15px; margin-bottom: 10px;">
                    <span class="current" style="font-size: 32px; color: var(--primary);"><?php echo formatPrice($row['productPrice']); ?></span>
                    <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                        <span class="original" style="font-size: 18px;"><?php echo formatPrice($row['productPriceBeforeDiscount']); ?></span>
                        <span class="se-badge se-badge-success">Save <?php echo $discount; ?>%</span>
                    <?php endif; ?>
                </div>
                
                <div class="se-product-stock <?php echo $stockStatus['class']; ?>" style="font-size: 15px;">
                    <i class="bi bi-<?php echo $isOutOfStock ? 'x-circle' : 'check-circle'; ?>"></i> 
                    <?php echo $stockStatus['text']; ?>
                </div>
                
                <?php if($row['shippingCharge'] > 0): ?>
                <div style="font-size: 14px; color: var(--gray-600); margin-top: 10px;">
                    <i class="bi bi-truck"></i> Shipping charge: <b><?php echo formatPrice($row['shippingCharge']); ?></b>
                </div>
                <?php else: ?>
                <div style="font-size: 14px; color: var(--success); font-weight: 600; margin-top: 10px;">
                    <i class="bi bi-truck"></i> Free Shipping
                </div>
                <?php endif; ?>
            </div>
            
            <form method="post">
                <div style="margin-bottom: var(--space-xl);">
                    <label style="display: block; font-weight: 600; margin-bottom: 10px;">Quantity</label>
                    <div class="se-qty-control">
                        <button type="button" onclick="const q=document.getElementById('qty'); if(q.value>1)q.value--">-</button>
                        <input type="number" id="qty" name="quantity" value="1" min="1" max="<?php echo $row['stock_quantity']; ?>">
                        <button type="button" onclick="const q=document.getElementById('qty'); if(q.value<<?php echo $row['stock_quantity']; ?>)q.value++">+</button>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <?php if(!$isOutOfStock): ?>
                        <button type="submit" name="add_to_cart" class="se-btn se-btn-outline se-btn-lg" style="flex: 1;">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                        <button type="submit" name="buy_now" class="se-btn se-btn-primary se-btn-lg" style="flex: 1;">
                            <i class="bi bi-lightning-fill"></i> Buy Now
                        </button>
                    <?php else: ?>
                        <button type="button" class="se-btn se-btn-danger se-btn-lg" disabled style="flex: 2;">
                            Out of Stock
                        </button>
                    <?php endif; ?>
                    
                    <a href="index.php?action=wishlist&id=<?php echo $row['id']; ?>" class="se-btn se-btn-outline se-btn-lg" style="padding: 12px 18px;" title="Add to Wishlist">
                        <i class="bi bi-heart"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Description & Reviews -->
    <div class="se-card" style="margin-top: var(--space-3xl);">
        <div class="se-card-header">
            Product Description
        </div>
        <div class="se-card-body" style="font-size: 15px; line-height: 1.8; color: var(--gray-700);">
            <?php echo $row['productDescription']; ?>
        </div>
    </div>
    
</div>

<?php include('includes/footer-new.php'); ?>