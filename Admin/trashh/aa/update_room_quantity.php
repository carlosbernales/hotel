<?php
session_start();
require 'db_con.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['room_type_id']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$roomTypeId = $data['room_type_id'];
$action = $data['action'];

try {
    // Get current booking list
    if (!isset($_SESSION['booking_list'])) {
        $_SESSION['booking_list'] = [];
    }
    
    // Find the room in the booking list
    $found = false;
    foreach ($_SESSION['booking_list'] as &$item) {
        if ($item['room_type_id'] == $roomTypeId) {
            $found = true;
            
            // Get available rooms count
            $stmt = $pdo->prepare("SELECT available_rooms FROM rooms WHERE room_type_id = ?");
            $stmt->execute([$roomTypeId]);
            $availableRooms = $stmt->fetchColumn();
            
            // Update quantity based on action
            if ($action === 'increase' && $item['quantity'] < $availableRooms) {
                $item['quantity']++;
            } else if ($action === 'decrease' && $item['quantity'] > 1) {
                $item['quantity']--;
            }
            break;
        }
    }
    
    if (!$found) {
        echo json_encode(['success' => false, 'message' => 'Room not found in booking list']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Quantity updated successfully'
    ]);
    
} catch (PDOException $e) {
    // Log the error
    error_log("Error updating room quantity: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?> 