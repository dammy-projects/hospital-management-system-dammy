<?php
require_once 'config/config.php';

echo "Testing Medical Record Controller Fix\n";
echo "===================================\n\n";

// Test the controller with a simple POST request
$postData = [
    'action' => 'create',
    'patient_id' => '1',
    'doctor_id' => '1',
    'record_date' => '2024-01-15T10:00',
    'status' => 'active',
    'diagnosis' => 'Test diagnosis',
    'treatment' => 'Test treatment',
    'subjective' => 'Test subjective',
    'objective' => 'Test objective',
    'assessment' => 'Test assessment',
    'plan' => 'Test plan',
    'height_cm' => '170',
    'weight_kg' => '70',
    'bmi' => '24.2',
    'blood_pressure' => '120/80',
    'heart_rate' => '72',
    'temperature_c' => '36.5',
    'respiratory_rate' => '16'
];

// Simulate POST request
$_POST = $postData;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Capture output
ob_start();

// Include and run the controller
include 'controllers/MedicalRecordController.php';

$output = ob_get_clean();

echo "Controller Output:\n";
echo $output . "\n";

// Check if we got a valid JSON response
$response = json_decode($output, true);
if ($response) {
    if ($response['success']) {
        echo "✓ Controller is working correctly!\n";
        echo "Response: " . $response['message'] . "\n";
        if (isset($response['data']['record_id'])) {
            echo "Created record ID: " . $response['data']['record_id'] . "\n";
        }
    } else {
        echo "✗ Controller returned error: " . $response['message'] . "\n";
    }
} else {
    echo "✗ Controller did not return valid JSON\n";
    echo "Raw output: " . $output . "\n";
}
?> 