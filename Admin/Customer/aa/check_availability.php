<?php
require 'db_con.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $checkin = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';

    // Validate dates
    if (empty($checkin) || empty($checkout)) {
        throw new Exception('Please provide both check-in and check-out dates');
    }

    $checkinDate = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);

    if ($checkoutDate <= $checkinDate) {
        throw new Exception('Check-out date must be after check-in date');
    }

    // First, check if we have any room types
    $sql = "SELECT COUNT(*) as count FROM room_types";
    $stmt = $pdo->query($sql);
    $roomTypeCount = $stmt->fetch()['count'];
    
    // Then check if we have any room numbers
    $sql = "SELECT COUNT(*) as count FROM room_numbers";
    $stmt = $pdo->query($sql);
    $roomNumberCount = $stmt->fetch()['count'];
    
    // Debug: Get all room types and their status
    $debugSql = "SELECT 
                    rt.room_type_id, 
                    rt.room_type,
                    COUNT(rn.room_number) as total_rooms,
                    SUM(CASE WHEN rn.status = 'active' OR rn.status IS NULL THEN 1 ELSE 0 END) as active_rooms,
                    GROUP_CONCAT(DISTINCT rn.status) as statuses
                 FROM room_types rt
                 LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id
                 GROUP BY rt.room_type_id, rt.room_type";
    
    $debugStmt = $pdo->query($debugSql);
    $debugInfo = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
    error_log('Room Type Availability: ' . print_r($debugInfo, true));

    // Get all active rooms
    $sql = "SELECT 
                rt.room_type_id,
                rt.room_type as type,
                rt.price,
                rt.capacity,
                rt.image,
                rt.description,
                GROUP_CONCAT(DISTINCT rn.room_number ORDER BY rn.room_number) as room_numbers,
                COUNT(DISTINCT rn.room_number) as total_rooms
            FROM room_types rt
            LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id 
                AND (rn.status = 'active' OR rn.status IS NULL)
            GROUP BY rt.room_type_id, rt.room_type, rt.price, rt.capacity, rt.image, rt.description";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $availableRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process room numbers into an array
    foreach ($availableRooms as &$room) {
        $room['room_numbers'] = !empty($room['room_numbers']) ? array_map('intval', explode(',', $room['room_numbers'])) : [];
        $room['available_rooms'] = count($room['room_numbers']);
    }
    
    // Process the results
    $response = [];
    foreach ($availableRooms as $room) {
        // Format image path
        $image = $room['image'];
        if (!empty($image)) {
            // Add '../../' to go up to the root directory from Customer/aa
            $image = '../../' . $image;
        } else {
            // Default image if none is provided
            $image = '../../images/default-room.jpg';
        }

        $response[] = [
            'type' => htmlspecialchars($room['type']),
            'price' => number_format($room['price'], 2),
            'capacity' => $room['capacity'],
            'image' => $image,
            'description' => htmlspecialchars($room['description'] ?? ''),
            'available_count' => $room['available_rooms'],
            'total_rooms' => $room['total_rooms']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $response
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 