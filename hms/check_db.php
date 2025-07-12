<?php
require_once 'config/config.php';

echo "Checking medical_records table...\n";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'medical_records'");
if ($result && $result->num_rows > 0) {
    echo "✓ medical_records table exists\n";
    
    // Check table structure
    $result = $conn->query("DESCRIBE medical_records");
    if ($result) {
        echo "Table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']}: {$row['Type']} ({$row['Null']})\n";
        }
    } else {
        echo "Error describing table: " . $conn->error . "\n";
    }
} else {
    echo "✗ medical_records table does not exist\n";
    
    // Check what tables exist
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "Available tables:\n";
        while ($row = $result->fetch_assoc()) {
            $tableName = array_values($row)[0];
            echo "- $tableName\n";
        }
    }
}
?> 