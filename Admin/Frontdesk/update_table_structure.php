<?php
require_once 'db.php';

// First, drop the existing table
$drop_table_sql = "DROP TABLE IF EXISTS table_bookings";
if (mysqli_query($con, $drop_table_sql)) {
    echo "Old table dropped successfully\n";
} else {
    echo "Error dropping table: " . mysqli_error($con) . "\n";
    exit;
}

// Create table with correct structure
$create_table_sql = "CREATE TABLE table_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    num_guests INT NOT NULL,
    special_requests TEXT,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_table_sql)) {
    echo "Table 'table_bookings' created successfully with new structure\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
    exit;
}

// Check if table exists and show its structure
$result = mysqli_query($con, "DESCRIBE table_bookings");
if ($result) {
    echo "\nNew table structure:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
} else {
    echo "Error getting table structure: " . mysqli_error($con) . "\n";
}
