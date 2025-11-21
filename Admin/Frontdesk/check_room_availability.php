<?php
require_once 'db.php';

if (!isset($_POST['room_type_id'])) {
    echo json_encode(['error' => 'Room type ID not provided']);
    exit;
}

$room_type_id = mysqli_real_escape_string($con, $_POST['room_type_id']);

// Query to get available rooms
$query = "SELECT r.available_rooms 
          FROM rooms r 
          WHERE r.room_type_id = '$room_type_id'";

$result = mysqli_query($con, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'available_rooms' => $row['available_rooms']
    ]);
} else {
    echo json_encode([
        'error' => 'Failed to fetch room availability',
        'available_rooms' => 0
    ]);
}

mysqli_close($con); 