<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // First, check if there are any existing bookings
    $check_sql = "SELECT COUNT(*) as count FROM bookings";
    $result = mysqli_query($con, $check_sql);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        // If there are existing bookings, we need to back them up
        echo "Backing up existing bookings...<br>";
        
        // Create temporary table
        mysqli_query($con, "CREATE TABLE bookings_backup LIKE bookings");
        mysqli_query($con, "INSERT INTO bookings_backup SELECT * FROM bookings");
        
        // Drop the existing table
        mysqli_query($con, "DROP TABLE bookings");
    }
    
    // Create the table with proper AUTO_INCREMENT
    $create_sql = "CREATE TABLE bookings (
        `booking_id` int(11) NOT NULL AUTO_INCREMENT,
        `booking_reference` varchar(255) NOT NULL,
        `user_id` int(11) DEFAULT NULL,
        `first_name` varchar(50) DEFAULT NULL,
        `last_name` varchar(50) DEFAULT NULL,
        `booking_type` varchar(50) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `contact` varchar(20) DEFAULT NULL,
        `check_in` date DEFAULT NULL,
        `check_out` date DEFAULT NULL,
        `arrival_time` time DEFAULT NULL,
        `number_of_guests` int(11) DEFAULT NULL,
        `room_type_id` int(11) DEFAULT NULL,
        `room_quantity` int(11) DEFAULT NULL,
        `payment_option` varchar(50) DEFAULT NULL,
        `payment_method` varchar(50) DEFAULT NULL,
        `total_amount` decimal(10,2) DEFAULT NULL,
        `extra_charges` decimal(10,2) DEFAULT 0.00,
        `status` varchar(50) DEFAULT 'pending',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `nights` int(11) NOT NULL,
        `downpayment_amount` decimal(10,2) DEFAULT NULL,
        `remaining_balance` decimal(10,2) DEFAULT NULL,
        `discount_type` varchar(50) DEFAULT NULL,
        `discount_amount` decimal(10,2) DEFAULT 0.00,
        `discount_percentage` decimal(5,2) DEFAULT 0.00,
        `payment_reference` varchar(50) NOT NULL,
        `payment_proof` varchar(255) NOT NULL,
        `user_types` enum('admin','frontdesk') NOT NULL DEFAULT 'frontdesk',
        PRIMARY KEY (`booking_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if (!mysqli_query($con, $create_sql)) {
        throw new Exception("Error creating table: " . mysqli_error($con));
    }
    
    // If we had existing bookings, restore them
    if ($row['count'] > 0) {
        echo "Restoring bookings data...<br>";
        
        // Insert the data back, but exclude the booking_id column
        $columns = implode(", ", [
            "booking_reference", "user_id", "first_name", "last_name", "booking_type",
            "email", "contact", "check_in", "check_out", "arrival_time",
            "number_of_guests", "room_type_id", "room_quantity", "payment_option",
            "payment_method", "total_amount", "extra_charges", "status", "created_at",
            "nights", "downpayment_amount", "remaining_balance", "discount_type",
            "discount_amount", "discount_percentage", "payment_reference",
            "payment_proof", "user_types"
        ]);
        
        $restore_sql = "INSERT INTO bookings ($columns) 
                       SELECT $columns FROM bookings_backup";
                       
        if (!mysqli_query($con, $restore_sql)) {
            throw new Exception("Error restoring data: " . mysqli_error($con));
        }
        
        // Drop the backup table
        mysqli_query($con, "DROP TABLE bookings_backup");
    }
    
    // Commit the transaction
    mysqli_commit($con);
    echo "Successfully fixed the bookings table structure!<br>";
    echo "The booking_id column is now auto-incrementing.<br>";
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($con);
    echo "Error: " . $e->getMessage() . "<br>";
}

// Close the connection
mysqli_close($con);
?> 