<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get data from URL parameters (GET request)
$data = $_GET;

// Validate input data
if (!$data || !isset($data['totalAmount']) || !isset($data['tablePaymentMethod'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid payment data received'
    ]);
    exit;
}

try {
    // Extract booking details
    $amountToPay = floatval($data['totalAmount']);
    $fullBookingAmount = floatval($data['offerPrice']);
    $paymentMethod = $data['tablePaymentMethod'];
    $paymentOption = $data['tablePaymentOption'];
    $numberOfGuests = $data['tableGuests'];
    $specialRequests = $data['tableSpecialRequests'] ?? '';
    $offerTitle = $data['offerTitle'] ?? 'Table Promo';
    $offerPrice = $data['offerPrice'] ?? '';
    
    // Extract guest information (using defaults for now since not collected in form)
    $guestFirstname = 'Guest'; // Default since not collected
    $guestLastname = 'User';   // Default since not collected
    $guestEmail = 'guest@example.com'; // Default since not collected
    $guestPhone = '0000000000'; // Default since not collected

    // Validate amount
    if ($amountToPay <= 0) {
        throw new Exception('Invalid payment amount');
    }

    // Validate guest information
    if (empty($guestFirstname) || empty($guestLastname) || empty($guestEmail)) {
        throw new Exception('Guest information (first name, last name, email) is required');
    }

    // Store guest information in session for success page
    session_start();
    error_log('Payment Script Session ID: ' . session_id());
    error_log('Payment Script Session Data Before: ' . print_r($_SESSION, true));
    
    $_SESSION['table_promo_guest_firstname'] = $guestFirstname;
    $_SESSION['table_promo_guest_lastname'] = $guestLastname;
    $_SESSION['table_promo_guest_email'] = $guestEmail;
    $_SESSION['table_promo_guest_phone'] = $guestPhone;
    $_SESSION['table_promo_guests'] = $numberOfGuests;
    $_SESSION['table_promo_special_requests'] = $specialRequests;
    $_SESSION['table_promo_payment_option'] = $paymentOption;
    $_SESSION['table_promo_amount'] = $amountToPay;
    $_SESSION['table_promo_total_amount'] = $fullBookingAmount;
    $_SESSION['table_promo_offer_title'] = $offerTitle;
    $_SESSION['table_promo_offer_price'] = $offerPrice;
    
    error_log('Payment Script Session Data After: ' . print_r($_SESSION, true));

    // Xendit API Configuration
    $xenditSecretKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n';
    
    // Create a unique invoice ID
    $invoiceId = 'TABLE_' . uniqid() . '_' . time();
    
    // Prepare the invoice data for Xendit
    $invoiceData = [
        'external_id' => $invoiceId,
        'amount' => $amountToPay,
        'description' => "Table Promo - {$offerTitle} ({$paymentOption})",
        'invoice_duration' => 86400, // 24 hours expiry
        'customer' => [
            'given_names' => $guestFirstname,
            'surname' => $guestLastname,
            'email' => $guestEmail
        ],
        'customer_notification_preference' => [
            'invoice_created' => ['email'],
            'invoice_reminder' => ['email'],
            'invoice_paid' => ['email']
        ],
        'success_redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/table_promo_success_payment.php?invoice_id=' . $invoiceId,
        'failure_redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/table_promo_failed_payment.php?invoice_id=' . $invoiceId,
        'currency' => 'PHP',
        'items' => [
            [
                'name' => $offerTitle,
                'quantity' => 1,
                'price' => $amountToPay,
                'category' => 'Dining'
            ]
        ],
        'fees' => [
            [
                'type' => 'ADMIN',
                'value' => 0
            ]
        ]
    ];

    // Add mobile number if provided
    if (!empty($guestPhone)) {
        $invoiceData['customer']['mobile_number'] = $guestPhone;
    }

    // Add metadata for tracking
    $invoiceData['metadata'] = [
        'booking_details' => json_encode([
            'number_of_guests' => $numberOfGuests,
            'special_requests' => $specialRequests,
            'payment_option' => $paymentOption,
            'payment_method' => $paymentMethod,
            'full_booking_amount' => $fullBookingAmount,
            'offer_price' => $offerPrice,
            'guest_firstname' => $guestFirstname,
            'guest_lastname' => $guestLastname,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone
        ])
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, 'https://api.xendit.co/v2/invoices');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoiceData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($xenditSecretKey . ':')
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    // Check HTTP response code
    if ($httpCode !== 200) {
        $responseData = json_decode($response, true);
        $errorMessage = $responseData['message'] ?? 'Xendit API error';
        throw new Exception("Xendit API Error (HTTP {$httpCode}): {$errorMessage}");
    }

    // Parse the response
    $responseData = json_decode($response, true);

    if (!isset($responseData['invoice_url'])) {
        throw new Exception('Invalid response from Xendit API');
    }

    // Redirect directly to Xendit payment URL
    header('Location: ' . $responseData['invoice_url']);
    exit;

} catch (Exception $e) {
    // Log the error for debugging
    error_log('Table Promo Xendit Payment Error: ' . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
