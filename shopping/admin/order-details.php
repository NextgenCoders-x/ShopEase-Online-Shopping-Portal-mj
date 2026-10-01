<?php
session_start();
include_once('includes/config.php');
if(strlen($_SESSION['aid'])==0)
{	
    header('location:logout.php');
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$orderid = isset($_GET['oid']) ? intval($_GET['oid']) : intval($_GET['orderid']);

// Fetch main order details
$orderQuery = mysqli_query($con, "SELECT o.*, u.name as username, u.email as useremail, u.contactno as usercontact FROM orders o JOIN users u ON o.userId=u.id WHERE o.id='$orderid'");
$order = mysqli_fetch_array($orderQuery);

if(!$order) {
    echo "Order not found!";
    exit;
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
        <title>Shopping Portal | Order Details</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="js/all.min.js" crossorigin="anonymous"></script>
        <script language="javascript" type="text/javascript">
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
 if(popUpWin)
{
if(!popUpWin.closed) popUpWin.close();
}
popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}
</script>
    </head>
    <body class="sb-nav-fixed">
 <?php include_once('includes/header.php');?>
        <div id="layoutSidenav">
       <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Order Details #<?php echo htmlentities($order['orderNumber'] ?: 'ORD-000'.$orderid);?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="all-orders.php">Orders</a></li>
                            <li class="breadcrumb-item active">Order Details</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                               Order Information
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">		
                                    <table class="table table-bordered" >
                                        <tbody>									
                                            <tr>
                                                <th>Order Id</th>
                                                <td><?php echo htmlentities($order['orderNumber'] ?: 'ORD-000'.$orderid);?></td>
                                                <th>Order Date</th>
                                                <td><?php echo date('d M Y h:i A', strtotime($order['orderDate']));?></td>
                                            </tr>
                                            <tr>
                                                <th>Customer Name</th>
                                                <td><?php echo htmlentities($order['username']);?></td>
                                                <th>Contact Details</th>
                                                <td><?php echo htmlentities($order['usercontact']);?> <br> <?php echo htmlentities($order['useremail']);?></td>
                                            </tr>
                                            <tr>
                                                <th>Shipping Address</th>
                                                <td colspan="3">
                                                    <b><?php echo htmlentities($order['shipName'] ?: $order['username']);?></b><br>
                                                    <?php echo htmlentities($order['shipAddress']);?><br>
                                                    <?php echo htmlentities($order['shipCity']);?>, <?php echo htmlentities($order['shipState']);?> - <?php echo htmlentities($order['shipPincode']);?><br>
                                                    Phone: <?php echo htmlentities($order['shipMobile']);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Payment Method</th>
                                                <td><?php echo htmlentities($order['paymentMethod']);?> (<?php echo htmlentities($order['paymentStatus']);?>)</td>
                                                <th>Current Status</th>
                                                <td><b style="color: blue;"><?php echo htmlentities($order['orderStatus']);?></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                                <br>
                                <h4>Order Items</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" >
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Image</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $itemsQuery = mysqli_query($con, "SELECT * FROM order_items WHERE orderId='$orderid'");
                                            $cnt=1;
                                            if(mysqli_num_rows($itemsQuery) > 0) {
                                                while($item = mysqli_fetch_array($itemsQuery)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $cnt++;?></td>
                                                <td><?php echo htmlentities($item['productName']);?></td>
                                                <td><img src="productimages/<?php echo htmlentities($item['productId']."/".$item['productImage']);?>" width="50" height="50"></td>
                                                <td>₹<?php echo htmlentities($item['productPrice']);?></td>
                                                <td><?php echo htmlentities($item['quantity']);?></td>
                                                <td>₹<?php echo htmlentities($item['subtotal']);?></td>
                                            </tr>
                                            <?php 
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No items found or old order format.</td></tr>";
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="5" align="right"><b>Total:</b></td>
                                                <td><b>₹<?php echo htmlentities($order['subtotal']);?></b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" align="right"><b>Shipping Charge:</b></td>
                                                <td><b>₹<?php echo htmlentities($order['shippingCharge']);?></b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" align="right"><b style="font-size: 16px;">Grand Total:</b></td>
                                                <td><b style="font-size: 16px; color: green;">₹<?php echo htmlentities($order['grandTotal']);?></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                                <br>
                                <h4>Order Tracking History</h4>
                                <?php 
                                $ret = mysqli_query($con,"SELECT * FROM ordertrackhistory WHERE orderId='$orderid' ORDER BY postingDate DESC");
                                $count=mysqli_num_rows($ret);
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" >
                                        <?php if($count>0){ ?>
                                            <tr>
                                                <th>Status</th>
                                                <th>Remark</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        <?php while($row=mysqli_fetch_array($ret)){?>
                                            <tr>
                                            <td><b><?php echo $row['status'];?></b></td>
                                            <td><?php echo $row['remark'];?></td>
                                            <td><?php echo date('d M Y h:i A', strtotime($row['postingDate']));?></td>
                                            </tr>
                                        <?php }} else { echo "<tr><td>No tracking history found.</td></tr>"; } ?>
                                    </table>
                                </div>

                                <br>
                                <?php if($order['orderStatus'] != 'Delivered' && $order['orderStatus'] != 'Cancelled') { ?>
                                    <a href="javascript:void(0);" onClick="popUpWindow('updateorder.php?oid=<?php echo htmlentities($orderid);?>', 200, 200, 600, 500);" title="Update order" class="btn btn-primary">Take Action / Update Status</a>
                                <?php } else { ?>
                                    <div class="alert alert-info">Order is already <?php echo htmlentities($order['orderStatus']); ?>. No further actions can be taken.</div>
                                <?php } ?>

                            </div>
                        </div>						
                    </div>
                </main>
<?php include_once('includes/footer.php');?>
            </div>
        </div>
        <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>