<?php
require_once 'includes/Mailer.php';
require_once 'includes/Session.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($postData['email']) || !isset($postData['formData'])) {
        throw new Exception('Missing required data');
    }

    $email = $postData['email'];
    $formData = $postData['formData'];

    // Generate verification code
    $verification_code = sprintf("%06d", mt_rand(0, 999999));
    
    // Store data in session
    session_start();
    $_SESSION['verification'] = [
        'email_code' => $verification_code,
        'email' => $email,
        'expires' => time() + (2 * 60), // 2 minutes expiry
        'formData' => $formData
    ];

    // Send verification email
    $mailer = new Mailer();
    $mailer->sendVerificationCode($email, $verification_code);

    echo json_encode(['success' => true, 'message' => 'Verification code sent successfully']);

} catch (Exception $e) {
    error_log("Email verification error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 