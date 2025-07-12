<?php
require_once 'includes/header.php';

// Get basic statistics
$stats = [];

// Count patients
$patient_sql = "SELECT COUNT(*) as count FROM patients WHERE status = 'active'";
$patient_result = $conn->query($patient_sql);
$stats['patients'] = $patient_result->fetch_assoc()['count'];

// Count doctors
$doctor_sql = "SELECT COUNT(*) as count FROM doctors WHERE status = 'active'";
$doctor_result = $conn->query($doctor_sql);
$stats['doctors'] = $doctor_result->fetch_assoc()['count'];

// Count today's appointments
$appointment_sql = "SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = CURDATE()";
$appointment_result = $conn->query($appointment_sql);
$stats['appointments_today'] = $appointment_result->fetch_assoc()['count'];

// Count pending appointments
$pending_sql = "SELECT COUNT(*) as count FROM appointments WHERE status = 'scheduled'";
$pending_result = $conn->query($pending_sql);
$stats['pending_appointments'] = $pending_result->fetch_assoc()['count'];
?>

<div class="container">
    <section class="section">
        <div class="columns">
            <div class="column">
                <h1 class="title">
                    Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!
                </h1>
                <p class="subtitle">
                    Hospital Management System Dashboard
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="columns is-multiline">
            <div class="column is-3">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large has-text-info">
                                    <i class="fas fa-user-injured fa-2x"></i>
                                </span>
                            </div>
                            <div class="media-content">
                                <p class="title is-4"><?php echo $stats['patients']; ?></p>
                                <p class="subtitle is-6">Active Patients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column is-3">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large has-text-success">
                                    <i class="fas fa-user-md fa-2x"></i>
                                </span>
                            </div>
                            <div class="media-content">
                                <p class="title is-4"><?php echo $stats['doctors']; ?></p>
                                <p class="subtitle is-6">Active Doctors</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column is-3">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large has-text-warning">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </span>
                            </div>
                            <div class="media-content">
                                <p class="title is-4"><?php echo $stats['appointments_today']; ?></p>
                                <p class="subtitle is-6">Today's Appointments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column is-3">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <span class="icon is-large has-text-danger">
                                    <i class="fas fa-clock fa-2x"></i>
                                </span>
                            </div>
                            <div class="media-content">
                                <p class="title is-4"><?php echo $stats['pending_appointments']; ?></p>
                                <p class="subtitle is-6">Pending Appointments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="columns">
            <div class="column">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            Quick Actions
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="columns is-multiline">
                            <div class="column is-3">
                                <a href="patients.php" class="button is-info is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-user-plus"></i>
                                    </span>
                                    <span>Add Patient</span>
                                </a>
                            </div>
                            <div class="column is-3">
                                <a href="appointments.php" class="button is-success is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </span>
                                    <span>New Appointment</span>
                                </a>
                            </div>
                            <div class="column is-3">
                                <a href="prescriptions.php" class="button is-warning is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-prescription-bottle-alt"></i>
                                    </span>
                                    <span>Prescriptions</span>
                                </a>
                            </div>
                            <div class="column is-3">
                                <a href="billing.php" class="button is-danger is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <span>Billing</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="columns">
            <div class="column">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            Recent System Activity
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <?php
                            $activity_sql = "SELECT sl.*, u.full_name 
                                           FROM system_logs sl 
                                           LEFT JOIN users u ON sl.user_id = u.user_id 
                                           ORDER BY sl.log_timestamp DESC 
                                           LIMIT 10";
                            $activity_result = $conn->query($activity_sql);
                            
                            if ($activity_result->num_rows > 0) {
                                echo '<table class="table is-fullwidth">';
                                echo '<thead><tr><th>User</th><th>Action</th><th>Time</th></tr></thead>';
                                echo '<tbody>';
                                while ($row = $activity_result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['full_name'] ?? 'Unknown') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['action']) . '</td>';
                                    echo '<td>' . date('M j, Y g:i A', strtotime($row['log_timestamp'])) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<p class="has-text-grey">No recent activity found.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="columns">
            <div class="column">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            Recent Appointments
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <?php
                            $recent_appt_sql = "SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name 
                                                FROM appointments a 
                                                LEFT JOIN patients p ON a.patient_id = p.patient_id 
                                                LEFT JOIN doctors d ON a.doctor_id = d.doctor_id 
                                                ORDER BY a.appointment_date DESC 
                                                LIMIT 5";
                            $recent_appt_result = $conn->query($recent_appt_sql);
                            if ($recent_appt_result && $recent_appt_result->num_rows > 0) {
                                echo '<table class="table is-fullwidth">';
                                echo '<thead><tr><th>Patient</th><th>Doctor</th><th>Date/Time</th><th>Status</th></tr></thead>';
                                echo '<tbody>';
                                while ($row = $recent_appt_result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['patient_name'] ?? 'Unknown') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['doctor_name'] ?? 'Unknown') . '</td>';
                                    echo '<td>' . date('M j, Y g:i A', strtotime($row['appointment_date'])) . '</td>';
                                    echo '<td>' . htmlspecialchars(ucfirst($row['status'])) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo '<p class="has-text-grey">No recent appointments found.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html> 