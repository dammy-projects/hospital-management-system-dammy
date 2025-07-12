<?php
require_once 'config/config.php';

// Set a simple password for testing
$new_password = "admin123";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the first user's password
$update_sql = "UPDATE users SET password = ? WHERE user_id = 1";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "<h2>Password Reset Successful!</h2>";
    echo "<p><strong>Username:</strong> sarah.admin</p>";
    echo "<p><strong>New Password:</strong> $new_password</p>";
    echo "<p><strong>Hashed Password:</strong> $hashed_password</p>";
    echo "<p>You can now use this password to test the profile password change functionality.</p>";
} else {
    echo "<h2>Error resetting password</h2>";
    echo "<p>Error: " . $stmt->error . "</p>";
}

$conn->close();
?> 