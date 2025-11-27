<?php
include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents('php://input'), true);

$bookingId = $data['bookingId'];
$checkIn = $data['checkIn'];
$checkOut = $data['checkOut'];
$totalAmount = $data['totalAmount'];
$rooms = $data['rooms'];
$status = $data['status'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET check_in = ?, check_out = ?, total_amount = ?, remaining_balance = 0, status = ?
        WHERE booking_id = ?
    ");
    $stmt->bind_param("ssdsi", $checkIn, $checkOut, $totalAmount, $status, $bookingId);
    $stmt->execute();

    $stmtRoom = $conn->prepare("
        UPDATE booked_rooms 
        SET room_type_id = ?, room_type_name = ?, price = ? 
        WHERE id = ?
    ");

    foreach ($rooms as $room) {
        $stmtRoom->bind_param("isdi", $room['room_type_id'], $room['room_type_name'], $room['price'], $room['id']);
        $stmtRoom->execute();
    }


    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
