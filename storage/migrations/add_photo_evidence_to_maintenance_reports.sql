-- Migration: Add photo_evidence column to maintenance_reports
-- Run this once against your GoBuff database.

ALTER TABLE `maintenance_reports`
    ADD COLUMN `photo_evidence` VARCHAR(255) NULL DEFAULT NULL
        COMMENT 'Relative path under public/assets/uploads/ for the uploaded photo'
        AFTER `description`;
