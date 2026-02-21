<?php
header('Content-Type: application/json');
require 'db_con.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get request data
function getRequestData() {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() === JSON_ERROR_NONE && $jsonData) return $jsonData;
    if (!empty($_GET)) return $_GET;
    return [];
}

$input = getRequestData();

// Backward compatibility: order_data in GET
if (isset($_GET['order_data'])) {
    $orderData = json_decode(urldecode($_GET['order_data']), true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $input = array_merge($input, $orderData);
    }
}

// Calculate total amount in PHP
if (!isset($input['amount']) && isset($input['total'])) {
    $input['amount'] = floatval($input['total']); // Xendit uses full currency units, no cents
} elseif (!isset($input['amount']) && isset($input['items']) && is_array($input['items'])) {
    $totalAmount = 0;
    foreach ($input['items'] as $item) {
        $itemPrice = floatval($item['price'] ?? 0);
        $quantity = intval($item['quantity'] ?? 1);
        $totalAmount += $itemPrice * $quantity;

        if (!empty($item['addons']) && is_array($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $totalAmount += floatval($addon['price'] ?? 0) * $quantity;
            }
        }
    }
    $input['amount'] = $totalAmount;
}

// Validate amount
if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount', 'input' => $input]);
    exit;
}

// Set default values
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$referenceNumber = $input['reference_number'] ?? 'ORD-' . uniqid();
$successUrl = $baseUrl . '/cafe_payment_information.php?status=success&ref=' . urlencode($referenceNumber);
$cancelUrl = $input['cancel_url'] ?? $baseUrl . '/cafe_payment_information.php?status=cancelled';
$description = $input['description'] ?? 'Cafe Order #' . $referenceNumber;

// Xendit API Key
$xenditApiKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n';

// Prepare invoice data
$invoiceData = [
    'external_id' => $referenceNumber,
    'amount' => (float)$input['amount'], // Xendit expects full units
    'payer_email' => $input['email'] ?? 'customer@example.com',
    'description' => $description,
    'success_redirect_url' => $successUrl,
    'failure_redirect_url' => $cancelUrl,
    'currency' => 'PHP',
    'invoice_duration' => 86400, // 1 day in seconds
    'metadata' => [
        'order_type' => 'cafe_order',
        'source' => 'cafe_ordering_system',
        'items' => $input['items'] ?? []
    ]
];

// Initialize cURL
$ch = curl_init('https://api.xendit.co/v2/invoices');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoiceData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($xenditApiKey . ':')
]);

// Execute cURL
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Check for errors
if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit;
}

// Decode response
$responseData = json_decode($response, true);
if ($httpCode >= 400) {
    http_response_code($httpCode);
    echo json_encode(['error' => $responseData['message'] ?? 'Failed to create invoice', 'response' => $responseData]);
    exit;
}

// Get invoice URL
$invoiceUrl = $responseData['invoice_url'] ?? null;
if (!$invoiceUrl) {
    http_response_code(500);
    echo json_encode(['error' => 'No invoice URL in response', 'response' => $responseData]);
    exit;
}

// Return JSON for AJAX or redirect
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' || 
    !empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    echo json_encode([
        'invoice_url' => $invoiceUrl,
        'reference_number' => $referenceNumber
    ]);
} else {
    header('Location: ' . $invoiceUrl);
    exit;
}
