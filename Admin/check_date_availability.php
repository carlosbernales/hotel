<?php
require_once 'db_con.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['packageName']) || !isset($data['date'])) {
        throw new Exception('Missing required parameters');
    }

    $stmt = $pdo->prepare("
        SELECT 
            eb.reservation_date,
            eb.package_name,
            eb.start_time,
            eb.end_time,
            eb.booking_status
        FROM event_bookings eb
        WHERE eb.booking_status IN ('pending', 'confirmed')
        AND eb.reservation_date = :date
        ORDER BY eb.start_time ASC
    ");

    $stmt->execute([
        ':date' => $data['date']
    ]);
    
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if there are any bookings for this date
    $isBooked = false;
    foreach ($bookings as $booking) {
        // If there's any booking on this date, mark as booked
        $isBooked = true;
        break;
    }

    echo json_encode([
        'isBooked' => $isBooked,
        'bookings' => $bookings
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?> 