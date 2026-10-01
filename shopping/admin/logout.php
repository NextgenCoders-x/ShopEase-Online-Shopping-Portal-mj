<?php
session_start();
unset($_SESSION['alogin']);
unset($_SESSION['aid']);
$_SESSION['errmsg']="You have successfully logout";
?>
<script language="javascript">
document.location="index.php";
</script>
