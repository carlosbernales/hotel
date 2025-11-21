<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// PayMongo API Configuration
define('PAYMONGO_SECRET_KEY', 'sk_test_MFRCBoj9hzSZbeBWrSDSRTEF');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['amount']) || empty($input['description'])) {
        throw new Exception('Missing required parameters');
    }
    
    // Prepare the request data
    $checkoutData = [
        'data' => [
            'attributes' => [
                'send_email_receipt' => true,
                'show_description' => true,
                'show_line_items' => true,
                'success_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]/payment_success.php?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]" . $_SERVER['REQUEST_URI'],
                'description' => $input['description'],
                'reference_number' => 'TEST-' . uniqid(),
                'line_items' => [
                    [
                        'currency' => 'PHP',
                        'amount' => (int)$input['amount'],
                        'name' => $input['description'],
                        'quantity' => 1
                    ]
                ],
                'payment_method_types' => ['gcash', 'card', 'grab_pay']
            ]
        ]
    ];

    // Initialize cURL
    $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');

    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($checkoutData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
        ]
    ]);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    $result = json_decode($response, true);

    if ($httpCode >= 400) {
        $errorMsg = $result['errors'][0]['detail'] ?? 'Unknown error occurred';
        throw new Exception('PayMongo API Error: ' . $errorMsg);
    }

    // Return the checkout URL
    echo json_encode([
        'success' => true,
        'checkout_url' => $result['data']['attributes']['checkout_url']
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
