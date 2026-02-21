<?php
session_start();

// Clear the booking session data
if (isset($_SESSION['booking_data'])) {
    unset($_SESSION['booking_data']);
}

// Clear completed booking data
if (isset($_SESSION['completed_booking'])) {
    unset($_SESSION['completed_booking']);
}

// Send success response
header('Content-Type: application/json');
echo json_encode(['success' => true]);
