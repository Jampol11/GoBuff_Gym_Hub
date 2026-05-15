-- ============================================================
-- GoBuff: Gym Hub — Database Schema
-- MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS `gobuff_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `gobuff_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── Roles ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(50)  NOT NULL UNIQUE,
  `label`       VARCHAR(100) NOT NULL,
  `description` TEXT,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `roles` (`name`, `label`) VALUES
  ('gym_owner',   'Gym Owner'),
  ('admin',       'Administrative Officer'),
  ('marketing',   'Marketing Officer'),
  ('trainer',     'Fitness Trainer'),
  ('maintenance', 'Maintenance Supervisor'),
  ('member',      'Member'),
  ('user',        'User');

-- ── Users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `username`   VARCHAR(80)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('gym_owner','admin','marketing','trainer','maintenance','member','user') NOT NULL DEFAULT 'user',
  `status`     ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email`  (`email`),
  INDEX `idx_role`   (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB;

-- ── Login Logs ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email`        VARCHAR(150) NOT NULL,
  `success`      TINYINT(1)   NOT NULL DEFAULT 0,
  `ip_address`   VARCHAR(45),
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email`  (`email`),
  INDEX `idx_success`(`success`)
) ENGINE=InnoDB;

-- ── Activity Logs ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED,
  `action`      VARCHAR(100) NOT NULL,
  `description` TEXT,
  `ip_address`  VARCHAR(45),
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id`(`user_id`),
  INDEX `idx_action` (`action`)
) ENGINE=InnoDB;

-- ── Members ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `members` (
  `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`           INT UNSIGNED,
  `membership_id`     VARCHAR(10)  NOT NULL UNIQUE,
  `first_name`        VARCHAR(80)  NOT NULL,
  `last_name`         VARCHAR(80)  NOT NULL,
  `phone`             VARCHAR(20),
  `address`           TEXT,
  `date_of_birth`     DATE,
  `gender`            ENUM('male','female','other'),
  `emergency_contact` VARCHAR(200),
  `photo`             VARCHAR(255),
  `status`            ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_membership_id`(`membership_id`),
  INDEX `idx_status`       (`status`)
) ENGINE=InnoDB;

-- ── Memberships ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `memberships` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`   INT UNSIGNED NOT NULL,
  `plan_name`   VARCHAR(150) NOT NULL,
  `plan_type`   ENUM('daily','monthly','quarterly','semi_annual','annual') DEFAULT 'monthly',
  `start_date`  DATE         NOT NULL,
  `expiry_date` DATE         NOT NULL,
  `amount`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`      ENUM('pending','active','expired','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
  INDEX `idx_member_id`  (`member_id`),
  INDEX `idx_status`     (`status`),
  INDEX `idx_expiry_date`(`expiry_date`)
) ENGINE=InnoDB;

-- ── Membership Payments ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `membership_payments` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `membership_id` INT UNSIGNED NOT NULL,
  `member_id`     INT UNSIGNED NOT NULL,
  `amount`        DECIMAL(10,2) NOT NULL,
  `payment_date`  DATE          NOT NULL,
  `payment_method`VARCHAR(50)   DEFAULT 'cash',
  `reference_no`  VARCHAR(100),
  `status`        ENUM('paid','pending','failed','refunded') DEFAULT 'paid',
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`membership_id`) REFERENCES `memberships`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`)     REFERENCES `members`(`id`)     ON DELETE CASCADE,
  INDEX `idx_member_id`    (`member_id`),
  INDEX `idx_payment_date` (`payment_date`)
) ENGINE=InnoDB;

