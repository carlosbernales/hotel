<?php
require_once 'db.php';

// Check if the room_number column exists in the bookings table
$check_column = "SHOW COLUMNS FROM `bookings` LIKE 'room_number'";
$result = mysqli_query($con, $check_column);

if (mysqli_num_rows($result) == 0) {
    // Add the room_number column if it doesn't exist
    $alter_table = "ALTER TABLE `bookings` ADD `room_number` VARCHAR(20) NULL AFTER `room_type_id`";
    
    if (mysqli_query($con, $alter_table)) {
        echo "Successfully added room_number column to bookings table.\n";
    } else {
        echo "Error adding room_number column: " . mysqli_error($con) . "\n";
    }
} else {
    echo "room_number column already exists in the bookings table.\n";
}

// Close the database connection
mysqli_close($con);
?>
