<?php
session_start();
include_once('includes/config.php');
if(strlen($_SESSION['aid'])==0)
{	
    header('location:logout.php');
}
else{
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date( 'd-m-Y h:i:s A', time () );

    if(isset($_POST['submit']))
    {
        $username = $_SESSION['alogin'];
        $sql = mysqli_query($con, "SELECT password FROM admin WHERE username='$username'");
        $row = mysqli_fetch_array($sql);
        
        if($row) {
            $hashed_password = $row['password'];
            $current_password = $_POST['password'];
            $is_valid = false;
            
            if(password_verify($current_password, $hashed_password)) {
                $is_valid = true;
            } elseif ($hashed_password === md5($current_password)) {
                $is_valid = true;
            }

            if($is_valid) {
                $new_password = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
                mysqli_query($con, "UPDATE admin SET password='$new_password', updationDate='$currentTime' WHERE username='$username'");
                $_SESSION['msg']="Password Changed Successfully !!";
            } else {
                $_SESSION['msg']="Old Password not match !!";
            }
        } else {
            $_SESSION['msg']="Old Password not match !!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Shopping Portal | Change Password</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript">
function valid()
{
if(document.chngpwd.password.value=="")
{
alert("Current Password Field is Empty !!");
document.chngpwd.password.focus();
return false;
}
else if(document.chngpwd.newpassword.value=="")
{
alert("New Password Field is Empty !!");
document.chngpwd.newpassword.focus();
return false;
}
else if(document.chngpwd.confirmpassword.value=="")
{
alert("Confirm Password Field is Empty !!");
document.chngpwd.confirmpassword.focus();
return false;
}
else if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.chngpwd.confirmpassword.focus();
return false;
}
return true;
}
</script>
    </head>
    <body>
   <?php include_once('includes/header.php');?>
        <div id="layoutSidenav">
   <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Change Password</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Change Password</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php if(isset($_POST['submit'])) {?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
                                </div>
                                <?php } ?>

                                <form name="chngpwd" method="post" onSubmit="return valid();">                                
                                <div class="row">
                                    <div class="col-2">Current Password</div>
                                    <div class="col-6"><input type="password" placeholder="Enter your current Password" name="password" class="form-control" required></div>
                                </div>

                                <div class="row" style="margin-top:1%;">
                                    <div class="col-2">New Password</div>
                                    <div class="col-6"><input type="password" placeholder="Enter your new Password" name="newpassword" class="form-control" required></div>
                                </div>

                                <div class="row" style="margin-top:1%;">
                                    <div class="col-2">Confirm Password</div>
                                    <div class="col-6"><input type="password" placeholder="Enter your new Password again" name="confirmpassword" class="form-control" required></div>
                                </div>

                                <div class="row" style="margin-top:1%;">
                                    <div class="col-2"><button type="submit" name="submit" class="btn btn-primary">Submit</button></div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
          <?php include_once('includes/footer.php');?>
            </div>
        </div>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
<?php } ?>