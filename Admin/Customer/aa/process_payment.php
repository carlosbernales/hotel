<?php
session_start();
require 'db_con.php';
require 'paymongo/PayMongoService.php';

// Set content type to JSON
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Function to handle PayMongo payment
function processPayMongoPayment($amount, $description, $metadata = []) {
    try {
        $paymongo = new PayMongoService(true); // true for test mode
        
        // Create payment intent
        $intent = $paymongo->createPaymentIntent($amount, $description, $metadata);
        
        // For card payments, you would typically handle the payment method creation and attachment here
        // For this example, we'll just return the client key for the frontend to handle
        
        return [
            'success' => true,
            'client_key' => $intent['data']['attributes']['client_key'],
            'payment_intent_id' => $intent['data']['id']
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

try {
    // Debug: Log incoming data
    error_log("Payment Data: " . print_r($_POST, true));
    error_log("Files Data: " . print_r($_FILES, true));

    // Check for PayMongo API requests
    $jsonData = json_decode(file_get_contents('php://input'), true);
    
    if (isset($jsonData['action'])) {
        $action = $jsonData['action'];
        
        require 'paymongo/PayMongoService.php';
        $paymongo = new PayMongoService(true); // true for test mode
        
        switch ($action) {
            case 'create_payment_intent':
                $amount = $jsonData['amount'];
                $description = $jsonData['description'];
                $metadata = $jsonData['metadata'] ?? [];
                
                try {
                    $intent = $paymongo->createPaymentIntent($amount, $description, $metadata);
                    sendResponse(true, 'Payment intent created', [
                        'payment_intent_id' => $intent['data']['id'],
                        'client_secret' => $intent['data']['attributes']['client_key']
                    ]);
                } catch (Exception $e) {
                    sendResponse(false, $e->getMessage());
                }
                break;
                
            case 'attach_payment_method':
                $paymentIntentId = $jsonData['payment_intent_id'];
                $paymentMethodId = $jsonData['payment_method_id'];
                $returnUrl = $jsonData['return_url'];
                
                try {
                    $result = $paymongo->attachPaymentMethod($paymentIntentId, $paymentMethodId, $returnUrl);
                    
                    // Save payment details to database
                    $bookingId = $jsonData['booking_id'];
                    $amount = $jsonData['amount'] / 100; // Convert back to PHP
                    $paymentMethod = 'paymongo_card';
                    $status = 'paid';
                    $referenceNumber = $paymentIntentId;
                    
                    // Save to database
                    $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, payment_method, status, reference_number, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->bind_param("idsss", $bookingId, $amount, $paymentMethod, $status, $referenceNumber);
                    
                    if ($stmt->execute()) {
                        // Update booking status
                        $updateStmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
                        $updateStmt->bind_param("i", $bookingId);
                        $updateStmt->execute();
                        
                        sendResponse(true, 'Payment processed successfully', [
                            'payment_intent' => $result['data'],
                            'next_action' => $result['data']['attributes']['next_action'] ?? null
                        ]);
                    } else {
                        throw new Exception('Failed to save payment details');
                    }
                } catch (Exception $e) {
                    sendResponse(false, $e->getMessage());
                }
                break;
                
            default:
                sendResponse(false, 'Invalid action');
        }
    }
    
    // Check if this is a form submission from room_payment.php
    $isRoomPayment = isset($_POST['payment_method']) && isset($_POST['total_amount']) && isset($_POST['amount_paid']);
    
    if ($isRoomPayment) {
        // Handle room payment form submission
        $payment_method = $_POST['payment_method'];
        $total_amount = floatval($_POST['total_amount']);
        $amount_paid = floatval($_POST['amount_paid']);
        $reference_number = $_POST['reference_number'] ?? '';
        
        // Validate input
        if (empty($payment_method) || $total_amount <= 0 || $amount_paid <= 0) {
            throw new Exception('Invalid payment details');
        }
        
        // Handle file upload
        if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Payment receipt is required');
        }
        
        // Upload receipt
        $upload_dir = '../uploads/payment_receipts/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
        $receipt_file = 'receipt_' . time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $receipt_file;
        
        if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $upload_path)) {
            throw new Exception('Failed to upload payment receipt');
        }
        
        // Here you would typically:
        // 1. Save the payment details to your database
        // 2. Update the booking status
        // 3. Send confirmation email to the user
        // 4. Notify the admin
        
        // For now, we'll just return a success response
        $response = [
            'redirect' => 'room_payment_success.php?ref=' . urlencode($reference_number)
        ];
        
        sendResponse(true, 'Payment processed successfully', $response);
    } else {
        // Original booking payment handling
        if (!isset($_POST['booking_id']) || !isset($_POST['amount']) || !isset($_POST['payment_method'])) {
            throw new Exception('Missing required parameters');
        }

        // Convert and validate booking_id as integer
        $booking_id = intval($_POST['booking_id']);
        if ($booking_id <= 0) {
            throw new Exception('Invalid booking ID');
        }

        $amount = floatval($_POST['amount']);
        $payment_method = $_POST['payment_method'];
        $reference_number = $_POST['reference_number'];
        $booking_reference = 'ROOM-' . $booking_id; // Generate booking reference

        // Handle file upload
        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Payment proof is required');
        }

        // Upload payment proof
        $upload_dir = '../uploads/payment_proofs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $proof_file = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $proof_file;

        if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $upload_path)) {
            throw new Exception('Failed to upload payment proof');
        }

        // Start transaction
        $pdo->beginTransaction();

        // First, verify the booking exists and get user details
        $check_booking = $pdo->prepare("SELECT b.booking_id, b.user_id, u.first_name, u.last_name 
                                       FROM bookings b 
                                       LEFT JOIN userss u ON b.user_id = u.id 
                                       WHERE b.booking_id = ?");
        $check_booking->execute([$booking_id]);
        $booking = $check_booking->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            throw new Exception('Booking not found');
        }
    }

    // Insert into payments table using numbered parameters
    $payment_sql = "INSERT INTO payments (booking_id, booking_reference, amount, payment_method, 
                    reference_number, payment_date, proof_file) 
                   VALUES (?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $pdo->prepare($payment_sql);
    $stmt->execute([
        $booking_id,
        $booking_reference,
        $amount,
        $payment_method,
        $reference_number,
        $proof_file
    ]);

    // Update booking remaining balance and status using numbered parameters
    $update_sql = "UPDATE bookings 
                  SET remaining_balance = remaining_balance - ?,
                      status = CASE 
                          WHEN remaining_balance - ? <= 0 THEN 'confirmed'
                          ELSE status 
                      END,
                      payment_proof = ?
                  WHERE booking_id = ?";

    $stmt = $pdo->prepare($update_sql);
    $stmt->execute([
        $amount,
        $amount,
        $proof_file,
        $booking_id
    ]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Payment processed successfully']);

} catch (Exception $e) {
    // Rollback transaction if started
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Delete uploaded file if exists
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 