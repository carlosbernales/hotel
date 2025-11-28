<?php
include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents("php://input"), true);

$booking_id = $data['booking_id'];
$checkin = $data['check_in'];
$checkout = $data['check_out'];
$total_amount = $data['total_amount'];
$rooms = $data['rooms'];

$dpQry = $conn->prepare("SELECT downpayment_amount FROM bookings WHERE booking_id = ?");
$dpQry->bind_param("i", $booking_id);
$dpQry->execute();
$dpQry->bind_result($existingDownPayment);
$dpQry->fetch();
$dpQry->close();

$totalDue = max(0, $total_amount - $existingDownPayment);

$newDownPayment = $existingDownPayment + $totalDue;

$updateBooking = $conn->prepare("
    UPDATE bookings
    SET 
        check_in = ?, 
        check_out = ?, 
        total_amount = ?, 
        remaining_balance = 0,
        downpayment_amount = ?,
        status = 'checkin'
    WHERE booking_id = ?
");
$updateBooking->bind_param("ssdii", $checkin, $checkout, $total_amount, $newDownPayment, $booking_id);
$updateBooking->execute();

foreach ($rooms as $r) {

    $typeQry = $conn->prepare("
        SELECT room_type, price 
        FROM room_types 
        WHERE room_type_id = ?
    ");
    $typeQry->bind_param("i", $r['room_type_id']);
    $typeQry->execute();
    $typeQry->bind_result($roomTypeName, $roomPrice);
    $typeQry->fetch();
    $typeQry->close();

    $updateRoom = $conn->prepare("
        UPDATE booked_rooms 
        SET 
            room_type_id = ?, 
            room_number_fk_id = ?,
            room_type_name = ?, 
            price = ?
        WHERE id = ?
    ");
    $updateRoom->bind_param(
        "iisdi",
        $r['room_type_id'],
        $r['room_number_fk_id'],
        $roomTypeName,
        $roomPrice,
        $r['id']
    );

    $updateRoom->execute();
}

echo "success";
?>