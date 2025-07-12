<?php
class Doctor {
    private $conn;
    private $table = 'doctors';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all doctors with pagination and search
     */
    public function getDoctors($page = 1, $limit = 6, $search = '', $department_filter = '', $status_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT d.*, 
                       CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) AS full_name,
                       dep.department_name
                FROM {$this->table} d 
                LEFT JOIN departments dep ON d.department_id = dep.department_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) LIKE ? 
                          OR d.specialty LIKE ? 
                          OR d.email LIKE ? 
                          OR d.contact_number LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= 'ssss';
        }
        
        // Add department filter
        if (!empty($department_filter)) {
            $sql .= " AND d.department_id = ?";
            $params[] = $department_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND d.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        $sql .= " ORDER BY d.first_name, d.last_name ASC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        
        return $doctors;
    }
    
    /**
     * Get total count of doctors for pagination
     */
    public function getTotalDoctors($search = '', $department_filter = '', $status_filter = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} d 
                LEFT JOIN departments dep ON d.department_id = dep.department_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) LIKE ? 
                          OR d.specialty LIKE ? 
                          OR d.email LIKE ? 
                          OR d.contact_number LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= 'ssss';
        }
        
        // Add department filter
        if (!empty($department_filter)) {
            $sql .= " AND d.department_id = ?";
            $params[] = $department_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND d.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    /**
     * Get doctor by ID
     */
    public function getDoctorById($id) {
        $sql = "SELECT d.*, 
                       CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) AS full_name,
                       dep.department_name
                FROM {$this->table} d 
                LEFT JOIN departments dep ON d.department_id = dep.department_id 
                WHERE d.doctor_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get all departments for dropdown
     */
    public function getAllDepartments() {
        $sql = "SELECT department_id, department_name
                FROM departments 
                WHERE status = 'active'
                ORDER BY department_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        
        return $departments;
    }
    
    /**
     * Check if email already exists
     */
    public function emailExists($email, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        $types = 's';
        
        if ($exclude_id) {
            $sql .= " AND doctor_id != ?";
            $params[] = $exclude_id;
            $types .= 'i';
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Check if contact number already exists
     */
    public function contactExists($contact_number, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE contact_number = ?";
        $params = [$contact_number];
        $types = 's';
        
        if ($exclude_id) {
            $sql .= " AND doctor_id != ?";
            $params[] = $exclude_id;
            $types .= 'i';
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Create new doctor
     */
    public function createDoctor($data) {
        $sql = "INSERT INTO {$this->table} (
                    first_name, middle_name, last_name, specialty, contact_number, 
                    email, department_id, schedule, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Prepare variables for bind_param (required for pass by reference)
        $firstName = $data['first_name'];
        $middleName = $data['middle_name'] ?? '';
        $lastName = $data['last_name'];
        $specialty = $data['specialty'];
        $contactNumber = $data['contact_number'];
        $email = $data['email'];
        $departmentId = $data['department_id'];
        $schedule = $data['schedule'] ?? '';
        $status = $data['status'];
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssssiss', 
            $firstName,
            $middleName,
            $lastName,
            $specialty,
            $contactNumber,
            $email,
            $departmentId,
            $schedule,
            $status
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update doctor
     */
    public function updateDoctor($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET first_name = ?, middle_name = ?, last_name = ?, specialty = ?, 
                    contact_number = ?, email = ?, department_id = ?, schedule = ?, 
                    status = ?, updated_at = CURRENT_TIMESTAMP
                WHERE doctor_id = ?";
        
        // Prepare variables for bind_param (required for pass by reference)
        $firstName = $data['first_name'];
        $middleName = $data['middle_name'] ?? '';
        $lastName = $data['last_name'];
        $specialty = $data['specialty'];
        $contactNumber = $data['contact_number'];
        $email = $data['email'];
        $departmentId = $data['department_id'];
        $schedule = $data['schedule'] ?? '';
        $status = $data['status'];
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssssissi', 
            $firstName,
            $middleName,
            $lastName,
            $specialty,
            $contactNumber,
            $email,
            $departmentId,
            $schedule,
            $status,
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete doctor
     */
    public function deleteDoctor($id) {
        $sql = "DELETE FROM {$this->table} WHERE doctor_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get departments for filter dropdown
     */
    public function getDepartmentsForFilter() {
        $sql = "SELECT DISTINCT dep.department_id, dep.department_name
                FROM departments dep
                INNER JOIN {$this->table} d ON dep.department_id = d.department_id
                WHERE dep.status = 'active'
                ORDER BY dep.department_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        
        return $departments;
    }
}
?> 