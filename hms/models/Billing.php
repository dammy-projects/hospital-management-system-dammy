<?php

class Billing {
    private $conn;
    private $table = 'billing';

    // Billing properties
    public $billing_id;
    public $patient_id;
    public $appointment_id;
    public $amount;
    public $payment_status;
    public $insurance_claim_status;
    public $billing_date;

    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all bills with pagination, search, and filters
    public function read($page = 1, $limit = 6, $search = '', $payment_status = '', $insurance_status = '') {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT b.*, 
                         CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name,
                         p.contact_number as patient_contact,
                         a.appointment_date,
                         a.purpose as appointment_purpose
                  FROM " . $this->table . " b
                  LEFT JOIN patients p ON b.patient_id = p.patient_id
                  LEFT JOIN appointments a ON b.appointment_id = a.appointment_id
                  WHERE 1=1";

        $params = [];
        $types = '';
        
        // Search functionality
        if (!empty($search)) {
            $query .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                        OR b.amount LIKE ?
                        OR b.billing_id LIKE ?)";
            $search_param = '%' . $search . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'sss';
        }

        // Payment status filter
        if (!empty($payment_status)) {
            $query .= " AND b.payment_status = ?";
            $params[] = $payment_status;
            $types .= 's';
        }

        // Insurance status filter
        if (!empty($insurance_status)) {
            $query .= " AND b.insurance_claim_status = ?";
            $params[] = $insurance_status;
            $types .= 's';
        }

