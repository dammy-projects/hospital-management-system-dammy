<?php
require_once 'includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($current_password)) {
        $errors[] = "Current password is required";
    }
    
    if (empty($new_password)) {
        $errors[] = "New password is required";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters long";
    }
    
    if (empty($confirm_password)) {
        $errors[] = "Confirm password is required";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New password and confirm password do not match";
    }
    
    // Verify current password
    if (empty($errors)) {
        $user_id = $_SESSION['user_id'];
        $user_sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        }
    }
    
    // Update password if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            $success = true;
            
            // Log the action
            $log_sql = "INSERT INTO system_logs (user_id, action) VALUES (?, 'Password changed')";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("i", $user_id);
            $log_stmt->execute();
            
            $_SESSION['message'] = "Password changed successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $errors[] = "Error changing password. Please try again.";
        }
    }
}
?>

<div class="container">
    <section class="section">
        <div class="columns is-centered">
            <div class="column is-6">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-key"></i>
                            </span>
                            Change Password
                        </p>
                    </header>
                    <div class="card-content">
                        <?php if ($success): ?>
                            <div class="notification is-success is-light">
                                <span class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <span>Password changed successfully! You can now use your new password to log in.</span>
                            </div>
                            <div class="field">
                                <div class="control">
                                    <a href="profile.php" class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span>Back to Profile</span>
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="notification is-danger is-light">
                                    <button class="delete" onclick="this.parentElement.remove();"></button>
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="field">
                                    <label class="label">Current Password</label>
                                    <div class="control">
                                        <input class="input" type="password" name="current_password" 
                                               placeholder="Enter your current password" required>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <label class="label">New Password</label>
                                    <div class="control">
                                        <input class="input" type="password" name="new_password" 
                                               placeholder="Enter your new password" required>
                                    </div>
                                    <p class="help">Password must be at least 6 characters long</p>
                                </div>
                                
                                <div class="field">
                                    <label class="label">Confirm New Password</label>
                                    <div class="control">
                                        <input class="input" type="password" name="confirm_password" 
                                               placeholder="Confirm your new password" required>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <div class="control">
                                        <button type="submit" class="button is-primary is-fullwidth">
                                            <span class="icon">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <span>Change Password</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="field">
                                    <div class="control">
                                        <a href="profile.php" class="button is-light is-fullwidth">
                                            <span class="icon">
                                                <i class="fas fa-arrow-left"></i>
                                            </span>
                                            <span>Cancel</span>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Password Security Tips -->
                <div class="card mt-4">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            Password Security Tips
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <ul>
                                <li>Use at least 8 characters for better security</li>
                                <li>Include a mix of uppercase and lowercase letters</li>
                                <li>Add numbers and special characters</li>
                                <li>Avoid using personal information (birthday, name, etc.)</li>
                                <li>Don't reuse passwords from other accounts</li>
                                <li>Consider using a password manager</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

    </div> <!-- End of container from header.php -->
</body>
</html> 