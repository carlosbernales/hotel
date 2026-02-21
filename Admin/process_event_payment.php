<?php
session_start();
require 'db_con.php';

try {
    // Debug: Log incoming data
    error_log("Payment Data: " . print_r($_POST, true));
    error_log("Files Data: " . print_r($_FILES, true));

    // Validate input
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
    $booking_reference = 'EVENT-' . $booking_id;

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

    // First, verify the booking exists
    $check_booking = $pdo->prepare("SELECT id FROM event_bookings WHERE id = ? AND user_id = ?");
    $check_booking->execute([$booking_id, $_SESSION['user_id']]);
    if (!$check_booking->fetch()) {
        throw new Exception('Event booking not found');
    }

    // Insert into payments table
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

    // Update event booking remaining balance and status
    $update_sql = "UPDATE event_bookings 
                  SET remaining_balance = remaining_balance - ?,
                      booking_status = CASE 
                          WHEN remaining_balance - ? <= 0 THEN 'confirmed'
                          ELSE booking_status 
                      END,
                      payment_proof = ?
                  WHERE id = ?";

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