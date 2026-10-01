<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Registration
if(isset($_POST['submit'])) {
    $name = $_POST['fullname'];
    $email = $_POST['emailid'];
    $contactno = $_POST['contactno'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    
    if($password !== $confirmpassword) {
        setFlash('error', 'Passwords do not match!');
    } else {
        // Check if email exists
        $check = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0) {
            setFlash('error', 'Email already exists. Please login.');
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = mysqli_prepare($con, "INSERT INTO users (name, email, contactno, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($query, "ssss", $name, $email, $contactno, $hashedPassword);
            
            if(mysqli_stmt_execute($query)) {
                $newUserId = mysqli_insert_id($con);
                
                // Automatically log them in
                $_SESSION['login'] = $email;
                $_SESSION['id'] = $newUserId;
                $_SESSION['username'] = $name;
                
                // Log user login
                $uip = $_SERVER['REMOTE_ADDR'];
                mysqli_query($con, "INSERT INTO userlog(userEmail, userip, status) VALUES('$email', '$uip', 1)");
                
                // Redirect to intended destination or cart
                $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'my-cart.php';
                unset($_SESSION['redirect_url']);
                
                setFlash('success', 'Account created successfully!');
                header("location: $redirect");
                exit;
            } else {
                setFlash('error', 'Registration failed. Please try again.');
            }
        }
    }
}

// Login
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = mysqli_prepare($con, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($query, "s", $email);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $user = mysqli_fetch_array($result);
    
    if($user && verifyPassword($password, $user['password'])) {
        // Upgrade password if it's still MD5
        if(strlen($user['password']) == 32) {
            migratePassword($con, 'users', $user['id'], $password);
        }
        
        $_SESSION['login'] = $email;
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        
        // Log user login
        $uip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($con, "INSERT INTO userlog(userEmail, userip, status) VALUES('$email', '$uip', 1)");
        
        // Redirect to intended destination or cart
        $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'my-cart.php';
        unset($_SESSION['redirect_url']);
        header("location: $redirect");
        exit();
    } else {
        $uip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($con, "INSERT INTO userlog(userEmail, userip, status) VALUES('$email', '$uip', 0)");
        setFlash('error', 'Invalid email or password.');
    }
}
include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">Authentication</span>
    </div>
    
    <div class="grid-2" style="gap: var(--space-3xl);">
        
        <!-- Login Section -->
        <div class="se-card">
            <div class="se-card-header">
                <h3>Sign In</h3>
            </div>
            <div class="se-card-body">
                <p style="color: var(--gray-500); margin-bottom: var(--space-lg);">Welcome back! Please enter your details to login.</p>
                
                <form method="post">
                    <div class="se-form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="se-input" required placeholder="Enter your email">
                    </div>
                    
                    <div class="se-form-group">
                        <label>Password <span class="required">*</span></label>
                        <input type="password" name="password" class="se-input" required placeholder="Enter your password">
                    </div>
                    
                    <div class="d-flex justify-between align-center mb-3">
                        <div>
                            <a href="forgot-password.php" style="font-size: 13px;">Forgot Password?</a>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="se-btn se-btn-primary se-btn-full se-btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Registration Section -->
        <div class="se-card">
            <div class="se-card-header">
                <h3>Create New Account</h3>
            </div>
            <div class="se-card-body">
                <p style="color: var(--gray-500); margin-bottom: var(--space-lg);">Create an account to speed through checkout and track orders.</p>
                
                <form method="post" onsubmit="return validateRegister();">
                    <div class="se-form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="fullname" class="se-input" required placeholder="John Doe">
                    </div>
                    
                    <div class="se-form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="emailid" class="se-input" required placeholder="john@example.com">
                    </div>
                    
                    <div class="se-form-group">
                        <label>Mobile Number <span class="required">*</span></label>
                        <input type="tel" name="contactno" class="se-input" required placeholder="10-digit mobile number" pattern="[0-9]{10}">
                    </div>
                    
                    <div class="grid-2">
                        <div class="se-form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" id="reg_password" name="password" class="se-input" required>
                        </div>
                        <div class="se-form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" id="reg_confirm" name="confirmpassword" class="se-input" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit" class="se-btn se-btn-accent se-btn-full se-btn-lg mt-2">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>
                </form>
            </div>
        </div>
        
    </div>
</div>

<script>
function validateRegister() {
    var pwd = document.getElementById('reg_password').value;
    var confirm = document.getElementById('reg_confirm').value;
    if(pwd !== confirm) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}
</script>

<?php include('includes/footer-new.php'); ?>