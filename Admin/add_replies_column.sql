-- Script to add the replies column to the messages table
-- This should be run from phpMyAdmin or MySQL command line

-- First, check if the column exists (MySQL version)
SET @dbname = 'hotelms';
SET @tablename = 'messages';
SET @columnname = 'replies';
SET @query = CONCAT('SELECT COUNT(*) INTO @exist FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ''', @dbname, ''' AND TABLE_NAME = ''', @tablename, ''' AND COLUMN_NAME = ''', @columnname, ''';');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add the column if it doesn't exist
SET @query = CONCAT('SELECT IF(@exist = 0, CONCAT(''ALTER TABLE '', ''', @tablename, ''' ,'' ADD COLUMN '', ''', @columnname, ''' ,'' TEXT AFTER message''), ''SELECT "Column already exists"'') INTO @sql');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Direct version (if the above doesn't work)
-- This will fail safely if the column already exists
ALTER TABLE messages ADD COLUMN IF NOT EXISTS replies TEXT AFTER message; 