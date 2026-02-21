<?php
require_once 'db_con.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Get POST data
$checkInDate = isset($_POST['checkInDate']) ? $_POST['checkInDate'] : '';
$checkOutDate = isset($_POST['checkOutDate']) ? $_POST['checkOutDate'] : '';

// Validate input
if (empty($checkInDate) || empty($checkOutDate)) {
    http_response_code(400);
    echo json_encode(['error' => 'Check-in and check-out dates are required']);
    exit;
}

try {
    // First, get all active room types with their total room count
    $roomTypesStmt = $pdo->query("
        SELECT 
            rt.room_type_id,
            rt.room_type,
            rt.price,
            rt.discount_percent,
            (SELECT COUNT(*) FROM room_numbers rn WHERE rn.room_type_id = rt.room_type_id AND rn.status = 'active') as total_rooms
        FROM room_types rt
        WHERE rt.status = 'active'
    ");
    $roomTypes = $roomTypesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare response array
    $response = [];

    foreach ($roomTypes as $roomType) {
        // Get count of booked rooms for this room type within the date range
        $bookedRoomsStmt = $pdo->prepare("
            SELECT COALESCE(SUM(room_quantity), 0) as booked_rooms
            FROM bookings
            WHERE room_type_id = :room_type_id
            AND status IN ('confirmed', 'checked_in')
            AND (
                (check_in < :check_out AND check_out > :check_in)
            )
        ");

        $bookedRoomsStmt->execute([
            ':room_type_id' => $roomType['room_type_id'],
            ':check_in' => $checkInDate,
            ':check_out' => $checkOutDate
        ]);

        $bookedRooms = $bookedRoomsStmt->fetch(PDO::FETCH_ASSOC)['booked_rooms'];
        $availableRooms = max(0, $roomType['total_rooms'] - $bookedRooms);

        // Calculate price with discount if applicable
        $price = $roomType['price'];
        $hasDiscount = !empty($roomType['discount_percent']) && $roomType['discount_percent'] > 0;
        $discountedPrice = $hasDiscount 
            ? $price * (1 - ($roomType['discount_percent'] / 100))
            : $price;

        // Add room type to response
        $response[] = [
            'room_type_id' => $roomType['room_type_id'],
            'room_type' => $roomType['room_type'],
            'price' => number_format($price, 2),
            'discounted_price' => number_format($discountedPrice, 2),
            'has_discount' => $hasDiscount,
            'discount_percent' => $hasDiscount ? $roomType['discount_percent'] : 0,
            'total_rooms' => (int)$roomType['total_rooms'],
            'booked_rooms' => (int)$bookedRooms,
            'available_rooms' => $availableRooms,
            'is_available' => $availableRooms > 0
        ];
    }

    // Return the response
    echo json_encode([
        'success' => true,
        'check_in' => $checkInDate,
        'check_out' => $checkOutDate,
        'rooms' => $response
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}
?>