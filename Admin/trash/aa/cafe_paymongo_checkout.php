<?php
header('Content-Type: application/json');
require 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get request data from either POST or GET
function getRequestData() {
    // Try to get JSON from request body first
    $jsonData = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() === JSON_ERROR_NONE && $jsonData) {
        return $jsonData;
    }
    
    // Fall back to GET parameters
    if (!empty($_GET)) {
        return $_GET;
    }
    
    // If no data found, return empty array
    return [];
}

// Get the request data
$input = getRequestData();

// For backward compatibility, check for order_data in GET parameters
if (isset($_GET['order_data'])) {
    $orderData = json_decode(urldecode($_GET['order_data']), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $input = array_merge($input, $orderData);
    }
}

// Calculate total amount from order data if not provided directly
if (!isset($input['amount']) && isset($input['total'])) {
    $input['amount'] = floatval($input['total']) * 100; // Convert to centavos
} elseif (!isset($input['amount']) && isset($input['items']) && is_array($input['items'])) {
    $totalAmount = 0;
    foreach ($input['items'] as $item) {
        $itemPrice = floatval($item['price'] ?? 0);
        $quantity = intval($item['quantity'] ?? 1);
        $totalAmount += $itemPrice * $quantity;
        
        // Add addons price if any
        if (!empty($item['addons']) && is_array($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $totalAmount += floatval($addon['price'] ?? 0) * $quantity;
            }
        }
    }
    $input['amount'] = $totalAmount * 100; // Convert to centavos
}

// Validate input
if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount', 'input' => $input]);
    exit;
}

// Set default values
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$input['reference_number'] = $input['reference_number'] ?? 'ORD-' . uniqid();
$input['success_url'] = $baseUrl . '/cafe_payment_information.php?status=success&ref=' . urlencode($input['reference_number']);
$input['cancel_url'] = $input['cancel_url'] ?? $baseUrl . '/cafe_payment_information.php?status=cancelled';
$input['description'] = $input['description'] ?? 'Cafe Order #' . ($input['reference_number'] ?? uniqid());
$input['reference_number'] = $input['reference_number'] ?? 'ORD-' . uniqid();

// Set your PayMongo API keys
$secretKey = 'sk_test_MFRCBoj9hzSZbeBWrSDSRTEF';
$publicKey = 'pk_test_xZijiyCoEn7YiX4jBJnQo2xw';

// Prepare the request data
$checkoutData = [
    'data' => [
        'attributes' => [
            'send_email_receipt' => true,
            'show_description' => true,
            'show_line_items' => true,
            'success_url' => $input['success_url'],
            'cancel_url' => $input['cancel_url'],
            'description' => $input['description'],
            'reference_number' => $input['reference_number'],
            'line_items' => [
                [
                    'currency' => 'PHP',
                    'amount' => (int)$input['amount'],
                    'name' => $input['description'],
                    'quantity' => 1
                ]
            ],
            'payment_method_types' => ['gcash', 'card', 'grab_pay'],
            'metadata' => [
                'order_reference' => $input['reference_number'],
                'order_type' => 'cafe_order',
                'timestamp' => time(),
                'source' => 'cafe_ordering_system',
                'amount' => (int)$input['amount']
            ]
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

// If this is an AJAX request or API call, return JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || 
    !empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    echo json_encode([
        'checkout_url' => $checkoutUrl,
        'reference_number' => $responseData['data']['attributes']['reference_number']
    ]);
} else {
    // For direct browser requests, redirect to the checkout URL
    header('Location: ' . $checkoutUrl);
    exit;
}