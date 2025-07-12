<?php
// Direct test of PatientController logic
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h2 style='color: red;'>Please log in to HMS first</h2>";
    exit;
}

echo "<h2>Direct PatientController Test</h2>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";

// Include the necessary files
require_once 'config/config.php';

echo "<h3>1. Testing Database Connection</h3>";
if ($conn) {
    echo "✅ Database connected<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

echo "<h3>2. Testing Patient Query</h3>";
try {
    $patientId = 1;
    $sql = "SELECT patient_id, first_name, middle_name, last_name, date_of_birth, gender, 
                   contact_number, email, address, medical_history, status, 
                   emergency_contact_name, emergency_contact_relationship, emergency_contact_phone, emergency_contact_address,
                   guardian_name, guardian_relationship, guardian_phone, guardian_address,
                   created_at, updated_at 
            FROM patients 
            WHERE patient_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($patient = $result->fetch_assoc()) {
        echo "✅ Patient found: " . htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) . "<br>";
    } else {
        echo "❌ Patient not found<br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Patient query error: " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

echo "<h3>3. Testing Insurance Query</h3>";
try {
    $insuranceSQL = "SELECT pi.*, 
                           ip.provider_name,
                           ip.contact_number AS provider_contact,
                           ip.address AS provider_address
                    FROM patient_insurance pi 
                    LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                    WHERE pi.patient_id = ? AND pi.status = 'active'";
    
    $insuranceStmt = $conn->prepare($insuranceSQL);
    $insuranceStmt->bind_param('i', $patientId);
    $insuranceStmt->execute();
    $insuranceResult = $insuranceStmt->get_result();
    
    $insurances = [];
    while ($insuranceRow = $insuranceResult->fetch_assoc()) {
        $insurances[] = $insuranceRow;
    }
    
    echo "✅ Insurance query executed successfully<br>";
    echo "📋 Number of insurances found: " . count($insurances) . "<br>";
    
    if (count($insurances) > 0) {
        echo "<h4>Insurance Details:</h4>";
        foreach ($insurances as $i => $insurance) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px 0;'>";
            echo "<strong>Insurance " . ($i + 1) . ":</strong><br>";
            echo "Provider: " . htmlspecialchars($insurance['provider_name'] ?? 'N/A') . "<br>";
            echo "Number: " . htmlspecialchars($insurance['insurance_number'] ?? 'N/A') . "<br>";
            echo "Status: " . htmlspecialchars($insurance['status'] ?? 'N/A') . "<br>";
            echo "Contact: " . htmlspecialchars($insurance['provider_contact'] ?? 'N/A') . "<br>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "❌ Insurance query error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<h3>4. Testing PatientController Class</h3>";
try {
    // Simulate the request parameters
    $_GET['action'] = 'get';
    $_GET['id'] = '1';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    // Capture output
    ob_start();
    
    // Include and test the controller
    require_once 'controllers/PatientController.php';
    $controller = new PatientController($conn);
    $controller->handleRequest();
    
    $output = ob_get_clean();
    
    echo "✅ PatientController executed<br>";
    echo "<h4>Controller Output:</h4>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow: auto;'>";
    echo htmlspecialchars($output);
    echo "</pre>";
    
    $data = json_decode($output, true);
    if ($data) {
        if ($data['success']) {
            echo "<p style='color: green;'>✅ Controller returned success</p>";
            if (isset($data['insurances'])) {
                echo "<p>🛡️ Insurances in response: " . count($data['insurances']) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ No insurances key in response</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Controller returned error: " . htmlspecialchars($data['message']) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid JSON from controller</p>";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ PatientController error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "Stack trace: <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?> 