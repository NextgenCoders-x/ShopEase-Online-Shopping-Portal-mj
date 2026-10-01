<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['id'])) {
    header('location: login.php');
    exit;
}

$uid = $_SESSION['id'];

// Update shipping address
if(isset($_POST['shipupdate'])) {
    $saddress = $_POST['shippingaddress'];
    $sstate = $_POST['shippingstate'];
    $scity = $_POST['shippingcity'];
    $spincode = $_POST['shippingpincode'];
    
    $query = mysqli_prepare($con, "UPDATE users SET shippingAddress=?, shippingState=?, shippingCity=?, shippingPincode=? WHERE id=?");
    mysqli_stmt_bind_param($query, "ssssi", $saddress, $sstate, $scity, $spincode, $uid);
    
    if(mysqli_stmt_execute($query)) {
        setFlash('success', 'Shipping address updated successfully.');
    } else {
        setFlash('error', 'Failed to update address.');
    }
    header('location: manage-addresses.php');
    exit;
}

// Update billing address
if(isset($_POST['billupdate'])) {
    $baddress = $_POST['billingaddress'];
    $bstate = $_POST['billingstate'];
    $bcity = $_POST['billingcity'];
    $bpincode = $_POST['billingpincode'];
    
    $query = mysqli_prepare($con, "UPDATE users SET billingAddress=?, billingState=?, billingCity=?, billingPincode=? WHERE id=?");
    mysqli_stmt_bind_param($query, "ssssi", $baddress, $bstate, $bcity, $bpincode, $uid);
    
    if(mysqli_stmt_execute($query)) {
        setFlash('success', 'Billing address updated successfully.');
    } else {
        setFlash('error', 'Failed to update address.');
    }
    header('location: manage-addresses.php');
    exit;
}

$query = mysqli_query($con, "SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_array($query);

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <span class="current">Addresses</span>
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
                <a href="my-wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
                <a href="manage-addresses.php" class="active"><i class="bi bi-geo-alt"></i> Addresses</a>
                <a href="change-password.php"><i class="bi bi-key"></i> Change Password</a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        
        <!-- Content -->
        <main>
            <div class="grid-2">
                <!-- Shipping Address -->
                <div class="se-card">
                    <div class="se-card-header">
                        Shipping Address
                    </div>
                    <div class="se-card-body">
                        <form method="post">
                            <div class="se-form-group">
                                <label>Address <span class="required">*</span></label>
                                <textarea name="shippingaddress" class="se-textarea" required><?php echo e($user['shippingAddress']); ?></textarea>
                            </div>
                            
                            <div class="se-form-group">
                                <label>State <span class="required">*</span></label>
                                <input type="text" name="shippingstate" class="se-input" value="<?php echo e($user['shippingState']); ?>" required>
                            </div>
                            
                            <div class="se-form-group">
                                <label>City <span class="required">*</span></label>
                                <input type="text" name="shippingcity" class="se-input" value="<?php echo e($user['shippingCity']); ?>" required>
                            </div>
                            
                            <div class="se-form-group">
                                <label>Pincode <span class="required">*</span></label>
                                <input type="text" name="shippingpincode" class="se-input" value="<?php echo e($user['shippingPincode']); ?>" required pattern="[0-9]+">
                            </div>
                            
                            <button type="submit" name="shipupdate" class="se-btn se-btn-primary mt-2">
                                <i class="bi bi-save"></i> Save Shipping Address
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Billing Address -->
                <div class="se-card">
                    <div class="se-card-header">
                        Billing Address
                    </div>
                    <div class="se-card-body">
                        <form method="post">
                            <div class="se-form-group">
                                <label>Address <span class="required">*</span></label>
                                <textarea name="billingaddress" class="se-textarea" required><?php echo e($user['billingAddress']); ?></textarea>
                            </div>
                            
                            <div class="se-form-group">
                                <label>State <span class="required">*</span></label>
                                <input type="text" name="billingstate" class="se-input" value="<?php echo e($user['billingState']); ?>" required>
                            </div>
                            
                            <div class="se-form-group">
                                <label>City <span class="required">*</span></label>
                                <input type="text" name="billingcity" class="se-input" value="<?php echo e($user['billingCity']); ?>" required>
                            </div>
                            
                            <div class="se-form-group">
                                <label>Pincode <span class="required">*</span></label>
                                <input type="text" name="billingpincode" class="se-input" value="<?php echo e($user['billingPincode']); ?>" required pattern="[0-9]+">
                            </div>
                            
                            <button type="submit" name="billupdate" class="se-btn se-btn-outline mt-2">
                                <i class="bi bi-save"></i> Save Billing Address
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
        </main>
        
    </div>
</div>

<?php include('includes/footer-new.php'); ?>
