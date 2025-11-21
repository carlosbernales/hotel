-- Drop existing tables if they exist
DROP TABLE IF EXISTS rooms;

-- Create rooms table
CREATE TABLE rooms (
    room_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    bed_type VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_rooms INT NOT NULL DEFAULT 1,
    image VARCHAR(255) NOT NULL,
    has_aircon BOOLEAN DEFAULT TRUE,
    has_wifi BOOLEAN DEFAULT TRUE,
    has_tv BOOLEAN DEFAULT TRUE,
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample rooms
INSERT INTO rooms (name, description, bed_type, capacity, price, total_rooms, image, has_aircon, has_wifi, has_tv) VALUES
('Standard Double Room', 'Cozy room with modern amenities perfect for couples or solo travelers', 'Double Bed', 2, 3200.00, 3, 'double.jpg', TRUE, TRUE, TRUE),
('Family Room', 'Spacious room ideal for families, featuring multiple beds and extra space', 'Two Double Beds', 4, 4200.00, 2, '4.jpg', TRUE, TRUE, TRUE),
('Deluxe Family Room', 'Our premium family accommodation with additional amenities and superior comfort', 'King + Single Beds', 5, 5100.00, 1, '3.jpg', TRUE, TRUE, TRUE);
