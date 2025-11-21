<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_booking') {
    // Clear all booking-related session data
    unset($_SESSION['booking_data']);
    unset($_SESSION['booking_list']);
    
    // Also clear any related cookies
    if (isset($_COOKIE['booking_data'])) {
        setcookie('booking_data', '', time() - 3600, '/');
    }
    
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
