-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 07:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `purpose`, `status`, `created_at`) VALUES
(1, 1, 1, '2024-01-15 10:00:00', 'Cardiac consultation', 'completed', '2025-06-29 07:29:29'),
(2, 2, 2, '2024-01-15 11:00:00', 'Neurological examination', 'scheduled', '2025-06-29 07:29:29'),
(3, 3, 1, '2024-01-15 14:00:00', 'Heart checkup', 'scheduled', '2025-06-29 07:29:29'),
(4, 4, 3, '2024-01-16 09:00:00', 'Pediatric consultation', 'scheduled', '2025-06-29 07:29:29'),
(5, 5, 4, '2024-01-16 10:30:00', 'Emergency consultation', 'scheduled', '2025-06-29 07:29:29'),
(6, 6, 5, '2024-01-16 13:00:00', 'Surgical consultation', 'scheduled', '2025-06-29 07:29:29'),
(7, 7, 6, '2024-01-17 08:00:00', 'Orthopedic examination', 'scheduled', '2025-06-29 07:29:29'),
(8, 8, 7, '2024-01-17 11:00:00', 'Oncology consultation', 'scheduled', '2025-06-29 07:29:29'),
(9, 9, 8, '2024-01-17 15:00:00', 'Psychiatric evaluation', 'scheduled', '2025-06-29 07:29:29'),
(11, 1, 1, '2024-01-18 14:00:00', 'Follow-up consultation', 'scheduled', '2025-06-29 07:29:29'),
(12, 2, 2, '2024-01-19 10:00:00', 'Treatment review', 'scheduled', '2025-06-29 07:29:29'),
(13, 3, 4, '2024-01-19 11:30:00', 'Emergency checkup', 'scheduled', '2025-06-29 07:29:29'),
(14, 4, 3, '2024-01-20 09:00:00', 'Vaccination', 'scheduled', '2025-06-29 07:29:29'),
(15, 5, 1, '2024-01-20 13:00:00', 'Cardiac stress test', 'scheduled', '2025-06-29 07:29:29'),
(16, 1, 1, '2025-06-30 10:00:00', 'Follow-up cardiac consultation', 'scheduled', '2025-06-30 02:07:58'),
(17, 2, 2, '2025-06-30 14:00:00', 'Neurological checkup', 'scheduled', '2025-06-30 02:07:58'),
(18, 3, 1, '2025-07-01 09:00:00', 'Heart monitoring', 'scheduled', '2025-06-30 02:07:58'),
(19, 4, 3, '2025-07-01 11:00:00', 'Pediatric vaccination', 'scheduled', '2025-06-30 02:07:58'),
(20, 5, 4, '2025-07-02 15:00:00', 'Emergency follow-up', 'scheduled', '2025-06-30 02:07:58'),
(21, 9, 5, '2025-07-03 10:30:00', 'Pre-surgery consultation', 'scheduled', '2025-06-30 02:07:58'),
(23, 1, 1, '2025-07-05 09:00:00', 'Follow-up cardiac checkup', 'scheduled', '2025-06-30 02:25:51'),
(24, 2, 2, '2025-07-05 10:30:00', 'Neurological assessment', 'scheduled', '2025-06-30 02:25:51'),
(25, 3, 3, '2025-07-05 14:00:00', 'Pediatric wellness check', 'scheduled', '2025-06-30 02:25:51'),
(26, 4, 4, '2025-07-06 11:00:00', 'Emergency consultation follow-up', 'scheduled', '2025-06-30 02:25:51'),
(27, 5, 5, '2025-07-06 15:30:00', 'Pre-operative assessment', 'scheduled', '2025-06-30 02:25:51'),
(28, 6, 6, '2025-07-07 08:00:00', 'Orthopedic follow-up', 'scheduled', '2025-06-30 02:25:51'),
(29, 7, 7, '2025-07-07 13:00:00', 'Oncology consultation', 'scheduled', '2025-06-30 02:25:51'),
(30, 8, 8, '2025-07-07 16:00:00', 'Psychiatric evaluation', 'scheduled', '2025-06-30 02:25:51'),
(32, 5, 5, '2025-07-05 11:27:27', 'Routine examination', 'scheduled', '2025-06-30 02:27:27'),
(33, 5, 5, '2025-07-21 16:27:27', 'Emergency visit', 'scheduled', '2025-06-30 02:27:27'),
(34, 3, 1, '2025-07-03 13:27:27', 'Regular checkup', 'scheduled', '2025-06-30 02:27:27'),
(35, 2, 5, '2025-07-07 10:27:27', 'Emergency visit', 'scheduled', '2025-06-30 02:27:27'),
(36, 3, 3, '2025-07-14 10:27:27', 'Consultation', 'scheduled', '2025-06-30 02:27:27'),
(37, 5, 1, '2025-07-08 12:27:27', 'Regular checkup', 'scheduled', '2025-06-30 02:27:27'),
(38, 3, 4, '2025-07-07 17:27:27', 'Routine examination', 'scheduled', '2025-06-30 02:27:27'),
(39, 4, 5, '2025-07-02 17:27:27', 'Follow-up visit', 'scheduled', '2025-06-30 02:27:27'),
(40, 4, 5, '2025-07-09 12:27:00', 'Routine examination', 'cancelled', '2025-06-30 02:27:27'),
(41, 1, 3, '2025-07-17 10:27:27', 'Regular checkup', 'scheduled', '2025-06-30 02:27:27'),
(42, 3, 1, '2025-07-23 18:27:27', 'Consultation', 'scheduled', '2025-06-30 02:27:27'),
(43, 3, 1, '2025-07-10 15:27:27', 'Regular checkup', 'scheduled', '2025-06-30 02:27:27'),
(44, 1, 5, '2025-07-17 12:27:27', 'Emergency visit', 'scheduled', '2025-06-30 02:27:27'),
(45, 3, 3, '2025-07-09 17:27:27', 'Routine examination', 'scheduled', '2025-06-30 02:27:27'),
(46, 1, 1, '2025-07-05 10:27:27', 'Consultation', 'scheduled', '2025-06-30 02:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `billing_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `insurance_claim_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `billing_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`billing_id`, `patient_id`, `appointment_id`, `amount`, `payment_status`, `insurance_claim_status`, `billing_date`) VALUES
(1, 1, 1, 150.00, 'paid', 'approved', '2024-01-15 10:30:00'),
(2, 2, 2, 200.00, 'pending', 'pending', '2024-01-15 11:30:00'),
(3, 3, 3, 175.00, 'pending', 'pending', '2024-01-15 14:30:00'),
(4, 4, 4, 100.00, 'paid', 'approved', '2024-01-16 09:30:00'),
(5, 5, 5, 500.00, 'pending', 'pending', '2024-01-16 11:00:00'),
(6, 6, 6, 300.00, 'pending', 'pending', '2024-01-16 13:30:00'),
(7, 7, 7, 125.00, 'paid', 'approved', '2024-01-17 08:30:00'),
(8, 8, 8, 250.00, 'pending', 'pending', '2024-01-17 11:30:00'),
(9, 9, 9, 180.00, 'pending', 'pending', '2024-01-17 15:30:00'),
(10, 10, NULL, 120.00, 'paid', 'approved', '2024-01-18 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `location`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Cardiologys', 'First Floor, Wing A', 'active', '2025-06-29 07:29:29', '2025-06-29 13:17:41'),
(2, 'Neurology', 'Second Floor, Wing B', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(3, 'Pediatrics', 'Third Floor, Wing C', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(4, 'Emergency Medicine', 'Ground Floor, Emergency Wing', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(5, 'General Surgery', 'First Floor, Wing D', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(6, 'Orthopedic', 'Second Floor, Wing E', 'active', '2025-06-29 07:29:29', '2025-06-29 11:14:00'),
(7, 'Oncology', 'Third Floor, Wing F', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(8, 'Psychiatry', 'Fourth Floor, Wing G', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(9, 'Radiology', 'Ground Floor, Imaging Center', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(10, 'Laboratory', 'Ground Floor, Lab Wing', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `first_name`, `middle_name`, `last_name`, `specialty`, `contact_number`, `email`, `department_id`, `schedule`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Dr. Sarah', '', 'Johnson', 'Cardiologist', '+1234567900', 'sarah.johnson@hospital.com', 1, 'Monday-Friday: 9:00 AM - 5:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(2, NULL, 'Dr. Michael', '', 'Chen', 'Neurologist', '+1234567901', 'michael.chen@hospital.com', 2, 'Monday-Friday: 8:00 AM - 4:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(3, NULL, 'Dr. Emily', '', 'Wilson', 'Pediatrician', '+1234567902', 'emily.wilson@hospital.com', 3, 'Monday-Friday: 9:00 AM - 6:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(4, NULL, 'Dr. Robert', '', 'Davis', 'Emergency Medicine', '+1234567903', 'robert.davis@hospital.com', 4, '24/7 rotating shifts', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(5, NULL, 'Dr. Lisa', '', 'Anderson', 'General Surgeon', '+1234567904', 'lisa.anderson@hospital.com', 5, 'Monday-Friday: 7:00 AM - 3:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(6, NULL, 'Dr. David', '', 'Taylor', 'Orthopedic Surgeon', '+1234567905', 'david.taylor@hospital.com', 6, 'Monday-Friday: 8:00 AM - 5:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(7, NULL, 'Dr. Jennifer', '', 'Martinez', 'Oncologist', '+1234567906', 'jennifer.martinez@hospital.com', 7, 'Monday-Friday: 9:00 AM - 5:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(8, NULL, 'Dr. Christopher', '', 'Garcia', 'Psychiatrist', '+1234567907', 'christopher.garcia@hospital.com', 8, 'Monday-Friday: 10:00 AM - 6:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(10, NULL, 'Dr. James', '', 'Thompson', 'Pathologist', '+1234567909', 'james.thompson@hospital.com', 10, 'Monday-Friday: 7:00 AM - 3:00 PM', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `insurance_providers`
--

CREATE TABLE `insurance_providers` (
  `insurance_provider_id` int(11) NOT NULL,
  `provider_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `insurance_providers`
--

INSERT INTO `insurance_providers` (`insurance_provider_id`, `provider_name`, `contact_number`, `address`, `status`) VALUES
(1, 'Blue Cross Blue Shield', '+18005551234', '123 Insurance Ave, City, State 12345', 'active'),
(2, 'Aetnas', '+18005551235', '456 Coverage St, City, State 12345', 'active'),
(3, 'Cigna', '+18005551236', '789 Health Blvd, City, State 12345', 'active'),
(4, 'UnitedHealth Group', '+18005551237', '321 Medical Dr, City, State 12345', 'active'),
(5, 'Humana', '+18005551238', '654 Care Way, City, State 12345', 'active'),
(6, 'Kaiser Permanente', '+18005551239', '987 Wellness Rd, City, State 12345', 'active'),
(7, 'Anthem', '+18005551240', '147 Benefit Ln, City, State 12345', 'active'),
(8, 'Molina Healthcare', '+18005551241', '258 Health Ct, City, State 12345', 'active'),
(9, 'Centene Corporation', '+18005551242', '369 Care Pl, City, State 12345', 'active'),
(10, 'WellCare Health Plans', '+18005551243', '741 Insurance Way, City, State 12345', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_categories`
--

CREATE TABLE `inventory_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_categories`
--

INSERT INTO `inventory_categories` (`category_id`, `category_name`) VALUES
(10, 'Cleaning Supplies'),
(4, 'Diagnostic Equipment'),
(7, 'Emergency Equipment'),
(6, 'Laboratory Supplies'),
(2, 'Medical Supplies'),
(1, 'Medications'),
(9, 'Office Supplies'),
(8, 'Patient Care Items'),
(5, 'Personal Protective Equipment'),
(3, 'Surgical Instruments');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_description` text DEFAULT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `product_number` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `quantity_in_stock` int(11) DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `reorder_level` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','discontinued') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`item_id`, `item_name`, `item_description`, `serial_number`, `product_number`, `category_id`, `quantity_in_stock`, `unit`, `reorder_level`, `last_updated`, `status`) VALUES
(1, 'Aspirin 81mg', 'Low-dose aspirin tablets', 'ASP001', 'ASP-81-100', 1, 501, '0', 100, '2025-06-30 03:53:17', 'active'),
(2, 'Surgical Masks', '3-ply disposable surgical masks', 'MASK001', 'MASK-3PLY-50', 5, 1000, 'pieces', 200, '2025-06-29 07:29:29', 'active'),
(3, 'Syringes 10ml', 'Disposable syringes 10ml', 'SYR001', 'SYR-10ML-100', 2, 500, 'pieces', 100, '2025-06-29 07:29:29', 'active'),
(4, 'Stethoscope', 'Professional stethoscope', 'STETH001', 'STETH-PRO-1', 4, 45, 'pieces', 10, '2025-06-30 04:18:36', 'active'),
(5, 'Bandages', 'Sterile gauze bandages', 'BAND001', 'BAND-4X4-100', 2, 200, 'rolls', 50, '2025-06-29 07:29:29', 'active'),
(6, 'Gloves L', 'Latex gloves size large', 'GLOVE001', 'GLOVE-L-100', 5, 500, 'pairs', 100, '2025-06-29 07:29:29', 'active'),
(7, 'Thermometer', 'Digital thermometer', 'THERM001', 'THERM-DIG-1', 4, 30, 'pieces', 5, '2025-06-29 07:29:29', 'active'),
(8, 'IV Bags', '500ml IV solution bags', 'IV001', 'IV-500ML-50', 2, 200, 'bags', 50, '2025-06-29 07:29:29', 'active'),
(9, 'Surgical Scissors', 'Sterile surgical scissors', 'SCISS001', 'SCISS-STER-1', 3, 20, 'pieces', 5, '2025-06-29 07:29:29', 'active'),
(10, 'Blood Pressure Cuff', 'Adult blood pressure cuff', 'BPC001', 'BPC-ADULT-1', 4, 20, 'pieces', 5, '2025-06-30 04:18:36', 'active'),
(11, 'Antibiotics', 'Broad spectrum antibiotics', 'ANTI001', 'ANTI-BROAD-50', 1, 100, 'vials', 20, '2025-06-29 07:29:29', 'active'),
(12, 'Defibrillator', 'Automated external defibrillator', 'DEFIB001', 'DEFIB-AED-1', 7, 5, 'units', 1, '2025-06-29 07:29:29', 'active'),
(13, 'Microscope', 'Laboratory microscope', 'MICRO001', 'MICRO-LAB-1', 6, 3, 'units', 1, '2025-06-29 07:29:29', 'active'),
(14, 'Wheelchair', 'Standard wheelchair', 'WHEEL001', 'WHEEL-STD-1', 8, 15, 'units', 3, '2025-06-29 07:29:29', 'active'),
(15, 'Hospital Beds', 'Electric hospital beds', 'BED001', 'BED-ELEC-1', 8, 20, 'units', 5, '2025-06-29 07:29:29', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `movement_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `movement_type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `movement_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_movements`
--

INSERT INTO `inventory_movements` (`movement_id`, `item_id`, `movement_type`, `quantity`, `movement_date`, `notes`, `performed_by`) VALUES
(1, 1, 'in', 1000, '2024-01-01 09:00:00', 'Initial stock', NULL),
(2, 2, 'in', 2000, '2024-01-01 09:00:00', 'Initial stock', NULL),
(3, 3, 'in', 1000, '2024-01-01 09:00:00', 'Initial stock', NULL),
(4, 4, 'in', 100, '2024-01-01 09:00:00', 'Initial stock', NULL),
(5, 5, 'in', 500, '2024-01-01 09:00:00', 'Initial stock', NULL),
(6, 1, 'out', 50, '2024-01-15 10:00:00', 'Used for patient prescriptions', NULL),
(7, 2, 'out', 100, '2024-01-15 10:00:00', 'Daily usage', NULL),
(8, 3, 'out', 25, '2024-01-15 10:00:00', 'Emergency department usage', NULL),
(9, 6, 'in', 1000, '2024-01-02 09:00:00', 'Restock', NULL),
(10, 7, 'in', 50, '2024-01-02 09:00:00', 'Restock', NULL),
(11, 10, 'out', 5, '2025-06-30 12:18:36', 'Withdrawal #9 completed', NULL),
(12, 4, 'out', 5, '2025-06-30 12:18:36', 'Withdrawal #9 completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_withdrawals`
--

CREATE TABLE `inventory_withdrawals` (
  `withdrawal_id` int(11) NOT NULL,
  `withdrawal_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_withdrawals`
--

INSERT INTO `inventory_withdrawals` (`withdrawal_id`, `withdrawal_date`, `notes`, `performed_by`, `status`) VALUES
(1, '2024-01-15 10:00:00', 'Daily medical supplies withdrawal', NULL, 'completed'),
(2, '2024-01-16 10:00:00', 'Emergency department supplies', NULL, 'completed'),
(3, '2024-01-17 10:00:00', 'Surgical department supplies', NULL, 'completed'),
(4, '2024-01-18 10:00:00', 'Laboratory supplies', NULL, 'completed'),
(5, '2024-01-19 10:00:00', 'General ward supplies', NULL, 'completed'),
(6, '2025-06-30 12:08:00', 'asdasd', NULL, 'completed'),
(7, '2025-06-30 12:09:00', 'ss', NULL, 'completed'),
(8, '2025-06-30 12:14:00', 'sds', NULL, 'completed'),
(9, '2025-06-30 12:18:00', 'sss', NULL, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_withdrawal_items`
--

CREATE TABLE `inventory_withdrawal_items` (
  `withdrawal_item_id` int(11) NOT NULL,
  `withdrawal_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_withdrawal_items`
--

INSERT INTO `inventory_withdrawal_items` (`withdrawal_item_id`, `withdrawal_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 50),
(2, 1, 2, 100),
(3, 1, 3, 25),
(4, 2, 2, 50),
(5, 2, 3, 15),
(6, 2, 6, 100),
(7, 3, 9, 5),
(8, 3, 5, 20),
(9, 4, 13, 1),
(11, 5, 8, 10),
(12, 5, 8, 10),
(17, 6, 10, 5),
(18, 6, 4, 5),
(19, 7, 10, 20),
(20, 7, 4, 40),
(21, 8, 10, 5),
(22, 8, 4, 5),
(23, 9, 10, 5),
(24, 9, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `subjective` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,2) DEFAULT NULL,
  `blood_pressure` varchar(100) NOT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `temperature_c` decimal(4,2) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `lab_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`lab_images`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `record_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','archived') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `patient_id`, `doctor_id`, `diagnosis`, `treatment`, `subjective`, `objective`, `assessment`, `plan`, `height_cm`, `weight_kg`, `bmi`, `blood_pressure`, `heart_rate`, `temperature_c`, `respiratory_rate`, `lab_images`, `created_at`, `updated_at`, `record_date`, `status`) VALUES
(12, 1, 1, 'Hypertension', 'Lifestyle modification, antihypertensive medication', 'Headache, dizziness', 'BP 150/95, HR 80', 'Stage 1 hypertension', '0', 170.00, 75.00, 25.95, '150', 80, 36.00, 16, NULL, '2025-07-12 03:12:39', '2025-07-12 03:51:29', '2024-06-01 09:00:00', 'active'),
(13, 1, 1, 'Diabetes Mellitus', 'Metformin, diet control', 'Increased thirst, urination', 'BMI 28, BP 130/85', 'Type 2 diabetes', '0', 165.00, 76.00, 27.96, '130', 78, 36.00, 18, NULL, '2025-07-12 03:12:39', '2025-07-12 03:51:36', '2024-06-02 10:30:00', 'active'),
(14, 1, 1, 'Asthma', 'Inhaled corticosteroids', 'Shortness of breath, wheezing', 'Expiratory wheeze', 'Mild persistent asthma', '0', 160.00, 60.00, 23.44, '120', 85, 36.00, 20, NULL, '2025-07-12 03:12:39', '2025-07-12 03:51:42', '2024-06-03 14:15:00', 'active'),
(15, 1, 1, 'Hypertension', 'Lifestyle modification, antihypertensive medication', 'Headache, dizziness', 'BP 150/95, HR 80', 'Stage 1 hypertension', '0', 170.00, 75.00, 25.95, '150', 80, 36.00, 16, NULL, '2025-07-12 03:14:20', '2025-07-12 03:51:52', '2024-06-01 09:00:00', 'active'),
(16, 1, 1, 'Diabetes Mellitus', 'Metformin, diet control', 'Increased thirst, urination', 'BMI 28, BP 130/85', 'Type 2 diabetes', '0', 165.00, 76.00, 27.96, '130', 78, 36.00, 18, NULL, '2025-07-12 03:14:20', '2025-07-12 03:51:57', '2024-06-02 10:30:00', 'active'),
(17, 1, 1, 'Asthma', 'Inhaled corticosteroids', 'Shortness of breath, wheezing', 'Expiratory wheeze', 'Mild persistent asthma', '0', 160.00, 60.00, 23.44, '120', 85, 36.00, 20, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:05', '2024-06-03 14:15:00', 'active'),
(18, 1, 1, 'Acute Bronchitis', 'Rest, fluids, cough suppressant', 'Cough, chest discomfort', 'Mild fever, rhonchi', 'Viral bronchitis', '0', 172.00, 68.00, 22.99, '118', 88, 37.00, 19, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:13', '2024-06-04 11:00:00', 'active'),
(19, 1, 1, 'Migraine', 'NSAIDs, rest, hydration', 'Severe headache, photophobia', 'No focal deficits', 'Migraine without aura', '0', 168.00, 62.00, 21.97, '122', 75, 36.00, 15, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:27', '2024-06-05 13:45:00', 'active'),
(20, 1, 1, 'Pneumonia', 'Antibiotics, oxygen therapy', 'Fever, productive cough', 'Crackles, tachypnea', 'Community-acquired pneumonia', '0', 175.00, 80.00, 26.12, '125', 95, 38.00, 24, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:35', '2024-06-06 08:30:00', 'active'),
(21, 1, 1, 'Chronic Kidney Disease', 'ACE inhibitors, dietary modification', 'Fatigue, swelling', 'Edema, hypertension', 'Stage 3 CKD', '0', 158.00, 70.00, 28.03, '140', 82, 36.00, 17, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:42', '2024-06-07 10:00:00', 'active'),
(22, 1, 1, 'Gastritis', 'Proton pump inhibitors', 'Epigastric pain, nausea', 'Mild tenderness', 'Acute gastritis', '0', 162.00, 58.00, 22.10, '115', 70, 36.00, 14, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:50', '2024-06-08 16:20:00', 'active'),
(23, 1, 1, 'Anemia', 'Iron supplements, dietary advice', 'Fatigue, pallor', 'Pale conjunctiva', 'Iron deficiency anemia', '0', 155.00, 50.00, 20.81, '110', 72, 36.00, 15, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:56', '2024-06-09 09:10:00', 'active'),
(24, 1, 1, 'Hyperthyroidism', 'Antithyroid drugs', 'Weight loss, palpitations', 'Tremor, tachycardia', 'Graves disease', '0', 163.00, 54.00, 20.33, '128', 98, 37.00, 18, NULL, '2025-07-12 03:14:20', '2025-07-12 03:52:20', '2024-06-10 12:00:00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `medicine_name` varchar(100) NOT NULL,
  `dosage_form` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `medicine_name`, `dosage_form`, `strength`) VALUES
(1, 'Aspirin', 'Tablet', '81mg'),
(2, 'Ibuprofen', 'Tablet', '400mg'),
(3, 'Acetaminophen', 'Tablet', '500mg'),
(4, 'Amoxicillin', 'Capsule', '500mg'),
(5, 'Omeprazole', 'Capsule', '20mg'),
(6, 'Metformin', 'Tablet', '500mg'),
(7, 'Lisinopril', 'Tablet', '10mg'),
(8, 'Atorvastatin', 'Tablet', '20mg'),
(9, 'Albuterol', 'Inhaler', '90mcg'),
(10, 'Sertraline', 'Tablet', '50mg'),
(11, 'Lorazepam', 'Tablet', '1mg'),
(12, 'Morphine', 'Injection', '10mg/mL'),
(13, 'Insulins', 'Injection', '100 units/mL'),
(14, 'Warfarin', 'Tablet', '5mg'),
(15, 'Furosemide', 'Tablet', '40mg');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_relationship` enum('parent','spouse','sibling','child','friend','other') DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_address` text DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_relationship` enum('parent','guardian','grandparent','aunt_uncle','foster_parent','other') DEFAULT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','deceased') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `contact_number`, `email`, `address`, `medical_history`, `emergency_contact_name`, `emergency_contact_relationship`, `emergency_contact_phone`, `emergency_contact_address`, `guardian_name`, `guardian_relationship`, `guardian_phone`, `guardian_address`, `profile_picture`, `status`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Michael', 'Smith', '1985-03-15', 'male', '+1234567890', 'john.smith@email.com', '123 Oak Street, City, State 12345', 'Hypertension, Diabetes Type 2', 'Jane Smith', 'spouse', '+1234567899', '123 Oak Street, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(2, 'Sarah', 'Elizabeth', 'Johnson', '1992-07-22', 'female', '+1234567891', 'sarah.johnson@email.com', '456 Maple Avenue, City, State 12345', 'Asthma', 'Robert Johnson', 'parent', '+1234567800', '456 Maple Avenue, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(3, 'Michael', 'David', 'Brown', '1978-11-08', 'male', '+1234567892', 'michael.brown@email.com', '789 Pine Road, City, State 12345', 'Heart condition', 'Susan Brown', 'spouse', '+1234567801', '789 Pine Road, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(4, 'Emily', 'Grace', 'Davis', '1995-04-30', 'female', '+1234567893', 'emily.davis@email.com', '321 Elm Street, City, State 12345', 'None', 'Thomas Davis', 'parent', '+1234567802', '321 Elm Street, City, State 12345', 'Thomas Davis', 'parent', '+1234567802', '321 Elm Street, City, State 12345', NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(5, 'Robert', 'James', 'Wilson', '1980-09-12', 'male', '+1234567894', 'robert.wilson@email.com', '654 Cedar Lane, City, State 12345', 'High cholesterol', 'Patricia Wilson', 'spouse', '+1234567803', '654 Cedar Lane, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(6, 'Lisa', 'Marie', 'Anderson', '1988-12-05', 'female', '+1234567895', 'lisa.anderson@email.com', '987 Birch Drive, City, State 12345', 'Migraine', 'Mark Anderson', 'spouse', '+1234567804', '987 Birch Drive, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(7, 'David', 'Thomas', 'Taylor', '1975-06-18', 'male', '+1234567896', 'david.taylor@email.com', '147 Willow Way, City, State 12345', 'Back pain', 'Carol Taylor', 'spouse', '+1234567805', '147 Willow Way, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(8, 'Jennifer', 'Ann', 'Martinez', '1990-01-25', 'female', '+1234567897', 'jennifer.martinez@email.com', '258 Spruce Court, City, State 12345', 'Anxiety', 'Luis Martinez', 'spouse', '+1234567806', '258 Spruce Court, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(9, 'Christopher', 'Lee', 'Garcia', '1983-08-14', 'male', '+1234567898', 'christopher.garcia@email.com', '369 Ash Place, City, State 12345', 'Sleep apnea', 'Maria Garcia', 'spouse', '+1234567807', '369 Ash Place, City, State 12345', NULL, NULL, NULL, NULL, NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 13:59:44'),
(10, 'Amandas', 'Rose', 'Rodriguez', '1987-05-20', 'female', '+1234567899', 'amanda.rodriguez@email.com', '741 Poplar Street, City, State 12345', 'None', 'jerry asna', 'spouse', '09947497747', 'zone 5 gango libona bukidnon', 'Lita Solis', 'parent', '09947497747', 'Teodoro N. Pepito Street', NULL, 'active', '2025-06-29 07:29:29', '2025-06-29 14:05:24');

-- --------------------------------------------------------

--
-- Table structure for table `patient_insurance`
--

CREATE TABLE `patient_insurance` (
  `patient_insurance_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `insurance_provider_id` int(11) DEFAULT NULL,
  `insurance_number` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_insurance`
--

INSERT INTO `patient_insurance` (`patient_insurance_id`, `patient_id`, `insurance_provider_id`, `insurance_number`, `status`) VALUES
(1, 1, 1, 'BCBS123456789', 'active'),
(2, 2, 2, 'AETNA987654321', 'active'),
(3, 3, 3, 'CIGNA456789123', 'active'),
(4, 4, 4, 'UNITED789123456', 'active'),
(5, 5, 5, 'HUMANA321654987', 'active'),
(6, 6, 1, 'BCBS654321987', 'active'),
(7, 7, 2, 'AETNA123789456', 'active'),
(8, 8, 3, 'CIGNA987321654', 'active'),
(9, 10, 4, 'UNITED456987321', 'active'),
(10, 10, 5, 'HUMANA789456123', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `prescription_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `status` enum('active','cancelled','fulfilled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`prescription_id`, `patient_id`, `doctor_id`, `prescription_date`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-01-15 00:00:00', 'For hypertension and diabetes management', 'active', '2025-06-29 07:29:29', '2025-06-30 03:33:12'),
(2, 2, 2, '2024-01-15 11:30:00', 'For migraine prevention', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(3, 3, 1, '2024-01-15 14:30:00', 'For heart condition management', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(4, 5, 4, '2024-01-16 11:00:00', 'Post-operative pain management', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(5, 6, 5, '2024-01-16 13:30:00', 'Pre-operative medication', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(6, 7, 6, '2024-01-17 08:30:00', 'For back pain management', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(7, 8, 7, '2024-01-17 11:30:00', 'For cancer treatment', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(8, 9, 8, '2024-01-17 15:30:00', 'For anxiety management', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(9, 1, 1, '2024-01-18 14:00:00', 'Follow-up prescription adjustment', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29'),
(10, 2, 2, '2024-01-19 10:00:00', 'Treatment continuation', 'active', '2025-06-29 07:29:29', '2025-06-29 07:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `prescription_item_id` int(11) NOT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `inventory_item_id` int(11) DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`prescription_item_id`, `prescription_id`, `medicine_id`, `inventory_item_id`, `dosage`, `frequency`, `duration_days`, `quantity`, `instructions`) VALUES
(3, 2, 10, NULL, '50mg', 'Once daily', 30, 30, 'Take in the morning'),
(4, 3, 1, NULL, '81mg', 'Once daily', 30, 30, 'Take with food'),
(5, 3, 8, NULL, '20mg', 'Once daily', 30, 30, 'Take at bedtime'),
(6, 4, 12, NULL, '10mg', 'As needed', 7, 10, 'For severe pain only'),
(7, 5, 4, NULL, '500mg', 'Three times daily', 7, 21, 'Take before meals'),
(8, 6, 11, NULL, '1mg', 'As needed', 30, 30, 'For anxiety'),
(9, 7, 5, NULL, '20mg', 'Once daily', 30, 30, 'Take before breakfast'),
(10, 8, 9, NULL, '90mcg', 'As needed', 30, 1, 'Inhale as directed'),
(18, 1, 6, NULL, '500mg', 'Twice daily', 30, 60, 'Take with meals'),
(19, 1, 7, NULL, '10mg', 'Once daily', 30, 30, 'Take in the morning'),
(20, 1, 14, NULL, 'dsd', 'Once daily', 2, 2, 'dsd'),
(21, 1, 10, NULL, 'dd', 'Twice daily', 22, 2, 'sdsad');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'doctor'),
(4, 'nurse'),
(3, 'receptionist');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `log_timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `user_id`, `action`, `log_timestamp`) VALUES
(1, 1, 'User logged in successfully', '2025-06-29 12:48:17'),
(2, 1, 'Profile updated', '2025-06-29 13:14:51'),
(3, 1, 'User logged out', '2025-06-29 13:38:24'),
(4, 1, 'User logged in successfully', '2025-06-29 13:38:34'),
(5, 1, 'User updated profile information', '2025-06-29 15:17:24'),
(6, NULL, 'Database seeded with sample data', '2024-01-01 00:00:00'),
(7, NULL, 'System initialization completed', '2024-01-01 00:00:00'),
(8, NULL, 'Sample appointments created', '2024-01-01 00:00:00'),
(9, NULL, 'Sample patients added to system', '2024-01-01 00:00:00'),
(10, NULL, 'Sample doctors added to system', '2024-01-01 00:00:00'),
(11, NULL, 'Inventory items added to system', '2024-01-01 00:00:00'),
(12, NULL, 'Medical records created', '2024-01-01 00:00:00'),
(13, NULL, 'Prescriptions generated', '2024-01-01 00:00:00'),
(14, NULL, 'Billing records created', '2024-01-01 00:00:00'),
(15, NULL, 'Insurance providers added', '2024-01-01 00:00:00'),
(16, 1, 'User logged in', '2025-06-29 16:11:07'),
(17, 1, 'Profile Updated', '2025-06-29 17:41:49'),
(18, 1, 'Profile Updated', '2025-06-29 17:42:02'),
(19, 1, 'Profile Updated', '2025-06-29 17:42:43'),
(20, 1, 'Profile Updated', '2025-06-29 17:42:52'),
(21, 1, 'User logged in', '2025-06-29 17:43:50'),
(22, 1, 'Profile updated', '2025-06-29 17:52:25'),
(23, 1, 'Profile updated', '2025-06-29 17:52:40'),
(24, 1, 'Profile updated', '2025-06-29 17:53:34'),
(25, 1, 'Profile updated', '2025-06-29 17:53:38'),
(26, 1, 'Profile updated', '2025-06-29 17:53:47'),
(27, 1, 'Password changed', '2025-06-29 17:55:17'),
(28, 1, 'User logged out', '2025-06-29 17:58:49'),
(29, 1, 'User logged in', '2025-06-29 17:59:11'),
(30, 1, 'Password changed', '2025-06-29 17:59:27'),
(31, 1, 'Profile updated', '2025-06-29 17:59:33'),
(32, 1, 'Profile updated', '2025-06-29 18:07:53'),
(33, 1, 'Updated user: stephanie.nurse', '2025-06-29 18:48:40'),
(34, 1, 'Created new user: admin', '2025-06-29 18:54:02'),
(35, 1, 'Updated department: Orthopedic', '2025-06-29 19:14:00'),
(36, 1, 'Created department: s', '2025-06-29 19:14:05'),
(37, 1, 'Deleted department: s', '2025-06-29 19:14:08'),
(38, 1, 'Updated insurance provider: Aetnas', '2025-06-29 19:34:54'),
(39, 1, 'Created insurance provider: s', '2025-06-29 19:34:59'),
(40, 1, 'Deleted insurance provider: s', '2025-06-29 19:35:04'),
(41, 16, 'User logged in', '2025-06-29 20:01:32'),
(42, 16, 'Updated insurance record for Amanda Rodriguez with UnitedHealth Group', '2025-06-29 20:02:01'),
(43, 16, 'Updated department: Cardiologys', '2025-06-29 21:17:41'),
(44, 16, 'Updated patient: Amandas Rose Rodriguez', '2025-06-29 21:21:45'),
(45, 16, 'Created patient: arman  barliso', '2025-06-29 21:21:57'),
(46, 16, 'Deleted patient: arman  barliso', '2025-06-29 21:22:13'),
(47, 16, 'Updated patient: Amandas Rose Rodriguez', '2025-06-29 22:05:24'),
(48, 16, 'User logged in', '2025-06-30 07:17:35'),
(49, 16, 'User logged in', '2025-06-30 10:33:34'),
(50, 16, 'Updated inventory category: Cleaning Supplies to Cleaning Suppliess', '2025-06-30 11:44:24'),
(51, 16, 'Updated inventory category: Cleaning Suppliess to Cleaning Supplies', '2025-06-30 11:44:28'),
(52, 16, 'Created new inventory category: ss', '2025-06-30 11:44:30'),
(53, 16, 'Deleted inventory category: ss', '2025-06-30 11:44:37'),
(54, 16, 'Updated inventory item: Aspirin 81mg to Aspirin 81mg', '2025-06-30 11:53:17'),
(55, 16, 'Created new inventory item: dfdf', '2025-06-30 11:53:28'),
(56, 16, 'Deleted inventory item: dfdf', '2025-06-30 11:53:32'),
(57, NULL, 'Updated withdrawal ID: 5', '2025-06-30 12:04:43'),
(58, NULL, 'Created withdrawal ID: 6', '2025-06-30 12:08:58'),
(59, NULL, 'Updated withdrawal ID: 6', '2025-06-30 12:09:08'),
(60, NULL, 'Updated withdrawal ID: 6', '2025-06-30 12:09:16'),
(61, NULL, 'Created withdrawal ID: 7', '2025-06-30 12:10:06'),
(62, NULL, 'Created withdrawal ID: 8', '2025-06-30 12:14:54'),
(63, NULL, 'Created and completed withdrawal ID: 9 - inventory updated', '2025-06-30 12:18:36'),
(64, 16, 'User logged in', '2025-07-01 07:18:10'),
(65, 16, 'User logged out', '2025-07-01 16:03:06'),
(66, 3, 'User logged in', '2025-07-01 16:03:35'),
(67, 3, 'User logged out', '2025-07-01 16:04:22'),
(68, 8, 'User logged in', '2025-07-01 16:04:49'),
(69, 8, 'User logged out', '2025-07-01 16:28:55'),
(70, 1, 'User logged in', '2025-07-12 09:16:03'),
(71, 1, 'User logged out', '2025-07-12 10:58:28'),
(72, 1, 'User logged in', '2025-07-12 10:58:29'),
(73, 1, 'Created patient: 123 123 123', '2025-07-12 11:20:44'),
(74, 1, 'Deleted patient: 123 123 123', '2025-07-12 11:20:50'),
(75, 1, 'User logged out', '2025-07-12 12:52:49'),
(76, 1, 'User logged in', '2025-07-12 12:54:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `phone_number`, `address`, `username`, `password`, `role_id`, `profile_picture`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Sarah Johnsonss', '+1-555-010111', '123 Medical Center Dr, Suite 100', 'sarah.admin', '$2y$10$Pn/axMcr9W4yc7t7G.av4Onc0QKSl2R1ETY6cvqpTUNNrR2UJTc1.', 1, 'uploads/profile_pictures/profile_1_1751191673.jpg', 'active', '2025-06-29 04:29:34', '2025-06-29 10:07:53'),
(2, 'Michael Chen', '+1-555-0102', '456 Healthcare Ave, Floor 2', 'michael.admin', '$2y$12$dVBzLDlhzX8VmDhIpgGRHuNMUcZEqPqfWZWkHIZJyC0RibaEbQAcO', 1, '', 'active', '2025-06-29 04:29:34', '2025-06-29 04:29:34'),
(3, 'Dr. Emily Rodriguez', '+1-555-0201', '789 Cardiology Lane', 'emily.doctor', '$2y$12$mzhPYpIr0Q/ZJQvNS4/Mwe9g5wBoN4sNb.boajsXgl1Fvc1GRUMWm', 2, '', 'active', '2025-06-29 04:29:34', '2025-06-29 04:29:34'),
(4, 'Dr. James Wilson', '+1-555-0202', '321 Neurology Street', 'james.doctor', '$2y$12$LUs789fSOyJHFOS/OUOCK.kaR.2Q0qMXa3qsMpeoKOBWiukSEJEyO', 2, '', 'active', '2025-06-29 04:29:35', '2025-06-29 04:29:35'),
(5, 'Dr. Lisa Thompson', '+1-555-0203', '654 Pediatrics Road', 'lisa.doctor', '$2y$12$KW0ELbwlKVNi7FiU0q8Ysu4Cp5tm4H1K.Wp9ZEOF5AWdcKK.co1f.', 2, '', 'active', '2025-06-29 04:29:35', '2025-06-29 04:29:35'),
(6, 'Dr. Robert Kim', '+1-555-0204', '987 Surgery Boulevard', 'robert.doctor', '$2y$12$VaybA5snKLr8dQ5iGe8KRuHz2kWoqCSPsyzu9X9muxSvSNJFj3XGG', 2, '', 'active', '2025-06-29 04:29:35', '2025-06-29 04:29:35'),
(7, 'Dr. Maria Garcia', '+1-555-0205', '147 Emergency Way', 'maria.doctor', '$2y$12$h9uCXEBuA14UPxQJZTxseuqCZn9v5QOgITM5Yed0AcrHUFOJEQ4F2', 2, '', 'active', '2025-06-29 04:29:35', '2025-06-29 04:29:35'),
(8, 'Jennifer Davis', '+1-555-0301', '258 Front Desk Plaza', 'jennifer.reception', '$2y$12$zwgY3wTLDOXU3GznKmyZD.hzP0q7484E2QTETvFYNmMy3EEI3G7Cm', 3, '', 'active', '2025-06-29 04:29:35', '2025-06-29 04:29:35'),
(9, 'David Martinez', '+1-555-0302', '369 Patient Services Ave', 'david.reception', '$2y$12$9cN/EVypWC9PMdc0G7g/6.6gYjOBQT1TX6KqgIO06vNtAfhFarEQq', 3, '', 'active', '2025-06-29 04:29:35', '2025-06-29 04:29:35'),
(10, 'Amanda Foster', '+1-555-0303', '741 Appointment Center Dr', 'amanda.reception', '$2y$12$AeSsNG/lgZ.Narn0mlqihuWxfA.7ulDZ3kOn/D1CH52ACyyHicKMa', 3, '', 'active', '2025-06-29 04:29:36', '2025-06-29 04:29:36'),
(11, 'Nurse Patricia Brown', '+1-555-0401', '852 Nursing Station Rd', 'patricia.nurse', '$2y$12$nQ1qEg94JmgpiPvBrhy/Zu1lOwe5AytjqqRHsne2X097V9dsq395C', 4, '', 'active', '2025-06-29 04:29:36', '2025-06-29 04:29:36'),
(12, 'Nurse Kevin Lee', '+1-555-0402', '963 ICU Care Lane', 'kevin.nurse', '$2y$12$b8UXksm.9oSnI5WmGEndgu2ns.gRl9FR/SHzDDUa/ot9gBQB9zFjW', 4, '', 'active', '2025-06-29 04:29:36', '2025-06-29 04:29:36'),
(13, 'Nurse Rachel Green', '+1-555-0403', '159 ER Department St', 'rachel.nurse', '$2y$12$1QwXMdFhNGYar9fMKANXt.9Yq1i77Nbg0uCTN2ZBJOnO3kABoz8vy', 4, '', 'active', '2025-06-29 04:29:36', '2025-06-29 04:29:36'),
(14, 'Nurse Thomas Anderson', '+1-555-0404', '753 Ward Management Ave', 'thomas.nurse', '$2y$12$XU8HSlj6zU.lDkw7ML9/XuuI6MzQba7H6c8kIOvNNLUGqC951wMn.', 4, '', 'active', '2025-06-29 04:29:36', '2025-06-29 04:29:36'),
(15, 'Nurse Stephanie White', '+1-555-0405', '951 Patient Care Blvds', 'stephanie.nurse', '$2y$12$imbJVtRRgsLDID3Fl4zwbO/JK5X60YIw.aobDeHCPik52YyTQ9igK', 4, '', 'active', '2025-06-29 04:29:37', '2025-06-29 10:48:40'),
(16, 'ALLAN JAMES CASONA DAUMAR', '09916390404', 'Pepito Street, Poblacion, Valencia City, Bukidnon\nIBA COLLEGE OF MINDANAO, INC. Building', 'admin', '$2y$10$twOxx/op3ghaz1WSNU8vDOjpJv78ir85ODienO/B5C3r/PVTItqsO', 3, NULL, 'active', '2025-06-29 10:54:02', '2025-06-29 10:54:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `fk_appointments_patient` (`patient_id`),
  ADD KEY `fk_appointments_doctor` (`doctor_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `fk_billing_patient` (`patient_id`),
  ADD KEY `fk_billing_appointment` (`appointment_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `uk_departments_name` (`department_name`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `fk_doctors_user` (`user_id`),
  ADD KEY `fk_doctors_department` (`department_id`);

--
-- Indexes for table `insurance_providers`
--
ALTER TABLE `insurance_providers`
  ADD PRIMARY KEY (`insurance_provider_id`),
  ADD UNIQUE KEY `uk_insurance_provider_name` (`provider_name`);

--
-- Indexes for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `uk_inventory_categories_name` (`category_name`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `uk_inventory_items_serial_number` (`serial_number`),
  ADD KEY `fk_inventory_items_category` (`category_id`);

--
-- Indexes for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`movement_id`),
  ADD KEY `fk_inventory_movements_item` (`item_id`),
  ADD KEY `fk_inventory_movements_user` (`performed_by`);

--
-- Indexes for table `inventory_withdrawals`
--
ALTER TABLE `inventory_withdrawals`
  ADD PRIMARY KEY (`withdrawal_id`),
  ADD KEY `fk_inventory_withdrawals_user` (`performed_by`);

--
-- Indexes for table `inventory_withdrawal_items`
--
ALTER TABLE `inventory_withdrawal_items`
  ADD PRIMARY KEY (`withdrawal_item_id`),
  ADD KEY `fk_inventory_withdrawal_items_withdrawal` (`withdrawal_id`),
  ADD KEY `fk_inventory_withdrawal_items_item` (`item_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `fk_medical_records_patient` (`patient_id`),
  ADD KEY `fk_medical_records_doctor` (`doctor_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD UNIQUE KEY `uk_medicines_name` (`medicine_name`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `patient_insurance`
--
ALTER TABLE `patient_insurance`
  ADD PRIMARY KEY (`patient_insurance_id`),
  ADD KEY `fk_patient_insurance_patient` (`patient_id`),
  ADD KEY `fk_patient_insurance_provider` (`insurance_provider_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `fk_prescriptions_patient` (`patient_id`),
  ADD KEY `fk_prescriptions_doctor` (`doctor_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`prescription_item_id`),
  ADD KEY `fk_prescription_items_prescription` (`prescription_id`),
  ADD KEY `fk_prescription_items_medicine` (`medicine_id`),
  ADD KEY `fk_prescription_items_inventory_item` (`inventory_item_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `uk_roles_role_name` (`role_name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sessions_user` (`user_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_system_logs_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uk_users_username` (`username`),
  ADD KEY `fk_users_roles` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `insurance_providers`
--
ALTER TABLE `insurance_providers`
  MODIFY `insurance_provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `inventory_withdrawals`
--
ALTER TABLE `inventory_withdrawals`
  MODIFY `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory_withdrawal_items`
--
ALTER TABLE `inventory_withdrawal_items`
  MODIFY `withdrawal_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `patient_insurance`
--
ALTER TABLE `patient_insurance`
  MODIFY `patient_insurance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `prescription_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `fk_billing_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_billing_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctors_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_doctors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `fk_inventory_items_category` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `fk_inventory_movements_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inventory_movements_user` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_withdrawals`
--
ALTER TABLE `inventory_withdrawals`
  ADD CONSTRAINT `fk_inventory_withdrawals_user` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_withdrawal_items`
--
ALTER TABLE `inventory_withdrawal_items`
  ADD CONSTRAINT `fk_inventory_withdrawal_items_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inventory_withdrawal_items_withdrawal` FOREIGN KEY (`withdrawal_id`) REFERENCES `inventory_withdrawals` (`withdrawal_id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `fk_medical_records_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_medical_records_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_insurance`
--
ALTER TABLE `patient_insurance`
  ADD CONSTRAINT `fk_patient_insurance_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_patient_insurance_provider` FOREIGN KEY (`insurance_provider_id`) REFERENCES `insurance_providers` (`insurance_provider_id`) ON DELETE SET NULL;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_prescriptions_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prescriptions_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE SET NULL;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `fk_prescription_items_inventory_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prescription_items_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prescription_items_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`prescription_id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `fk_system_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
