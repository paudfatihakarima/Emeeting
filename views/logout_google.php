<?php
session_start();
unset($_SESSION['access_token']);
header("Location: login_google.php");
exit();
?>
