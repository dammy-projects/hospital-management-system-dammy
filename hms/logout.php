<?php
session_start();
require_once 'config/config.php';

// Log the logout if user is logged in
if (isset($_SESSION['user_id'])) {
    $log_sql = "INSERT INTO system_logs (user_id, action) VALUES (?, 'User logged out')";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("i", $_SESSION['user_id']);
    $log_stmt->execute();
    $log_stmt->close();
}

// Destroy all session data
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?> 