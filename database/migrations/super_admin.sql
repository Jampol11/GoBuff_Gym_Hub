-- ============================================================
-- Super Admin Migration
-- Run this against gobuff_db
-- ============================================================

-- 1. Add super_admin to users.role ENUM
ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('super_admin','gym_owner','admin','marketing','trainer','maintenance','member','user')
  NOT NULL DEFAULT 'user';

-- 2. Insert super_admin into the roles reference table
INSERT IGNORE INTO `roles` (`name`, `label`, `description`)
VALUES ('super_admin', 'Super Admin', 'Platform-level administrator with full system authority');

-- 3. Create a default Super Admin account
--    Password: SuperAdmin@123  (bcrypt hash below — change after first login)
INSERT INTO `users` (`name`, `email`, `username`, `password`, `role`, `status`, `created_at`)
VALUES (
  'Super Administrator',
  'superadmin@gobuff.com',
  'superadmin',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password (CHANGE THIS)
  'super_admin',
  'active',
  NOW()
);

-- 4. Gyms table (for multi-gym management)
CREATE TABLE IF NOT EXISTS `gyms` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NOT NULL,
  `address`     TEXT,
  `contact`     VARCHAR(100),
  `email`       VARCHAR(150),
  `owner_id`    INT UNSIGNED DEFAULT NULL COMMENT 'FK to users.id (gym_owner role)',
  `status`      ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `description` TEXT,
  `created_by`  INT UNSIGNED DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gyms_owner` (`owner_id`),
  KEY `idx_gyms_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insert a default gym record representing the current single gym
INSERT IGNORE INTO `gyms` (`id`, `name`, `address`, `status`, `created_at`)
VALUES (1, 'GoBuff Gym', 'Main Branch', 'active', NOW());
