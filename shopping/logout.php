<?php
session_start();
include("includes/config.php");

// Update userlog
if(isset($_SESSION['login'])) {
    $email = $_SESSION['login'];
    $uip = $_SERVER['REMOTE_ADDR'];
    mysqli_query($con, "UPDATE userlog SET logout = NOW() WHERE userEmail = '$email' AND userip = '$uip' ORDER BY id DESC LIMIT 1");
}

$_SESSION['login'] = "";
$_SESSION['id'] = "";
$_SESSION['username'] = "";

unset($_SESSION['login']);
unset($_SESSION['id']);
unset($_SESSION['username']);

// Optionally keep cart if we want to, or clear it
// unset($_SESSION['cart']); 

session_destroy();
session_start();
setFlash('success', 'You have been logged out successfully.');
?>
<script language="javascript">
document.location="index.php";
</script>
