<?php
session_start();
error_reporting(0);
include("include/config.php");

if(isset($_POST['submit'])) {
    $username=mysqli_real_escape_string($con, $_POST['username']);
    $password=$_POST['password'];
    $ret=mysqli_query($con,"SELECT * FROM admin WHERE username='$username'");
    $num=mysqli_fetch_array($ret);
    
    if($num) {
        $hashed_password = $num['password'];
        $login_success = false;

        if(password_verify($password, $hashed_password)) {
            $login_success = true;
        } elseif ($hashed_password === md5($password)) {
            // Legacy MD5 verification and upgrade
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($con, "UPDATE admin SET password='$new_hash' WHERE id=" . $num['id']);
            $login_success = true;
        }

        if($login_success) {
            $_SESSION['alogin']=$_POST['username'];
            $_SESSION['aid']=$num['id'];
            
            header("location:dashboard.php");
            exit();
        } else {
            $_SESSION['errmsg']="Invalid username or password";
            header("location:index.php");
            exit();
        }
    } else {
        $_SESSION['errmsg']="Invalid username or password";
        header("location:index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase | Admin Portal</title>
    
    <!-- Link the ShopEase CSS -->
    <link href="../assets/css/shopease.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .admin-login-card {
            background: #fff;
            padding: var(--space-2xl);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 400px;
            border-top: 5px solid var(--primary);
        }
        
        .admin-login-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }
        
        .admin-login-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .admin-login-header p {
            color: var(--gray-500);
            font-size: 14px;
        }
        
        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            margin-bottom: var(--space-md);
            text-align: center;
            display: <?php echo empty($_SESSION['errmsg']) ? 'none' : 'block'; ?>;
        }
    </style>
</head>
<body>

    <div class="admin-login-card">
        <div class="admin-login-header">
            <h2><i class="bi bi-shield-lock" style="color: var(--primary);"></i> Admin Portal</h2>
            <p>Secure authentication required</p>
        </div>
        
        <?php if(!empty($_SESSION['errmsg'])): ?>
            <div class="error-message">
                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlentities($_SESSION['errmsg']); ?><?php echo htmlentities($_SESSION['errmsg']="");?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="se-form-group">
                <label>Username</label>
                <div style="position: relative;">
                    <i class="bi bi-person" style="position: absolute; left: 15px; top: 12px; color: var(--gray-400);"></i>
                    <input type="text" name="username" class="se-input" placeholder="Admin Username" style="padding-left: 40px;" required>
                </div>
            </div>
            
            <div class="se-form-group">
                <label>Password</label>
                <div style="position: relative;">
                    <i class="bi bi-key" style="position: absolute; left: 15px; top: 12px; color: var(--gray-400);"></i>
                    <input type="password" name="password" class="se-input" placeholder="Enter Password" style="padding-left: 40px;" required>
                </div>
            </div>
            
            <button type="submit" name="submit" class="se-btn se-btn-primary se-btn-full se-btn-lg" style="margin-top: 10px;">
                LOGIN TO ADMIN PANEL
            </button>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="../index.php" style="color: var(--gray-500); font-size: 14px; text-decoration: none;">
                    <i class="bi bi-arrow-left"></i> Back to ShopEase
                </a>
            </div>
        </form>
    </div>

</body>
</html>