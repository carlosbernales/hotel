    <?php
    // Start output buffering
    ob_start();

    header('Content-Type: application/json');
    require 'db_con.php';

    // Error handling
    error_reporting(E_ALL);
    ini_set('display_errors', 0);

    // Read JSON body
    $input = file_get_contents('php://input');
    error_log("Xendit Request received: " . $input);

    $inputData = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }

    // Validate amount (expects centavos from frontend)
    if (!isset($inputData['amount']) || !is_numeric($inputData['amount']) || $inputData['amount'] <= 0) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid amount']);
        exit;
    }

    // ================================
    // AMOUNT CONVERSION (CRITICAL FIX)
    // ================================
    // Frontend sends: 150000 (centavos)
    // Xendit needs:    1500 (pesos)
    $amountInPesos = round(((int)$inputData['amount']) / 100, 2);

    error_log("Converted amount for Xendit: {$amountInPesos} PHP");

    // ================================
    // XENDIT CONFIG
    // ================================
    $xenditSecretKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n';

    // Generate reference
    $referenceNumber = $inputData['reference_number'] ?? 'BOOK-' . uniqid();

    // URLs
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://")
        . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

    $successUrl = $inputData['success_url']
        ?? $baseUrl . '/room_booking_summary.php?payment=success';

    $failureUrl = $inputData['cancel_url']
        ?? $baseUrl . '/room_booking_summary.php?payment=cancelled';

    // ================================
    // XENDIT INVOICE PAYLOAD
    // ================================
    $invoiceData = [
        'external_id' => $referenceNumber,
        'amount' => $amountInPesos, // ✅ FIXED
        'currency' => 'PHP',
        'description' => $inputData['description'] ?? 'Hotel Booking Payment',

        'success_redirect_url' => $successUrl,
        'failure_redirect_url' => $failureUrl,

        'metadata' => $inputData['metadata'] ?? [],

        'customer' => [
            'given_names' => $inputData['customer_name'] ?? 'Guest'
        ]
    ];

    error_log("Xendit Invoice Payload: " . json_encode($invoiceData));

    // ================================
    // CURL REQUEST TO XENDIT
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

    error_log("Xendit API Response [$httpCode]: $response");

    // ================================
    // ERROR HANDLING
    // ================================
    if ($error) {
        ob_clean();
        http_response_code(500);
        echo json_encode(['error' => 'cURL Error', 'details' => $error]);
        exit;
    }

    $responseData = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        ob_clean();
        http_response_code(500);
        echo json_encode(['error' => 'Invalid Xendit response']);
        exit;
    }

    if ($httpCode >= 400) {
        ob_clean();
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
        ob_clean();
        http_response_code(500);
        echo json_encode(['error' => 'No invoice URL returned']);
        exit;
    }

    ob_clean();
    echo json_encode([
        'checkout_url' => $responseData['invoice_url'],
        'reference_number' => $referenceNumber,
        'amount' => $amountInPesos
    ]);
