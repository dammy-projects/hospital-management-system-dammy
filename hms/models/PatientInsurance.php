<?php
class PatientInsurance {
    private $conn;
    private $table = 'patient_insurance';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all patient insurance records with pagination and search
     */
    public function getPatientInsurances($page = 1, $limit = 6, $search = '', $provider_filter = '', $status_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT pi.*, 
                       CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS patient_name,
                       p.contact_number AS patient_phone,
                       ip.provider_name
                FROM {$this->table} pi 
                LEFT JOIN patients p ON pi.patient_id = p.patient_id 
                LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                          OR pi.insurance_number LIKE ? 
                          OR ip.provider_name LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add provider filter
        if (!empty($provider_filter)) {
            $sql .= " AND pi.insurance_provider_id = ?";
            $params[] = $provider_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND pi.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        $sql .= " ORDER BY p.first_name, p.last_name ASC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $insurances = [];
        while ($row = $result->fetch_assoc()) {
            $insurances[] = $row;
        }
        
        return $insurances;
    }
    
    /**
     * Get total count of patient insurance records for pagination
     */
    public function getTotalPatientInsurances($search = '', $provider_filter = '', $status_filter = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} pi 
                LEFT JOIN patients p ON pi.patient_id = p.patient_id 
                LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                          OR pi.insurance_number LIKE ? 
                          OR ip.provider_name LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add provider filter
        if (!empty($provider_filter)) {
            $sql .= " AND pi.insurance_provider_id = ?";
            $params[] = $provider_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND pi.status = ?";
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
     * Get patient insurance record by ID
     */
    public function getPatientInsuranceById($id) {
        $sql = "SELECT pi.*, 
                       CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS patient_name,
                       p.contact_number AS patient_phone,
                       ip.provider_name,
                       ip.contact_number AS provider_contact,
                       ip.address AS provider_address
                FROM {$this->table} pi 
                LEFT JOIN patients p ON pi.patient_id = p.patient_id 
                LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                WHERE pi.patient_insurance_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get all patients for dropdown
     */
    public function getAllPatients() {
        $sql = "SELECT patient_id, 
                       CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) AS patient_name
                FROM patients 
                WHERE status = 'active'
                ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        
        return $patients;
    }
    
    /**
     * Get all insurance providers for dropdown
     */
    public function getAllInsuranceProviders() {
        $sql = "SELECT insurance_provider_id, provider_name
                FROM insurance_providers 
                WHERE status = 'active'
                ORDER BY provider_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $providers = [];
        while ($row = $result->fetch_assoc()) {
            $providers[] = $row;
        }
        
        return $providers;
    }
    
    /**
     * Check if insurance number already exists for a patient
     */
    public function insuranceNumberExists($insurance_number, $patient_id, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE insurance_number = ? AND patient_id = ?";
        
        $params = [$insurance_number, $patient_id];
        $types = 'si';
        
        if ($exclude_id) {
            $sql .= " AND patient_insurance_id != ?";
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
     * Create new patient insurance record
     */
    public function createPatientInsurance($data) {
        $sql = "INSERT INTO {$this->table} (patient_id, insurance_provider_id, insurance_number, status) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iiss', 
            $data['patient_id'],
            $data['insurance_provider_id'],
            $data['insurance_number'],
            $data['status']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update patient insurance record
     */
    public function updatePatientInsurance($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET patient_id = ?, insurance_provider_id = ?, insurance_number = ?, status = ?
                WHERE patient_insurance_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iissi', 
            $data['patient_id'],
            $data['insurance_provider_id'],
            $data['insurance_number'],
            $data['status'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete patient insurance record
     */
    public function deletePatientInsurance($id) {
        $sql = "DELETE FROM {$this->table} WHERE patient_insurance_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Get insurance providers for filter dropdown
     */
    public function getInsuranceProvidersForFilter() {
        $sql = "SELECT DISTINCT ip.insurance_provider_id, ip.provider_name
                FROM insurance_providers ip
                INNER JOIN {$this->table} pi ON ip.insurance_provider_id = pi.insurance_provider_id
                WHERE ip.status = 'active'
                ORDER BY ip.provider_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $providers = [];
        while ($row = $result->fetch_assoc()) {
            $providers[] = $row;
        }
        
        return $providers;
    }
}
?> 