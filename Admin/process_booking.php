<?php
require 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Log raw POST data
error_log("Raw POST data: " . file_get_contents('php://input'));
error_log("POST array: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Log the received data
        error_log("Received POST data: " . print_r($_POST, true));
        error_log("Received rooms data: " . $_POST['rooms']);

        $con->begin_transaction();

        // Get booking data
        $firstName = $con->real_escape_string($_POST['firstName']);
        $lastName = $con->real_escape_string($_POST['lastName']);
        $contact = $con->real_escape_string($_POST['contact']);
        $email = $con->real_escape_string($_POST['email']);
        $checkIn = $con->real_escape_string($_POST['checkIn']);
        $checkOut = $con->real_escape_string($_POST['checkOut']);
        $paymentMethod = $con->real_escape_string($_POST['paymentMethod'] ?? 'Cash');
        $paymentOption = $con->real_escape_string($_POST['paymentOption'] ?? 'full');
        $discountType = $con->real_escape_string($_POST['discountType'] ?? '');
        
        // Debug discount type
        error_log("Discount Type received: " . $discountType);
        
        // Decode rooms data
        $rooms = json_decode($_POST['rooms'], true);
        error_log("Decoded rooms data: " . print_r($rooms, true));

        if (!$rooms) {
            throw new Exception("No rooms selected or invalid room data");
        }

        // Calculate nights
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $nights = $checkOutDate->diff($checkInDate)->days;

        error_log("Calculated nights: " . $nights);

        // Calculate total amount and total guests
        $totalAmount = 0;
        $totalGuests = 0;
        foreach ($rooms as $room) {
            $totalAmount += floatval($room['totalPrice']);
            $totalGuests += intval($room['guestCount']);
        }

        error_log("Total amount: " . $totalAmount);
        error_log("Total guests: " . $totalGuests);

        // Get discount information
        $discount_type = $_POST['discountType'] ?? '';
        $total_amount = 0;

        // Calculate total amount from rooms
        foreach ($rooms as $room) {
            $total_amount += $room['totalPrice'];
        }

        // Apply 10% discount if a discount type is selected
        $discount_amount = 0;
        $final_total = $total_amount;
        if (!empty($discount_type)) {
            $discount_amount = $total_amount * 0.1; // 10% discount
            $final_total = $total_amount - $discount_amount;
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
            payment_method,
            payment_option,
            total_amount,
            status,
            created_at,
            nights,
            downpayment_amount,
            discount_type
        ) VALUES (
            ?, ?, 'Walkin', ?, ?, ?, ?, 
            CURRENT_TIME(), 
            ?, ?, ?, ?, 
            'pending',
            CURRENT_TIMESTAMP,
            ?,
            ?,
            ?
        )";

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $downpaymentAmount = ($paymentOption === 'downpayment') ? ($final_total * 0.5) : $final_total;

        error_log("About to execute booking insert with params: " . print_r([
            $firstName, $lastName, $email, $contact,
            $checkIn, $checkOut, $totalGuests,
            $paymentMethod, $paymentOption, $final_total,
            $nights, $downpaymentAmount, $discountType
        ], true));

        $stmt->bind_param("ssssssissdids",
            $firstName,
            $lastName,
            $email,
            $contact,
            $checkIn,
            $checkOut,
            $totalGuests,
            $paymentMethod,
            $paymentOption,
            $final_total,
            $nights,
            $downpaymentAmount,
            $discountType
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting booking: " . $stmt->error);
        }

        $bookingId = $con->insert_id;
        error_log("Booking inserted with ID: " . $bookingId);

        // Create notification for the new booking
        $notificationTitle = "New Room Booking";
        $notificationMessage = "New room booking from $firstName $lastName for check-in on " . date('M j, Y', strtotime($checkIn));
        $customerName = "$firstName $lastName";
        
        $notification_sql = "INSERT INTO notifications (title, message, type, is_read, created_at) 
                           VALUES (?, ?, 'booking', 0, CURRENT_TIMESTAMP)";
        $stmt_notification = $con->prepare($notification_sql);
        if (!$stmt_notification) {
            throw new Exception("Prepare failed for notification: " . $con->error);
        }

        $stmt_notification->bind_param("ss", $notificationTitle, $notificationMessage);
        if (!$stmt_notification->execute()) {
            throw new Exception("Error creating notification: " . $stmt_notification->error);
        }

        // Process each room booking
        foreach ($rooms as $room) {
            error_log("Processing room: " . print_r($room, true));

            // Calculate extra guest fee
            $extraGuests = max(0, intval($room['guestCount']) - intval($room['capacity']));
            $extraGuestFee = $extraGuests * 1000 * $nights;

            // Calculate subtotal
            $basePrice = floatval($room['price']);
            $subtotal = $basePrice * $nights + $extraGuestFee;

            error_log("Room calculations: extraGuests=$extraGuests, extraGuestFee=$extraGuestFee, basePrice=$basePrice, subtotal=$subtotal");

            // Insert into room_bookings table
            $sql = "INSERT INTO room_bookings (
                booking_id,
                room_type_id,
                room_name,
                room_price,
                room_quantity,
                number_of_days,
                subtotal,
                created_at,
                guest_count,
                extra_guest_fee,
                number_of_nights
            ) VALUES (?, ?, ?, ?, 1, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?)";

            $stmt = $con->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed for room booking: " . $con->error);
            }

            error_log("About to execute room booking insert with params: " . print_r([
                $bookingId, $room['id'], $room['type'], $basePrice,
                $nights, $subtotal, $room['guestCount'], $extraGuestFee, $nights
            ], true));

            $stmt->bind_param("iisdiddid",
                $bookingId,
                $room['id'],
                $room['type'],
                $basePrice,
                $nights,
                $subtotal,
                $room['guestCount'],
                $extraGuestFee,
                $nights
            );

            if (!$stmt->execute()) {
                throw new Exception("Error inserting room booking: " . $stmt->error);
            }

            // Insert guest names
            if (!empty($room['guestNames'])) {
                $sql = "INSERT INTO guest_names (booking_id, guest_name, created_at) 
                       VALUES (?, ?, CURRENT_TIMESTAMP)";
                $guestStmt = $con->prepare($sql);
                
                foreach ($room['guestNames'] as $guestName) {
                    $guestStmt->bind_param("is", $bookingId, $guestName);
                    if (!$guestStmt->execute()) {
                        throw new Exception("Error inserting guest name: " . $guestStmt->error);
                    }
                }
            }

            // Update room availability
            $sql = "UPDATE rooms 
                   SET available_rooms = available_rooms - 1 
                   WHERE room_type_id = ? AND available_rooms > 0";
            
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed for room update: " . $con->error);
            }

            $stmt->bind_param("i", $room['id']);
            if (!$stmt->execute()) {
                throw new Exception("Error updating room availability: " . $stmt->error);
            }

            if ($stmt->affected_rows === 0) {
                throw new Exception("Room {$room['type']} is no longer available");
            }
        }

        $con->commit();
        
        $response['success'] = true;
        $response['message'] = 'Booking successful';
        $response['bookingId'] = $bookingId;

    } catch (Exception $e) {
        $con->rollback();
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        error_log("Booking error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
    }

    echo json_encode($response);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$con->close();
?>