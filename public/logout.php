<?php
session_start();
session_destroy();
setcookie('user_id', '', time() - 3600, '/');
setcookie('user_name', '', time() - 3600, '/');
header("Location: ../index.php");
exit();
