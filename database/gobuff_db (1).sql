-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 04:12 PM
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
-- Database: `gobuff_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'User logged in', '::1', '2026-05-08 08:55:24'),
(2, 1, 'password_change', 'User changed password', '::1', '2026-05-08 08:55:57'),
(3, 1, 'checkin', 'Member 7749 checked in', '::1', '2026-05-08 09:22:57'),
(4, 1, 'user_update', 'Updated user ID: 1, role: gym_owner', '::1', '2026-05-08 09:41:45'),
(5, 1, 'login', 'User logged in', '::1', '2026-05-08 10:16:05'),
(6, 1, 'document_upload', 'Uploaded legal document: Business Permit', '::1', '2026-05-08 10:36:33'),
(7, 1, 'budget_create', 'Created budget plan: Equipment Budget', '::1', '2026-05-08 10:38:26'),
(8, 1, 'expense_create', 'Recorded expense: Monthly Electricity Bill - ₱750', '::1', '2026-05-08 10:39:35'),
(9, 1, 'expense_approve', 'Approved expense ID: 1', '::1', '2026-05-08 10:39:43'),
(10, 1, 'budget_approve', 'Approved budget plan ID: 1', '::1', '2026-05-08 10:39:55'),
(11, 1, 'expense_create', 'Recorded expense: Equipment bill - ₱5000', '::1', '2026-05-08 10:41:44'),
(12, 1, 'expense_approve', 'Approved expense ID: 2', '::1', '2026-05-08 10:41:52'),
(13, 1, 'expense_update', 'Updated expense: Equipment bill', '::1', '2026-05-08 10:43:32'),
(14, 1, 'maintenance_report', 'Maintenance reported for equipment ID: 3', '::1', '2026-05-08 10:44:35'),
(15, 1, 'logout', 'User logged out', '::1', '2026-05-08 10:51:03'),
(16, 1, 'login', 'User logged in', '::1', '2026-05-08 10:59:56'),
(17, 1, 'logout', 'User logged out', '::1', '2026-05-08 11:06:33'),
(18, 7, 'register', 'New member registered', '::1', '2026-05-08 11:24:57'),
(19, 8, 'register', 'New member registered', '::1', '2026-05-08 11:26:13'),
(20, 9, 'register', 'New member registered', '::1', '2026-05-08 11:31:20'),
(21, 9, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:31:41'),
(22, 9, 'logout', 'User logged out', '::1', '2026-05-08 11:32:38'),
(23, 1, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:35:26'),
(24, 1, 'logout', 'User logged out', '::1', '2026-05-08 11:35:42'),
(25, 9, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:38:42'),
(26, 9, 'logout', 'User logged out', '::1', '2026-05-08 11:40:36'),
(27, 10, 'register', 'New member registered', '::1', '2026-05-08 11:41:27'),
(28, 10, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:41:48'),
(29, 10, 'logout', 'User logged out', '::1', '2026-05-08 11:42:35'),
(30, 1, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:42:58'),
(31, 1, 'logout', 'User logged out', '::1', '2026-05-08 11:45:33'),
(32, 2, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:46:09'),
(33, 2, 'logout', 'User logged out', '::1', '2026-05-08 11:48:02'),
(34, 1, 'login', 'User logged in (OTP verified)', '::1', '2026-05-08 11:48:30'),
(35, 1, 'logout', 'User logged out', '::1', '2026-05-08 11:50:24'),
(36, 11, 'register', 'New member registered', '::1', '2026-05-11 04:52:35'),
(37, 11, 'login', 'User logged in (OTP verified)', '::1', '2026-05-11 04:52:55'),
(38, 11, 'logout', 'User logged out', '::1', '2026-05-11 04:53:13'),
(39, 12, 'register', 'New member registered', '::1', '2026-05-11 04:56:23'),
(40, 12, 'login', 'User logged in (OTP verified)', '::1', '2026-05-11 04:56:58'),
(41, 12, 'logout', 'User logged out', '::1', '2026-05-11 04:58:06'),
(42, 2, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 06:08:07'),
(43, 2, 'notification_send', 'Sent notification: Membership EXPIRY', '::1', '2026-05-13 06:33:52'),
(44, 2, 'member_delete', 'Deleted member ID: 6', '::1', '2026-05-13 06:33:59'),
(45, 2, 'member_delete', 'Deleted member ID: 5', '::1', '2026-05-13 06:34:01'),
(46, 2, 'member_delete', 'Deleted member ID: 4', '::1', '2026-05-13 06:34:04'),
(47, 2, 'member_delete', 'Deleted member ID: 3', '::1', '2026-05-13 06:34:06'),
(48, 2, 'member_delete', 'Deleted member ID: 2', '::1', '2026-05-13 06:34:08'),
(49, 2, 'logout', 'User logged out', '::1', '2026-05-13 06:34:34'),
(50, 12, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 06:34:53'),
(51, 12, 'booking_create', 'Booking created for trainer ID: 1', '::1', '2026-05-13 06:35:56'),
(52, 12, 'logout', 'User logged out', '::1', '2026-05-13 06:37:25'),
(53, 3, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 06:38:00'),
(54, 3, 'logout', 'User logged out', '::1', '2026-05-13 06:38:11'),
(55, 4, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 06:39:44'),
(56, 4, 'logout', 'User logged out', '::1', '2026-05-13 07:13:58'),
(57, 5, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 07:14:27'),
(58, 5, 'logout', 'User logged out', '::1', '2026-05-13 07:32:45'),
(59, 13, 'register', 'New user registered', '::1', '2026-05-13 07:35:07'),
(60, 13, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 07:35:17'),
(61, 13, 'gym_owner_application', 'User applied to become Gym Owner (application #1)', '::1', '2026-05-13 07:40:18'),
(62, 13, 'logout', 'User logged out', '::1', '2026-05-13 07:42:53'),
(63, 13, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 07:44:07'),
(64, 13, 'gym_owner_auto_approve', 'Auto-approved Gym Owner application #2 (no existing owner)', '::1', '2026-05-13 07:46:50'),
(65, 13, 'logout', 'User logged out', '::1', '2026-05-13 07:50:14'),
(66, 13, 'login', 'User logged in (OTP verified)', '::1', '2026-05-13 07:50:57'),
(67, 13, 'logout', 'User logged out', '::1', '2026-05-13 08:04:08'),
(68, 5, 'login', 'User logged in (OTP verified)', '::1', '2026-05-15 07:23:57'),
(69, 13, 'login', 'User logged in (OTP verified)', '::1', '2026-05-15 07:27:03'),
(70, 13, 'equipment_create', 'Added equipment: Giga Dumbells', '::1', '2026-05-15 07:28:04'),
(71, 5, 'equipment_update', 'Updated equipment ID: 2', '::1', '2026-05-15 07:31:53'),
(72, 5, 'maintenance_report', 'Maintenance reported for equipment ID: 2', '::1', '2026-05-15 07:44:19'),
(73, 13, 'budget_create', 'Created budget plan: 2026 Operational Plan', '::1', '2026-05-15 07:47:41'),
(74, 13, 'budget_update', 'Updated budget plan: June Operational Plan', '::1', '2026-05-15 07:51:32'),
(75, 13, 'budget_approve', 'Approved budget plan ID: 2', '::1', '2026-05-15 08:15:38'),
(76, 13, 'expense_create', 'Recorded expense: Monthly Electricity Bill - ₱900', '::1', '2026-05-15 08:16:00'),
(77, 13, 'expense_approve', 'Approved expense ID: 3', '::1', '2026-05-15 08:16:07'),
(78, 13, 'expense_create', 'Recorded expense: Energy Drinks - ₱300', '::1', '2026-05-15 08:26:31'),
(79, 13, 'expense_approve', 'Approved expense ID: 4', '::1', '2026-05-15 08:26:37'),
(80, 13, 'login', 'User logged in (OTP verified)', '::1', '2026-05-15 08:27:27'),
(81, 13, 'expense_update', 'Updated expense: Energy Drinks', '::1', '2026-05-15 08:27:49'),
(82, 13, 'expense_update', 'Updated expense: Energy Drinks', '::1', '2026-05-15 08:28:18'),
(83, 13, 'expense_update', 'Updated expense: Energy Drinks', '::1', '2026-05-15 08:28:57'),
(84, 13, 'budget_delete', 'Deleted budget plan ID: 2', '::1', '2026-05-15 08:45:57'),
(85, 5, 'login', 'User logged in (OTP verified)', '::1', '2026-05-15 09:18:56'),
(86, 13, 'schedule_save', 'Saved work schedule sheet for date: 2026-05-15, 2 entries', '::1', '2026-05-15 09:23:05'),
(87, 14, 'register', 'New user registered', '::1', '2026-05-17 10:16:30'),
(88, 14, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 10:16:45'),
(89, 14, 'role_application', 'User applied for role: member', '::1', '2026-05-17 10:17:05'),
(90, 2, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 10:17:41'),
(91, 13, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 11:19:13'),
(92, 14, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 11:19:36'),
(93, 13, 'logout', 'User logged out', '::1', '2026-05-17 11:19:48'),
(94, 2, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 11:20:06'),
(95, 14, 'role_application', 'User applied for role: member', '::1', '2026-05-17 11:21:41'),
(96, 14, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 13:45:32'),
(97, 14, 'logout', 'User logged out', '::1', '2026-05-17 13:46:22'),
(98, 15, 'register', 'New user registered', '::1', '2026-05-17 13:46:53'),
(99, 15, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 13:47:02'),
(100, 2, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 13:47:14'),
(101, 15, 'role_application', 'User applied for role: member', '::1', '2026-05-17 13:48:12'),
(102, 2, 'member_app_approve', 'Admin approved membership application ID: 3', '::1', '2026-05-17 13:48:37'),
(103, 15, 'membership_checkout', 'User 15 initiated PayMongo checkout for membership ID: 2', '::1', '2026-05-17 13:55:40'),
(104, 0, 'membership_activated', 'Membership ID 2 activated via PayMongo.', '::1', '2026-05-17 13:56:17'),
(105, 15, 'logout', 'User logged out', '::1', '2026-05-17 13:59:32'),
(106, 15, 'login', 'User logged in (OTP verified)', '::1', '2026-05-17 14:00:04');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late','half_day','leave') DEFAULT 'present',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `time_in`, `time_out`, `status`, `notes`) VALUES
(1, 3, '2026-05-15', '16:51:55', '16:53:05', 'present', NULL),
(2, 2, '2026-05-15', '17:21:16', '17:21:19', 'present', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `budget_items`
--

CREATE TABLE `budget_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `budget_plan_id` int(10) UNSIGNED NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `allocated` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` tinyint(3) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_plans`
--

