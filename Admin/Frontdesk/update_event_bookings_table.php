<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Drop the existing event_bookings table
$drop_sql = "DROP TABLE IF EXISTS event_bookings";
if (mysqli_query($con, $drop_sql)) {
    echo "Dropped existing event_bookings table\n";
} else {
    echo "Error dropping table: " . mysqli_error($con) . "\n";
    exit;
}

// Create event_bookings table with correct column names
$create_sql = "CREATE TABLE event_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email_address VARCHAR(100) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    num_guests INT NOT NULL,
    special_requests TEXT,
    additional_services TEXT,
    package_type VARCHAR(50) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_sql)) {
    echo "Created event_bookings table with updated structure\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
    exit;
}

// Show the new table structure
$result = mysqli_query($con, "DESCRIBE event_bookings");
if ($result) {
    echo "\nNew table structure for event_bookings:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
} else {
    echo "\nError getting table structure: " . mysqli_error($con) . "\n";
}
