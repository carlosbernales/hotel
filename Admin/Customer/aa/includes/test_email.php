<?php
require_once 'Mailer.php';

try {
    $mailer = new Mailer();
    $result = $mailer->sendVerificationCode('christianrealisan45@gmail.com', '123456');
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    error_log("Test Email Error: " . $e->getMessage());
} 