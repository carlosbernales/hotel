<?php
session_start();
require 'db_con.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'count' => 0,
    'totalAmount' => 0
];

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

try {
    // Check if room_type_id was provided
    if (!isset($_POST['room_type_id'])) {
        throw new Exception('Room ID is required');
    }

    $room_type_id = $_POST['room_type_id'];

    // Initialize booking list if it doesn't exist
    if (!isset($_SESSION['booking_list'])) {
        $_SESSION['booking_list'] = [];
    }

    // Remove the room from the booking list
    $_SESSION['booking_list'] = array_filter($_SESSION['booking_list'], function($item) use ($room_type_id) {
        return $item['room_type_id'] != $room_type_id;
    });

    // Get the new count
    $count = count($_SESSION['booking_list']);

    echo json_encode([
        'success' => true,
        'message' => 'Room removed successfully',
        'count' => $count
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 