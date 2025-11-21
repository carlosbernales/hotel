<?php
require 'db_con.php';

if (!isset($_GET['room_type_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Room type ID is required']);
    exit;
}

try {
    $roomTypeId = $_GET['room_type_id'];
    
    // Fetch room details including amenities
    $sql = "SELECT rt.*, 
            r.available_rooms,
            r.total_rooms,
            GROUP_CONCAT(a.name) as amenities
            FROM room_types rt
            JOIN rooms r ON rt.room_type_id = r.room_type_id
            LEFT JOIN room_type_amenities rta ON rt.room_type_id = rta.room_type_id
            LEFT JOIN amenities a ON rta.amenity_id = a.amenity_id
            WHERE rt.room_type_id = ?
            GROUP BY rt.room_type_id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$roomTypeId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        http_response_code(404);
        echo json_encode(['error' => 'Room not found']);
        exit;
    }
    
    // Get room images (you'll need to implement this based on your image storage system)
    $room['images'] = [
        $room['image'] // Add the main image
        // Add additional images if you have them
    ];
    
    echo json_encode($room);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 