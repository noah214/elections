<?php
require_once "add_logs.php";
// Start the session
session_start();
$user_id = $_SESSION['user_id'];
add_logs($conn, $user_id, 'LOGOUT');

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ../public/login.php");
exit();
?> 