<?php
class SMS {
    private static $email = 'allysonmildred696@gmail.com'; // Your iTextMo email
    private static $password = 'password123'; // Your iTextMo password
    private static $apiCode = 'TR-ALLYA285676_DKF9E'; // Your iTextMo API code

    public static function send($number, $message) {
        try {
            $ch = curl_init();
            $itexmo = array(
                'Email' => self::$email,
                'Password' => self::$password,
                'ApiCode' => self::$apiCode,
                'Recipients' => array($number), // Changed to array format
                'Message' => $message
            );

            // Convert Recipients to JSON string
            $itexmo['Recipients'] = json_encode($itexmo['Recipients']);

            // Debug log
            error_log("Sending SMS with data: " . print_r($itexmo, true));

            curl_setopt($ch, CURLOPT_URL, "https://api.itexmo.com/api/broadcast");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($itexmo));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ));

            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Debug logs
            error_log("SMS API Response: " . $response);
            error_log("HTTP Code: " . $http_code);
            if ($curl_error) {
                error_log("CURL Error: " . $curl_error);
            }

            curl_close($ch);

            // Parse the response
            $result = json_decode($response, true);
            error_log("Decoded response: " . print_r($result, true));
            
            if ($result === null) {
                error_log("Failed to decode JSON response: " . $response);
                throw new Exception("Invalid response from server: " . $response);
            }

            // Check for specific error conditions
            if (isset($result['error'])) {
                error_log("API Error: " . print_r($result['error'], true));
                throw new Exception($result['error']['message'] ?? 'API Error occurred');
            }

            return [
                'success' => isset($result['status']) && $result['status'] === 'success',
                'message' => $result['message'] ?? 'Message sent successfully'
            ];

        } catch (Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public static function generateCode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private static function getResponseMessage($code) {
        $messages = array(
            "0" => "Message sent successfully",
            "1" => "Invalid number",
            "2" => "Invalid API code",
            "3" => "Invalid API password",
            "4" => "Invalid message",
            "5" => "Invalid sender name",
            "6" => "Message expired",
            "7" => "Invalid SMS type",
            "8" => "Maximum message per day reached",
            "9" => "API disabled",
            "10" => "Maximum message per month reached",
            "11" => "Insufficient credit",
            "12" => "Invalid network"
        );

        return $messages[$code] ?? "Unknown error (Code: $code)";
    }
} 