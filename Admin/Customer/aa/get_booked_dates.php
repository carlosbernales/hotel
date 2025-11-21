<?php
require_once 'db_con.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['package_name'])) {
        throw new Exception('Package name is required');
    }

    $stmt = $pdo->prepare("
        SELECT 
            event_date as reservation_date,
            start_time,
            end_time
        FROM event_bookings 
        WHERE booking_status IN ('pending', 'confirmed')
        AND package_name = :package_name
        AND event_date >= CURRENT_DATE
        ORDER BY event_date ASC, start_time ASC
    ");

    $stmt->execute([
        ':package_name' => $data['package_name']
    ]);
    
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'bookings' => $bookings
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 