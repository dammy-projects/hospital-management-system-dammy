<?php
// Simple test for medical record update
require_once 'config/config.php';

echo "Testing Medical Record Update (Simple Version)\n";
echo "============================================\n\n";

// Test data
$_POST = [
    'action' => 'update',
    'record_id' => 28,
    'patient_id' => 4,
    'doctor_id' => 5,
    'record_date' => '2025-07-12T09:34',
    'status' => 'active',
    'height_cm' => 123.00,
    'weight_kg' => 33.00,
    'bmi' => 21.81,
    'blood_pressure' => '123',
    'heart_rate' => 123,
    'temperature_c' => 99.99,
    'respiratory_rate' => 123,
    'subjective' => 'TEST UPDATE',
    'objective' => 'TEST UPDATE',
    'assessment' => 'TEST UPDATE',
    'plan' => 'TEST UPDATE',
    'diagnosis' => 'TEST UPDATE',
    'treatment' => 'TEST UPDATE'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Test data prepared\n";
echo "Record ID: " . $_POST['record_id'] . "\n";
echo "Patient ID: " . $_POST['patient_id'] . "\n";
echo "Doctor ID: " . $_POST['doctor_id'] . "\n\n";

// Capture output
ob_start();

// Include controller
require_once 'controllers/MedicalRecordController.php';

$output = ob_get_clean();

echo "Controller Output:\n";
echo $output;
echo "\n";

// Try to parse JSON
$response = json_decode($output, true);
if ($response) {
    echo "Success: " . ($response['success'] ? 'true' : 'false') . "\n";
    echo "Message: " . $response['message'] . "\n";
} else {
    echo "Failed to parse JSON response\n";
    echo "Raw output: $output\n";
}
?> 