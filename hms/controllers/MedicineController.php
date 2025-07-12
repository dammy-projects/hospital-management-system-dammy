<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Medicine.php';

class MedicineController {
    private $medicine;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->medicine = new Medicine($conn);
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
                $this->getMedicines();
                break;
            case 'get':
                $this->getMedicine();
                break;
            case 'dosage-forms':
                $this->getDosageForms();
                break;
            case 'stats':
                $this->getStats();
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
                $this->createMedicine();
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
                $this->updateMedicine();
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
                $this->deleteMedicine();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get medicines with pagination and search
     */
    private function getMedicines() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $dosage_form_filter = $_GET['dosage_form_filter'] ?? '';
        
        $medicines = $this->medicine->getMedicines($page, $limit, $search, $dosage_form_filter);
        $total = $this->medicine->getTotalMedicines($search, $dosage_form_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $medicines,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single medicine
     */
    private function getMedicine() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Medicine ID is required');
        }
        
        $medicine = $this->medicine->getMedicineById($id);
        if (!$medicine) {
            throw new Exception('Medicine not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $medicine
        ]);
    }
    
    /**
     * Create new medicine
     */
    private function createMedicine() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['medicine_name', 'dosage_form', 'strength'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . " is required");
            }
        }
        
        // Check if medicine name already exists
        if ($this->medicine->medicineNameExists($input['medicine_name'])) {
            throw new Exception('Medicine name already exists');
        }
        
        $medicineId = $this->medicine->createMedicine($input);
        if ($medicineId) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Created new medicine: {$input['medicine_name']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Medicine created successfully',
                'data' => ['medicine_id' => $medicineId]
            ]);
        } else {
            throw new Exception('Failed to create medicine');
        }
    }
    
    /**
     * Update medicine
     */
    private function updateMedicine() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Medicine ID is required');
        }
        
        // Validate required fields
        $required = ['medicine_name', 'dosage_form', 'strength'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . " is required");
            }
        }
        
        // Check if medicine exists
        $existingMedicine = $this->medicine->getMedicineById($id);
        if (!$existingMedicine) {
            throw new Exception('Medicine not found');
        }
        
        // Check if medicine name already exists (excluding current medicine)
        if ($this->medicine->medicineNameExists($input['medicine_name'], $id)) {
            throw new Exception('Medicine name already exists');
        }
        
        $success = $this->medicine->updateMedicine($id, $input);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Updated medicine: {$input['medicine_name']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Medicine updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update medicine');
        }
    }
    
    /**
     * Delete medicine
     */
    private function deleteMedicine() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Medicine ID is required');
        }
        
        // Get medicine info before deletion for logging
        $medicineInfo = $this->medicine->getMedicineById($id);
        if (!$medicineInfo) {
            throw new Exception('Medicine not found');
        }
        
        $success = $this->medicine->deleteMedicine($id);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted medicine: {$medicineInfo['medicine_name']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Medicine deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete medicine');
        }
    }
    
    /**
     * Get all dosage forms
     */
    private function getDosageForms() {
        $dosageForms = $this->medicine->getDosageForms();
        echo json_encode([
            'success' => true,
            'data' => $dosageForms
        ]);
    }
    
    /**
     * Get medicine statistics
     */
    private function getStats() {
        $stats = $this->medicine->getMedicineStats();
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Log system action
     */
    private function logAction($userId, $action) {
        if ($userId) {
            $sql = "INSERT INTO system_logs (user_id, action, log_timestamp) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('is', $userId, $action);
            $stmt->execute();
        }
    }
}

// Handle request if called directly
if ($_SERVER['REQUEST_METHOD']) {
    $controller = new MedicineController();
    $controller->handleRequest();
}
?> 