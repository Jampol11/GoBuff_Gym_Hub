-- Migration 002: Owner Hub — Legal Documents, Budget Plans, Operational Expenses
USE `gobuff_db`;

-- ── Legal Documents ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `legal_documents` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uploaded_by`   INT UNSIGNED NOT NULL,
  `title`         VARCHAR(255) NOT NULL,
  `category`      ENUM(
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
  `description`   TEXT,
  `file_name`     VARCHAR(255) NOT NULL,
  `file_original` VARCHAR(255) NOT NULL,
  `file_size`     INT UNSIGNED NOT NULL DEFAULT 0  COMMENT 'bytes',
  `file_type`     VARCHAR(100) NOT NULL,
  `expiry_date`   DATE         COMMENT 'NULL = no expiry',
  `status`        ENUM('active','archived','expired') NOT NULL DEFAULT 'active',
  `is_confidential` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_category`    (`category`),
  INDEX `idx_status`      (`status`),
  INDEX `idx_expiry_date` (`expiry_date`)
) ENGINE=InnoDB;

-- ── Budget Plans ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `budget_plans` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `created_by`      INT UNSIGNED NOT NULL,
  `title`           VARCHAR(255) NOT NULL,
  `fiscal_year`     YEAR         NOT NULL,
  `period`          ENUM('monthly','quarterly','semi_annual','annual') NOT NULL DEFAULT 'annual',
  `period_label`    VARCHAR(50)  COMMENT 'e.g. Q1 2026, January 2026',
  `total_budget`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes`           TEXT,
  `status`          ENUM('draft','approved','active','closed') NOT NULL DEFAULT 'draft',
  `approved_by`     INT UNSIGNED,
  `approved_at`     DATETIME,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_fiscal_year`(`fiscal_year`),
  INDEX `idx_status`     (`status`)
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
  INDEX `idx_budget_plan_id`(`budget_plan_id`)
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
  `expense_date`   DATE         NOT NULL,
  `payment_method` ENUM('cash','bank_transfer','check','credit_card','gcash','other') DEFAULT 'cash',
  `reference_no`   VARCHAR(100),
  `receipt_file`   VARCHAR(255) COMMENT 'uploaded receipt image/pdf',
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by`    INT UNSIGNED,
  `approved_at`    DATETIME,
  `notes`          TEXT,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`recorded_by`)    REFERENCES `users`(`id`)         ON DELETE CASCADE,
  FOREIGN KEY (`budget_plan_id`) REFERENCES `budget_plans`(`id`)  ON DELETE SET NULL,
  FOREIGN KEY (`approved_by`)    REFERENCES `users`(`id`)         ON DELETE SET NULL,
  INDEX `idx_category`    (`category`),
  INDEX `idx_expense_date`(`expense_date`),
  INDEX `idx_status`      (`status`)
) ENGINE=InnoDB;
