<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/InventoryItem.php';

class InventoryItemController {
    private $inventoryItem;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->inventoryItem = new InventoryItem($conn);
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
                $this->getItems();
                break;
            case 'get':
                $this->getItem();
                break;
            case 'categories':
                $this->getCategories();
                break;
            case 'stats':
                $this->getStats();
                break;
            case 'low-stock':
                $this->getLowStockItems();
                break;
            case 'out-of-stock':
                $this->getOutOfStockItems();
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
                $this->createItem();
                break;
            case 'update-stock':
                $this->updateStock();
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
                $this->updateItem();
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
                $this->deleteItem();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get items with pagination and search
     */
    private function getItems() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $category_filter = $_GET['category_filter'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        $stock_filter = $_GET['stock_filter'] ?? '';
        
        $items = $this->inventoryItem->getItems($page, $limit, $search, $category_filter, $status_filter, $stock_filter);
        $total = $this->inventoryItem->getTotalItems($search, $category_filter, $status_filter, $stock_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single item
     */
    private function getItem() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Item ID is required');
        }
        
        $item = $this->inventoryItem->getItemById($id);
        if (!$item) {
            throw new Exception('Item not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $item
        ]);
    }
    
    /**
     * Create new item
     */
    private function createItem() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['item_name', 'category_id', 'quantity_in_stock', 'status'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate item name
        $itemName = trim($input['item_name']);
        if (strlen($itemName) < 2) {
            throw new Exception('Item name must be at least 2 characters long');
        }
        
        if (strlen($itemName) > 100) {
            throw new Exception('Item name must not exceed 100 characters');
        }
        
        // Validate serial number uniqueness
        if (!empty($input['serial_number']) && $this->inventoryItem->serialNumberExists($input['serial_number'])) {
            throw new Exception('Serial number already exists');
        }
        
        // Validate numeric fields
        if (!is_numeric($input['quantity_in_stock']) || $input['quantity_in_stock'] < 0) {
            throw new Exception('Quantity in stock must be a non-negative number');
        }
        
        if (isset($input['reorder_level']) && (!is_numeric($input['reorder_level']) || $input['reorder_level'] < 0)) {
            throw new Exception('Reorder level must be a non-negative number');
        }
        
        // Prepare data
        $itemData = [
            'item_name' => $itemName,
            'item_description' => trim($input['item_description'] ?? ''),
            'serial_number' => trim($input['serial_number'] ?? ''),
            'product_number' => trim($input['product_number'] ?? ''),
            'category_id' => intval($input['category_id']),
            'quantity_in_stock' => intval($input['quantity_in_stock']),
            'unit' => trim($input['unit'] ?? ''),
            'reorder_level' => intval($input['reorder_level'] ?? 0),
            'status' => $input['status']
        ];
        
        $itemId = $this->inventoryItem->createItem($itemData);
        if ($itemId) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Created new inventory item: {$itemName}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => ['item_id' => $itemId]
            ]);
        } else {
            throw new Exception('Failed to create item');
        }
    }
    
    /**
     * Update item
     */
    private function updateItem() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Item ID is required');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['item_name', 'category_id', 'quantity_in_stock', 'status'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Check if item exists
        $existingItem = $this->inventoryItem->getItemById($id);
        if (!$existingItem) {
            throw new Exception('Item not found');
        }
        
        // Validate item name
        $itemName = trim($input['item_name']);
        if (strlen($itemName) < 2) {
            throw new Exception('Item name must be at least 2 characters long');
        }
        
        if (strlen($itemName) > 100) {
            throw new Exception('Item name must not exceed 100 characters');
        }
        
        // Validate serial number uniqueness
        if (!empty($input['serial_number']) && $this->inventoryItem->serialNumberExists($input['serial_number'], $id)) {
            throw new Exception('Serial number already exists');
        }
        
        // Validate numeric fields
        if (!is_numeric($input['quantity_in_stock']) || $input['quantity_in_stock'] < 0) {
            throw new Exception('Quantity in stock must be a non-negative number');
        }
        
        if (isset($input['reorder_level']) && (!is_numeric($input['reorder_level']) || $input['reorder_level'] < 0)) {
            throw new Exception('Reorder level must be a non-negative number');
        }
        
        // Prepare data
        $itemData = [
            'item_name' => $itemName,
            'item_description' => trim($input['item_description'] ?? ''),
            'serial_number' => trim($input['serial_number'] ?? ''),
            'product_number' => trim($input['product_number'] ?? ''),
            'category_id' => intval($input['category_id']),
            'quantity_in_stock' => intval($input['quantity_in_stock']),
            'unit' => trim($input['unit'] ?? ''),
            'reorder_level' => intval($input['reorder_level'] ?? 0),
            'status' => $input['status']
        ];
        
        $success = $this->inventoryItem->updateItem($id, $itemData);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Updated inventory item: {$existingItem['item_name']} to {$itemName}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Item updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update item');
        }
    }
    
    /**
     * Delete item
     */
    private function deleteItem() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Item ID is required');
        }
        
        // Get item info before deletion for logging
        $itemInfo = $this->inventoryItem->getItemById($id);
        if (!$itemInfo) {
            throw new Exception('Item not found');
        }
        
        try {
            $success = $this->inventoryItem->deleteItem($id);
            if ($success) {
                // Log the action
                $this->logAction($_SESSION['user_id'], "Deleted inventory item: {$itemInfo['item_name']}");
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Item deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete item');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Get all categories for dropdowns
     */
    private function getCategories() {
        $categories = $this->inventoryItem->getCategories();
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
    }
    
    /**
     * Get inventory statistics
     */
    private function getStats() {
        $stats = $this->inventoryItem->getInventoryStats();
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Get low stock items
     */
    private function getLowStockItems() {
        $limit = intval($_GET['limit'] ?? 10);
        $items = $this->inventoryItem->getLowStockItems($limit);
        echo json_encode([
            'success' => true,
            'data' => $items
        ]);
    }
    
    /**
     * Get out of stock items
     */
    private function getOutOfStockItems() {
        $limit = intval($_GET['limit'] ?? 10);
        $items = $this->inventoryItem->getOutOfStockItems($limit);
        echo json_encode([
            'success' => true,
            'data' => $items
        ]);
    }
    
    /**
     * Update stock quantity
     */
    private function updateStock() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['item_id']) || !isset($input['quantity'])) {
            throw new Exception('Item ID and quantity are required');
        }
        
        $itemId = intval($input['item_id']);
        $quantity = intval($input['quantity']);
        $reason = $input['reason'] ?? 'Stock update';
        
        if ($quantity < 0) {
            throw new Exception('Quantity cannot be negative');
        }
        
        $success = $this->inventoryItem->updateStock($itemId, $quantity, $reason);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Updated stock for item ID {$itemId}: {$quantity} units - {$reason}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Stock updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update stock');
        }
    }
    
    /**
     * Log system action
     */
    private function logAction($userId, $action) {
        $sql = "INSERT INTO system_logs (user_id, action, log_timestamp) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('is', $userId, $action);
        $stmt->execute();
    }
}

// Handle request if called directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'User not authenticated'
        ]);
        exit;
    }
    
    $controller = new InventoryItemController();
    $controller->handleRequest();
}
?> 