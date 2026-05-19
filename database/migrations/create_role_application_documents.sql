-- Migration: Create role_application_documents table
-- Stores supporting documents uploaded with employee role applications.

CREATE TABLE IF NOT EXISTS `role_application_documents` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` INT(10) UNSIGNED NOT NULL,
  `document_type`  ENUM('resume','biodata','birth_certificate','government_id','certificate','other') NOT NULL DEFAULT 'other',
  `file_name`      VARCHAR(255) NOT NULL,
  `file_original`  VARCHAR(255) NOT NULL,
  `file_size`      INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_type`      VARCHAR(100) NOT NULL,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_application_id` (`application_id`),
  CONSTRAINT `fk_rad_application`
    FOREIGN KEY (`application_id`) REFERENCES `role_applications` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
