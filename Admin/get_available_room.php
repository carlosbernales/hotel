<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Include database connection after setting error reporting
require_once 'db.php';

// Check if database connection is successful
if (!isset($con) || !$con) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomTypeId = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
    
    if ($roomTypeId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid room type ID']);
        exit;
    }
    
    try {
        // Start transaction
        mysqli_begin_transaction($con);
        
        // Find an available room that's not currently booked
        $query = "SELECT rn.room_number_id, rn.room_number 
                 FROM room_numbers rn
                 LEFT JOIN bookings b ON rn.room_number_id = b.room_number_id 
                    AND b.status IN ('confirmed', 'checked_in')
                 WHERE rn.room_type_id = ? 
                 AND rn.status = 'active'
                 AND b.booking_id IS NULL
                 LIMIT 1 FOR UPDATE";
        
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $con->error);
        }
        
        $bindResult = $stmt->bind_param('i', $roomTypeId);
        if (!$bindResult) {
            throw new Exception('Bind param failed: ' . $stmt->error);
        }
        
        $executeResult = $stmt->execute();
        if (!$executeResult) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception('Get result failed: ' . $stmt->error);
        }
        
        if ($result->num_rows === 0) {
            mysqli_rollback($con);
            echo json_encode(['success' => false, 'message' => 'No available rooms of this type']);
            exit;
        }
        
        $room = $result->fetch_assoc();
        $roomNumberId = $room['room_number_id'];
        $roomNumber = $room['room_number'];
        
        // Mark the room as temporarily reserved (optional, if you want to prevent race conditions)
        // $updateQuery = "UPDATE room_numbers SET status = 'reserved' WHERE room_number_id = ?";
        // $updateStmt = $con->prepare($updateQuery);
        // $updateStmt->bind_param('i', $roomNumberId);
        // $updateStmt->execute();
        
        // Commit transaction
        mysqli_commit($con);
        
        echo json_encode([
            'success' => true,
            'room_number_id' => $roomNumberId,
            'room_number' => $roomNumber
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        error_log("Error in get_available_room.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
