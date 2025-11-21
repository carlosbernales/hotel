<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'room_transfer_errors.log');

if (!isset($_GET['room_type_id'])) {
    error_log("get_room_numbers.php called without room_type_id");
    echo '<option value="">Please select a room type first</option>';
    exit;
}

$room_type_id = $_GET['room_type_id'];
$current_room_number = isset($_GET['current_room_number']) ? $_GET['current_room_number'] : '';

error_log("get_room_numbers.php called with room_type_id: $room_type_id, current_room_number: $current_room_number");

// First, show the current room number in an info option if it exists
if (!empty($current_room_number)) {
    echo '<option value="" disabled>Current Room: ' . htmlspecialchars($current_room_number) . '</option>';
}

// Fetch available room numbers for the selected room type
$query = "SELECT room_number_id, room_number 
          FROM room_numbers 
          WHERE room_type_id = ? 
          AND status = 'active'
          AND room_number != ?
          ORDER BY room_number";

$stmt = $con->prepare($query);

if (!$stmt) {
    error_log("Error preparing query: " . $con->error);
    die("Error preparing query");
}

$stmt->bind_param('ss', $room_type_id, $current_room_number);

if (!$stmt->execute()) {
    error_log("Error executing query: " . $stmt->error);
    die("Error executing query");
}

$result = $stmt->get_result();

if (!$result) {
    error_log("Error getting result set: " . $stmt->error);
    die("Error getting result set");
}

echo '<option value="">Select a room number...</option>';

while ($room = $result->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($room['room_number']) . '">' .
         'Room ' . htmlspecialchars($room['room_number']) .
         '</option>';
}

$stmt->close();
$con->close();
?> 