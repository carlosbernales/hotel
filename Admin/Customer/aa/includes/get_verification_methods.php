<?php
require_once 'Session.php';

header('Content-Type: application/json');

try {
    Session::start();
    
    // Get the form data from session if it exists
    $pendingUserData = Session::get('pending_signup_data');

    // Define available verification methods
    $methods = [
        [
            'method_name' => 'email',
            'display_name' => 'Email Verification',
            'is_active' => true,
            'icon' => 'envelope'
        ],
        [
            'method_name' => 'phone',
            'display_name' => 'Phone Verification',
            'is_active' => true,
            'icon' => 'phone'
        ]
    ];

    echo json_encode([
        'success' => true,
        'methods' => $methods,
        'userData' => $pendingUserData
    ]);

} catch (Exception $e) {
    error_log("Get Verification Methods Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 