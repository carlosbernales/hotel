<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Update room types with correct capacities and ensure consistent data
$update_room_types = "UPDATE room_types SET 
    capacity = CASE room_type_id
        WHEN 1 THEN 2  -- Standard Double Room should have capacity 2
        WHEN 2 THEN 4  -- Deluxe Family Room should have capacity 4
        WHEN 3 THEN 5  -- Family Room should have capacity 5
        ELSE capacity
    END,
    beds = CASE room_type_id
        WHEN 1 THEN '2 Single Beds'
        WHEN 2 THEN '1 Queen Bed, 1 Single Bed'
        WHEN 3 THEN '1 Queen Bed, 2 Single Beds'
        ELSE beds
    END
WHERE room_type_id IN (1, 2, 3)";

if (mysqli_query($con, $update_room_types)) {
    echo "Room types updated successfully<br>";
} else {
    echo "Error updating room types: " . mysqli_error($con) . "<br>";
}

// Delete duplicate room types (Standard, Deluxe, Suite that were added multiple times)
$delete_duplicates = "DELETE FROM room_types WHERE room_type_id > 6";

if (mysqli_query($con, $delete_duplicates)) {
    echo "Duplicate room types removed successfully<br>";
} else {
    echo "Error removing duplicate room types: " . mysqli_error($con) . "<br>";
}

// Ensure rooms table has correct inventory
$update_rooms = "INSERT INTO rooms (room_type_id, total_rooms, available_rooms, status)
VALUES 
    (1, 10, 10, 'Available'),  -- Standard Double Room
    (2, 8, 8, 'Available'),   -- Deluxe Family Room
    (3, 6, 6, 'Available')    -- Family Room
ON DUPLICATE KEY UPDATE
    total_rooms = VALUES(total_rooms),
    available_rooms = VALUES(total_rooms),
    status = VALUES(status)";

if (mysqli_query($con, $update_rooms)) {
    echo "Room inventory updated successfully<br>";
} else {
    echo "Error updating room inventory: " . mysqli_error($con) . "<br>";
}

// Add indexes for better performance
$add_indexes = "
ALTER TABLE rooms ADD INDEX idx_room_type (room_type_id);
ALTER TABLE room_bookings ADD INDEX idx_booking (booking_id);
ALTER TABLE guest_names ADD INDEX idx_booking_guest (booking_id);
";

if (mysqli_multi_query($con, $add_indexes)) {
    do {
        // Process each result set
        if ($result = mysqli_store_result($con)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($con));
    echo "Indexes added successfully<br>";
} else {
    echo "Error adding indexes: " . mysqli_error($con) . "<br>";
}

echo "<br>Room inventory fix completed. Please try booking again.";
?> 