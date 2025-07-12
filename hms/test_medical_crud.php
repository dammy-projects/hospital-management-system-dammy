<?php
// Test CRUD operations for medical records
require_once 'config/config.php';

echo "<h2>Medical Records CRUD Test</h2>";

// Test 1: List medical records
echo "<h3>Test 1: List Medical Records</h3>";
$url = "controllers/MedicalRecordController.php?action=list&page=1&limit=5";
echo "URL: $url<br>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre><br><br>";

// Test 2: Get a specific medical record
echo "<h3>Test 2: Get Medical Record (ID: 12)</h3>";
$url = "controllers/MedicalRecordController.php?action=get&id=12";
echo "URL: $url<br>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre><br><br>";

// Test 3: Create a medical record
echo "<h3>Test 3: Create Medical Record</h3>";
$url = "controllers/MedicalRecordController.php";
$postData = [
    'action' => 'create',
    'patient_id' => '1',
    'doctor_id' => '1',
    'record_date' => '2024-01-15T10:00',
    'status' => 'active',
    'diagnosis' => 'Test diagnosis',
    'treatment' => 'Test treatment',
    'height_cm' => '170',
    'weight_kg' => '70',
    'bmi' => '24.22',
    'blood_pressure' => '120/80',
    'heart_rate' => '75',
    'temperature_c' => '36.5',
    'respiratory_rate' => '16',
    'subjective' => 'Test subjective',
    'objective' => 'Test objective',
    'assessment' => 'Test assessment',
    'plan' => 'Test plan'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre><br><br>";

// Test 4: Delete a medical record (use a test record)
echo "<h3>Test 4: Delete Medical Record (ID: 25 - if exists)</h3>";
$url = "controllers/MedicalRecordController.php";
$postData = [
    'action' => 'delete',
    'record_id' => '25'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre><br><br>";

echo "<h3>Test Complete</h3>";
?> 