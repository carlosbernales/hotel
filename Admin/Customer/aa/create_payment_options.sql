-- Create payment_options table
CREATE TABLE IF NOT EXISTS payment_options (
    payment_option_id INT AUTO_INCREMENT PRIMARY KEY,
    option_name VARCHAR(100) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default payment options if table is empty
INSERT INTO payment_options (option_name, percentage) 
SELECT 'Full Payment', 100.00
WHERE NOT EXISTS (SELECT 1 FROM payment_options)
UNION ALL
SELECT '50% Downpayment', 50.00
WHERE NOT EXISTS (SELECT 1 FROM payment_options)
UNION ALL
SELECT '30% Downpayment', 30.00
WHERE NOT EXISTS (SELECT 1 FROM payment_options);
