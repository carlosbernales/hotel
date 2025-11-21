CREATE TABLE IF NOT EXISTS payment_qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gcash_qr VARCHAR(255),
    gcash_name VARCHAR(100),
    gcash_number VARCHAR(50),
    maya_qr VARCHAR(255),
    maya_name VARCHAR(100),
    maya_number VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_record (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 