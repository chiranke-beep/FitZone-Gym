<?php
session_start();
session_destroy();
$_SESSION['message'] = "Logged out successfully.";
header("Location: auth.php#login");
exit();
?>