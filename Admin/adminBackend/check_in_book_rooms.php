<?php
include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents("php://input"), true);

$booking_id = $data['booking_id'];
$checkout = $data['check_out'];
$total_amount = $data['total_amount'];
$downpayment_amount = $data['downpayment_amount'];
$remaining_balance = $data['remaining_balance'];
$rooms = $data['rooms'];

date_default_timezone_set('Asia/Manila');
$checkin = date('Y-m-d H:i:s');

$updateBooking = $conn->prepare("
    UPDATE bookings
    SET 
        check_in = ?, 
        check_out = ?, 
        total_amount = ?, 
        downpayment_amount = ?, 
        remaining_balance = ?, 
        status = 'checkin'
    WHERE booking_id = ?
");

$updateBooking->bind_param(
    "ssdddi",
    $checkin,
    $checkout,
    $total_amount,
    $downpayment_amount,
    $remaining_balance,
    $booking_id
);

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