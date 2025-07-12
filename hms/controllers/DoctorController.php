<?php
// Suppress error output to prevent HTML before JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Doctor.php';

// Start output buffering to catch any unwanted output
ob_start();

// Start session and check authentication
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

class DoctorController {
    private $doctor;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->doctor = new Doctor($conn);
    }
    
    /**
     * Handle API requests
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';
        
        header('Content-Type: application/json');
        
        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($action);
                    break;
                case 'POST':
                    $this->handlePost($action);
                    break;
                case 'PUT':
                    $this->handlePut($action);
                    break;
                case 'DELETE':
                    $this->handleDelete($action);
                    break;
                default:
                    throw new Exception('Method not allowed');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle GET requests
     */
    private function handleGet($action) {
        switch ($action) {
            case 'list':
                $this->getDoctors();
                break;
            case 'list_all':
                $this->getAllDoctors();
                break;
            case 'get':
                $this->getDoctor();
                break;
            case 'departments':
                $this->getDepartments();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Handle POST requests
     */
    private function handlePost($action) {
        switch ($action) {
            case 'create':
                $this->createDoctor();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Handle PUT requests
     */
    private function handlePut($action) {
        switch ($action) {
            case 'update':
                $this->updateDoctor();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Handle DELETE requests
     */
    private function handleDelete($action) {
        switch ($action) {
            case 'delete':
                $this->deleteDoctor();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get doctors with pagination and search
     */
    private function getDoctors() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $department_filter = $_GET['department_filter'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        
        $doctors = $this->doctor->getDoctors($page, $limit, $search, $department_filter, $status_filter);
        $total = $this->doctor->getTotalDoctors($search, $department_filter, $status_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $doctors,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get all doctors for dropdown
     */
    private function getAllDoctors() {
        $sql = "SELECT d.doctor_id, d.first_name, d.middle_name, d.last_name, d.specialty
                FROM doctors d 
                WHERE d.status = 'active' 
                ORDER BY d.first_name, d.last_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $doctors
        ]);
    }
    
    /**
     * Get single doctor
     */
    private function getDoctor() {
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Doctor ID is required');
        }
        
        $doctor = $this->doctor->getDoctorById($id);
        
        if (!$doctor) {
            throw new Exception('Doctor not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $doctor
        ]);
    }
    
    /**
     * Get all departments for dropdown
     */
    private function getDepartments() {
        $departments = $this->doctor->getAllDepartments();
        
        echo json_encode([
            'success' => true,
            'data' => $departments
        ]);
    }
    
    /**
     * Create new doctor
     */
    private function createDoctor() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['first_name', 'last_name', 'specialty', 'contact_number', 'email', 'department_id', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Check if email already exists
        if ($this->doctor->emailExists($input['email'])) {
            throw new Exception('Email address already exists');
        }
        
        // Check if contact number already exists
        if ($this->doctor->contactExists($input['contact_number'])) {
            throw new Exception('Contact number already exists');
        }
        
        $result = $this->doctor->createDoctor($input);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Doctor created successfully',
                'data' => ['id' => $result]
            ]);
        } else {
            throw new Exception('Failed to create doctor');
        }
    }
    
    /**
     * Update doctor
     */
    private function updateDoctor() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['doctor_id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Doctor ID is required');
        }
        
        // Validate required fields
        $required = ['first_name', 'last_name', 'specialty', 'contact_number', 'email', 'department_id', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Check if email already exists (excluding current doctor)
        if ($this->doctor->emailExists($input['email'], $id)) {
            throw new Exception('Email address already exists');
        }
        
        // Check if contact number already exists (excluding current doctor)
        if ($this->doctor->contactExists($input['contact_number'], $id)) {
            throw new Exception('Contact number already exists');
        }
        
        $result = $this->doctor->updateDoctor($id, $input);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Doctor updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update doctor');
        }
    }
    
    /**
     * Delete doctor
     */
    private function deleteDoctor() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Doctor ID is required');
        }
        
        $result = $this->doctor->deleteDoctor($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Doctor deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete doctor');
        }
    }
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

// Handle the request
try {
    ob_end_clean();
    header('Content-Type: application/json');
    $controller = new DoctorController();
    $controller->handleRequest();
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    error_log("Doctor Controller Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A fatal error occurred']);
}
?> 