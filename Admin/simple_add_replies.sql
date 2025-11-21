-- Simple script to add replies column to messages table
-- Run this in phpMyAdmin or MySQL command line

-- Add column if it doesn't exist (MySQL 8.0+)
ALTER TABLE messages ADD COLUMN IF NOT EXISTS replies TEXT AFTER message;

-- For older MySQL versions, can use this approach instead:
-- First check if column exists and then add it if missing

-- SELECT COUNT(*) INTO @exist 
-- FROM information_schema.COLUMNS 
-- WHERE TABLE_SCHEMA = 'hotelms' 
-- AND TABLE_NAME = 'messages' 
-- AND COLUMN_NAME = 'replies';

-- SET @addCol = IF(@exist = 0, 'ALTER TABLE messages ADD COLUMN replies TEXT AFTER message', 'SELECT "Column already exists"');
-- PREPARE stmt FROM @addCol;
-- EXECUTE stmt;
-- DEALLOCATE PREPARE stmt; 