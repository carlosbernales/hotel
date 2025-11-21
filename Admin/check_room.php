<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check room 434
$room_query = "SELECT rn.*, rt.room_type 
               FROM room_numbers rn
               JOIN room_types rt ON rn.room_type_id = rt.room_type_id
               WHERE rn.room_number = '434'";

$result = mysqli_query($con, $room_query);

if (!$result) {
    die("Error executing query: " . mysqli_error($con));
}

if (mysqli_num_rows($result) > 0) {
    $room = mysqli_fetch_assoc($result);
    echo "Room 434 Details:<br>";
    echo "Status: " . $room['status'] . "<br>";
    echo "Room Type: " . $room['room_type'] . "<br>";
} else {
    echo "Room 434 not found in the database.";
}

// Also check the rooms table
$rooms_query = "SELECT r.*, rt.room_type 
                FROM rooms r
                JOIN room_types rt ON r.room_type_id = rt.room_type_id
                WHERE r.room_number = '434'";

$rooms_result = mysqli_query($con, $rooms_query);

if ($rooms_result && mysqli_num_rows($rooms_result) > 0) {
    $room = mysqli_fetch_assoc($rooms_result);
    echo "<br><br>Room 434 in rooms table:<br>";
    echo "Status: " . $room['status'] . "<br>";
    echo "Room Type: " . $room['room_type'] . "<br>";
} else {
    echo "<br><br>Room 434 not found in rooms table.";
}
?> 