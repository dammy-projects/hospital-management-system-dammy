<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

class DepartmentController {
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
                    $this->listDepartments();
                    break;
                case 'get':
                    $this->getDepartment();
                    break;
                case 'create':
                    $this->createDepartment();
                    break;
                case 'update':
                    $this->updateDepartment();
                    break;
                case 'delete':
                    $this->deleteDepartment();
                    break;
                default:
                    $this->sendResponse(false, 'Invalid action specified');
            }
        } catch (Exception $e) {
            error_log("Department Controller Error: " . $e->getMessage());
            $this->sendResponse(false, 'An error occurred: ' . $e->getMessage());
        }
    }
    
    private function listDepartments() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 6)));
        $offset = ($page - 1) * $limit;
        
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sort = trim($_GET['sort'] ?? 'department_name');
        
        // Validate sort field
        $allowedSorts = ['department_name', 'location', 'status', 'created_at', 'updated_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'department_name';
        }
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $whereConditions[] = "(department_name LIKE ? OR location LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
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
        $countSql = "SELECT COUNT(*) as total FROM departments {$whereClause}";
        $countStmt = $this->conn->prepare($countSql);
        
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRecords = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);
        
        // Get departments
        $sql = "SELECT department_id, department_name, location, status, created_at, updated_at 
                FROM departments 
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
        
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        
        $this->sendResponse(true, 'Departments retrieved successfully', [
            'departments' => $departments,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'records_per_page' => $limit
            ]
        ]);
    }
    
    private function getDepartment() {
        $departmentId = intval($_GET['id'] ?? 0);
        
        if ($departmentId <= 0) {
            $this->sendResponse(false, 'Valid department ID is required');
            return;
        }
        
        $sql = "SELECT department_id, department_name, location, status, created_at, updated_at 
                FROM departments 
                WHERE department_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($department = $result->fetch_assoc()) {
            $this->sendResponse(true, 'Department retrieved successfully', [
                'department' => $department
            ]);
        } else {
            $this->sendResponse(false, 'Department not found');
        }
    }
    
    private function createDepartment() {
        $departmentName = trim($_POST['department_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        
        // Validation
        $errors = $this->validateDepartmentData($departmentName, $location, $status);
        if (!empty($errors)) {
            $this->sendResponse(false, implode(', ', $errors));
            return;
        }
        
        // Check if department name already exists
        if ($this->departmentNameExists($departmentName)) {
            $this->sendResponse(false, 'A department with this name already exists');
            return;
        }
        
        $sql = "INSERT INTO departments (department_name, location, status, created_at, updated_at) 
                VALUES (?, ?, ?, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $departmentName, $location, $status);
        
        if ($stmt->execute()) {
            $departmentId = $this->conn->insert_id;
            
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Created department: {$departmentName}");
            
            $this->sendResponse(true, 'Department created successfully', [
                'department_id' => $departmentId
            ]);
        } else {
            $this->sendResponse(false, 'Failed to create department');
        }
    }
    
    private function updateDepartment() {
        $departmentId = intval($_POST['department_id'] ?? 0);
        $departmentName = trim($_POST['department_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $status = trim($_POST['status'] ?? 'active');
        
        if ($departmentId <= 0) {
            $this->sendResponse(false, 'Valid department ID is required');
            return;
        }
        
        // Validation
        $errors = $this->validateDepartmentData($departmentName, $location, $status);
        if (!empty($errors)) {
            $this->sendResponse(false, implode(', ', $errors));
            return;
        }
        
        // Check if department exists
        if (!$this->departmentExists($departmentId)) {
            $this->sendResponse(false, 'Department not found');
            return;
        }
        
        // Check if department name already exists (excluding current department)
        if ($this->departmentNameExists($departmentName, $departmentId)) {
            $this->sendResponse(false, 'A department with this name already exists');
            return;
        }
        
        $sql = "UPDATE departments 
                SET department_name = ?, location = ?, status = ?, updated_at = NOW() 
                WHERE department_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssi', $departmentName, $location, $status, $departmentId);
        
        if ($stmt->execute()) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Updated department: {$departmentName}");
            
            $this->sendResponse(true, 'Department updated successfully');
        } else {
            $this->sendResponse(false, 'Failed to update department');
        }
    }
    
    private function deleteDepartment() {
        $departmentId = intval($_POST['department_id'] ?? 0);
        
        if ($departmentId <= 0) {
            $this->sendResponse(false, 'Valid department ID is required');
            return;
        }
        
        // Check if department exists
        if (!$this->departmentExists($departmentId)) {
            $this->sendResponse(false, 'Department not found');
            return;
        }
        
        // Get department name for logging
        $deptSql = "SELECT department_name FROM departments WHERE department_id = ?";
        $deptStmt = $this->conn->prepare($deptSql);
        $deptStmt->bind_param('i', $departmentId);
        $deptStmt->execute();
        $deptResult = $deptStmt->get_result();
        $departmentName = $deptResult->fetch_assoc()['department_name'] ?? 'Unknown';
        
        // Check if department has associated doctors
        $doctorCheckSql = "SELECT COUNT(*) as doctor_count FROM doctors WHERE department_id = ?";
        $doctorStmt = $this->conn->prepare($doctorCheckSql);
        $doctorStmt->bind_param('i', $departmentId);
        $doctorStmt->execute();
        $doctorResult = $doctorStmt->get_result();
        $doctorCount = $doctorResult->fetch_assoc()['doctor_count'];
        
        if ($doctorCount > 0) {
            $this->sendResponse(false, 'Cannot delete department. There are still doctors assigned to this department.');
            return;
        }
        
        $sql = "DELETE FROM departments WHERE department_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $departmentId);
        
        if ($stmt->execute()) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted department: {$departmentName}");
            
            $this->sendResponse(true, 'Department deleted successfully');
        } else {
            $this->sendResponse(false, 'Failed to delete department');
        }
    }
    
    private function validateDepartmentData($departmentName, $location, $status) {
        $errors = [];
        
        if (empty($departmentName)) {
            $errors[] = 'Department name is required';
        } elseif (strlen($departmentName) > 100) {
            $errors[] = 'Department name cannot exceed 100 characters';
        }
        
        if (empty($location)) {
            $errors[] = 'Location is required';
        } elseif (strlen($location) > 100) {
            $errors[] = 'Location cannot exceed 100 characters';
        }
        
        if (!in_array($status, ['active', 'inactive'])) {
            $errors[] = 'Status must be either active or inactive';
        }
        
        return $errors;
    }
    
    private function departmentExists($departmentId) {
        $sql = "SELECT COUNT(*) as count FROM departments WHERE department_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['count'] > 0;
    }
    
    private function departmentNameExists($departmentName, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM departments WHERE department_name = ?";
        $params = [$departmentName];
        $types = 's';
        
        if ($excludeId !== null) {
            $sql .= " AND department_id != ?";
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
    $controller = new DepartmentController($conn);
    $controller->handleRequest();
} catch (Exception $e) {
    error_log("Department Controller Fatal Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A fatal error occurred']);
}
?> 