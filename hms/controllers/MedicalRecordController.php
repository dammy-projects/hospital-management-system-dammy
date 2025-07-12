<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

// Start session
session_start();

// Start output buffering to catch any unwanted output
ob_start();

class MedicalRecordController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        
        // Debug output
        error_log("MedicalRecordController: Action = $action, Method = $method");
        
        try {
            switch ($action) {
                case 'list':
                    $this->listMedicalRecords();
                    break;
                case 'list_all':
                    $this->listAllMedicalRecords();
                    break;
                case 'get':
                    $this->getMedicalRecord();
                    break;
                case 'create':
                    $this->createMedicalRecord();
                    break;
                case 'update':
                    $this->updateMedicalRecord();
                    break;
                case 'delete':
                    $this->deleteMedicalRecord();
                    break;
                case 'upload_lab_image':
                    $this->uploadLabImage();
                    break;
                default:
                    $this->sendResponse(false, 'Invalid action specified: ' . $action);
            }
        } catch (Exception $e) {
            error_log("Medical Record Controller Error: " . $e->getMessage());
            $this->sendResponse(false, 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function listMedicalRecords() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 6)));
        $offset = ($page - 1) * $limit;
        
        $search = trim($_GET['search'] ?? '');
        $patientId = trim($_GET['patient_id'] ?? '');
        $doctorId = trim($_GET['doctor_id'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sort = trim($_GET['sort'] ?? 'record_date');
        
        // Validate sort field
        $allowedSorts = ['record_date', 'created_at', 'patient_name', 'doctor_name'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'record_date';
        }
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $whereConditions[] = "(p.first_name LIKE ? OR p.middle_name LIKE ? OR p.last_name LIKE ? OR mr.diagnosis LIKE ? OR mr.treatment LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sssss';
        }
        
        if (!empty($patientId)) {
            $whereConditions[] = "mr.patient_id = ?";
            $params[] = $patientId;
            $types .= 'i';
        }
        
        if (!empty($doctorId)) {
            $whereConditions[] = "mr.doctor_id = ?";
            $params[] = $doctorId;
            $types .= 'i';
        }
        
        if (!empty($status)) {
            $whereConditions[] = "mr.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                     FROM medical_records mr 
                     LEFT JOIN patients p ON mr.patient_id = p.patient_id 
                     LEFT JOIN doctors d ON mr.doctor_id = d.doctor_id 
                     {$whereClause}";
        $countStmt = $this->conn->prepare($countSql);
        
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRecords = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);
        
        // Get medical records with patient and doctor information
        $sql = "SELECT mr.record_id, mr.patient_id, mr.doctor_id, mr.diagnosis, mr.treatment, 
                       mr.subjective, mr.objective, mr.assessment, mr.plan, mr.height_cm, mr.weight_kg, 
                       mr.bmi, mr.blood_pressure, mr.heart_rate, mr.temperature_c, mr.respiratory_rate, 
                       mr.lab_images, mr.created_at, mr.updated_at, mr.record_date, mr.status,
                       CONCAT(p.first_name, ' ', COALESCE(p.middle_name, ''), ' ', p.last_name) as patient_name,
                       CONCAT(d.first_name, ' ', COALESCE(d.middle_name, ''), ' ', d.last_name) as doctor_name
                FROM medical_records mr 
                LEFT JOIN patients p ON mr.patient_id = p.patient_id 
                LEFT JOIN doctors d ON mr.doctor_id = d.doctor_id 
                {$whereClause} 
                ORDER BY {$sort} DESC 
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
        
        $medicalRecords = [];
        while ($row = $result->fetch_assoc()) {
            $medicalRecords[] = $row;
        }
        
        $this->sendResponse(true, 'Medical records retrieved successfully', [
            'medical_records' => $medicalRecords,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'records_per_page' => $limit
            ]
        ]);
    }
    
    private function listAllMedicalRecords() {
        $sql = "SELECT mr.record_id, mr.patient_id, mr.doctor_id, mr.diagnosis, mr.treatment, 
                       mr.record_date, mr.status,
                       CONCAT(p.first_name, ' ', COALESCE(p.middle_name, ''), ' ', p.last_name) as patient_name,
                       CONCAT(d.first_name, ' ', COALESCE(d.middle_name, ''), ' ', d.last_name) as doctor_name
                FROM medical_records mr 
                LEFT JOIN patients p ON mr.patient_id = p.patient_id 
                LEFT JOIN doctors d ON mr.doctor_id = d.doctor_id 
                WHERE mr.status = 'active' 
                ORDER BY mr.record_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $medicalRecords = [];
        while ($row = $result->fetch_assoc()) {
            $medicalRecords[] = $row;
        }
        
        $this->sendResponse(true, 'All medical records retrieved successfully', [
            'data' => $medicalRecords
        ]);
    }
    
    private function getMedicalRecord() {
        $recordId = intval($_GET['id'] ?? 0);
        
        if ($recordId <= 0) {
            $this->sendResponse(false, 'Valid medical record ID is required');
            return;
        }
        
        $sql = "SELECT mr.record_id, mr.patient_id, mr.doctor_id, mr.diagnosis, mr.treatment, 
                       mr.subjective, mr.objective, mr.assessment, mr.plan, mr.height_cm, mr.weight_kg, 
                       mr.bmi, mr.blood_pressure, mr.heart_rate, mr.temperature_c, mr.respiratory_rate, 
                       mr.lab_images, mr.created_at, mr.updated_at, mr.record_date, mr.status,
                       CONCAT(p.first_name, ' ', COALESCE(p.middle_name, ''), ' ', p.last_name) as patient_name,
                       CONCAT(d.first_name, ' ', COALESCE(d.middle_name, ''), ' ', d.last_name) as doctor_name
                FROM medical_records mr 
                LEFT JOIN patients p ON mr.patient_id = p.patient_id 
                LEFT JOIN doctors d ON mr.doctor_id = d.doctor_id 
                WHERE mr.record_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $recordId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
            $this->sendResponse(true, 'Medical record retrieved successfully', $record);
        } else {
            $this->sendResponse(false, 'Medical record not found');
        }
    }
    
    private function createMedicalRecord() {
        // Debug logging
        error_log("MedicalRecordController: Creating medical record");
        error_log("POST data: " . print_r($_POST, true));
        
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
        
        // Validate required fields
        if (!$this->validateMedicalRecordData($patientId, $doctorId, $recordDate, $status)) {
            return;
        }
        
        // Check if patient exists
        if (!$this->patientExists($patientId)) {
            $this->sendResponse(false, 'Patient not found');
            return;
        }
        
        // Check if doctor exists
        if (!$this->doctorExists($doctorId)) {
            $this->sendResponse(false, 'Doctor not found');
            return;
        }
        
        $sql = "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, subjective, objective, 
                       assessment, plan, height_cm, weight_kg, bmi, blood_pressure, heart_rate, temperature_c, 
                       respiratory_rate, lab_images, record_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Create variables for null values to avoid reference errors
        $labImages = null;
        $heightCmVar = $heightCm;
        $weightKgVar = $weightKg;
        $bmiVar = $bmi;
        $heartRateVar = $heartRate;
        $temperatureCVar = $temperatureC;
        $respiratoryRateVar = $respiratoryRate;
        
        try {
            $stmt->bind_param('iissssssdddsidisss', 
                $patientId, $doctorId, $diagnosis, $treatment, $subjective, $objective, 
                $assessment, $plan, $heightCmVar, $weightKgVar, $bmiVar, $bloodPressure, $heartRateVar, 
                $temperatureCVar, $respiratoryRateVar, $labImages, $recordDate, $status);
            
            error_log("MedicalRecordController: bind_param successful");
        } catch (Exception $e) {
            error_log("MedicalRecordController: bind_param error: " . $e->getMessage());
            $this->sendResponse(false, 'Database error: ' . $e->getMessage());
            return;
        }
        
        error_log("MedicalRecordController: Executing SQL with params - patientId: $patientId, doctorId: $doctorId, bloodPressure: $bloodPressure");
        
        if ($stmt->execute()) {
            $recordId = $stmt->insert_id;
            error_log("MedicalRecordController: Successfully created record with ID: $recordId");
            $this->logAction($_SESSION['user_id'] ?? null, "Created medical record ID: {$recordId}");
            $this->sendResponse(true, 'Medical record created successfully', ['record_id' => $recordId]);
        } else {
            error_log("MedicalRecordController: SQL Error: " . $stmt->error);
            error_log("MedicalRecordController: SQL State: " . $stmt->sqlstate);
            error_log("MedicalRecordController: Error Code: " . $stmt->errno);
            $this->sendResponse(false, 'Failed to create medical record: ' . $stmt->error);
        }
    }
    
    private function updateMedicalRecord() {
        error_log('--- updateMedicalRecord called ---');
        
        // Get POST data
        $recordId = intval($_POST['record_id'] ?? 0);
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
        $labImages = $_POST['lab_images'] ?? null;
        
        error_log('updateMedicalRecord: POST data: ' . print_r($_POST, true));
        
        // Validate required fields
        if (!$this->validateMedicalRecordData($patientId, $doctorId, $recordDate, $status)) {
            error_log('updateMedicalRecord: Validation failed');
            return;
        }
        
        // Check if medical record exists
        if (!$this->medicalRecordExists($recordId)) {
            error_log('updateMedicalRecord: Medical record not found');
            $this->sendResponse(false, 'Medical record not found');
            return;
        }
        
        // Check if patient exists
        if (!$this->patientExists($patientId)) {
            error_log('updateMedicalRecord: Patient not found');
            $this->sendResponse(false, 'Patient not found');
            return;
        }
        
        // Check if doctor exists
        if (!$this->doctorExists($doctorId)) {
            error_log('updateMedicalRecord: Doctor not found');
            $this->sendResponse(false, 'Doctor not found');
            return;
        }
        
        error_log('updateMedicalRecord: All validations passed');
        
        // Use a simpler approach with direct SQL execution
        $sql = "UPDATE medical_records SET 
                patient_id = ?, 
                doctor_id = ?, 
                diagnosis = ?, 
                treatment = ?, 
                subjective = ?, 
                objective = ?, 
                assessment = ?, 
                plan = ?, 
                height_cm = ?, 
                weight_kg = ?, 
                bmi = ?, 
                blood_pressure = ?, 
                heart_rate = ?, 
                temperature_c = ?, 
                respiratory_rate = ?, 
                record_date = ?, 
                status = ?, 
                lab_images = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE record_id = ?";
        
        error_log('updateMedicalRecord: Preparing SQL statement');
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log('updateMedicalRecord: SQL prepare error: ' . $this->conn->error);
            $this->sendResponse(false, 'SQL prepare error: ' . $this->conn->error);
            return;
        }
        
        error_log('updateMedicalRecord: About to bind parameters');
        
        // Create variables for null values
        $heightCmVar = $heightCm;
        $weightKgVar = $weightKg;
        $bmiVar = $bmi;
        $heartRateVar = $heartRate;
        $temperatureCVar = $temperatureC;
        $respiratoryRateVar = $respiratoryRate;
        
        // Use a single bind_param call with correct type string
        $typeString = 'iissssssdddsidssssi';
        error_log('updateMedicalRecord: Type string: ' . $typeString . ' (length: ' . strlen($typeString) . ')');
        
        $bindResult = $stmt->bind_param($typeString, 
            $patientId, $doctorId, $diagnosis, $treatment, $subjective, $objective, 
            $assessment, $plan, $heightCmVar, $weightKgVar, $bmiVar, $bloodPressure, $heartRateVar, 
            $temperatureCVar, $respiratoryRateVar, $recordDate, $status, $labImages, $recordId);
        
        if (!$bindResult) {
            error_log('updateMedicalRecord: bind_param failed: ' . $stmt->error);
            $this->sendResponse(false, 'bind_param failed: ' . $stmt->error);
            return;
        }
        
        error_log('updateMedicalRecord: bind_param successful');
        error_log('updateMedicalRecord: Executing statement');
        
        if ($stmt->execute()) {
            error_log('updateMedicalRecord: Update successful');
            $this->logAction($_SESSION['user_id'] ?? null, "Updated medical record ID: {$recordId}");
            $this->sendResponse(true, 'Medical record updated successfully');
        } else {
            error_log('updateMedicalRecord: Execute error: ' . $stmt->error);
            $this->sendResponse(false, 'Failed to update medical record: ' . $stmt->error);
        }
    }
    
    private function deleteMedicalRecord() {
        $recordId = intval($_POST['record_id'] ?? 0);
        
        if ($recordId <= 0) {
            $this->sendResponse(false, 'Valid medical record ID is required');
            return;
        }
        
        // Check if medical record exists
        if (!$this->medicalRecordExists($recordId)) {
            $this->sendResponse(false, 'Medical record not found');
            return;
        }
        
        $sql = "DELETE FROM medical_records WHERE record_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $recordId);
        
        if ($stmt->execute()) {
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted medical record ID: {$recordId}");
            $this->sendResponse(true, 'Medical record deleted successfully');
        } else {
            $this->sendResponse(false, 'Failed to delete medical record: ' . $stmt->error);
        }
    }
    
    private function validateMedicalRecordData($patientId, $doctorId, $recordDate, $status) {
        if ($patientId <= 0) {
            $this->sendResponse(false, 'Valid patient ID is required');
            return false;
        }
        
        if ($doctorId <= 0) {
            $this->sendResponse(false, 'Valid doctor ID is required');
            return false;
        }
        
        if (empty($recordDate)) {
            $this->sendResponse(false, 'Record date is required');
            return false;
        }
        
        if (!$this->isValidDateTime($recordDate)) {
            $this->sendResponse(false, 'Invalid record date format');
            return false;
        }
        
        if (!in_array($status, ['active', 'archived'])) {
            $this->sendResponse(false, 'Invalid status. Must be active or archived');
            return false;
        }
        
        return true;
    }
    
    private function isValidDateTime($dateTime) {
        $d = DateTime::createFromFormat('Y-m-d\TH:i', $dateTime);
        return $d && $d->format('Y-m-d\TH:i') === $dateTime;
    }
    
    private function medicalRecordExists($recordId) {
        $sql = "SELECT COUNT(*) as count FROM medical_records WHERE record_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $recordId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function patientExists($patientId) {
        $sql = "SELECT COUNT(*) as count FROM patients WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function doctorExists($doctorId) {
        $sql = "SELECT COUNT(*) as count FROM doctors WHERE doctor_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function logAction($userId, $action) {
        if ($userId) {
            $sql = "INSERT INTO system_logs (user_id, action) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('is', $userId, $action);
            $stmt->execute();
        }
    }
    
    private function uploadLabImage() {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->sendResponse(false, 'No file uploaded or upload error occurred');
            return;
        }
        
        $file = $_FILES['file'];
        $recordId = intval($_POST['record_id'] ?? 0);
        
        if ($recordId <= 0) {
            $this->sendResponse(false, 'Valid record ID is required');
            return;
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->sendResponse(false, 'Invalid file type. Only JPG, PNG, GIF, and PDF files are allowed');
            return;
        }
        
        // Validate file size (5MB max)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            $this->sendResponse(false, 'File size too large. Maximum size is 5MB');
            return;
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/lab_results/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'lab_' . $recordId . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $this->sendResponse(true, 'File uploaded successfully', [
                'filename' => $filename,
                'original_name' => $file['name'],
                'file_path' => 'uploads/lab_results/' . $filename
            ]);
        } else {
            $this->sendResponse(false, 'Failed to save uploaded file');
        }
    }
    
    private function sendResponse($success, $message, $data = null) {
        // Clear any output buffer
        ob_clean();
        
        // Debug output
        error_log("MedicalRecordController sendResponse: success=$success, message=$message");
        
        header('Content-Type: application/json');
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $jsonResponse = json_encode($response);
        error_log("MedicalRecordController JSON response: " . $jsonResponse);
        
        echo $jsonResponse;
        exit;
    }
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $controller = new MedicalRecordController($conn);
        $controller->handleRequest();
    } catch (Exception $e) {
        error_log("MedicalRecordController Fatal Error: " . $e->getMessage());
        error_log("MedicalRecordController Stack Trace: " . $e->getTraceAsString());
        
        // Clear any output and send error response
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
}
?> 