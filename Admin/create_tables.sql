-- Create event packages table
CREATE TABLE IF NOT EXISTS event_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    package_type ENUM('Wedding', 'Birthday', 'Corporate', 'Other') NOT NULL,
    description TEXT,
    inclusions JSON NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    max_guests INT NOT NULL,
    duration_hours INT NOT NULL,
    image_path VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create event images table
CREATE TABLE IF NOT EXISTS event_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    caption TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample event packages
INSERT INTO event_packages (package_name, package_type, description, inclusions, price, max_guests, duration_hours, image_path) VALUES
('Standard Package', 'Wedding', '5-hour venue rental with basic amenities', 
 JSON_ARRAY('5-hour venue rental', 'Basic sound system', 'Standard decoration', 'Basic catering service'),
 47500.00, 30, 5, 'images/hall.jpg'),
 
('Premium Package', 'Wedding', '5-hour venue rental with premium amenities',
 JSON_ARRAY('5-hour venue rental', 'Professional sound system', 'Premium decoration', 'Full catering service', 'Photo/Video coverage'),
 85000.00, 50, 5, 'images/hall2.jpg'),
 
('Deluxe Package', 'Wedding', '8-hour venue rental with luxury amenities',
 JSON_ARRAY('8-hour venue rental', 'Complete sound system', 'Luxury decoration', 'Premium catering service', 'Photo/Video coverage', 'Event coordinator'),
 150000.00, 100, 8, 'images/hall3.jpg');

-- Insert sample event images
INSERT INTO event_images (image_path, caption, is_featured) VALUES
('images/hall.jpg', 'Elegant Wedding Reception', 1),
('images/hall2.jpg', 'Garden Wedding Ceremony', 1),
('images/hall3.jpg', 'Birthday Celebration Setup', 1); 