<?php
    // Temporary debug - log raw request
    file_put_contents('debug_request.log', print_r([
        'POST' => $_POST,
        'GET' => $_GET,
        'RAW' => file_get_contents('php://input')
    ], true));
    
    require 'db.php';
    require_once 'email_functions.php';

    header('Content-Type: application/json');

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', 'booking_errors.log');

    // Debug: Log raw POST data
    error_log("=== START OF BOOKING REQUEST ===");
    error_log("Raw POST data: " . print_r($_POST, true));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = ['success' => false, 'message' => '', 'debug_info' => []];
        
        try {
            // Debug logging of all POST data
            error_log("=== BOOKING REQUEST DATA ===");
            error_log(json_encode($_POST, JSON_PRETTY_PRINT));

            // Log connection status
            if (!$con) {
                $error_msg = "Database connection failed: " . mysqli_connect_error();
                error_log($error_msg);
                throw new Exception($error_msg);
            }
            error_log("Database connection successful");

            // Validate required fields with specific messages
            $required_fields = [
                'firstName' => 'First Name',
                'lastName' => 'Last Name',
                'contact' => 'Contact Number',
                'email' => 'Email Address',
                'checkIn' => 'Check-in Date',
                'checkOut' => 'Check-out Date',
                'rooms' => 'Room Selection',
                'paymentMethod' => 'Payment Method',
                'paymentOption' => 'Payment Option'
            ];
            
            $missing_fields = [];
            foreach ($required_fields as $field => $label) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    $missing_fields[] = $label;
                    error_log("Missing required field: {$label}");
                }
            }
            
            if (!empty($missing_fields)) {
                throw new Exception("Missing required fields: " . implode(', ', $missing_fields));
            }

            // Validate rooms data format with specific message
            $rooms = json_decode($_POST['rooms'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error_msg = "Invalid room data format: " . json_last_error_msg() . ". Received: " . $_POST['rooms'];
                error_log($error_msg);
                throw new Exception($error_msg);
            }
            
            if (empty($rooms)) {
                throw new Exception("No rooms selected. Please select at least one room.");
            }

            error_log("Room data decoded: " . json_encode($rooms, JSON_PRETTY_PRINT));

            // Get the first room's data with validation
            $firstRoom = $rooms[0];
            if (!isset($firstRoom['id'])) {
                throw new Exception("Room ID is missing from room data");
            }
            $firstRoomTypeId = $firstRoom['id'];
            
            // Validate price data
            if (!isset($firstRoom['totalPrice']) && !isset($firstRoom['price'])) {
                throw new Exception("Room price information is missing");
            }
            $totalAmount = $firstRoom['totalPrice'] ?? ($firstRoom['price'] * ($firstRoom['nights'] ?? 1));
            
            if ($totalAmount <= 0) {
                throw new Exception("Invalid total amount: {$totalAmount}");
            }

            $totalGuests = $firstRoom['guestCount'] ?? 1;
            if ($totalGuests < 1) {
                throw new Exception("Invalid guest count: {$totalGuests}");
            }

            // Get room type information
            $room_type_sql = "SELECT room_type FROM room_types WHERE room_type_id = ?";
            $room_type_stmt = mysqli_prepare($con, $room_type_sql);
            if (!$room_type_stmt) {
                throw new Exception("Failed to prepare room type query: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($room_type_stmt, "i", $firstRoomTypeId);
            if (!mysqli_stmt_execute($room_type_stmt)) {
                throw new Exception("Failed to get room type: " . mysqli_stmt_error($room_type_stmt));
            }

            $room_type_result = mysqli_stmt_get_result($room_type_stmt);
            $room_type_data = mysqli_fetch_assoc($room_type_result);
            $room_type = $room_type_data['room_type'];

            // Validate dates
            try {
                $checkInDate = new DateTime($_POST['checkIn']);
                $checkOutDate = new DateTime($_POST['checkOut']);
            } catch (Exception $e) {
                throw new Exception("Invalid date format. Check-in: {$_POST['checkIn']}, Check-out: {$_POST['checkOut']}");
            }

            $nights = $checkOutDate->diff($checkInDate)->days;
            if ($nights < 1) {
                throw new Exception("Check-out date must be after check-in date. Nights: {$nights}");
            }

            error_log("Booking details validated successfully:");
            error_log("Nights: {$nights}");
            error_log("Total amount: {$totalAmount}");
            error_log("Total guests: {$totalGuests}");

            // Validate payment method
            if (!in_array($_POST['paymentMethod'], ['GCash', 'Cash', 'Credit Card', 'Bank Transfer'])) {
                throw new Exception("Invalid payment method: {$_POST['paymentMethod']}");
            }

            // Validate payment option
            if (!in_array($_POST['paymentOption'], ['full', 'downpayment'])) {
                throw new Exception("Invalid payment option: {$_POST['paymentOption']}");
            }

            $paymentOption = $_POST['paymentOption'];
            $discountType = $_POST['discountType'] ?? null;

            // Apply discount if selected
            $discountAmount = 0;
            $discountPercentage = 0;
            if (!empty($discountType)) {
                $discountPercentage = 10; // 10% discount
                $discountAmount = $totalAmount * ($discountPercentage / 100);
                $totalAmount -= $discountAmount;
            }

            error_log("Total amount before payment option: " . $totalAmount);

            // Calculate amount to be paid based on payment option
            $amountPaid = $totalAmount; // Default to full amount
            if ($paymentOption === 'downpayment') {
                $amountPaid = 1500; // Fixed downpayment amount
            }
            $remainingBalance = $totalAmount - $amountPaid;

            error_log("Payment details:");
            error_log("Payment option: " . $paymentOption);
            error_log("Total amount: " . $totalAmount);
            error_log("Amount paid: " . $amountPaid);
            error_log("Remaining balance: " . $remainingBalance);

            // Start transaction
            mysqli_begin_transaction($con);

            // Check if the room is available
            $check_room_sql = "SELECT available_rooms FROM rooms WHERE room_type_id = ?";
            $check_stmt = mysqli_prepare($con, $check_room_sql);
            if (!$check_stmt) {
                throw new Exception("Failed to check room availability: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($check_stmt, "i", $firstRoomTypeId);
            if (!mysqli_stmt_execute($check_stmt)) {
                throw new Exception("Failed to check room availability: " . mysqli_stmt_error($check_stmt));
            }

            $result = mysqli_stmt_get_result($check_stmt);
            $room_data = mysqli_fetch_assoc($result);

            if (!$room_data) {
                throw new Exception("Room not found");
            }

            if ($room_data['available_rooms'] < 1) {
                throw new Exception("Sorry, this room is not available");
            }

            // Insert into bookings table
            $sql = "INSERT INTO bookings (
                first_name,
                last_name,
                booking_type,
                email,
                contact,
                check_in,
                check_out,
                arrival_time,
                number_of_guests,
                room_type_id,
                payment_method,
                payment_option,
                total_amount,
                status,
                created_at,
                nights,
                downpayment_amount,
                remaining_balance
            ) VALUES (
                ?, ?, 'Walkin', ?, ?, ?, ?,
                CURRENT_TIME(),
                ?, ?, ?, ?, ?,
                'pending',
                CURRENT_TIMESTAMP,
                ?, ?, ?
            )";

            $stmt = mysqli_prepare($con, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare booking insert: " . mysqli_error($con));
            }

            // Calculate nights, downpayment, and remaining balance
            $nights = $checkOutDate->diff($checkInDate)->days;
            $downpayment_amount = $_POST['paymentOption'] === 'downpayment' ? 1500 : $totalAmount;
            $remaining_balance = $_POST['paymentOption'] === 'downpayment' ? ($totalAmount - 1500) : 0;

            // Bind parameters for the booking insertion
            mysqli_stmt_bind_param($stmt, "ssssssiisssddd",
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['email'],
                $_POST['contact'],
                $_POST['checkIn'],
                $_POST['checkOut'],
                $totalGuests,
                $firstRoomTypeId,
                $_POST['paymentMethod'],
                $_POST['paymentOption'],
                $totalAmount,
                $nights,
                $downpayment_amount,
                $remaining_balance
            );

            if (!mysqli_stmt_execute($stmt)) {
                error_log("Execute failed: " . mysqli_stmt_error($stmt));
                throw new Exception("Error inserting booking: " . mysqli_stmt_error($stmt));
            }

            $bookingId = mysqli_insert_id($con);

            // Update room availability
            $update_room_sql = "UPDATE rooms SET available_rooms = available_rooms - 1 WHERE room_type_id = ? AND available_rooms > 0";
            $update_room_stmt = mysqli_prepare($con, $update_room_sql);
            if (!$update_room_stmt) {
                throw new Exception("Failed to prepare room update: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($update_room_stmt, "i", $firstRoomTypeId);
            if (!mysqli_stmt_execute($update_room_stmt)) {
                throw new Exception("Failed to update room availability: " . mysqli_stmt_error($update_room_stmt));
            }

            if (mysqli_affected_rows($con) === 0) {
                throw new Exception("Failed to update room availability - room may no longer be available");
            }

            // Prepare booking details for email
            $bookingDetails = [
                'booking_id' => $bookingId,
                'check_in' => $_POST['checkIn'],
                'check_out' => $_POST['checkOut'],
                'room_type' => $room_type,
                'number_of_guests' => $totalGuests,
                'total_amount' => number_format($totalAmount, 2),
                'payment_status' => $_POST['paymentOption'] === 'full' ? 'Fully Paid' : 'Downpayment Required'
            ];

            // Send confirmation email
            $emailSent = sendBookingConfirmationEmail(
                $_POST['email'],
                $_POST['firstName'] . ' ' . $_POST['lastName'],
                $bookingDetails
            );

            if (!$emailSent) {
                error_log("Warning: Confirmation email could not be sent for booking ID: " . $bookingId);
            }

            // If everything is successful, commit the transaction
            mysqli_commit($con);
            
            $response['success'] = true;
            $response['message'] = 'Booking successful';
            $response['data'] = [
                'bookingId' => $bookingId,
                'bookingReference' => sprintf('BK%07d', $bookingId),
                'totalAmount' => number_format($totalAmount, 2),
                'amountPaid' => number_format($amountPaid, 2),
                'remainingBalance' => number_format($remainingBalance, 2),
                'paymentStatus' => $_POST['paymentOption'] === 'full' ? 'Fully Paid' : 'Downpayment Required',
                'checkIn' => $_POST['checkIn'],
                'checkOut' => $_POST['checkOut'],
                'nights' => $nights,
                'roomType' => $room_type
            ];
            $response['redirect'] = 'index.php?reservation';

        } catch (Exception $e) {
            error_log("=== BOOKING ERROR ===");
            error_log("Error message: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("POST data: " . json_encode($_POST, JSON_PRETTY_PRINT));
            
            mysqli_rollback($con);
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $response['debug_info'] = [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'post_data' => $_POST
            ];
        }

        // Log the final response
        error_log("=== BOOKING RESPONSE ===");
        error_log(json_encode($response, JSON_PRETTY_PRINT));

        echo json_encode($response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
    }

    mysqli_close($con);
    ?>  