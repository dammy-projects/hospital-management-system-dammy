<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/config.php';
require_once '../models/Billing.php';

class BillingController {
    private $conn;
    private $billing;

    public function __construct($connection) {
        $this->conn = $connection;
        $this->billing = new Billing($this->conn);
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        
        // Extract action from URL path if not in query parameters
        if (empty($action)) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path_parts = explode('/', trim($path, '/'));
            
            // Look for action in URL path
            if (count($path_parts) >= 2) {
                $action = end($path_parts);
            }
        }

        try {
            switch($method) {
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
                    if (empty($action)) {
                        // Default GET action - list bills
                        $this->getAllBills();
                    } else {
                        $this->sendResponse(405, ['error' => 'Method not allowed']);
                    }
                    break;
            }
        } catch (Exception $e) {
            error_log("BillingController Error: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Internal server error: ' . $e->getMessage()]);
        }
    }

    private function handleGet($action) {
        switch($action) {
            case 'list':
            case 'bills':
            case '':
                $this->getAllBills();
                break;
            case 'get':
            case 'bill':
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $this->getBill($id);
                } else {
                    $this->sendResponse(400, ['error' => 'Bill ID required']);
                }
                break;
            case 'patients':
                $this->getPatients();
                break;
            case 'appointments':
                $this->getAppointments();
                break;
            case 'patient-appointments':
                $patient_id = $_GET['patient_id'] ?? null;
                if ($patient_id) {
                    $this->getPatientAppointments($patient_id);
                } else {
                    $this->sendResponse(400, ['error' => 'Patient ID required']);
                }
                break;
            case 'stats':
                $this->getBillingStats();
                break;
            default:
                // Check if it's a numeric ID in URL path
                if (is_numeric($action)) {
                    $this->getBill($action);
                } else {
                    // Default action - list bills
                    $this->getAllBills();
                }
                break;
        }
    }

    private function handlePost($action) {
        switch($action) {
            case 'create':
            case 'bills':
            case '':
                $this->createBill();
                break;
            default:
                $this->sendResponse(404, ['error' => 'Endpoint not found']);
                break;
        }
    }

    private function handlePut($action) {
        $id = $_GET['id'] ?? null;
        
        // Check if ID is in URL path
        if (!$id && is_numeric($action)) {
            $id = $action;
        }
        
        if ($id) {
            $this->updateBill($id);
        } else {
            $this->sendResponse(400, ['error' => 'Bill ID required']);
        }
    }

    private function handleDelete($action) {
        $id = $_GET['id'] ?? null;
        
        // Check if ID is in URL path
        if (!$id && is_numeric($action)) {
            $id = $action;
        }
        
        if ($id) {
            $this->deleteBill($id);
        } else {
            $this->sendResponse(400, ['error' => 'Bill ID required']);
        }
    }

    private function getAllBills() {
        try {
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 6;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $payment_status = isset($_GET['payment_status']) ? trim($_GET['payment_status']) : '';
            $insurance_status = isset($_GET['insurance_status']) ? trim($_GET['insurance_status']) : '';

            // Validate status filters
            if (!empty($payment_status) && !$this->billing->isValidPaymentStatus($payment_status)) {
                $this->sendResponse(400, ['error' => 'Invalid payment status']);
                return;
            }

            if (!empty($insurance_status) && !$this->billing->isValidInsuranceStatus($insurance_status)) {
                $this->sendResponse(400, ['error' => 'Invalid insurance status']);
                return;
            }

            $bills = $this->billing->read($page, $limit, $search, $payment_status, $insurance_status);
            $total = $this->billing->getTotalCount($search, $payment_status, $insurance_status);
            $total_pages = ceil($total / $limit);

            // Format bills data
            foreach ($bills as &$bill) {
                $bill['formatted_amount'] = $this->billing->formatAmount($bill['amount']);
                $bill['payment_status_class'] = $this->billing->getPaymentStatusClass($bill['payment_status']);
                $bill['insurance_status_class'] = $this->billing->getInsuranceStatusClass($bill['insurance_claim_status']);
                $bill['formatted_date'] = date('M j, Y g:i A', strtotime($bill['billing_date']));
                $bill['formatted_appointment_date'] = $bill['appointment_date'] ? date('M j, Y g:i A', strtotime($bill['appointment_date'])) : null;
            }

            $this->sendResponse(200, [
                'success' => true,
                'data' => $bills,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_records' => $total,
                    'per_page' => $limit,
                    'has_prev' => $page > 1,
                    'has_next' => $page < $total_pages
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error in getAllBills: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to fetch bills']);
        }
    }

    private function getBill($id) {
        try {
            if (!$this->billing->exists($id)) {
                $this->sendResponse(404, ['error' => 'Bill not found']);
                return;
            }

            $bill = $this->billing->readOne($id);
            
            if ($bill) {
                // Format the bill data
                $bill['formatted_amount'] = $this->billing->formatAmount($bill['amount']);
                $bill['payment_status_class'] = $this->billing->getPaymentStatusClass($bill['payment_status']);
                $bill['insurance_status_class'] = $this->billing->getInsuranceStatusClass($bill['insurance_claim_status']);
                $bill['formatted_date'] = date('M j, Y g:i A', strtotime($bill['billing_date']));
                $bill['formatted_appointment_date'] = $bill['appointment_date'] ? date('M j, Y g:i A', strtotime($bill['appointment_date'])) : null;

                $this->sendResponse(200, [
                    'success' => true,
                    'data' => $bill
                ]);
            } else {
                $this->sendResponse(404, ['error' => 'Bill not found']);
            }

        } catch (Exception $e) {
            error_log("Error in getBill: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to fetch bill']);
        }
    }

    private function createBill() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                $this->sendResponse(400, ['error' => 'Invalid JSON data']);
                return;
            }

            // Validate required fields
            $required_fields = ['patient_id', 'amount', 'payment_status', 'insurance_claim_status', 'billing_date'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                    $this->sendResponse(400, ['error' => "Field '$field' is required"]);
                    return;
                }
            }

            // Validate amount
            if (!is_numeric($data['amount']) || floatval($data['amount']) < 0) {
                $this->sendResponse(400, ['error' => 'Amount must be a positive number']);
                return;
            }

            // Validate payment status
            if (!$this->billing->isValidPaymentStatus($data['payment_status'])) {
                $this->sendResponse(400, ['error' => 'Invalid payment status']);
                return;
            }

            // Validate insurance status
            if (!$this->billing->isValidInsuranceStatus($data['insurance_claim_status'])) {
                $this->sendResponse(400, ['error' => 'Invalid insurance claim status']);
                return;
            }

            // Validate billing date format
            $billing_date = DateTime::createFromFormat('Y-m-d\TH:i', $data['billing_date']);
            if (!$billing_date) {
                $this->sendResponse(400, ['error' => 'Invalid billing date format']);
                return;
            }

            // Set billing properties
            $this->billing->patient_id = $data['patient_id'];
            $this->billing->appointment_id = !empty($data['appointment_id']) ? $data['appointment_id'] : null;
            $this->billing->amount = floatval($data['amount']);
            $this->billing->payment_status = $data['payment_status'];
            $this->billing->insurance_claim_status = $data['insurance_claim_status'];
            $this->billing->billing_date = $billing_date->format('Y-m-d H:i:s');

            if ($this->billing->create()) {
                $created_bill = $this->billing->readOne($this->billing->billing_id);
                
                // Format the response data
                $created_bill['formatted_amount'] = $this->billing->formatAmount($created_bill['amount']);
                $created_bill['payment_status_class'] = $this->billing->getPaymentStatusClass($created_bill['payment_status']);
                $created_bill['insurance_status_class'] = $this->billing->getInsuranceStatusClass($created_bill['insurance_claim_status']);
                $created_bill['formatted_date'] = date('M j, Y g:i A', strtotime($created_bill['billing_date']));

                $this->sendResponse(201, [
                    'success' => true,
                    'message' => 'Bill created successfully',
                    'data' => $created_bill
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Failed to create bill']);
            }

        } catch (Exception $e) {
            error_log("Error in createBill: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to create bill']);
        }
    }

    private function updateBill($id) {
        try {
            if (!$this->billing->exists($id)) {
                $this->sendResponse(404, ['error' => 'Bill not found']);
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                $this->sendResponse(400, ['error' => 'Invalid JSON data']);
                return;
            }

            // Validate required fields
            $required_fields = ['patient_id', 'amount', 'payment_status', 'insurance_claim_status', 'billing_date'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                    $this->sendResponse(400, ['error' => "Field '$field' is required"]);
                    return;
                }
            }

            // Validate amount
            if (!is_numeric($data['amount']) || floatval($data['amount']) < 0) {
                $this->sendResponse(400, ['error' => 'Amount must be a positive number']);
                return;
            }

            // Validate payment status
            if (!$this->billing->isValidPaymentStatus($data['payment_status'])) {
                $this->sendResponse(400, ['error' => 'Invalid payment status']);
                return;
            }

            // Validate insurance status
            if (!$this->billing->isValidInsuranceStatus($data['insurance_claim_status'])) {
                $this->sendResponse(400, ['error' => 'Invalid insurance claim status']);
                return;
            }

            // Validate billing date format
            $billing_date = DateTime::createFromFormat('Y-m-d\TH:i', $data['billing_date']);
            if (!$billing_date) {
                $this->sendResponse(400, ['error' => 'Invalid billing date format']);
                return;
            }

            // Set billing properties
            $this->billing->billing_id = $id;
            $this->billing->patient_id = $data['patient_id'];
            $this->billing->appointment_id = !empty($data['appointment_id']) ? $data['appointment_id'] : null;
            $this->billing->amount = floatval($data['amount']);
            $this->billing->payment_status = $data['payment_status'];
            $this->billing->insurance_claim_status = $data['insurance_claim_status'];
            $this->billing->billing_date = $billing_date->format('Y-m-d H:i:s');

            if ($this->billing->update()) {
                $updated_bill = $this->billing->readOne($id);
                
                // Format the response data
                $updated_bill['formatted_amount'] = $this->billing->formatAmount($updated_bill['amount']);
                $updated_bill['payment_status_class'] = $this->billing->getPaymentStatusClass($updated_bill['payment_status']);
                $updated_bill['insurance_status_class'] = $this->billing->getInsuranceStatusClass($updated_bill['insurance_claim_status']);
                $updated_bill['formatted_date'] = date('M j, Y g:i A', strtotime($updated_bill['billing_date']));

                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Bill updated successfully',
                    'data' => $updated_bill
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Failed to update bill']);
            }

        } catch (Exception $e) {
            error_log("Error in updateBill: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to update bill']);
        }
    }

    private function deleteBill($id) {
        try {
            if (!$this->billing->exists($id)) {
                $this->sendResponse(404, ['error' => 'Bill not found']);
                return;
            }

            $this->billing->billing_id = $id;

            if ($this->billing->delete()) {
                $this->sendResponse(200, [
                    'success' => true,
                    'message' => 'Bill deleted successfully'
                ]);
            } else {
                $this->sendResponse(500, ['error' => 'Failed to delete bill']);
            }

        } catch (Exception $e) {
            error_log("Error in deleteBill: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to delete bill']);
        }
    }

    private function getPatients() {
        try {
            $patients = $this->billing->getAllPatients();
            $this->sendResponse(200, [
                'success' => true,
                'data' => $patients
            ]);

        } catch (Exception $e) {
            error_log("Error in getPatients: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to fetch patients']);
        }
    }

    private function getAppointments() {
        try {
            $appointments = $this->billing->getAllAppointments();
            
            // Format appointment dates
            foreach ($appointments as &$appointment) {
                $appointment['formatted_date'] = date('M j, Y g:i A', strtotime($appointment['appointment_date']));
            }

            $this->sendResponse(200, [
                'success' => true,
                'data' => $appointments
            ]);

        } catch (Exception $e) {
            error_log("Error in getAppointments: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to fetch appointments']);
        }
    }

    private function getPatientAppointments($patient_id) {
        try {
            $appointments = $this->billing->getAppointmentsByPatient($patient_id);
            
            // Format appointment dates
            foreach ($appointments as &$appointment) {
                $appointment['formatted_date'] = date('M j, Y g:i A', strtotime($appointment['appointment_date']));
            }

            $this->sendResponse(200, [
                'success' => true,
                'data' => $appointments
            ]);

        } catch (Exception $e) {
            error_log("Error in getPatientAppointments: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to fetch patient appointments']);
        }
    }

    private function getBillingStats() {
        try {
            $stats = $this->billing->getBillingStats();
            
            // Format amounts
            $stats['formatted_total_paid'] = $this->billing->formatAmount($stats['total_paid'] ?: 0);
            $stats['formatted_total_pending'] = $this->billing->formatAmount($stats['total_pending'] ?: 0);
            $stats['formatted_total_cancelled'] = $this->billing->formatAmount($stats['total_cancelled'] ?: 0);

            $this->sendResponse(200, [
                'success' => true,
                'data' => $stats
            ]);

        } catch (Exception $e) {
            error_log("Error in getBillingStats: " . $e->getMessage());
            $this->sendResponse(500, ['error' => 'Failed to fetch billing statistics']);
        }
    }

    private function sendResponse($status_code, $data) {
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }
}

// Initialize and handle request
try {
    $controller = new BillingController($conn);
    $controller->handleRequest();
} catch (Exception $e) {
    error_log("Fatal error in BillingController: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?> 