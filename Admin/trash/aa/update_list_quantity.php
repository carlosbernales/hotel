<?php
session_start();
require 'db_con.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'quantity' => 0,
    'totalAmount' => 0,
    'available_rooms' => 0  // Add this field to track available rooms
];

// Check if user is logged in (if your system requires login)
// if (!isset($_SESSION['user_id'])) {
//     $response['message'] = 'Please log in to update your booking list';
//     echo json_encode($response);
//     exit;
// }

// Check if booking list exists in session
if (!isset($_SESSION['booking_list']) || !is_array($_SESSION['booking_list'])) {
    $_SESSION['booking_list'] = [];
}

// Validate request method and parameters
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['room_type_id']) || !isset($_POST['quantity'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

$roomTypeId = intval($_POST['room_type_id']);
$quantity = intval($_POST['quantity']);

// Validate quantity
if ($quantity < 1) {
    $response['message'] = 'Quantity must be at least 1';
    echo json_encode($response);
    exit;
}

// Check if room exists in booking list
$roomExists = false;
foreach ($_SESSION['booking_list'] as $index => $item) {
    if (isset($item['room_type_id']) && $item['room_type_id'] == $roomTypeId) {
        $roomExists = true;
        break;
    }
}

if (!$roomExists) {
    $response['message'] = 'Room not in booking list';
    echo json_encode($response);
    exit;
}

try {
    // Get room information from database - use the room_numbers table to get accurate available_rooms
    $stmt = $pdo->prepare("SELECT rt.*, 
                          (SELECT COUNT(*) FROM room_numbers rn 
                           WHERE rn.room_type_id = rt.room_type_id 
                           AND rn.status = 'active') as available_rooms
                          FROM room_types rt 
                          WHERE rt.room_type_id = ?");
    $stmt->execute([$roomTypeId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        // Room type not found
        $response['message'] = 'Room type not found';
        echo json_encode($response);
        exit;
    }
    
    // Check if requested quantity is available
    $availableRooms = intval($room['available_rooms']);
    $response['available_rooms'] = $availableRooms; // Add to response
    
    if ($quantity > $availableRooms) {
        $response['message'] = "Only {$availableRooms} room(s) available of this type";
        $response['quantity'] = $availableRooms > 0 ? $availableRooms : 1;
        
        // Update to maximum available if less than requested
        if ($availableRooms > 0) {
            // Update the quantity in session to maximum available
    foreach ($_SESSION['booking_list'] as &$item) {
                if ($item['room_type_id'] == $roomTypeId) {
                    $item['quantity'] = $availableRooms;
                    $item['available_rooms'] = $availableRooms; // Update available_rooms in session
                    break;
                }
            }
        }
        
        // Calculate total amount
        $totalAmount = 0;
        foreach ($_SESSION['booking_list'] as $item) {
            if (isset($item['price']) && isset($item['quantity'])) {
                $itemPrice = floatval($item['price']);
                $itemQuantity = intval($item['quantity']);
                $totalAmount += $itemPrice * $itemQuantity;
                
                // Add debugging
                error_log("(Max available case) Item: {$item['room_type']} - Price: {$itemPrice} x Quantity: {$itemQuantity} = " . ($itemPrice * $itemQuantity));
            }
        }
        
        error_log("(Max available case) Total calculated amount: {$totalAmount}");
        
        $response['totalAmount'] = $totalAmount;
        echo json_encode($response);
        exit;
    }
    
    // Update quantity in session
    foreach ($_SESSION['booking_list'] as &$item) {
        if ($item['room_type_id'] == $roomTypeId) {
            $item['quantity'] = $quantity;
            $item['available_rooms'] = $availableRooms; // Update available_rooms in session
            break;
        }
    }
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($_SESSION['booking_list'] as $item) {
        if (isset($item['price']) && isset($item['quantity'])) {
            $itemPrice = floatval($item['price']);
            $itemQuantity = intval($item['quantity']);
            $totalAmount += $itemPrice * $itemQuantity;
            
            // Add debugging
            error_log("Item: {$item['room_type']} - Price: {$itemPrice} x Quantity: {$itemQuantity} = " . ($itemPrice * $itemQuantity));
        }
    }
    
    error_log("Total calculated amount: {$totalAmount}");
    
    // Prepare successful response
    $response['success'] = true;
    $response['message'] = 'Quantity updated successfully';
    $response['quantity'] = $quantity;
    $response['totalAmount'] = $totalAmount;
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response['message'] = 'Database error occurred: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;
?> 