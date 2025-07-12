<?php
// Simple test to check controller output
require_once 'config/config.php';

// Set up the request
$_GET['action'] = 'list';
$_GET['page'] = '1';
$_GET['limit'] = '5';

// Capture output
ob_start();

// Include the controller
include 'controllers/MedicalRecordController.php';

$output = ob_get_clean();

echo "<h2>Controller Output Test</h2>";
echo "<h3>Raw Output:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

echo "<h3>Output Length: " . strlen($output) . "</h3>";

if (empty($output)) {
    echo "<p style='color: red;'>No output from controller!</p>";
} else {
    echo "<p style='color: green;'>Controller is producing output.</p>";
    
    // Try to decode as JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "<h3>JSON Decoded Successfully:</h3>";
        echo "<pre>" . print_r($json, true) . "</pre>";
    } else {
        echo "<p style='color: red;'>Output is not valid JSON!</p>";
    }
}
?> 