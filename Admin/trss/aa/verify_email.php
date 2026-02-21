<?php
require_once 'includes/User.php';
require_once 'includes/Session.php';

header('Content-Type: application/json');

try {
    // Initialize response array
    $response = [
        'success' => false,
        'message' => '',
        'redirect' => null
    ];

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($data['email']) || !isset($data['code'])) {
        throw new Exception('Email and verification code are required');
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $code = trim($data['code']);

    // Validate email format
    if (!$email) {
        throw new Exception('Invalid email format');
    }

    // Validate verification code format (6 digits)
    if (!preg_match('/^\d{6}$/', $code)) {
        throw new Exception('Invalid verification code format');
    }

    // Initialize User class
    $user = new User();

    // Attempt to verify the code
    if ($user->verifyCode($email, $code)) {
        $response['success'] = true;
        $response['message'] = 'Email verified successfully!';
        $response['redirect'] = 'login.php';
        
        // Set success flash message for the login page
        Session::setFlash('success', 'Email verified successfully! You can now log in.');
    } else {
        throw new Exception('Failed to verify email');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Send JSON response
echo json_encode($response); 