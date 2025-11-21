<?php
require_once "db.php";

try {
    // Create table_reservations table
    $sql = "CREATE TABLE IF NOT EXISTS table_reservations (
        reservation_id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        contact_number VARCHAR(20) NOT NULL,
        guest_count INT NOT NULL,
        table_type VARCHAR(50) NOT NULL,
        reservation_datetime DATETIME NOT NULL,
        special_requests TEXT,
        advance_order JSON DEFAULT NULL,
        payment_type ENUM('full', 'partial') DEFAULT NULL,
        payment_method ENUM('cash', 'gcash', 'maya', 'bank') DEFAULT NULL,
        total_amount DECIMAL(10,2) DEFAULT 0.00,
        amount_to_pay DECIMAL(10,2) DEFAULT 0.00,
        payment_status ENUM('pending', 'paid') DEFAULT 'pending',
        status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (mysqli_query($con, $sql)) {
        echo "table_reservations table created successfully";
    } else {
        throw new Exception("Error creating table: " . mysqli_error($con));
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 