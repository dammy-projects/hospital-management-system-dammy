<?php
session_start();
require_once __DIR__ . '/../config/config.php';

class ReportsController {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function handleRequest() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse(false, 'Unauthorized access - please login first');
            return;
        }
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'overview':
                $this->getOverviewStats();
                break;
            case 'patients':
                $this->getPatientReports();
                break;
            case 'patient_details':
                $this->getPatientDetails();
                break;
            case 'doctors':
                $this->getDoctorReports();
                break;
            case 'billing':
                $this->getBillingReports();
                break;
            case 'export_patients':
                $this->exportPatientData();
                break;
            case 'export_doctors':
                $this->exportDoctorData();
                break;
            case 'doctor_details':
                $this->getDoctorDetails();
                break;
            case 'export_summary':
                $this->exportSummary();
                break;
            case 'appointment_reports':
                $this->getAppointmentReports();
                break;
            case 'daily_schedule':
                $this->getDailySchedule();
                break;
            case 'appointment_list':
                $this->getAppointmentList();
                break;
            case 'export_appointments':
                $this->exportAppointmentData();
                break;
            case 'doctor_performance':
                $this->getDoctorPerformancePaginated();
                break;
            case 'billing_reports':
                $this->getBillingReportsData();
                break;
            case 'billing_history':
                $this->getBillingHistory();
                break;
            case 'export_billing':
                $this->exportBillingData();
                break;
            case 'inventory':
                $this->getInventoryReports();
                break;
            case 'inventory_categories':
                $this->getInventoryCategories();
                break;
            case 'inventory_details':
                $this->getInventoryDetails();
                break;
            case 'export_inventory':
                $this->exportInventoryData();
                break;
            case 'prescriptions':
                $this->getPrescriptionReports();
                break;
            case 'prescription_details':
                $this->getPrescriptionDetails();
                break;
            case 'export_prescriptions':
                $this->exportPrescriptionData();
                break;

            case 'insurance':
                $this->getInsuranceReports();
                break;
            case 'insurance_providers':
                $this->getInsuranceProvidersList();
                break;
            case 'insurance_details':
                $this->getInsuranceDetails();
                break;
            case 'export_insurance':
                $this->exportInsuranceData();
                break;
            case 'system':
                $this->getSystemReports();
                break;
            case 'system_users':
                $this->getSystemUsersList();
                break;
            case 'system_logs':
                $this->getSystemLogs();
                break;
            case 'system_performance':
                $this->getSystemPerformance();
                break;
            case 'system_security':
                $this->getSystemSecurity();
                break;
            case 'user_details':
                $this->getUserDetails();
                break;
            case 'export_system':
                $this->exportSystemData();
                break;
            default:
                $this->sendResponse(false, 'Invalid action or module not yet implemented');
        }
    }
    
    private function getOverviewStats() {
        try {
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            
            $stats = [
                'total_patients' => $this->getTotalPatients($fromDate, $toDate),
                'total_doctors' => $this->getTotalDoctors(),
                'total_appointments' => $this->getTotalAppointments($fromDate, $toDate),
                'total_revenue' => $this->getTotalRevenue($fromDate, $toDate),
                'total_prescriptions' => $this->getTotalPrescriptions($fromDate, $toDate),
                'low_stock_items' => $this->getLowStockItems(),
                'pending_bills' => $this->getPendingBills(),
                'active_users' => $this->getActiveUsers()
            ];
            
            $this->sendResponse(true, 'Overview stats retrieved successfully', $stats);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving overview stats: ' . $e->getMessage());
        }
    }
    
    private function getTotalPatients($fromDate = null, $toDate = null) {
        $sql = "SELECT COUNT(*) as count FROM patients";
        $params = [];
        
        if ($fromDate && $toDate) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
    
    private function getTotalDoctors() {
        $sql = "SELECT COUNT(*) as count FROM doctors WHERE status = 'active'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getTotalAppointments($fromDate = null, $toDate = null) {
        $sql = "SELECT COUNT(*) as count FROM appointments";
        $params = [];
        
        if ($fromDate && $toDate) {
            $sql .= " WHERE appointment_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
    
    private function getTotalRevenue($fromDate = null, $toDate = null) {
        $sql = "SELECT SUM(amount) as total FROM billing WHERE payment_status = 'paid'";
        $params = [];
        
        if ($fromDate && $toDate) {
            $sql .= " AND billing_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
    
    private function getTotalPrescriptions($fromDate = null, $toDate = null) {
        $sql = "SELECT COUNT(*) as count FROM prescriptions";
        $params = [];
        
        if ($fromDate && $toDate) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
    
    private function getLowStockItems() {
        $sql = "SELECT COUNT(*) as count FROM inventory_items WHERE quantity_in_stock <= reorder_level";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getPendingBills() {
        $sql = "SELECT COUNT(*) as count FROM billing WHERE payment_status = 'pending'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getActiveUsers() {
        $sql = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    private function getPatientReports() {
        try {
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $reportType = $_GET['report_type'] ?? 'demographics';
            
            switch ($reportType) {
                case 'demographics':
                    $data = $this->getPatientDemographics($fromDate, $toDate);
                    break;
                case 'registrations':
                    $data = $this->getPatientRegistrations($fromDate, $toDate);
                    break;
                case 'status':
                    $data = $this->getPatientStatusSummary();
                    break;
                case 'age_gender':
                    $data = $this->getPatientAgeGenderDistribution();
                    break;
                default:
                    $data = $this->getPatientDemographics($fromDate, $toDate);
            }
            
            $this->sendResponse(true, 'Patient reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving patient reports: ' . $e->getMessage());
        }
    }
    
    private function getPatientDemographics($fromDate, $toDate) {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        $gender = $_GET['gender'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
                    p.patient_id as id,
                    p.first_name,
                    p.last_name,
                    p.gender,
                    p.contact_number as phone,
                    p.email,
                    p.date_of_birth,
                    p.address,
                    p.created_at,
                    TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) as age
                FROM patients p WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND p.created_at BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        if ($gender) {
            $sql .= " AND p.gender = ?";
            $params[] = $gender;
            $types .= 's';
        }
        
        if ($search) {
            $sql .= " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR p.email LIKE ? OR p.contact_number LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ssss';
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM patients p WHERE 1=1";
        $countParams = [];
        $countTypes = '';
        
        if ($fromDate && $toDate) {
            $countSql .= " AND p.created_at BETWEEN ? AND ?";
            $countParams[] = $fromDate;
            $countParams[] = $toDate . ' 23:59:59';
            $countTypes .= 'ss';
        }
        
        if ($gender) {
            $countSql .= " AND p.gender = ?";
            $countParams[] = $gender;
            $countTypes .= 's';
        }
        
        if ($search) {
            $countSql .= " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR p.email LIKE ? OR p.contact_number LIKE ?)";
            $searchParam = '%' . $search . '%';
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countTypes .= 'ssss';
        }
        
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result()->fetch_assoc();
        
        return [
            'patients' => $patients,
            'total' => $countResult['total'],
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($countResult['total'] / $limit)
        ];
    }
    
    private function getPatientRegistrations($fromDate, $toDate) {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as registrations
                FROM patients";
        
        $params = [];
        if ($fromDate && $toDate) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $sql .= " GROUP BY DATE(created_at) ORDER BY date DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $registrations = [];
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
        
        return $registrations;
    }
    
    private function getPatientStatusSummary() {
        $sql = "SELECT 
                    COUNT(*) as total_patients,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN 1 ELSE 0 END) as minors,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18 AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 65 THEN 1 ELSE 0 END) as adults,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 65 THEN 1 ELSE 0 END) as seniors,
                    SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as male_patients,
                    SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as female_patients
                FROM patients";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    private function getPatientAgeGenderDistribution() {
        $sql = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN 'Under 18'
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 35 THEN '18-35'
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 36 AND 50 THEN '36-50'
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 51 AND 65 THEN '51-65'
                        ELSE 'Over 65'
                    END as age_group,
                    gender,
                    COUNT(*) as count
                FROM patients 
                GROUP BY age_group, gender
                ORDER BY age_group, gender";
        
        $result = $this->conn->query($sql);
        $distribution = [];
        while ($row = $result->fetch_assoc()) {
            $distribution[] = $row;
        }
        
        return $distribution;
    }
    
    private function getDoctorReports() {
        try {
            $reportType = $_GET['report_type'] ?? 'directory';
            
            switch ($reportType) {
                case 'directory':
                    $page = $_GET['page'] ?? 1;
                    $limit = $_GET['limit'] ?? 20;
                    $search = $_GET['search'] ?? '';
                    $specialty = $_GET['specialty'] ?? '';
                    $department = $_GET['department'] ?? '';
                    $data = $this->getDoctorDirectory($page, $limit, $search, $specialty, $department);
                    break;
                case 'specialty':
                    $data = $this->getDoctorSpecialtyDistribution();
                    break;
                case 'department':
                    $data = $this->getDoctorDepartmentAssignment();
                    break;
                case 'workload':
                    $data = $this->getDoctorWorkload();
                    break;
                default:
                    $data = $this->getDoctorDirectory();
            }
            
            $this->sendResponse(true, 'Doctor reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving doctor reports: ' . $e->getMessage());
        }
    }
    
    private function getDoctorDirectory($page = 1, $limit = 20, $search = '', $specialty = '', $department = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
                    d.doctor_id as id,
                    d.first_name,
                    d.last_name,
                    d.specialty as specialization,
                    d.contact_number as phone,
                    d.email,
                    d.status,
                    dept.department_name,
                    d.created_at
                FROM doctors d
                LEFT JOIN departments dept ON d.department_id = dept.department_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($search) {
            $sql .= " AND (d.first_name LIKE ? OR d.last_name LIKE ? OR d.email LIKE ? OR d.contact_number LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ssss';
        }
        
        if ($specialty) {
            $sql .= " AND d.specialty = ?";
            $params[] = $specialty;
            $types .= 's';
        }
        
        if ($department) {
            $sql .= " AND dept.department_name = ?";
            $params[] = $department;
            $types .= 's';
        }
        
        $sql .= " ORDER BY d.first_name, d.last_name LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM doctors d
                     LEFT JOIN departments dept ON d.department_id = dept.department_id
                     WHERE 1=1";
        $countParams = [];
        $countTypes = '';
        
        if ($search) {
            $countSql .= " AND (d.first_name LIKE ? OR d.last_name LIKE ? OR d.email LIKE ? OR d.contact_number LIKE ?)";
            $searchParam = '%' . $search . '%';
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countTypes .= 'ssss';
        }
        
        if ($specialty) {
            $countSql .= " AND d.specialty = ?";
            $countParams[] = $specialty;
            $countTypes .= 's';
        }
        
        if ($department) {
            $countSql .= " AND dept.department_name = ?";
            $countParams[] = $department;
            $countTypes .= 's';
        }
        
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result()->fetch_assoc();
        
        return [
            'data' => $doctors,
            'total' => $countResult['total'],
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($countResult['total'] / $limit)
        ];
    }
    
    private function getDoctorSpecialtyDistribution() {
        $sql = "SELECT 
                    specialty as specialization,
                    COUNT(*) as count
                FROM doctors 
                WHERE status = 'active'
                GROUP BY specialty
                ORDER BY count DESC";
        
        $result = $this->conn->query($sql);
        $distribution = [];
        while ($row = $result->fetch_assoc()) {
            $distribution[] = $row;
        }
        
        return $distribution;
    }
    
    private function getDoctorDepartmentAssignment() {
        $sql = "SELECT 
                    dept.department_name,
                    COUNT(d.doctor_id) as doctor_count
                FROM departments dept
                LEFT JOIN doctors d ON dept.department_id = d.department_id AND d.status = 'active'
                GROUP BY dept.department_id, dept.department_name
                ORDER BY doctor_count DESC";
        
        $result = $this->conn->query($sql);
        $assignment = [];
        while ($row = $result->fetch_assoc()) {
            $assignment[] = $row;
        }
        
        return $assignment;
    }
    
    private function getDoctorWorkload() {
        $sql = "SELECT 
                    d.doctor_id as id,
                    d.first_name,
                    d.last_name,
                    d.specialty as specialization,
                    COUNT(a.appointment_id) as total_appointments,
                    COUNT(CASE WHEN a.appointment_date >= CURDATE() - INTERVAL 30 DAY THEN 1 END) as appointments_last_30_days,
                    COUNT(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments
                FROM doctors d
                LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                WHERE d.status = 'active'
                GROUP BY d.doctor_id
                ORDER BY total_appointments DESC";
        
        $result = $this->conn->query($sql);
        $workload = [];
        while ($row = $result->fetch_assoc()) {
            $workload[] = $row;
        }
        
        return $workload;
    }
    
    private function getBillingReports() {
        try {
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $reportType = $_GET['report_type'] ?? 'revenue';
            
            switch ($reportType) {
                case 'revenue':
                    $data = $this->getRevenueReport($fromDate, $toDate);
                    break;
                case 'payment_status':
                    $data = $this->getPaymentStatusReport($fromDate, $toDate);
                    break;
                default:
                    $data = $this->getRevenueReport($fromDate, $toDate);
            }
            
            $this->sendResponse(true, 'Billing reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving billing reports: ' . $e->getMessage());
        }
    }
    
    private function getRevenueReport($fromDate, $toDate) {
        $sql = "SELECT 
                    DATE(billing_date) as date,
                    COUNT(*) as total_bills,
                    SUM(amount) as total_revenue,
                    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending_amount
                FROM billing";
        
        $params = [];
        if ($fromDate && $toDate) {
            $sql .= " WHERE billing_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $sql .= " GROUP BY DATE(billing_date) ORDER BY date DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $revenue = [];
        while ($row = $result->fetch_assoc()) {
            $revenue[] = $row;
        }
        
        return $revenue;
    }
    
    private function getPaymentStatusReport($fromDate, $toDate) {
        $sql = "SELECT 
                    payment_status,
                    COUNT(*) as count,
                    SUM(amount) as total_amount
                FROM billing";
        
        $params = [];
        if ($fromDate && $toDate) {
            $sql .= " WHERE billing_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $sql .= " GROUP BY payment_status";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $status = [];
        while ($row = $result->fetch_assoc()) {
            $status[] = $row;
        }
        
        return $status;
    }
    
    private function exportSummary() {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="hospital_summary.txt"');
        
        $fromDate = $_GET['from_date'] ?? 'All time';
        $toDate = $_GET['to_date'] ?? 'All time';
        
        echo "Hospital Management System - Summary Report\n";
        echo "Date Range: $fromDate to $toDate\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $stats = [
            'total_patients' => $this->getTotalPatients($_GET['from_date'] ?? null, $_GET['to_date'] ?? null),
            'total_doctors' => $this->getTotalDoctors(),
            'total_appointments' => $this->getTotalAppointments($_GET['from_date'] ?? null, $_GET['to_date'] ?? null),
            'total_revenue' => $this->getTotalRevenue($_GET['from_date'] ?? null, $_GET['to_date'] ?? null),
            'total_prescriptions' => $this->getTotalPrescriptions($_GET['from_date'] ?? null, $_GET['to_date'] ?? null),
            'low_stock_items' => $this->getLowStockItems()
        ];
        
        echo "OVERVIEW STATISTICS:\n";
        echo "Total Patients: " . $stats['total_patients'] . "\n";
        echo "Active Doctors: " . $stats['total_doctors'] . "\n";
        echo "Total Appointments: " . $stats['total_appointments'] . "\n";
        echo "Total Revenue: ₱" . number_format($stats['total_revenue'], 2) . "\n";
        echo "Total Prescriptions: " . $stats['total_prescriptions'] . "\n";
        echo "Low Stock Items: " . $stats['low_stock_items'] . "\n";
        
        exit;
    }
    
    private function getPatientDetails() {
        try {
            $patientId = $_GET['patient_id'] ?? null;
            
            if (!$patientId) {
                $this->sendResponse(false, 'Patient ID is required');
                return;
            }
            
            $sql = "SELECT 
                        p.patient_id as id,
                        p.first_name,
                        p.middle_name,
                        p.last_name,
                        p.gender,
                        p.contact_number as phone,
                        p.email,
                        p.date_of_birth,
                        p.address,
                        p.medical_history,
                        p.status,
                        p.created_at,
                        TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) as age
                    FROM patients p 
                    WHERE p.patient_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($patient = $result->fetch_assoc()) {
                $this->sendResponse(true, 'Patient details retrieved successfully', $patient);
            } else {
                $this->sendResponse(false, 'Patient not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving patient details: ' . $e->getMessage());
        }
    }
    
    private function exportPatientData() {
        try {
            $format = $_GET['format'] ?? 'excel';
            $reportType = $_GET['report_type'] ?? 'demographics';
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $gender = $_GET['gender'] ?? null;
            
            // Get all patient data for export (no pagination)
            $sql = "SELECT 
                        p.patient_id as id,
                        p.first_name,
                        p.middle_name,
                        p.last_name,
                        p.gender,
                        p.contact_number as phone,
                        p.email,
                        p.date_of_birth,
                        p.address,
                        p.medical_history,
                        p.status,
                        p.created_at,
                        TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) as age
                    FROM patients p 
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            // Add filters
            if ($fromDate) {
                $sql .= " AND DATE(p.created_at) >= ?";
                $params[] = $fromDate;
                $types .= 's';
            }
            
            if ($toDate) {
                $sql .= " AND DATE(p.created_at) <= ?";
                $params[] = $toDate;
                $types .= 's';
            }
            
            if ($gender) {
                $sql .= " AND p.gender = ?";
                $params[] = $gender;
                $types .= 's';
            }
            
            $sql .= " ORDER BY p.created_at DESC";
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $patients = [];
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }
            
            if ($format === 'excel') {
                $this->exportToCSV($patients, $reportType);
            } else {
                $this->sendResponse(false, 'Unsupported format');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting data: ' . $e->getMessage());
        }
    }
    
    private function exportToCSV($patients, $reportType) {
        $filename = "patient_report_{$reportType}_" . date('Y-m-d') . ".csv";
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write CSV header
        fputcsv($output, [
            'Patient ID',
            'First Name',
            'Middle Name', 
            'Last Name',
            'Gender',
            'Age',
            'Phone',
            'Email',
            'Date of Birth',
            'Address',
            'Medical History',
            'Status',
            'Registration Date'
        ]);
        
        // Write data rows
        foreach ($patients as $patient) {
            fputcsv($output, [
                $patient['id'],
                $patient['first_name'],
                $patient['middle_name'],
                $patient['last_name'],
                $patient['gender'],
                $patient['age'],
                $patient['phone'],
                $patient['email'],
                $patient['date_of_birth'],
                $patient['address'],
                $patient['medical_history'],
                $patient['status'],
                $patient['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportDoctorToCSV($data, $filename, $columnMapping) {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write CSV header
        $headers = array_values($columnMapping);
        fputcsv($output, $headers);
        
        // Write data rows
        foreach ($data as $row) {
            $csvRow = [];
            foreach (array_keys($columnMapping) as $key) {
                $csvRow[] = $row[$key] ?? '';
            }
            fputcsv($output, $csvRow);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportDoctorData() {
        $reportType = $_GET['report_type'] ?? 'directory';
        $specialty = $_GET['specialty'] ?? '';
        $department = $_GET['department'] ?? '';
        
        try {
            // Get doctor data based on report type
            switch ($reportType) {
                case 'directory':
                    $data = $this->getDoctorDirectory(1, 10000, '', $specialty, $department); // Get all doctors
                    $filename = 'doctor_directory_export_' . date('Y-m-d') . '.csv';
                    $this->exportDoctorToCSV($data['data'], $filename, [
                        'id' => 'Doctor ID',
                        'first_name' => 'First Name',
                        'last_name' => 'Last Name',
                        'specialization' => 'Specialty',
                        'department_name' => 'Department',
                        'phone' => 'Phone',
                        'email' => 'Email',
                        'status' => 'Status'
                    ]);
                    break;
                    
                case 'specialties':
                    $data = $this->getDoctorSpecialtyDistribution();
                    $filename = 'doctor_specialties_export_' . date('Y-m-d') . '.csv';
                    $this->exportDoctorToCSV($data, $filename, [
                        'specialization' => 'Specialty',
                        'count' => 'Number of Doctors'
                    ]);
                    break;
                    
                case 'departments':
                    $data = $this->getDoctorDepartmentAssignment();
                    $filename = 'doctor_departments_export_' . date('Y-m-d') . '.csv';
                    $this->exportDoctorToCSV($data, $filename, [
                        'department_name' => 'Department',
                        'doctor_count' => 'Number of Doctors'
                    ]);
                    break;
                    
                case 'workload':
                    $data = $this->getDoctorWorkload();
                    $filename = 'doctor_workload_export_' . date('Y-m-d') . '.csv';
                    $this->exportDoctorToCSV($data, $filename, [
                        'first_name' => 'First Name',
                        'last_name' => 'Last Name',
                        'specialization' => 'Specialty',
                        'total_appointments' => 'Total Appointments',
                        'appointments_last_30_days' => 'Last 30 Days',
                        'completed_appointments' => 'Completed'
                    ]);
                    break;
                    
                default:
                    throw new Exception('Invalid report type for export');
            }
            
        } catch (Exception $e) {
            error_log("Export doctors error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Export failed']);
        }
    }
    
    private function getDoctorDetails() {
        try {
            $doctorId = $_GET['doctor_id'] ?? '';
            
            if (empty($doctorId)) {
                $this->sendResponse(false, 'Doctor ID is required');
                return;
            }
            
            $sql = "SELECT 
                        d.doctor_id as id,
                        d.first_name,
                        d.last_name,
                        d.specialty,
                        d.contact_number as phone,
                        d.email,
                        d.status,
                        dept.department_name,
                        d.created_at
                    FROM doctors d
                    LEFT JOIN departments dept ON d.department_id = dept.department_id
                    WHERE d.doctor_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($doctor = $result->fetch_assoc()) {
                $this->sendResponse(true, 'Doctor details retrieved successfully', $doctor);
            } else {
                $this->sendResponse(false, 'Doctor not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving doctor details: ' . $e->getMessage());
        }
    }
    
    /**
     * Appointment Reports Methods
     */
    private function getAppointmentReports() {
        try {
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $status = $_GET['status'] ?? '';
            
            $data = [
                'statistics' => $this->getAppointmentStatistics($fromDate, $toDate, $status),
                'trends' => $this->getAppointmentTrends($fromDate, $toDate),
                'doctor_performance' => $this->getDoctorAppointmentPerformance($fromDate, $toDate),
                'status_distribution' => $this->getAppointmentStatusDistribution($fromDate, $toDate)
            ];
            
            $this->sendResponse(true, 'Appointment reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving appointment reports: ' . $e->getMessage());
        }
    }
    
    private function getAppointmentStatistics($fromDate, $toDate, $status) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN DATE(appointment_date) = CURDATE() THEN 1 ELSE 0 END) as today
                FROM appointments WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND appointment_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    private function getAppointmentTrends($fromDate, $toDate) {
        $sql = "SELECT 
                    DATE_FORMAT(appointment_date, '%Y-%m') as month,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM appointments";
        
        $params = [];
        if ($fromDate && $toDate) {
            $sql .= " WHERE appointment_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $sql .= " GROUP BY DATE_FORMAT(appointment_date, '%Y-%m') 
                  ORDER BY month DESC LIMIT 12";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $trends = [];
        while ($row = $result->fetch_assoc()) {
            $trends[] = $row;
        }
        
        return array_reverse($trends);
    }
    
    private function getDoctorAppointmentPerformance($fromDate, $toDate) {
        $sql = "SELECT 
                    CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                    d.specialty,
                    COUNT(a.appointment_id) as total_appointments,
                    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN a.status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                    SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM doctors d
                LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND a.appointment_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $sql .= " GROUP BY d.doctor_id, d.first_name, d.last_name, d.specialty
                  ORDER BY total_appointments DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $performance = [];
        while ($row = $result->fetch_assoc()) {
            $performance[] = $row;
        }
        
        return $performance;
    }
    
    private function getAppointmentStatusDistribution($fromDate, $toDate) {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM appointments WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND appointment_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $sql .= ")), 2) as percentage
                FROM appointments WHERE 1=1";
        
        if ($fromDate && $toDate) {
            $sql .= " AND appointment_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $sql .= " GROUP BY status ORDER BY count DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $distribution = [];
        while ($row = $result->fetch_assoc()) {
            $distribution[] = $row;
        }
        
        return $distribution;
    }
    
    private function getDailySchedule() {
        try {
            $date = $_GET['date'] ?? date('Y-m-d');
            
            $sql = "SELECT 
                        a.appointment_id,
                        a.appointment_date,
                        a.purpose,
                        a.status,
                        CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                        CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                        d.specialty
                    FROM appointments a
                    LEFT JOIN patients p ON a.patient_id = p.patient_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    WHERE DATE(a.appointment_date) = ?
                    ORDER BY a.appointment_date ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $date);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $schedule = [];
            while ($row = $result->fetch_assoc()) {
                $schedule[] = $row;
            }
            
            $this->sendResponse(true, 'Daily schedule retrieved successfully', $schedule);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving daily schedule: ' . $e->getMessage());
        }
    }
    
    private function getAppointmentList() {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            
            $offset = ($page - 1) * $limit;
            
            // Build WHERE conditions
            $whereConditions = [];
            $params = [];
            $types = '';
            
            if ($search) {
                $whereConditions[] = "(CONCAT(p.first_name, ' ', p.last_name) LIKE ? 
                                     OR CONCAT(d.first_name, ' ', d.last_name) LIKE ? 
                                     OR a.purpose LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'sss';
            }
            
            if ($status) {
                $whereConditions[] = "a.status = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            if ($fromDate && $toDate) {
                $whereConditions[] = "a.appointment_date BETWEEN ? AND ?";
                $params[] = $fromDate;
                $params[] = $toDate . ' 23:59:59';
                $types .= 'ss';
            }
            
            $whereClause = empty($whereConditions) ? '' : 'AND ' . implode(' AND ', $whereConditions);
            
            // Count total records
            $countSql = "SELECT COUNT(*) as total
                        FROM appointments a
                        LEFT JOIN patients p ON a.patient_id = p.patient_id
                        LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                        WHERE 1=1 {$whereClause}";
            
            $countStmt = $this->conn->prepare($countSql);
            if (!empty($params)) {
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $total = $countResult->fetch_assoc()['total'];
            
            // Get paginated results
            $sql = "SELECT 
                        a.appointment_id,
                        a.appointment_date,
                        a.purpose,
                        a.status,
                        a.created_at,
                        CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                        CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                        d.specialty
                    FROM appointments a
                    LEFT JOIN patients p ON a.patient_id = p.patient_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    WHERE 1=1 {$whereClause}
                    ORDER BY a.appointment_date DESC 
                    LIMIT ? OFFSET ?";
            
            // Add pagination parameters
            $paginationParams = $params;
            $paginationParams[] = $limit;
            $paginationParams[] = $offset;
            $paginationTypes = $types . 'ii';
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($paginationParams)) {
                $stmt->bind_param($paginationTypes, ...$paginationParams);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            
            $data = [
                'appointments' => $appointments,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_records' => $total,
                    'limit' => $limit
                ]
            ];
            
            $this->sendResponse(true, 'Appointment list retrieved successfully', $data);
            
        } catch (Exception $e) {
            error_log("Appointment list error: " . $e->getMessage());
            $this->sendResponse(false, 'Error retrieving appointment list: ' . $e->getMessage());
        }
    }
    
    private function exportAppointmentData() {
        try {
            $format = $_GET['format'] ?? 'excel';
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $status = $_GET['status'] ?? '';
            
            $sql = "SELECT 
                        DATE_FORMAT(a.appointment_date, '%Y-%m-%d') as appointment_date,
                        TIME_FORMAT(a.appointment_date, '%H:%i') as appointment_time,
                        CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                        p.contact_number as patient_phone,
                        CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                        d.specialty,
                        a.purpose,
                        a.status,
                        DATE_FORMAT(a.created_at, '%Y-%m-%d') as created_date
                    FROM appointments a
                    LEFT JOIN patients p ON a.patient_id = p.patient_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            if ($fromDate && $toDate) {
                $sql .= " AND a.appointment_date BETWEEN ? AND ?";
                $params[] = $fromDate;
                $params[] = $toDate . ' 23:59:59';
                $types .= 'ss';
            }
            
            if ($status) {
                $sql .= " AND a.status = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            $sql .= " ORDER BY a.appointment_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            
            if ($format === 'excel') {
                $this->exportAppointmentToCSV($appointments);
            } else {
                $this->sendResponse(true, 'Appointment data retrieved for export', $appointments);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting appointment data: ' . $e->getMessage());
        }
    }
    
    private function exportAppointmentToCSV($appointments) {
        $filename = 'appointment_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Date',
            'Time', 
            'Patient Name',
            'Patient Phone',
            'Doctor Name',
            'Specialty',
            'Purpose',
            'Status',
            'Created Date'
        ]);
        
        // CSV Data
        foreach ($appointments as $appointment) {
            fputcsv($output, [
                $appointment['appointment_date'],
                $appointment['appointment_time'],
                $appointment['patient_name'],
                $appointment['patient_phone'],
                $appointment['doctor_name'],
                $appointment['specialty'],
                $appointment['purpose'],
                ucfirst($appointment['status']),
                $appointment['created_date']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function getDoctorPerformancePaginated() {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 5);
            $search = $_GET['search'] ?? '';
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT 
                        CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                        d.specialty,
                        COUNT(a.appointment_id) as total_appointments,
                        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN a.status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                        SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                    FROM doctors d
                    LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            if ($fromDate && $toDate) {
                $sql .= " AND a.appointment_date BETWEEN ? AND ?";
                $params[] = $fromDate;
                $params[] = $toDate . ' 23:59:59';
                $types .= 'ss';
            }
            
            if ($search) {
                $sql .= " AND (CONCAT(d.first_name, ' ', d.last_name) LIKE ? OR d.specialty LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'ss';
            }
            
            $sql .= " GROUP BY d.doctor_id, d.first_name, d.last_name, d.specialty";
            
            // Get total count for pagination
            $countSql = "SELECT COUNT(DISTINCT d.doctor_id) as total
                        FROM doctors d
                        LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
                        WHERE 1=1";
            
            $countParams = [];
            $countTypes = '';
            
            if ($fromDate && $toDate) {
                $countSql .= " AND a.appointment_date BETWEEN ? AND ?";
                $countParams[] = $fromDate;
                $countParams[] = $toDate . ' 23:59:59';
                $countTypes .= 'ss';
            }
            
            if ($search) {
                $countSql .= " AND (CONCAT(d.first_name, ' ', d.last_name) LIKE ? OR d.specialty LIKE ?)";
                $searchParam = '%' . $search . '%';
                $countParams[] = $searchParam;
                $countParams[] = $searchParam;
                $countTypes .= 'ss';
            }
            
            $countStmt = $this->conn->prepare($countSql);
            if (!empty($countParams)) {
                $countStmt->bind_param($countTypes, ...$countParams);
            }
            $countStmt->execute();
            $totalResult = $countStmt->get_result();
            $total = $totalResult->fetch_assoc()['total'];
            
            // Get paginated results
            $sql .= " ORDER BY total_appointments DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= 'ii';
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $doctors = [];
            while ($row = $result->fetch_assoc()) {
                $doctors[] = $row;
            }
            
            $data = [
                'doctors' => $doctors,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_records' => $total,
                    'limit' => $limit
                ]
            ];
            
            $this->sendResponse(true, 'Doctor performance retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving doctor performance: ' . $e->getMessage());
        }
    }
    
    private function getBillingReportsData() {
        try {
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $paymentStatus = $_GET['payment_status'] ?? '';
            
            $data = [
                'statistics' => $this->getBillingStatistics($fromDate, $toDate, $paymentStatus),
                'revenue_trends' => $this->getRevenueTrends($fromDate, $toDate),
                'payment_status_distribution' => $this->getPaymentStatusDistribution($fromDate, $toDate)
            ];
            
            $this->sendResponse(true, 'Billing reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving billing reports: ' . $e->getMessage());
        }
    }
    
    private function getBillingStatistics($fromDate, $toDate, $paymentStatus) {
        $sql = "SELECT 
                    COUNT(*) as total_bills,
                    SUM(amount) as total_revenue,
                    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending_amount,
                    SUM(CASE WHEN payment_status = 'cancelled' THEN amount ELSE 0 END) as cancelled_amount,
                    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN payment_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as outstanding_bills,
                    SUM(CASE WHEN insurance_claim_status = 'approved' THEN 1 ELSE 0 END) as insurance_approved,
                    SUM(CASE WHEN insurance_claim_status = 'pending' THEN 1 ELSE 0 END) as insurance_pending,
                    SUM(CASE WHEN insurance_claim_status = 'rejected' THEN 1 ELSE 0 END) as insurance_rejected
                FROM billing WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND billing_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        if ($paymentStatus) {
            $sql .= " AND payment_status = ?";
            $params[] = $paymentStatus;
            $types .= 's';
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    private function getRevenueTrends($fromDate, $toDate) {
        $sql = "SELECT 
                    DATE_FORMAT(billing_date, '%Y-%m') as month,
                    SUM(amount) as total_revenue,
                    SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as paid_revenue,
                    COUNT(*) as total_bills
                FROM billing";
        
        $params = [];
        if ($fromDate && $toDate) {
            $sql .= " WHERE billing_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate . ' 23:59:59'];
        }
        
        $sql .= " GROUP BY DATE_FORMAT(billing_date, '%Y-%m') 
                  ORDER BY month DESC LIMIT 12";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param('ss', ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $trends = [];
        while ($row = $result->fetch_assoc()) {
            $trends[] = $row;
        }
        
        return array_reverse($trends);
    }
    
    private function getPaymentStatusDistribution($fromDate, $toDate) {
        $sql = "SELECT 
                    payment_status,
                    COUNT(*) as count,
                    SUM(amount) as total_amount,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM billing WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND billing_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $sql .= ")), 2) as percentage
                FROM billing WHERE 1=1";
        
        if ($fromDate && $toDate) {
            $sql .= " AND billing_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $sql .= " GROUP BY payment_status ORDER BY count DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $distribution = [];
        while ($row = $result->fetch_assoc()) {
            $distribution[] = $row;
        }
        
        return $distribution;
    }
    
    private function getBillingHistory() {
        try {
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);
            $search = $_GET['search'] ?? '';
            $paymentStatus = $_GET['payment_status'] ?? '';
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            
            $offset = ($page - 1) * $limit;
            
            // Build WHERE conditions
            $whereConditions = [];
            $params = [];
            $types = '';
            
            if ($search) {
                $whereConditions[] = "(CONCAT(p.first_name, ' ', p.last_name) LIKE ? 
                                     OR b.billing_id LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'ss';
            }
            
            if ($paymentStatus) {
                $whereConditions[] = "b.payment_status = ?";
                $params[] = $paymentStatus;
                $types .= 's';
            }
            
            if ($fromDate && $toDate) {
                $whereConditions[] = "b.billing_date BETWEEN ? AND ?";
                $params[] = $fromDate;
                $params[] = $toDate . ' 23:59:59';
                $types .= 'ss';
            }
            
            $whereClause = empty($whereConditions) ? '' : 'AND ' . implode(' AND ', $whereConditions);
            
            // Count total records
            $countSql = "SELECT COUNT(*) as total
                        FROM billing b
                        LEFT JOIN patients p ON b.patient_id = p.patient_id
                        WHERE 1=1 {$whereClause}";
            
            $countStmt = $this->conn->prepare($countSql);
            if (!empty($params)) {
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $total = $countResult->fetch_assoc()['total'];
            
            // Get paginated results
            $sql = "SELECT 
                        b.billing_id,
                        b.amount,
                        b.payment_status,
                        b.insurance_claim_status,
                        b.billing_date,
                        CONCAT(p.first_name, ' ', p.last_name) as patient_name
                    FROM billing b
                    LEFT JOIN patients p ON b.patient_id = p.patient_id
                    WHERE 1=1 {$whereClause}
                    ORDER BY b.billing_date DESC 
                    LIMIT ? OFFSET ?";
            
            // Add pagination parameters
            $paginationParams = $params;
            $paginationParams[] = $limit;
            $paginationParams[] = $offset;
            $paginationTypes = $types . 'ii';
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($paginationParams)) {
                $stmt->bind_param($paginationTypes, ...$paginationParams);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $billing = [];
            while ($row = $result->fetch_assoc()) {
                $billing[] = $row;
            }
            
            $data = [
                'billing' => $billing,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_records' => $total,
                    'limit' => $limit
                ]
            ];
            
            $this->sendResponse(true, 'Billing history retrieved successfully', $data);
            
        } catch (Exception $e) {
            error_log("Billing history error: " . $e->getMessage());
            $this->sendResponse(false, 'Error retrieving billing history: ' . $e->getMessage());
        }
    }
    
    private function exportBillingData() {
        try {
            $format = $_GET['format'] ?? 'excel';
            $fromDate = $_GET['from_date'] ?? null;
            $toDate = $_GET['to_date'] ?? null;
            $paymentStatus = $_GET['payment_status'] ?? '';
            
            $sql = "SELECT 
                        b.billing_id,
                        CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                        b.amount,
                        b.payment_status,
                        b.insurance_claim_status,
                        DATE_FORMAT(b.billing_date, '%Y-%m-%d') as billing_date,
                        CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                        a.purpose as appointment_purpose
                    FROM billing b
                    LEFT JOIN patients p ON b.patient_id = p.patient_id
                    LEFT JOIN appointments a ON b.appointment_id = a.appointment_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            if ($fromDate && $toDate) {
                $sql .= " AND b.billing_date BETWEEN ? AND ?";
                $params[] = $fromDate;
                $params[] = $toDate . ' 23:59:59';
                $types .= 'ss';
            }
            
            if ($paymentStatus) {
                $sql .= " AND b.payment_status = ?";
                $params[] = $paymentStatus;
                $types .= 's';
            }
            
            $sql .= " ORDER BY b.billing_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $billingRecords = [];
            while ($row = $result->fetch_assoc()) {
                $billingRecords[] = $row;
            }
            
            if ($format === 'excel') {
                $this->exportBillingToCSV($billingRecords);
            } else {
                $this->sendResponse(true, 'Billing data retrieved for export', $billingRecords);
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting billing data: ' . $e->getMessage());
        }
    }
    
    private function exportBillingToCSV($billingRecords) {
        $filename = "billing_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Bill ID',
            'Patient Name',
            'Doctor Name',
            'Billing Date',
            'Amount',
            'Payment Status',
            'Payment Method',
            'Payment Date',
            'Insurance Amount',
            'Patient Share',
            'Description'
        ]);
        
        // CSV Data
        foreach ($billingRecords as $record) {
            fputcsv($output, [
                $record['billing_id'],
                $record['patient_name'],
                $record['doctor_name'],
                $record['billing_date'],
                $record['amount'],
                $record['payment_status'],
                $record['payment_method'] ?? '',
                $record['payment_date'] ?? '',
                $record['insurance_amount'] ?? 0,
                $record['patient_share'] ?? $record['amount'],
                $record['description'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function getInventoryReports() {
        try {
            $reportType = $_GET['report_type'] ?? 'overview';
            
            switch ($reportType) {
                case 'overview':
                    $data = $this->getInventoryOverview();
                    break;
                case 'stats':
                    $data = $this->getInventoryStats();
                    break;
                case 'stock_levels':
                    $data = $this->getInventoryStockLevels();
                    break;
                case 'categories':
                    $data = $this->getInventoryCategoryDistribution();
                    break;
                case 'movements':
                    $data = $this->getInventoryMovements();
                    break;
                case 'summary':
                    $data = $this->getInventoryStats(); // Same as stats for summary
                    break;
                default:
                    $data = $this->getInventoryOverview();
            }
            
            $this->sendResponse(true, 'Inventory reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving inventory reports: ' . $e->getMessage());
        }
    }
    
    private function getInventoryOverview() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        $stock = $_GET['stock'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
                    i.item_id,
                    i.item_name,
                    i.item_description,
                    i.serial_number,
                    i.product_number,
                    i.quantity_in_stock,
                    i.unit,
                    i.reorder_level,
                    i.status,
                    i.last_updated,
                    c.category_name
                FROM inventory_items i 
                LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($search) {
            $sql .= " AND (i.item_name LIKE ? OR i.item_description LIKE ? OR i.serial_number LIKE ? OR i.product_number LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ssss';
        }
        
        if ($category) {
            $sql .= " AND i.category_id = ?";
            $params[] = $category;
            $types .= 'i';
        }
        
        if ($status) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        if ($stock) {
            if ($stock === 'low') {
                $sql .= " AND i.quantity_in_stock <= i.reorder_level AND i.quantity_in_stock > 0";
            } elseif ($stock === 'out') {
                $sql .= " AND i.quantity_in_stock = 0";
            } elseif ($stock === 'normal') {
                $sql .= " AND i.quantity_in_stock > i.reorder_level";
            }
        }
        
        $sql .= " ORDER BY i.last_updated DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM inventory_items i 
                     LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                     WHERE 1=1";
        $countParams = [];
        $countTypes = '';
        
        if ($search) {
            $countSql .= " AND (i.item_name LIKE ? OR i.item_description LIKE ? OR i.serial_number LIKE ? OR i.product_number LIKE ?)";
            $searchParam = '%' . $search . '%';
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countTypes .= 'ssss';
        }
        
        if ($category) {
            $countSql .= " AND i.category_id = ?";
            $countParams[] = $category;
            $countTypes .= 'i';
        }
        
        if ($status) {
            $countSql .= " AND i.status = ?";
            $countParams[] = $status;
            $countTypes .= 's';
        }
        
        if ($stock) {
            if ($stock === 'low') {
                $countSql .= " AND i.quantity_in_stock <= i.reorder_level AND i.quantity_in_stock > 0";
            } elseif ($stock === 'out') {
                $countSql .= " AND i.quantity_in_stock = 0";
            } elseif ($stock === 'normal') {
                $countSql .= " AND i.quantity_in_stock > i.reorder_level";
            }
        }
        
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    
    private function getInventoryStats() {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_items,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_items,
                    SUM(CASE WHEN status = 'discontinued' THEN 1 ELSE 0 END) as discontinued_items,
                    SUM(CASE WHEN quantity_in_stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(CASE WHEN quantity_in_stock <= reorder_level AND quantity_in_stock > 0 THEN 1 ELSE 0 END) as low_stock
                FROM inventory_items";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    private function getInventoryStockLevels() {
        $sql = "SELECT 
                    SUM(CASE WHEN quantity_in_stock > reorder_level THEN 1 ELSE 0 END) as normal_stock,
                    SUM(CASE WHEN quantity_in_stock <= reorder_level AND quantity_in_stock > 0 THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN quantity_in_stock = 0 THEN 1 ELSE 0 END) as out_of_stock
                FROM inventory_items";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    private function getInventoryCategoryDistribution() {
        $sql = "SELECT 
                    c.category_name,
                    COUNT(i.item_id) as item_count
                FROM inventory_categories c
                LEFT JOIN inventory_items i ON c.category_id = i.category_id
                GROUP BY c.category_id, c.category_name
                ORDER BY item_count DESC";
        
        $result = $this->conn->query($sql);
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    private function getInventoryMovements() {
        // This would require an inventory_movements table
        // For now, return placeholder data
        $movements = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $movements[] = [
                'date' => $date,
                'in_movements' => rand(0, 10),
                'out_movements' => rand(0, 15)
            ];
        }
        
        return $movements;
    }
    
    private function getInventoryCategories() {
        try {
            $sql = "SELECT category_id, category_name FROM inventory_categories ORDER BY category_name";
            $result = $this->conn->query($sql);
            
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            $this->sendResponse(true, 'Categories retrieved successfully', $categories);
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving categories: ' . $e->getMessage());
        }
    }
    
    private function getInventoryDetails() {
        try {
            $itemId = $_GET['item_id'] ?? '';
            
            if (empty($itemId)) {
                $this->sendResponse(false, 'Item ID is required');
                return;
            }
            
            $sql = "SELECT 
                        i.item_id,
                        i.item_name,
                        i.item_description,
                        i.serial_number,
                        i.product_number,
                        i.quantity_in_stock,
                        i.unit,
                        i.reorder_level,
                        i.status,
                        i.last_updated,
                        c.category_name
                    FROM inventory_items i 
                    LEFT JOIN inventory_categories c ON i.category_id = c.category_id 
                    WHERE i.item_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $itemId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $this->sendResponse(true, 'Item details retrieved successfully', $row);
            } else {
                $this->sendResponse(false, 'Item not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving item details: ' . $e->getMessage());
        }
    }
    
    private function exportInventoryData() {
        try {
            $format = $_GET['format'] ?? 'csv';
            $reportType = $_GET['report_type'] ?? 'overview';
            
            // Get inventory data based on filters
            $data = $this->getInventoryOverview();
            
            if ($format === 'csv' || $format === 'excel') {
                $this->exportInventoryToCSV($data['items']);
            } else {
                $this->sendResponse(false, 'Unsupported export format');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting inventory data: ' . $e->getMessage());
        }
    }
    
    private function exportInventoryToCSV($items) {
        $filename = "inventory_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Item ID',
            'Item Name',
            'Description',
            'Category',
            'Serial Number',
            'Product Number',
            'Current Stock',
            'Unit',
            'Reorder Level',
            'Status',
            'Last Updated'
        ]);
        
        // CSV Data
        foreach ($items as $item) {
            fputcsv($output, [
                $item['item_id'],
                $item['item_name'],
                $item['item_description'] ?? '',
                $item['category_name'] ?? '',
                $item['serial_number'] ?? '',
                $item['product_number'] ?? '',
                $item['quantity_in_stock'],
                $item['unit'] ?? '',
                $item['reorder_level'] ?? '',
                $item['status'],
                $item['last_updated'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function getPrescriptionReports() {
        try {
            $reportType = $_GET['report_type'] ?? 'overview';
            
            switch ($reportType) {
                case 'overview':
                    $data = $this->getPrescriptionOverview();
                    break;
                case 'stats':
                    $data = $this->getPrescriptionStats();
                    break;
                case 'medicine_usage':
                    $data = $this->getPrescriptionMedicineUsage();
                    break;
                case 'doctor_patterns':
                    $data = $this->getPrescriptionDoctorPatterns();
                    break;
                case 'trends':
                    $data = $this->getPrescriptionTrends();
                    break;
                case 'summary':
                    $data = $this->getPrescriptionStats(); // Same as stats for summary
                    break;
                default:
                    $data = $this->getPrescriptionOverview();
            }
            
            $this->sendResponse(true, 'Prescription reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving prescription reports: ' . $e->getMessage());
        }
    }
    
    private function getPrescriptionOverview() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        $fromDate = $_GET['from_date'] ?? null;
        $toDate = $_GET['to_date'] ?? null;
        
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
                    p.prescription_id,
                    p.prescription_date,
                    p.notes,
                    p.status,
                    p.created_at,
                    CONCAT(pt.first_name, ' ', pt.last_name) AS patient_name,
                    CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
                    d.specialty as doctor_specialty,
                    COUNT(pi.prescription_item_id) as total_medicines
                FROM prescriptions p
                LEFT JOIN patients pt ON p.patient_id = pt.patient_id
                LEFT JOIN doctors d ON p.doctor_id = d.doctor_id
                LEFT JOIN prescription_items pi ON p.prescription_id = pi.prescription_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " AND p.prescription_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        if ($search) {
            $sql .= " AND (CONCAT(pt.first_name, ' ', pt.last_name) LIKE ? OR CONCAT(d.first_name, ' ', d.last_name) LIKE ? OR p.notes LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
        }
        
        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $sql .= " GROUP BY p.prescription_id ORDER BY p.prescription_date DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(DISTINCT p.prescription_id) as total 
                     FROM prescriptions p
                     LEFT JOIN patients pt ON p.patient_id = pt.patient_id
                     LEFT JOIN doctors d ON p.doctor_id = d.doctor_id
                     WHERE 1=1";
        $countParams = [];
        $countTypes = '';
        
        if ($fromDate && $toDate) {
            $countSql .= " AND p.prescription_date BETWEEN ? AND ?";
            $countParams[] = $fromDate;
            $countParams[] = $toDate . ' 23:59:59';
            $countTypes .= 'ss';
        }
        
        if ($search) {
            $countSql .= " AND (CONCAT(pt.first_name, ' ', pt.last_name) LIKE ? OR CONCAT(d.first_name, ' ', d.last_name) LIKE ? OR p.notes LIKE ?)";
            $searchParam = '%' . $search . '%';
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countTypes .= 'sss';
        }
        
        if ($status) {
            $countSql .= " AND p.status = ?";
            $countParams[] = $status;
            $countTypes .= 's';
        }
        
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];
        
        return [
            'prescriptions' => $prescriptions,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    
    private function getPrescriptionStats() {
        $fromDate = $_GET['from_date'] ?? null;
        $toDate = $_GET['to_date'] ?? null;
        
        $sql = "SELECT 
                    COUNT(*) as total_prescriptions,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_prescriptions,
                    SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END) as fulfilled_prescriptions,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_prescriptions,
                    COUNT(DISTINCT patient_id) as total_patients_with_prescriptions
                FROM prescriptions";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " WHERE prescription_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        
        // Get total medicines prescribed
        $medicinesSql = "SELECT COUNT(*) as total_medicines_prescribed 
                        FROM prescription_items pi
                        LEFT JOIN prescriptions p ON pi.prescription_id = p.prescription_id";
        
        if ($fromDate && $toDate) {
            $medicinesSql .= " WHERE p.prescription_date BETWEEN ? AND ?";
        }
        
        $medicinesStmt = $this->conn->prepare($medicinesSql);
        if (!empty($params)) {
            $medicinesStmt->bind_param($types, ...$params);
        }
        $medicinesStmt->execute();
        $medicinesResult = $medicinesStmt->get_result();
        $medicinesData = $medicinesResult->fetch_assoc();
        
        $stats['total_medicines_prescribed'] = $medicinesData['total_medicines_prescribed'];
        
        return $stats;
    }
    
    private function getPrescriptionMedicineUsage() {
        $fromDate = $_GET['from_date'] ?? null;
        $toDate = $_GET['to_date'] ?? null;
        
        $sql = "SELECT 
                    m.medicine_name,
                    COUNT(pi.prescription_item_id) as prescription_count
                FROM prescription_items pi
                LEFT JOIN medicines m ON pi.medicine_id = m.medicine_id
                LEFT JOIN prescriptions p ON pi.prescription_id = p.prescription_id";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " WHERE p.prescription_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $sql .= " GROUP BY m.medicine_id, m.medicine_name
                  ORDER BY prescription_count DESC
                  LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $medicines = [];
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
        
        return $medicines;
    }
    
    private function getPrescriptionDoctorPatterns() {
        $fromDate = $_GET['from_date'] ?? null;
        $toDate = $_GET['to_date'] ?? null;
        
        // Doctor prescription counts
        $doctorSql = "SELECT 
                        CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
                        COUNT(p.prescription_id) as prescription_count
                      FROM prescriptions p
                      LEFT JOIN doctors d ON p.doctor_id = d.doctor_id";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $doctorSql .= " WHERE p.prescription_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        }
        
        $doctorSql .= " GROUP BY d.doctor_id ORDER BY prescription_count DESC LIMIT 10";
        
        $doctorStmt = $this->conn->prepare($doctorSql);
        if (!empty($params)) {
            $doctorStmt->bind_param($types, ...$params);
        }
        $doctorStmt->execute();
        $doctorResult = $doctorStmt->get_result();
        
        $doctors = [];
        while ($row = $doctorResult->fetch_assoc()) {
            $doctors[] = $row;
        }
        
        // Specialty distribution
        $specialtySql = "SELECT 
                            d.specialty,
                            COUNT(p.prescription_id) as prescription_count
                         FROM prescriptions p
                         LEFT JOIN doctors d ON p.doctor_id = d.doctor_id";
        
        if ($fromDate && $toDate) {
            $specialtySql .= " WHERE p.prescription_date BETWEEN ? AND ?";
        }
        
        $specialtySql .= " GROUP BY d.specialty ORDER BY prescription_count DESC";
        
        $specialtyStmt = $this->conn->prepare($specialtySql);
        if (!empty($params)) {
            $specialtyStmt->bind_param($types, ...$params);
        }
        $specialtyStmt->execute();
        $specialtyResult = $specialtyStmt->get_result();
        
        $specialties = [];
        while ($row = $specialtyResult->fetch_assoc()) {
            $specialties[] = $row;
        }
        
        return [
            'doctors' => $doctors,
            'specialties' => $specialties
        ];
    }
    
    private function getPrescriptionTrends() {
        $fromDate = $_GET['from_date'] ?? null;
        $toDate = $_GET['to_date'] ?? null;
        
        $sql = "SELECT 
                    DATE(prescription_date) as date,
                    COUNT(*) as prescription_count
                FROM prescriptions";
        
        $params = [];
        $types = '';
        
        if ($fromDate && $toDate) {
            $sql .= " WHERE prescription_date BETWEEN ? AND ?";
            $params[] = $fromDate;
            $params[] = $toDate . ' 23:59:59';
            $types .= 'ss';
        } else {
            // Default to last 30 days if no date range provided
            $sql .= " WHERE prescription_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
        
        $sql .= " GROUP BY DATE(prescription_date) ORDER BY date ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $trends = [];
        while ($row = $result->fetch_assoc()) {
            $trends[] = $row;
        }
        
        return $trends;
    }
    
    private function getPrescriptionDetails() {
        try {
            $prescriptionId = $_GET['prescription_id'] ?? '';
            
            if (empty($prescriptionId)) {
                $this->sendResponse(false, 'Prescription ID is required');
                return;
            }
            
            $sql = "SELECT 
                        p.prescription_id,
                        p.prescription_date,
                        p.notes,
                        p.status,
                        CONCAT(pt.first_name, ' ', pt.last_name) AS patient_name,
                        CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
                        d.specialty as doctor_specialty
                    FROM prescriptions p
                    LEFT JOIN patients pt ON p.patient_id = pt.patient_id
                    LEFT JOIN doctors d ON p.doctor_id = d.doctor_id
                    WHERE p.prescription_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $prescriptionId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($prescription = $result->fetch_assoc()) {
                // Get prescription items
                $itemsSql = "SELECT 
                                pi.*,
                                m.medicine_name,
                                m.dosage_form,
                                m.strength
                             FROM prescription_items pi
                             LEFT JOIN medicines m ON pi.medicine_id = m.medicine_id
                             WHERE pi.prescription_id = ?
                             ORDER BY pi.prescription_item_id";
                
                $itemsStmt = $this->conn->prepare($itemsSql);
                $itemsStmt->bind_param('i', $prescriptionId);
                $itemsStmt->execute();
                $itemsResult = $itemsStmt->get_result();
                
                $items = [];
                while ($row = $itemsResult->fetch_assoc()) {
                    $items[] = $row;
                }
                
                $prescription['items'] = $items;
                
                $this->sendResponse(true, 'Prescription details retrieved successfully', $prescription);
            } else {
                $this->sendResponse(false, 'Prescription not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving prescription details: ' . $e->getMessage());
        }
    }
    
    private function exportPrescriptionData() {
        try {
            $format = $_GET['format'] ?? 'csv';
            $reportType = $_GET['report_type'] ?? 'overview';
            
            // Get prescription data based on filters
            $data = $this->getPrescriptionOverview();
            
            if ($format === 'csv' || $format === 'excel') {
                $this->exportPrescriptionToCSV($data['prescriptions']);
            } else {
                $this->sendResponse(false, 'Unsupported export format');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting prescription data: ' . $e->getMessage());
        }
    }
    
    private function exportPrescriptionToCSV($prescriptions) {
        $filename = "prescription_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Prescription ID',
            'Patient Name',
            'Doctor Name',
            'Doctor Specialty',
            'Prescription Date',
            'Total Medicines',
            'Status',
            'Notes'
        ]);
        
        // CSV Data
        foreach ($prescriptions as $prescription) {
            fputcsv($output, [
                $prescription['prescription_id'],
                $prescription['patient_name'],
                $prescription['doctor_name'],
                $prescription['doctor_specialty'] ?? '',
                $prescription['prescription_date'],
                $prescription['total_medicines'] ?? 0,
                $prescription['status'],
                $prescription['notes'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function getInsuranceReports() {
        try {
            $reportType = $_GET['report_type'] ?? 'overview';
            
            switch ($reportType) {
                case 'overview':
                    $data = $this->getInsuranceOverview();
                    break;
                case 'stats':
                    $data = $this->getInsuranceStats();
                    break;
                case 'providers':
                    $data = $this->getInsuranceProviders();
                    break;
                case 'coverage':
                    $data = $this->getInsuranceCoverage();
                    break;
                case 'trends':
                    $data = $this->getInsuranceTrends();
                    break;
                case 'summary':
                    $data = $this->getInsuranceSummary();
                    break;
                default:
                    $data = $this->getInsuranceOverview();
            }
            
            $this->sendResponse(true, 'Insurance reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving insurance reports: ' . $e->getMessage());
        }
    }

    private function getInsuranceOverview() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        $search = $_GET['search'] ?? '';
        $provider = $_GET['provider'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
                    pi.patient_insurance_id,
                    pi.insurance_number,
                    pi.status,
                    CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name,
                    p.contact_number as patient_phone,
                    ip.provider_name,
                    ip.contact_number as provider_contact,
                    ip.address as provider_address
                FROM patient_insurance pi
                LEFT JOIN patients p ON pi.patient_id = p.patient_id
                LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($search) {
            $sql .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                          OR pi.insurance_number LIKE ? 
                          OR ip.provider_name LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
        }
        
        if ($provider) {
            $sql .= " AND pi.insurance_provider_id = ?";
            $params[] = $provider;
            $types .= 'i';
        }
        
        if ($status) {
            $sql .= " AND pi.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $sql .= " ORDER BY p.first_name, p.last_name LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total 
                     FROM patient_insurance pi
                     LEFT JOIN patients p ON pi.patient_id = p.patient_id
                     LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                     WHERE 1=1";
        
        $countParams = [];
        $countTypes = '';
        
        if ($search) {
            $countSql .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                              OR pi.insurance_number LIKE ? 
                              OR ip.provider_name LIKE ?)";
            $searchParam = '%' . $search . '%';
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countTypes .= 'sss';
        }
        
        if ($provider) {
            $countSql .= " AND pi.insurance_provider_id = ?";
            $countParams[] = $provider;
            $countTypes .= 'i';
        }
        
        if ($status) {
            $countSql .= " AND pi.status = ?";
            $countParams[] = $status;
            $countTypes .= 's';
        }
        
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];
        
        return [
            'records' => $records,
            'total_records' => $total,
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'limit' => $limit
        ];
    }

    private function getInsuranceStats() {
        $sql = "SELECT 
                    COUNT(*) as total_records,
                    SUM(CASE WHEN pi.status = 'active' THEN 1 ELSE 0 END) as active_records,
                    SUM(CASE WHEN pi.status = 'inactive' THEN 1 ELSE 0 END) as inactive_records,
                    COUNT(DISTINCT pi.insurance_provider_id) as total_providers
                FROM patient_insurance pi";
        
        $result = $this->conn->query($sql);
        $stats = $result->fetch_assoc();
        
        // Calculate coverage percentage
        $patientCountSql = "SELECT COUNT(*) as total_patients FROM patients WHERE status = 'active'";
        $patientResult = $this->conn->query($patientCountSql);
        $totalPatients = $patientResult->fetch_assoc()['total_patients'];
        
        $insuredPatientsSql = "SELECT COUNT(DISTINCT patient_id) as insured_patients 
                               FROM patient_insurance 
                               WHERE status = 'active'";
        $insuredResult = $this->conn->query($insuredPatientsSql);
        $insuredPatients = $insuredResult->fetch_assoc()['insured_patients'];
        
        $stats['coverage_percentage'] = $totalPatients > 0 ? round(($insuredPatients / $totalPatients) * 100, 1) : 0;
        
        return $stats;
    }

    private function getInsuranceProviders() {
        // Provider statistics
        $providerStatsSql = "SELECT 
                                ip.provider_name,
                                COUNT(pi.patient_insurance_id) as policy_count
                             FROM insurance_providers ip
                             LEFT JOIN patient_insurance pi ON ip.insurance_provider_id = pi.insurance_provider_id
                             WHERE ip.status = 'active'
                             GROUP BY ip.insurance_provider_id, ip.provider_name
                             ORDER BY policy_count DESC
                             LIMIT 10";
        
        $result = $this->conn->query($providerStatsSql);
        $providerStats = [];
        while ($row = $result->fetch_assoc()) {
            $providerStats[] = $row;
        }
        
        return [
            'provider_stats' => $providerStats
        ];
    }

    private function getInsuranceCoverage() {
        // Coverage status distribution
        $coverageStatsSql = "SELECT 
                                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
                             FROM patient_insurance";
        
        $result = $this->conn->query($coverageStatsSql);
        $coverageStats = $result->fetch_assoc();
        
        // Patient coverage distribution
        $totalPatientsSql = "SELECT COUNT(*) as total FROM patients WHERE status = 'active'";
        $totalPatientsResult = $this->conn->query($totalPatientsSql);
        $totalPatients = $totalPatientsResult->fetch_assoc()['total'];
        
        $insuredPatientsSql = "SELECT COUNT(DISTINCT patient_id) as insured 
                               FROM patient_insurance 
                               WHERE status = 'active'";
        $insuredResult = $this->conn->query($insuredPatientsSql);
        $insuredPatients = $insuredResult->fetch_assoc()['insured'];
        
        $uninsuredPatients = $totalPatients - $insuredPatients;
        
        return [
            'coverage_stats' => $coverageStats,
            'patient_coverage' => [
                'insured' => $insuredPatients,
                'uninsured' => $uninsuredPatients
            ]
        ];
    }

    private function getInsuranceTrends() {
        // Since we don't have enrollment dates, create sample trend data
        // In a real system, you'd track enrollment dates
        $trends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $trends[] = [
                'date' => $date,
                'enrollments' => rand(0, 5) // Sample data
            ];
        }
        
        return [
            'trends' => $trends
        ];
    }

    private function getInsuranceSummary() {
        $stats = $this->getInsuranceStats();
        
        // Additional summary metrics
        $avgPoliciesSql = "SELECT 
                              COUNT(pi.patient_insurance_id) / COUNT(DISTINCT ip.insurance_provider_id) as avg_policies_per_provider
                           FROM patient_insurance pi
                           RIGHT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                           WHERE ip.status = 'active'";
        
        $avgResult = $this->conn->query($avgPoliciesSql);
        $avgData = $avgResult->fetch_assoc();
        
        // Get top provider
        $topProviderSql = "SELECT ip.provider_name
                           FROM insurance_providers ip
                           LEFT JOIN patient_insurance pi ON ip.insurance_provider_id = pi.insurance_provider_id
                           WHERE ip.status = 'active'
                           GROUP BY ip.insurance_provider_id, ip.provider_name
                           ORDER BY COUNT(pi.patient_insurance_id) DESC
                           LIMIT 1";
        
        $topProviderResult = $this->conn->query($topProviderSql);
        $topProvider = $topProviderResult->fetch_assoc();
        
        return array_merge($stats, [
            'avg_policies_per_provider' => round($avgData['avg_policies_per_provider'] ?? 0, 1),
            'top_provider' => $topProvider['provider_name'] ?? 'N/A'
        ]);
    }

    private function getInsuranceProvidersList() {
        try {
            $sql = "SELECT insurance_provider_id, provider_name 
                    FROM insurance_providers 
                    WHERE status = 'active' 
                    ORDER BY provider_name";
            
            $result = $this->conn->query($sql);
            $providers = [];
            while ($row = $result->fetch_assoc()) {
                $providers[] = $row;
            }
            
            $this->sendResponse(true, 'Providers retrieved successfully', $providers);
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving providers: ' . $e->getMessage());
        }
    }

    private function getInsuranceDetails() {
        try {
            $insuranceId = $_GET['insurance_id'] ?? '';
            
            if (empty($insuranceId)) {
                $this->sendResponse(false, 'Insurance ID is required');
                return;
            }
            
            $sql = "SELECT 
                        pi.patient_insurance_id,
                        pi.insurance_number,
                        pi.status,
                        CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name,
                        p.contact_number as patient_phone,
                        p.email as patient_email,
                        ip.provider_name,
                        ip.contact_number as provider_contact,
                        ip.address as provider_address
                    FROM patient_insurance pi
                    LEFT JOIN patients p ON pi.patient_id = p.patient_id
                    LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                    WHERE pi.patient_insurance_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $insuranceId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $this->sendResponse(true, 'Insurance details retrieved successfully', $row);
            } else {
                $this->sendResponse(false, 'Insurance record not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving insurance details: ' . $e->getMessage());
        }
    }

    private function exportInsuranceData() {
        try {
            $format = $_GET['format'] ?? 'excel';
            $search = $_GET['search'] ?? '';
            $provider = $_GET['provider'] ?? '';
            $status = $_GET['status'] ?? '';
            
            $sql = "SELECT 
                        pi.patient_insurance_id,
                        pi.insurance_number,
                        pi.status,
                        CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) as patient_name,
                        p.contact_number as patient_phone,
                        p.email as patient_email,
                        ip.provider_name,
                        ip.contact_number as provider_contact,
                        ip.address as provider_address
                    FROM patient_insurance pi
                    LEFT JOIN patients p ON pi.patient_id = p.patient_id
                    LEFT JOIN insurance_providers ip ON pi.insurance_provider_id = ip.insurance_provider_id
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            if ($search) {
                $sql .= " AND (CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) LIKE ? 
                              OR pi.insurance_number LIKE ? 
                              OR ip.provider_name LIKE ?)";
                $searchParam = '%' . $search . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
                $types .= 'sss';
            }
            
            if ($provider) {
                $sql .= " AND pi.insurance_provider_id = ?";
                $params[] = $provider;
                $types .= 'i';
            }
            
            if ($status) {
                $sql .= " AND pi.status = ?";
                $params[] = $status;
                $types .= 's';
            }
            
            $sql .= " ORDER BY p.first_name, p.last_name";
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
            
            if ($format === 'excel' || $format === 'csv') {
                $this->exportInsuranceToCSV($records);
            } else {
                $this->sendResponse(false, 'Unsupported export format');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting insurance data: ' . $e->getMessage());
        }
    }

    private function exportInsuranceToCSV($records) {
        $filename = "insurance_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Insurance ID',
            'Patient Name',
            'Patient Phone',
            'Patient Email',
            'Insurance Provider',
            'Policy Number',
            'Status',
            'Provider Contact',
            'Provider Address'
        ]);
        
        // CSV Data
        foreach ($records as $record) {
            fputcsv($output, [
                $record['patient_insurance_id'],
                $record['patient_name'],
                $record['patient_phone'] ?? '',
                $record['patient_email'] ?? '',
                $record['provider_name'],
                $record['insurance_number'],
                $record['status'],
                $record['provider_contact'] ?? '',
                $record['provider_address'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function sendResponse($success, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // System Reports Methods
    private function getSystemReports() {
        try {
            $reportType = $_GET['report_type'] ?? 'overview';
            
            switch ($reportType) {
                case 'overview':
                    $data = $this->getSystemOverview();
                    break;
                case 'status':
                    $data = $this->getSystemStatus();
                    break;
                case 'activity':
                    $data = $this->getSystemActivity();
                    break;
                case 'logs':
                    $data = $this->getSystemLogs();
                    break;
                case 'performance':
                    $data = $this->getSystemPerformance();
                    break;
                case 'security':
                    $data = $this->getSystemSecurity();
                    break;
                default:
                    $data = $this->getSystemOverview();
            }
            
            $this->sendResponse(true, 'System reports retrieved successfully', $data);
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving system reports: ' . $e->getMessage());
        }
    }

    private function getSystemOverview() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
                    u.user_id as id,
                    u.username,
                    u.email,
                    u.role,
                    u.status,
                    u.created_at,
                    u.last_login,
                    CASE 
                        WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Recent'
                        WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 90 DAY) THEN 'Moderate'
                        ELSE 'Inactive'
                    END as activity_level
                FROM users u WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($search) {
            $sql .= " AND (u.username LIKE ? OR u.email LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $params[] = $role;
            $types .= 's';
        }
        
        $sql .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM users u WHERE 1=1";
        $countParams = [];
        $countTypes = '';
        
        if ($search) {
            $countSql .= " AND (u.username LIKE ? OR u.email LIKE ?)";
            $searchParam = '%' . $search . '%';
            $countParams[] = $searchParam;
            $countParams[] = $searchParam;
            $countTypes .= 'ss';
        }
        
        if ($role) {
            $countSql .= " AND u.role = ?";
            $countParams[] = $role;
            $countTypes .= 's';
        }
        
        $countStmt = $this->conn->prepare($countSql);
        if (!empty($countParams)) {
            $countStmt->bind_param($countTypes, ...$countParams);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result()->fetch_assoc();
        
        return [
            'users' => $users,
            'total' => $countResult['total'],
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($countResult['total'] / $limit)
        ];
    }

    private function getSystemStatus() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users,
                    SUM(CASE WHEN role = 'doctor' THEN 1 ELSE 0 END) as doctor_users,
                    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff_users,
                    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as daily_active,
                    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as weekly_active,
                    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as monthly_active
                FROM users";
        
        $result = $this->conn->query($sql);
        $stats = $result->fetch_assoc();
        
        // Get system health metrics
        $systemHealth = $this->getSystemHealthMetrics();
        
        return array_merge($stats, $systemHealth);
    }

    private function getSystemHealthMetrics() {
        // Database size and table counts
        $dbStats = $this->getDatabaseStats();
        
        // System uptime (simulated - in real system you'd track this)
        $uptime = [
            'days' => rand(1, 30),
            'hours' => rand(0, 23),
            'minutes' => rand(0, 59)
        ];
        
        // Performance metrics
        $performance = [
            'response_time' => round(rand(50, 300) / 100, 2), // Simulated response time in seconds
            'memory_usage' => rand(40, 80), // Simulated memory usage percentage
            'cpu_usage' => rand(10, 60) // Simulated CPU usage percentage
        ];
        
        return [
            'database_stats' => $dbStats,
            'uptime' => $uptime,
            'performance' => $performance
        ];
    }

    private function getDatabaseStats() {
        $tables = [
            'users', 'patients', 'doctors', 'appointments', 'billing', 
            'medical_records', 'prescriptions', 'inventory_items'
        ];
        
        $stats = [];
        foreach ($tables as $table) {
            try {
                $sql = "SELECT COUNT(*) as count FROM `$table`";
                $result = $this->conn->query($sql);
                if ($result) {
                    $row = $result->fetch_assoc();
                    $stats[$table] = $row['count'];
                } else {
                    $stats[$table] = 0;
                }
            } catch (Exception $e) {
                $stats[$table] = 0;
            }
        }
        
        return $stats;
    }

    private function getSystemActivity() {
        // Get user activity over time
        $sql = "SELECT 
                    DATE(last_login) as date,
                    COUNT(DISTINCT user_id) as active_users
                FROM users 
                WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(last_login)
                ORDER BY date DESC";
        
        $result = $this->conn->query($sql);
        $activity = [];
        while ($row = $result->fetch_assoc()) {
            $activity[] = $row;
        }
        
        return $activity;
    }

    private function getSystemUsersList() {
        try {
            $this->getSystemOverview();
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving user list: ' . $e->getMessage());
        }
    }

    private function getSystemLogs() {
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 20);
        
        // Generate total logs first (simulated - in a real system, you'd query a logs table)
        $totalLogs = 100; // Simulated total number of logs
        $offset = ($page - 1) * $limit;
        
        $logTypes = ['INFO', 'WARNING', 'ERROR', 'SUCCESS'];
        $actions = [
            'User login attempt',
            'Password change',
            'Data export',
            'System backup',
            'Database query',
            'File upload',
            'Configuration change',
            'User registration'
        ];
        
        $logs = [];
        for ($i = $offset; $i < min($offset + $limit, $totalLogs); $i++) {
            $logs[] = [
                'id' => $i + 1,
                'timestamp' => date('Y-m-d H:i:s', strtotime("-{$i} hours")),
                'type' => $logTypes[array_rand($logTypes)],
                'action' => $actions[array_rand($actions)],
                'user' => 'User ' . rand(1, 10),
                'ip_address' => '192.168.1.' . rand(1, 254),
                'details' => 'System operation completed successfully'
            ];
        }
        
        return [
            'logs' => $logs,
            'total' => $totalLogs,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalLogs / $limit)
        ];
    }

    private function getSystemPerformance() {
        // Generate performance metrics over time
        $performance = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = date('H:00', strtotime("-{$i} hours"));
            $performance[] = [
                'time' => $hour,
                'response_time' => round(rand(100, 500) / 100, 2),
                'memory_usage' => rand(30, 80),
                'cpu_usage' => rand(10, 70),
                'active_sessions' => rand(5, 50)
            ];
        }
        
        return $performance;
    }

    private function getSystemSecurity() {
        // Security metrics and events
        $security = [
            'failed_logins' => [
                'last_24h' => rand(0, 10),
                'last_week' => rand(0, 50),
                'last_month' => rand(0, 200)
            ],
            'password_changes' => [
                'last_24h' => rand(0, 5),
                'last_week' => rand(0, 20),
                'last_month' => rand(0, 80)
            ],
            'active_sessions' => rand(10, 50),
            'security_events' => []
        ];
        
        // Generate sample security events
        $eventTypes = ['Failed Login', 'Successful Login', 'Password Change', 'Account Locked', 'Suspicious Activity'];
        for ($i = 0; $i < 20; $i++) {
            $security['security_events'][] = [
                'timestamp' => date('Y-m-d H:i:s', strtotime("-{$i} hours")),
                'type' => $eventTypes[array_rand($eventTypes)],
                'user' => 'User ' . rand(1, 10),
                'ip_address' => '192.168.1.' . rand(1, 254),
                'severity' => ['Low', 'Medium', 'High'][rand(0, 2)]
            ];
        }
        
        return $security;
    }

    private function getUserDetails() {
        try {
            $userId = $_GET['user_id'] ?? '';
            
            if (empty($userId)) {
                $this->sendResponse(false, 'User ID is required');
                return;
            }
            
            $sql = "SELECT 
                        u.user_id,
                        u.username,
                        u.email,
                        u.role,
                        u.status,
                        u.created_at,
                        u.last_login,
                        CASE 
                            WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Recent'
                            WHEN u.last_login >= DATE_SUB(NOW(), INTERVAL 90 DAY) THEN 'Moderate'
                            ELSE 'Inactive'
                        END as activity_level
                    FROM users u 
                    WHERE u.user_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $this->sendResponse(true, 'User details retrieved successfully', $row);
            } else {
                $this->sendResponse(false, 'User not found');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error retrieving user details: ' . $e->getMessage());
        }
    }

    private function exportSystemData() {
        try {
            $format = $_GET['format'] ?? 'excel';
            $reportType = $_GET['report_type'] ?? 'overview';
            
            switch ($reportType) {
                case 'overview':
                    $data = $this->getSystemOverview();
                    $this->exportSystemToCSV($data['users'], 'system_users');
                    break;
                case 'logs':
                    $data = $this->getSystemLogs();
                    $this->exportSystemLogsToCSV($data['logs']);
                    break;
                case 'performance':
                    $data = $this->getSystemPerformance();
                    $this->exportSystemPerformanceToCSV($data);
                    break;
                default:
                    $this->sendResponse(false, 'Invalid export type');
            }
            
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error exporting system data: ' . $e->getMessage());
        }
    }

    private function exportSystemToCSV($users, $type) {
        $filename = "system_{$type}_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'User ID',
            'Username',
            'Email',
            'Role',
            'Status',
            'Activity Level',
            'Created At',
            'Last Login'
        ]);
        
        // CSV Data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['username'],
                $user['email'],
                $user['role'],
                $user['status'],
                $user['activity_level'],
                $user['created_at'],
                $user['last_login'] ?? 'Never'
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportSystemLogsToCSV($logs) {
        $filename = "system_logs_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Log ID',
            'Timestamp',
            'Type',
            'Action',
            'User',
            'IP Address',
            'Details'
        ]);
        
        // CSV Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['timestamp'],
                $log['type'],
                $log['action'],
                $log['user'],
                $log['ip_address'],
                $log['details']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportSystemPerformanceToCSV($performance) {
        $filename = "system_performance_report_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'Time',
            'Response Time (s)',
            'Memory Usage (%)',
            'CPU Usage (%)',
            'Active Sessions'
        ]);
        
        // CSV Data
        foreach ($performance as $metric) {
            fputcsv($output, [
                $metric['time'],
                $metric['response_time'],
                $metric['memory_usage'],
                $metric['cpu_usage'],
                $metric['active_sessions']
            ]);
        }
        
        fclose($output);
        exit;
    }
}

// Clean any unwanted output
ob_clean();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle the request
try {
    ob_end_clean();
    header('Content-Type: application/json');
    $controller = new ReportsController();
    $controller->handleRequest();
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
} 