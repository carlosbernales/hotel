<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'room_transfer_errors.log');

// Get the current room type ID if provided
$current_room_type_id = isset($_GET['current_room_id']) ? $_GET['current_room_id'] : '';

// Log request
error_log("get_transfer_rooms.php called with current_room_id: " . $current_room_type_id);

// Fetch all active room types with available rooms
$query = "SELECT rt.room_type_id, rt.room_type, rt.price, rt.capacity,
          COUNT(rn.room_number) as available_rooms
          FROM room_types rt
          LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id 
          AND rn.status = 'active'
          WHERE rt.status = 'active'
          GROUP BY rt.room_type_id, rt.room_type, rt.price, rt.capacity
          HAVING available_rooms > 0
          ORDER BY rt.room_type";

$stmt = $con->prepare($query);

if (!$stmt) {
    error_log("Error preparing query: " . $con->error);
    die("Error preparing query");
}

if (!$stmt->execute()) {
    error_log("Error executing query: " . $stmt->error);
    die("Error executing query");
}

$result = $stmt->get_result();

if (!$result) {
    error_log("Error getting result set: " . $stmt->error);
    die("Error getting result set");
}

// Start with an empty option
echo '<option value="">Select a room type...</option>';

// Add options for each available room type
while ($room = $result->fetch_assoc()) {
    $selected = ($room['room_type_id'] == $current_room_type_id) ? 'selected' : '';
    echo '<option value="' . htmlspecialchars($room['room_type_id']) . '" ' .
         'data-price="' . htmlspecialchars($room['price']) . '" ' .
         'data-capacity="' . htmlspecialchars($room['capacity']) . '" ' .
         'data-room-type-id="' . htmlspecialchars($room['room_type_id']) . '" ' .
         $selected . '>' .
         htmlspecialchars($room['room_type']) . ' - ₱' . number_format($room['price'], 2) .
         ' (' . $room['available_rooms'] . ' available)' .
         '</option>';
}

$stmt->close();
$con->close();
?> 