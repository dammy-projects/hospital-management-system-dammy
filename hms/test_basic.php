<?php
// Basic test to isolate the issue
echo "<h2>Basic PHP Test</h2>";
echo "<p>✅ PHP is working</p>";

// Test 1: Session
session_start();
echo "<p>✅ Session started</p>";

if (isset($_SESSION['user_id'])) {
    echo "<p>✅ User logged in: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p>❌ User not logged in</p>";
}

// Test 2: Config file
echo "<p>🔧 Loading config...</p>";
try {
    require_once 'config/config.php';
    echo "<p>✅ Config loaded</p>";
} catch (Exception $e) {
    echo "<p>❌ Config error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 3: Database connection
echo "<p>🔧 Testing database...</p>";
if (isset($conn) && $conn) {
    echo "<p>✅ Database connected</p>";
} else {
    echo "<p>❌ Database connection failed</p>";
    exit;
}

// Test 4: Simple query
echo "<p>🔧 Testing simple query...</p>";
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM patients");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✅ Query successful - " . $row['count'] . " patients found</p>";
    } else {
        echo "<p>❌ Query failed</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Query error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p>✅ Test completed successfully!</p>";
?> 