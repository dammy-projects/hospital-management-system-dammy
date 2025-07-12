<?php
require_once 'config/config.php';

// Check if admin user already exists
$check_sql = "SELECT COUNT(*) as count FROM users WHERE username = 'admin'";
$check_result = $conn->query($check_sql);
$admin_exists = $check_result->fetch_assoc()['count'] > 0;

if (!$admin_exists) {
    // Create admin user
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $insert_sql = "INSERT INTO users (full_name, username, password, role_id, status) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $full_name = "System Administrator";
    $username = "admin";
    $role_id = 1; // admin role
    $status = "active";
    
    $insert_stmt->bind_param("sssis", $full_name, $username, $admin_password, $role_id, $status);
    
    if ($insert_stmt->execute()) {
        echo "✅ Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "<br>You can now login to the system.<br>";
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        echo "❌ Error creating admin user: " . $conn->error;
    }
    $insert_stmt->close();
} else {
    echo "✅ Admin user already exists!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br>You can login to the system.<br>";
    echo "<a href='login.php'>Go to Login</a>";
}

$conn->close();
?> 