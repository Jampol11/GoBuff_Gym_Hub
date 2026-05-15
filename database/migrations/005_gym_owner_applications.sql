-- ============================================================
-- Migration 005: Gym Owner Applications
-- ============================================================

USE `gobuff_db`;

-- Gym Owner Applications table
CREATE TABLE IF NOT EXISTS `gym_owner_applications` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`         INT UNSIGNED NOT NULL,
  `business_name`   VARCHAR(255) NOT NULL,
  `contact_number`  VARCHAR(50) NOT NULL,
  `address`         TEXT NOT NULL,
  `reason`          TEXT NOT NULL,
  `status`          ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by`     INT UNSIGNED NULL,
  `reviewed_at`     DATETIME NULL,
  `review_notes`    TEXT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_goa_user_id` (`user_id`),
  INDEX `idx_goa_status`  (`status`)
) ENGINE=InnoDB;

-- Documents attached to a gym owner application
CREATE TABLE IF NOT EXISTS `gym_owner_application_documents` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `application_id`  INT UNSIGNED NOT NULL,
  `document_type`   VARCHAR(100) NOT NULL,
  `file_name`       VARCHAR(255) NOT NULL,
  `file_original`   VARCHAR(255) NOT NULL,
  `file_size`       INT UNSIGNED NOT NULL DEFAULT 0,
  `file_type`       VARCHAR(100) NOT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`application_id`) REFERENCES `gym_owner_applications`(`id`) ON DELETE CASCADE,
  INDEX `idx_goad_app_id` (`application_id`)
) ENGINE=InnoDB;
