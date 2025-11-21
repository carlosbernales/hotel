ALTER TABLE bookings
ADD COLUMN user_types ENUM('admin', 'frontdesk') NOT NULL DEFAULT 'frontdesk'; 