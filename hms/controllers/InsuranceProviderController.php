<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

class InsuranceProviderController {
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
                    $this->listProviders();
                    break;
                case 'get':
                    $this->getProvider();
                    break;
                case 'create':
                    $this->createProvider();
                    break;
                case 'update':
                    $this->updateProvider();
                    break;
                case 'delete':
                    $this->deleteProvider();
                    break;
                default:
                    $this->sendResponse(false, 'Invalid action specified');
            }
        } catch (Exception $e) {
            error_log("Insurance Provider Controller Error: " . $e->getMessage());
            $this->sendResponse(false, 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function listProviders() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 6)));
        $offset = ($page - 1) * $limit;
        
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sort = trim($_GET['sort'] ?? 'provider_name');
        
        // Validate sort field
        $allowedSorts = ['provider_name', 'contact_number', 'status'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'provider_name';
        }
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $whereConditions[] = "(provider_name LIKE ? OR contact_number LIKE ? OR address LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
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
        $countSql = "SELECT COUNT(*) as total FROM insurance_providers {$whereClause}";
        $countStmt = $this->conn->prepare($countSql);
        
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRecords = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);
        
        // Get providers
        $sql = "SELECT insurance_provider_id, provider_name, contact_number, address, status 
                FROM insurance_providers 
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
        
        $providers = [];
        while ($row = $result->fetch_assoc()) {
            $providers[] = $row;
        }
        
        $this->sendResponse(true, 'Insurance providers retrieved successfully', [
            'providers' => $providers,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'records_per_page' => $limit
            ]
        ]);
    }
    
    private function getProvider() {
        $providerId = intval($_GET['id'] ?? 0);
        
        if ($providerId <= 0) {
            $this->sendResponse(false, 'Valid provider ID is required');
            return;
        }
        
        $sql = "SELECT insurance_provider_id, provider_name, contact_number, address, status 
                FROM insurance_providers 
                WHERE insurance_provider_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($provider = $result->fetch_assoc()) {
            $this->sendResponse(true, 'Provider retrieved successfully', [
                'provider' => $provider
            ]);
        } else {
            $this->sendResponse(false, 'Provider not found');
        }
    }
    
    private function createProvider() {
        $providerName = trim($_POST['provider_name'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        
        // Validation
        $errors = $this->validateProviderData($providerName, $contactNumber, $address, $status);
        if (!empty($errors)) {
            $this->sendResponse(false, implode(', ', $errors));
            return;
        }
        
        // Check if provider name already exists
        if ($this->providerNameExists($providerName)) {
            $this->sendResponse(false, 'A provider with this name already exists');
            return;
        }
        
        $sql = "INSERT INTO insurance_providers (provider_name, contact_number, address, status) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssss', $providerName, $contactNumber, $address, $status);
        
        if ($stmt->execute()) {
            $providerId = $this->conn->insert_id;
            
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Created insurance provider: {$providerName}");
            
            $this->sendResponse(true, 'Insurance provider created successfully', [
                'insurance_provider_id' => $providerId
            ]);
        } else {
            $this->sendResponse(false, 'Failed to create insurance provider');
        }
    }
    
    private function updateProvider() {
        $providerId = intval($_POST['insurance_provider_id'] ?? 0);
        $providerName = trim($_POST['provider_name'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        
        if ($providerId <= 0) {
            $this->sendResponse(false, 'Valid provider ID is required');
            return;
        }
        
        // Validation
        $errors = $this->validateProviderData($providerName, $contactNumber, $address, $status);
        if (!empty($errors)) {
            $this->sendResponse(false, implode(', ', $errors));
            return;
        }
        
        // Check if provider exists
        if (!$this->providerExists($providerId)) {
            $this->sendResponse(false, 'Provider not found');
            return;
        }
        
        // Check if provider name already exists (excluding current provider)
        if ($this->providerNameExists($providerName, $providerId)) {
            $this->sendResponse(false, 'A provider with this name already exists');
            return;
        }
        
        $sql = "UPDATE insurance_providers 
                SET provider_name = ?, contact_number = ?, address = ?, status = ? 
                WHERE insurance_provider_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssi', $providerName, $contactNumber, $address, $status, $providerId);
        
        if ($stmt->execute()) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Updated insurance provider: {$providerName}");
            
            $this->sendResponse(true, 'Insurance provider updated successfully');
        } else {
            $this->sendResponse(false, 'Failed to update insurance provider');
        }
    }
    
    private function deleteProvider() {
        $providerId = intval($_POST['insurance_provider_id'] ?? 0);
        
        if ($providerId <= 0) {
            $this->sendResponse(false, 'Valid provider ID is required');
            return;
        }
        
        // Check if provider exists
        if (!$this->providerExists($providerId)) {
            $this->sendResponse(false, 'Provider not found');
            return;
        }
        
        // Get provider name for logging
        $providerSql = "SELECT provider_name FROM insurance_providers WHERE insurance_provider_id = ?";
        $providerStmt = $this->conn->prepare($providerSql);
        $providerStmt->bind_param('i', $providerId);
        $providerStmt->execute();
        $providerResult = $providerStmt->get_result();
        $providerName = $providerResult->fetch_assoc()['provider_name'] ?? 'Unknown';
        
        // Check if provider has associated patient insurance records
        $insuranceCheckSql = "SELECT COUNT(*) as insurance_count FROM patient_insurance WHERE insurance_provider_id = ?";
        $insuranceStmt = $this->conn->prepare($insuranceCheckSql);
        $insuranceStmt->bind_param('i', $providerId);
        $insuranceStmt->execute();
        $insuranceResult = $insuranceStmt->get_result();
        $insuranceCount = $insuranceResult->fetch_assoc()['insurance_count'];
        
        if ($insuranceCount > 0) {
            $this->sendResponse(false, 'Cannot delete provider. There are still patients with insurance from this provider.');
            return;
        }
        
        $sql = "DELETE FROM insurance_providers WHERE insurance_provider_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $providerId);
        
        if ($stmt->execute()) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted insurance provider: {$providerName}");
            
            $this->sendResponse(true, 'Insurance provider deleted successfully');
        } else {
            $this->sendResponse(false, 'Failed to delete insurance provider');
        }
    }
    
    private function validateProviderData($providerName, $contactNumber, $address, $status) {
        $errors = [];
        
        if (empty($providerName)) {
            $errors[] = 'Provider name is required';
        } elseif (strlen($providerName) > 100) {
            $errors[] = 'Provider name cannot exceed 100 characters';
        }
        
        if (empty($contactNumber)) {
            $errors[] = 'Contact number is required';
        } elseif (strlen($contactNumber) > 20) {
            $errors[] = 'Contact number cannot exceed 20 characters';
        }
        
        if (empty($address)) {
            $errors[] = 'Address is required';
        }
        
        if (!in_array($status, ['active', 'inactive'])) {
            $errors[] = 'Status must be either active or inactive';
        }
        
        return $errors;
    }
    
    private function providerExists($providerId) {
        $sql = "SELECT COUNT(*) as count FROM insurance_providers WHERE insurance_provider_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function providerNameExists($providerName, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM insurance_providers WHERE provider_name = ?";
        $params = [$providerName];
        $types = 's';
        
        if ($excludeId !== null) {
            $sql .= " AND insurance_provider_id != ?";
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
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $controller = new InsuranceProviderController($conn);
    $controller->handleRequest();
} catch (Exception $e) {
    error_log("Insurance Provider Controller Fatal Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A fatal error occurred']);
}
?> 