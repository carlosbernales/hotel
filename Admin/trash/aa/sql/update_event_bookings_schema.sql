-- Add new columns to event_bookings table if they don't exist
ALTER TABLE event_bookings
ADD COLUMN IF NOT EXISTS event_type VARCHAR(100) DEFAULT 'General' AFTER package_name,
ADD COLUMN IF NOT EXISTS other_event_type VARCHAR(255) DEFAULT NULL AFTER event_type,
ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'pending' AFTER status,
ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(100) DEFAULT NULL AFTER payment_status,
MODIFY COLUMN payment_method ENUM('cash', 'gcash', 'bank', 'paymongo', 'card') NOT NULL DEFAULT 'paymongo',
MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending';
