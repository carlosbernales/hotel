CREATE TABLE IF NOT EXISTS dining_tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    table_type ENUM('Couple', 'Friends', 'Family', 'Package A', 'Package B', 'Package C') NOT NULL,
    category ENUM('regular', 'ultimate') NOT NULL DEFAULT 'regular',
    capacity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'occupied') DEFAULT 'available',
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
