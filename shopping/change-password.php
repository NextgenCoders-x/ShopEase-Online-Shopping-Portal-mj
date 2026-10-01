<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(empty($_SESSION['id'])) {
    header('location: logout.php');
    exit;
}

if(isset($_POST['update'])) {
    $currentpwd = $_POST['cpass'];
    $newpwd = $_POST['newpass'];
    $cnfpass = $_POST['cnfpass'];
    $uid = $_SESSION['id'];
    
    if($newpwd !== $cnfpass) {
        setFlash('error', 'New password and confirm password do not match.');
    } else {
        // Fetch current password safely
        $query = mysqli_prepare($con, "SELECT password FROM users WHERE id=?");
        mysqli_stmt_bind_param($query, "i", $uid);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $user = mysqli_fetch_assoc($result);
        
        if($user && verifyPassword($currentpwd, $user['password'])) {
            // Hash the new password
            $hashedPassword = password_hash($newpwd, PASSWORD_DEFAULT);
            
            // Safely update password. THE BUG WAS OVERWRITING $con HERE.
            $update = mysqli_prepare($con, "UPDATE users SET password=? WHERE id=?");
            mysqli_stmt_bind_param($update, "si", $hashedPassword, $uid);
            
            if(mysqli_stmt_execute($update)) {
                setFlash('success', 'Password Changed Successfully!');
            } else {
                setFlash('error', 'Failed to change password. Please try again.');
            }
        } else {
            setFlash('error', 'Current Password does not match!');
        }
    }
    
    header('location: change-password.php');
    exit;
}

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <a href="my-account.php">My Account</a> <span>/</span> <span class="current">Change Password</span>
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
                <a href="manage-addresses.php"><i class="bi bi-geo-alt"></i> Addresses</a>
                <a href="change-password.php" class="active"><i class="bi bi-key"></i> Change Password</a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        
        <!-- Content -->
        <main>
            <div class="se-card">
                <div class="se-card-header">
                    Change Password
                </div>
                <div class="se-card-body">
                    
                    <form method="post" name="chngpwd" onSubmit="return valid();" style="max-width: 500px;">
                        
                        <div class="se-form-group">
                            <label>Current Password <span class="required">*</span></label>
                            <input type="password" name="cpass" id="cpass" class="se-input" required>
                        </div>
                        
                        <div class="se-form-group">
                            <label>New Password <span class="required">*</span></label>
                            <input type="password" name="newpass" id="newpass" class="se-input" required>
                        </div>
                        
                        <div class="se-form-group">
                            <label>Confirm New Password <span class="required">*</span></label>
                            <input type="password" name="cnfpass" id="cnfpass" class="se-input" required>
                        </div>
                        
                        <button type="submit" name="update" class="se-btn se-btn-primary mt-2">
                            <i class="bi bi-shield-lock"></i> Update Password
                        </button>
                        
                    </form>
                    
                </div>
            </div>
        </main>
        
    </div>
</div>

<script>
function valid() {
    if(document.chngpwd.cpass.value=="") {
        alert("Current Password Field is Empty!!");
        document.chngpwd.cpass.focus();
        return false;
    }
    else if(document.chngpwd.newpass.value=="") {
        alert("New Password Field is Empty!!");
        document.chngpwd.newpass.focus();
        return false;
    }
    else if(document.chngpwd.cnfpass.value=="") {
        alert("Confirm Password Field is Empty!!");
        document.chngpwd.cnfpass.focus();
        return false;
    }
    else if(document.chngpwd.newpass.value!= document.chngpwd.cnfpass.value) {
        alert("Password and Confirm Password Field do not match!!");
        document.chngpwd.cnfpass.focus();
        return false;
    }
    return true;
}
</script>

<?php include('includes/footer-new.php'); ?>
