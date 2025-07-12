<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Prescription.php';

class PrescriptionController {
    private $prescription;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->prescription = new Prescription($conn);
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
                $this->getPrescriptions();
                break;
            case 'get':
                $this->getPrescription();
                break;
            case 'patients':
                $this->getPatients();
                break;
            case 'doctors':
                $this->getDoctors();
                break;
            case 'medicines':
                $this->getMedicines();
                break;
            case 'stats':
                $this->getStats();
                break;
            case 'by-patient':
                $this->getPrescriptionsByPatient();
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
                $this->createPrescription();
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
                $this->updatePrescription();
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
                $this->deletePrescription();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get prescriptions with pagination and search
     */
    private function getPrescriptions() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        $patient_filter = $_GET['patient_filter'] ?? '';
        
        $prescriptions = $this->prescription->getPrescriptions($page, $limit, $search, $status_filter, $patient_filter);
        $total = $this->prescription->getTotalPrescriptions($search, $status_filter, $patient_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $prescriptions,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single prescription with items
     */
    private function getPrescription() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Prescription ID is required');
        }
        
        $prescription = $this->prescription->getPrescriptionById($id);
        if (!$prescription) {
            throw new Exception('Prescription not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $prescription
        ]);
    }
    
    /**
     * Create new prescription
     */
    private function createPrescription() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['patient_id', 'doctor_id', 'prescription_date', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . " is required");
            }
        }
        
        // Validate prescription items
        if (empty($input['items']) || !is_array($input['items'])) {
            throw new Exception('At least one medicine is required');
        }
        
        foreach ($input['items'] as $index => $item) {
            if (empty($item['medicine_id'])) {
                throw new Exception("Medicine is required for item " . ($index + 1));
            }
            if (empty($item['dosage'])) {
                throw new Exception("Dosage is required for item " . ($index + 1));
            }
            if (empty($item['frequency'])) {
                throw new Exception("Frequency is required for item " . ($index + 1));
            }
            if (empty($item['duration_days']) || !is_numeric($item['duration_days'])) {
                throw new Exception("Duration is required for item " . ($index + 1));
            }
            if (empty($item['quantity']) || !is_numeric($item['quantity'])) {
                throw new Exception("Quantity is required for item " . ($index + 1));
            }
        }
        
        $prescriptionId = $this->prescription->createPrescription($input);
        if ($prescriptionId) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Created new prescription for patient ID: {$input['patient_id']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Prescription created successfully',
                'data' => ['prescription_id' => $prescriptionId]
            ]);
        } else {
            throw new Exception('Failed to create prescription');
        }
    }
    
    /**
     * Update prescription
     */
    private function updatePrescription() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Prescription ID is required');
        }
        
        // Validate required fields
        $required = ['patient_id', 'doctor_id', 'prescription_date', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . " is required");
            }
        }
        
        // Check if prescription exists
        $existingPrescription = $this->prescription->getPrescriptionById($id);
        if (!$existingPrescription) {
            throw new Exception('Prescription not found');
        }
        
        // Validate prescription items
        if (empty($input['items']) || !is_array($input['items'])) {
            throw new Exception('At least one medicine is required');
        }
        
        foreach ($input['items'] as $index => $item) {
            if (empty($item['medicine_id'])) {
                throw new Exception("Medicine is required for item " . ($index + 1));
            }
            if (empty($item['dosage'])) {
                throw new Exception("Dosage is required for item " . ($index + 1));
            }
            if (empty($item['frequency'])) {
                throw new Exception("Frequency is required for item " . ($index + 1));
            }
            if (empty($item['duration_days']) || !is_numeric($item['duration_days'])) {
                throw new Exception("Duration is required for item " . ($index + 1));
            }
            if (empty($item['quantity']) || !is_numeric($item['quantity'])) {
                throw new Exception("Quantity is required for item " . ($index + 1));
            }
        }
        
        $success = $this->prescription->updatePrescription($id, $input);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Updated prescription ID: {$id}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Prescription updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update prescription');
        }
    }
    
    /**
     * Delete prescription
     */
    private function deletePrescription() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Prescription ID is required');
        }
        
        // Get prescription info before deletion for logging
        $prescriptionInfo = $this->prescription->getPrescriptionById($id);
        if (!$prescriptionInfo) {
            throw new Exception('Prescription not found');
        }
        
        $success = $this->prescription->deletePrescription($id);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted prescription ID: {$id} for patient: {$prescriptionInfo['patient_name']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Prescription deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete prescription');
        }
    }
    
    /**
     * Get all patients
     */
    private function getPatients() {
        $patients = $this->prescription->getPatients();
        echo json_encode([
            'success' => true,
            'data' => $patients
        ]);
    }
    
    /**
     * Get all doctors
     */
    private function getDoctors() {
        $doctors = $this->prescription->getDoctors();
        echo json_encode([
            'success' => true,
            'data' => $doctors
        ]);
    }
    
    /**
     * Get all medicines
     */
    private function getMedicines() {
        $medicines = $this->prescription->getMedicines();
        echo json_encode([
            'success' => true,
            'data' => $medicines
        ]);
    }
    
    /**
     * Get prescription statistics
     */
    private function getStats() {
        $stats = $this->prescription->getPrescriptionStats();
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Get prescriptions by patient
     */
    private function getPrescriptionsByPatient() {
        $patientId = intval($_GET['patient_id']);
        if (!$patientId) {
            throw new Exception('Patient ID is required');
        }
        
        $prescriptions = $this->prescription->getPrescriptionsByPatient($patientId);
        echo json_encode([
            'success' => true,
            'data' => $prescriptions
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
    $controller = new PrescriptionController();
    $controller->handleRequest();
}
?> 