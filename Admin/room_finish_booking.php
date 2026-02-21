<?php
// Start output buffering to prevent any output before headers
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Disable display_errors to prevent output during JSON response
ini_set('display_errors', 0);

try {
    require_once 'db_con.php';
    require_once 'includes/room_booking_mailer.php';

    header('Content-Type: application/json');

    // Get booking data from POST request
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }
    
    // Extract booking information
    $booking_ref = $data['booking_reference'] ?? '';
    $check_in = $data['check_in'] ?? '';
    $check_out = $data['check_out'] ?? '';
    $nights = intval($data['nights'] ?? 1);
    $adults = intval($data['adults'] ?? 0);
    $children = intval($data['children'] ?? 0);
    $payment_option = $data['payment_option'] ?? '';
    $payment_method = $data['payment_method'] ?? '';
    $total_amount = floatval($data['total_amount'] ?? 0);
    $remaining_balance = floatval($data['balance'] ?? 0);
    
    // Guest information
    $first_name = $data['first_name'] ?? '';
    $last_name = $data['last_name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    
    // Room information
    $rooms = $data['rooms'] ?? [];
    
    // Adult and children details
    $adult_details = $data['adults_details'] ?? [];
    $children_details = $data['children_details'] ?? [];
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Get user details if logged in
    $user_id = $_SESSION['user_id'] ?? 0;
    $first_name = $data['first_name'] ?? '';
    $last_name = $data['last_name'] ?? '';
    $email = $data['email'] ?? '';
    $contact = $data['contact'] ?? '';
    
    if ($user_id > 0) {
        $user_sql = "SELECT first_name, last_name, email, contact_number FROM userss WHERE id = :user_id";
        $user_stmt = $pdo->prepare($user_sql);
        $user_stmt->execute(['user_id' => $user_id]);
        $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            $first_name = $user_data['first_name'];
            $last_name = $user_data['last_name'];
            $email = $user_data['email'];
            $contact = $user_data['contact_number'];
        }
    }
    
    // 1. Insert into bookings table
    $booking_sql = "INSERT INTO bookings (
        booking_reference, user_id, first_name, last_name, email, contact, 
        check_in, check_out, nights, booking_type, number_of_guests, arrival_time, 
        room_quantity, payment_option, payment_method, total_amount, downpayment_amount, remaining_balance, 
        user_types, num_adults, num_children, status
    ) VALUES (
        :booking_reference, :user_id, :first_name, :last_name, :email, :contact,
        :check_in, :check_out, :nights, :booking_type, :number_of_guests, :arrival_time,
        :room_quantity, :payment_option, :payment_method, :total_amount, :downpayment_amount, :remaining_balance, :user_types, :num_adults, :num_children, :status
    )";
    
    $booking_stmt = $pdo->prepare($booking_sql);
    
    $status = 'pending';
    $booking_type = 'online';
    $arrival_time = '14:00:00';
    $room_quantity = array_sum(array_column($rooms, 'quantity'));
    $downpayment_amount = $payment_option === 'down_payment' ? 1500 : 0;
    // Debug: Log the payment option and calculated downpayment
    error_log("Payment option: " . $payment_option . ", Downpayment amount: " . $downpayment_amount);
    $user_type = 'customer';
        
    $booking_stmt->execute([
        ':booking_reference' => $booking_ref,
        ':user_id' => $user_id,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':contact' => $contact,
        ':check_in' => $check_in,
        ':check_out' => $check_out,
        ':nights' => $nights,
        ':booking_type' => $booking_type,
        ':number_of_guests' => $adults + $children,
        ':arrival_time' => $arrival_time,
        ':room_quantity' => $room_quantity,
        ':payment_option' => $payment_option,
        ':payment_method' => $payment_method,
        ':total_amount' => $total_amount,
        ':downpayment_amount' => $downpayment_amount,
        ':remaining_balance' => $remaining_balance,
        ':status' => $status,
        ':user_types' => $user_type,
        ':num_adults' => $adults,
        ':num_children' => $children,
    ]);
    
    $booking_id = $pdo->lastInsertId();
    
    // 2. Insert into booked_rooms table
    foreach ($rooms as $room) {
        // Get room_type_id from the room data if available, otherwise look it up
        $room_type_id = $room['room_type_id'] ?? null;
        $room_type_name = $room['name'] ?? $room['room_type'] ?? 'Unknown';
        
        // If room_type_id is not provided, try to get it from the room_types table
        if (empty($room_type_id)) {
            $room_type_sql = "SELECT room_type_id FROM room_types WHERE room_type = :room_type_name LIMIT 1";
            $room_type_stmt = $pdo->prepare($room_type_sql);
            $room_type_stmt->execute([':room_type_name' => $room_type_name]);
            $room_type = $room_type_stmt->fetch(PDO::FETCH_ASSOC);
            $room_type_id = $room_type ? $room_type['room_type_id'] : null;
        }
        
        // Get room number ID - check for different possible keys in the room data
        $room_number_id = $room['room_number_id'] ?? $room['room_number_fk_id'] ?? $room['room_number'] ?? null;
        
        // If we don't have a room number ID, find an available room of this type
        if (empty($room_number_id)) {
            $find_room_sql = "SELECT rn.room_number_id 
                             FROM room_numbers rn
                             LEFT JOIN booked_rooms br ON rn.room_number_id = br.room_number_fk_id
                             LEFT JOIN bookings b ON br.booking_id = b.booking_id
                             WHERE rn.room_type_id = :room_type_id 
                             AND rn.status = 'active'
                             AND (br.room_number_fk_id IS NULL 
                                  OR NOT (
                                      b.check_in <= :check_out 
                                      AND b.check_out >= :check_in
                                      AND b.status IN ('confirmed', 'checked_in')
                                  ))
                             LIMIT 1";
            
            $find_room_stmt = $pdo->prepare($find_room_sql);
            $find_room_stmt->execute([
                ':room_type_id' => $room_type_id,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);
            
            $available_room = $find_room_stmt->fetch(PDO::FETCH_ASSOC);
            $room_number_id = $available_room ? $available_room['room_number_id'] : null;
        }
        
        // If we still don't have a room number ID, throw an exception
        if (empty($room_number_id)) {
            throw new Exception("No available room found for type: " . $room_type_name);
        }
        
        // Verify the room exists and is available
        $check_room_sql = "SELECT room_number_id FROM room_numbers 
                          WHERE room_number_id = :room_number_id 
                          AND status = 'active' 
                          AND room_type_id = :room_type_id
                          LIMIT 1";
        
        $check_room_stmt = $pdo->prepare($check_room_sql);
        $check_room_stmt->execute([
            ':room_number_id' => $room_number_id,
            ':room_type_id' => $room_type_id
        ]);
        
        if (!$check_room_stmt->fetch()) {
            throw new Exception("Selected room is not available or doesn't match the room type");
        }
        
        // Now insert the booking
        $room_sql = "INSERT INTO booked_rooms (
            booking_id, room_number_fk_id, room_type_id, room_type_name, 
            price, created_at
        ) VALUES (
            :booking_id, :room_number_fk_id, :room_type_id, :room_type_name,
            :price, NOW()
        )";
        
        $room_stmt = $pdo->prepare($room_sql);
        $room_stmt->execute([
            ':booking_id' => $booking_id,
            ':room_number_fk_id' => $room_number_id,
            ':room_type_id' => $room_type_id,
            ':room_type_name' => $room_type_name,
            ':price' => $room['price'] ?? 0
        ]);
        
    }
    

    // 4. Insert into guest_names table
    foreach ($adult_details as $adult) {
        $guest_sql = "INSERT INTO guest_names (
            booking_id, first_name, last_name, guest_type, age, created_at
        ) VALUES (
            :booking_id, :first_name, :last_name, :guest_type, :age, NOW()
        )";
        
        $guest_stmt = $pdo->prepare($guest_sql);
        $guest_stmt->execute([
            ':booking_id' => $booking_id,
            ':first_name' => $adult['firstName'],
            ':last_name' => $adult['lastName'],
            ':guest_type' => $adult['userType'] ?? 'regular',
            ':age' => $adult['age'] ?? null
        ]);
    }
    
    foreach ($children_details as $child) {
        $guest_sql = "INSERT INTO guest_names (
            booking_id, first_name, last_name, guest_type, age, created_at
        ) VALUES (
            :booking_id, :first_name, :last_name, :guest_type, :age, NOW()
        )";
        
        $guest_stmt = $pdo->prepare($guest_sql);
        $guest_stmt->execute([
            ':booking_id' => $booking_id,
            ':first_name' => $child['firstName'],
            ':last_name' => $child['lastName'],
            ':guest_type' => 'child',
            ':age' => $child['age'] ?? null
        ]);
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Insert into booking_check_inout table
    $check_inout_sql = "INSERT INTO booking_check_inout (
        booking_fk_id, check_in, check_out
    ) VALUES (
        :booking_fk_id, :check_in, :check_out
    )";
    
    $check_inout_stmt = $pdo->prepare($check_inout_sql);
    $check_inout_stmt->execute([
        ':booking_fk_id' => $booking_id,
        ':check_in' => $check_in,
        ':check_out' => $check_out
    ]);
    
    // Insert notification for the booking
    $notification_sql = "INSERT INTO notifications (
        user_id, title, message, type, is_read, created_at
    ) VALUES (
        :user_id, :title, :message, :type, 0, NOW()
    )";
    
    $notification_stmt = $pdo->prepare($notification_sql);
    $notification_stmt->execute([
        ':user_id' => $user_id > 0 ? $user_id : null,
        ':title' => 'Booking Confirmed',
        ':message' => "Your booking #$booking_ref has been confirmed. Check-in date: $check_in. Total amount: ₱" . number_format($total_amount, 2),
        ':type' => 'Room Booking'
    ]);
    
    try {
        // Calculate amount paid based on payment option
        $amountPaid = 0;
        if ($payment_option === 'down_payment') {
            $amountPaid = 1500;
        } elseif ($payment_option === 'full_payment') {
            $amountPaid = $total_amount;
        } elseif ($payment_option === 'custom_payment') {
            $amountPaid = $total_amount - $remaining_balance;
        }
        
        // Ensure remaining balance is calculated correctly
        if ($remaining_balance === 0 && $payment_option !== 'full_payment') {
            $remaining_balance = $total_amount - $amountPaid;
        }
        
        // Prepare booking data for email
        $bookingData = [
            'booking_reference' => $booking_ref,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'nights' => $nights,
            'total_amount' => $total_amount,
            'amount_paid' => $amountPaid,
            'remaining_balance' => $remaining_balance,
            'payment_option' => $payment_option,
            'payment_method' => $payment_method,
            'rooms' => $rooms,
            'adults' => $adult_details,
            'children' => $children_details,
            'email' => $email,
            'phone' => $contact
        ];
        
        // Debug logging
        error_log("Email Data: " . json_encode([
            'payment_option' => $payment_option,
            'total_amount' => $total_amount,
            'amount_paid' => $amountPaid,
            'remaining_balance' => $remaining_balance
        ]));
        
        // Send booking confirmation email
        $bookingMailer = new RoomBookingMailer();
        $emailSent = $bookingMailer->sendBookingConfirmation($bookingData);
        
        if (!$emailSent) {
            error_log("Failed to send booking confirmation email for booking ID: " . $booking_id);
        }
    } catch (Exception $e) {
        // Log email sending error but don't fail the booking
        error_log("Error sending booking confirmation email: " . $e->getMessage());
    }
    
    // Return success response
    $response = [
        'success' => true,
        'message' => 'Booking completed successfully',
        'booking_id' => $booking_id,
        'booking_reference' => $booking_ref
    ];
    
    if (isset($emailSent) && !$emailSent) {
        $response['email_sent'] = false;
        $response['message'] .= ' (but email notification failed)';
    } else {
        $response['email_sent'] = true;
    }
    
    // Clear any previous output and set JSON header
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log error
    error_log("Booking insertion failed: " . $e->getMessage());
    
    // Clear any previous output and set JSON header
    ob_clean();
    header('Content-Type: application/json');
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Failed to complete booking: ' . $e->getMessage()
    ]);
}

// Flush output buffer and turn off output buffering
ob_end_flush();
?>