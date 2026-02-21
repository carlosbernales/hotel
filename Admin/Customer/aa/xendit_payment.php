<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to store guest information
session_start();

// Get the posted data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input data
if (!$data || !isset($data['amountToPay']) || !isset($data['paymentMethod'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid payment data received'
    ]);
    exit;
}

try {
    // Extract booking details
    $amountToPay = floatval($data['amountToPay']);
    $fullBookingAmount = floatval($data['fullBookingAmount']);
    $paymentMethod = $data['paymentMethod'];
    $paymentOption = $data['paymentOption'];
    $checkInDate = $data['checkInDate'];
    $checkOutDate = $data['checkOutDate'];
    $numberOfGuests = $data['numberOfGuests'];
    $roomType = $data['roomType'];
    $specialRequests = $data['specialRequests'] ?? '';
    $guestFirstName = $data['guestFirstName'] ?? '';
    $guestLastName = $data['guestLastName'] ?? '';
    $guestEmail = $data['guestEmail'] ?? '';
    $guestPhone = $data['guestPhone'] ?? '';
    $offerTitle = $data['offerTitle'] ?? 'Room Booking';
    $offerPrice = $data['offerPrice'] ?? '';

    // Store guest information in session for success page
    $_SESSION['booking_guest_firstname'] = $guestFirstName;
    $_SESSION['booking_guest_lastname'] = $guestLastName;
    $_SESSION['booking_guest_email'] = $guestEmail;
    $_SESSION['booking_guest_phone'] = $guestPhone;
    $_SESSION['booking_checkin'] = $checkInDate;
    $_SESSION['booking_checkout'] = $checkOutDate;
    $_SESSION['booking_guests'] = $numberOfGuests;
    $_SESSION['booking_room_type'] = $roomType;
    $_SESSION['booking_special_requests'] = $specialRequests;
    $_SESSION['booking_payment_option'] = $paymentOption;
    $_SESSION['booking_amount'] = $amountToPay;
    $_SESSION['booking_total_amount'] = $fullBookingAmount;
    $_SESSION['booking_offer_title'] = $offerTitle;
    $_SESSION['booking_offer_price'] = $offerPrice;

    // Validate required fields
    if ($amountToPay <= 0) {
        throw new Exception('Invalid payment amount');
    }
    
    if (empty($guestFirstName) || empty($guestLastName) || empty($guestEmail)) {
        throw new Exception('Guest first name, last name, and email are required');
    }

    // Xendit API Configuration
    // IMPORTANT: Replace with your actual Xendit Secret Key
    // For production, use environment variables or secure config
    $xenditSecretKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n'; // Replace with your actual secret key
    
    // For development, you can get a free secret key from Xendit dashboard
    // Go to https://dashboard.xendit.co/settings/developers#api-keys
    
    // Create a unique invoice ID
    $invoiceId = 'ROOM_' . uniqid() . '_' . time();
    
    // Prepare the invoice data for Xendit
    $invoiceData = [
        'external_id' => $invoiceId,
        'amount' => $amountToPay,
        'description' => "Room Booking - {$offerTitle} ({$paymentOption})",
        'invoice_duration' => 86400, // 24 hours expiry
        'customer' => [
            'given_names' => trim($guestFirstName),
            'surname' => trim($guestLastName),
            'email' => trim($guestEmail)
        ],
    ];
    
    // Add mobile number only if provided
    if (!empty(trim($guestPhone))) {
        $invoiceData['customer']['mobile_number'] = trim($guestPhone);
    }
    
    // Add other required fields
    $invoiceData['customer_notification_preference'] = [
        'invoice_created' => ['email'],
        'invoice_reminder' => ['email'],
        'invoice_paid' => ['email']
    ];
    $invoiceData['success_redirect_url'] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/xendit_payment_success.php?invoice_id=' . $invoiceId;
    $invoiceData['failure_redirect_url'] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/xendit_payment_failed.php?invoice_id=' . $invoiceId;
    $invoiceData['currency'] = 'PHP';
    $invoiceData['items'] = [
        [
            'name' => $offerTitle,
            'quantity' => 1,
            'price' => $amountToPay,
            'category' => 'Accommodation'
        ]
    ];
    $invoiceData['fees'] = [
        [
            'type' => 'ADMIN',
            'value' => 0
        ]
    ];

    // Add metadata for tracking
    $invoiceData['metadata'] = [
        'booking_details' => json_encode([
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'number_of_guests' => $numberOfGuests,
            'room_type' => $roomType,
            'special_requests' => $specialRequests,
            'guest_first_name' => $guestFirstName,
            'guest_last_name' => $guestLastName,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'payment_option' => $paymentOption,
            'payment_method' => $paymentMethod,
            'full_booking_amount' => $fullBookingAmount,
            'offer_price' => $offerPrice
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

    // Return success response with redirect URL
    echo json_encode([
        'success' => true,
        'redirect_url' => $responseData['invoice_url'],
        'invoice_id' => $invoiceId,
        'amount' => $amountToPay
    ]);

} catch (Exception $e) {
    // Log the error for debugging
    error_log('Xendit Payment Error: ' . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
