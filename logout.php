<?php
session_start();
error_reporting(0);
$_SESSION['clube'] = "";
session_unset();
session_destroy();
setcookie('rememberMe', '', time() - 3600, "/");
?>
<!-- voltar ao index -->
<script language="javascript">
document.location="index.php";
</script>
