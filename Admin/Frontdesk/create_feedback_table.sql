CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Customer_name VARCHAR(100) NOT NULL,
    feedback_type VARCHAR(50) NOT NULL,
    feedback TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolve_status BOOLEAN DEFAULT FALSE,
    resolve_date DATETIME NULL,
    remarks TEXT NULL
); 