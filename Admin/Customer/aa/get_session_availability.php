<?php
require_once 'db_con.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Check if room availability data exists in session
    if (isset($_SESSION['room_availability'])) {
        $roomAvailability = $_SESSION['room_availability'];
        
        echo json_encode([
            'success' => true,
            'checkin' => $roomAvailability['checkin'] ?? '',
            'checkout' => $roomAvailability['checkout'] ?? '',
            'rooms' => $roomAvailability['rooms'] ?? []
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No availability data found in session'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
