CREATE TABLE IF NOT EXISTS event_spaces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    space_name VARCHAR(100) NOT NULL,
    display_category ENUM('venue_rental', 'event_package') NOT NULL DEFAULT 'venue_rental',
    space_type VARCHAR(50) NOT NULL,
    category VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    description TEXT,
    amenities TEXT,
    image_path VARCHAR(255),
    gallery_images TEXT,
    status ENUM('Available', 'Occupied', 'Maintenance') NOT NULL DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
