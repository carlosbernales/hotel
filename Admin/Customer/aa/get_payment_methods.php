<?php
session_start();
require 'db_con.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'methods' => []
];

try {
    // Log the connection variables to debug
    error_log('PDO connection status: ' . ($pdo ? 'Connected' : 'Not connected'));
    
    // Only get active payment methods
    $sql = "SELECT id, name, display_name, qr_code_image, account_name, account_number, is_active 
            FROM payment_methods 
            ORDER BY display_name";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Fetch all payment methods
    $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fix the QR code image paths to make them accessible from the browser
    foreach ($methods as &$method) {
        if (!empty($method['qr_code_image'])) {
            // Convert database path to a web-accessible path
            // Assuming images are stored in the uploads/payment_qr_codes directory
            $filename = basename($method['qr_code_image']);
            // Path relative to the current directory that will work in the browser
            $method['qr_code_image'] = '../../../Admin/' . $method['qr_code_image'];
        }
    }
    
    // Update response - using 'payment_methods' to match frontend expectation
    $response['success'] = true;
    $response['payment_methods'] = $methods;
    $response['count'] = count($methods);
    
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    error_log('Error fetching payment methods: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;
?>