<?php
// Start output buffering to catch any unexpected output
ob_start();

header('Content-Type: application/json');
require 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, log them instead

// Get the request body
$input = file_get_contents('php://input');
error_log("PayMongo Request received: " . $input);

$inputData = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    ob_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data received']);
    exit;
}

// Validate input
if (!isset($inputData['amount']) || !is_numeric($inputData['amount']) || $inputData['amount'] <= 0) {
    error_log("Invalid amount: " . ($inputData['amount'] ?? 'not set'));
    ob_clean();
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount']);
    exit;
}

// Set your PayMongo API keys
$secretKey = 'sk_test_MFRCBoj9hzSZbeBWrSDSRTEF'; // Replace with your actual secret key
$publicKey = 'pk_test_xZijiyCoEn7YiX4jBJnQo2xw'; // Replace with your actual public key

// Prepare the request data
$checkoutData = [
    'data' => [
        'attributes' => [
            'send_email_receipt' => true,
            'show_description' => true,
            'show_line_items' => true,
            'success_url' => $inputData['success_url'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/room_booking_summary.php?payment=success' . 
                (isset($inputData['metadata']) ? '&' . http_build_query($inputData['metadata']) : ''),
            'cancel_url' => $inputData['cancel_url'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/room_booking_summary.php?payment=cancelled',
            'description' => $inputData['description'] ?? 'Hotel Booking Payment',
            'reference_number' => $inputData['reference_number'] ?? 'BOOK-' . uniqid(),
            'line_items' => [
                [
                    'currency' => 'PHP',
                    'amount' => (int)$inputData['amount'],
                    'name' => $inputData['description'] ?? 'Hotel Booking',
                    'quantity' => 1
                ]
            ],
            'payment_method_types' => ['gcash', 'card', 'grab_pay'],
            'metadata' => $inputData['metadata'] ?? []
        ]
    ]
];

error_log("PayMongo checkout data: " . json_encode($checkoutData));

// Initialize cURL
$ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($checkoutData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($secretKey . ':')
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

error_log("PayMongo API Response - HTTP Code: " . $httpCode . ", Response: " . $response);

// Check for cURL errors
if ($error) {
    error_log("cURL Error: " . $error);
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit;
}

// Check for empty response
if (empty($response)) {
    error_log("Empty response from PayMongo API");
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Empty response from payment provider']);
    exit;
}

// Decode the response
$responseData = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error in response: " . json_last_error_msg() . ", Response: " . $response);
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Invalid response from payment provider', 'raw_response' => $response]);
    exit;
}

// Check for API errors
if ($httpCode >= 400) {
    $errorMessage = 'Failed to create checkout session';
    if (isset($responseData['errors'][0]['detail'])) {
        $errorMessage = $responseData['errors'][0]['detail'];
    }
    error_log("PayMongo API Error: " . $errorMessage);
    ob_clean();
    http_response_code($httpCode);
    echo json_encode([
        'error' => $errorMessage,
        'response' => $responseData
    ]);
    exit;
}

// Return the checkout URL
$checkoutUrl = $responseData['data']['attributes']['checkout_url'] ?? null;

if (!$checkoutUrl) {
    error_log("No checkout URL in PayMongo response");
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'No checkout URL in response', 'response' => $responseData]);
    exit;
}

// Log the transaction (you should implement your own logging)
// logTransaction($inputData, $responseData);

ob_clean();
echo json_encode([
    'checkout_url' => $checkoutUrl,
    'reference_number' => $responseData['data']['attributes']['reference_number']
]);
?>
