<?php
require_once 'User.php';
require_once 'Session.php';

header('Content-Type: application/json');

try {
    Session::start();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['code']) || !isset($data['email'])) {
        throw new Exception('Verification code and email are required');
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }

    $code = trim($data['code']);
    if (!preg_match('/^\d{6}$/', $code)) {
        throw new Exception('Invalid verification code format');
    }

    // Initialize User class and verify the code
    $user = new User();
    if ($user->verifyCode($email, $code)) {
        // Clear any verification-related session data
        Session::unset('verification_code');
        Session::unset('verification_email');
        Session::unset('verification_expiry');
        Session::unset('pending_signup_data');
        
        // Set success message for the login page
        Session::setFlash('success', 'Email verified successfully! You can now log in.');
        
        echo json_encode([
            'success' => true,
            'message' => 'Email verified successfully!',
            'redirect' => 'login.php'
        ]);
    } else {
        throw new Exception('Failed to verify email');
    }
} catch (Exception $e) {
    error_log("Email verification error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 