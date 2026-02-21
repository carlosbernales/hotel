<?php
require 'db_con.php';
session_start();

header('Content-Type: application/json');

try {
    if (!isset($_POST['booking_id']) || !isset($_FILES['payment_proof'])) {
        throw new Exception('Missing required fields');
    }

    $bookingId = $_POST['booking_id'];
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];
    $referenceNumber = $_POST['payment_reference'];
    
    // Handle file upload
    $paymentProof = $_FILES['payment_proof'];
    $uploadDir = 'uploads/payment_proofs/';
    $fileName = uniqid() . '_' . basename($paymentProof['name']);
    $uploadPath = $uploadDir . $fileName;

    if (!move_uploaded_file($paymentProof['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to upload payment proof');
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Update table_bookings
        $stmt = $pdo->prepare("
            UPDATE table_bookings 
            SET payment_status = 'Paid',
                payment_method = ?,
                payment_reference = ?,
                payment_proof = ?,
                amount_paid = amount_paid + ?
            WHERE id = ?
        ");

        $stmt->execute([
            $paymentMethod,
            $referenceNumber,
            $uploadPath,
            $amount,
            $bookingId
        ]);

        // Update orders table
        $orderStmt = $pdo->prepare("
            UPDATE orders 
            SET payment_status = 'Paid',
                payment_method = ?,
                payment_reference = ?,
                payment_proof = ?,
                amount_paid = amount_paid + ?,
                remaining_balance = remaining_balance - ?
            WHERE table_id = ? AND order_type = 'advance'
        ");

        $orderStmt->execute([
            $paymentMethod,
            $referenceNumber,
            $uploadPath,
            $amount,
            $amount,
            $bookingId
        ]);

        // If remaining balance is 0, update status to confirmed
        $checkBalanceStmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'confirmed'
            WHERE table_id = ? 
            AND order_type = 'advance'
            AND remaining_balance <= 0
        ");
        $checkBalanceStmt->execute([$bookingId]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Payment processed successfully'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}