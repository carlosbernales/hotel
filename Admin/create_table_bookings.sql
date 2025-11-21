-- Step 1: Create or Select Database
CREATE DATABASE IF NOT EXISTS your_database_name;
USE your_database_name;

-- Step 2: Create Table with 'package_type' column
CREATE TABLE IF NOT EXISTS table_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) UNIQUE,
    customer_name VARCHAR(100),
    booking_date DATE,
    booking_time TIME,
    num_guests INT,
    payment_method VARCHAR(50),
    total_amount DECIMAL(10,2),
    amount_paid DECIMAL(10,2),
    change_amount DECIMAL(10,2),
    special_requests TEXT,
    package_type VARCHAR(50),  -- Make sure this column exists
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 3: Verify the table structure
DESC table_bookings;

-- Step 4: Insert Sample Data
INSERT INTO table_bookings 
(booking_id, customer_name, booking_date, booking_time, num_guests, payment_method, total_amount, amount_paid, change_amount, special_requests, package_type, status) 
VALUES 
('B001', 'John Doe', '2024-02-08', '18:00:00', 4, 'Credit Card', 500.00, 500.00, 0.00, 'No onions', 'Premium', 'Confirmed'),
('B002', 'Jane Smith', '2024-02-09', '19:30:00', 2, 'Cash', 300.00, 300.00, 0.00, 'Vegetarian meal', 'Standard', 'Pending');

-- Step 5: Check if the data is inserted
SELECT * FROM table_bookings;
