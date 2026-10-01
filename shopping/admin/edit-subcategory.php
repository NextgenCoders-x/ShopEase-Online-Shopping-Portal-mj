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
        $subcat=$_POST['subcategory'];
        $id=intval($_GET['id']);
        $sql=mysqli_query($con,"update subcategory set categoryid='$category',subcategory='$subcat',updationDate='$currentTime' where id='$id'");
        echo "<script>alert('Sub-Category Updated successfully');</script>";
        echo "<script>window.location.href='manage-subcategories.php'</script>";
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
        <title>Shopping Portal | Edit SubCategory</title>
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
                        <h1 class="mt-4">Edit SubCategory</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="manage-subcategories.php">SubCategories</a></li>
                            <li class="breadcrumb-item active">Edit SubCategory</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
<?php
$id=intval($_GET['id']);
$query=mysqli_query($con,"select category.id,category.categoryName,subcategory.subcategory from subcategory join category on category.id=subcategory.categoryid where subcategory.id='$id'");
while($row=mysqli_fetch_array($query))
{
?>		
<form method="post">                                
<div class="row">
    <div class="col-2">Category</div>
    <div class="col-6">
        <select name="category" class="form-control" required>
            <option value="<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($catname=$row['categoryName']);?></option>
            <?php 
            $ret=mysqli_query($con,"select * from category");
            while($result=mysqli_fetch_array($ret))
            {
                $cat=$result['categoryName'];
                if($catname==$cat) {
                    continue;
                } else {
            ?>
            <option value="<?php echo $result['id'];?>"><?php echo $result['categoryName'];?></option>
            <?php } }?>
        </select>
    </div>
</div>

<div class="row" style="margin-top:1%;">
    <div class="col-2">SubCategory Name</div>
    <div class="col-6"><input type="text" placeholder="Enter subcategory Name" name="subcategory" value="<?php echo htmlentities($row['subcategory']);?>" class="form-control" required></div>
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