CREATE TABLE `budget_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `fiscal_year` year(4) NOT NULL,
  `period` enum('monthly','quarterly','semi_annual','annual') NOT NULL DEFAULT 'annual',
  `period_label` varchar(50) DEFAULT NULL,
  `total_budget` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('draft','approved','active','closed') NOT NULL DEFAULT 'draft',
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `target_audience` varchar(200) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(10,2) DEFAULT 0.00,
  `discount_pct` decimal(5,2) DEFAULT 0.00,
  `banner_image` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','active','inactive','completed') DEFAULT 'scheduled',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `title`, `description`, `target_audience`, `start_date`, `end_date`, `budget`, `discount_pct`, `banner_image`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Summer Fitness Promo', 'Get fit this summer! 20% off all monthly plans.', 'New Members', '2026-05-08', '2026-06-07', 0.00, 20.00, NULL, 'active', NULL, '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(2, 'Refer a Friend', 'Refer a friend and get 1 month free!', 'Existing Members', '2026-05-08', '2026-07-07', 0.00, 0.00, NULL, 'active', NULL, '2026-05-08 08:54:48', '2026-05-08 08:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `checkins`
--

CREATE TABLE `checkins` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `method` enum('manual','qr_code','card') DEFAULT 'manual',
  `status` enum('checked_in','checked_out') DEFAULT 'checked_in',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `checkins`
--

INSERT INTO `checkins` (`id`, `member_id`, `check_in_time`, `check_out_time`, `method`, `status`, `notes`) VALUES
(1, 1, '2026-05-08 17:22:57', '2026-05-08 19:38:54', 'manual', 'checked_out', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dietary_logs`
--

