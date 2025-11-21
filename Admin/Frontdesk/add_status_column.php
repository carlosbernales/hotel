<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add status column if it doesn't exist
$check_column = "SHOW COLUMNS FROM table_bookings LIKE 'status'";
$result = mysqli_query($con, $check_column);

if (mysqli_num_rows($result) == 0) {
    $add_column = "ALTER TABLE table_bookings ADD COLUMN status VARCHAR(20) DEFAULT 'Pending'";
    if (mysqli_query($con, $add_column)) {
        echo "Status column added successfully";
    } else {
        echo "Error adding status column: " . mysqli_error($con);
    }
} else {
    echo "Status column already exists";
}
?>
