-- Add status column to room_types table if it doesn't exist
ALTER TABLE room_types 
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';

-- Set all existing room types to active by default
UPDATE room_types SET status = 'active' WHERE status IS NULL; 