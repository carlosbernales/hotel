<?php
session_start();
require_once 'includes/VerificationManager.php';
require_once 'includes/SMS.php';
require_once 'db_con.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $phone = $data['phone'] ?? '';
    $formData = $data['formData'] ?? null;
    
    if (empty($phone)) {
        throw new Exception('Phone number is required');
    }

    // Initialize verification manager
    $verificationManager = new VerificationManager($con);
    
    // Generate new verification code
    $code = $verificationManager->generateCode();
    
    // Store verification code in database
    if (!$verificationManager->createVerification($phone, $code, 'phone')) {
        throw new Exception('Failed to create verification code');
    }

    // Store verification data in session
    $_SESSION['phone_verification'] = [
        'phone' => $phone,
        'code' => $code,
        'expires' => time() + (15 * 60), // 15 minutes
        'attempts' => 0
    ];

    // If form data is provided, store it in session
    if ($formData) {
        $_SESSION['pending_signup_data'] = $formData;
    }

    // Send SMS with verification code
    $message = "Your E-Akomoda verification code is: $code. Valid for 15 minutes.";
    $smsResult = SMS::send($phone, $message);

    if (!$smsResult['success']) {
        throw new Exception($smsResult['message']);
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Verification code sent successfully'
    ]);

} catch (Exception $e) {
    error_log("Phone verification error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 