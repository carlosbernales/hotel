<?php
session_start();
require 'db_con.php';

// Initialize response array
$response = [
    'success' => false,
    'count' => 0,
    'message' => ''
];

try {
    // Get user ID from session
    $user_id = $_SESSION['user_id'] ?? null;
    
    // Count items in booking list
    if ($user_id) {
        // For logged in users, get count from database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM booking_list WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $count = $stmt->fetchColumn();
    } else {
        // For guests, get count from session
        $count = count($_SESSION['booking_list'] ?? []);
    }
    
    // Update response
    $response['success'] = true;
    $response['count'] = $count;
    $response['message'] = 'Count retrieved successfully';
    
} catch (Exception $e) {
    $response['message'] = 'Error retrieving booking count: ' . $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit; 