<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $room_type = $_POST['room_type'] ?? '';

    if (empty($check_in) || empty($check_out) || empty($room_type)) {
        echo json_encode(['available' => false, 'error' => 'Missing required parameters']);
        exit;
    }

    // Query to check for overlapping bookings
    $check_sql = "SELECT COUNT(*) as count FROM bookings 
                 WHERE room_type = ? 
                 AND status != 'Cancelled'
                 AND (
                     (check_in <= ? AND check_out >= ?) OR
                     (check_in <= ? AND check_out >= ?) OR
                     (check_in >= ? AND check_out <= ?)
                 )";
    
    $check_stmt = mysqli_prepare($con, $check_sql);
    if (!$check_stmt) {
        echo json_encode(['available' => false, 'error' => 'Database error: ' . mysqli_error($con)]);
        exit;
    }

    mysqli_stmt_bind_param($check_stmt, 'sssssss',
        $room_type, 
        $check_out, $check_in,  // Case 1: Existing booking spans our dates
        $check_in, $check_in,   // Case 2: Our check-in falls within existing booking
        $check_in, $check_out   // Case 3: Our booking spans existing booking
    );

    if (!mysqli_stmt_execute($check_stmt)) {
        echo json_encode(['available' => false, 'error' => 'Error checking availability: ' . mysqli_stmt_error($check_stmt)]);
        exit;
    }

    $result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($result);
    
    echo json_encode(['available' => ($row['count'] == 0)]);
} else {
    echo json_encode(['available' => false, 'error' => 'Invalid request method']);
}
