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
        $category=$_POST['category'];
        $description=$_POST['description'];
        $id=intval($_GET['id']);
        $sql=mysqli_query($con,"update category set categoryName='$category',categoryDescription='$description',updationDate='$currentTime' where id='$id'");
        echo "<script>alert('Category Updated successfully');</script>";
        echo "<script>window.location.href='manage-categories.php'</script>";
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
        <title>Shopping Portal | Edit Category</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body>
   <?php include_once('includes/header.php');?>
        <div id="layoutSidenav">
   <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Edit Category</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="manage-categories.php">Categories</a></li>
                            <li class="breadcrumb-item active">Edit Category</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
<?php
$id=intval($_GET['id']);
$query=mysqli_query($con,"select * from category where id='$id'");
while($row=mysqli_fetch_array($query))
{
?>                                 
<form method="post">                                
<div class="row">
    <div class="col-2">Category Name</div>
    <div class="col-6"><input type="text" placeholder="Enter category Name" name="category" value="<?php echo htmlentities($row['categoryName']);?>" class="form-control" required></div>
</div>

<div class="row" style="margin-top:1%;">
    <div class="col-2">Description</div>
    <div class="col-6"><textarea class="form-control" name="description" rows="5"><?php echo htmlentities($row['categoryDescription']);?></textarea></div>
</div>

<div class="row" style="margin-top:1%;">
    <div class="col-2"><button type="submit" name="submit" class="btn btn-primary">Update</button></div>
</div>
</form>
<?php } ?>
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