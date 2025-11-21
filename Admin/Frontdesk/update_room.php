<?php
require_once 'includes/init.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['room_id']) || !isset($_POST['room_type_id']) || !isset($_POST['capacity'])) {
        throw new Exception('Missing required parameters');
    }

    $room_id = (int)$_POST['room_id'];
    $room_type_id = (int)$_POST['room_type_id'];
    $capacity = (int)$_POST['capacity'];

    // First verify if the room exists
    $check_sql = "SELECT * FROM rooms WHERE id = ?";
    $check_stmt = $con->prepare($check_sql);
    $check_stmt->bind_param("i", $room_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if (!$check_result->fetch_assoc()) {
        throw new Exception("Room not found");
    }

    // Begin transaction
    $con->begin_transaction();

    // Update the room
    $sql = "UPDATE rooms SET 
            room_type_id = ?, 
            total_rooms = ?, 
            available_rooms = ?
            WHERE id = ?";
            
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }

    $stmt->bind_param("iiii", 
        $room_type_id, 
        $capacity, 
        $capacity, 
        $room_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No rows were updated");
    }

    // Verify the update
    $verify_sql = "SELECT r.*, rt.room_type 
                   FROM rooms r 
                   LEFT JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                   WHERE r.id = ?";
    $verify_stmt = $con->prepare($verify_sql);
    $verify_stmt->bind_param("i", $room_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();
    $updated_room = $result->fetch_assoc();

    if (!$updated_room) {
        throw new Exception("Failed to verify room update");
    }

    // Commit transaction
    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Room updated successfully',
        'data' => $updated_room
    ]);

} catch (Exception $e) {
    // Rollback transaction if one is active
    if ($con && $con->connect_errno == 0) {
        $con->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error updating room: ' . $e->getMessage(),
        'debug' => [
            'room_id' => $room_id ?? null,
            'room_type_id' => $room_type_id ?? null,
            'capacity' => $capacity ?? null
        ]
    ]);
} 