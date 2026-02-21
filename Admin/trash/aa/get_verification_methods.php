<?php
require_once 'includes/VerificationMethod.php';

header('Content-Type: application/json');

try {
    $verificationMethod = new VerificationMethod();
    $methods = $verificationMethod->getAllMethods();
    
    if (empty($methods)) {
        // If no methods found, check if table exists
        throw new Exception("No verification methods available");
    }
    
    echo json_encode([
        'success' => true,
        'methods' => $methods
    ]);
} catch (Exception $e) {
    error_log("Verification Methods Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading verification methods. Please try again later.',
        'debug_message' => $e->getMessage() // Only for development
    ]);
} 