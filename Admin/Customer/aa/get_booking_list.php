<?php
session_start();
require 'db_con.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'items' => [],
    'count' => 0,
    'totalAmount' => 0
];

// Check if booking list exists
if (!isset($_SESSION['booking_list']) || !is_array($_SESSION['booking_list']) || empty($_SESSION['booking_list'])) {
    $response['message'] = 'Your booking list is empty';
    $response['success'] = true;
    $response['count'] = 0;
    echo json_encode($response);
    exit;
}

try {
    // Get the latest available rooms count for each room type
    $roomTypeIds = [];
    foreach ($_SESSION['booking_list'] as $item) {
        if (isset($item['room_type_id']) && is_numeric($item['room_type_id'])) {
            $roomTypeIds[] = (int)$item['room_type_id'];
        }
    }
    
    // Only proceed with database queries if we have room types in the list
    $availableRooms = [];
    if (!empty($roomTypeIds)) {
        // Create a safe list of placeholders
        $placeholders = rtrim(str_repeat('?,', count($roomTypeIds)), ',');
        
        // Debug log
        error_log("Room Type IDs: " . implode(', ', $roomTypeIds));
        error_log("Placeholders: " . $placeholders);
        
        if (!empty($placeholders)) {
            try {
                // Query to get the count of available room numbers for each room type
                $query = "SELECT rt.room_type_id, 
                         COALESCE(COUNT(rn.room_number_id), 0) as available
                         FROM room_types rt
                         LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id 
                         AND rn.status = 'active'
                         WHERE rt.room_type_id IN ($placeholders)
                         GROUP BY rt.room_type_id";
                          
                error_log("Available rooms query: " . $query);
                
                $stmt = $pdo->prepare($query);
                
                // Bind each parameter individually with its position
                foreach ($roomTypeIds as $index => $id) {
                    $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
                }
                
                $stmt->execute();
                $availableRooms = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                error_log("Available rooms result: " . print_r($availableRooms, true));
            } catch (PDOException $e) {
                error_log("SQL Error: " . $e->getMessage());
                // Continue with empty available rooms
            }
        }
    }
    
    // Update the booking list with the latest available rooms count
    $totalAmount = 0;
    $updatedItems = [];
    
    foreach ($_SESSION['booking_list'] as &$item) {
        if (!isset($item['room_type_id'])) {
            continue; // Skip invalid items
        }
        
        $roomTypeId = $item['room_type_id'];
        
        // Update available_rooms if we have new data
        if (isset($availableRooms[$roomTypeId])) {
            $item['available'] = intval($availableRooms[$roomTypeId]);
        } else {
            // Default to 0 if not found
            $item['available'] = isset($item['available']) ? intval($item['available']) : 0;
        }
        
        // Ensure quantity is valid
        if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] < 1) {
            $item['quantity'] = 1;
        }
        
        // Adjust quantity if it exceeds available rooms
        if ($item['quantity'] > $item['available'] && $item['available'] > 0) {
            $item['quantity'] = $item['available'];
        }
        
        // Ensure price is valid
        if (!isset($item['price']) || !is_numeric($item['price'])) {
            $item['price'] = 0;
        }
        
        // Calculate total amount
        $totalAmount += floatval($item['price']) * intval($item['quantity']);
        
        // Add to updated items
        $updatedItems[] = $item;
    }
    
    // Update session with the updated items
    $_SESSION['booking_list'] = $updatedItems;
    
    // Prepare response
    $response['success'] = true;
    $response['message'] = 'Booking list retrieved successfully';
    $response['items'] = $updatedItems;
    $response['count'] = count($updatedItems);
    $response['totalAmount'] = $totalAmount;
    
    // When preparing the room data for JSON response
    foreach ($updatedItems as &$item) {
        if (!empty($item['image'])) {
            if (strpos($item['image'], 'uploads/') !== false) {
                $item['image'] = $item['image'];  // Keep as is if it already contains 'uploads/'
            } else {
                $item['image'] = 'uploads/rooms/' . basename($item['image']);
            }
        } else {
            $item['image'] = 'uploads/rooms/default.jpg';
        }
    }
    
} catch (PDOException $e) {
    $response['message'] = 'Error retrieving booking list: ' . $e->getMessage();
    error_log("Database error in get_booking_list.php: " . $e->getMessage());
} catch (Exception $e) {
    $response['message'] = 'An error occurred. Please try again later.';
    error_log("General error in get_booking_list.php: " . $e->getMessage());
}

echo json_encode($response);
exit;
?> 