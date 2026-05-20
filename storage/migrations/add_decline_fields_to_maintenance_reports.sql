-- Migration: Add decline fields to maintenance_reports
-- Run this once against your GoBuff database.

ALTER TABLE `maintenance_reports`
    ADD COLUMN `decline_reason` TEXT NULL DEFAULT NULL
        COMMENT 'Reason provided by owner when declining a report or work'
        AFTER `resolution`,
    ADD COLUMN `declined_at` DATETIME NULL DEFAULT NULL
        COMMENT 'Timestamp when the report/work was declined'
        AFTER `decline_reason`;
