<?php
session_start();

// Test the controller directly
require_once 'config/config.php';
require_once 'controllers/MedicalRecordController.php';

// Simulate POST data
$_POST = [
    'action' => 'create',
    'patient_id' => '1',
    'doctor_id' => '1',
    'record_date' => '2025-07-12T10:00',
    'status' => 'active',
    'diagnosis' => 'Test Diagnosis',
    'treatment' => 'Test Treatment',
    'subjective' => 'Test Subjective',
    'objective' => 'Test Objective',
    'assessment' => 'Test Assessment',
    'plan' => 'Test Plan',
    'height_cm' => '170',
    'weight_kg' => '70',
    'bmi' => '24.2',
    'blood_pressure' => '120/80',
    'heart_rate' => '72',
    'temperature_c' => '36.5',
    'respiratory_rate' => '16'
];

// Create controller instance
$controller = new MedicalRecordController($conn);

// Test the create method directly
try {
    $controller->createMedicalRecord();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?> 