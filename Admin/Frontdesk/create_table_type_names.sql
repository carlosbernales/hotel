-- Create table_type_names table
CREATE TABLE IF NOT EXISTS table_type_names (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    is_disabled TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert existing package names from table_packages
INSERT IGNORE INTO table_type_names (name)
SELECT DISTINCT package_name 
FROM table_packages 
WHERE package_name IS NOT NULL; 