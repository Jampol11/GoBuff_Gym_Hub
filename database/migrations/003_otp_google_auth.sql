-- Migration 003: OTP + Google OAuth support
USE `gobuff_db`;

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
  INDEX `idx_otp_user_id`  (`user_id`),
  INDEX `idx_otp_expires`  (`expires_at`)
) ENGINE=InnoDB;

-- ── Google OAuth columns on users ────────────────────────────
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `google_id`     VARCHAR(100) NULL AFTER `username`,
  ADD COLUMN IF NOT EXISTS `avatar_url`    VARCHAR(500) NULL AFTER `google_id`,
  ADD COLUMN IF NOT EXISTS `auth_provider` ENUM('local','google') NOT NULL DEFAULT 'local' AFTER `avatar_url`;

ALTER TABLE `users`
  ADD UNIQUE INDEX IF NOT EXISTS `idx_google_id` (`google_id`);
