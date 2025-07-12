<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $user;
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->user = new User($conn);
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
                $this->getUsers();
                break;
            case 'get':
                $this->getUser();
                break;
            case 'roles':
                $this->getRoles();
                break;
            case 'stats':
                $this->getStats();
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
                $this->createUser();
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
                $this->updateUser();
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
                $this->deleteUser();
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
    
    /**
     * Get users with pagination and search
     */
    private function getUsers() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 6);
        $search = $_GET['search'] ?? '';
        $role_filter = $_GET['role_filter'] ?? '';
        $status_filter = $_GET['status_filter'] ?? '';
        
        $users = $this->user->getUsers($page, $limit, $search, $role_filter, $status_filter);
        $total = $this->user->getTotalUsers($search, $role_filter, $status_filter);
        $totalPages = ceil($total / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]);
    }
    
    /**
     * Get single user
     */
    private function getUser() {
        $id = intval($_GET['id']);
        if (!$id) {
            throw new Exception('User ID is required');
        }
        
        $user = $this->user->getUserById($id);
        if (!$user) {
            throw new Exception('User not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    }
    
    /**
     * Create new user
     */
    private function createUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['full_name', 'username', 'password', 'role_id', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Check if username already exists
        if ($this->user->usernameExists($input['username'])) {
            throw new Exception('Username already exists');
        }
        
        // Validate password strength
        if (strlen($input['password']) < 6) {
            throw new Exception('Password must be at least 6 characters long');
        }
        
        $userId = $this->user->createUser($input);
        if ($userId) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Created new user: {$input['username']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'data' => ['user_id' => $userId]
            ]);
        } else {
            throw new Exception('Failed to create user');
        }
    }
    
    /**
     * Update user
     */
    private function updateUser() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('User ID is required');
        }
        
        // Validate required fields
        $required = ['full_name', 'username', 'role_id', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Check if username already exists (excluding current user)
        if ($this->user->usernameExists($input['username'], $id)) {
            throw new Exception('Username already exists');
        }
        
        // Validate password if provided
        if (!empty($input['password']) && strlen($input['password']) < 6) {
            throw new Exception('Password must be at least 6 characters long');
        }
        
        $success = $this->user->updateUser($id, $input);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Updated user: {$input['username']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update user');
        }
    }
    
    /**
     * Delete user
     */
    private function deleteUser() {
        $id = intval($_GET['id']);
        
        if (!$id) {
            throw new Exception('User ID is required');
        }
        
        // Prevent self-deletion
        if ($id == $_SESSION['user_id']) {
            throw new Exception('You cannot delete your own account');
        }
        
        // Get user info before deletion for logging
        $userInfo = $this->user->getUserById($id);
        if (!$userInfo) {
            throw new Exception('User not found');
        }
        
        $success = $this->user->deleteUser($id);
        if ($success) {
            // Log the action
            $this->logAction($_SESSION['user_id'], "Deleted user: {$userInfo['username']}");
            
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete user');
        }
    }
    
    /**
     * Get all roles
     */
    private function getRoles() {
        $roles = $this->user->getRoles();
        echo json_encode([
            'success' => true,
            'data' => $roles
        ]);
    }
    
    /**
     * Get user statistics
     */
    private function getStats() {
        $stats = $this->user->getUserStats();
        echo json_encode([
            'success' => true,
            'data' => $stats
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
if (basename($_SERVER['PHP_SELF']) === 'UserController.php') {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized'
        ]);
        exit;
    }
    
    $controller = new UserController();
    $controller->handleRequest();
}
?> 