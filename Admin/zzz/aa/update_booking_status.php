<?php
session_start();
require 'db_con.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    $dbConnection->beginTransaction();

    $booking_id = $_POST['booking_id'] ?? null;
    $new_status = $_POST['status'] ?? null;

    if (!$booking_id || !$new_status) {
        throw new Exception('Missing required parameters');
    }

    // Get the booking details with room information
    $bookingSql = "SELECT * FROM bookings WHERE id = :booking_id";
    $stmt = $dbConnection->prepare($bookingSql);
    $stmt->bindParam(':booking_id', $booking_id);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found');
    }

    // If status is changing to finished or cancelled
    if (($new_status === 'finished' || $new_status === 'cancelled') && 
        $booking['status'] !== 'finished' && $booking['status'] !== 'cancelled') {
        
        // Get current room availability
        $getRoomSql = "SELECT available_rooms, total_rooms 
                       FROM rooms 
                       WHERE room_type_id = :room_type_id 
                       FOR UPDATE";  // Lock the row
        
        $stmt = $dbConnection->prepare($getRoomSql);
        $stmt->bindParam(':room_type_id', $booking['room_type_id']);
        $stmt->execute();
        $currentRoom = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentRoom) {
            // Calculate new available rooms
            $newAvailable = min(
                $currentRoom['total_rooms'],
                $currentRoom['available_rooms'] + $booking['room_quantity']
            );

            // Update the rooms table
            $updateSql = "UPDATE rooms 
                         SET available_rooms = :new_available 
                         WHERE room_type_id = :room_type_id";
            
            $stmt = $dbConnection->prepare($updateSql);
            $stmt->bindParam(':new_available', $newAvailable);
            $stmt->bindParam(':room_type_id', $booking['room_type_id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update room availability');
            }

            // Log the update for debugging
            error_log(sprintf(
                "Room type %d: Available rooms updated from %d to %d (quantity: %d)",
                $booking['room_type_id'],
                $currentRoom['available_rooms'],
                $newAvailable,
                $booking['room_quantity']
            ));
        }
    }

    // Update booking status
    $updateStatusSql = "UPDATE bookings 
                       SET status = :new_status,
                           updated_at = NOW() 
                       WHERE id = :booking_id";

    $stmt = $dbConnection->prepare($updateStatusSql);
    $stmt->bindParam(':new_status', $new_status);
    $stmt->bindParam(':booking_id', $booking_id);
    $stmt->execute();

    $dbConnection->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Booking status and room availability updated successfully'
    ]);

} catch (Exception $e) {
    if ($dbConnection->inTransaction()) {
        $dbConnection->rollBack();
    }
    error_log("Error in update_booking_status.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 