CREATE TABLE `dietary_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `log_date` date NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') NOT NULL,
  `food_items` text NOT NULL,
  `calories` decimal(7,2) DEFAULT 0.00,
  `protein` decimal(6,2) DEFAULT 0.00,
  `carbs` decimal(6,2) DEFAULT 0.00,
  `fat` decimal(6,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `job_role` varchar(80) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `specialization` varchar(200) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','resigned') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `first_name`, `last_name`, `job_role`, `department`, `specialization`, `phone`, `address`, `hire_date`, `salary`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'John', 'Trainer', 'trainer', 'Fitness', 'Strength & Conditioning', NULL, NULL, NULL, NULL, 'active', '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(2, 5, 'Mark', 'Santos', 'maintenance', 'Maintenance', 'Equipment Repair', NULL, NULL, NULL, NULL, 'active', '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(3, 13, 'Mr.', 'Owner', 'gym_owner', 'Management', NULL, NULL, NULL, NULL, NULL, 'active', '2026-05-13 07:46:50', '2026-05-13 07:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `condition_status` enum('good','needs_repair','under_maintenance') DEFAULT 'good',
  `last_maintenance_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `brand`, `model`, `serial_number`, `category`, `location`, `purchase_date`, `purchase_price`, `condition_status`, `last_maintenance_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Treadmill A1', 'LifeFitness', NULL, NULL, 'Cardio', 'Main Floor', NULL, NULL, 'good', NULL, NULL, '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(2, 'Bench Press Rack', 'Hammer', '', NULL, 'Strength', 'Weight Room', NULL, NULL, 'good', '2026-05-15', 'Nabali ang butanganan', '2026-05-08 08:54:48', '2026-05-15 07:44:42'),
(3, 'Rowing Machine', 'Concept2', NULL, NULL, 'Cardio', 'Main Floor', NULL, NULL, 'good', '2026-05-08', NULL, '2026-05-08 08:54:48', '2026-05-08 10:44:39'),
(4, 'Dumbbells Set', 'York', NULL, NULL, 'Free Weights', 'Weight Room', NULL, NULL, 'good', NULL, NULL, '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(5, 'Pull-up Station', 'Body-Solid', NULL, NULL, 'Functional', 'Main Floor', NULL, NULL, 'good', NULL, NULL, '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(6, 'Giga Dumbells', 'STOIC', 'S3112', '123456789', 'Strength', 'Main Floor', '2026-05-15', 900.00, 'good', NULL, 'wala', '2026-05-15 07:28:04', '2026-05-15 07:28:04');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_plans`
--

CREATE TABLE `fitness_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `trainer_id` int(10) UNSIGNED DEFAULT NULL,
  `plan_name` varchar(200) NOT NULL,
  `goal` varchar(200) DEFAULT NULL,
  `exercises` text DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT 4,
  `notes` text DEFAULT NULL,
  `status` enum('active','completed','paused') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gym_owner_applications`
--

CREATE TABLE `gym_owner_applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_owner_applications`
--

