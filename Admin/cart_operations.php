<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $room_type_id = $_POST['room_type_id'] ?? '';
            
            // Get room details from database
            $stmt = $con->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
            $stmt->bind_param("i", $room_type_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $room = $result->fetch_assoc();
                
                // Add to cart session
                $_SESSION['cart'][$room_type_id] = [
                    'room_type_id' => $room_type_id,
                    'room_type' => $room['room_type'],
                    'price' => $room['price'],
                    'image' => $room['image'],
                    'added_at' => time()
                ];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Room added to list',
                    'cart_count' => count($_SESSION['cart'])
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Room not found'
                ]);
            }
            break;
            
        case 'remove':
            $room_type_id = $_POST['room_type_id'] ?? '';
            if (isset($_SESSION['cart'][$room_type_id])) {
                unset($_SESSION['cart'][$room_type_id]);
                echo json_encode([
                    'success' => true,
                    'message' => 'Room removed from list',
                    'cart_count' => count($_SESSION['cart'])
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Room not found in list'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 