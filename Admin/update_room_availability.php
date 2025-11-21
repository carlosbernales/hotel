<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the action, room type ID, and room number from the request
$action = $_POST['action'] ?? '';
$roomTypeId = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
$roomNumber = $_POST['room_number'] ?? '';

// Validate inputs
if (empty($action) || !in_array($action, ['increment', 'decrement']) || $roomTypeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Start transaction
    $con->begin_transaction();
    
    if ($action === 'decrement') {
        // For decrement, we need to find an available room
        $query = "UPDATE room_numbers 
                 SET status = 'occupied' 
                 WHERE room_type_id = ? 
                 AND status = 'active' 
                 " . ($roomNumber ? "AND room_number = ?" : "ORDER BY room_number ASC LIMIT 1");
        
        $stmt = $con->prepare($query);
        
        if ($roomNumber) {
            $stmt->bind_param('is', $roomTypeId, $roomNumber);
        } else {
            $stmt->bind_param('i', $roomTypeId);
        }
        
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception('No available rooms of this type');
        }
        
        // Get the updated room number if not provided
        if (!$roomNumber) {
            $roomNumber = $con->query("SELECT room_number FROM room_numbers 
                                     WHERE room_type_id = $roomTypeId 
                                     AND status = 'occupied' 
                                     ORDER BY updated_at DESC LIMIT 1")->fetch_assoc()['room_number'];
        }
        
    } else {
        // For increment, we need to mark the room as available
        if (empty($roomNumber)) {
            throw new Exception('Room number is required to increment availability');
        }
        
        $stmt = $con->prepare("UPDATE room_numbers 
                              SET status = 'active', 
                                  updated_at = NOW() 
                              WHERE room_type_id = ? 
                              AND room_number = ?");
        $stmt->bind_param('is', $roomTypeId, $roomNumber);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception('Room not found or already available');
        }
    }
    
    // Commit transaction
    $con->commit();
    
    // Get updated count of available rooms
    $countStmt = $con->prepare("SELECT COUNT(*) as available_count 
                              FROM room_numbers 
                              WHERE room_type_id = ? 
                              AND status = 'active'");
    $countStmt->bind_param('i', $roomTypeId);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    
    // Return success response with room number and updated count
    echo json_encode([
        'success' => true,
        'room_number' => $roomNumber,
        'available_count' => intval($countResult['available_count']),
        'message' => 'Room availability updated successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con)) {
        $con->rollback();
    }
    
    // Log the error
    error_log('Error updating room availability: ' . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close database connection
if (isset($con)) {
    $con->close();
}
?>
