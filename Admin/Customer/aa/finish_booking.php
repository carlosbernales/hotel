<?php
session_start();
require 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'booking_id' => null,
    'redirect_url' => 'rooms.php' // Set default redirect URL
];

// Function to clean input data
function cleanInput($data) {
    if (is_null($data)) {
        return '';
    }
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    if (is_bool($data)) {
        return $data ? 1 : 0;
    }
    if (is_numeric($data)) {
        return $data + 0; // Convert to int or float
    }
    if (!is_string($data)) {
        return $data;
    }
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

try {
    // Get booking data from POST or session
    $bookingData = [];
    
    // First try to get from POST data
    if (!empty($_POST)) {
        $bookingData = [];
        foreach ($_POST as $key => $value) {
            $bookingData[$key] = cleanInput($value);
        }
        
        // Handle JSON-encoded room_items if present
        if (!empty($bookingData['room_items']) && is_string($bookingData['room_items'])) {
            $decodedItems = json_decode($bookingData['room_items'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $bookingData['room_items'] = $decodedItems;
            } else {
                $bookingData['room_items'] = [];
            }
        }
    } 
    // Fall back to session if no POST data
    elseif (!empty($_SESSION['booking_data'])) {
        $bookingData = $_SESSION['booking_data'];
    } else {
        throw new Exception('No booking data found. Please start a new booking.');
    }

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Generate a unique booking reference
        $bookingReference = 'BK-' . strtoupper(uniqid());
        
        // 1. Prepare booking data
        $userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $numberOfGuests = (int)($bookingData['num_adults'] ?? 1) + (int)($bookingData['num_children'] ?? 0);
        $nights = (int)($bookingData['num_nights'] ?? 1);
        $totalAmount = (float)($bookingData['total_amount'] ?? 0);
        $paymentAmount = (float)($bookingData['payment_amount'] ?? $totalAmount);
        $remainingBalance = (float)($bookingData['remaining_balance'] ?? ($totalAmount - $paymentAmount));
        
        // 1.1 Get user/guest details
        if ($userId) {
            // For logged-in users, fetch from database
            $userStmt = $pdo->prepare("SELECT first_name, last_name, email, contact_number FROM userss WHERE id = :user_id");
            $userStmt->execute(['user_id' => $userId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            // Use user details from database
            $firstName = $user['first_name'];
            $lastName = $user['last_name'];
            $email = $user['email'];
            $contact = $user['contact_number'];
        } else {
            // For guest users, use details from booking data
            $firstName = $bookingData['first_name'] ?? '';
            $lastName = $bookingData['last_name'] ?? '';
            $email = $bookingData['email'] ?? '';
            $contact = $bookingData['contact_number'] ?? ($bookingData['phone'] ?? ($bookingData['contact'] ?? ''));
            
            // Validate required guest information
            if (empty($email)) {
                throw new Exception('Email is required for guest bookings');
            }
            if (empty($contact)) {
                throw new Exception('Contact number is required for guest bookings');
            }
            
            // Additional validation for email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Please enter a valid email address');
            }
        }

        // Function to get room name by ID
        function getRoomNameById($pdo, $roomTypeId) {
            try {
                $stmt = $pdo->prepare("SELECT room_type FROM room_types WHERE room_type_id = ?");
                $stmt->execute([$roomTypeId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['room_type'] : 'Standard Room';
            } catch (Exception $e) {
                error_log('Error fetching room name: ' . $e->getMessage());
                return 'Standard Room';
            }
        }

        // Get room_type_id and room name from room_items if available
        $roomTypeId = null;
        $roomName = 'Standard Room';
        
        // Debug log the entire booking data to see what we're working with
        error_log('Booking data in finish_booking: ' . print_r($bookingData, true));
        
        // Check for room_type_id directly in booking data first
        if (!empty($bookingData['room_type_id'])) {
            $roomTypeId = (int)$bookingData['room_type_id'];
            error_log('Found room_type_id in booking data: ' . $roomTypeId);
        } 
        // Check if we have room_items in the booking data
        elseif (!empty($bookingData['room_items'])) {
            $roomItems = is_string($bookingData['room_items']) ? json_decode($bookingData['room_items'], true) : $bookingData['room_items'];
            
            // Debug log the room items
            error_log('Room items in finish_booking: ' . print_r($roomItems, true));
            
            if (is_array($roomItems) && !empty($roomItems)) {
                // Get the first room item if roomItems is a list, otherwise use the whole array
                $firstRoom = is_array($roomItems[0] ?? null) ? $roomItems[0] : $roomItems;
                
                // Try different possible keys for room type ID
                $roomTypeId = isset($firstRoom['room_type_id']) ? (int)$firstRoom['room_type_id'] : 
                              (isset($firstRoom['id']) ? (int)$firstRoom['id'] : null);
                
                // If we still don't have an ID, check if there's a room_id
                if (empty($roomTypeId) && isset($firstRoom['room_id'])) {
                    $roomTypeId = (int)$firstRoom['room_id'];
                }
                
                // If we have a room type ID, try to get the room name
                if ($roomTypeId) {
                    $roomName = $firstRoom['room_type'] ?? $firstRoom['name'] ?? getRoomNameById($pdo, $roomTypeId);
                    error_log('Extracted room_type_id from room_items: ' . $roomTypeId);
                }
            }
        }
        
        // As a last resort, check if we have a room_id in the booking data
        if (empty($roomTypeId) && !empty($bookingData['room_id'])) {
            $roomTypeId = (int)$bookingData['room_id'];
            error_log('Using room_id from booking data as room_type_id: ' . $roomTypeId);
        }
        
        // If we still don't have a room type ID, log an error but continue
        if (empty($roomTypeId)) {
            error_log('WARNING: Could not determine room_type_id from booking data');
        }
        
        // Debug log the final room type ID
        error_log('Final room_type_id: ' . ($roomTypeId ?? 'null'));

        // 2. Insert into bookings table
        $stmt = $pdo->prepare("
            INSERT INTO bookings (
                booking_reference, user_id, first_name, last_name, email, contact, booking_type, 
                check_in, check_out, number_of_guests, room_quantity, room_type_id, 
                num_adults, num_children, extra_bed, payment_method, payment_option, 
                nights, total_amount, downpayment_amount, remaining_balance, status, 
                discount_type, discount_amount, discount_percentage, created_at
            ) VALUES (
                :booking_reference, :user_id, :first_name, :last_name, :email, :contact,
                'Online', :check_in, :check_out, :number_of_guests, :room_quantity, :room_type_id,
                :num_adults, :num_children, :extra_bed, :payment_method, :payment_option,
                :nights, :total_amount, :downpayment_amount, :remaining_balance, 'pending',
                :discount_type, :discount_amount, :discount_percentage, NOW()
            )"
        );

        // Bind parameters
        $stmt->execute([
            'booking_reference' => $bookingReference,
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'contact' => $contact,
            'check_in' => $bookingData['check_in'] ?? null,
            'check_out' => $bookingData['check_out'] ?? null,
            'number_of_guests' => $numberOfGuests,
            'room_quantity' => $bookingData['room_quantity'] ?? 1,
            'room_type_id' => $roomTypeId,
            'num_adults' => $bookingData['num_adults'] ?? 1,
            'num_children' => $bookingData['num_children'] ?? 0,
            'extra_bed' => $bookingData['extra_bed'] ?? 0,
            'payment_method' => $bookingData['payment_method'] ?? 'Cash',
            'payment_option' => $bookingData['payment_option'] ?? 'Full Payment',
            'nights' => $nights,
            'total_amount' => $totalAmount,
            'downpayment_amount' => $paymentAmount,
            'remaining_balance' => $remainingBalance,
            'discount_type' => $bookingData['discount_type'] ?? null,
            'discount_amount' => (float)($bookingData['discount_amount'] ?? 0),
            'discount_percentage' => (float)($bookingData['discount_percentage'] ?? 0)
        ]);

        $bookingId = $pdo->lastInsertId();

        // 3. Insert guest information
        try {
            $guestStmt = $pdo->prepare("
                INSERT INTO booking_guests (
                    booking_id, guest_name, guest_type, guest_age, id_proof_image, created_at
                ) VALUES (
                    :booking_id, :guest_name, :guest_type, :guest_age, :id_proof_image, NOW()
                )"
            );

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
            if (!empty($bookingData['guest_names']) && is_array($bookingData['guest_names'])) {
                foreach ($bookingData['guest_names'] as $index => $guestName) {
                    if (!empty($guestName)) {
                        $guestStmt->execute([
                            'booking_id' => $bookingId,
                            'guest_name' => $guestName,
                            'guest_type' => 'additional_guest',
                            'guest_age' => $bookingData['guest_ages'][$index] ?? null,
                            'id_proof_image' => $bookingData['guest_id_proofs'][$index] ?? null
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Log error but don't fail the booking
            error_log('Error saving guest info: ' . $e->getMessage());
        }

        // Room items are now handled in the main bookings table


        // 5. Insert payment record (if payments table exists)
        try {
            $tableExists = $pdo->query("SHOW TABLES LIKE 'payments'")->rowCount() > 0;
            
            if ($tableExists && $paymentAmount > 0) {
                $paymentStmt = $pdo->prepare("
                    INSERT INTO payments (
                        booking_id, amount, payment_method, payment_status, payment_date, reference_number, created_at
                    ) VALUES (
                        :booking_id, :amount, :payment_method, 'completed', NOW(), :reference_number, NOW()
                    )"
                );

                $paymentStmt->execute([
                    'booking_id' => $bookingId,
                    'amount' => $paymentAmount,
                    'payment_method' => $bookingParams['payment_method'],
                    'reference_number' => 'PAY-' . strtoupper(uniqid())
                ]);
            }
        } catch (Exception $e) {
            // Log error but don't fail the booking
            error_log('Error saving payment: ' . $e->getMessage());
        }
        
        // Commit transaction
        $pdo->commit();

        // Set success response with redirect URL
        $response = [
            'success' => true,
            'message' => 'Booking completed successfully',
            'booking_id' => $bookingId,
            'reference_number' => $bookingReference,
            'redirect_url' => 'booking_confirmation.php?id=' . $bookingId
        ];

        // Prepare booking data for email
        $emailData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'check_in' => $bookingData['check_in'] ?? '',
            'check_out' => $bookingData['check_out'] ?? '',
            'nights' => $nights,
            'number_of_guests' => $numberOfGuests,
            'payment_method' => $bookingData['payment_method'] ?? 'Cash',
            'payment_option' => $bookingData['payment_option'] ?? 'Full Payment',
            'total_amount' => $totalAmount,
            'downpayment_amount' => $paymentAmount,
            'remaining_balance' => $remainingBalance,
            'payment_reference' => $bookingReference,
            'arrival_time' => $bookingData['arrival_time'] ?? '2:00 PM',
            'extra_charges' => $bookingData['extra_charges'] ?? 0,
            'discount_amount' => $bookingData['discount_amount'] ?? 0,
            'discount_percentage' => $bookingData['discount_percentage'] ?? 0
        ];

        // Send booking confirmation email
        try {
            require_once 'includes/Mailer.php';
            $mailer = new Mailer();
            $mailer->sendBookingConfirmation($emailData);
        } catch (Exception $e) {
            // Log email error but don't fail the booking
            error_log('Error sending booking confirmation email: ' . $e->getMessage());
        }

        // Clear booking data from session
        unset($_SESSION['booking_data']);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error processing booking: ' . $e->getMessage(),
        'error_details' => $e->getTraceAsString()
    ];
}

echo json_encode($response);
