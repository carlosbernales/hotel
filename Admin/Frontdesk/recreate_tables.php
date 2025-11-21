<?php
include 'db.php';

// Function to check if a table exists
function tableExists($con, $tableName) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Create bookings table if it doesn't exist
if (!tableExists($con, 'bookings')) {
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
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
        payment_option VARCHAR(20) NOT NULL,
        payment_method VARCHAR(20) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_status VARCHAR(20) DEFAULT 'Pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($con, $sql)) {
        echo "Table 'bookings' created successfully<br>";
    } else {
        echo "Error creating table 'bookings': " . mysqli_error($con) . "<br>";
    }
}

// Create table_bookings table if it doesn't exist
if (!tableExists($con, 'table_bookings')) {
    $sql = "CREATE TABLE IF NOT EXISTS table_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(20) UNIQUE NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        booking_date DATE NOT NULL,
        booking_time TIME NOT NULL,
        num_guests INT NOT NULL,
        special_requests TEXT,
        package_type VARCHAR(100) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        amount_paid DECIMAL(10,2) NOT NULL,
        change_amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_status VARCHAR(20) DEFAULT 'Pending',
        status VARCHAR(20) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($con, $sql)) {
        echo "Table 'table_bookings' created successfully<br>";
    } else {
        echo "Error creating table 'table_bookings': " . mysqli_error($con) . "<br>";
    }
}

// Check if tables exist and show row counts
$tables = ['bookings', 'table_bookings'];
foreach ($tables as $table) {
    if (tableExists($con, $table)) {
        $result = mysqli_query($con, "SELECT COUNT(*) as count FROM $table");
        $row = mysqli_fetch_assoc($result);
        echo "Table '$table' exists and has {$row['count']} rows<br>";
    } else {
        echo "Table '$table' does not exist<br>";
    }
}
