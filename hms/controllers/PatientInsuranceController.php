<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/PatientInsurance.php';

class PatientInsuranceController {
    private $patientInsurance;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->patientInsurance = new PatientInsurance($conn);
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
                $this->getPatientInsurances();
                break;
            case 'get':
                $this->getPatientInsurance();
                break;
            case 'patients':
                $this->getPatients();
                break;
            case 'providers':
                $this->getInsuranceProviders();
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
                $this->createPatientInsurance();
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
                $this->updatePatientInsurance();
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
                $this->deletePatientInsurance();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get patient insurance records with pagination and search
     */
    private function getPatientInsurances() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $provider_filter = $_GET['provider_filter'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        
        $insurances = $this->patientInsurance->getPatientInsurances($page, $limit, $search, $provider_filter, $status_filter);
        $total = $this->patientInsurance->getTotalPatientInsurances($search, $provider_filter, $status_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $insurances,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single patient insurance record
     */
    private function getPatientInsurance() {
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Insurance ID is required');
        }
        
        $insurance = $this->patientInsurance->getPatientInsuranceById($id);
        
        if (!$insurance) {
            throw new Exception('Insurance record not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $insurance
        ]);
    }
    
    /**
     * Get all patients for dropdown
     */
    private function getPatients() {
        $patients = $this->patientInsurance->getAllPatients();
        
        echo json_encode([
            'success' => true,
            'data' => $patients
        ]);
    }
    
    /**
     * Get all insurance providers for dropdown
     */
    private function getInsuranceProviders() {
        $providers = $this->patientInsurance->getAllInsuranceProviders();
        
        echo json_encode([
            'success' => true,
            'data' => $providers
        ]);
    }
    
    /**
     * Create new patient insurance record
     */
    private function createPatientInsurance() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['patient_id', 'insurance_provider_id', 'insurance_number', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Check if insurance number already exists for this patient
        if ($this->patientInsurance->insuranceNumberExists($input['insurance_number'], $input['patient_id'])) {
            throw new Exception('This insurance number already exists for the selected patient');
        }
        
        $result = $this->patientInsurance->createPatientInsurance($input);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Patient insurance record created successfully',
                'data' => ['id' => $result]
            ]);
        } else {
            throw new Exception('Failed to create patient insurance record');
        }
    }
    
    /**
     * Update patient insurance record
     */
    private function updatePatientInsurance() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['patient_insurance_id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Insurance ID is required');
        }
        
        // Validate required fields
        $required = ['patient_id', 'insurance_provider_id', 'insurance_number', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Check if insurance number already exists for this patient (excluding current record)
        if ($this->patientInsurance->insuranceNumberExists($input['insurance_number'], $input['patient_id'], $id)) {
            throw new Exception('This insurance number already exists for the selected patient');
        }
        
        $result = $this->patientInsurance->updatePatientInsurance($id, $input);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Patient insurance record updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update patient insurance record');
        }
    }
    
    /**
     * Delete patient insurance record
     */
    private function deletePatientInsurance() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Insurance ID is required');
        }
        
        $result = $this->patientInsurance->deletePatientInsurance($id);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Patient insurance record deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete patient insurance record');
        }
    }
}

// Handle the request
$controller = new PatientInsuranceController();
$controller->handleRequest();
?> 