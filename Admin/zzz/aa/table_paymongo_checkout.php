<?php
header('Content-Type: application/json');
require 'db_con.php';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

// ================================
// VALIDATION
// ================================
if (!isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount']);
    exit;
}

// ================================
// AMOUNT CONVERSION
// Frontend sends centavos (e.g. 150000)
// Xendit needs pesos (1500)
// ================================
$amountInPesos = round(((int)$input['amount']) / 100, 2);

// ================================
// XENDIT CONFIG
// ================================
$xenditSecretKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n';

$referenceNumber = $input['reference_number'] ?? 'BOOK-' . uniqid();

// URLs
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://")
    . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

$successUrl = $input['success_url']
    ?? $baseUrl . '/table_payment_process.php?payment=success&ref=' . urlencode($referenceNumber);

$failureUrl = $input['cancel_url']
    ?? $baseUrl . '/table.php?payment=cancelled';

// ================================
// XENDIT INVOICE PAYLOAD
// ================================
$invoiceData = [
    'external_id' => $referenceNumber,
    'amount' => $amountInPesos,
    'currency' => 'PHP',
    'description' => $input['description'] ?? 'Table Booking Payment',

    'success_redirect_url' => $successUrl,
    'failure_redirect_url' => $failureUrl,

    'metadata' => array_merge(
        $input['metadata'] ?? [],
        [
            'type' => 'table_booking',
            'timestamp' => date('Y-m-d H:i:s')
        ]
    )
];

// ================================
// SEND TO XENDIT
// ================================
$ch = curl_init('https://api.xendit.co/v2/invoices');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($invoiceData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($xenditSecretKey . ':')
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// ================================
// ERROR HANDLING
// ================================
if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error', 'details' => $error]);
    exit;
}

$responseData = json_decode($response, true);

if ($httpCode >= 400) {
    http_response_code($httpCode);
    echo json_encode([
        'error' => $responseData['message'] ?? 'Xendit API Error',
        'response' => $responseData
    ]);
    exit;
}

// ================================
// SUCCESS RESPONSE
// ================================
if (!isset($responseData['invoice_url'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'No checkout URL returned',
        'response' => $responseData
    ]);
    exit;
}

echo json_encode([
    'checkout_url' => $responseData['invoice_url'],
    'reference_number' => $referenceNumber,
    'amount' => $amountInPesos
]);
