<?php
require_once 'includes/Session.php';
require_once 'includes/User.php';
require_once 'includes/Mailer.php';

Session::start();

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';

    if (empty($email)) {
        throw new Exception('Email is required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Generate verification code
    $code = sprintf('%06d', mt_rand(0, 999999));
    
    // Store the code in the database
    $user = new User();
    if (!$user->storeVerificationCode($email, $code)) {
        throw new Exception('Failed to store verification code');
    }

    // Send verification email
    $mailer = new Mailer();
    
    // Add debug logging
    error_log("Attempting to send verification code to: " . $email);
    
    if (!$mailer->sendVerificationCode($email, $code)) {
        throw new Exception('Failed to send verification email');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Verification code sent successfully'
    ]);

} catch (Exception $e) {
    error_log("Verification Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
