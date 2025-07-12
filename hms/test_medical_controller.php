<?php
// Test the MedicalRecordController
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing MedicalRecordController...\n";

// Check if config file exists
if (!file_exists('config/config.php')) {
    echo "ERROR: config/config.php not found\n";
    exit;
}

// Include config
require_once 'config/config.php';

echo "Config loaded successfully\n";

// Check if controller file exists
if (!file_exists('controllers/MedicalRecordController.php')) {
    echo "ERROR: controllers/MedicalRecordController.php not found\n";
    exit;
}

// Include controller
require_once 'controllers/MedicalRecordController.php';

echo "Controller loaded successfully\n";

// Test database connection
if (!isset($conn) || !$conn) {
    echo "ERROR: Database connection not available\n";
    exit;
}

echo "Database connection available\n";

// Test creating controller instance
try {
    $controller = new MedicalRecordController($conn);
    echo "Controller instance created successfully\n";
} catch (Exception $e) {
    echo "ERROR creating controller: " . $e->getMessage() . "\n";
    exit;
}

echo "Test completed successfully\n";
?> 