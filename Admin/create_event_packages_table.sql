CREATE TABLE IF NOT EXISTS event_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    package_type ENUM('Wedding', 'Birthday', 'Corporate', 'Other') NOT NULL,
    description TEXT,
    inclusions TEXT,
    price DECIMAL(10,2) NOT NULL,
    max_guests INT NOT NULL,
    duration_hours INT NOT NULL,
    image_path VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
