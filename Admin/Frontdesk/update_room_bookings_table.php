<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Drop the existing bookings table
$drop_sql = "DROP TABLE IF EXISTS bookings";
if (mysqli_query($con, $drop_sql)) {
    echo "Dropped existing bookings table\n";
} else {
    echo "Error dropping table: " . mysqli_error($con) . "\n";
    exit;
}

// Create bookings table with updated structure
$create_sql = "CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    contact VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    num_guests INT NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_sql)) {
    echo "Created bookings table with updated structure\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
    exit;
}

// Show the new table structure
$result = mysqli_query($con, "DESCRIBE bookings");
if ($result) {
    echo "\nNew table structure for bookings:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
} else {
    echo "\nError getting table structure: " . mysqli_error($con) . "\n";
}
