-- Migration 007: Campaign Participations
-- Allows members to join campaigns, track referrals, and earn rewards.

CREATE TABLE IF NOT EXISTS `campaign_participations` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id`   INT UNSIGNED NOT NULL,
  `member_id`     INT UNSIGNED NOT NULL,
  `referral_code` VARCHAR(32)  DEFAULT NULL COMMENT 'Unique code this member can share',
  `referred_by`   INT UNSIGNED DEFAULT NULL COMMENT 'member_id of the person who referred this member',
  `reward_status` ENUM('pending','applied','expired') NOT NULL DEFAULT 'pending',
  `joined_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_member_campaign` (`campaign_id`, `member_id`),
  UNIQUE KEY `uq_referral_code`   (`referral_code`),
  CONSTRAINT `fk_cp_campaign`    FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cp_member`      FOREIGN KEY (`member_id`)   REFERENCES `members`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_cp_referred_by` FOREIGN KEY (`referred_by`) REFERENCES `members`(`id`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