-- ── Check-Ins ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `checkins` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`      INT UNSIGNED NOT NULL,
  `check_in_time`  DATETIME     NOT NULL,
  `check_out_time` DATETIME,
  `method`         ENUM('manual','qr_code','card') DEFAULT 'manual',
  `status`         ENUM('checked_in','checked_out') DEFAULT 'checked_in',
  `notes`          TEXT,
  FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
  INDEX `idx_member_id`     (`member_id`),
  INDEX `idx_check_in_time` (`check_in_time`)
) ENGINE=InnoDB;

-- ── Employees ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `employees` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT UNSIGNED,
  `first_name`     VARCHAR(80)  NOT NULL,
  `last_name`      VARCHAR(80)  NOT NULL,
  `job_role`       VARCHAR(80)  NOT NULL,
  `department`     VARCHAR(100),
  `specialization` VARCHAR(200),
  `phone`          VARCHAR(20),
  `address`        TEXT,
  `hire_date`      DATE,
  `salary`         DECIMAL(10,2),
  `status`         ENUM('active','inactive','resigned') DEFAULT 'active',
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_job_role`(`job_role`),
  INDEX `idx_status`  (`status`)
) ENGINE=InnoDB;

-- ── Attendance ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `attendance` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL,
  `date`        DATE         NOT NULL,
  `time_in`     TIME,
  `time_out`    TIME,
  `status`      ENUM('present','absent','late','half_day','leave') DEFAULT 'present',
  `notes`       TEXT,
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  INDEX `idx_employee_id`(`employee_id`),
  INDEX `idx_date`       (`date`)
) ENGINE=InnoDB;

-- ── Trainer Bookings ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `trainer_bookings` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`    INT UNSIGNED NOT NULL,
  `trainer_id`   INT UNSIGNED NOT NULL,
  `booking_date` DATE         NOT NULL,
  `booking_time` TIME         NOT NULL,
  `duration`     INT          NOT NULL DEFAULT 60 COMMENT 'minutes',
  `notes`        TEXT,
  `status`       ENUM('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`)  REFERENCES `members`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`trainer_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  INDEX `idx_member_id`   (`member_id`),
  INDEX `idx_trainer_id`  (`trainer_id`),
  INDEX `idx_booking_date`(`booking_date`)
) ENGINE=InnoDB;

-- ── Fitness Plans ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `fitness_plans` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`      INT UNSIGNED NOT NULL,
  `trainer_id`     INT UNSIGNED,
  `plan_name`      VARCHAR(200) NOT NULL,
  `goal`           VARCHAR(200),
  `exercises`      TEXT,
  `frequency`      VARCHAR(100),
  `duration_weeks` INT          DEFAULT 4,
  `notes`          TEXT,
  `status`         ENUM('active','completed','paused') DEFAULT 'active',
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`)  REFERENCES `members`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`trainer_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL,
  INDEX `idx_member_id`(`member_id`)
) ENGINE=InnoDB;

