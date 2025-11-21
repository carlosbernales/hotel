<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Update room 434's status to active
    $update_sql = "UPDATE room_numbers SET status = 'active' WHERE room_number = '434'";
    if (!mysqli_query($con, $update_sql)) {
        throw new Exception("Failed to update room 434 status: " . mysqli_error($con));
    }

    // Check if the update was successful
    $check_sql = "SELECT * FROM room_numbers WHERE room_number = '434'";
    $result = mysqli_query($con, $check_sql);
    
    if (!$result) {
        throw new Exception("Failed to check room status: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) {
        $room = mysqli_fetch_assoc($result);
        echo "Room 434 status updated successfully.<br>";
        echo "Current status: " . $room['status'];
    } else {
        echo "Room 434 not found in the database.";
    }

    // Commit transaction
    mysqli_commit($con);

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($con);
    echo "Error: " . $e->getMessage();
}
?> 