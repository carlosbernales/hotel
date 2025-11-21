-- Drop existing rooms table if it exists
DROP TABLE IF EXISTS rooms;

-- Create rooms table with proper structure for room numbers
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL,
    room_type_id INT NOT NULL,
    description TEXT,
    status ENUM('Available', 'Occupied', 'Maintenance') NOT NULL DEFAULT 'Available',
    floor VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for faster lookups
CREATE INDEX idx_room_type_id ON rooms(room_type_id);
CREATE INDEX idx_status ON rooms(status);
CREATE INDEX idx_room_number ON rooms(room_number);

-- Insert sample room numbers for each room type
-- Replace the room_type_id values with your actual room type IDs
-- This is just an example - you may want to customize this for your actual room types

-- Sample for room type 1 (Standard)
INSERT INTO rooms (room_number, room_type_id, floor, description, status) VALUES 
('101', 1, '1', 'Standard room with city view', 'Available'),
('102', 1, '1', 'Standard room with garden view', 'Available'),
('103', 1, '1', 'Standard room with twin beds', 'Available');

-- Sample for room type 2 (Deluxe)
INSERT INTO rooms (room_number, room_type_id, floor, description, status) VALUES 
('201', 2, '2', 'Deluxe room with balcony', 'Available'),
('202', 2, '2', 'Deluxe room with sea view', 'Available'),
('203', 2, '2', 'Deluxe corner room', 'Available');

-- Sample for room type 3 (Family)
INSERT INTO rooms (room_number, room_type_id, floor, description, status) VALUES 
('301', 3, '3', 'Family room with extra beds', 'Available'),
('302', 3, '3', 'Family connecting rooms', 'Available'),
('303', 3, '3', 'Family suite with kitchenette', 'Available');

-- Note: Add more room numbers for each room type as needed
