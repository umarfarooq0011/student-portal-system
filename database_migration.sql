-- Database Migration: Add created_by columns for tracking content ownership
-- Run this SQL in phpMyAdmin to update your database

-- Add created_by column to announcements table
ALTER TABLE `announcements`
ADD COLUMN `created_by` INT(11) NULL AFTER `created_at`,
ADD INDEX `idx_created_by` (`created_by`);

-- Add created_by column to assignments table
ALTER TABLE `assignments`
ADD COLUMN `created_by` INT(11) NULL AFTER `created_at`,
ADD INDEX `idx_created_by` (`created_by`);

-- Add created_by column to timetables table
ALTER TABLE `timetables`
ADD COLUMN `created_by` INT(11) NULL AFTER `created_at`,
ADD INDEX `idx_created_by` (`created_by`);

-- Optional: Set existing records to a default admin (update 29 to your admin's ID)
-- UPDATE announcements SET created_by = 29 WHERE created_by IS NULL;
-- UPDATE assignments SET created_by = 29 WHERE created_by IS NULL;
-- UPDATE timetables SET created_by = 29 WHERE created_by IS NULL;
