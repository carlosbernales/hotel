-- SQL to create table_number table
CREATE TABLE IF NOT EXISTS table_number (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
    occupied_at TIMESTAMP NULL DEFAULT NULL,
    order_id INT NULL DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial table data (tables 1-10)
INSERT INTO table_number (table_number, status) 
SELECT t.n, 'available' 
FROM (
    SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) t
WHERE NOT EXISTS (SELECT 1 FROM table_number WHERE table_number = t.n);
