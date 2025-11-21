<?php
header('Content-Type: application/json');
require 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
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
            'success_url' => $input['success_url'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/room_payment.php?payment=success',
            'cancel_url' => $input['cancel_url'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/room_payment.php?payment=cancelled',
            'description' => $input['description'] ?? 'Hotel Booking Payment',
            'reference_number' => $input['reference_number'] ?? 'BOOK-' . uniqid(),
            'line_items' => [
                [
                    'currency' => 'PHP',
                    'amount' => (int)$input['amount'],
                    'name' => $input['description'] ?? 'Hotel Booking',
                    'quantity' => 1
                ]
            ],
            'payment_method_types' => ['gcash', 'card', 'grab_pay'],
            'metadata' => $input['metadata'] ?? []
        ]
    ]
];

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

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Check for cURL errors
if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit;
}

// Decode the response
$responseData = json_decode($response, true);

// Check for API errors
if ($httpCode >= 400) {
    http_response_code($httpCode);
    echo json_encode([
        'error' => $responseData['errors'][0]['detail'] ?? 'Failed to create checkout session',
        'response' => $responseData
    ]);
    exit;
}

// Return the checkout URL
$checkoutUrl = $responseData['data']['attributes']['checkout_url'] ?? null;

if (!$checkoutUrl) {
    http_response_code(500);
    echo json_encode(['error' => 'No checkout URL in response', 'response' => $responseData]);
    exit;
}

// Log the transaction (you should implement your own logging)
// logTransaction($input, $responseData);

echo json_encode([
    'checkout_url' => $checkoutUrl,
    'reference_number' => $responseData['data']['attributes']['reference_number']
]);
