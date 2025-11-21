-- Add missing columns to event_bookings table
ALTER TABLE event_bookings
ADD COLUMN IF NOT EXISTS customer_name VARCHAR(255) NOT NULL AFTER user_id,
ADD COLUMN IF NOT EXISTS base_price DECIMAL(10,2) NOT NULL AFTER package_price,
ADD COLUMN IF NOT EXISTS overtime_hours DECIMAL(4,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS overtime_charge DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS extra_guests INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS extra_guest_charge DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS event_date DATE NOT NULL AFTER reservation_date,
ADD COLUMN IF NOT EXISTS event_type VARCHAR(50) NOT NULL AFTER status,
ADD COLUMN IF NOT EXISTS reserve_type VARCHAR(50) DEFAULT 'Regular',
ADD COLUMN IF NOT EXISTS booking_source VARCHAR(50) DEFAULT 'Website Booking';

-- Modify existing columns to match our requirements
ALTER TABLE event_bookings
MODIFY COLUMN capacity INT DEFAULT 0,
MODIFY COLUMN duration_hours DECIMAL(4,2) DEFAULT 0.00,
MODIFY COLUMN payment_method VARCHAR(50) NOT NULL,
MODIFY COLUMN payment_type VARCHAR(50) NOT NULL,
MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending';

-- Rename status to booking_status for clarity
ALTER TABLE event_bookings
CHANGE status booking_status VARCHAR(20) DEFAULT 'pending';
