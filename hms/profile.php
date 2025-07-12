<?php
require_once 'includes/header.php';

// Get user information
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT u.*, r.role_name 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.role_id 
             WHERE u.user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Check if this is a password-only update
    $password_only = !empty($new_password) && empty($full_name) && empty($phone_number) && empty($address);
    
    // Validation - only validate profile fields if they're being updated
    if (!$password_only) {
        if (empty($full_name)) {
            $errors[] = "Full name is required";
        }
        
        if (empty($phone_number)) {
            $errors[] = "Phone number is required";
        }
        
        if (empty($address)) {
            $errors[] = "Address is required";
        }
    }
    
    // Password change validation
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New password and confirm password do not match";
        }
    }
    
    // If no errors, update user information
    if (empty($errors)) {
        if ($password_only) {
            // Only update password
            $update_sql = "UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
        } else {
            // Update profile information (with optional password)
            $update_sql = "UPDATE users SET full_name = ?, phone_number = ?, address = ?, updated_at = CURRENT_TIMESTAMP";
            $params = [$full_name, $phone_number, $address];
            $types = "sss";
            
            // Add password to update if provided
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql .= ", password = ?";
                $params[] = $hashed_password;
                $types .= "s";
            }
            
            $update_sql .= " WHERE user_id = ?";
            $params[] = $user_id;
            $types .= "i";
            
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param($types, ...$params);
        }
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = $password_only ? "Password changed successfully!" : "Profile updated successfully!";
            $_SESSION['message_type'] = "success";
            
            // Update session data only if profile was updated
            if (!$password_only) {
                $_SESSION['full_name'] = $full_name;
            }
            
            // Log the action
            $log_action = $password_only ? "Password changed" : "Profile updated";
            $log_sql = "INSERT INTO system_logs (user_id, action) VALUES (?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("is", $user_id, $log_action);
            $log_stmt->execute();
            
            // Redirect to refresh the page
            header("Location: profile.php");
            exit();
        } else {
            $errors[] = "Error updating profile. Please try again.";
        }
    }
}
?>

<div class="container">
    <section class="section">
        <div class="columns">
            <div class="column">
                <h1 class="title">
                    <span class="icon">
                        <i class="fas fa-user-circle"></i>
                    </span>
                    My Profile
                </h1>
                <p class="subtitle">
                    Manage your account information and settings
                </p>
            </div>
        </div>

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

        <div class="columns">
            <!-- Profile Information -->
            <div class="column is-8">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-edit"></i>
                            </span>
                            Edit Profile Information
                        </p>
                    </header>
                    <div class="card-content">
                        <form method="POST" action="">
                            <div class="columns is-multiline">
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Full Name</label>
                                        <div class="control">
                                            <input class="input" type="text" name="full_name" 
                                                   value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Username</label>
                                        <div class="control">
                                            <input class="input" type="text" 
                                                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" 
                                                   readonly disabled>
                                        </div>
                                        <p class="help">Username cannot be changed</p>
                                    </div>
                                </div>
                                
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Phone Number</label>
                                        <div class="control">
                                            <input class="input" type="tel" name="phone_number" 
                                                   value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-12">
                                    <div class="field">
                                        <label class="label">Address</label>
                                        <div class="control">
                                            <textarea class="textarea" name="address" rows="3" 
                                                      required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Role</label>
                                        <div class="control">
                                            <input class="input" type="text" 
                                                   value="<?php echo htmlspecialchars(ucfirst($user['role_name'] ?? '')); ?>" 
                                                   readonly disabled>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-6">
                                    <div class="field">
                                        <label class="label">Status</label>
                                        <div class="control">
                                            <span class="tag is-<?php echo $user['status'] === 'active' ? 'success' : ($user['status'] === 'inactive' ? 'warning' : 'danger'); ?>">
                                                <?php echo htmlspecialchars(ucfirst($user['status'] ?? '')); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span>Update Profile</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="column is-4">
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
                        <form method="POST" action="">
                            <div class="field">
                                <label class="label">Current Password</label>
                                <div class="control">
                                    <input class="input" type="password" name="current_password" 
                                           placeholder="Enter current password">
                                </div>
                            </div>
                            
                            <div class="field">
                                <label class="label">New Password</label>
                                <div class="control">
                                    <input class="input" type="password" name="new_password" 
                                           placeholder="Enter new password">
                                </div>
                                <p class="help">Minimum 6 characters</p>
                            </div>
                            
                            <div class="field">
                                <label class="label">Confirm New Password</label>
                                <div class="control">
                                    <input class="input" type="password" name="confirm_password" 
                                           placeholder="Confirm new password">
                                </div>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-info is-fullwidth">
                                        <span class="icon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <span>Change Password</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="card mt-4">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            Account Information
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><strong>Member Since:</strong><br>
                            <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                            
                            <p><strong>Last Updated:</strong><br>
                            <?php echo date('F j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                            
                            <p><strong>Account ID:</strong><br>
                            #<?php echo str_pad($user['user_id'], 6, '0', STR_PAD_LEFT); ?></p>
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