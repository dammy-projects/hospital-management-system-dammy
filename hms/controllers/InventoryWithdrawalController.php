<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/InventoryWithdrawal.php';

class InventoryWithdrawalController {
    private $withdrawal;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->withdrawal = new InventoryWithdrawal($conn);
    }
    
    /**
     * Handle incoming requests
     */
    public function handleRequest() {
        header('Content-Type: application/json');
        
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $action = $_GET['action'] ?? '';
            
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
            http_response_code(400);
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
                $this->getWithdrawals();
                break;
            case 'get':
                $this->getWithdrawal();
                break;
            case 'users':
                $this->getUsers();
                break;
            case 'inventory-items':
                $this->getInventoryItems();
                break;
            case 'stats':
                $this->getStats();
                break;
            case 'check-stock':
                $this->checkStockAvailability();
                break;
            case 'debug-withdrawal':
                $this->debugWithdrawal();
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
                $this->createWithdrawal();
                break;
            case 'complete':
                $this->completeWithdrawal();
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
                $this->updateWithdrawal();
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
                $this->deleteWithdrawal();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get withdrawals with pagination and search
     */
    private function getWithdrawals() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        $user_filter = $_GET['user_filter'] ?? '';
        
        $withdrawals = $this->withdrawal->getWithdrawals($page, $limit, $search, $status_filter, $user_filter);
        $total = $this->withdrawal->getTotalWithdrawals($search, $status_filter, $user_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $withdrawals,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single withdrawal with items
     */
    private function getWithdrawal() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Withdrawal ID is required');
        }
        
        $withdrawal = $this->withdrawal->getWithdrawalById($id);
        if (!$withdrawal) {
            throw new Exception('Withdrawal not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $withdrawal
        ]);
    }
    
    /**
     * Create new withdrawal
     */
    private function createWithdrawal() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($input['withdrawal_date'])) {
            throw new Exception('Withdrawal date is required');
        }
        
        // Force status to be 'completed' - auto-complete all withdrawals
        $input['status'] = 'completed';
        
        if (empty($input['items']) || !is_array($input['items'])) {
            throw new Exception('At least one withdrawal item is required');
        }
        
        // Validate withdrawal items
        foreach ($input['items'] as $index => $item) {
            if (empty($item['item_id'])) {
                throw new Exception("Item is required for entry " . ($index + 1));
            }
            if (empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                throw new Exception("Valid quantity is required for entry " . ($index + 1));
            }
        }
        
        // Set performed_by to current user
        $input['performed_by'] = $_SESSION['user_id'] ?? null;
        
        // Check stock availability before creating
        error_log("Checking stock availability before withdrawal creation...");
        foreach ($input['items'] as $item) {
            $sql = "SELECT item_name, quantity_in_stock FROM inventory_items WHERE item_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $item['item_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentItem = $result->fetch_assoc();
            
            if (!$currentItem) {
                throw new Exception("Item not found for ID: {$item['item_id']}");
            }
            
            if ($currentItem['quantity_in_stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for {$currentItem['item_name']}. Available: {$currentItem['quantity_in_stock']}, Required: {$item['quantity']}");
            }
        }
        
        try {
            $this->conn->begin_transaction();
            
            // Create withdrawal
            $withdrawalId = $this->withdrawal->createWithdrawal($input);
            error_log("Withdrawal created with ID: {$withdrawalId}");
            
            // Immediately update inventory stock since status is 'completed'
            error_log("Immediately updating inventory stock for withdrawal ID: {$withdrawalId}");
            $stockUpdates = $this->withdrawal->updateInventoryStock($withdrawalId);
            error_log("Stock updates completed: " . json_encode($stockUpdates));
            
            $this->conn->commit();
            
            // Create detailed message about stock updates
            $updateDetails = "Withdrawal created and inventory updated successfully. Stock updates:\n";
            foreach ($stockUpdates as $update) {
                $updateDetails .= "- {$update['item_name']}: {$update['old_stock']} → {$update['new_stock']} (withdrew {$update['withdrawn']})\n";
            }
            
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Created and completed withdrawal ID: {$withdrawalId} - inventory updated");
            
            echo json_encode([
                'success' => true,
                'message' => 'Withdrawal created and inventory updated successfully',
                'withdrawal_id' => $withdrawalId,
                'stock_updates' => $stockUpdates,
                'details' => $updateDetails
            ]);
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error creating withdrawal: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update withdrawal
     */
    private function updateWithdrawal() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['withdrawal_id']);
        
        if (!$id) {
            throw new Exception('Withdrawal ID is required');
        }
        
        // Validate required fields
        if (empty($input['withdrawal_date'])) {
            throw new Exception('Withdrawal date is required');
        }
        
        if (empty($input['status'])) {
            throw new Exception('Status is required');
        }
        
        if (empty($input['items']) || !is_array($input['items'])) {
            throw new Exception('At least one withdrawal item is required');
        }
        
        // Validate withdrawal items
        foreach ($input['items'] as $index => $item) {
            if (empty($item['item_id'])) {
                throw new Exception("Item is required for entry " . ($index + 1));
            }
            if (empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                throw new Exception("Valid quantity is required for entry " . ($index + 1));
            }
        }
        
        // Set performed_by to current user
        $input['performed_by'] = $_SESSION['user_id'] ?? null;
        
        $success = $this->withdrawal->updateWithdrawal($id, $input);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Updated withdrawal ID: {$id}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Withdrawal updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update withdrawal');
        }
    }
    
    /**
     * Delete withdrawal
     */
    private function deleteWithdrawal() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Withdrawal ID is required');
        }
        
        // Get withdrawal info before deletion for logging
        $withdrawalInfo = $this->withdrawal->getWithdrawalById($id);
        if (!$withdrawalInfo) {
            throw new Exception('Withdrawal not found');
        }
        
        $success = $this->withdrawal->deleteWithdrawal($id);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Deleted withdrawal ID: {$id}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Withdrawal deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete withdrawal');
        }
    }
    
    /**
     * Complete withdrawal and update inventory
     */
    private function completeWithdrawal() {
        error_log("=== WITHDRAWAL COMPLETION STARTED ===");
        
        $input = json_decode(file_get_contents('php://input'), true);
        error_log("Input received: " . json_encode($input));
        
        $id = intval($input['withdrawal_id']);
        
        if (!$id) {
            error_log("ERROR: No withdrawal ID provided");
            throw new Exception('Withdrawal ID is required');
        }
        
        error_log("Processing withdrawal completion for ID: {$id}");
        
        // Check if withdrawal exists and is in correct status
        $withdrawal = $this->withdrawal->getWithdrawalById($id);
        if (!$withdrawal) {
            error_log("ERROR: Withdrawal not found for ID: {$id}");
            throw new Exception('Withdrawal not found');
        }
        
        error_log("Withdrawal found. Current status: {$withdrawal['status']}");
        
        if ($withdrawal['status'] !== 'approved') {
            error_log("ERROR: Invalid status for completion. Expected 'approved', got '{$withdrawal['status']}'");
            throw new Exception('Only approved withdrawals can be completed. Current status: ' . $withdrawal['status']);
        }
        
        // Check stock availability before completing
        error_log("Checking stock availability...");
        $stockIssues = $this->withdrawal->checkStockAvailability($id);
        if (!empty($stockIssues)) {
            error_log("STOCK ISSUES FOUND: " . json_encode($stockIssues));
            $message = "Stock availability issues:\n";
            foreach ($stockIssues as $issue) {
                $message .= "- {$issue['item_name']}: Requested {$issue['requested']}, Available {$issue['available']}\n";
            }
            throw new Exception($message);
        }
        
        error_log("Stock availability check passed");
        
        try {
            error_log("Starting database transaction...");
            $this->conn->begin_transaction();
            
            // Log before completion
            error_log("Starting withdrawal completion for ID: {$id}");
            
            // Update withdrawal status to completed
            $updateData = [
                'withdrawal_date' => $withdrawal['withdrawal_date'],
                'notes' => $withdrawal['notes'],
                'performed_by' => $withdrawal['performed_by'],
                'status' => 'completed',
                'items' => $withdrawal['items']
            ];
            
            error_log("Updating withdrawal status to completed...");
            $updateResult = $this->withdrawal->updateWithdrawal($id, $updateData);
            if (!$updateResult) {
                throw new Exception("Failed to update withdrawal status");
            }
            error_log("Withdrawal status updated to completed for ID: {$id}");
            
            // Update inventory stock
            error_log("Starting inventory stock update...");
            $stockUpdates = $this->withdrawal->updateInventoryStock($id);
            error_log("Stock updates completed for withdrawal ID: {$id}. Updates: " . json_encode($stockUpdates));
            
            error_log("Committing transaction...");
            $this->conn->commit();
            error_log("Transaction committed successfully");
            
            // Create detailed message about stock updates
            $updateDetails = "Withdrawal completed successfully. Stock updates:\n";
            foreach ($stockUpdates as $update) {
                $updateDetails .= "- {$update['item_name']}: {$update['old_stock']} → {$update['new_stock']} (withdrew {$update['withdrawn']})\n";
            }
            
            // Log the action
            $this->logAction($_SESSION['user_id'] ?? null, "Completed withdrawal ID: {$id} and updated inventory stock");
            
            error_log("=== WITHDRAWAL COMPLETION SUCCESS ===");
            
            echo json_encode([
                'success' => true,
                'message' => 'Withdrawal completed and inventory updated successfully',
                'stock_updates' => $stockUpdates,
                'details' => $updateDetails
            ]);
            
        } catch (Exception $e) {
            error_log("TRANSACTION ERROR: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->conn->rollback();
            error_log("Transaction rolled back");
            error_log("Error completing withdrawal ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check stock availability for a withdrawal
     */
    private function checkStockAvailability() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Withdrawal ID is required');
        }
        
        $stockIssues = $this->withdrawal->checkStockAvailability($id);
        
        echo json_encode([
            'success' => true,
            'stock_available' => empty($stockIssues),
            'issues' => $stockIssues
        ]);
    }
    
    /**
     * Get all users
     */
    private function getUsers() {
        $users = $this->withdrawal->getUsers();
        echo json_encode([
            'success' => true,
            'data' => $users
        ]);
    }
    
    /**
     * Get all inventory items
     */
    private function getInventoryItems() {
        $items = $this->withdrawal->getInventoryItems();
        echo json_encode([
            'success' => true,
            'data' => $items
        ]);
    }
    
    /**
     * Get withdrawal statistics
     */
    private function getStats() {
        $stats = $this->withdrawal->getWithdrawalStats();
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Debug withdrawal details and stock levels
     */
    private function debugWithdrawal() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Withdrawal ID is required');
        }
        
        // Get withdrawal details
        $withdrawal = $this->withdrawal->getWithdrawalById($id);
        if (!$withdrawal) {
            throw new Exception('Withdrawal not found');
        }
        
        // Get current stock levels for each item
        $itemsWithStock = [];
        foreach ($withdrawal['items'] as $item) {
            $sql = "SELECT i.item_id, i.item_name, i.quantity_in_stock, i.unit, 
                           c.category_name, i.last_updated, i.status
                    FROM inventory_items i
                    LEFT JOIN inventory_categories c ON i.category_id = c.category_id
                    WHERE i.item_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $item['item_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentItem = $result->fetch_assoc();
            
            $itemsWithStock[] = [
                'withdrawal_item' => $item,
                'current_stock' => $currentItem,
                'stock_sufficient' => ($currentItem['quantity_in_stock'] >= $item['quantity']),
                'stock_after_withdrawal' => ($currentItem['quantity_in_stock'] - $item['quantity'])
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'withdrawal' => $withdrawal,
                'items_analysis' => $itemsWithStock,
                'can_complete' => $withdrawal['status'] === 'approved',
                'debug_info' => [
                    'current_status' => $withdrawal['status'],
                    'total_items' => count($withdrawal['items']),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]
        ]);
    }
    
    /**
     * Log system action
     */
    private function logAction($userId, $action) {
        try {
            $sql = "INSERT INTO system_logs (user_id, action) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('is', $userId, $action);
            $stmt->execute();
        } catch (Exception $e) {
            // Log errors silently, don't break the main operation
            error_log("Failed to log action: " . $e->getMessage());
        }
    }
}

// Handle the request
$controller = new InventoryWithdrawalController();
$controller->handleRequest();
?> 