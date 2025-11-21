<?php
include 'db.php';

$sql = "CREATE TABLE IF NOT EXISTS table_bookings (
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
)";

if (mysqli_query($con, $sql)) {
    echo "Table 'table_bookings' created successfully";
} else {
    echo "Error creating table: " . mysqli_error($con);
}

mysqli_close($con);
?>
