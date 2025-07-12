<?php
// Suppress error output to prevent HTML before JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config/config.php';

// Start output buffering to catch any unwanted output
ob_start();

class PatientController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        
        try {
            switch ($action) {
                case 'list':
                    $this->listPatients();
                    break;
                case 'list_all':
                    $this->listAllPatients();
                    break;
                case 'get':
                    $this->getPatient();
                    break;
                case 'create':
                    $this->createPatient();
                    break;
                case 'update':
                    $this->updatePatient();
                    break;
                case 'delete':
                    $this->deletePatient();
                    break;
                default:
                    $this->sendResponse(false, 'Invalid action specified');
            }
        } catch (Exception $e) {
            error_log("Patient Controller Error: " . $e->getMessage());
            $this->sendResponse(false, 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function listPatients() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 6)));
        $offset = ($page - 1) * $limit;
        
        $search = trim($_GET['search'] ?? '');
        $gender = trim($_GET['gender'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sort = trim($_GET['sort'] ?? 'first_name');
        
        // Validate sort field
        $allowedSorts = ['first_name', 'last_name', 'date_of_birth', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'first_name';
        }
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $whereConditions[] = "(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR contact_number LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sssss';
        }
        
        if (!empty($gender)) {
            $whereConditions[] = "gender = ?";
            $params[] = $gender;
            $types .= 's';
        }
        
        if (!empty($status)) {
            $whereConditions[] = "status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM patients {$whereClause}";
        $countStmt = $this->conn->prepare($countSql);
        
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRecords = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);
        
        // Get patients with all fields including new emergency contact and guardian fields
        $sql = "SELECT patient_id, first_name, middle_name, last_name, date_of_birth, gender, 
                       contact_number, email, address, medical_history, status, 
                       emergency_contact_name, emergency_contact_relationship, emergency_contact_phone, emergency_contact_address,
                       guardian_name, guardian_relationship, guardian_phone, guardian_address,
                       created_at, updated_at 
                FROM patients 
                {$whereClause} 
                ORDER BY {$sort} ASC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $allParams = $params;
        $allTypes = $types;
        $allParams[] = $limit;
        $allParams[] = $offset;
        $allTypes .= 'ii';
        
        if (!empty($allParams)) {
            $stmt->bind_param($allTypes, ...$allParams);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        
        $this->sendResponse(true, 'Patients retrieved successfully', [
            'patients' => $patients,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'records_per_page' => $limit
            ]
        ]);
    }
    
    private function listAllPatients() {
        $sql = "SELECT patient_id, first_name, middle_name, last_name 
                FROM patients 
                WHERE status = 'active' 
                ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        
        $this->sendResponse(true, 'All patients retrieved successfully', [
            'data' => $patients
        ]);
    }
    
    private function getPatient() {
        $patientId = intval($_GET['id'] ?? 0);
        
        if ($patientId <= 0) {
            $this->sendResponse(false, 'Valid patient ID is required');
            return;
        }
        
        $sql = "SELECT patient_id, first_name, middle_name, last_name, date_of_birth, gender, 
                       contact_number, email, address, medical_history, status, 
                       emergency_contact_name, emergency_contact_relationship, emergency_contact_phone, emergency_contact_address,
                       guardian_name, guardian_relationship, guardian_phone, guardian_address,
                       created_at, updated_at 
                FROM patients 
                WHERE patient_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($patient = $result->fetch_assoc()) {
            // Get patient insurance information - fetch ALL insurance records
            $insuranceSQL = "SELECT pi.patient_insurance_id,
                                   pi.patient_id,
                                   pi.insurance_provider_id,
                                   pi.insurance_number,
                                   pi.status,
                                   ip.provider_name,
                                   ip.contact_number AS provider_contact,
                                   ip.address AS provider_address,
                                   ip.status AS provider_status
                            FROM patient_insurance pi 
                            LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                            WHERE pi.patient_id = ?
                            ORDER BY pi.status DESC, pi.patient_insurance_id ASC";
            
            $insuranceStmt = $this->conn->prepare($insuranceSQL);
            $insuranceStmt->bind_param('i', $patientId);
            $insuranceStmt->execute();
            $insuranceResult = $insuranceStmt->get_result();
            
            $insurances = [];
            while ($insuranceRow = $insuranceResult->fetch_assoc()) {
                $insurances[] = $insuranceRow;
            }
            
            $this->sendResponse(true, 'Patient retrieved successfully', [
                'patient' => $patient,
                'insurances' => $insurances
            ]);
        } else {
            $this->sendResponse(false, 'Patient not found');
        }
    }
    
    private function createPatient() {
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $medicalHistory = trim($_POST['medical_history'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        
        // Emergency contact fields
        $emergencyContactName = trim($_POST['emergency_contact_name'] ?? '');
        $emergencyContactRelationship = trim($_POST['emergency_contact_relationship'] ?? '');
        $emergencyContactPhone = trim($_POST['emergency_contact_phone'] ?? '');
        $emergencyContactAddress = trim($_POST['emergency_contact_address'] ?? '');
        
        // Guardian/parent fields
        $guardianName = trim($_POST['guardian_name'] ?? '');
        $guardianRelationship = trim($_POST['guardian_relationship'] ?? '');
        $guardianPhone = trim($_POST['guardian_phone'] ?? '');
        $guardianAddress = trim($_POST['guardian_address'] ?? '');
        
        // Validation
        $errors = $this->validatePatientData($firstName, $lastName, $dateOfBirth, $gender, $email, $status);
        if (!empty($errors)) {
            $this->sendResponse(false, implode(', ', $errors));
            return;
        }
        
        // Validate emergency contact relationship if provided
        if (!empty($emergencyContactRelationship)) {
            $allowedEmergencyRelationships = ['parent', 'spouse', 'sibling', 'child', 'friend', 'other'];
            if (!in_array($emergencyContactRelationship, $allowedEmergencyRelationships)) {
                $this->sendResponse(false, 'Invalid emergency contact relationship');
                return;
            }
        }
        
        // Validate guardian relationship if provided
        if (!empty($guardianRelationship)) {
            $allowedGuardianRelationships = ['parent', 'guardian', 'grandparent', 'aunt_uncle', 'foster_parent', 'other'];
            if (!in_array($guardianRelationship, $allowedGuardianRelationships)) {
                $this->sendResponse(false, 'Invalid guardian relationship');
                return;
            }
        }
        
        // Check if email already exists (if provided)
        if (!empty($email) && $this->emailExists($email)) {
            $this->sendResponse(false, 'A patient with this email already exists');
            return;
        }
        
        $sql = "INSERT INTO patients (first_name, middle_name, last_name, date_of_birth, gender, 
                                     contact_number, email, address, medical_history, status,
                                     emergency_contact_name, emergency_contact_relationship, emergency_contact_phone, emergency_contact_address,
                                     guardian_name, guardian_relationship, guardian_phone, guardian_address,
                                     created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssssssssssssssss', 
            $firstName, $middleName, $lastName, $dateOfBirth, $gender,
            $contactNumber, $email, $address, $medicalHistory, $status,
            $emergencyContactName, $emergencyContactRelationship, $emergencyContactPhone, $emergencyContactAddress,
            $guardianName, $guardianRelationship, $guardianPhone, $guardianAddress
        );
        
        if ($stmt->execute()) {
            $patientId = $this->conn->insert_id;
            
            // Log the action
            $fullName = trim("$firstName $middleName $lastName");
            $this->logAction($_SESSION['user_id'] ?? null, "Created patient: {$fullName}");
            
            $this->sendResponse(true, 'Patient created successfully', [
                'patient_id' => $patientId
            ]);
        } else {
            $this->sendResponse(false, 'Failed to create patient');
        }
    }
    
    private function updatePatient() {
        $patientId = intval($_POST['patient_id'] ?? 0);
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $medicalHistory = trim($_POST['medical_history'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        
        // Emergency contact fields
        $emergencyContactName = trim($_POST['emergency_contact_name'] ?? '');
        $emergencyContactRelationship = trim($_POST['emergency_contact_relationship'] ?? '');
        $emergencyContactPhone = trim($_POST['emergency_contact_phone'] ?? '');
        $emergencyContactAddress = trim($_POST['emergency_contact_address'] ?? '');
        
        // Guardian/parent fields
        $guardianName = trim($_POST['guardian_name'] ?? '');
        $guardianRelationship = trim($_POST['guardian_relationship'] ?? '');
        $guardianPhone = trim($_POST['guardian_phone'] ?? '');
        $guardianAddress = trim($_POST['guardian_address'] ?? '');
        
        if ($patientId <= 0) {
            $this->sendResponse(false, 'Valid patient ID is required');
            return;
        }
        
        // Validation
        $errors = $this->validatePatientData($firstName, $lastName, $dateOfBirth, $gender, $email, $status);
        if (!empty($errors)) {
            $this->sendResponse(false, implode(', ', $errors));
            return;
        }
        
        // Validate emergency contact relationship if provided
        if (!empty($emergencyContactRelationship)) {
            $allowedEmergencyRelationships = ['parent', 'spouse', 'sibling', 'child', 'friend', 'other'];
            if (!in_array($emergencyContactRelationship, $allowedEmergencyRelationships)) {
                $this->sendResponse(false, 'Invalid emergency contact relationship');
                return;
            }
        }
        
        // Validate guardian relationship if provided
        if (!empty($guardianRelationship)) {
            $allowedGuardianRelationships = ['parent', 'guardian', 'grandparent', 'aunt_uncle', 'foster_parent', 'other'];
            if (!in_array($guardianRelationship, $allowedGuardianRelationships)) {
                $this->sendResponse(false, 'Invalid guardian relationship');
                return;
            }
        }
        
        // Check if patient exists
        if (!$this->patientExists($patientId)) {
            $this->sendResponse(false, 'Patient not found');
            return;
        }
        
        // Check if email already exists (excluding current patient)
        if (!empty($email) && $this->emailExists($email, $patientId)) {
            $this->sendResponse(false, 'A patient with this email already exists');
            return;
        }
        
        $sql = "UPDATE patients 
                SET first_name = ?, middle_name = ?, last_name = ?, date_of_birth = ?, gender = ?, 
                    contact_number = ?, email = ?, address = ?, medical_history = ?, status = ?,
                    emergency_contact_name = ?, emergency_contact_relationship = ?, emergency_contact_phone = ?, emergency_contact_address = ?,
                    guardian_name = ?, guardian_relationship = ?, guardian_phone = ?, guardian_address = ?,
                    updated_at = NOW() 
                WHERE patient_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssssssssssssssssi', 
            $firstName, $middleName, $lastName, $dateOfBirth, $gender,
            $contactNumber, $email, $address, $medicalHistory, $status,
            $emergencyContactName, $emergencyContactRelationship, $emergencyContactPhone, $emergencyContactAddress,
            $guardianName, $guardianRelationship, $guardianPhone, $guardianAddress,
            $patientId
        );
        
        if ($stmt->execute()) {
            // Log the action
            $fullName = trim("$firstName $middleName $lastName");
            $this->logAction($_SESSION['user_id'] ?? null, "Updated patient: {$fullName}");
            
            $this->sendResponse(true, 'Patient updated successfully');
        } else {
            $this->sendResponse(false, 'Failed to update patient');
        }
    }
    
    private function deletePatient() {
        $patientId = intval($_POST['patient_id'] ?? 0);
        
        if ($patientId <= 0) {
            $this->sendResponse(false, 'Valid patient ID is required');
            return;
        }
        
        // Check if patient exists
        if (!$this->patientExists($patientId)) {
            $this->sendResponse(false, 'Patient not found');
            return;
        }
        
        // Get patient name for logging
        $patientSql = "SELECT first_name, middle_name, last_name FROM patients WHERE patient_id = ?";
        $patientStmt = $this->conn->prepare($patientSql);
        $patientStmt->bind_param('i', $patientId);
        $patientStmt->execute();
        $patientResult = $patientStmt->get_result();
        $patientData = $patientResult->fetch_assoc();
        $patientName = trim(($patientData['first_name'] ?? '') . ' ' . 
                           ($patientData['middle_name'] ?? '') . ' ' . 
                           ($patientData['last_name'] ?? '')) ?: 'Unknown';
        
        // Check for related records that would prevent deletion
        $relatedChecks = [
            'appointments' => "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?",
            'medical_records' => "SELECT COUNT(*) as count FROM medical_records WHERE patient_id = ?",
            'billing' => "SELECT COUNT(*) as count FROM billing WHERE patient_id = ?",
            'prescriptions' => "SELECT COUNT(*) as count FROM prescriptions WHERE patient_id = ?",
            'patient_insurance' => "SELECT COUNT(*) as count FROM patient_insurance WHERE patient_id = ?"
        ];
        
        $relatedRecords = [];
        foreach ($relatedChecks as $table => $sql) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            if ($count > 0) {
                $relatedRecords[] = "$count " . str_replace('_', ' ', $table);
            }
        }
        
        if (!empty($relatedRecords)) {
            $this->sendResponse(false, 'Cannot delete patient. There are related records: ' . implode(', ', $relatedRecords));
            return;
        }
        
        $sql = "DELETE FROM patients WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $patientId);
        
        if ($stmt->execute()) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted patient: {$patientName}");
            
            $this->sendResponse(true, 'Patient deleted successfully');
        } else {
            $this->sendResponse(false, 'Failed to delete patient');
        }
    }
    
    private function validatePatientData($firstName, $lastName, $dateOfBirth, $gender, $email, $status) {
        $errors = [];
        
        if (empty($firstName)) {
            $errors[] = 'First name is required';
        } elseif (strlen($firstName) > 100) {
            $errors[] = 'First name cannot exceed 100 characters';
        }
        
        if (empty($lastName)) {
            $errors[] = 'Last name is required';
        } elseif (strlen($lastName) > 100) {
            $errors[] = 'Last name cannot exceed 100 characters';
        }
        
        if (empty($dateOfBirth)) {
            $errors[] = 'Date of birth is required';
        } elseif (!$this->isValidDate($dateOfBirth)) {
            $errors[] = 'Invalid date of birth';
        } elseif (strtotime($dateOfBirth) > time()) {
            $errors[] = 'Date of birth cannot be in the future';
        }
        
        if (empty($gender)) {
            $errors[] = 'Gender is required';
        } elseif (!in_array($gender, ['male', 'female', 'other'])) {
            $errors[] = 'Gender must be male, female, or other';
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (!in_array($status, ['active', 'inactive', 'deceased'])) {
            $errors[] = 'Status must be active, inactive, or deceased';
        }
        
        return $errors;
    }
    
    private function isValidDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    private function patientExists($patientId) {
        $sql = "SELECT COUNT(*) as count FROM patients WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM patients WHERE email = ?";
        $params = [$email];
        $types = 's';
        
        if ($excludeId !== null) {
            $sql .= " AND patient_id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function logAction($userId, $action) {
        try {
            $logSql = "INSERT INTO system_logs (user_id, action, log_timestamp) VALUES (?, ?, NOW())";
            $logStmt = $this->conn->prepare($logSql);
            $logStmt->bind_param('is', $userId, $action);
            $logStmt->execute();
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log("Failed to log action: " . $e->getMessage());
        }
    }
    
    private function sendResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response = array_merge($response, $data);
        }
        
        echo json_encode($response);
        exit;
    }
}

// Initialize and handle the request
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Clean any unwanted output
ob_clean();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    ob_end_clean();
    header('Content-Type: application/json');
    $controller = new PatientController($conn);
    $controller->handleRequest();
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    error_log("Patient Controller Fatal Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A fatal error occurred']);
}
?> 