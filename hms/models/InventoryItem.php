<?php
class InventoryItem {
    private $conn;
    private $table = 'inventory_items';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all inventory items with pagination and search
     */
    public function getItems($page = 1, $limit = 6, $search = '', $category_filter = '', $status_filter = '', $stock_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT i.*, c.category_name 
                FROM {$this->table} i 
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (i.item_name LIKE ? OR i.item_description LIKE ? OR i.serial_number LIKE ? OR i.product_number LIKE ? OR c.category_name LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= 'sssss';
        }
        
        // Add category filter
        if (!empty($category_filter)) {
            $sql .= " AND i.category_id = ?";
            $params[] = $category_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND i.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add stock level filter
        if (!empty($stock_filter)) {
            if ($stock_filter === 'low') {
                $sql .= " AND i.quantity_in_stock <= i.reorder_level AND i.quantity_in_stock > 0";
            } elseif ($stock_filter === 'out') {
                $sql .= " AND i.quantity_in_stock = 0";
            }
        }
        
        $sql .= " ORDER BY i.last_updated DESC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get total count of items for pagination
     */
    public function getTotalItems($search = '', $category_filter = '', $status_filter = '', $stock_filter = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} i 
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (i.item_name LIKE ? OR i.item_description LIKE ? OR i.serial_number LIKE ? OR i.product_number LIKE ? OR c.category_name LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            $types .= 'sssss';
        }
        
        // Add category filter
        if (!empty($category_filter)) {
            $sql .= " AND i.category_id = ?";
            $params[] = $category_filter;
            $types .= 'i';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND i.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add stock level filter
        if (!empty($stock_filter)) {
            if ($stock_filter === 'low') {
                $sql .= " AND i.quantity_in_stock <= i.reorder_level AND i.quantity_in_stock > 0";
            } elseif ($stock_filter === 'out') {
                $sql .= " AND i.quantity_in_stock = 0";
            }
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
     * Get item by ID
     */
    public function getItemById($id) {
        $sql = "SELECT i.*, c.category_name 
                FROM {$this->table} i 
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                WHERE i.item_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Create new item
     */
    public function createItem($data) {
        $sql = "INSERT INTO {$this->table} (item_name, item_description, serial_number, product_number, category_id, quantity_in_stock, unit, reorder_level, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssiiiss', 
            $data['item_name'],
            $data['item_description'],
            $data['serial_number'],
            $data['product_number'],
            $data['category_id'],
            $data['quantity_in_stock'],
            $data['unit'],
            $data['reorder_level'],
            $data['status']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update item
     */
    public function updateItem($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET item_name = ?, item_description = ?, serial_number = ?, product_number = ?, category_id = ?, quantity_in_stock = ?, unit = ?, reorder_level = ?, status = ?
                WHERE item_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssiiissi', 
            $data['item_name'],
            $data['item_description'],
            $data['serial_number'],
            $data['product_number'],
            $data['category_id'],
            $data['quantity_in_stock'],
            $data['unit'],
            $data['reorder_level'],
            $data['status'],
            $id
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete item
     */
    public function deleteItem($id) {
        // First check if item is used in other tables (prescriptions, movements, etc.)
        $checkSql = "SELECT COUNT(*) as count FROM prescription_items WHERE inventory_item_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $row = $checkResult->fetch_assoc();
        
        if ($row['count'] > 0) {
            throw new Exception('Cannot delete item: it is currently being used in prescriptions');
        }
        
        // Check inventory movements
        $checkMovementSql = "SELECT COUNT(*) as count FROM inventory_movements WHERE item_id = ?";
        $checkMovementStmt = $this->conn->prepare($checkMovementSql);
        $checkMovementStmt->bind_param('i', $id);
        $checkMovementStmt->execute();
        $checkMovementResult = $checkMovementStmt->get_result();
        $movementRow = $checkMovementResult->fetch_assoc();
        
        if ($movementRow['count'] > 0) {
            throw new Exception('Cannot delete item: it has movement history');
        }
        
        $sql = "DELETE FROM {$this->table} WHERE item_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if serial number exists (for validation)
     */
    public function serialNumberExists($serialNumber, $excludeId = null) {
        if (empty($serialNumber)) {
            return false; // Serial number is optional
        }
        
        $sql = "SELECT item_id FROM {$this->table} WHERE serial_number = ?";
        $params = [$serialNumber];
        $types = 's';
        
        if ($excludeId) {
            $sql .= " AND item_id != ?";
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
     * Get all categories for dropdown
     */
    public function getCategories() {
        $sql = "SELECT * FROM inventory_categories ORDER BY category_name";
        $result = $this->conn->query($sql);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    /**
     * Get inventory statistics
     */
    public function getInventoryStats() {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_items,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_items,
                    SUM(CASE WHEN status = 'discontinued' THEN 1 ELSE 0 END) as discontinued_items,
                    SUM(CASE WHEN quantity_in_stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(CASE WHEN quantity_in_stock <= reorder_level AND quantity_in_stock > 0 THEN 1 ELSE 0 END) as low_stock
                FROM {$this->table}";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Get low stock items
     */
    public function getLowStockItems($limit = 10) {
        $sql = "SELECT i.*, c.category_name 
                FROM {$this->table} i 
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                WHERE i.quantity_in_stock <= i.reorder_level AND i.quantity_in_stock > 0 AND i.status = 'active'
                ORDER BY (i.quantity_in_stock / GREATEST(i.reorder_level, 1)) ASC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get out of stock items
     */
    public function getOutOfStockItems($limit = 10) {
        $sql = "SELECT i.*, c.category_name 
                FROM {$this->table} i 
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                WHERE i.quantity_in_stock = 0 AND i.status = 'active'
                ORDER BY i.last_updated DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Update item stock quantity
     */
    public function updateStock($itemId, $newQuantity, $reason = '') {
        $sql = "UPDATE {$this->table} SET quantity_in_stock = ? WHERE item_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $newQuantity, $itemId);
        
        if ($stmt->execute()) {
            // Log the stock change in inventory_movements if reason is provided
            if (!empty($reason)) {
                $this->logStockMovement($itemId, $newQuantity, $reason);
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Log stock movement
     */
    private function logStockMovement($itemId, $quantity, $notes) {
        $sql = "INSERT INTO inventory_movements (item_id, movement_type, quantity, notes, movement_date) 
                VALUES (?, 'in', ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iis', $itemId, $quantity, $notes);
        $stmt->execute();
    }
}
?> 