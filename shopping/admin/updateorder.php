<?php
session_start();

include_once 'include/config.php';
if(strlen($_SESSION['alogin'])==0)
{ 
    header('location:index.php');
    exit;
}

$oid=intval($_GET['oid']);

if(isset($_POST['submit2'])){
    $status=$_POST['status'];
    $remark=$_POST['remark'];

    // Insert track history
    $query = mysqli_prepare($con, "INSERT INTO ordertrackhistory(orderId, status, remark) VALUES(?, ?, ?)");
    mysqli_stmt_bind_param($query, "iss", $oid, $status, $remark);
    mysqli_stmt_execute($query);
    
    // Update main order
    $sql = mysqli_prepare($con, "UPDATE orders SET orderStatus=? WHERE id=?");
    mysqli_stmt_bind_param($sql, "si", $status, $oid);
    mysqli_stmt_execute($sql);
    
    echo "<script>alert('Order updated sucessfully...'); window.opener.location.reload(); window.close();</script>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Update Order</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .header { background: #007bff; color: #fff; padding: 15px; font-weight: bold; font-size: 16px; }
        select, textarea, input[type="submit"], input[type="button"] { padding: 8px; width: 100%; box-sizing: border-box; }
        input[type="submit"] { background: #28a745; color: #fff; border: none; cursor: pointer; padding: 10px; font-weight: bold; }
        input[type="button"] { background: #6c757d; color: #fff; border: none; cursor: pointer; padding: 10px; font-weight: bold; margin-top: 10px; }
        .history { background: #f1f1f1; font-size: 12px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <form name="updateticket" id="updateticket" method="post"> 
        <div class="header">Update Order #<?php echo $oid;?></div>
        
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2"><b>Current Order History</b></td>
            </tr>
            
            <tr>
                <td colspan="2" style="padding: 0;">
                    <table class="history">
                        <tr style="background: #ddd; font-weight: bold;">
                            <td>Date</td>
                            <td>Status</td>
                            <td>Remark</td>
                        </tr>
                        <?php 
                        $ret = mysqli_query($con,"SELECT * FROM ordertrackhistory WHERE orderId='$oid' ORDER BY postingDate DESC");
                        while($row=mysqli_fetch_array($ret)) {
                        ?>
                        <tr>
                            <td><?php echo date('d M Y h:i A', strtotime($row['postingDate']));?></td>
                            <td><b><?php echo $row['status'];?></b></td>
                            <td><?php echo $row['remark'];?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
            
            <?php 
            $rt = mysqli_query($con,"SELECT orderStatus FROM orders WHERE id='$oid'");
            $num = mysqli_fetch_array($rt);
            $currentStatus = $num['orderStatus'];
            
            if($currentStatus == 'Delivered' || $currentStatus == 'Cancelled') { 
            ?>
            <tr>
                <td colspan="2" style="color: red; text-align: center; padding: 20px;">
                    <b>This order is already <?php echo $currentStatus; ?>. Further updates are not allowed.</b><br><br>
                    <input type="button" value="Close Window" onClick="window.close();" />
                </td>
            </tr>
            <?php } else { ?>
            <tr>
                <td width="20%"><b>Update Status:</b></td>
                <td>
                    <select name="status" required>
                        <option value="">-- Select Status --</option>
                        <option value="In Process" <?php if($currentStatus=='In Process') echo 'selected';?>>In Process</option>
                        <option value="Packed" <?php if($currentStatus=='Packed') echo 'selected';?>>Packed</option>
                        <option value="Dispatched" <?php if($currentStatus=='Dispatched') echo 'selected';?>>Dispatched</option>
                        <option value="In Transit" <?php if($currentStatus=='In Transit') echo 'selected';?>>In Transit</option>
                        <option value="Out For Delivery" <?php if($currentStatus=='Out For Delivery') echo 'selected';?>>Out For Delivery</option>
                        <option value="Delivered" <?php if($currentStatus=='Delivered') echo 'selected';?>>Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><b>Remark:</b></td>
                <td>
                    <textarea cols="50" rows="5" name="remark" required placeholder="Enter remarks for the customer..."></textarea>
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit2" value="Update Order Status" />
                    <input type="button" value="Cancel & Close" onClick="window.close();" />
                </td>
            </tr>
            <?php } ?>
        </table>
    </form>

</body>
</html>