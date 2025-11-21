<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $room_type = isset($_POST['room_type']) ? $_POST['room_type'] : null;

    if (empty($check_in) || empty($check_out)) {
        echo json_encode(['available' => false, 'error' => 'Please select both check-in and check-out dates']);
        exit;
    }

    // Convert input dates to timestamps
    $check_in_ts = strtotime($check_in);
    $check_out_ts = strtotime($check_out);
    
    // Static booking period: November 10-25, 2025
    $static_booking_start = strtotime('2025-11-10');
    $static_booking_end = strtotime('2025-11-25');
    
    // Check for overlap with static booking period
    $has_static_booking = ($check_in_ts < $static_booking_end && $check_out_ts > $static_booking_start);
    
    // Check for database bookings if room_type is provided
    $has_database_booking = false;
    
    if ($room_type !== null) {
        // Query to check for overlapping bookings in the database
        $check_sql = "SELECT COUNT(*) as count FROM bookings 
                     WHERE room_type = ? 
                     AND status != 'Cancelled'
                     AND (
                         (check_in <= ? AND check_out >= ?) OR
                         (check_in <= ? AND check_out >= ?) OR
                         (check_in >= ? AND check_out <= ?)
                     )";
        
        $check_stmt = mysqli_prepare($con, $check_sql);
        if ($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, 'sssssss',
                $room_type, 
                $check_out, $check_in,
                $check_in, $check_in,
                $check_in, $check_out
            );
            
            if (mysqli_stmt_execute($check_stmt)) {
                $result = mysqli_stmt_get_result($check_stmt);
                $row = mysqli_fetch_assoc($result);
                $has_database_booking = ($row['count'] > 0);
            }
        }
    }
    
    // Room is available if there are no bookings in either static period or database
    $is_available = !$has_static_booking && !$has_database_booking;
    
    // Get all room types and their availability
    $rooms = [];
    $room_types_query = "SELECT room_type_id, room_type FROM room_types WHERE status = 'active'";
    $room_types_result = mysqli_query($con, $room_types_query);
    
    while ($room_type_row = mysqli_fetch_assoc($room_types_result)) {
        $room_type_id = $room_type_row['room_type_id'];
        $room_type_name = $room_type_row['room_type'];
        
        // Check if this room type is available for the selected dates
        $is_room_available = true;
        $message = 'Available';
        
        // Check static booking for this room type
        if ($has_static_booking) {
            $is_room_available = false;
            $message = 'Not available (Booked from Nov 10-25, 2025)';
        } else {
            // Check database bookings for this room type
            $room_check_sql = "SELECT COUNT(*) as count FROM bookings 
                             WHERE room_type_id = ? 
                             AND status != 'Cancelled'
                             AND (
                                 (check_in <= ? AND check_out >= ?) OR
                                 (check_in <= ? AND check_out >= ?) OR
                                 (check_in >= ? AND check_out <= ?)
                             )";
            
            if ($stmt = mysqli_prepare($con, $room_check_sql)) {
                mysqli_stmt_bind_param($stmt, 'issssss', 
                    $room_type_id,
                    $check_out, $check_in,
                    $check_in, $check_in,
                    $check_in, $check_out
                );
                
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    if ($row['count'] > 0) {
                        $is_room_available = false;
                        $message = 'Already booked for selected dates';
                    }
                }
            }
        }
        
        $rooms[] = [
            'room_type_id' => $room_type_id,
            'room_type' => $room_type_name,
            'available' => $is_room_available,
            'message' => $message
        ];
    }
    
    echo json_encode([
        'success' => true,
        'available' => $is_available,
        'static_booking' => $has_static_booking,
        'static_booking_dates' => ['2025-11-10', '2025-11-25'],
        'check_in' => $check_in,
        'check_out' => $check_out,
        'rooms' => $rooms
    ]);
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
