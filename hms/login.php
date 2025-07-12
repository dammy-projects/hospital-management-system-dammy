<?php
session_start();
require_once 'config/config.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        $sql = "SELECT u.*, r.role_name FROM users u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                WHERE u.username = ? AND u.status = 'active'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $user['role_name'];
                
                // Log the login
                $log_sql = "INSERT INTO system_logs (user_id, action) VALUES (?, 'User logged in')";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("i", $user['user_id']);
                $log_stmt->execute();
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMS - Login</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .field:not(:last-child) {
            margin-bottom: 1.5rem;
        }
        .button.is-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
            height: 3rem;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .button.is-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.125em rgba(102, 126, 234, 0.25);
        }
        .notification.is-danger {
            background-color: #feecf0;
            color: #cc0f35;
            border: 1px solid #f14668;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="title is-3 has-text-white">
                <i class="fas fa-hospital"></i> HMS
            </h1>
            <p class="subtitle is-6 has-text-white">Hospital Management System</p>
        </div>
        
        <div class="login-body">
            <?php if (!empty($error_message)): ?>
                <div class="notification is-danger is-light">
                    <button class="delete"></button>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="username" placeholder="Enter your username" required>
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>
                
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control has-icons-left">
                        <input class="input" type="password" name="password" placeholder="Enter your password" required>
                        <span class="icon is-small is-left">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                </div>
                
                <div class="field">
                    <div class="control">
                        <button type="submit" class="button is-primary">
                            <span class="icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                            <span>Login</span>
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="has-text-centered mt-4">
                <p class="has-text-grey-light">
                    <i class="fas fa-shield-alt"></i> Secure Login
                </p>
            </div>
        </div>
    </div>

    <script>
        // Close notification when delete button is clicked
        document.addEventListener('DOMContentLoaded', function() {
            var deleteButtons = document.querySelectorAll('.delete');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    this.parentNode.remove();
                });
            });
        });
    </script>
</body>
</html> 