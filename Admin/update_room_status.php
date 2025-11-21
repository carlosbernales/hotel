<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // First, set all rooms to 'active' that don't have current bookings
    $sql = "UPDATE room_numbers rn
            SET rn.status = 'active'
            WHERE NOT EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.room_number = rn.room_number
                AND b.status IN ('confirmed', 'checked_in')
                AND b.check_out >= CURDATE()
            )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Failed to update room statuses: " . mysqli_error($con));
    }

    // Then, set rooms to 'occupied' that have current bookings
    $sql = "UPDATE room_numbers rn
            SET rn.status = 'occupied'
            WHERE EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.room_number = rn.room_number
                AND b.status IN ('confirmed', 'checked_in')
                AND b.check_in <= CURDATE()
                AND b.check_out >= CURDATE()
            )";
    
    if (!mysqli_query($con, $sql)) {
        throw new Exception("Failed to update occupied rooms: " . mysqli_error($con));
    }

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Room statuses updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connection
mysqli_close($con);
?> 