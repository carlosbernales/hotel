<?php
require 'database.php';

header('Content-Type: application/json');

try {
    // Verify the request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // Retrieve and sanitize POST data
    $package_name = filter_input(INPUT_POST, 'packageName', FILTER_SANITIZE_STRING);
    $package_price = filter_input(INPUT_POST, 'packagePrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $reservation_date = filter_input(INPUT_POST, 'reservationDate', FILTER_SANITIZE_STRING);
    $capacity = filter_input(INPUT_POST, 'reservationCapacity', FILTER_SANITIZE_NUMBER_INT);
    $start_time = filter_input(INPUT_POST, 'reservationStartTime', FILTER_SANITIZE_STRING);
    $end_time = filter_input(INPUT_POST, 'reservationEndTime', FILTER_SANITIZE_STRING);
    $payment_method = filter_input(INPUT_POST, 'paymentMethod', FILTER_SANITIZE_STRING);
    $payment_type = filter_input(INPUT_POST, 'paymentType', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (!$package_name || !$package_price || !$reservation_date || !$capacity || 
        !$start_time || !$end_time || !$payment_method || !$payment_type) {
        throw new Exception('All fields are required.');
    }

    // Validate date and time
    $reservation_datetime = new DateTime($reservation_date);
    $today = new DateTime();
    
    if ($reservation_datetime < $today) {
        throw new Exception('Reservation date cannot be in the past.');
    }

    // Convert times to DateTime objects for comparison
    $start_datetime = DateTime::createFromFormat('H:i', $start_time);
    $end_datetime = DateTime::createFromFormat('H:i', $end_time);
    
    if ($end_datetime <= $start_datetime) {
        throw new Exception('End time must be after start time.');
    }

    // Calculate duration in hours
    $duration = ($end_datetime->getTimestamp() - $start_datetime->getTimestamp()) / 3600;

    // Check for existing bookings in the same time slot
    $check_query = "SELECT COUNT(*) FROM event_bookings 
                   WHERE reservation_date = :date 
                   AND ((start_time BETWEEN :start AND :end) 
                   OR (end_time BETWEEN :start AND :end)
                   OR (:start BETWEEN start_time AND end_time))";
    
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute([
        ':date' => $reservation_date,
        ':start' => $start_time,
        ':end' => $end_time
    ]);
    
    if ($check_stmt->fetchColumn() > 0) {
        throw new Exception('This time slot is already booked. Please choose another time.');
    }

    // Calculate payment amounts
    $total_amount = floatval($package_price);
    $paid_amount = $payment_type === 'downpayment' ? ($total_amount * 0.5) : $total_amount;
    $remaining_balance = $total_amount - $paid_amount;

    // Begin transaction
    $pdo->beginTransaction();

    // Insert booking record
    $insert_query = "INSERT INTO event_bookings (
        user_id, package_name, package_price, reservation_date, 
        capacity, start_time, end_time, duration_hours,
        payment_method, payment_type, total_amount, 
        paid_amount, remaining_balance, status, created_at
    ) VALUES (
        :user_id, :package_name, :package_price, :reservation_date,
        :capacity, :start_time, :end_time, :duration_hours,
        :payment_method, :payment_type, :total_amount,
        :paid_amount, :remaining_balance, 'pending', NOW()
    )";

    $stmt = $pdo->prepare($insert_query);
    $stmt->execute([
        ':user_id' => $_SESSION['userid'],
        ':package_name' => $package_name,
        ':package_price' => $package_price,
        ':reservation_date' => $reservation_date,
        ':capacity' => $capacity,
        ':start_time' => $start_time,
        ':end_time' => $end_time,
        ':duration_hours' => $duration,
        ':payment_method' => $payment_method,
        ':payment_type' => $payment_type,
        ':total_amount' => $total_amount,
        ':paid_amount' => $paid_amount,
        ':remaining_balance' => $remaining_balance
    ]);

    // Create a notification for admin
    $notification_query = "INSERT INTO notifications (
        user_id, type, message, created_at, status
    ) VALUES (
        :user_id, 'event_booking', 
        CONCAT('New event booking: ', :package_name, ' for ', :reservation_date),
        NOW(), 'unread'
    )";

    $notify_stmt = $pdo->prepare($notification_query);
    $notify_stmt->execute([
        ':user_id' => $_SESSION['userid'],
        ':package_name' => $package_name,
        ':reservation_date' => $reservation_date
    ]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Booking successful!',
        'booking_details' => [
            'package' => $package_name,
            'date' => $reservation_date,
            'time' => "$start_time - $end_time",
            'total_amount' => $total_amount,
            'paid_amount' => $paid_amount,
            'remaining_balance' => $remaining_balance
        ]
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Event booking error: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
