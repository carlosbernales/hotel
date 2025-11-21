<?php
header('Content-Type: application/json');
require_once 'db_con.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'redirect' => 'login.php'
    ]);
    exit();
}

try {
    // Get database connection
    $pdo = Database::getInstance()->connect();
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Get JSON input
    $json = file_get_contents('php://input');
    error_log('Raw JSON input: ' . $json);
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error: ' . json_last_error_msg());
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }
    
    error_log('Decoded data: ' . print_r($data, true));
    
    // Get all parameters from the JSON data
    $packageName = $data['packageName'] ?? '';
    $packagePrice = isset($data['packagePrice']) ? floatval($data['packagePrice']) : 0;
    $basePrice = isset($data['basePrice']) ? floatval($data['basePrice']) : $packagePrice;
    $eventDate = $data['eventDate'] ?? '';
    $startTime = $data['startTime'] ?? '';
    $endTime = $data['endTime'] ?? '';
    $numberOfGuests = isset($data['numberOfGuests']) ? intval($data['numberOfGuests']) : 0;
    $eventType = $data['eventType'] ?? '';
    $otherEventType = $data['otherEventType'] ?? '';
    $paymentMethod = $data['paymentMethod'] ?? 'gcash';
    $paymentType = $data['paymentType'] ?? 'full';
    $overtimeHours = isset($data['overtimeHours']) ? floatval($data['overtimeHours']) : 0;
    $overtimeCharge = isset($data['overtimeCharge']) ? floatval($data['overtimeCharge']) : 0;
    $extraGuests = isset($data['extraGuests']) ? intval($data['extraGuests']) : 0;
    $extraGuestCharge = isset($data['extraGuestCharge']) ? floatval($data['extraGuestCharge']) : 0;
    $userId = $_SESSION['user_id'];
    $reserveType = 'online';
    
    // Generate booking reference
    $reference = 'TB' . date('YmdHis') . rand(100, 999);
    
    // Calculate total amount
    $totalAmount = $packagePrice + $overtimeCharge + $extraGuestCharge;
    
    // Calculate payment amounts
    $paidAmount = ($paymentType === 'downpayment') ? ($totalAmount * 0.5) : $totalAmount;
    $remainingBalance = $totalAmount - $paidAmount;
    
    // Log the values being inserted for debugging
    error_log('Inserting booking with data: ' . print_r([
        'overtime_hours' => $overtimeHours,
        'overtime_charge' => $overtimeCharge,
        'extra_guests' => $extraGuests,
        'extra_guest_charge' => $extraGuestCharge,
        'total_amount' => $totalAmount
    ], true));
    
    // Insert the booking
    $sql = "INSERT INTO event_bookings (
        id, user_id, package_name, base_price, package_price,
        overtime_hours, overtime_charge, extra_guests, extra_guest_charge,
        total_amount, paid_amount, remaining_balance, reservation_date, 
        start_time, end_time, number_of_guests, event_type, 
        payment_method, payment_type, booking_status, reserve_type, created_at
    ) VALUES (
        :reference, :user_id, :package, :base_price, :package_price,
        :overtime_hours, :overtime_charge, :extra_guests, :extra_guest_charge,
        :total_amount, :paid_amount, :remaining_balance, :reservation_date,
        :start_time, :end_time, :number_of_guests, :event_type,
        :payment_method, :payment_type, 'confirmed', :reserve_type, NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':reference' => $reference,
        ':user_id' => $userId,
        ':package' => $packageName,
        ':base_price' => $basePrice,
        ':package_price' => $packagePrice,
        ':overtime_hours' => $overtimeHours,
        ':overtime_charge' => $overtimeCharge,
        ':extra_guests' => $extraGuests,
        ':extra_guest_charge' => $extraGuestCharge,
        ':total_amount' => $totalAmount,
        ':paid_amount' => $paidAmount,
        ':remaining_balance' => $remainingBalance,
        ':reservation_date' => $eventDate,
        ':start_time' => $startTime,
        ':end_time' => $endTime,
        ':number_of_guests' => $numberOfGuests,
        ':event_type' => $eventType,
        ':payment_method' => $paymentMethod,
        ':payment_type' => $paymentType,
        ':reserve_type' => $reserveType
    ]);
    
    // Update package status to occupied
    $updatePackageStmt = $pdo->prepare("
        UPDATE event_packages 
        SET is_available = 0, status = 'Occupied'
        WHERE name = :package_name
    ");
    
    $updatePackageStmt->execute(['package_name' => $packageName]);
    
    // If everything is successful, commit the transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Booking completed successfully',
        'booking_reference' => $reference,
        'data' => [
            'packageName' => $packageName,
            'reservationDate' => $eventDate,
            'time' => $startTime . ' - ' . $endTime,
            'guests' => $numberOfGuests,
            'basePrice' => $basePrice,
            'totalAmount' => $totalAmount,
            'paymentType' => $paymentType,
            'paidAmount' => $paidAmount,
            'remainingBalance' => $remainingBalance,
            'overtimeHours' => $overtimeHours,
            'overtimeCharge' => $overtimeCharge,
            'extraGuests' => $extraGuests,
            'extraGuestCharge' => $extraGuestCharge
        ]
    ]);
    
} catch (Exception $e) {
    // If there's an error, rollback the transaction
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    // Log the error for debugging
    error_log('Booking Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to complete booking: ' . $e->getMessage(),
        'error' => [
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]
    ]);
}
?>
