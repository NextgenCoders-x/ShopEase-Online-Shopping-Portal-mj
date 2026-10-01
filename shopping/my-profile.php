<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['id'])) {
    header('location: login.php');
    exit;
}

$uid = $_SESSION['id'];

// Update Profile
if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $contactno = $_POST['contactno'];
    
    $query = mysqli_prepare($con, "UPDATE users SET name=?, contactno=? WHERE id=?");
    mysqli_stmt_bind_param($query, "ssi", $name, $contactno, $uid);
    
    if(mysqli_stmt_execute($query)) {
        $_SESSION['username'] = $name;
        setFlash('success', 'Profile updated successfully.');
    } else {
        setFlash('error', 'Failed to update profile.');
    }
    header('location: my-profile.php');
    exit;
}

$query = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_array($query);

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <span class="current">My Profile</span>
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
                <a href="my-profile.php" class="active"><i class="bi bi-person"></i> Profile</a>
                <a href="my-orders.php"><i class="bi bi-box"></i> My Orders</a>
                <a href="my-wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
                <a href="manage-addresses.php"><i class="bi bi-geo-alt"></i> Addresses</a>
                <a href="change-password.php"><i class="bi bi-key"></i> Change Password</a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        
        <!-- Content -->
        <main>
            <div class="se-card" style="max-width: 600px;">
                <div class="se-card-header">
                    Personal Information
                </div>
                <div class="se-card-body">
                    <form method="post">
                        <div class="se-form-group">
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="name" class="se-input" value="<?php echo e($user['name']); ?>" required>
                        </div>
                        
                        <div class="se-form-group">
                            <label>Email Address <span style="font-size: 11px; color: var(--gray-400); font-weight: normal;">(Cannot be changed)</span></label>
                            <input type="email" class="se-input" value="<?php echo e($user['email']); ?>" disabled style="background: var(--gray-50); color: var(--gray-500); cursor: not-allowed;">
                        </div>
                        
                        <div class="se-form-group">
                            <label>Mobile Number <span class="required">*</span></label>
                            <input type="tel" name="contactno" class="se-input" value="<?php echo e($user['contactno']); ?>" required pattern="[0-9]{10}">
                        </div>
                        
                        <div class="se-form-group">
                            <label>Account Created On</label>
                            <div style="padding: 10px 0; font-size: 14px; color: var(--gray-600);">
                                <?php echo date('d M Y, h:i A', strtotime($user['regDate'])); ?>
                            </div>
                        </div>
                        
                        <button type="submit" name="update" class="se-btn se-btn-primary mt-3">
                            <i class="bi bi-person-check"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </main>
        
    </div>
</div>

<?php include('includes/footer-new.php'); ?>
