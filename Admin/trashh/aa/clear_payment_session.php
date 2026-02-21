<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_payment'])) {
    // Clear the payment success data from the session
    unset($_SESSION['payment_success']);
    
    // Send a success response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit();
}

// If not a valid request, return an error
header('HTTP/1.1 400 Bad Request');
echo json_encode(['error' => 'Invalid request']);
