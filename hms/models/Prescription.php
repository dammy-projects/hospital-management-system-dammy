<?php
class Prescription {
    private $conn;
    private $table = 'prescriptions';
    private $items_table = 'prescription_items';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all prescriptions with pagination and search
     */
    public function getPrescriptions($page = 1, $limit = 6, $search = '', $status_filter = '', $patient_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.*, 
                       CONCAT(pt.first_name, ' ', IFNULL(pt.middle_name, ''), ' ', pt.last_name) AS patient_name,
                       CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) AS doctor_name,
                       d.specialty as doctor_specialty,
                       COUNT(pi.prescription_item_id) as total_medicines
                FROM {$this->table} p
                LEFT JOIN patients pt ON p.patient_id = pt.patient_id
                LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                LEFT JOIN {$this->items_table} pi ON p.prescription_id = pi.prescription_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(pt.first_name, ' ', pt.last_name) LIKE ? 
                          OR CONCAT(d.first_name, ' ', d.last_name) LIKE ? 
                          OR p.notes LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND p.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add patient filter
        if (!empty($patient_filter)) {
            $sql .= " AND p.patient_id = ?";
            $params[] = $patient_filter;
            $types .= 'i';
        }
        
        $sql .= " GROUP BY p.prescription_id ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        
        return $prescriptions;
    }
    
    /**
     * Get total count of prescriptions for pagination
     */
    public function getTotalPrescriptions($search = '', $status_filter = '', $patient_filter = '') {
        $sql = "SELECT COUNT(DISTINCT p.prescription_id) as total 
                FROM {$this->table} p
                LEFT JOIN patients pt ON p.patient_id = pt.patient_id
                LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (CONCAT(pt.first_name, ' ', pt.last_name) LIKE ? 
                          OR CONCAT(d.first_name, ' ', d.last_name) LIKE ? 
                          OR p.notes LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND p.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add patient filter
        if (!empty($patient_filter)) {
            $sql .= " AND p.patient_id = ?";
            $params[] = $patient_filter;
            $types .= 'i';
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
     * Get prescription by ID with items
     */
    public function getPrescriptionById($id) {
        // Get prescription details
        $sql = "SELECT p.*, 
                       CONCAT(pt.first_name, ' ', IFNULL(pt.middle_name, ''), ' ', pt.last_name) AS patient_name,
                       CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) AS doctor_name,
                       d.specialty as doctor_specialty
                FROM {$this->table} p
                LEFT JOIN patients pt ON p.patient_id = pt.patient_id
                LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                WHERE p.prescription_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prescription = $result->fetch_assoc();
        
        if (!$prescription) {
            return null;
        }
        
        // Get prescription items
        $prescription['items'] = $this->getPrescriptionItems($id);
        
        return $prescription;
    }
    
    /**
     * Get prescription items for a prescription
     */
    public function getPrescriptionItems($prescriptionId) {
        $sql = "SELECT pi.*, m.medicine_name, m.dosage_form, m.strength
                FROM {$this->items_table} pi
                LEFT JOIN medicines m ON pi.medicine_id = m.medicine_id
                WHERE pi.prescription_id = ?
                ORDER BY pi.prescription_item_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $prescriptionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Create new prescription with items
     */
    public function createPrescription($data) {
        $this->conn->begin_transaction();
        
        try {
            // Create prescription
            $sql = "INSERT INTO {$this->table} (patient_id, doctor_id, prescription_date, notes, status) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('iisss', 
                $data['patient_id'],
                $data['doctor_id'],
                $data['prescription_date'],
                $data['notes'],
                $data['status']
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to create prescription');
            }
            
            $prescriptionId = $this->conn->insert_id;
            
            // Add prescription items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addPrescriptionItem($prescriptionId, $item);
                }
            }
            
            $this->conn->commit();
            return $prescriptionId;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    /**
     * Update prescription with items
     */
    public function updatePrescription($id, $data) {
        $this->conn->begin_transaction();
        
        try {
            // Update prescription
            $sql = "UPDATE {$this->table} 
                    SET patient_id = ?, doctor_id = ?, prescription_date = ?, notes = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE prescription_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('iisssi', 
                $data['patient_id'],
                $data['doctor_id'],
                $data['prescription_date'],
                $data['notes'],
                $data['status'],
                $id
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update prescription');
            }
            
            // Delete existing items
            $this->deletePrescriptionItems($id);
            
            // Add new items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addPrescriptionItem($id, $item);
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    /**
     * Delete prescription and its items
     */
    public function deletePrescription($id) {
        $this->conn->begin_transaction();
        
        try {
            // Delete prescription items first (foreign key constraint)
            $this->deletePrescriptionItems($id);
            
            // Delete prescription
            $sql = "DELETE FROM {$this->table} WHERE prescription_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete prescription');
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    /**
     * Add prescription item
     */
    private function addPrescriptionItem($prescriptionId, $item) {
        $sql = "INSERT INTO {$this->items_table} 
                (prescription_id, medicine_id, dosage, frequency, duration_days, quantity, instructions) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisssis', 
            $prescriptionId,
            $item['medicine_id'],
            $item['dosage'],
            $item['frequency'],
            $item['duration_days'],
            $item['quantity'],
            $item['instructions']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete all prescription items for a prescription
     */
    private function deletePrescriptionItems($prescriptionId) {
        $sql = "DELETE FROM {$this->items_table} WHERE prescription_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $prescriptionId);
        return $stmt->execute();
    }
    
    /**
     * Get all patients for dropdown
     */
    public function getPatients() {
        $sql = "SELECT patient_id, 
                       CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) AS patient_name
                FROM patients 
                WHERE status = 'active' 
                ORDER BY first_name, last_name";
        
        $result = $this->conn->query($sql);
        
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        
        return $patients;
    }
    
    /**
     * Get all doctors for dropdown
     */
    public function getDoctors() {
        $sql = "SELECT doctor_id, 
                       CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) AS doctor_name,
                       specialty
                FROM doctors 
                WHERE status = 'active' 
                ORDER BY first_name, last_name";
        
        $result = $this->conn->query($sql);
        
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        
        return $doctors;
    }
    
    /**
     * Get all medicines for dropdown
     */
    public function getMedicines() {
        $sql = "SELECT medicine_id, medicine_name, dosage_form, strength
                FROM medicines 
                ORDER BY medicine_name";
        
        $result = $this->conn->query($sql);
        
        $medicines = [];
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
        
        return $medicines;
    }
    
    /**
     * Get prescription statistics
     */
    public function getPrescriptionStats() {
        $sql = "SELECT 
                    COUNT(*) as total_prescriptions,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_prescriptions,
                    SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END) as fulfilled_prescriptions,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_prescriptions,
                    COUNT(DISTINCT patient_id) as total_patients_with_prescriptions
                FROM {$this->table}";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Get prescriptions for a specific patient
     */
    public function getPrescriptionsByPatient($patientId) {
        $sql = "SELECT p.*, 
                       CONCAT(d.first_name, ' ', IFNULL(d.middle_name, ''), ' ', d.last_name) AS doctor_name,
                       d.specialty as doctor_specialty,
                       COUNT(pi.prescription_item_id) as total_medicines
                FROM {$this->table} p
                LEFT JOIN doctors d ON p.doctor_id = d.doctor_id 
                LEFT JOIN {$this->items_table} pi ON p.prescription_id = pi.prescription_id
                WHERE p.patient_id = ?
                GROUP BY p.prescription_id 
                ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        
        return $prescriptions;
    }
}
?> 