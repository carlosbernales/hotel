-- Add status column to bookings table if it doesn't exist
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending';

-- Add status column to event_bookings table if it doesn't exist
ALTER TABLE event_bookings 
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending';

-- Add status column to table_bookings table if it doesn't exist
ALTER TABLE table_bookings 
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending';
