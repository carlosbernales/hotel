<?php
header('Content-Type: application/json');
require 'db_con.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate amount
if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount']);
    exit;
}

// Validate description
if (!isset($input['description']) || empty($input['description'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Description is required']);
    exit;
}

/**
 * ==============================
 * XENDIT CONFIG
 * ==============================
 */
$xenditSecretKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n'; // 🔴 replace
$xenditEndpoint  = 'https://api.xendit.co/v2/invoices';

/**
 * ==============================
 * PREPARE DATA (same behavior)
 * ==============================
 */

// Reference number
$referenceNumber = $input['reference_number'] ?? 'BOOK-' . uniqid();

// Convert cents → pesos (PayMongo → Xendit difference)
$amount = (float) $input['amount'];

// Base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://")
    . $_SERVER['HTTP_HOST']
    . dirname($_SERVER['PHP_SELF']);

// Success & Cancel URLs (unchanged logic)
$successUrl = $input['success_url'] ??
    $baseUrl . '/event_payment_success.php?payment=success'
    . '&booking_id=' . urlencode($input['metadata']['booking_id'] ?? '')
    . '&package_name=' . urlencode($input['description'] ?? '')
    . '&amount=' . $amount
    . '&reference=' . $referenceNumber;

$cancelUrl = $input['cancel_url'] ??
    $baseUrl . '/event_payment_process.php?payment=cancelled';

/**
 * ==============================
 * XENDIT INVOICE PAYLOAD
 * ==============================
 */
$checkoutData = [
    'external_id' => $referenceNumber,
    'amount' => $amount, // Amount should be in centavos already
    'description' => $input['description'] ?? 'Event Booking Payment',
    'invoice_duration' => 86400, // 24 hours
    'currency' => 'PHP',
    'success_redirect_url' => $successUrl,
    'failure_redirect_url' => $cancelUrl,
    'customer' => [
        'given_names' => 'Guest',
        'email' => 'guest@example.com',
        'mobile_number' => '+639000000000'
    ],
    'metadata' => array_merge(
        $input['metadata'] ?? [],
        [
            'type' => 'event_booking',
            'timestamp' => date('Y-m-d H:i:s'),
            'booking_reference' => $referenceNumber
        ]
    )
];

// Optional webhook (production only)
if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
    $checkoutData['callback_url'] =
        $baseUrl . '/event_payment_success.php';
}

/**
 * ==============================
 * CURL REQUEST
 * ==============================
 */
$ch = curl_init($xenditEndpoint);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($checkoutData),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($xenditSecretKey . ':')
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);

curl_close($ch);

// cURL error
if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error', 'message' => $error]);
    exit;
}

$responseData = json_decode($response, true);

// Log the request and response for debugging
error_log('Xendit Request: ' . print_r($checkoutData, true));
error_log('Xendit Response Code: ' . $httpCode);
error_log('Xendit Response: ' . $response);

// API error
if ($httpCode >= 400) {
    http_response_code($httpCode);
    echo json_encode([
        'error' => 'Failed to create Xendit checkout session',
        'response' => $responseData,
        'debug_info' => [
            'http_code' => $httpCode,
            'request_data' => $checkoutData
        ]
    ]);
    exit;
}

// Invoice URL (Xendit returns invoice_url)
$checkoutUrl = $responseData['invoice_url'] ?? null;

if (!$checkoutUrl) {
    http_response_code(500);
    echo json_encode([
        'error' => 'No invoice URL in response',
        'response' => $responseData
    ]);
    exit;
}

/**
 * ==============================
 * FINAL RESPONSE (same shape)
 * ==============================
 */
echo json_encode([
    'checkout_url'     => $checkoutUrl,
    'reference_number' => $referenceNumber
]);
