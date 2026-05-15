-- ============================================================
-- Migration 004: Role Applications + user role
-- ============================================================

USE `gobuff_db`;

-- Add 'user' to the users.role ENUM (must come before 'member' in precedence)
ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('gym_owner','admin','marketing','trainer','maintenance','member','user')
    NOT NULL DEFAULT 'user';

-- ── Role Applications ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `role_applications` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED NOT NULL,
  `requested_role` ENUM('admin','marketing','trainer','maintenance','member') NOT NULL,
  `reason`       TEXT,
  `status`       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by`  INT UNSIGNED NULL,
  `reviewed_at`  DATETIME NULL,
  `review_notes` TEXT NULL,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_ra_user_id` (`user_id`),
  INDEX `idx_ra_status`  (`status`)
) ENGINE=InnoDB;

-- Add 'user' label to roles reference table
INSERT IGNORE INTO `roles` (`name`, `label`, `description`) VALUES
  ('user', 'User', 'Newly registered user pending role assignment');
