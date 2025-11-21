<?php
require_once 'db.php';

// Only set header if not already sent
if (!headers_sent()) {
    header('Content-Type: application/json');
}

if (!isset($_GET['room_type_id']) || empty($_GET['room_type_id'])) {
    echo json_encode(['success' => false, 'error' => 'Room type ID is required']);
    exit;
}

$room_type_id = (int)$_GET['room_type_id'];

try {
    // Get room type details
    $query = "SELECT 
                rt.room_type_id,
                rt.room_type,
                rt.description,
                rt.price,
                rt.capacity,
                rt.image,
                rt.beds,
                rt.discount_percent,
                rt.status,
                COUNT(CASE WHEN rn.status = 'active' THEN rn.room_number_id ELSE NULL END) as total_available_rooms
              FROM room_types rt 
              LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id 
              WHERE rt.room_type_id = ? AND rt.status = 'active'
              GROUP BY rt.room_type_id";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $room_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Get room amenities if they exist
        $amenities_query = "SELECT a.name, a.icon 
                           FROM room_type_amenities rta 
                           JOIN amenities a ON rta.amenity_id = a.amenity_id 
                           WHERE rta.room_type_id = ?";
        $amenities_stmt = mysqli_prepare($con, $amenities_query);
        mysqli_stmt_bind_param($amenities_stmt, "i", $room_type_id);
        mysqli_stmt_execute($amenities_stmt);
        $amenities_result = mysqli_stmt_get_result($amenities_stmt);
        
        $amenities = [];
        while ($amenity = mysqli_fetch_assoc($amenities_result)) {
            $amenities[] = $amenity;
        }
        
        // Get available room numbers
        $rooms_query = "SELECT room_number_id, room_number 
                       FROM room_numbers 
                       WHERE room_type_id = ? AND status = 'active' 
                       ORDER BY room_number ASC";
        $rooms_stmt = mysqli_prepare($con, $rooms_query);
        mysqli_stmt_bind_param($rooms_stmt, "i", $room_type_id);
        mysqli_stmt_execute($rooms_stmt);
        $rooms_result = mysqli_stmt_get_result($rooms_stmt);
        
        $room_numbers = [];
        while ($room = mysqli_fetch_assoc($rooms_result)) {
            $room_numbers[] = $room;
        }
        
        echo json_encode([
            'success' => true,
            'room' => [
                'room_type_id' => $row['room_type_id'],
                'room_type' => $row['room_type'],
                'description' => $row['description'],
                'price' => $row['price'],
                'capacity' => $row['capacity'],
                'image' => $row['image'],
                'beds' => $row['beds'],
                'discount_percent' => $row['discount_percent'],
                'status' => $row['status'],
                'total_available_rooms' => $row['total_available_rooms'],
                'amenities' => $amenities,
                'room_numbers' => $room_numbers
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Room type not found']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>