-- ── Nutrition Plans ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `nutrition_plans` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`      INT UNSIGNED NOT NULL,
  `trainer_id`     INT UNSIGNED,
  `plan_name`      VARCHAR(200) NOT NULL,
  `daily_calories` INT          DEFAULT 2000,
  `protein_grams`  DECIMAL(6,1) DEFAULT 0,
  `carbs_grams`    DECIMAL(6,1) DEFAULT 0,
  `fat_grams`      DECIMAL(6,1) DEFAULT 0,
  `meal_plan`      TEXT,
  `notes`          TEXT,
  `status`         ENUM('active','completed','paused') DEFAULT 'active',
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`)  REFERENCES `members`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`trainer_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Progress Tracking ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `progress_tracking` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`    INT UNSIGNED NOT NULL,
  `weight_kg`    DECIMAL(5,2),
  `height_cm`    DECIMAL(5,2),
  `bmi`          DECIMAL(5,2),
  `body_fat_pct` DECIMAL(5,2),
  `notes`        TEXT,
  `recorded_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
  INDEX `idx_member_id`  (`member_id`),
  INDEX `idx_recorded_at`(`recorded_at`)
) ENGINE=InnoDB;

-- ── Schedules ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `schedules` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL,
  `day_of_week` TINYINT      NOT NULL COMMENT '0=Sun,1=Mon,...,6=Sat',
  `start_time`  TIME         NOT NULL,
  `end_time`    TIME         NOT NULL,
  `notes`       TEXT,
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Workout Activities ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `workout_activities` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`   INT UNSIGNED NOT NULL,
  `activity`    VARCHAR(200) NOT NULL,
  `type`        ENUM('indoor','outdoor') DEFAULT 'indoor',
  `duration_min`INT,
  `calories_burned` DECIMAL(7,2),
  `notes`       TEXT,
  `logged_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Dietary Logs ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `dietary_logs` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `member_id`  INT UNSIGNED NOT NULL,
  `log_date`   DATE         NOT NULL,
  `meal_type`  ENUM('breakfast','lunch','dinner','snack') NOT NULL,
  `food_items` TEXT         NOT NULL,
  `calories`   DECIMAL(7,2) DEFAULT 0,
  `protein`    DECIMAL(6,2) DEFAULT 0,
  `carbs`      DECIMAL(6,2) DEFAULT 0,
  `fat`        DECIMAL(6,2) DEFAULT 0,
  `notes`      TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
  INDEX `idx_member_id`(`member_id`),
  INDEX `idx_log_date` (`log_date`)
) ENGINE=InnoDB;

-- ── Equipment ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `equipment` (
  `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`                 VARCHAR(200) NOT NULL,
  `brand`                VARCHAR(100),
  `model`                VARCHAR(100),
  `serial_number`        VARCHAR(100),
  `category`             VARCHAR(100),
  `location`             VARCHAR(200),
  `purchase_date`        DATE,
  `purchase_price`       DECIMAL(10,2),
  `condition_status`     ENUM('good','needs_repair','under_maintenance') DEFAULT 'good',
  `last_maintenance_date`DATE,
  `notes`                TEXT,
  `created_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_condition_status`(`condition_status`)
) ENGINE=InnoDB;

-- ── Maintenance Reports ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `maintenance_reports` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `equipment_id` INT UNSIGNED NOT NULL,
  `reported_by`  INT UNSIGNED,
  `issue_type`   VARCHAR(200) NOT NULL,
  `description`  TEXT         NOT NULL,
  `priority`     ENUM('low','medium','high','critical') DEFAULT 'medium',
  `status`       ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `resolution`   TEXT,
  `verified_at`  DATETIME,
  `completed_at` DATETIME,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`reported_by`)  REFERENCES `employees`(`id`)   ON DELETE SET NULL,
  INDEX `idx_equipment_id`(`equipment_id`),
  INDEX `idx_status`      (`status`)
) ENGINE=InnoDB;

-- ── Campaigns ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`           VARCHAR(200) NOT NULL,
  `description`     TEXT,
  `target_audience` VARCHAR(200),
  `start_date`      DATE         NOT NULL,
  `end_date`        DATE         NOT NULL,
  `budget`          DECIMAL(10,2) DEFAULT 0,
  `discount_pct`    DECIMAL(5,2)  DEFAULT 0,
  `banner_image`    VARCHAR(255),
  `status`          ENUM('scheduled','active','inactive','completed') DEFAULT 'scheduled',
  `created_by`      INT UNSIGNED,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_status`    (`status`),
  INDEX `idx_start_date`(`start_date`)
) ENGINE=InnoDB;

-- ── Notifications ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED COMMENT 'NULL = broadcast to all',
  `type`       ENUM('system','membership','booking','general','maintenance') DEFAULT 'general',
  `title`      VARCHAR(200) NOT NULL,
  `message`    TEXT         NOT NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id`(`user_id`),
  INDEX `idx_is_read`(`is_read`)
) ENGINE=InnoDB;

-- ── Role Applications ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `role_applications` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT UNSIGNED NOT NULL,
  `requested_role` ENUM('admin','marketing','trainer','maintenance','member') NOT NULL,
  `reason`         TEXT,
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by`    INT UNSIGNED NULL,
  `reviewed_at`    DATETIME NULL,
  `review_notes`   TEXT NULL,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_ra_user_id` (`user_id`),
  INDEX `idx_ra_status`  (`status`)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- All seed passwords are "password" (bcrypt cost 12, generated fresh)
