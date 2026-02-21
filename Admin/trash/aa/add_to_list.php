<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

try {
    // Get room type ID from POST
    $room_type_id = $_POST['room_type_id'] ?? null;
    
    if (!$room_type_id) {
        throw new Exception('Room type ID is required');
    }
    
    // Initialize booking list in session if it doesn't exist
    if (!isset($_SESSION['booking_list'])) {
        $_SESSION['booking_list'] = [];
    }
    
    // Check if room exists and is available
    $stmt = $pdo->prepare("SELECT rt.*, 
                          (SELECT COUNT(*) FROM room_numbers rn 
                           WHERE rn.room_type_id = rt.room_type_id 
                           AND rn.status = 'active') as available_rooms
                          FROM room_types rt
                          WHERE rt.room_type_id = ?");
    $stmt->execute([$room_type_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        throw new Exception('Room not found');
    }
    
    if ($room['available_rooms'] <= 0) {
        throw new Exception('Room is not available');
    }
    
    // Check if room is already in list
    $existingItem = null;
        foreach ($_SESSION['booking_list'] as &$item) {
            if ($item['room_type_id'] == $room_type_id) {
            $existingItem = &$item;
                break;
            }
        }
        
    if ($existingItem) {
        // If room exists and adding more would exceed available rooms, show error
        if ($existingItem['quantity'] >= $room['available_rooms']) {
            throw new Exception('Cannot add more rooms of this type - maximum availability reached');
        }
        $existingItem['quantity']++;
        $message = 'Room quantity updated in your list';
    } else {
        // Add new room to list
            $_SESSION['booking_list'][] = [
                'room_type_id' => $room_type_id,
                'room_type' => $room['room_type'],
                'price' => $room['price'],
                'capacity' => $room['capacity'],
            'image' => $room['image'],
            'available_rooms' => $room['available_rooms'],
            'quantity' => 1
        ];
        $message = 'Room added to your list';
    }
    
    // Get total count of items in booking list
    $count = array_sum(array_column($_SESSION['booking_list'], 'quantity'));
    
    // Clear booking form data from session
    unset($_SESSION['booking_form_data']);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'count' => $count
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 