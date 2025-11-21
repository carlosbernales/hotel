<?php
require_once 'SMS.php';

class SMSHandler {
    private $apiCode;
    private $apiPassword;
    // Using the alternative API endpoint
    private $apiUrl = 'https://api.itexmo.com/api/broadcast';

    public function __construct($apiCode = null, $apiPassword = null) {
        $this->apiCode = $apiCode ?? 'TR-AKOPO897243_3K1QN';
        $this->apiPassword = $apiPassword ?? 'A12345678';
    }

    public function sendVerificationCode($phone, $code) {
        if (empty($phone) || empty($code)) {
            error_log("SMS Error: Empty phone or code");
            return ['success' => false, 'message' => 'Phone number and code are required'];
        }

        // Format phone number (remove any non-numeric characters)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present (Philippines)
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '09') {
            $phone = '63' . substr($phone, 1);
        }
        
        error_log("Formatted phone number for SMS: " . $phone);
        
        // Validate phone number format
        if (!preg_match('/^(63|0)9\d{9}$/', $phone)) {
            error_log("SMS Error: Invalid phone format: " . $phone);
            return ['success' => false, 'message' => 'Invalid phone number format'];
        }

        $message = "Your E-Akomoda verification code is: $code. Valid for 15 minutes.";
        error_log("Sending verification message: " . $message);
        
        try {
            // Use the SMS class to send the message
            $result = SMS::send($phone, $message);
            error_log("SMS send result: " . print_r($result, true));
            return $result;
        } catch (Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS. Please try again later.'
            ];
        }
    }

    private function sendSMS($phone, $message) {
        error_log("Attempting to send SMS to: " . $phone);
        error_log("Message content: " . $message);

        try {
            // Use the SMS class to send the message
            return SMS::send($phone, $message);
        } catch (Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS. Please try again later.'
            ];
        }
    }
} 