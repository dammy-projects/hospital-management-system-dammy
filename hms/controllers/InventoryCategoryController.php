<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/InventoryCategory.php';

class InventoryCategoryController {
    private $inventoryCategory;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->inventoryCategory = new InventoryCategory($conn);
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
                $this->getCategories();
                break;
            case 'get':
                $this->getCategory();
                break;
            case 'stats':
                $this->getStats();
                break;
            case 'item-counts':
                $this->getCategoryItemCounts();
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
                $this->createCategory();
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
                $this->updateCategory();
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
                $this->deleteCategory();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get categories with pagination and search
     */
    private function getCategories() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'category_name';
        
        $categories = $this->inventoryCategory->getCategories($page, $limit, $search, $sort);
        $total = $this->inventoryCategory->getTotalCategories($search);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $categories,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single category
     */
    private function getCategory() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Category ID is required');
        }
        
        $category = $this->inventoryCategory->getCategoryById($id);
        if (!$category) {
            throw new Exception('Category not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $category
        ]);
    }
    
    /**
     * Create new category
     */
    private function createCategory() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($input['category_name'])) {
            throw new Exception("Category name is required");
        }
        
        // Validate category name
        $categoryName = trim($input['category_name']);
        if (strlen($categoryName) < 2) {
            throw new Exception('Category name must be at least 2 characters long');
        }
        
        if (strlen($categoryName) > 100) {
            throw new Exception('Category name must not exceed 100 characters');
        }
        
        // Check if category name already exists
        if ($this->inventoryCategory->categoryNameExists($categoryName)) {
            throw new Exception('Category name already exists');
        }
        
        $categoryData = [
            'category_name' => $categoryName
        ];
        
        $categoryId = $this->inventoryCategory->createCategory($categoryData);
        if ($categoryId) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Created new inventory category: {$categoryName}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => ['category_id' => $categoryId]
            ]);
        } else {
            throw new Exception('Failed to create category');
        }
    }
    
    /**
     * Update category
     */
    private function updateCategory() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('Category ID is required');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($input['category_name'])) {
            throw new Exception("Category name is required");
        }
        
        // Validate category name
        $categoryName = trim($input['category_name']);
        if (strlen($categoryName) < 2) {
            throw new Exception('Category name must be at least 2 characters long');
        }
        
        if (strlen($categoryName) > 100) {
            throw new Exception('Category name must not exceed 100 characters');
        }
        
        // Check if category exists
        $existingCategory = $this->inventoryCategory->getCategoryById($id);
        if (!$existingCategory) {
            throw new Exception('Category not found');
        }
        
        // Check if category name already exists (excluding current category)
        if ($this->inventoryCategory->categoryNameExists($categoryName, $id)) {
            throw new Exception('Category name already exists');
        }
        
        $categoryData = [
            'category_name' => $categoryName
        ];
        
        $success = $this->inventoryCategory->updateCategory($id, $categoryData);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Updated inventory category: {$existingCategory['category_name']} to {$categoryName}");
            
            echo json_encode([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update category');
        }
    }
    
    /**
     * Delete category
     */
    private function deleteCategory() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('Category ID is required');
        }
        
        // Get category info before deletion for logging
        $categoryInfo = $this->inventoryCategory->getCategoryById($id);
        if (!$categoryInfo) {
            throw new Exception('Category not found');
        }
        
        try {
            $success = $this->inventoryCategory->deleteCategory($id);
            if ($success) {
                // Log the action
                $this->logAction($_SESSION['user_id'], "Deleted inventory category: {$categoryInfo['category_name']}");
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Category deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete category');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Get category statistics
     */
    private function getStats() {
        $stats = $this->inventoryCategory->getCategoryStats();
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Get category item counts
     */
    private function getCategoryItemCounts() {
        $categoryCounts = $this->inventoryCategory->getCategoryItemCounts();
        echo json_encode([
            'success' => true,
            'data' => $categoryCounts
        ]);
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
    
    $controller = new InventoryCategoryController();
    $controller->handleRequest();
}
?> 