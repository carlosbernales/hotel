<?php
// This endpoint checks room type availability without date constraints

// Clear any previous output
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Set headers first
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/room_type_availability_errors.log');

// Include database connection
require 'db_con.php';

// Check if room_type_id is provided
if (!isset($_GET['room_type_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Room type ID is required']);
    exit;
}

$room_type_id = (int)$_GET['room_type_id'];

try {
    // Get total active rooms for this type
    $sql = "
        SELECT 
            rt.room_type_id, 
            rt.room_type, 
            COUNT(rn.room_number_id) as total_rooms,
            COUNT(rn.room_number_id) - IFNULL(
                (SELECT COUNT(*) 
                 FROM booking_rooms br
                 JOIN bookings b ON br.booking_id = b.booking_id
                 WHERE br.room_type_id = rt.room_type_id
                 AND b.status IN ('confirmed', 'checked_in')
                 AND (
                     (b.check_in <= CURDATE() AND b.check_out > CURDATE())  -- Currently occupied
                     OR (b.check_in = CURDATE())  -- Check-in today
                 )
                ), 0) as available_rooms
        FROM room_types rt
        LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id AND rn.status = 'active'
        WHERE rt.room_type_id = ?
        AND rt.status = 'active'
        GROUP BY rt.room_type_id, rt.room_type
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$room_type_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Room type not found or inactive'
        ]);
        exit;
    }
    
    // Ensure available_rooms is never negative
    $available_rooms = max(0, (int)$result['available_rooms']);
    
    echo json_encode([
        'success' => true,
        'room_type_id' => $room_type_id,
        'room_type' => $result['room_type'],
        'available_rooms' => $available_rooms,
        'total_rooms' => (int)$result['total_rooms']
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while checking room availability',
        'error' => $e->getMessage()
    ]);
}

// Ensure no extra output
ob_end_flush();
?>
