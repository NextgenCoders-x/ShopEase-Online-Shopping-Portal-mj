<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">All Categories</span>
    </div>

    <h2 class="se-section-title" style="margin-bottom: var(--space-xl);">All Categories</h2>

    <div class="grid-2" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: var(--space-lg);">
        <?php 
        $query = mysqli_query($con, "SELECT id, categoryName, categoryDescription FROM category ORDER BY categoryName ASC");
        while($row = mysqli_fetch_array($query)) {
        ?>
            <div class="se-card" style="transition: transform var(--transition-fast);">
                <div class="se-card-header" style="background: var(--gray-50); border-bottom: 1px solid var(--gray-100);">
                    <a href="category.php?catid=<?php echo $row['id']; ?>" style="color: var(--primary); font-size: 16px;">
                        <i class="bi bi-folder2-open" style="margin-right: 5px;"></i> <?php echo e($row['categoryName']); ?>
                    </a>
                </div>
                <div class="se-card-body">
                    <p style="font-size: 13px; color: var(--gray-500); margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?php echo e($row['categoryDescription']); ?>
                    </p>
                    
                    <ul style="list-style: none; margin: 0; padding: 0;">
                        <?php 
                        $subQuery = mysqli_query($con, "SELECT id, subcategory FROM subcategory WHERE categoryid='".$row['id']."' ORDER BY subcategory ASC");
                        if(mysqli_num_rows($subQuery) > 0) {
                            while($subRow = mysqli_fetch_array($subQuery)) {
                        ?>
                            <li style="margin-bottom: 8px;">
                                <a href="sub-category.php?scid=<?php echo $subRow['id']; ?>" style="font-size: 14px; color: var(--gray-700); display: block; padding: 4px 0;">
                                    <i class="bi bi-chevron-right" style="font-size: 10px; color: var(--gray-400);"></i> <?php echo e($subRow['subcategory']); ?>
                                </a>
                            </li>
                        <?php 
                            }
                        } else {
                            echo '<li style="font-size: 13px; color: var(--gray-400);">No subcategories</li>';
                        }
                        ?>
                    </ul>
                    
                    <a href="category.php?catid=<?php echo $row['id']; ?>" class="se-btn se-btn-outline se-btn-sm se-btn-full mt-3">View All in <?php echo e($row['categoryName']); ?></a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include('includes/footer-new.php'); ?>
