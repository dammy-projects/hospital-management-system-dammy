<?php
// Test script for medical record update functionality
require_once 'config/config.php';

echo "Testing Medical Record Update Functionality\n";
echo "==========================================\n\n";

// Test data for update - using existing record ID 12
$testData = [
    'action' => 'update',
    'record_id' => 12, // Using existing record ID
    'patient_id' => 1,
    'doctor_id' => 1,
    'record_date' => '2024-01-15T10:30',
    'status' => 'active',
    'height_cm' => 175.5,
    'weight_kg' => 70.2,
    'bmi' => 22.9,
    'blood_pressure' => '120/80',
    'heart_rate' => 72,
    'temperature_c' => 36.8,
    'respiratory_rate' => 16,
    'subjective' => 'Patient reports feeling better',
    'objective' => 'Vital signs stable',
    'assessment' => 'Improving',
    'plan' => 'Continue current treatment',
    'diagnosis' => 'Common cold',
    'treatment' => 'Rest and fluids'
];

echo "Test Data:\n";
foreach ($testData as $key => $value) {
    echo "$key: $value\n";
}
echo "\n";

// Simulate POST request
$_POST = $testData;
$_SERVER['REQUEST_METHOD'] = 'POST';

// Capture output
ob_start();

// Include and run the controller
require_once 'controllers/MedicalRecordController.php';

$output = ob_get_clean();

echo "Controller Output:\n";
echo $output;
echo "\n";

// Try to parse JSON response
$response = json_decode($output, true);
if ($response) {
    echo "Parsed Response:\n";
    echo "Success: " . ($response['success'] ? 'true' : 'false') . "\n";
    echo "Message: " . ($response['message'] . "\n");
    if (isset($response['data'])) {
        echo "Data: " . print_r($response['data'], true) . "\n";
    }
} else {
    echo "Failed to parse JSON response\n";
    echo "Raw output: $output\n";
}
?> 