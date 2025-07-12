<?php
// Suppress error output to prevent HTML before JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config/config.php';

// Start output buffering to catch any unwanted output
ob_start();

// Start session and check authentication
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

class AppointmentController {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
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
                $this->getAppointments();
                break;
            case 'get':
                $this->getAppointment();
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
                $this->createAppointment();
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
                $this->updateAppointment();
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
                $this->deleteAppointment();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get appointments with pagination and search
     */
    private function getAppointments() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 8);
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        $doctor_filter = $_GET['doctor_filter'] ?? '';
        $date_filter = $_GET['date_filter'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT a.*, 
                       CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                       CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                       d.specialty
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.patient_id
                LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(p.first_name, ' ', p.last_name) LIKE ? 
                         OR CONCAT(d.first_name, ' ', d.last_name) LIKE ? 
                         OR a.purpose LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND a.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add doctor filter
        if (!empty($doctor_filter)) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $doctor_filter;
            $types .= 'i';
        }
        
        // Add date filter
        if (!empty($date_filter)) {
            switch ($date_filter) {
                case 'today':
                    $sql .= " AND DATE(a.appointment_date) = CURDATE()";
                    break;
                case 'tomorrow':
                    $sql .= " AND DATE(a.appointment_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
                    break;
                case 'this_week':
                    $sql .= " AND YEARWEEK(a.appointment_date) = YEARWEEK(CURDATE())";
                    break;
                case 'next_week':
                    $sql .= " AND YEARWEEK(a.appointment_date) = YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 1 WEEK))";
                    break;
                case 'this_month':
                    $sql .= " AND MONTH(a.appointment_date) = MONTH(CURDATE()) AND YEAR(a.appointment_date) = YEAR(CURDATE())";
                    break;
            }
        }
        
        // Get total count for pagination
        $countSql = str_replace('SELECT a.*, CONCAT(p.first_name, \' \', p.last_name) as patient_name, CONCAT(d.first_name, \' \', d.last_name) as doctor_name, d.specialty', 'SELECT COUNT(*)', $sql);
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($params)) {
            $countStmt->bind_param($types, ...$params);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['COUNT(*)'];
        
        // Get paginated results - show recent appointments first (by created date, then appointment date)
        $sql .= " ORDER BY a.created_at DESC, a.appointment_date DESC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $appointments,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single appointment
     */
    private function getAppointment() {
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Appointment ID is required');
        }
        
        $sql = "SELECT a.*, 
                       CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                       CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                       d.specialty
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.patient_id
                LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                WHERE a.appointment_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $appointment = $result->fetch_assoc();
        
        if (!$appointment) {
            throw new Exception('Appointment not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $appointment
        ]);
    }
    
    /**
     * Create new appointment
     */
    private function createAppointment() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['patient_id', 'doctor_id', 'appointment_date', 'purpose', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Validate appointment date is in the future (for scheduled appointments)
        if ($input['status'] === 'scheduled') {
            $appointmentDate = new DateTime($input['appointment_date']);
            $now = new DateTime();
            
            if ($appointmentDate <= $now) {
                throw new Exception('Appointment date must be in the future for scheduled appointments');
            }
        }
        
        // Enhanced double booking prevention - check for overlapping appointments (1 hour slots)
        $appointmentDateTime = new DateTime($input['appointment_date']);
        $startTime = $appointmentDateTime->format('Y-m-d H:i:s');
        $endTime = $appointmentDateTime->modify('+1 hour')->format('Y-m-d H:i:s');
        
        $conflictSql = "SELECT appointment_id, appointment_date,
                               CONCAT(p.first_name, ' ', p.last_name) as patient_name
                        FROM appointments a
                        LEFT JOIN patients p ON a.patient_id = p.patient_id
                        WHERE a.doctor_id = ? 
                        AND a.status = 'scheduled'
                        AND (
                            (a.appointment_date >= ? AND a.appointment_date < ?) OR
                            (DATE_ADD(a.appointment_date, INTERVAL 1 HOUR) > ? AND a.appointment_date <= ?)
                        )";
        
        $conflictStmt = $this->conn->prepare($conflictSql);
        $conflictStmt->bind_param('issss', $input['doctor_id'], $startTime, $endTime, $startTime, $startTime);
        $conflictStmt->execute();
        $conflictResult = $conflictStmt->get_result();
        
        if ($conflictResult->num_rows > 0) {
            $conflict = $conflictResult->fetch_assoc();
            $conflictTime = date('g:i A', strtotime($conflict['appointment_date']));
            throw new Exception("Double booking detected! Doctor already has an appointment with {$conflict['patient_name']} at {$conflictTime}");
        }
        
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, purpose, status) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisss', 
            $input['patient_id'], 
            $input['doctor_id'], 
            $input['appointment_date'], 
            $input['purpose'], 
            $input['status']
        );
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Appointment scheduled successfully',
                'data' => ['id' => $this->conn->insert_id]
            ]);
        } else {
            throw new Exception('Failed to schedule appointment');
        }
    }
    
    /**
     * Update appointment
     */
    private function updateAppointment() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Appointment ID is required');
        }
        
        // Validate required fields
        $required = ['patient_id', 'doctor_id', 'appointment_date', 'purpose', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }
        
        // Enhanced double booking prevention for updates - check for overlapping appointments (1 hour slots)
        $appointmentDateTime = new DateTime($input['appointment_date']);
        $startTime = $appointmentDateTime->format('Y-m-d H:i:s');
        $endTime = $appointmentDateTime->modify('+1 hour')->format('Y-m-d H:i:s');
        
        $conflictSql = "SELECT a.appointment_id, a.appointment_date,
                               CONCAT(p.first_name, ' ', p.last_name) as patient_name
                        FROM appointments a
                        LEFT JOIN patients p ON a.patient_id = p.patient_id
                        WHERE a.doctor_id = ? 
                        AND a.status = 'scheduled'
                        AND a.appointment_id != ?
                        AND (
                            (a.appointment_date >= ? AND a.appointment_date < ?) OR
                            (DATE_ADD(a.appointment_date, INTERVAL 1 HOUR) > ? AND a.appointment_date <= ?)
                        )";
        
        $conflictStmt = $this->conn->prepare($conflictSql);
        $conflictStmt->bind_param('iissss', $input['doctor_id'], $id, $startTime, $endTime, $startTime, $startTime);
        $conflictStmt->execute();
        $conflictResult = $conflictStmt->get_result();
        
        if ($conflictResult->num_rows > 0) {
            $conflict = $conflictResult->fetch_assoc();
            $conflictTime = date('g:i A', strtotime($conflict['appointment_date']));
            throw new Exception("Double booking detected! Doctor already has an appointment with {$conflict['patient_name']} at {$conflictTime}");
        }
        
        $sql = "UPDATE appointments 
                SET patient_id = ?, doctor_id = ?, appointment_date = ?, purpose = ?, status = ?
                WHERE appointment_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisssi', 
            $input['patient_id'], 
            $input['doctor_id'], 
            $input['appointment_date'], 
            $input['purpose'], 
            $input['status'], 
            $id
        );
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Appointment updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update appointment');
        }
    }
    
    /**
     * Delete appointment
     */
    private function deleteAppointment() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('Appointment ID is required');
        }
        
        $sql = "DELETE FROM appointments WHERE appointment_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete appointment');
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
    $controller = new AppointmentController();
    $controller->handleRequest();
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    error_log("Appointment Controller Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A fatal error occurred']);
}
?> 