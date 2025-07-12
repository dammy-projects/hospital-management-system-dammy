<?php
require_once 'config/config.php';

echo "Testing medical record creation...\n";

// Simulate the POST data that's being sent
$_POST = [
    'action' => 'create',
    'patient_id' => '4',
    'doctor_id' => '3',
    'record_date' => '2025-07-12T07:31',
    'status' => 'active',
    'height_cm' => '123',
    'weight_kg' => '123',
    'bmi' => '81.30',
    'blood_pressure' => '123',
    'heart_rate' => '123',
    'temperature_c' => '123',
    'respiratory_rate' => '123',
    'subjective' => '123',
    'objective' => '123',
    'assessment' => '12312',
    'plan' => '3123',
    'diagnosis' => '123',
    'treatment' => '123'
];

echo "POST data: " . print_r($_POST, true) . "\n";

// Test the SQL query directly
$patientId = intval($_POST['patient_id'] ?? 0);
$doctorId = intval($_POST['doctor_id'] ?? 0);
$recordDate = trim($_POST['record_date'] ?? '');
$status = trim($_POST['status'] ?? 'active');
$heightCm = $_POST['height_cm'] ? floatval($_POST['height_cm']) : null;
$weightKg = $_POST['weight_kg'] ? floatval($_POST['weight_kg']) : null;
$bmi = $_POST['bmi'] ? floatval($_POST['bmi']) : null;
$bloodPressure = trim($_POST['blood_pressure'] ?? 'N/A');
$heartRate = $_POST['heart_rate'] ? intval($_POST['heart_rate']) : null;
$temperatureC = $_POST['temperature_c'] ? floatval($_POST['temperature_c']) : null;
$respiratoryRate = $_POST['respiratory_rate'] ? intval($_POST['respiratory_rate']) : null;
$subjective = trim($_POST['subjective'] ?? '');
$objective = trim($_POST['objective'] ?? '');
$assessment = trim($_POST['assessment'] ?? '');
$plan = trim($_POST['plan'] ?? '');
$diagnosis = trim($_POST['diagnosis'] ?? '');
$treatment = trim($_POST['treatment'] ?? '');

echo "Processed data:\n";
echo "patientId: $patientId\n";
echo "doctorId: $doctorId\n";
echo "recordDate: $recordDate\n";
echo "status: $status\n";
echo "bloodPressure: $bloodPressure\n";

// Test the SQL query
$sql = "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, subjective, objective, 
               assessment, plan, height_cm, weight_kg, bmi, blood_pressure, heart_rate, temperature_c, 
               respiratory_rate, lab_images, record_date, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

echo "SQL: $sql\n";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Prepare error: " . $conn->error . "\n";
    exit;
}

// Create a variable for the null value to avoid reference error
$labImages = null;

$bindResult = $stmt->bind_param('iissssssdddsidsss', 
    $patientId, $doctorId, $diagnosis, $treatment, $subjective, $objective, 
    $assessment, $plan, $heightCm, $weightKg, $bmi, $bloodPressure, $heartRate, 
    $temperatureC, $respiratoryRate, $labImages, $recordDate, $status);

if (!$bindResult) {
    echo "Bind error: " . $stmt->error . "\n";
    exit;
}

echo "Parameters bound successfully\n";

$executeResult = $stmt->execute();
if ($executeResult) {
    $recordId = $stmt->insert_id;
    echo "✓ Successfully created record with ID: $recordId\n";
} else {
    echo "✗ Execute error: " . $stmt->error . "\n";
    echo "SQL State: " . $stmt->sqlstate . "\n";
    echo "Error Code: " . $stmt->errno . "\n";
}

$stmt->close();
?> 