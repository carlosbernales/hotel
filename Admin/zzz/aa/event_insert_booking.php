<?php
ob_start();
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db_con.php';
require_once 'includes/Mailer.php';

header('Content-Type: application/json');

function jsonResponse($success, $message = '', $data = []) {
    while (ob_get_level()) ob_end_clean();
    echo json_encode([
        'success' => (bool)$success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Check if table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'event_bookings'")->rowCount() > 0;
    if (!$tableExists) {
        throw new Exception('Event bookings table does not exist');
    }

    // Check database connection
    $pdo->query("SELECT 1")->execute();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Invalid request method');
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['booking_data'])) {
        jsonResponse(false, 'No booking data received');
    }

    $booking = $input['booking_data'];
    
    // Get user details from database
    $userName = 'Guest';
    $userEmail = '';
    if (isset($_SESSION['user_id'])) {
        $userStmt = $pdo->prepare("SELECT first_name, last_name, email FROM userss WHERE id = :user_id");
        $userStmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userName = trim($user['first_name'] . ' ' . $user['last_name']);
            $userEmail = $user['email'];
        }
    }

    // Generate a unique booking reference to check for duplicates
    $bookingRef = 'EVT-' . date('Ymd') . '-' . strtoupper(uniqid());
    
    // Check if this exact booking already exists in the database
    $checkStmt = $pdo->prepare("SELECT id FROM event_bookings WHERE booking_refId = :ref");
    $checkStmt->execute([':ref' => $bookingRef]);
    
    if ($checkStmt->rowCount() > 0) {
        jsonResponse(true, 'Booking already saved');
    }

    // ───── DATA ─────
    $totalAmount   = (float)($booking['package_price'] ?? 0);
    $paymentType   = $booking['payment_option'] ?? 'full_payment';
    $paidAmount    = $paymentType === 'full_payment' ? $totalAmount : $totalAmount * 0.5;
    $balance       = $totalAmount - $paidAmount;

    $start = new DateTime($booking['event_date'] . ' ' . ($booking['event_time'] ?? '00:00'));
    $end   = clone $start;
    $end->add(new DateInterval('PT4H'));

    // Booking reference is now generated at the start to check for duplicates

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO event_bookings (
            booking_refId,
            user_id,
            package_id,
            customer_name,
            package_name,
            package_price,
            total_amount,
            paid_amount,
            remaining_balance,
            event_type,
            place,
            date_time_start,
            date_time_end,
            number_of_guests,
            payment_method,
            payment_type,
            booking_status,
            reserve_type
        ) VALUES (
            :ref,
            :user,
            :package_id,
            :customer,
            :package,
            :price,
            :total,
            :paid,
            :balance,
            :type,
            :place,
            :start,
            :end,
            :guests,
            :method,
            :payment,
            'pending',
            :reserve
        )
    ");

    $stmt->execute([
        ':ref'        => $bookingRef,
        ':user'       => $_SESSION['user_id'] ?? 0,
        ':package_id' => $booking['package_id'] ?? 0, // Add package_id from booking data
        ':customer'   => $userName,
        ':package'    => $booking['package_name'] ?? 'Custom Package',
        ':price'      => $totalAmount,
        ':total'      => $totalAmount,
        ':paid'       => $paidAmount,
        ':balance'    => $balance,
        ':type'       => $booking['event_type'] ?? 'Event',
        ':place'      => $booking['event_place'] ?? 'Main Hall',
        ':start'      => $start->format('Y-m-d H:i:s'),
        ':end'        => $end->format('Y-m-d H:i:s'),
        ':guests'     => (int)($booking['guest_count'] ?? 1),
        ':method'     => $booking['payment_method'] ?? 'paymongo',
        ':payment'    => $paymentType,
        ':reserve'    => $booking['reserve_type'] ?? 'event'
    ]);
    
    if ($stmt->errorCode() !== '00000') {
        error_log('ERROR: Database insert failed');
        error_log('PDO Error info: ' . json_encode($stmt->errorInfo()));
        throw new Exception('Failed to insert booking record');
    }

    error_log('Database insert successful. Last insert ID: ' . $pdo->lastInsertId());
    $eventBookingId = $pdo->lastInsertId();

    // Send booking confirmation email
    $emailStatus = 'Email not sent';
    try {
        if (!empty($userEmail)) {
            $mailer = new Mailer();
            
            // Prepare booking data for email using the existing sendEventBookingConfirmation method
            $emailBookingData = [
                'booking_refId' => $bookingRef,
                'first_name' => $user['first_name'] ?? 'Guest',
                'last_name' => $user['last_name'] ?? '',
                'email' => $userEmail,
                'package_name' => $booking['package_name'] ?? 'Event Package',
                'event_date' => $start->format('F j, Y'),
                'start_time' => $start->format('h:i A'),
                'end_time' => $end->format('h:i A'),
                'number_of_guests' => (int)($booking['guest_count'] ?? 1),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_balance' => $balance,
                'payment_method' => $booking['payment_method'] ?? 'paymongo',
                'payment_type' => $paymentType
            ];
            
            $emailSent = $mailer->sendEventBookingConfirmation($emailBookingData);
            $emailStatus = $emailSent ? 'Email sent successfully' : 'Email sending failed';
            error_log('Email status: ' . $emailStatus);
        } else {
            $emailStatus = 'No user email found';
            error_log('No user email found for sending confirmation');
        }
    } catch (Exception $e) {
        $emailStatus = 'Email error: ' . $e->getMessage();
        error_log('Email sending error: ' . $e->getMessage());
    }

    // Don't set session flag as it can prevent legitimate duplicate bookings
    // Instead, we rely on the unique booking_refId to prevent duplicates
    $pdo->commit();

    // Insert notification for event booking
    $notification_sql = "INSERT INTO notifications (
        user_id, event_fk_id, title, message, type, is_read, created_at
    ) VALUES (
        :user_id, :event_fk_id, :title, :message, :type, 0, NOW()
    )";
    
    $notification_stmt = $pdo->prepare($notification_sql);
    $packageName = $booking['package_name'] ?? 'Event';
    $notification_stmt->execute([
        ':user_id' => $_SESSION['user_id'] ?? null,
        ':event_fk_id' => $eventBookingId,
        ':title' => 'Event Booking Pending',
        ':message' => "Your booking for event '{$packageName}' has been confirmed. Booking reference: {$bookingRef}.",
        ':type' => 'Event Booking'
    ]);

    jsonResponse(true, 'Booking saved successfully', [
        'booking_ref' => $bookingRef,
        'email_status' => $emailStatus,
        'booking_id' => $eventBookingId
    ]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log detailed error information
    error_log('Booking Error: ' . $e->getMessage());
    error_log('Error Type: ' . get_class($e));
    error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
    error_log('Stack Trace: ' . $e->getTraceAsString());
    
    // Return detailed error in development, generic message in production
    $errorMessage = 'Failed to save booking. Please try again.';
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        $errorMessage .= ' Error: ' . $e->getMessage();
    }
    
    jsonResponse(false, $errorMessage);
}
