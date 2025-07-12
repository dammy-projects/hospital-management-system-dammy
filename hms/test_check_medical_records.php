<?php
// Test script to check medical records in database
require_once 'config/config.php';

echo "Checking Medical Records in Database\n";
echo "==================================\n\n";

// Check if medical_records table exists
$sql = "SHOW TABLES LIKE 'medical_records'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "✓ medical_records table exists\n\n";
    
    // Count total records
    $countSql = "SELECT COUNT(*) as total FROM medical_records";
    $countResult = $conn->query($countSql);
    $totalRecords = $countResult->fetch_assoc()['total'];
    echo "Total medical records: $totalRecords\n\n";
    
    if ($totalRecords > 0) {
        // Get first few records
        $sql = "SELECT record_id, patient_id, doctor_id, record_date, status FROM medical_records LIMIT 5";
        $result = $conn->query($sql);
        
        echo "Sample records:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['record_id']}, Patient: {$row['patient_id']}, Doctor: {$row['doctor_id']}, Date: {$row['record_date']}, Status: {$row['status']}\n";
        }
    } else {
        echo "No medical records found in database\n";
    }
} else {
    echo "✗ medical_records table does not exist\n";
}

// Check patients table
$sql = "SHOW TABLES LIKE 'patients'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $countSql = "SELECT COUNT(*) as total FROM patients";
    $countResult = $conn->query($countSql);
    $totalPatients = $countResult->fetch_assoc()['total'];
    echo "\nTotal patients: $totalPatients\n";
} else {
    echo "\n✗ patients table does not exist\n";
}

// Check doctors table
$sql = "SHOW TABLES LIKE 'doctors'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $countSql = "SELECT COUNT(*) as total FROM doctors";
    $countResult = $conn->query($countSql);
    $totalDoctors = $countResult->fetch_assoc()['total'];
    echo "Total doctors: $totalDoctors\n";
} else {
    echo "✗ doctors table does not exist\n";
}
?> 