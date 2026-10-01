<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['change'])) {
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    if($password !== $confirmpassword) {
        setFlash('error', 'New password and confirm password do not match!');
    } else {
        $query = mysqli_prepare($con, "SELECT id FROM users WHERE email=? AND contactno=?");
        mysqli_stmt_bind_param($query, "ss", $email, $contact);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        
        if(mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $update = mysqli_prepare($con, "UPDATE users SET password=? WHERE id=?");
            mysqli_stmt_bind_param($update, "si", $hashedPassword, $user['id']);
            
            if(mysqli_stmt_execute($update)) {
                setFlash('success', 'Password successfully reset! You can now login.');
                header('location: login.php');
                exit;
            } else {
                setFlash('error', 'Failed to update password.');
            }
        } else {
            setFlash('error', 'Invalid Email ID or Contact Number.');
        }
    }
}

include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">Forgot Password</span>
    </div>
    
    <div class="se-card" style="max-width: 500px; margin: 0 auto;">
        <div class="se-card-header text-center">
            <h3>Reset Password</h3>
        </div>
        <div class="se-card-body">
            <p style="color: var(--gray-500); margin-bottom: var(--space-lg); text-align: center;">
                Enter your email address and registered contact number to reset your password.
            </p>
            
            <form method="post" name="register" onSubmit="return valid();">
                <div class="se-form-group">
                    <label>Registered Email Address <span class="required">*</span></label>
                    <input type="email" name="email" class="se-input" required placeholder="john@example.com">
                </div>
                
                <div class="se-form-group">
                    <label>Registered Mobile Number <span class="required">*</span></label>
                    <input type="tel" name="contact" class="se-input" required placeholder="10-digit mobile number" pattern="[0-9]{10}">
                </div>
                
                <div class="se-form-group">
                    <label>New Password <span class="required">*</span></label>
                    <input type="password" name="password" id="password" class="se-input" required>
                </div>
                
                <div class="se-form-group">
                    <label>Confirm New Password <span class="required">*</span></label>
                    <input type="password" name="confirmpassword" id="confirmpassword" class="se-input" required>
                </div>
                
                <button type="submit" name="change" class="se-btn se-btn-primary se-btn-full se-btn-lg mt-3">
                    Reset Password
                </button>
                
                <div class="text-center mt-3">
                    <a href="login.php" style="font-size: 14px;">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function valid() {
    if(document.register.password.value != document.register.confirmpassword.value) {
        alert("Password and Confirm Password Field do not match!!");
        document.register.confirmpassword.focus();
        return false;
    }
    return true;
}
</script>

<?php include('includes/footer-new.php'); ?>