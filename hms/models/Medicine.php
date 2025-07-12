<?php
class Medicine {
    private $conn;
    private $table = 'medicines';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all medicines with pagination and search
     */
    public function getMedicines($page = 1, $limit = 6, $search = '', $dosage_form_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (medicine_name LIKE ? OR dosage_form LIKE ? OR strength LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add dosage form filter
        if (!empty($dosage_form_filter)) {
            $sql .= " AND dosage_form = ?";
            $params[] = $dosage_form_filter;
            $types .= 's';
        }
        
        $sql .= " ORDER BY medicine_name ASC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $medicines = [];
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
        
        return $medicines;
    }
    
    /**
     * Get total count of medicines for pagination
     */
    public function getTotalMedicines($search = '', $dosage_form_filter = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (medicine_name LIKE ? OR dosage_form LIKE ? OR strength LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add dosage form filter
        if (!empty($dosage_form_filter)) {
            $sql .= " AND dosage_form = ?";
            $params[] = $dosage_form_filter;
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
     * Get medicine by ID
     */
    public function getMedicineById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE medicine_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Create new medicine
     */
    public function createMedicine($data) {
        $sql = "INSERT INTO {$this->table} (medicine_name, dosage_form, strength) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param('sss', 
            $data['medicine_name'],
            $data['dosage_form'],
            $data['strength']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update medicine
     */
    public function updateMedicine($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET medicine_name = ?, dosage_form = ?, strength = ? 
                WHERE medicine_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param('sssi', 
            $data['medicine_name'],
            $data['dosage_form'],
            $data['strength'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete medicine
     */
    public function deleteMedicine($id) {
        $sql = "DELETE FROM {$this->table} WHERE medicine_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if medicine name exists (for validation)
     */
    public function medicineNameExists($medicineName, $excludeId = null) {
        $sql = "SELECT medicine_id FROM {$this->table} WHERE medicine_name = ?";
        $params = [$medicineName];
        $types = 's';
        
        if ($excludeId) {
            $sql .= " AND medicine_id != ?";
            $params[] = $excludeId;
            $types .= 'i';
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    /**
     * Get distinct dosage forms for filter dropdown
     */
    public function getDosageForms() {
        $sql = "SELECT DISTINCT dosage_form FROM {$this->table} WHERE dosage_form IS NOT NULL AND dosage_form != '' ORDER BY dosage_form";
        $result = $this->conn->query($sql);
        
        $dosageForms = [];
        while ($row = $result->fetch_assoc()) {
            $dosageForms[] = $row;
        }
        
        return $dosageForms;
    }
    
    /**
     * Get medicine statistics
     */
    public function getMedicineStats() {
        $sql = "SELECT 
                    COUNT(*) as total_medicines,
                    COUNT(DISTINCT dosage_form) as total_dosage_forms,
                    SUM(CASE WHEN dosage_form = 'Tablet' THEN 1 ELSE 0 END) as tablets,
                    SUM(CASE WHEN dosage_form = 'Capsule' THEN 1 ELSE 0 END) as capsules,
                    SUM(CASE WHEN dosage_form = 'Injection' THEN 1 ELSE 0 END) as injections,
                    SUM(CASE WHEN dosage_form = 'Inhaler' THEN 1 ELSE 0 END) as inhalers
                FROM {$this->table}";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
}
?> 