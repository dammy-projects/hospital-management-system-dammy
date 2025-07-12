<?php
class User {
    private $conn;
    private $table = 'users';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all users with pagination and search
     */
    public function getUsers($page = 1, $limit = 6, $search = '', $role_filter = '', $status_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT u.*, r.role_name 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE ? OR u.username LIKE ? OR u.phone_number LIKE ? OR u.address LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= 'ssss';
        }
        
        // Add role filter
        if (!empty($role_filter)) {
            $sql .= " AND u.role_id = ?";
            $params[] = $role_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND u.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        $sql .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    /**
     * Get total count of users for pagination
     */
    public function getTotalUsers($search = '', $role_filter = '', $status_filter = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} u WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE ? OR u.username LIKE ? OR u.phone_number LIKE ? OR u.address LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= 'ssss';
        }
        
        // Add role filter
        if (!empty($role_filter)) {
            $sql .= " AND u.role_id = ?";
            $params[] = $role_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND u.status = ?";
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
     * Get user by ID
     */
    public function getUserById($id) {
        $sql = "SELECT u.*, r.role_name 
                FROM {$this->table} u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                WHERE u.user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Create new user
     */
    public function createUser($data) {
        $sql = "INSERT INTO {$this->table} (full_name, phone_number, address, username, password, role_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bind_param('sssssss', 
            $data['full_name'],
            $data['phone_number'],
            $data['address'],
            $data['username'],
            $hashedPassword,
            $data['role_id'],
            $data['status']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        // Check if password should be updated
        if (!empty($data['password'])) {
            $sql = "UPDATE {$this->table} 
                    SET full_name = ?, phone_number = ?, address = ?, username = ?, password = ?, role_id = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE user_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt->bind_param('sssssssi', 
                $data['full_name'],
                $data['phone_number'],
                $data['address'],
                $data['username'],
                $hashedPassword,
                $data['role_id'],
                $data['status'],
                $id
            );
        } else {
            $sql = "UPDATE {$this->table} 
                    SET full_name = ?, phone_number = ?, address = ?, username = ?, role_id = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE user_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bind_param('ssssssi', 
                $data['full_name'],
                $data['phone_number'],
                $data['address'],
                $data['username'],
                $data['role_id'],
                $data['status'],
                $id
            );
        }
        
        return $stmt->execute();
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if username exists (for validation)
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT user_id FROM {$this->table} WHERE username = ?";
        $params = [$username];
        $types = 's';
        
        if ($excludeId) {
            $sql .= " AND user_id != ?";
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
     * Get all roles for dropdown
     */
    public function getRoles() {
        $sql = "SELECT * FROM roles ORDER BY role_name";
        $result = $this->conn->query($sql);
        
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
        
        return $roles;
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
                    SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_users
                FROM {$this->table}";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
}
?> 