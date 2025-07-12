<?php
class InventoryWithdrawal {
    private $conn;
    private $table = 'inventory_withdrawals';
    private $items_table = 'inventory_withdrawal_items';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all withdrawals with pagination and search
     */
    public function getWithdrawals($page = 1, $limit = 6, $search = '', $status_filter = '', $user_filter = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT w.*, 
                       u.full_name as performed_by_name,
                       COUNT(wi.withdrawal_item_id) as total_items
                FROM {$this->table} w
                LEFT JOIN users u ON w.performed_by = u.user_id
                LEFT JOIN {$this->items_table} wi ON w.withdrawal_id = wi.withdrawal_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (w.notes LIKE ? OR u.full_name LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam]);
            $types .= 'ss';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND w.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add user filter
        if (!empty($user_filter)) {
            $sql .= " AND w.performed_by = ?";
            $params[] = $user_filter;
            $types .= 'i';
        }
        
        $sql .= " GROUP BY w.withdrawal_id ORDER BY w.withdrawal_date DESC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $withdrawals = [];
        while ($row = $result->fetch_assoc()) {
            $withdrawals[] = $row;
        }
        
        return $withdrawals;
    }
    
    /**
     * Get total count of withdrawals for pagination
     */
    public function getTotalWithdrawals($search = '', $status_filter = '', $user_filter = '') {
        $sql = "SELECT COUNT(DISTINCT w.withdrawal_id) as total 
                FROM {$this->table} w
                LEFT JOIN users u ON w.performed_by = u.user_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        // Add search conditions
        if (!empty($search)) {
            $sql .= " AND (w.notes LIKE ? OR u.full_name LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam]);
            $types .= 'ss';
        }
        
        // Add status filter
        if (!empty($status_filter)) {
            $sql .= " AND w.status = ?";
            $params[] = $status_filter;
            $types .= 's';
        }
        
        // Add user filter
        if (!empty($user_filter)) {
            $sql .= " AND w.performed_by = ?";
            $params[] = $user_filter;
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
     * Get withdrawal by ID with items
     */
    public function getWithdrawalById($id) {
        // Get withdrawal details
        $sql = "SELECT w.*, 
                       u.full_name as performed_by_name
                FROM {$this->table} w
                LEFT JOIN users u ON w.performed_by = u.user_id
                WHERE w.withdrawal_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $withdrawal = $result->fetch_assoc();
        
        if (!$withdrawal) {
            return null;
        }
        
        // Get withdrawal items
        $withdrawal['items'] = $this->getWithdrawalItems($id);
        
        return $withdrawal;
    }
    
    /**
     * Get withdrawal items for a withdrawal
     */
    public function getWithdrawalItems($withdrawalId) {
        $sql = "SELECT wi.*, i.item_name, i.unit, i.quantity_in_stock, c.category_name
                FROM {$this->items_table} wi
                LEFT JOIN inventory_items i ON wi.item_id = i.item_id
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id
                WHERE wi.withdrawal_id = ?
                ORDER BY wi.withdrawal_item_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $withdrawalId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Create new withdrawal with items
     */
    public function createWithdrawal($data) {
        $this->conn->begin_transaction();
        
        try {
            // Create withdrawal
            $sql = "INSERT INTO {$this->table} (withdrawal_date, notes, performed_by, status) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ssis', 
                $data['withdrawal_date'],
                $data['notes'],
                $data['performed_by'],
                $data['status']
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to create withdrawal');
            }
            
            $withdrawalId = $this->conn->insert_id;
            
            // Add withdrawal items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addWithdrawalItem($withdrawalId, $item);
                }
            }
            
            $this->conn->commit();
            return $withdrawalId;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    /**
     * Update withdrawal with items
     */
    public function updateWithdrawal($id, $data) {
        $this->conn->begin_transaction();
        
        try {
            // Update withdrawal
            $sql = "UPDATE {$this->table} 
                    SET withdrawal_date = ?, notes = ?, performed_by = ?, status = ?
                    WHERE withdrawal_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ssisi', 
                $data['withdrawal_date'],
                $data['notes'],
                $data['performed_by'],
                $data['status'],
                $id
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update withdrawal');
            }
            
            // Delete existing items
            $this->deleteWithdrawalItems($id);
            
            // Add new items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $this->addWithdrawalItem($id, $item);
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
     * Delete withdrawal and its items
     */
    public function deleteWithdrawal($id) {
        $this->conn->begin_transaction();
        
        try {
            // Delete withdrawal items first (foreign key constraint)
            $this->deleteWithdrawalItems($id);
            
            // Delete withdrawal
            $sql = "DELETE FROM {$this->table} WHERE withdrawal_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete withdrawal');
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    /**
     * Add withdrawal item
     */
    private function addWithdrawalItem($withdrawalId, $item) {
        $sql = "INSERT INTO {$this->items_table} 
                (withdrawal_id, item_id, quantity) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iii', 
            $withdrawalId,
            $item['item_id'],
            $item['quantity']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Delete all withdrawal items for a withdrawal
     */
    private function deleteWithdrawalItems($withdrawalId) {
        $sql = "DELETE FROM {$this->items_table} WHERE withdrawal_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $withdrawalId);
        return $stmt->execute();
    }
    
    /**
     * Get all users for dropdown
     */
    public function getUsers() {
        $sql = "SELECT user_id, full_name FROM users WHERE status = 'active' ORDER BY full_name";
        $result = $this->conn->query($sql);
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    /**
     * Get all inventory items for dropdown
     */
    public function getInventoryItems() {
        $sql = "SELECT i.item_id, i.item_name, i.quantity_in_stock, i.unit, c.category_name
                FROM inventory_items i
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id
                WHERE i.status = 'active'
                ORDER BY c.category_name, i.item_name";
        $result = $this->conn->query($sql);
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get withdrawal statistics
     */
    public function getWithdrawalStats() {
        $sql = "SELECT 
                    COUNT(*) as total_withdrawals,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_withdrawals,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_withdrawals,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_withdrawals,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_withdrawals
                FROM {$this->table}";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Get recent withdrawals
     */
    public function getRecentWithdrawals($limit = 10) {
        $sql = "SELECT w.*, 
                       u.full_name as performed_by_name,
                       COUNT(wi.withdrawal_item_id) as total_items
                FROM {$this->table} w
                LEFT JOIN users u ON w.performed_by = u.user_id
                LEFT JOIN {$this->items_table} wi ON w.withdrawal_id = wi.withdrawal_id
                GROUP BY w.withdrawal_id 
                ORDER BY w.withdrawal_date DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $withdrawals = [];
        while ($row = $result->fetch_assoc()) {
            $withdrawals[] = $row;
        }
        
        return $withdrawals;
    }
    
    /**
     * Update inventory stock when withdrawal is completed
     */
    public function updateInventoryStock($withdrawalId) {
        $items = $this->getWithdrawalItems($withdrawalId);
        
        if (empty($items)) {
            throw new Exception("No items found for withdrawal ID: {$withdrawalId}");
        }
        
        $updatedItems = [];
        
        foreach ($items as $item) {
            // First, get current stock for this item
            $stockCheckSql = "SELECT quantity_in_stock, item_name FROM inventory_items WHERE item_id = ?";
            $stockStmt = $this->conn->prepare($stockCheckSql);
            $stockStmt->bind_param('i', $item['item_id']);
            $stockStmt->execute();
            $stockResult = $stockStmt->get_result();
            $currentStock = $stockResult->fetch_assoc();
            
            if (!$currentStock) {
                throw new Exception("Item not found: {$item['item_name']}");
            }
            
            // Check if we have enough stock
            if ($currentStock['quantity_in_stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for {$currentStock['item_name']}. Available: {$currentStock['quantity_in_stock']}, Required: {$item['quantity']}");
            }
            
            // Update the inventory stock
            $updateSql = "UPDATE inventory_items 
                         SET quantity_in_stock = quantity_in_stock - ?, 
                             last_updated = NOW()
                         WHERE item_id = ?";
            
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->bind_param('ii', $item['quantity'], $item['item_id']);
            
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update stock for item: {$currentStock['item_name']}");
            }
            
            if ($updateStmt->affected_rows === 0) {
                throw new Exception("No rows affected when updating stock for item: {$currentStock['item_name']}");
            }
            
            // Record what was updated for logging
            $newStock = $currentStock['quantity_in_stock'] - $item['quantity'];
            $updatedItems[] = [
                'item_name' => $currentStock['item_name'],
                'old_stock' => $currentStock['quantity_in_stock'],
                'withdrawn' => $item['quantity'],
                'new_stock' => $newStock
            ];
            
            // Add inventory movement record
            $this->addInventoryMovement($item['item_id'], 'out', $item['quantity'], "Withdrawal #$withdrawalId completed");
        }
        
        // Log the successful stock updates
        error_log("Stock updated for withdrawal #$withdrawalId: " . json_encode($updatedItems));
        
        return $updatedItems;
    }
    
    /**
     * Add inventory movement record
     */
    private function addInventoryMovement($itemId, $movementType, $quantity, $notes) {
        $sql = "INSERT INTO inventory_movements (item_id, movement_type, quantity, movement_date, notes, performed_by) 
                VALUES (?, ?, ?, NOW(), ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $userId = $_SESSION['user_id'] ?? null;
        $stmt->bind_param('isisi', $itemId, $movementType, $quantity, $notes, $userId);
        
        return $stmt->execute();
    }
    
    /**
     * Check if withdrawal can be completed (stock availability)
     */
    public function checkStockAvailability($withdrawalId) {
        $items = $this->getWithdrawalItems($withdrawalId);
        $issues = [];
        
        foreach ($items as $item) {
            if ($item['quantity_in_stock'] < $item['quantity']) {
                $issues[] = [
                    'item_name' => $item['item_name'],
                    'requested' => $item['quantity'],
                    'available' => $item['quantity_in_stock']
                ];
            }
        }
        
        return $issues;
    }
}
?> 