INSERT INTO `gym_owner_applications` (`id`, `user_id`, `business_name`, `contact_number`, `address`, `reason`, `status`, `reviewed_by`, `reviewed_at`, `review_notes`, `created_at`, `updated_at`) VALUES
(2, 13, 'Onse Powerfitness Gym', '09774233211', '2nd Floor Fernandez Bldg, Saavedra St, Toril, Davao City, Davao del Sur', 'Wala lang try lang mani', 'approved', 13, '2026-05-13 15:46:50', 'Auto-approved: no existing Gym Owner.', '2026-05-13 07:46:50', '2026-05-13 07:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `gym_owner_application_documents`
--

CREATE TABLE `gym_owner_application_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `application_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_original` varchar(255) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gym_owner_application_documents`
--

INSERT INTO `gym_owner_application_documents` (`id`, `application_id`, `document_type`, `file_name`, `file_original`, `file_size`, `file_type`, `created_at`) VALUES
(3, 2, 'business_permit', '68fc198ad999929654eca7ccbbef320a.pdf', 'IAS 102 Final Requirements.pdf', 236625, 'application/pdf', '2026-05-13 07:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `legal_documents`
--

CREATE TABLE `legal_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `uploaded_by` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('business_permit','bir_registration','sec_registration','dti_registration','sanitary_permit','fire_safety_permit','lease_contract','insurance_policy','employment_contract','nda','other') NOT NULL DEFAULT 'other',
  `description` text DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_original` varchar(255) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_type` varchar(100) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','archived','expired') NOT NULL DEFAULT 'active',
  `is_confidential` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(150) NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `email`, `success`, `ip_address`, `attempted_at`) VALUES
(1, 'owner@gobuff.com', 1, '::1', '2026-05-08 08:55:24'),
(2, 'owner@gobuff.com', 1, '::1', '2026-05-08 10:16:05'),
(3, 'owner@gobuff.com', 0, '::1', '2026-05-08 10:58:55'),
(4, 'owner@gobuff.com', 0, '::1', '2026-05-08 10:59:05'),
(5, 'owner@gobuff.com', 1, '::1', '2026-05-08 10:59:56'),
(6, 'jpogs565@gmail.com', 0, '::1', '2026-05-08 11:06:38'),
(7, 'jpogs565@gmail.com', 0, '::1', '2026-05-08 11:06:47'),
(8, 'maintenance@gobuff.com', 1, '::1', '2026-05-08 11:08:38'),
(9, 'maintenance@gobuff.com', 1, '::1', '2026-05-08 11:12:04'),
(10, 'maintenance@gobuff.com', 1, '::1', '2026-05-08 11:22:17'),
(11, 'jpogs565@gmail.com', 0, '::1', '2026-05-08 11:25:12'),
(12, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:25:39'),
(13, 'jpogs565@gmail.com', 1, '::1', '2026-05-08 11:31:41'),
(14, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:32:47'),
(15, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:34:41'),
(16, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:35:08'),
(17, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:35:26'),
(18, 'jpogs565@gmail.com', 1, '::1', '2026-05-08 11:38:21'),
(19, 'jpogs565@gmail.com', 1, '::1', '2026-05-08 11:38:42'),
(20, 'jpogs565@gmail.com', 1, '::1', '2026-05-08 11:41:48'),
(21, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:42:46'),
(22, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:42:58'),
(23, 'admin@gobuff.com', 1, '::1', '2026-05-08 11:45:53'),
(24, 'admin@gobuff.com', 1, '::1', '2026-05-08 11:46:09'),
(25, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:48:18'),
(26, 'owner@gobuff.com', 1, '::1', '2026-05-08 11:48:30'),
(27, 'jpogs565@gmail.com', 1, '::1', '2026-05-11 04:52:55'),
(28, 'jpogs565@gmail.com', 1, '::1', '2026-05-11 04:56:58'),
(29, 'admin@gobuff.com', 1, '::1', '2026-05-13 06:07:16'),
(30, 'admin@gobuff.com', 1, '::1', '2026-05-13 06:07:56'),
(31, 'admin@gobuff.com', 1, '::1', '2026-05-13 06:08:07'),
(32, 'jpogs565@gmail.com', 1, '::1', '2026-05-13 06:34:43'),
(33, 'jpogs565@gmail.com', 1, '::1', '2026-05-13 06:34:53'),
(34, 'marketing@gobuff.com', 1, '::1', '2026-05-13 06:37:48'),
(35, 'marketing@gobuff.com', 1, '::1', '2026-05-13 06:38:00'),
(36, 'trainer@gobuff.com', 1, '::1', '2026-05-13 06:39:32'),
(37, 'trainer@gobuff.com', 1, '::1', '2026-05-13 06:39:44'),
(38, 'maintenance@gobuff.com', 1, '::1', '2026-05-13 07:14:17'),
(39, 'maintenance@gobuff.com', 1, '::1', '2026-05-13 07:14:27'),
(40, 'anzed333@gmail.com', 1, '::1', '2026-05-13 07:35:17'),
(41, 'anzed333@gmail.com', 1, '::1', '2026-05-13 07:43:59'),
(42, 'anzed333@gmail.com', 1, '::1', '2026-05-13 07:44:07'),
(43, 'anzed333@gmail.com', 1, '::1', '2026-05-13 07:50:21'),
(44, 'anzed333@gmail.com', 1, '::1', '2026-05-13 07:50:49'),
(45, 'anzed333@gmail.com', 1, '::1', '2026-05-13 07:50:57'),
(46, 'maintenance@gobuff.com', 0, '::1', '2026-05-15 07:23:36'),
(47, 'maintenance@gobuff.com', 1, '::1', '2026-05-15 07:23:44'),
(48, 'maintenance@gobuff.com', 1, '::1', '2026-05-15 07:23:57'),
(49, 'anzed333@gmail.com', 1, '::1', '2026-05-15 07:26:52'),
(50, 'anzed333@gmail.com', 1, '::1', '2026-05-15 07:27:03'),
(51, 'anzed333@gmail.com', 1, '::1', '2026-05-15 08:27:16'),
(52, 'anzed333@gmail.com', 1, '::1', '2026-05-15 08:27:27'),
(53, 'maintenance@gobuff.com', 1, '::1', '2026-05-15 09:18:47'),
(54, 'maintenance@gobuff.com', 1, '::1', '2026-05-15 09:18:56'),
(55, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 10:16:45'),
(56, 'admin@gobuff.com', 1, '::1', '2026-05-17 10:17:32'),
(57, 'admin@gobuff.com', 1, '::1', '2026-05-17 10:17:41'),
(58, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 11:18:50'),
(59, 'anzed333@gmail.com', 1, '::1', '2026-05-17 11:19:04'),
(60, 'anzed333@gmail.com', 1, '::1', '2026-05-17 11:19:13'),
(61, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 11:19:23'),
(62, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 11:19:36'),
(63, 'admin@gobuff.com', 1, '::1', '2026-05-17 11:19:59'),
(64, 'admin@gobuff.com', 1, '::1', '2026-05-17 11:20:06'),
(65, 'admin@gobuff.com', 1, '::1', '2026-05-17 13:45:06'),
(66, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 13:45:23'),
(67, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 13:45:32'),
(68, 'admin@gobuff.com', 1, '::1', '2026-05-17 13:45:34'),
(69, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 13:47:02'),
(70, 'admin@gobuff.com', 1, '::1', '2026-05-17 13:47:06'),
(71, 'admin@gobuff.com', 1, '::1', '2026-05-17 13:47:14'),
(72, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 13:59:40'),
(73, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 13:59:55'),
(74, 'jpogs565@gmail.com', 1, '::1', '2026-05-17 14:00:04');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_reports`
--

CREATE TABLE `maintenance_reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `reported_by` int(10) UNSIGNED DEFAULT NULL,
  `issue_type` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `resolution` text DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_reports`
--

INSERT INTO `maintenance_reports` (`id`, `equipment_id`, `reported_by`, `issue_type`, `description`, `priority`, `status`, `resolution`, `verified_at`, `completed_at`, `created_at`) VALUES
(1, 3, NULL, 'Wear and Tear', 'Paayu please', 'medium', 'completed', 'Maintenance completed', NULL, '2026-05-08 18:44:39', '2026-05-08 10:44:35'),
(4, 2, 2, 'Wear and Tear', 'Paayu ni bi', 'low', 'completed', 'Maintenance completed', '2026-05-15 15:44:35', '2026-05-15 15:44:42', '2026-05-15 07:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `membership_id` varchar(10) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `emergency_contact` varchar(200) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `user_id`, `membership_id`, `first_name`, `last_name`, `phone`, `address`, `date_of_birth`, `gender`, `emergency_contact`, `photo`, `status`, `created_at`, `updated_at`) VALUES
(1, 6, '7749', 'Juan', 'dela Cruz', '09171234567', NULL, NULL, 'male', NULL, NULL, 'active', '2026-05-08 08:54:48', '2026-05-08 09:21:25'),
(8, 15, '9103', 'John Paul', 'Manulat', '09774233211', 'Davao City', '2004-08-11', 'male', 'Niga man (09094773126) - Homie', NULL, 'active', '2026-05-17 13:48:37', '2026-05-17 13:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `plan_name` varchar(150) NOT NULL,
  `plan_type` enum('daily','monthly','quarterly','semi_annual','annual') DEFAULT 'monthly',
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','active','expired','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `member_id`, `plan_name`, `plan_type`, `start_date`, `expiry_date`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Monthly Basic', 'monthly', '2026-05-08', '2026-06-08', 999.00, 'active', '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(2, 8, 'Monthly Basic', 'monthly', '2026-05-17', '2026-06-17', 999.00, 'active', '2026-05-17 13:55:39', '2026-05-17 13:56:17');

-- --------------------------------------------------------

--
-- Table structure for table `membership_payments`
--

CREATE TABLE `membership_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `membership_id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT 'cash',
  `reference_no` varchar(100) DEFAULT NULL,
  `status` enum('paid','pending','failed','refunded') DEFAULT 'paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_payments`
--

INSERT INTO `membership_payments` (`id`, `membership_id`, `member_id`, `amount`, `payment_date`, `payment_method`, `reference_no`, `status`, `created_at`) VALUES
(1, 2, 8, 999.00, '2026-05-17', 'paymongo', 'link_0bc5129db108e73ba1bbe813', 'paid', '2026-05-17 13:55:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = broadcast to all',
  `type` enum('system','membership','booking','general','maintenance') DEFAULT 'general',
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `is_read`, `created_at`) VALUES
(5, 13, 'system', 'New Role Application', 'Member pol has applied for the role: Member', 0, '2026-05-17 10:17:05'),
(7, 2, 'system', 'New Membership Application', 'John Doe has submitted a membership application and is awaiting your review.', 0, '2026-05-17 13:48:12'),
(8, 15, 'membership', 'Membership Application Approved', 'Your membership application has been approved by the Administrative Office! Please go to \"My Membership\" to submit your payment and activate your membership.', 0, '2026-05-17 13:48:37'),
(9, 15, 'membership', 'Membership Activated', 'Your Monthly Basic membership is now active! Your Membership ID is: 9103. Valid until June 17, 2026. Please log out and log back in to access all member features.', 0, '2026-05-17 13:56:17'),
(10, 2, 'membership', 'Payment Confirmed', 'Payment for John Paul Manulat\'s Monthly Basic membership has been confirmed via PayMongo.', 0, '2026-05-17 13:56:17');

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_plans`
--

CREATE TABLE `nutrition_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `trainer_id` int(10) UNSIGNED DEFAULT NULL,
  `plan_name` varchar(200) NOT NULL,
  `daily_calories` int(11) DEFAULT 2000,
  `protein_grams` decimal(6,1) DEFAULT 0.0,
  `carbs_grams` decimal(6,1) DEFAULT 0.0,
  `fat_grams` decimal(6,1) DEFAULT 0.0,
  `meal_plan` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','completed','paused') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operational_expenses`
--

CREATE TABLE `operational_expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `recorded_by` int(10) UNSIGNED NOT NULL,
  `budget_plan_id` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'miscellaneous',
  `description` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expense_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','check','credit_card','gcash','other') DEFAULT 'cash',
  `reference_no` varchar(100) DEFAULT NULL,
  `receipt_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operational_expenses`
--

INSERT INTO `operational_expenses` (`id`, `recorded_by`, `budget_plan_id`, `category`, `description`, `amount`, `expense_date`, `payment_method`, `reference_no`, `receipt_file`, `status`, `approved_by`, `approved_at`, `notes`, `created_at`, `updated_at`) VALUES
(3, 13, NULL, 'utilities', 'Monthly Electricity Bill', 900.00, '2026-05-15', 'cash', 'OR-2026-01', '7fa94e08747608b5ed07a1648c890e80.pdf', 'approved', 13, '2026-05-15 16:16:07', '', '2026-05-15 08:16:00', '2026-05-15 08:16:07'),
(4, 13, NULL, 'Essentials', 'Energy Drinks', 300.00, '2026-05-15', 'cash', 'OR-2026-02', 'd5fb871c58010e13e85f32705658db1e.jpg', 'approved', 13, '2026-05-15 16:26:37', 'Payts ra', '2026-05-15 08:26:31', '2026-05-15 08:43:26');

-- --------------------------------------------------------

--
-- Table structure for table `otp_tokens`
--

CREATE TABLE `otp_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(10) NOT NULL,
  `purpose` enum('login','register','password_reset') NOT NULL DEFAULT 'login',
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_tokens`
--

INSERT INTO `otp_tokens` (`id`, `user_id`, `token`, `purpose`, `attempts`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 5, '807227', 'login', 0, '2026-05-08 19:32:17', '2026-05-13 15:14:17', '2026-05-08 11:22:17'),
(12, 2, '237507', 'login', 0, '2026-05-08 19:55:53', '2026-05-08 19:46:09', '2026-05-08 11:45:53'),
(16, 2, '108510', 'login', 0, '2026-05-13 14:17:16', '2026-05-13 14:07:56', '2026-05-13 06:07:16'),
(17, 2, '157145', 'login', 0, '2026-05-13 14:17:56', '2026-05-13 14:08:07', '2026-05-13 06:07:56'),
(19, 3, '277034', 'login', 0, '2026-05-13 14:47:48', '2026-05-13 14:37:59', '2026-05-13 06:37:48'),
(20, 4, '387318', 'login', 0, '2026-05-13 14:49:32', '2026-05-13 14:39:44', '2026-05-13 06:39:32'),
(21, 5, '949902', 'login', 0, '2026-05-13 15:24:17', '2026-05-13 15:14:27', '2026-05-13 07:14:17'),
(22, 13, '499509', 'register', 0, '2026-05-13 15:45:07', '2026-05-13 15:35:17', '2026-05-13 07:35:07'),
(23, 13, '932558', 'login', 0, '2026-05-13 15:53:59', '2026-05-13 15:44:07', '2026-05-13 07:43:59'),
(24, 13, '068992', 'login', 0, '2026-05-13 16:00:21', '2026-05-13 15:50:49', '2026-05-13 07:50:21'),
(25, 13, '424065', 'login', 0, '2026-05-13 16:00:49', '2026-05-13 15:50:57', '2026-05-13 07:50:49'),
(26, 5, '496228', 'login', 0, '2026-05-15 15:33:45', '2026-05-15 15:23:57', '2026-05-15 07:23:45'),
(27, 13, '704396', 'login', 0, '2026-05-15 15:36:52', '2026-05-15 15:27:03', '2026-05-15 07:26:52'),
(28, 13, '944080', 'login', 0, '2026-05-15 16:37:16', '2026-05-15 16:27:27', '2026-05-15 08:27:16'),
(29, 5, '090805', 'login', 0, '2026-05-15 17:28:47', '2026-05-15 17:18:56', '2026-05-15 09:18:47'),
(31, 2, '709245', 'login', 0, '2026-05-17 18:27:32', '2026-05-17 18:17:41', '2026-05-17 10:17:32'),
(33, 13, '010286', 'login', 0, '2026-05-17 19:29:04', '2026-05-17 19:19:13', '2026-05-17 11:19:04'),
(35, 2, '725601', 'login', 0, '2026-05-17 19:29:59', '2026-05-17 19:20:06', '2026-05-17 11:19:59'),
(36, 2, '143409', 'login', 0, '2026-05-17 21:55:06', '2026-05-17 21:45:35', '2026-05-17 13:45:06'),
(38, 2, '952296', 'login', 0, '2026-05-17 21:55:35', '2026-05-17 21:47:06', '2026-05-17 13:45:35'),
(39, 15, '363214', 'register', 0, '2026-05-17 21:56:53', '2026-05-17 21:47:02', '2026-05-17 13:46:53'),
(40, 2, '029831', 'login', 0, '2026-05-17 21:57:06', '2026-05-17 21:47:14', '2026-05-17 13:47:06'),
(41, 15, '654082', 'login', 0, '2026-05-17 22:09:40', '2026-05-17 21:59:55', '2026-05-17 13:59:40'),
(42, 15, '925762', 'login', 0, '2026-05-17 22:09:55', '2026-05-17 22:00:03', '2026-05-17 13:59:55');

-- --------------------------------------------------------

--
-- Table structure for table `progress_tracking`
--

CREATE TABLE `progress_tracking` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `body_fat_pct` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `label`, `description`, `created_at`) VALUES
(1, 'gym_owner', 'Gym Owner', NULL, '2026-05-08 08:54:48'),
(2, 'admin', 'Administrative Officer', NULL, '2026-05-08 08:54:48'),
(3, 'marketing', 'Marketing Officer', NULL, '2026-05-08 08:54:48'),
(4, 'trainer', 'Fitness Trainer', NULL, '2026-05-08 08:54:48'),
(5, 'maintenance', 'Maintenance Supervisor', NULL, '2026-05-08 08:54:48'),
(6, 'member', 'Member', NULL, '2026-05-08 08:54:48'),
(7, 'user', 'User', 'Newly registered user pending role assignment', '2026-05-13 06:29:26');

-- --------------------------------------------------------

--
-- Table structure for table `role_applications`
--

CREATE TABLE `role_applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `requested_role` enum('admin','marketing','trainer','maintenance','member') NOT NULL,
  `reason` text DEFAULT NULL,
  `membership_form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`membership_form_data`)),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_applications`
--

INSERT INTO `role_applications` (`id`, `user_id`, `requested_role`, `reason`, `membership_form_data`, `status`, `reviewed_by`, `reviewed_at`, `review_notes`, `created_at`, `updated_at`) VALUES
(3, 15, 'member', 'Membership application submitted via membership form.', '{\"first_name\":\"John Paul\",\"last_name\":\"Manulat\",\"date_of_birth\":\"2004-08-11\",\"gender\":\"male\",\"phone\":\"09774233211\",\"address\":\"Davao City\",\"emergency_name\":\"Niga man\",\"emergency_phone\":\"09094773126\",\"emergency_relation\":\"Homie\",\"plan_preference\":\"monthly\",\"health_conditions\":\"\",\"fitness_goals\":\"Build Masil\"}', 'approved', 2, '2026-05-17 21:48:37', 'Whats up my nigga', '2026-05-17 13:48:12', '2026-05-17 13:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '0=Sun,1=Mon,...,6=Sat',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainer_bookings`
--

CREATE TABLE `trainer_bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `trainer_id` int(10) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `duration` int(11) NOT NULL DEFAULT 60 COMMENT 'minutes',
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(80) NOT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `auth_provider` enum('local','google') NOT NULL DEFAULT 'local',
  `password` varchar(255) NOT NULL,
  `role` enum('gym_owner','admin','marketing','trainer','maintenance','member','user') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `google_id`, `avatar_url`, `auth_provider`, `password`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(2, 'Admin Officer', 'admin@gobuff.com', 'admin', NULL, NULL, 'local', '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'admin', 'active', '2026-05-17 21:47:14', '2026-05-08 08:54:48', '2026-05-17 13:47:14'),
(3, 'Marketing Staff', 'marketing@gobuff.com', 'marketing', NULL, NULL, 'local', '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'marketing', 'active', '2026-05-13 14:38:00', '2026-05-08 08:54:48', '2026-05-13 06:38:00'),
(4, 'John Trainer', 'trainer@gobuff.com', 'trainer1', NULL, NULL, 'local', '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'trainer', 'active', '2026-05-13 14:39:44', '2026-05-08 08:54:48', '2026-05-13 06:39:44'),
(5, 'Maintenance Head', 'maintenance@gobuff.com', 'maintenance', NULL, NULL, 'local', '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'maintenance', 'active', '2026-05-15 17:18:56', '2026-05-08 08:54:48', '2026-05-15 09:18:56'),
(6, 'Juan dela Cruz', 'member@gobuff.com', 'member1', NULL, NULL, 'local', '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'member', 'active', NULL, '2026-05-08 08:54:48', '2026-05-08 08:54:48'),
(13, 'Mr. Owner', 'anzed333@gmail.com', 'Mr. Owner Pogi', NULL, NULL, 'local', '$2y$12$guYohzPan2wIbA5MojRF6era3DkHvDp9MgKJDM7ODMX1oq0jOH5Hm', 'gym_owner', 'active', '2026-05-17 19:19:13', '2026-05-13 07:35:07', '2026-05-17 11:19:13'),
(15, 'John Doe', 'jpogs565@gmail.com', 'jando', NULL, NULL, 'local', '$2y$12$97x03aGZgswRwcYWD726quPT28zRdlF8lw8GDeLuPUf/ho1HkOhIm', 'member', 'active', '2026-05-17 22:00:03', '2026-05-17 13:46:53', '2026-05-17 14:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `workout_activities`
--

CREATE TABLE `workout_activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `activity` varchar(200) NOT NULL,
  `type` enum('indoor','outdoor') DEFAULT 'indoor',
  `duration_min` int(11) DEFAULT NULL,
  `calories_burned` decimal(7,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `logged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_schedules`
--

CREATE TABLE `work_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_schedules`
--

INSERT INTO `work_schedules` (`id`, `employee_id`, `schedule_date`, `time_in`, `time_out`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, '2026-05-15', '06:00:00', '21:00:00', '', 13, '2026-05-15 09:23:05', '2026-05-15 09:23:05'),
(2, 2, '2026-05-15', '06:00:00', '15:00:00', '', 13, '2026-05-15 09:23:05', '2026-05-15 09:23:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_date` (`date`);

--
-- Indexes for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_budget_plan_id` (`budget_plan_id`);

--
-- Indexes for table `budget_plans`
--
ALTER TABLE `budget_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_fiscal_year` (`fiscal_year`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start_date` (`start_date`);

--
-- Indexes for table `checkins`
--
ALTER TABLE `checkins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_check_in_time` (`check_in_time`);

--
-- Indexes for table `dietary_logs`
--
ALTER TABLE `dietary_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_log_date` (`log_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_job_role` (`job_role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_condition_status` (`condition_status`);

--
-- Indexes for table `fitness_plans`
--
ALTER TABLE `fitness_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`),
  ADD KEY `idx_member_id` (`member_id`);

--
-- Indexes for table `gym_owner_applications`
--
ALTER TABLE `gym_owner_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_goa_user_id` (`user_id`),
  ADD KEY `idx_goa_status` (`status`);

--
-- Indexes for table `gym_owner_application_documents`
--
ALTER TABLE `gym_owner_application_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_goad_app_id` (`application_id`);

--
-- Indexes for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expiry_date` (`expiry_date`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_success` (`success`);

--
-- Indexes for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `idx_equipment_id` (`equipment_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `membership_id` (`membership_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_membership_id` (`membership_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expiry_date` (`expiry_date`);

--
-- Indexes for table `membership_payments`
--
ALTER TABLE `membership_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `membership_id` (`membership_id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `operational_expenses`
--
ALTER TABLE `operational_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `budget_plan_id` (`budget_plan_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_expense_date` (`expense_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_otp_user_id` (`user_id`),
  ADD KEY `idx_otp_expires` (`expires_at`);

--
-- Indexes for table `progress_tracking`
--
ALTER TABLE `progress_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_recorded_at` (`recorded_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_applications`
--
ALTER TABLE `role_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_ra_user_id` (`user_id`),
  ADD KEY `idx_ra_status` (`status`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member_id` (`member_id`),
  ADD KEY `idx_trainer_id` (`trainer_id`),
  ADD KEY `idx_booking_date` (`booking_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `idx_google_id` (`google_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `workout_activities`
--
ALTER TABLE `workout_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_date` (`employee_id`,`schedule_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `budget_items`
--
ALTER TABLE `budget_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `budget_plans`
--
ALTER TABLE `budget_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `checkins`
--
ALTER TABLE `checkins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dietary_logs`
--
ALTER TABLE `dietary_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fitness_plans`
--
ALTER TABLE `fitness_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gym_owner_applications`
--
ALTER TABLE `gym_owner_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gym_owner_application_documents`
--
ALTER TABLE `gym_owner_application_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `membership_payments`
--
ALTER TABLE `membership_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operational_expenses`
--
ALTER TABLE `operational_expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `progress_tracking`
--
ALTER TABLE `progress_tracking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `role_applications`
--
ALTER TABLE `role_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `workout_activities`
--
ALTER TABLE `workout_activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_schedules`
--
ALTER TABLE `work_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD CONSTRAINT `budget_items_ibfk_1` FOREIGN KEY (`budget_plan_id`) REFERENCES `budget_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_plans`
--
ALTER TABLE `budget_plans`
  ADD CONSTRAINT `budget_plans_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_plans_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `checkins`
--
ALTER TABLE `checkins`
  ADD CONSTRAINT `checkins_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dietary_logs`
--
ALTER TABLE `dietary_logs`
  ADD CONSTRAINT `dietary_logs_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fitness_plans`
--
ALTER TABLE `fitness_plans`
  ADD CONSTRAINT `fitness_plans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fitness_plans_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gym_owner_applications`
--
ALTER TABLE `gym_owner_applications`
  ADD CONSTRAINT `gym_owner_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gym_owner_applications_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gym_owner_application_documents`
--
ALTER TABLE `gym_owner_application_documents`
  ADD CONSTRAINT `gym_owner_application_documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `gym_owner_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD CONSTRAINT `legal_documents_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  ADD CONSTRAINT `maintenance_reports_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_reports_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `memberships`
--
ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `membership_payments`
--
ALTER TABLE `membership_payments`
  ADD CONSTRAINT `membership_payments_ibfk_1` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membership_payments_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  ADD CONSTRAINT `nutrition_plans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nutrition_plans_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `operational_expenses`
--
ALTER TABLE `operational_expenses`
  ADD CONSTRAINT `operational_expenses_ibfk_1` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `operational_expenses_ibfk_2` FOREIGN KEY (`budget_plan_id`) REFERENCES `budget_plans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `operational_expenses_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `otp_tokens`
--
ALTER TABLE `otp_tokens`
  ADD CONSTRAINT `otp_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress_tracking`
--
ALTER TABLE `progress_tracking`
  ADD CONSTRAINT `progress_tracking_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_applications`
--
ALTER TABLE `role_applications`
  ADD CONSTRAINT `role_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_applications_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  ADD CONSTRAINT `trainer_bookings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_bookings_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_activities`
--
ALTER TABLE `workout_activities`
  ADD CONSTRAINT `workout_activities_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD CONSTRAINT `fk_ws_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
