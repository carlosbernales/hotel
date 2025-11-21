<?php
require_once 'db.php';
header('Content-Type: application/json');

if (isset($_POST['booking_id'])) {
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    
    try {
        // Start transaction
        $con->begin_transaction();
        
        // First get the room type ID for this booking
        $get_room_sql = "SELECT rb.room_type_id, b.status 
                        FROM bookings b
                        JOIN room_bookings rb ON b.booking_id = rb.booking_id
                        WHERE b.booking_id = ?";
        $stmt = $con->prepare($get_room_sql);
        $stmt->bind_param("s", $booking_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error getting booking details: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $booking_data = $result->fetch_assoc();
        
        if (!$booking_data) {
            throw new Exception("Booking not found");
        }

        // Update booking status
        $update_sql = "UPDATE bookings SET status = 'Rejected' WHERE booking_id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("s", $booking_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating booking status: " . $stmt->error);
        }

        // Get the room_type_id from room_bookings
        $get_room_type_sql = "SELECT room_type_id FROM room_bookings WHERE booking_id = ?";
        $stmt = $con->prepare($get_room_type_sql);
        $stmt->bind_param("s", $booking_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error getting room type: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $room_data = $result->fetch_assoc();

        if (!$room_data) {
            throw new Exception("Room booking not found");
        }

        // Debug log
        error_log("Updating room availability for room_type_id: " . $room_data['room_type_id']);

        // Increase available rooms count
        $update_room_sql = "UPDATE rooms 
                           SET available_rooms = available_rooms + 1 
                           WHERE room_type_id = ?";
        $stmt = $con->prepare($update_room_sql);
        $stmt->bind_param("i", $room_data['room_type_id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating room availability: " . $stmt->error);
        }

        // Debug log
        error_log("Rows affected by room update: " . $stmt->affected_rows);

        // Commit transaction
        $con->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        error_log("Error in reject_booking.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 