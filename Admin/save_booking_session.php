<?php
session_start();
require 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_booking') {
        // Remove the action from the POST data
        unset($_POST['action']);
        
        // Sanitize all input data
        $bookingData = array_map('trim', $_POST);
        $bookingData = array_map('htmlspecialchars', $bookingData);
        
        // Get URL parameters if they exist
        $urlParams = [
            'packageName' => $_GET['packageName'] ?? null,
            'packagePrice' => $_GET['packagePrice'] ?? null,
            'basePrice' => $_GET['basePrice'] ?? null,
            'overtimeHours' => $_GET['overtimeHours'] ?? null,
            'overtimeCharge' => $_GET['overtimeCharge'] ?? null,
            'totalAmount' => $_GET['totalAmount'] ?? null,
            'downpaymentAmount' => $_GET['downpaymentAmount'] ?? null,
            'remainingBalance' => $_GET['remainingBalance'] ?? null
        ];
        
        // Filter out null values
        $urlParams = array_filter($urlParams, function($value) {
            return $value !== null;
        });
        
        // Merge URL parameters with booking data
        $bookingData = array_merge($bookingData, $urlParams);
        
        // Store in session
        $_SESSION['booking_data'] = $bookingData;
        
        // Also store in a cookie that expires in 1 hour (as a fallback)
        setcookie('booking_data', json_encode($bookingData), time() + 3600, '/');
        
        echo json_encode(['status' => 'success', 'data' => $bookingData]);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
