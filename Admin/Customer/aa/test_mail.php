<?php
require_once 'includes/Mailer.php';

try {
    $mailer = new Mailer();
    $testEmail = 'christianrealisan3@gmail.com'; // Your email
    $testCode = '123456';
    
    echo "Attempting to send test email...<br>";
    error_log("Starting test email send...");
    
    $result = $mailer->sendVerificationCode($testEmail, $testCode);
    
    if ($result) {
        echo "Test email sent successfully!<br>";
        error_log("Test email sent successfully!");
    } else {
        echo "Failed to send test email.<br>";
        error_log("Failed to send test email");
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    error_log("Test email error: " . $e->getMessage());
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
} 