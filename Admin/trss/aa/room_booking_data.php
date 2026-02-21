<?php
session_start();
require 'db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $booking_data = array(
        'room_type' => $_POST['room_type'] ?? '',
        'room_type_id' => $_POST['room_type_id'] ?? '',
        'check_in' => $_POST['check_in'] ?? '',
        'check_out' => $_POST['check_out'] ?? '',
        'total_guests' => $_POST['total_guests'] ?? 0,
        'nights' => $_POST['nights'] ?? 1,
        'num_adults' => $_POST['num_adults'] ?? 0,
        'num_children' => $_POST['num_children'] ?? 0,
        'room_rate' => $_POST['room_rate'] ?? 0,
        'room_quantity' => $_POST['room_quantity'] ?? 1,
        'total_amount' => $_POST['total_amount'] ?? 0,
        // Add any other relevant booking data
    );

    // Store in session
    $_SESSION['booking_data'] = $booking_data;

    // Return success response
    echo json_encode(['success' => true]);
    exit;
} else {
    // Return error for invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
} 