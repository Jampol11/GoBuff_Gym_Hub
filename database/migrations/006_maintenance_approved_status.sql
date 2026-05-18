-- Migration 006: Add 'approved' status and approved_at column to maintenance_reports
-- Run this against your gobuff database

ALTER TABLE `maintenance_reports`
  MODIFY COLUMN `status` ENUM('pending','in_progress','completed','approved','cancelled') DEFAULT 'pending',
  ADD COLUMN `approved_at` DATETIME DEFAULT NULL AFTER `completed_at`;
