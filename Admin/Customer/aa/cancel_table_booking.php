<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

try {
    // Get the JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['booking_id']) || !isset($data['reason'])) {
        throw new Exception('Missing required fields');
    }

    $bookingId = $data['booking_id'];
    $reason = $data['reason'];
    $userId = $_SESSION['user_id'];

    // First verify that this booking belongs to the current user
    $checkStmt = $pdo->prepare("SELECT * FROM table_bookings WHERE id = ? AND user_id = ?");
    $checkStmt->execute([$bookingId, $userId]);
    
    if ($checkStmt->rowCount() === 0) {
        throw new Exception('Invalid booking or unauthorized access');
    }

    $booking = $checkStmt->fetch(PDO::FETCH_ASSOC);

    // Check if booking is already cancelled
    if ($booking['status'] === 'cancelled') {
        throw new Exception('Booking is already cancelled');
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Update table_bookings
        $stmt = $pdo->prepare("
        UPDATE table_bookings 
            SET status = 'cancelled'
            WHERE id = ?
        ");
        $stmt->execute([$bookingId]);

        // Update orders table
        $orderStmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'cancelled'
            WHERE table_id = ? AND order_type = 'advance'
        ");
        $orderStmt->execute([$bookingId]);

        // Insert a single notification for the system
        $notifStmt = $pdo->prepare("
            INSERT INTO notifications (
                type, 
                message, 
                created_at
            ) VALUES (
                'table_cancelled', 
                ?, 
                NOW()
            )
        ");
        
        $message = "Table Reservation #$bookingId has been cancelled. Reason: $reason";
        $notifStmt->execute([$message]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
            'message' => 'Reservation cancelled successfully'
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
?> 