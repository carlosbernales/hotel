<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to execute SQL and handle errors
function executeSql($con, $sql, $tableName) {
    if (mysqli_query($con, $sql)) {
        echo "Table '$tableName' created successfully or already exists\n";
        return true;
    } else {
        echo "Error creating table '$tableName': " . mysqli_error($con) . "\n";
        return false;
    }
}

// Create table_bookings table
$table_bookings_sql = "CREATE TABLE IF NOT EXISTS table_bookings (
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

// Create event_bookings table
$event_bookings_sql = "CREATE TABLE IF NOT EXISTS event_bookings (
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

// Create bookings (room) table
$room_bookings_sql = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) UNIQUE NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    contact VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    arrival_time TIME NOT NULL,
    num_guests INT NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Create booking_guests table
$booking_guests_sql = "CREATE TABLE IF NOT EXISTS booking_guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
)";

// Execute all create table statements
echo "Creating tables...\n\n";
executeSql($con, $table_bookings_sql, 'table_bookings');
executeSql($con, $event_bookings_sql, 'event_bookings');
executeSql($con, $room_bookings_sql, 'bookings');
executeSql($con, $booking_guests_sql, 'booking_guests');

// Show table structures
$tables = ['table_bookings', 'event_bookings', 'bookings', 'booking_guests'];

foreach ($tables as $table) {
    $result = mysqli_query($con, "DESCRIBE $table");
    if ($result) {
        echo "\nTable structure for '$table':\n";
        while ($row = mysqli_fetch_assoc($result)) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
        }
    } else {
        echo "\nError getting table structure for '$table': " . mysqli_error($con) . "\n";
    }
}
