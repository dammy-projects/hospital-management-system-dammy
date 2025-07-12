<?php
session_start();

// Include database configuration
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit();
}

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    
    <!-- Bulma CSS Framework -->
    <link rel="stylesheet" href="css/bulma.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* Enhanced Color Scheme for Better Readability */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
            --medium-text: #475569;
            --light-text: #64748b;
            --white: #ffffff;
            --border-color: #e2e8f0;
            --hover-bg: #f1f5f9;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(0, 0, 0, 0.25);
        }

        body {
            color: var(--dark-text);
            background-color: var(--light-bg);
        }

        .navbar-item.is-active {
            background-color: var(--primary-color) !important;
            color: var(--white) !important;
            font-weight: 600;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--light-bg);
        }
        
        .main-content {
            min-height: 100vh;
        }
        
        .notification.is-info {
            background-color: var(--info-color);
            color: var(--white);
            border-left: 4px solid var(--primary-color);
        }
        
        .card {
            box-shadow: 0 2px 4px var(--shadow-light);
            border: 1px solid var(--border-color);
            background-color: var(--white);
        }
        
        .card:hover {
            box-shadow: 0 4px 8px var(--shadow-medium);
            transition: box-shadow 0.3s ease;
        }

        /* Simple Navigation Styles - No Hover Effects */
        .navbar-item {
            color: var(--white) !important;
            font-weight: 500;
        }

        .navbar-item:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--white) !important;
        }

        .navbar-link {
            color: var(--white) !important;
            font-weight: 500;
        }

        .navbar-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--white) !important;
        }

        .navbar-dropdown {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px var(--shadow-medium);
        }

        .navbar-dropdown .navbar-item {
            color: var(--dark-text) !important;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-dropdown .navbar-item:hover {
            background-color: var(--hover-bg) !important;
            color: var(--primary-color) !important;
        }

        .navbar-brand .navbar-item {
            color: var(--white) !important;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .navbar-brand .navbar-item:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--white) !important;
        }

        .navbar-brand .navbar-item i {
            color: var(--white);
        }

        /* Simple icon styles */
        .icon {
            margin-right: 0.5rem;
        }

        .navbar-item .icon i {
            color: var(--white);
        }

        .navbar-dropdown .navbar-item .icon i {
            color: var(--medium-text);
        }

        .navbar-dropdown .navbar-item:hover .icon i {
            color: var(--primary-color);
        }

        /* Notification hover effect */
        .notification {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .notification:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow-medium);
        }

        .notification.is-info {
            background: linear-gradient(135deg, var(--info-color), var(--primary-color));
            color: var(--white);
            font-weight: 500;
        }

        /* Button hover effects with better colors */
        .button {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-weight: 500;
            border-radius: 6px;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px var(--shadow-medium);
        }

        .button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .button:hover::before {
            left: 100%;
        }

        /* Enhanced button colors */
        .button.is-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
            color: var(--white);
        }

        .button.is-info:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .button.is-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: var(--white);
        }

        .button.is-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: var(--white);
        }

        .button.is-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: var(--white);
        }

        /* Card hover effects with better colors */
        .card {
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 8px;
            background-color: var(--white);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px var(--shadow-heavy);
        }

        .card-header {
            background-color: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .card-header-title {
            color: var(--dark-text);
            font-weight: 600;
        }

        .card-content {
            color: var(--medium-text);
        }

        /* Table enhancements */
        .table {
            background-color: var(--white);
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--light-bg);
            color: var(--dark-text);
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: var(--hover-bg);
        }

        .table tbody td {
            color: var(--medium-text);
            border-bottom: 1px solid var(--border-color);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Loading animation for page transitions */
        .page-transition {
            opacity: 0;
            animation: fadeIn 0.5s ease-in forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        /* Text color improvements */
        .title {
            color: var(--dark-text);
            font-weight: 700;
        }

        .subtitle {
            color: var(--medium-text);
            font-weight: 500;
        }

        /* Navbar background enhancement */
        .navbar.is-info {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            box-shadow: 0 2px 8px var(--shadow-medium);
        }

        /* Divider styling */
        .navbar-divider {
            background-color: var(--border-color);
            margin: 0.5rem 0;
        }

        /* Simple dropdown display */
        .navbar-dropdown {
            display: none;
        }

        .navbar-dropdown.is-active {
            display: block !important;
        }

        /* User menu styles */
        .navbar-end .navbar-item {
            color: var(--white) !important;
            font-weight: 500;
        }

        .navbar-end .navbar-item:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--white) !important;
        }

        .navbar-end .navbar-dropdown .navbar-item {
            color: var(--dark-text) !important;
        }

        .navbar-end .navbar-dropdown .navbar-item:hover {
            color: var(--primary-color) !important;
            background-color: var(--hover-bg) !important;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar is-info" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="dashboard.php">
                <i class="fas fa-hospital-alt mr-2"></i>
                <strong>HMS</strong>
            </a>

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasic">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarBasic" class="navbar-menu">
            <div class="navbar-start">
                <?php
                $role = $_SESSION['role_name'] ?? '';
                // Helper functions for role checks
                function is_admin($role) {
                    return strtolower($role) === 'admin';
                }
                function is_doctor($role) {
                    return strtolower($role) === 'doctor';
                }
                function is_nurse($role) {
                    return strtolower($role) === 'nurse';
                }
                function is_receptionist($role) {
                    return strtolower($role) === 'receptionist';
                }
                // For nurse or receptionist
                function is_nurse_or_receptionist($role) {
                    $r = strtolower($role);
                    return $r === 'nurse' || $r === 'receptionist';
                }
                ?>
                <a class="navbar-item <?php echo $current_page === 'dashboard' ? 'is-active' : ''; ?>" href="dashboard.php">
                    <span class="icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
                <?php if (is_admin($role) || is_doctor($role) || is_nurse_or_receptionist($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-users"></i>
                        </span>
                        <span>Patients</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="patients.php">
                            <span class="icon">
                                <i class="fas fa-list"></i>
                            </span>
                            <span>Manage Patient</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role) || is_doctor($role) || is_nurse($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-notes-medical"></i>
                        </span>
                        <span>Medical Records</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item <?php echo $current_page === 'medical_records' ? 'is-active' : ''; ?>" href="medical_records.php">
                            <span class="icon">
                                <i class="fas fa-notes-medical"></i>
                            </span>
                            <span>Manage Medical Records</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-user-md"></i>
                        </span>
                        <span>Doctors</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="doctors.php">
                            <span class="icon">
                                <i class="fas fa-user-md"></i>
                            </span>
                            <span>Manage Doctors</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role) || is_doctor($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-pills"></i>
                        </span>
                        <span>Medicines</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item <?php echo $current_page === 'medicines' ? 'is-active' : ''; ?>" href="medicines.php">
                            <span class="icon">
                                <i class="fas fa-pills"></i>
                            </span>
                            <span>Manage Medicines</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role) || is_doctor($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </span>
                        <span>Prescriptions</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item <?php echo $current_page === 'prescriptions' ? 'is-active' : ''; ?>" href="prescriptions.php">
                            <span class="icon">
                                <i class="fas fa-prescription-bottle-alt"></i>
                            </span>
                            <span>Manage Prescriptions</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role) || is_doctor($role) || is_nurse_or_receptionist($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <span>Appointments</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="appointments.php">
                            <span class="icon">
                                <i class="fas fa-list"></i>
                            </span>
                            <span>Manage Appointments</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <span>Billing</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="billing.php">
                            <span class="icon">
                                <i class="fas fa-file-invoice"></i>
                            </span>
                            <span>Manage Bills</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <span>Insurance</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="patient_insurance.php">
                            <span class="icon">
                                <i class="fas fa-user-shield"></i>
                            </span>
                            <span>Patient Insurance</span>
                        </a>
                        <a class="navbar-item" href="insurance_providers.php">
                            <span class="icon">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <span>Insurance Providers</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role) || is_nurse_or_receptionist($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-boxes"></i>
                        </span>
                        <span>Inventory</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item <?php echo $current_page === 'inventory' ? 'is-active' : ''; ?>" href="inventory.php">
                            <span class="icon">
                                <i class="fas fa-box"></i>
                            </span>
                            <span>Manage Inventory</span>
                        </a>
                        <a class="navbar-item <?php echo $current_page === 'inventory_categories' ? 'is-active' : ''; ?>" href="inventory_categories.php">
                            <span class="icon">
                                <i class="fas fa-tags"></i>
                            </span>
                            <span>Categories</span>
                        </a>
                        <a class="navbar-item <?php echo $current_page === 'inventory_withdrawals' ? 'is-active' : ''; ?>" href="inventory_withdrawals.php">
                            <span class="icon">
                                <i class="fas fa-box-open"></i>
                            </span>
                            <span>Withdrawals</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (is_admin($role) || is_nurse_or_receptionist($role) || is_doctor($role)): ?>
                <a class="navbar-item <?php echo $current_page === 'reports' ? 'is-active' : ''; ?>" href="reports.php">
                    <span class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    <span>Reports</span>
                </a>
                <?php endif; ?>
                <?php if (is_admin($role)): ?>
                <div class="navbar-item has-dropdown">
                    <a class="navbar-link">
                        <span class="icon">
                            <i class="fas fa-cog"></i>
                        </span>
                        <span>Settings</span>
                    </a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" href="users.php">
                            <span class="icon">
                                <i class="fas fa-users-cog"></i>
                            </span>
                            <span>User Management</span>
                        </a>
                        <a class="navbar-item" href="departments.php">
                            <span class="icon">
                                <i class="fas fa-building"></i>
                            </span>
                            <span>Departments</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <div class="navbar-item has-dropdown">
                            <a class="navbar-link">
                                <span class="icon">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                                <span><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></span>
                            </a>
                            <div class="navbar-dropdown is-right">
                                <a class="navbar-item" href="profile.php">
                                    <span class="icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <span>Profile</span>
                                </a>
                                <a class="navbar-item" href="change_password.php">
                                    <span class="icon">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <span>Change Password</span>
                                </a>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="logout.php">
                                    <span class="icon">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </span>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="notification is-<?php echo $_SESSION['message_type'] ?? 'info'; ?> is-light mt-4">
                <button class="delete" onclick="this.parentElement.remove();"></button>
                <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

    <!-- JavaScript for navbar burger menu -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM loaded, initializing dropdowns...');
            
            // Get all "navbar-burger" elements
            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

            // Add a click event on each of them
            $navbarBurgers.forEach(el => {
                el.addEventListener('click', () => {
                    // Get the target from the "data-target" attribute
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);

                    // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                });
            });

            // Handle dropdown toggles
            const dropdowns = document.querySelectorAll('.has-dropdown');
            console.log('Found dropdowns:', dropdowns.length);
            
            dropdowns.forEach((dropdown, index) => {
                const dropdownMenu = dropdown.querySelector('.navbar-dropdown');
                const dropdownLink = dropdown.querySelector('.navbar-link');
                
                console.log(`Setting up dropdown ${index + 1}:`, dropdownLink.textContent.trim());
                
                // Toggle dropdown on click
                dropdownLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Dropdown clicked:', dropdownLink.textContent.trim());
                    
                    // Close all other dropdowns
                    dropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.classList.remove('is-active');
                            const otherMenu = otherDropdown.querySelector('.navbar-dropdown');
                            if (otherMenu) {
                                otherMenu.classList.remove('is-active');
                            }
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdown.classList.toggle('is-active');
                    dropdownMenu.classList.toggle('is-active');
                    
                    console.log('Dropdown toggled:', dropdown.classList.contains('is-active'));
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.has-dropdown')) {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('is-active');
                        const dropdownMenu = dropdown.querySelector('.navbar-dropdown');
                        if (dropdownMenu) {
                            dropdownMenu.classList.remove('is-active');
                        }
                    });
                }
            });

            // Close dropdowns on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('is-active');
                        const dropdownMenu = dropdown.querySelector('.navbar-dropdown');
                        if (dropdownMenu) {
                            dropdownMenu.classList.remove('is-active');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 