<?php
class InventoryCategory {
    private $conn;
    private $table = 'inventory_categories';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all categories with pagination and search
     */
    public function getCategories($page = 1, $limit = 6, $search = '', $sort = 'category_name') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND category_name LIKE ?";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $types .= 's';
        }
        
        // Add sorting
        $allowedSorts = ['category_name', 'category_id'];
        if (in_array($sort, $allowedSorts)) {
            $sql .= " ORDER BY $sort ASC";
        } else {
            $sql .= " ORDER BY category_name ASC";
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    /**
     * Get total count of categories for pagination
     */
    public function getTotalCategories($search = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND category_name LIKE ?";
            $searchParam = "%$search%";
            $params[] = $searchParam;
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
     * Get category by ID
     */
    public function getCategoryById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Create new category
     */
    public function createCategory($data) {
        $sql = "INSERT INTO {$this->table} (category_name) VALUES (?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $data['category_name']);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update category
     */
    public function updateCategory($id, $data) {
        $sql = "UPDATE {$this->table} SET category_name = ? WHERE category_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $data['category_name'], $id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete category
     */
    public function deleteCategory($id) {
        // First check if category is used in inventory_items
        $checkSql = "SELECT COUNT(*) as count FROM inventory_items WHERE category_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] > 0) {
            throw new Exception('Cannot delete category: it is currently being used by inventory items');
        }
        
        $sql = "DELETE FROM {$this->table} WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if category name exists (for validation)
     */
    public function categoryNameExists($categoryName, $excludeId = null) {
        $sql = "SELECT category_id FROM {$this->table} WHERE category_name = ?";
        $params = [$categoryName];
        $types = 's';
        
        if ($excludeId) {
            $sql .= " AND category_id != ?";
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
     * Get category statistics
     */
    public function getCategoryStats() {
        $sql = "SELECT 
                    COUNT(c.category_id) as total_categories,
                    COALESCE(SUM(CASE WHEN i.item_id IS NOT NULL THEN 1 ELSE 0 END), 0) as categories_with_items,
                    COALESCE(SUM(CASE WHEN i.item_id IS NULL THEN 1 ELSE 0 END), 0) as empty_categories
                FROM {$this->table} c 
                LEFT JOIN inventory_items i ON c.category_id = i.category_id";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Get item count for each category
     */
    public function getCategoryItemCounts() {
        $sql = "SELECT 
                    c.category_id,
                    c.category_name,
                    COUNT(i.item_id) as item_count
                FROM {$this->table} c 
                LEFT JOIN inventory_items i ON c.category_id = i.category_id
                GROUP BY c.category_id, c.category_name
                ORDER BY c.category_name";
        
        $result = $this->conn->query($sql);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
}
?> 