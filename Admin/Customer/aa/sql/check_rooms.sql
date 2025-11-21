-- Check if rooms table exists and create it if not
CREATE TABLE IF NOT EXISTS rooms (
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
);

-- Insert sample rooms if table is empty
INSERT INTO rooms (name, description, bed_type, capacity, price, total_rooms, image, has_aircon, has_wifi, has_tv)
SELECT * FROM (
    SELECT 
        'Standard Double Room' as name,
        'Cozy room with modern amenities perfect for couples or solo travelers' as description,
        'Double Bed' as bed_type,
        2 as capacity,
        3200.00 as price,
        3 as total_rooms,
        'double.jpg' as image,
        TRUE as has_aircon,
        TRUE as has_wifi,
        TRUE as has_tv
    UNION ALL
    SELECT 
        'Family Room',
        'Spacious room ideal for families, featuring multiple beds and extra space',
        'Two Double Beds',
        4,
        4200.00,
        2,
        '4.jpg',
        TRUE,
        TRUE,
        TRUE
    UNION ALL
    SELECT 
        'Deluxe Family Room',
        'Our premium family accommodation with additional amenities and superior comfort',
        'King + Single Beds',
        5,
        5100.00,
        1,
        '3.jpg',
        TRUE,
        TRUE,
        TRUE
) AS temp
WHERE NOT EXISTS (SELECT 1 FROM rooms LIMIT 1);
