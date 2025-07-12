<?php
require_once 'config/config.php';

echo "<h2>Password Debug Information</h2>";

// Get user information
$user_id = 1; // Test with first user
$user_sql = "SELECT username, password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

echo "<h3>User: " . htmlspecialchars($user['username']) . "</h3>";
echo "<p><strong>Stored Hash:</strong> " . htmlspecialchars($user['password']) . "</p>";

// Test some common default passwords
$test_passwords = [
    'password',
    '123456',
    'admin',
    'user',
    'test',
    'sarah.admin',
    'admin123',
    'password123'
];

echo "<h3>Testing Password Verification:</h3>";
echo "<ul>";

foreach ($test_passwords as $test_pwd) {
    $is_valid = password_verify($test_pwd, $user['password']);
    $status = $is_valid ? "✅ MATCH" : "❌ NO MATCH";
    echo "<li><strong>$test_pwd</strong>: $status</li>";
}

echo "</ul>";

// Show how to create a new password hash
echo "<h3>To create a new password hash:</h3>";
echo "<p>Use: <code>password_hash('your_password', PASSWORD_DEFAULT)</code></p>";

// Test creating a new hash
$new_password = "admin123";
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
echo "<p><strong>Example:</strong> password_hash('$new_password', PASSWORD_DEFAULT) = $new_hash</p>";

echo "<h3>To update a user's password:</h3>";
echo "<p>UPDATE users SET password = '$new_hash' WHERE user_id = 1;</p>";

$conn->close();
?> 