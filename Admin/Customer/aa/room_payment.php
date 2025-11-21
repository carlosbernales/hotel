9<?php
session_start();
require 'db_con.php';

/**
 * Inserts booking data into the database
 * @param array $bookingData Array containing all booking information
 * @param PDO $pdo Database connection object
 * @return array Returns an array with success status and booking ID or error message
 */
function insertBookingIntoDatabase($bookingData, $pdo) {
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Generate a unique booking reference
        $bookingReference = 'BK-' . strtoupper(uniqid());
        
        // Prepare booking data
        $userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $numberOfGuests = (int)($bookingData['num_adults'] ?? 1) + (int)($bookingData['num_children'] ?? 0);
        $nights = (int)($bookingData['num_nights'] ?? 1);
        $totalAmount = (float)($bookingData['total_amount'] ?? 0);
        $paymentAmount = (float)($bookingData['payment_amount'] ?? $totalAmount);
        $remainingBalance = (float)($bookingData['remaining_balance'] ?? ($totalAmount - $paymentAmount));
        
        // 1. Insert into bookings table
        $stmt = $pdo->prepare("
            INSERT INTO bookings (
                user_id, check_in, check_out, number_of_guests,
                payment_method, payment_option, nights, total_amount,
                downpayment_amount, remaining_balance, status, reference_number, 
                room_type_id, room_quantity, num_adults, num_children, created_at
            ) VALUES (
                :user_id, :check_in, :check_out, :number_of_guests,
                :payment_method, :payment_option, :nights, :total_amount,
                :downpayment_amount, :remaining_balance, 'confirmed', :reference_number,
                :room_type_id, :room_quantity, :num_adults, :num_children, NOW()
            )"
        );

        // Execute booking insertion
        $bookingParams = [
            'user_id' => $userId,
            'check_in' => $bookingData['check_in'] ?? date('Y-m-d'),
            'check_out' => $bookingData['check_out'] ?? date('Y-m-d', strtotime('+1 day')),
            'number_of_guests' => $numberOfGuests,
            'payment_method' => $bookingData['payment_method'] ?? 'Cash',
            'payment_option' => $bookingData['payment_option'] ?? 'Full Payment',
            'nights' => $nights,
            'total_amount' => $totalAmount,
            'downpayment_amount' => $paymentAmount,
            'remaining_balance' => $remainingBalance,
            'reference_number' => $bookingReference,
            'room_type_id' => $bookingData['room_type_id'] ?? null,
            'room_quantity' => $bookingData['room_quantity'] ?? 1,
            'num_adults' => $bookingData['num_adults'] ?? 1,
            'num_children' => $bookingData['num_children'] ?? 0
        ];

        $stmt->execute($bookingParams);
        $bookingId = $pdo->lastInsertId();
        
        // 2. Insert guest information if available
        if (isset($bookingData['first_name']) || isset($bookingData['last_name'])) {
            try {
                $guestStmt = $pdo->prepare("
                    INSERT INTO booking_guests (
                        booking_id, guest_name, guest_type, guest_age, id_proof_image, created_at
                    ) VALUES (
                        :booking_id, :guest_name, :guest_type, :guest_age, :id_proof_image, NOW()
                    )"
                );

                $firstName = $bookingData['first_name'] ?? '';
                $lastName = $bookingData['last_name'] ?? '';
                $guestType = $userId ? 'registered_user' : 'guest';
                
                // Insert primary guest (booker)
                $guestStmt->execute([
                    'booking_id' => $bookingId,
                    'guest_name' => trim($firstName . ' ' . $lastName),
                    'guest_type' => $guestType,
                    'guest_age' => $bookingData['age'] ?? null,
                    'id_proof_image' => $bookingData['id_proof_image'] ?? null
                ]);
                
                // Insert additional guests if available
                if (!empty($bookingData['guests']) && is_array($bookingData['guests'])) {
                    foreach ($bookingData['guests'] as $guest) {
                        if (!empty($guest['first_name']) || !empty($guest['last_name'])) {
                            $guestName = trim(($guest['first_name'] ?? '') . ' ' . ($guest['last_name'] ?? ''));
                            $guestType = $guest['guest_type'] ?? 'additional_guest';
                            
                            $guestStmt->execute([
                                'booking_id' => $bookingId,
                                'guest_name' => $guestName,
                                'guest_type' => $guestType,
                                'guest_age' => $guest['age'] ?? null,
                                'id_proof_image' => $guest['id_proof_path'] ?? null
                            ]);
                        }
                    }
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $pdo->rollBack();
                
                return [
                    'success' => false,
                    'error' => 'Database error: ' . $e->getMessage()
                ];
            }
        }
        
        // 3. Insert room items if any
        if (!empty($bookingData['room_items']) && is_array($bookingData['room_items'])) {
            $roomStmt = $pdo->prepare("
                INSERT INTO booking_rooms (
                    booking_id, room_id, room_type, room_price, quantity, created_at
                ) VALUES (
                    :booking_id, :room_id, :room_type, :room_price, :quantity, NOW()
                )"
            );

            foreach ($bookingData['room_items'] as $item) {
                $roomStmt->execute([
                    'booking_id' => $bookingId,
                    'room_id' => $item['room_type_id'] ?? 0,
                    'room_type' => $item['room_type'] ?? 'Standard',
                    'room_price' => (float)($item['price'] ?? 0),
                    'quantity' => (int)($item['quantity'] ?? 1)
                ]);
            }
        }
        
        // 4. Insert payment record if amount is greater than 0
        if ($paymentAmount > 0) {
            $paymentStmt = $pdo->prepare("
                INSERT INTO payments (
                    booking_id, amount, payment_method, payment_status, 
                    reference_number, payment_date, created_at
                ) VALUES (
                    :booking_id, :amount, :payment_method, 'completed', 
                    :reference_number, NOW(), NOW()
                )"
            );

            $paymentStmt->execute([
                'booking_id' => $bookingId,
                'amount' => $paymentAmount,
                'payment_method' => $bookingParams['payment_method'],
                'reference_number' => 'PAY-' . strtoupper(uniqid())
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        return [
            'success' => true,
            'booking_id' => $bookingId,
            'reference' => $bookingReference
        ];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        return [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ];
    }
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to sanitize input
function sanitizeInput($data) {
    // If data is an array, recursively sanitize each element
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    // If data is not a string, return as is
    if (!is_string($data)) {
        return $data;
    }
    
    // Sanitize string data
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $data;
}

// Debug: Log the initial state of booking data
if (!empty($_SESSION['debug_booking_data'])) {
    error_log('Initial booking data from session: ' . print_r($_SESSION['debug_booking_data'], true));
}

// Initialize variables
$bookingData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'check_in' => '',
    'check_out' => '',
    'num_adults' => '1', // Default to 1 adult
    'num_children' => '0', // Default to 0 children
    'num_nights' => '1', // Default to 1 night
    'payment_option' => '',
    'payment_method' => '',
    'total_amount' => 0,
    'payment_amount' => 0,
    'remaining_balance' => 0,
    'room_items' => []
];

// Try to get room items from session booking list if not already set
if (empty($bookingData['room_items']) && !empty($_SESSION['booking_list']) && is_array($_SESSION['booking_list'])) {
    $bookingData['room_items'] = [];
    foreach ($_SESSION['booking_list'] as $item) {
        if (isset($item['room_type_id']) && isset($item['room_type'])) {
            $bookingData['room_items'][] = [
                'room_type' => $item['room_type'],
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity'] ?? 1,
                'total' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1)
            ];
            
            // Set total amount if not set
            if (empty($bookingData['total_amount'])) {
                $bookingData['total_amount'] = 0;
            }
            $bookingData['total_amount'] += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
    }
    
    // Set payment amount to total amount if not set
    if (empty($bookingData['payment_amount']) && !empty($bookingData['total_amount'])) {
        $bookingData['payment_amount'] = $bookingData['total_amount'];
        $bookingData['remaining_balance'] = 0;
    }
}

// Check for successful payment and restore booking data from session if available
if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
    // Try to get booking data from session
    if (!empty($_SESSION['booking_data'])) {
        $bookingData = array_merge($bookingData, $_SESSION['booking_data']);
    } 
    // If not in session, try to get from cookie as fallback
    elseif (!empty($_COOKIE['booking_data'])) {
        $cookieData = json_decode($_COOKIE['booking_data'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $bookingData = array_merge($bookingData, $cookieData);
            // Save back to session for consistency
            $_SESSION['booking_data'] = $cookieData;
        }
    }
}
// Process URL parameters
else if (!empty($_GET)) {
    $urlData = array_map('sanitizeInput', $_GET);
    
    // Map URL parameters to booking data
    $mapping = [
        'check_in' => ['key' => 'check_in', 'type' => 'string'],
        'check_out' => ['key' => 'check_out', 'type' => 'string'],
        'adults' => ['key' => 'num_adults', 'type' => 'int'],
        'children' => ['key' => 'num_children', 'type' => 'int'],
        'total' => ['key' => 'total_amount', 'type' => 'float'],
        'first_name' => ['key' => 'first_name', 'type' => 'string'],
        'last_name' => ['key' => 'last_name', 'type' => 'string'],
        'email' => ['key' => 'email', 'type' => 'string'],
        'phone' => ['key' => 'phone', 'type' => 'string'],
        'room_type' => ['key' => 'room_type', 'type' => 'string'],
        'room_type_id' => ['key' => 'room_type_id', 'type' => 'int'],
        'room_price' => ['key' => 'room_price', 'type' => 'float']
    ];
    
    foreach ($mapping as $urlParam => $config) {
        if (isset($urlData[$urlParam])) {
            $value = $urlData[$urlParam];
            // Convert value to appropriate type
            switch ($config['type']) {
                case 'int':
                    $value = (int)$value;
                    break;
                case 'float':
                    $value = (float)$value;
                    break;
                default:
                    // string type, no conversion needed
                    $value = trim($value);
                    break;
            }
            $bookingData[$config['key']] = $value;
        }
    }
    
    // If room type and price are provided, create a room item
    if (!empty($bookingData['room_type']) && isset($bookingData['room_price'])) {
        $roomItem = [
            'room_type' => $bookingData['room_type'],
            'price' => $bookingData['room_price'],
            'quantity' => 1,
            'total' => $bookingData['room_price']
        ];
        
        // Add room_type_id to the room item if available
        if (!empty($bookingData['room_type_id'])) {
            $roomItem['room_type_id'] = $bookingData['room_type_id'];
        }
        
        $bookingData['room_items'] = [$roomItem];
        
        // Set default payment option and method if not set
        if (empty($bookingData['payment_option'])) {
            $bookingData['payment_option'] = 'Full Payment';
        }
        if (empty($bookingData['payment_method'])) {
            $bookingData['payment_method'] = 'Credit/Debit Card';
        }
    }
    
    // Calculate number of nights if check-in and check-out dates are set
    if (!empty($bookingData['check_in']) && !empty($bookingData['check_out'])) {
        $checkIn = new DateTime($bookingData['check_in']);
        $checkOut = new DateTime($bookingData['check_out']);
        $interval = $checkIn->diff($checkOut);
        $bookingData['num_nights'] = $interval->days;
    }
}

// Process request data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process POST data
    $requestData = $_POST;
    
    // Check if we're using URL parameters (from roomss.php)
    if (!empty($requestData['use_url_params'])) {
        $guestCount = isset($requestData['guest_count']) ? (int)$requestData['guest_count'] : 0;
        $guests = [];
        
        for ($i = 0; $i < $guestCount; $i++) {
            $guest = [
                'first_name' => $requestData['guest_'.$i.'_first_name'] ?? '',
                'last_name' => $requestData['guest_'.$i.'_last_name'] ?? '',
                'guest_type' => $requestData['guest_'.$i.'_type'] ?? 'regular',
                'id_number' => $requestData['guest_'.$i.'_id_number'] ?? '',
                'id_type' => $requestData['guest_'.$i.'_id_type'] ?? '',
                'id_proof' => null
            ];
            
            // Process ID proof if it exists in the URL
            $idProofKey = 'guest_'.$i.'_id_proof';
            if (!empty($requestData[$idProofKey])) {
                $idProofData = json_decode($requestData[$idProofKey], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($idProofData)) {
                    $guest['id_proof'] = $idProofData;
                    
                    // Save the file to the server if needed
                    $uploadDir = 'uploads/id_proofs/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = uniqid() . '_' . ($idProofData['filename'] ?? 'id_proof.jpg');
                    $filePath = $uploadDir . $fileName;
                    
                    // Save the base64 data to a file
                    $fileData = base64_decode($idProofData['data']);
                    if ($fileData !== false && file_put_contents($filePath, $fileData)) {
                        $guest['id_proof_path'] = $filePath;
                    }
                }
            }
            
            $guests[] = $guest;
        }
        
        if (!empty($guests)) {
            $requestData['guest_info'] = $guests;
        }
        
        // Remove the flag to prevent processing again
        unset($requestData['use_url_params']);
    }
    
    // Process guest information if available
    if (!empty($requestData['guest_info'])) {
        $requestData['guests'] = json_decode($requestData['guest_info'], true);
        unset($requestData['guest_info']);
    }
    
    // Process ID proofs from form data (base64 encoded)
    $guestCount = isset($requestData['guest_count']) ? (int)$requestData['guest_count'] : 0;
    $guests = [];
    
    for ($i = 0; $i < $guestCount; $i++) {
        $guest = [
            'first_name' => $requestData['guest_first_name_'.$i] ?? '',
            'last_name' => $requestData['guest_last_name_'.$i] ?? '',
            'guest_type' => $requestData['guest_type_'.$i] ?? 'regular',
            'id_number' => $requestData['guest_id_number_'.$i] ?? '',
            'id_type' => $requestData['guest_id_type_'.$i] ?? '',
            'id_proof' => null
        ];
        
        // Check for ID proof in base64 format
        $idProofKey = 'guest_id_proof_'.$i;
        if (!empty($requestData[$idProofKey])) {
            $idProofData = json_decode($requestData[$idProofKey], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($idProofData)) {
                $guest['id_proof'] = $idProofData;
                
                // Save the file to the server if needed
                $uploadDir = 'uploads/id_proofs/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . ($idProofData['filename'] ?? 'id_proof.jpg');
                $filePath = $uploadDir . $fileName;
                
                // Save the base64 data to a file
                $fileData = base64_decode($idProofData['data']);
                if ($fileData !== false && file_put_contents($filePath, $fileData)) {
                    $guest['id_proof_path'] = $filePath;
                }
            }
        }
        
        $guests[] = $guest;
    }
    
    if (!empty($guests)) {
        $requestData['guest_info'] = $guests;
    }
    
    // Process room items if sent as JSON string
    if (!empty($requestData['room_items_json'])) {
        $roomItems = json_decode($requestData['room_items_json'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $bookingData['room_items'] = $roomItems;
            unset($requestData['room_items_json']);
        }
    }
    
    // Calculate number of nights if check-in and check-out dates are set
    if (!empty($requestData['check_in']) && !empty($requestData['check_out'])) {
        try {
            $checkIn = new DateTime($requestData['check_in']);
            $checkOut = new DateTime($requestData['check_out']);
            
            // Ensure check-out is after check-in
            if ($checkOut <= $checkIn) {
                throw new Exception('Check-out date must be after check-in date');
            }
            
            $interval = $checkIn->diff($checkOut);
            $bookingData['num_nights'] = $interval->days;
            $bookingData['check_in'] = $checkIn->format('Y-m-d');
            $bookingData['check_out'] = $checkOut->format('Y-m-d');
        } catch (Exception $e) {
            // Log error and set default values
            error_log('Invalid date format: ' . $e->getMessage());
            $bookingData['check_in'] = date('Y-m-d');
            $bookingData['check_out'] = date('Y-m-d', strtotime('+1 day'));
            $bookingData['num_nights'] = 1;
        }
    } else {
        // Set default values if dates are not provided
        $bookingData['check_in'] = date('Y-m-d');
        $bookingData['check_out'] = date('Y-m-d', strtotime('+1 day'));
        $bookingData['num_nights'] = 1;
    }
    
    // Merge other parameters into booking data
    foreach ($requestData as $key => $value) {
        if ($key === 'guest_info') {
            // Handle guest info separately to preserve arrays
            $bookingData['guest_info'] = $value;
        } elseif (is_array($value)) {
            // Handle array fields (like guest names)
            $bookingData[$key] = array_map('sanitizeInput', $value);
        } else {
            $bookingData[$key] = sanitizeInput($value);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Process GET parameters for backward compatibility
    $params = $_GET;
    
    // Debug: Log all GET parameters
    error_log('GET parameters: ' . print_r($params, true));
    
    // Process room items if present
    if (isset($params['room_items'])) {
        // First try to decode as is (might be already decoded by the server)
        $roomItems = is_array($params['room_items']) ? $params['room_items'] : null;
        
        // If not an array, try to decode as JSON
        if (!is_array($roomItems)) {
            $roomItems = json_decode(urldecode($params['room_items']), true);
            
            // If still not an array, try without urldecode first
            if (json_last_error() !== JSON_ERROR_NONE) {
                $roomItems = json_decode($params['room_items'], true);
            }
        }
        
        if (is_array($roomItems) && !empty($roomItems)) {
            $bookingData['room_items'] = $roomItems;
            error_log('Successfully processed room items: ' . print_r($roomItems, true));
        } else {
            error_log('Failed to decode room items. Raw value: ' . $params['room_items']);
            error_log('JSON error: ' . json_last_error_msg());
        }
        unset($params['room_items']);
    }
    
    // Merge other parameters into booking data
    foreach ($params as $key => $value) {
        if (array_key_exists($key, $bookingData)) {
            $bookingData[$key] = sanitizeInput($value);
        }
    }
}

// Calculate number of nights
if (!empty($bookingData['check_in']) && !empty($bookingData['check_out'])) {
    $checkIn = new DateTime($bookingData['check_in']);
    $checkOut = new DateTime($bookingData['check_out']);
    $interval = $checkIn->diff($checkOut);
    $bookingData['num_nights'] = $interval->days;
}

// Calculate payment amounts if not set
if (empty($bookingData['payment_amount']) && !empty($bookingData['total_amount'])) {
    if ($bookingData['payment_option'] === 'Full Payment') {
        $bookingData['payment_amount'] = $bookingData['total_amount'];
        $bookingData['remaining_balance'] = 0;
    } elseif ($bookingData['payment_option'] === 'Partial Payment') {
        $bookingData['payment_amount'] = 1500; // Fixed downpayment
        $bookingData['remaining_balance'] = floatval($bookingData['total_amount']) - 1500;
    } elseif ($bookingData['payment_option'] === 'Partial Payment' && !empty($bookingData['custom_payment'])) {
        $bookingData['payment_amount'] = max(1500, floatval($bookingData['custom_payment']));
        $bookingData['remaining_balance'] = max(0, floatval($bookingData['total_amount']) - floatval($bookingData['payment_amount']));
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment - Casa Estela Boutique Hotel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .booking-summary {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
        }
        .payment-form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .room-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .room-item:last-child {
            border-bottom: none;
        }
        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .btn-pay-now {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .btn-pay-now:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="text-center">
                    <h2 class="mb-4">Complete Your Booking</h2>

                    </div>
                </div>
            </div>
            
            <div class="col-lg-8 offset-lg-2">
                <div class="booking-summary">
                    <h3 class="mb-4">Booking Summary</h3>
                    
                    <div class="mb-3">
                        <h5>Guest Information</h5>
                        <p class="mb-1"><?php echo htmlspecialchars($bookingData['first_name'] . ' ' . $bookingData['last_name']); ?></p>
                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($bookingData['email']); ?></p>
                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($bookingData['phone']); ?></p>
                        
                        <?php 
                        // Display guest information if available
                        if (!empty($bookingData['guest_info'])) {
                            // Convert guest_info to guests array format if it's not already
                            if (!isset($bookingData['guests'])) {
                                $bookingData['guests'] = [];
                                foreach ($bookingData['guest_info'] as $index => $guest) {
                                    $bookingData['guests'][] = [
                                        'first_name' => $guest['first_name'] ?? '',
                                        'last_name' => $guest['last_name'] ?? '',
                                        'guest_type' => $guest['guest_type'] ?? 'regular',
                                        'id_number' => $guest['id_number'] ?? '',
                                        'id_type' => $guest['id_type'] ?? '',
                                        'id_proof' => $guest['id_proof'] ?? null
                                    ];
                                }
                            }
                            echo '<div class="mt-3">';
                            echo '<h6>Additional Guests:</h6>';
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-sm align-middle">';
                            echo '<thead><tr>';
                            echo '<th>Name</th>';
                            echo '<th>Type</th>';
                            echo '<th>ID Proof</th>';
                            echo '</tr></thead>';
                            echo '<tbody>';
                            
                            foreach ($bookingData['guests'] as $index => $guest) {
                                if (empty($guest['first_name']) && empty($guest['last_name'])) continue;
                                
                                $fullName = trim(($guest['first_name'] ?? '') . ' ' . ($guest['last_name'] ?? ''));
                                $guestType = $guest['guest_type'] ?? 'Regular';
                                $idType = $guest['id_type'] ?? '';
                                $idNumber = $guest['id_number'] ?? '';
                                $idProof = $guest['id_proof'] ?? null;
                                
                                // Format guest type for display
                                $displayType = ucfirst(str_replace('_', ' ', $guestType));
                                
                                // Set badge color based on guest type
                                $badgeClass = 'bg-secondary'; // Default
                                if (strtolower($guestType) === 'senior' || strtolower($guestType) === 'senior citizen') {
                                    $badgeClass = 'bg-warning text-dark';
                                } elseif (strtolower($guestType) === 'pwd') {
                                    $badgeClass = 'bg-primary';
                                }
                                
                                echo '<tr>';
                                echo '<td class="align-middle">' . htmlspecialchars($fullName) . '</td>';
                                echo '<td class="align-middle"><span class="badge ' . $badgeClass . '">' . htmlspecialchars($displayType) . '</span></td>';
                                echo '<td class="align-middle">';
                                
                                if (!empty($idProof)) {
                                    if (is_array($idProof) && !empty($idProof['data'])) {
                                        // Display as image if it's an image
                                        $mimeType = $idProof['type'] ?? '';
                                        $base64Data = $idProof['data'] ?? '';
                                        $fileName = $idProof['filename'] ?? 'id_proof.jpg';
                                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                        $isImage = strpos($mimeType, 'image/') === 0 || in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']); 
                                        
                                        if ($isImage) {
                                            // For images, show a preview button with lightbox
                                            $uniqueId = 'id-proof-' . $index . '-' . uniqid();
                                            echo '<div class="d-flex align-items-center gap-2">';
                                            // Thumbnail
                                            echo '<div class="position-relative" style="width: 50px; height: 50px; overflow: hidden; border-radius: 4px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">';
                                            echo '<img src="data:' . htmlspecialchars($mimeType) . ';base64,' . $base64Data . '" ';
                                            echo 'alt="ID Proof" style="max-width: 100%; max-height: 100%; object-fit: contain;">';
                                            echo '</div>';
                                            // View button
                                            echo '<a href="#" class="btn btn-sm btn-outline-primary view-id-proof" ';
                                            echo 'data-bs-toggle="modal" data-bs-target="#idProofModal" ';
                                            echo 'data-title="ID Proof for ' . htmlspecialchars($fullName) . '" ';
                                            echo 'data-src="data:' . htmlspecialchars($mimeType) . ';base64,' . $base64Data . '">';
                                            echo '<i class="fas fa-eye me-1"></i> View ID';
                                            echo '</a>';
                                            // Download button
                                            echo '<a href="data:' . htmlspecialchars($mimeType) . ';base64,' . $base64Data . '" ';
                                            echo 'download="' . htmlspecialchars($fileName) . '" ';
                                            echo 'class="btn btn-sm btn-outline-secondary">';
                                            echo '<i class="fas fa-download me-1"></i> Download';
                                            echo '</a>';
                                            echo '</div>';
                                        } else {
                                            // For non-image files, show a download link
                                            echo '<div class="d-flex align-items-center gap-2">';
                                            echo '<div class="file-icon">';
                                            echo '<i class="fas fa-file-alt fa-2x text-muted"></i>';
                                            echo '</div>';
                                            echo '<a href="data:' . htmlspecialchars($mimeType) . ';base64,' . $base64Data . '" ';
                                            echo 'download="' . htmlspecialchars($fileName) . '" ';
                                            echo 'class="btn btn-sm btn-outline-secondary">';
                                            echo '<i class="fas fa-download me-1"></i> Download ' . htmlspecialchars($fileExtension);
                                            echo '</a>';
                                            echo '</div>';
                                        }
                                    } elseif (is_string($idProof)) {
                                        // Handle JSON string format for ID proof
                                        try {
                                            $idProofData = json_decode($idProof, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($idProofData)) {
                                                // If it's a valid JSON array, use it as the ID proof
                                                $idProof = $idProofData;
                                                // Continue to the main display logic
                                                continue;
                                            }
                                        } catch (Exception $e) {
                                            // If JSON decode fails, treat as legacy format
                                            echo '<div class="d-flex align-items-center gap-2">';
                                            echo '<i class="fas fa-check-circle text-success"></i>';
                                            echo '<a href="' . htmlspecialchars($idProof) . '" target="_blank" class="btn btn-sm btn-outline-primary">';
                                            echo '<i class="fas fa-eye me-1"></i> View ID';
                                            echo '</a>';
                                            echo '</div>';
                                        }
                                    }
                                } else {
                                    echo '<div class="d-flex align-items-center gap-2">';
                                    echo '<i class="fas fa-times-circle text-danger"></i>';
                                    echo '<span class="text-muted">No ID proof</span>';
                                    echo '</div>';
                                }
                                
                                echo '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</tbody></table></div>';
                            
                            // Add ID Proof Modal
                            echo '<div class="modal fade" id="idProofModal" tabindex="-1" aria-labelledby="idProofModalLabel" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-lg modal-dialog-centered">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="idProofModalLabel">ID Proof</h5>';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body text-center">';
                            echo '<img id="modalIdProofImage" src="" class="img-fluid" alt="ID Proof" style="max-height: 70vh;">';
                            echo '</div>';
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            
                            // Add JavaScript for modal
                            echo '<script>';
                            echo 'document.addEventListener("DOMContentLoaded", function() {';
                            echo '  const viewButtons = document.querySelectorAll(".view-id-proof");';
                            echo '  const modalTitle = document.getElementById("idProofModalLabel");';
                            echo '  const modalImage = document.getElementById("modalIdProofImage");';
                            echo '  ';
                            echo '  viewButtons.forEach(button => {';
                            echo '    button.addEventListener("click", function(e) {';
                            echo '      e.preventDefault();';
                            echo '      const title = this.getAttribute("data-title");';
                            echo '      const src = this.getAttribute("data-src");';
                            echo '      modalTitle.textContent = title;';
                            echo '      modalImage.src = src;';
                            echo '    });';
                            echo '  });';
                            echo '  ';
                            echo '  // Clear image when modal is hidden to free memory';
                            echo '  const modal = document.getElementById("idProofModal");';
                            echo '  modal.addEventListener("hidden.bs.modal", function() {';
                            echo '    modalImage.src = "";';
                            echo '  });';
                            echo '});';
                            echo '</script>';
                            
                            echo '</div>'; // Close mt-3 div
                        }
                        ?>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Stay Details</h5>
                        <p class="mb-1">
                            <i class="far fa-calendar-alt me-2"></i>
                            <?php 
                            if (!empty($bookingData['check_in']) && !empty($bookingData['check_out'])) {
                                try {
                                    $checkIn = new DateTime($bookingData['check_in']);
                                    $checkOut = new DateTime($bookingData['check_out']);
                                    echo $checkIn->format('F j, Y') . ' - ' . $checkOut->format('F j, Y');
                                } catch (Exception $e) {
                                    // Fallback to current date if there's an error
                                    $today = new DateTime();
                                    $tomorrow = (clone $today)->modify('+1 day');
                                    echo $today->format('F j, Y') . ' - ' . $tomorrow->format('F j, Y');
                                }
                            } else {
                                // Default to current date if dates are not set
                                $today = new DateTime();
                                $tomorrow = (clone $today)->modify('+1 day');
                                echo $today->format('F j, Y') . ' - ' . $tomorrow->format('F j, Y');
                            }
                            ?>
                            <span class="text-muted">(<?php echo $bookingData['num_nights']; ?> night<?php echo $bookingData['num_nights'] > 1 ? 's' : ''; ?>)</span>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-users me-2"></i>
                            <?php 
                            echo $bookingData['num_adults'] . ' Adult' . ($bookingData['num_adults'] > 1 ? 's' : '');
                            if ($bookingData['num_children'] > 0) {
                                echo ', ' . $bookingData['num_children'] . ' Child' . ($bookingData['num_children'] > 1 ? 'ren' : '');
                            }
                            ?>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Room Details</h5>
                        <?php 
                        // Debug: Log the state of room_items before processing
                        error_log('Raw room_items: ' . print_r($bookingData['room_items'] ?? 'No room_items', true));
                        
                        if (!empty($bookingData['room_items'])) {
                            // Check if room_items is a JSON string
                            if (is_string($bookingData['room_items'])) {
                                $roomItems = json_decode($bookingData['room_items'], true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $bookingData['room_items'] = $roomItems;
                                } else {
                                    $bookingData['room_items'] = [];
                                }
                            }
                            
                            if (is_array($bookingData['room_items']) && !empty($bookingData['room_items'])) {
                                foreach ($bookingData['room_items'] as $item): 
                                    if (is_string($item)) {
                                        $item = json_decode($item, true);
                                    }
                                    
                                    if (!is_array($item)) continue;
                                    
                                    $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                                    $price = isset($item['price']) ? (float)$item['price'] : 0;
                                    $total = $price * $quantity;
                                    $roomType = !empty($item['room_type']) ? $item['room_type'] : 
                                              (!empty($item['name']) ? $item['name'] : 'Room');
                                    ?>
                                    <div class="room-item mb-2 p-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="fw-bold"><?php echo htmlspecialchars($roomType); ?> x<?php echo $quantity; ?></span>
                                                <?php if (!empty($item['description'])): ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($item['description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <div>₱<?php echo number_format($price, 2); ?></div>
                                                <?php if ($quantity > 1): ?>
                                                    <div class="text-muted small">x<?php echo $quantity; ?> = ₱<?php echo number_format($total, 2); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                endforeach; 
                            } else {
                                echo '<p class="text-muted">No room details available</p>';
                            }
                        } else {
                            echo '<p class="text-muted">No rooms selected</p>';
                        }
                        ?>
                    </div>
                    
                    <div class="border-top pt-3">
                        <?php 
                        // Get discount amount from GET or session
                        $discountAmount = isset($_GET['discount']) ? (float)$_GET['discount'] : 0;
                        $subtotal = isset($bookingData['total_amount']) ? (float)$bookingData['total_amount'] : 0;
                        
                        // If discount is not in GET, try to get it from session
                        if ($discountAmount <= 0 && isset($_SESSION['booking_data']['discount'])) {
                            $discountAmount = (float)$_SESSION['booking_data']['discount'];
                        }
                        
                        // Calculate final amount after discount
                        $finalAmount = $subtotal - $discountAmount;
                        ?>
                        <?php if ($discountAmount > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₱<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount (Senior Citizen/PWD):</span>
                            <span>- ₱<?php echo number_format($discountAmount, 2); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₱<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Option:</span>
                            <span><?php echo htmlspecialchars($bookingData['payment_option']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Method:</span>
                            <span><?php echo htmlspecialchars($bookingData['payment_method']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                            <span>Amount to Pay:</span>
                            <span class="total-amount">₱<?php 
                                // Use the discounted amount if available, otherwise fall back to the payment amount
                                $amountToPay = ($discountAmount > 0 && $finalAmount > 0) ? $finalAmount : 
                                    (isset($bookingData['payment_amount']) ? (float)$bookingData['payment_amount'] : 0);
                                echo number_format($amountToPay, 2); 
                            ?></span>
                        </div>
                        <?php if (isset($bookingData['remaining_balance']) && (float)$bookingData['remaining_balance'] > 0): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Total Amount</h5>
                            <h4 class="mb-0">₱<?php echo number_format($bookingData['total_amount'], 2); ?></h4>
                        </div>
                        
                        <?php 
                        $paymentStatus = 'pending';
                        if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
                            $paymentStatus = 'success';
                        }
                        ?>
                        
                        <?php if ($paymentStatus === 'success'): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Amount Paid</h5>
                                <h5 class="mb-0 text-success">- ₱<?php echo number_format($bookingData['payment_amount'], 2); ?></h5>
                            </div>
                            
                            <?php if ($bookingData['remaining_balance'] > 0): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Remaining Balance</h5>
                                    <h5 class="mb-0 text-danger">₱<?php echo number_format($bookingData['remaining_balance'], 2); ?></h5>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Please settle your remaining balance before check-in.
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Payment Status</h5>
                                    <span class="badge bg-success">Fully Paid</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Payment Status</h5>
                                <span class="badge bg-warning">Pending Payment</span>
                            </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex flex-column gap-3">
                        <button id="paymongo-button" type="button" class="btn btn-outline-secondary btn-lg">
                            <img src="https://assets-global.website-files.com/5fdb2866020c6cdcaa83b5a4/5fdb80d5d8f0d9f6b2e6e1d3_paymongo-logo-vector.svg" alt="PayMongo" style="height: 24px; margin-right: 10px;">
                            Pay with PayMongo
                        </button>
                        <div class="text-muted small text-center mt-2">
                            Secure payment processing powered by PayMongo
                        </div>
                        
                        <!-- Finish Button (initially disabled) -->
                        <button id="finishButton" class="btn btn-success btn-lg mt-3" disabled>
                            <i class="fas fa-check-circle me-2"></i>Finish Booking
                            <span id="finishSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Your booking is not confirmed until you complete the payment process.
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    
    <!-- jQuery and Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- PayMongo JS SDK -->
    <script src="https://js.paymongo.com/v1/paymongo.js"></script>
    
    <script>
        // Handle PayMongo button click
        document.getElementById('paymongo-button').addEventListener('click', async function() {
            // Disable button to prevent multiple clicks
            const button = this;
            button.disabled = true;
            
            // Show loading state
            const buttonText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Prepare booking data
            const bookingData = <?php echo json_encode($bookingData); ?>;
            
            try {
                // First, save the booking data to the session
                const saveResponse = await fetch('save_booking_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=save_booking&' + new URLSearchParams(bookingData).toString()
                });
                
                const saveResult = await saveResponse.json();
                
                if (!saveResponse.ok || saveResult.status !== 'success') {
                    throw new Error('Failed to save booking data');
                }
                
                // Add success and cancel URLs
                const successUrl = new URL(window.location.href);
                successUrl.searchParams.set('payment', 'success');
                const roomItems = bookingData.room_items || [];
                if (roomItems.length > 0) {
                    successUrl.searchParams.append('room_items', JSON.stringify(roomItems));
                }
                successUrl.searchParams.append('check_in', bookingData.check_in || '');
                successUrl.searchParams.append('check_out', bookingData.check_out || '');
                successUrl.searchParams.append('num_adults', bookingData.num_adults || 1);
                successUrl.searchParams.append('num_children', bookingData.num_children || 0);
                successUrl.searchParams.append('total_amount', bookingData.total_amount || 0);
                successUrl.searchParams.append('payment_option', bookingData.payment_option || 'Full Payment');
                
                const cancelUrl = new URL(window.location.href);
                cancelUrl.searchParams.set('payment', 'cancelled');
                
                // Prepare request data
                const requestData = {
                    amount: Math.round(bookingData.payment_amount * 100), // Convert to centavos
                    description: 'Room Booking - ' + (bookingData.room_items?.[0]?.room_type || 'Standard Room'),
                    success_url: successUrl.toString(),
                    cancel_url: cancelUrl.toString(),
                    metadata: {
                        booking_reference: 'BK-' + Date.now()
                    }
                };
                
                // Send request to create PayMongo checkout session
                const response = await fetch('create_paymongo_checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });
                
                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Failed to create payment session');
                }
                
                const data = await response.json();
                
                // Redirect to PayMongo checkout
                if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                } else {
                    throw new Error('No checkout URL received');
                }
            } catch (error) {
                console.error('Payment error:', error);
                
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Error',
                    text: error.message || 'An error occurred while processing your payment. Please try again.',
                    confirmButtonText: 'OK'
                });
                
                // Re-enable the button
                button.disabled = false;
                button.innerHTML = buttonText;
            }
        });

        // Check for payment status in URL
        const urlParams = new URLSearchParams(window.location.search);
        const paymentStatus = urlParams.get('payment');
        
        if (paymentStatus === 'success') {
            // Enable the finish booking button
            const finishButton = document.getElementById('finishButton');
            if (finishButton) {
                finishButton.disabled = false;
                
                // Debug: Log the current booking data after successful payment
                const bookingData = <?php echo json_encode($bookingData); ?>;
                console.log('After payment success - bookingData:', bookingData);
                console.log('Room items after payment:', bookingData.room_items || 'No room items found');
                finishButton.classList.remove('disabled');
                
                // Hide the payment button and show the finish button
                const paymongoButton = document.getElementById('paymongo-button');
                if (paymongoButton) {
                    paymongoButton.style.display = 'none';
                }
                
                // Hide the warning message
                const warningMessage = document.querySelector('.alert.alert-warning');
                if (warningMessage) {
                    warningMessage.style.display = 'none';
                }
                
                // Store booking data in session
                fetch('save_booking_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=save_booking&' + new URLSearchParams(<?php echo json_encode($bookingData); ?>).toString()
                });
                
                // Add click handler for finish button
                finishButton.addEventListener('click', handleFinishBooking);
            }
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Payment Successful!',
                text: 'Your payment has been confirmed. A payment confirmation has been sent to your email. Press OK then click the Finish Booking button.',
                confirmButtonText: 'OK!',
                allowOutsideClick: false
            }).then(() => {
                // Remove payment status from URL but keep the parameters
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.delete('payment');
                window.history.replaceState({}, document.title, newUrl.toString());
            });
        } else if (paymentStatus === 'cancelled') {
            Swal.fire({
                icon: 'info',
                title: 'Payment Cancelled',
                text: 'Your payment was cancelled. You can try again if you wish to complete your booking.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Remove payment status from URL
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }

        // Handle finish booking
        async function handleFinishBooking() {
            const finishButton = document.getElementById('finishButton');
            const finishSpinner = document.getElementById('finishSpinner');
            
            try {
                // Disable button and show spinner
                finishButton.disabled = true;
                finishSpinner.classList.remove('d-none');
                
                // Show processing message with loading state
                Swal.fire({
                    title: 'Processing Booking',
                    html: 'Please wait while we confirm your booking...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Prepare booking data
                const bookingData = <?php echo json_encode($bookingData); ?>;
                console.log('Initial bookingData:', bookingData);
                
                // Ensure room_items is properly set and extract room_type_id
                if (!bookingData.room_items && bookingData.roomItems) {
                    console.log('Setting room_items from roomItems');
                    bookingData.room_items = bookingData.roomItems;
                    
                    // Extract room_type_id from the first room item if available
                    if (bookingData.room_items && bookingData.room_items.length > 0) {
                        bookingData.room_type_id = bookingData.room_items[0].room_type_id;
                        console.log('Extracted room_type_id:', bookingData.room_type_id);
                    }
                }
                
                // Debug: Log the final state before sending
                console.log('Final room_items before sending:', bookingData.room_items);
                
                // Add CSRF token if available
                const formData = new FormData();
                Object.keys(bookingData).forEach(key => {
                    if (bookingData[key] !== null && bookingData[key] !== undefined) {
                        if (key === 'room_items' && Array.isArray(bookingData[key])) {
                            // Handle room items array specially to ensure it's properly formatted
                            formData.append('room_items', JSON.stringify(bookingData[key]));
                        } else if (typeof bookingData[key] === 'object') {
                            formData.append(key, JSON.stringify(bookingData[key]));
                        } else {
                            formData.append(key, bookingData[key]);
                        }
                    }
                });
                
                // Ensure room_type_id is included in form data
                if (bookingData.room_type_id) {
                    formData.append('room_type_id', bookingData.room_type_id);
                    console.log('Added room_type_id to form data:', bookingData.room_type_id);
                } else if (bookingData.room_items && bookingData.room_items.length > 0) {
                    // Try to get room_type_id from first room item if not already set
                    const firstItem = bookingData.room_items[0];
                    if (firstItem.room_type_id) {
                        formData.append('room_type_id', firstItem.room_type_id);
                        console.log('Extracted room_type_id from first room item:', firstItem.room_type_id);
                    }
                }
                
                // Debug: Log the form data being sent
                console.log('Form data being sent:', Object.fromEntries(formData));
                
                // Send request to finish booking
                const response = await fetch('finish_booking.php', {
                    method: 'POST',
                    body: formData
                });
                
                let result;
                try {
                    result = await response.json();
                } catch (e) {
                    console.error('Error parsing response:', e);
                    throw new Error('Invalid response from server');
                }
                
                if (response.ok && result.success) {
                    // Close loading modal
                    Swal.close();
                    
                    // Show success message with booking reference and redirect
                    const bookingRef = result.reference_number || 'N/A';
                    await Swal.fire({
                        icon: 'success',
                        title: 'Booking Confirmed!',
                        html: `
                            <div class="text-start">
                            <p class="mb-0"><strong>Booking Reference:</strong> ${bookingRef}</p>
                                <p>Your booking has been successfully processed.</p>
                                <p class="mb-0">The booking confirmation has been sent to your email address.</p>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        showConfirmButton: true
                    }).then((result) => {
                        // Clear all booking-related data from session
                    fetch('clear_booking_session.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=clear_booking'
                    }).then(() => {
                        // Clear any existing booking data from local storage
                        sessionStorage.removeItem('bookingData');
                        
                        // Redirect to rooms.php
                        window.location.href = 'roomss.php';
                    });
                    });
                } else {
                    throw new Error(result.message || 'Failed to process booking');
                }
                
            } catch (error) {
                console.error('Error:', error);
                
                // Show error message
                await Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: error.message || 'An error occurred while processing your booking. Please try again.',
                    confirmButtonText: 'OK',
                    allowOutsideClick: true,
                    showConfirmButton: true
                });
                
                // Re-enable button and hide spinner
                if (finishButton) finishButton.disabled = false;
                if (finishSpinner) finishSpinner.classList.add('d-none');
            }
        }
    // Initialize finish button handler if payment was successful
    if (paymentStatus === 'success') {
        const finishButton = document.getElementById('finishButton');
        if (finishButton) {
            finishButton.addEventListener('click', handleFinishBooking);
        }
    }
    
    $(document).ready(function() {
        // Format card number
        $('#cardNumber').on('input', function() {
            let value = $(this).val().replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formatted = '';
            for (let i = 0; i < value.length && i < 16; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            $(this).val(formatted);
        });
        
        // Format expiry date
        $('#expiryDate').on('input', function() {
            let value = $(this).val().replace(/\s*\/\s*\s*/g, '').replace(/[^0-9]/gi, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            $(this).val(value);
        });
        
        // Format CVV
        $('#cvv').on('input', function() {
            $(this).val($(this).val().replace(/[^0-9]/g, '').substring(0, 4));
        });
        
        // Process payment
        $('#processPayment').click(function() {
            // Simple validation
            const cardNumber = $('#cardNumber').val().replace(/\s+/g, '');
            const expiryDate = $('#expiryDate').val();
            const cvv = $('#cvv').val();
            const cardName = $('#cardName').val();
            
            if (!cardNumber || cardNumber.length < 16) {
                Swal.fire('Error', 'Please enter a valid card number', 'error');
                return;
            }
            
            if (!expiryDate || !/^\d{2}\/\d{2}$/.test(expiryDate)) {
                Swal.fire('Error', 'Please enter a valid expiry date (MM/YY)', 'error');
                return;
            }
            
            if (!cvv || cvv.length < 3) {
                Swal.fire('Error', 'Please enter a valid CVV', 'error');
                return;
            }
            
            if (!cardName) {
                Swal.fire('Error', 'Please enter the name on card', 'error');
                return;
            }
            
            // Show processing animation
            const btn = $(this);
            const btnText = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...');
            
            // Simulate API call
            setTimeout(function() {
                // In a real application, you would make an AJAX call to your payment processor here
                // For this example, we'll simulate a successful payment
                
                // Show success message
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Your booking has been confirmed. A confirmation has been sent to your email.',
                    icon: 'success',
                    confirmButtonText: 'View Booking',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to booking details page or home page
                        window.location.href = 'index.php?booking=success';
                    }
                });
                
                // Reset button
                btn.prop('disabled', false).html(btnText);
                
            }, 2000);
        });
    });
    </script>
</body>
</html>
 