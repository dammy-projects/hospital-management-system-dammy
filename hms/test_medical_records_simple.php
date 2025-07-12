<?php
// Simple test to verify medical records functionality
require_once 'config/config.php';

try {
    // Test database connection
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test if medical_records table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM medical_records");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Medical Records Test Results:\n";
    echo "============================\n";
    echo "Database connection: SUCCESS\n";
    echo "Medical records table exists: SUCCESS\n";
    echo "Number of medical records: " . $result['count'] . "\n";
    
    // Test if patients table has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of patients: " . $result['count'] . "\n";
    
    // Test if doctors table has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of doctors: " . $result['count'] . "\n";
    
    // Test controller response
    $controller_url = "controllers/MedicalRecordController.php?action=list&page=1&limit=6";
    echo "\nController test URL: " . $controller_url . "\n";
    echo "Please test the medical records page in your browser to verify JavaScript functionality.\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 