CREATE TABLE IF NOT EXISTS `gym_services` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('membership','class','personal_training','amenity','other') NOT NULL DEFAULT 'other',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `submitted_to_marketing` tinyint(1) NOT NULL DEFAULT 0,
  `submitted_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `campaigns` ADD COLUMN IF NOT EXISTS `service_ids` text DEFAULT NULL AFTER `banner_image`;
ALTER TABLE `campaigns` ADD COLUMN IF NOT EXISTS `platform_website` tinyint(1) NOT NULL DEFAULT 1 AFTER `service_ids`;
ALTER TABLE `campaigns` ADD COLUMN IF NOT EXISTS `platform_facebook` tinyint(1) NOT NULL DEFAULT 0 AFTER `platform_website`;
ALTER TABLE `campaigns` ADD COLUMN IF NOT EXISTS `platform_instagram` tinyint(1) NOT NULL DEFAULT 0 AFTER `platform_facebook`;
ALTER TABLE `campaigns` ADD COLUMN IF NOT EXISTS `size` varchar(100) DEFAULT NULL AFTER `platform_instagram`;
ALTER TABLE `campaigns` ADD COLUMN IF NOT EXISTS `theme` varchar(100) DEFAULT NULL AFTER `size`;
