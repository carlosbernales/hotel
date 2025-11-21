<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['room_type_id'])) {
        throw new Exception('Room type ID is required');
    }

    // Check if room is already in booking list
    $sql = "SELECT COUNT(*) as count 
            FROM booking_list 
            WHERE room_type_id = :room_type_id 
            AND user_id = :user_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'room_type_id' => $data['room_type_id'],
        'user_id' => $_SESSION['user_id']
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'in_list' => $result['count'] > 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 