-- Run fix_passwords.php if you still get login errors after import.
-- Change all passwords immediately after setup!
INSERT INTO `users` (`name`, `email`, `username`, `password`, `role`, `status`) VALUES
('Gym Owner',        'owner@gobuff.com',      'gym_owner',   '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'gym_owner',   'active'),
('Admin Officer',    'admin@gobuff.com',       'admin',       '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'admin',       'active'),
('Marketing Staff',  'marketing@gobuff.com',   'marketing',   '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'marketing',   'active'),
('John Trainer',     'trainer@gobuff.com',     'trainer1',    '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'trainer',     'active'),
('Maintenance Head', 'maintenance@gobuff.com', 'maintenance', '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'maintenance', 'active'),
('Juan dela Cruz',   'member@gobuff.com',      'member1',     '$2y$12$xJ/XSwQhmE7ZtFdA5GxLW.UPtj5umbBy2GOXyoRkkInS.xNN.ihlS', 'member',      'active');

-- Employees
INSERT INTO `employees` (`user_id`, `first_name`, `last_name`, `job_role`, `department`, `specialization`, `status`) VALUES
(4, 'John',  'Trainer',  'trainer',     'Fitness',     'Strength & Conditioning', 'active'),
(5, 'Mark',  'Santos',   'maintenance', 'Maintenance', 'Equipment Repair',        'active');

-- Members
INSERT INTO `members` (`user_id`, `membership_id`, `first_name`, `last_name`, `phone`, `gender`, `status`) VALUES
(6, '1001', 'Juan', 'dela Cruz', '09171234567', 'male', 'active');

-- Memberships
INSERT INTO `memberships` (`member_id`, `plan_name`, `plan_type`, `start_date`, `expiry_date`, `amount`, `status`) VALUES
(1, 'Monthly Basic', 'monthly', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 999.00, 'active');

-- Equipment
INSERT INTO `equipment` (`name`, `brand`, `category`, `location`, `condition_status`) VALUES
('Treadmill A1',      'LifeFitness', 'Cardio',        'Main Floor', 'good'),
('Bench Press Rack',  'Hammer',      'Strength',      'Weight Room','good'),
('Rowing Machine',    'Concept2',    'Cardio',        'Main Floor', 'needs_repair'),
('Dumbbells Set',     'York',        'Free Weights',  'Weight Room','good'),
('Pull-up Station',   'Body-Solid',  'Functional',    'Main Floor', 'good');

-- Campaigns
INSERT INTO `campaigns` (`title`, `description`, `target_audience`, `start_date`, `end_date`, `discount_pct`, `status`, `created_by`) VALUES
('Summer Fitness Promo', 'Get fit this summer! 20% off all monthly plans.', 'New Members', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 20.00, 'active', 1),
('Refer a Friend',       'Refer a friend and get 1 month free!',            'Existing Members', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 0.00, 'active', 1);

-- Notifications
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `is_read`) VALUES
(NULL, 'system', 'Welcome to GoBuff!', 'Your gym management system is ready. Please change default passwords immediately.', 0),
(1,    'system', 'Setup Complete',     'GoBuff Gym Hub has been successfully installed and configured.', 0);

-- ============================================================
-- Owner Hub: Legal Documents, Budget Plans, Operational Expenses
-- (from migration 002_owner_hub.sql)
-- ============================================================

-- ── Legal Documents ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `legal_documents` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uploaded_by`     INT UNSIGNED NOT NULL,
  `title`           VARCHAR(255) NOT NULL,
  `category`        ENUM(
                      'business_permit',
                      'bir_registration',
                      'sec_registration',
                      'dti_registration',
                      'sanitary_permit',
                      'fire_safety_permit',
                      'lease_contract',
                      'insurance_policy',
                      'employment_contract',
                      'nda',
                      'other'
                    ) NOT NULL DEFAULT 'other',
  `description`     TEXT,
  `file_name`       VARCHAR(255) NOT NULL,
  `file_original`   VARCHAR(255) NOT NULL,
  `file_size`       INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'bytes',
  `file_type`       VARCHAR(100) NOT NULL,
  `expiry_date`     DATE         COMMENT 'NULL = no expiry',
  `status`          ENUM('active','archived','expired') NOT NULL DEFAULT 'active',
  `is_confidential` TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_ld_category`    (`category`),
  INDEX `idx_ld_status`      (`status`),
  INDEX `idx_ld_expiry_date` (`expiry_date`)
) ENGINE=InnoDB;

-- ── Budget Plans ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `budget_plans` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `created_by`   INT UNSIGNED NOT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `fiscal_year`  YEAR         NOT NULL,
  `period`       ENUM('monthly','quarterly','semi_annual','annual') NOT NULL DEFAULT 'annual',
  `period_label` VARCHAR(50)  COMMENT 'e.g. Q1 2026, January 2026',
  `total_budget` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes`        TEXT,
  `status`       ENUM('draft','approved','active','closed') NOT NULL DEFAULT 'draft',
  `approved_by`  INT UNSIGNED,
  `approved_at`  DATETIME,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_bp_fiscal_year` (`fiscal_year`),
  INDEX `idx_bp_status`      (`status`)
) ENGINE=InnoDB;

-- ── Budget Line Items ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `budget_items` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `budget_plan_id` INT UNSIGNED NOT NULL,
  `category`       VARCHAR(100) NOT NULL,
  `description`    VARCHAR(255) NOT NULL,
  `allocated`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `sort_order`     TINYINT UNSIGNED DEFAULT 0,
  FOREIGN KEY (`budget_plan_id`) REFERENCES `budget_plans`(`id`) ON DELETE CASCADE,
  INDEX `idx_bi_budget_plan_id` (`budget_plan_id`)
) ENGINE=InnoDB;

-- ── Operational Expenses ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `operational_expenses` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `recorded_by`    INT UNSIGNED NOT NULL,
  `budget_plan_id` INT UNSIGNED COMMENT 'optional link to a budget plan',
  `category`       ENUM(
                     'rent',
                     'utilities',
                     'salaries',
                     'equipment_purchase',
                     'equipment_repair',
                     'supplies',
                     'marketing',
                     'insurance',
                     'taxes',
                     'miscellaneous'
                   ) NOT NULL DEFAULT 'miscellaneous',
  `description`    VARCHAR(255) NOT NULL,
  `amount`         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `expense_date`   DATE          NOT NULL,
  `payment_method` ENUM('cash','bank_transfer','check','credit_card','gcash','other') DEFAULT 'cash',
  `reference_no`   VARCHAR(100),
  `receipt_file`   VARCHAR(255)  COMMENT 'uploaded receipt image/pdf',
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by`    INT UNSIGNED,
  `approved_at`    DATETIME,
  `notes`          TEXT,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`recorded_by`)    REFERENCES `users`(`id`)        ON DELETE CASCADE,
  FOREIGN KEY (`budget_plan_id`) REFERENCES `budget_plans`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`approved_by`)    REFERENCES `users`(`id`)        ON DELETE SET NULL,
  INDEX `idx_oe_category`    (`category`),
  INDEX `idx_oe_expense_date`(`expense_date`),
  INDEX `idx_oe_status`      (`status`)
) ENGINE=InnoDB;

-- ============================================================
-- OTP Tokens + Google OAuth (from migration 003)
-- ============================================================

-- ── OTP Tokens ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `otp_tokens` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `token`      VARCHAR(10)  NOT NULL,
  `purpose`    ENUM('login','register','password_reset') NOT NULL DEFAULT 'login',
  `attempts`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` DATETIME     NOT NULL,
  `used_at`    DATETIME     NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_otp_user_id` (`user_id`),
  INDEX `idx_otp_expires` (`expires_at`)
) ENGINE=InnoDB;

-- ── Google OAuth columns ─────────────────────────────────────
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `google_id`     VARCHAR(100) NULL AFTER `username`,
  ADD COLUMN IF NOT EXISTS `avatar_url`    VARCHAR(500) NULL AFTER `google_id`,
  ADD COLUMN IF NOT EXISTS `auth_provider` ENUM('local','google') NOT NULL DEFAULT 'local' AFTER `avatar_url`;