        $query .= " ORDER BY b.billing_date DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            $bills = [];
            while ($row = $result->fetch_assoc()) {
                $bills[] = $row;
            }
            return $bills;
        }
        
        return [];
    }

    // Get total count of bills for pagination
    public function getTotalCount($search = '', $payment_status = '', $insurance_status = '') {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " b
                  LEFT JOIN patients p ON b.patient_id = p.patient_id
                  WHERE 1=1";

        $params = [];
        $types = '';

        // Search functionality
        if (!empty($search)) {
            $query .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                        OR b.amount LIKE ?
                        OR b.billing_id LIKE ?)";
            $search_param = '%' . $search . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'sss';
        }

        // Payment status filter
        if (!empty($payment_status)) {
            $query .= " AND b.payment_status = ?";
            $params[] = $payment_status;
            $types .= 's';
        }

        // Insurance status filter
        if (!empty($insurance_status)) {
            $query .= " AND b.insurance_claim_status = ?";
            $params[] = $insurance_status;
            $types .= 's';
        }

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        
        return 0;
    }

    // Get single bill by ID
    public function readOne($id) {
        $query = "SELECT b.*, 
                         CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name,
                         p.contact_number as patient_contact,
                         p.email as patient_email,
                         p.address as patient_address,
                         a.appointment_date,
                         a.purpose as appointment_purpose,
                         CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) as doctor_name,
                         d.specialty as doctor_specialty
                  FROM " . $this->table . " b
                  LEFT JOIN patients p ON b.patient_id = p.patient_id
                  LEFT JOIN appointments a ON b.appointment_id = a.appointment_id
                  LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                  WHERE b.billing_id = ?
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->bind_param('i', $id) && $stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        
        return false;
    }

    // Create new bill
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (patient_id, appointment_id, amount, payment_status, insurance_claim_status, billing_date) 
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->appointment_id = !empty($this->appointment_id) ? htmlspecialchars(strip_tags($this->appointment_id)) : null;
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
        $this->insurance_claim_status = htmlspecialchars(strip_tags($this->insurance_claim_status));
        $this->billing_date = htmlspecialchars(strip_tags($this->billing_date));

        if ($stmt && $stmt->bind_param('iidsss', 
            $this->patient_id,
            $this->appointment_id,
            $this->amount,
            $this->payment_status,
            $this->insurance_claim_status,
            $this->billing_date
        ) && $stmt->execute()) {
            $this->billing_id = $this->conn->insert_id;
            return true;
        }

        return false;
    }

    // Update bill
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET patient_id = ?, 
                      appointment_id = ?, 
                      amount = ?, 
                      payment_status = ?, 
                      insurance_claim_status = ?, 
                      billing_date = ?
                  WHERE billing_id = ?";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->appointment_id = !empty($this->appointment_id) ? htmlspecialchars(strip_tags($this->appointment_id)) : null;
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));
        $this->insurance_claim_status = htmlspecialchars(strip_tags($this->insurance_claim_status));
        $this->billing_date = htmlspecialchars(strip_tags($this->billing_date));
        $this->billing_id = htmlspecialchars(strip_tags($this->billing_id));

        if ($stmt && $stmt->bind_param('iidsssi', 
            $this->patient_id,
            $this->appointment_id,
            $this->amount,
            $this->payment_status,
            $this->insurance_claim_status,
            $this->billing_date,
            $this->billing_id
        ) && $stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete bill
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE billing_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->billing_id = htmlspecialchars(strip_tags($this->billing_id));

        if ($stmt && $stmt->bind_param('i', $this->billing_id) && $stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get all patients for dropdown
    public function getAllPatients() {
        $query = "SELECT patient_id, 
                         CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) as patient_name
                  FROM patients 
                  WHERE status = 'active'
                  ORDER BY first_name, last_name";

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            $patients = [];
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
            return $patients;
        }
        
        return [];
    }

    // Get all appointments for dropdown
    public function getAllAppointments() {
        $query = "SELECT a.appointment_id, 
                         a.appointment_date,
                         a.purpose,
                         CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name,
                         CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.patient_id
                  LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                  WHERE a.status IN ('scheduled', 'completed')
                  ORDER BY a.appointment_date DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            return $appointments;
        }
        
        return [];
    }

    // Get appointments by patient ID
    public function getAppointmentsByPatient($patient_id) {
        $query = "SELECT a.appointment_id, 
                         a.appointment_date,
                         a.purpose,
                         CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                  WHERE a.patient_id = ? AND a.status IN ('scheduled', 'completed')
                  ORDER BY a.appointment_date DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->bind_param('i', $patient_id) && $stmt->execute()) {
            $result = $stmt->get_result();
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            return $appointments;
        }
        
        return [];
    }

    // Get billing statistics
    public function getBillingStats() {
        $query = "SELECT 
                    COUNT(*) as total_bills,
                    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as total_pending,
                    SUM(CASE WHEN payment_status = 'cancelled' THEN amount ELSE 0 END) as total_cancelled,
                    COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_count,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN payment_status = 'cancelled' THEN 1 END) as cancelled_count,
                    COUNT(CASE WHEN insurance_claim_status = 'approved' THEN 1 END) as insurance_approved,
                    COUNT(CASE WHEN insurance_claim_status = 'pending' THEN 1 END) as insurance_pending,
                    COUNT(CASE WHEN insurance_claim_status = 'rejected' THEN 1 END) as insurance_rejected
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        
        return [];
    }

    // Check if bill exists
    public function exists($id) {
        $query = "SELECT billing_id FROM " . $this->table . " WHERE billing_id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->bind_param('i', $id) && $stmt->execute()) {
            $result = $stmt->get_result();
            return $result->num_rows > 0;
        }
        
        return false;
    }

    // Validate payment status
    public function isValidPaymentStatus($status) {
        return in_array($status, ['pending', 'paid', 'cancelled']);
    }

    // Validate insurance claim status
    public function isValidInsuranceStatus($status) {
        return in_array($status, ['pending', 'approved', 'rejected']);
    }

    // Get payment status badge class
    public function getPaymentStatusClass($status) {
        switch ($status) {
            case 'paid':
                return 'has-background-success has-text-white';
            case 'pending':
                return 'has-background-warning has-text-dark';
            case 'cancelled':
                return 'has-background-danger has-text-white';
            default:
                return 'has-background-grey-light';
        }
    }

    // Get insurance status badge class
    public function getInsuranceStatusClass($status) {
        switch ($status) {
            case 'approved':
                return 'has-background-success has-text-white';
            case 'pending':
                return 'has-background-info has-text-white';
            case 'rejected':
                return 'has-background-danger has-text-white';
            default:
                return 'has-background-grey-light';
        }
    }

    // Format amount for display
    public function formatAmount($amount) {
        return '₱' . number_format($amount, 2);
    }

    // Get bills by date range
    public function getBillsByDateRange($start_date, $end_date) {
        $query = "SELECT b.*, 
                         CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name
                  FROM " . $this->table . " b
                  LEFT JOIN patients p ON b.patient_id = p.patient_id
                  WHERE DATE(b.billing_date) BETWEEN ? AND ?
                  ORDER BY b.billing_date DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->bind_param('ss', $start_date, $end_date) && $stmt->execute()) {
            $result = $stmt->get_result();
            $bills = [];
            while ($row = $result->fetch_assoc()) {
                $bills[] = $row;
            }
            return $bills;
        }
        
        return [];
    }

    // Get recent bills
    public function getRecentBills($limit = 5) {
        $query = "SELECT b.*, 
                         CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name
                  FROM " . $this->table . " b
                  LEFT JOIN patients p ON b.patient_id = p.patient_id
                  ORDER BY b.billing_date DESC 
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        
        if ($stmt && $stmt->bind_param('i', $limit) && $stmt->execute()) {
            $result = $stmt->get_result();
            $bills = [];
            while ($row = $result->fetch_assoc()) {
                $bills[] = $row;
            }
            return $bills;
        }
        
        return [];
    }
} 