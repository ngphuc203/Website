<?php
session_start();
session_unset();
session_destroy();
header('Location: login.php'); // Chuyển đến trang đăng nhập
exit();
?>
