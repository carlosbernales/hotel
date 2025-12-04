<?php
include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents("php://input"), true);

$booking_id = $data['booking_id'];
$checkin = $data['check_in'];
$checkout = $data['check_out'];
$total_amount = $data['total_amount'];
$payment_input = $data['payment_input'];
$rooms = $data['rooms'];
$payment_method = $data['payment_method'];
$status = $data['status'];
$resched_reason = isset($data['resched_reason']) ? $data['resched_reason'] : null;
$discount_amount = $data['discount_amount'];

$bookingQry = $conn->prepare("SELECT check_in, check_out, downpayment_amount FROM bookings WHERE booking_id = ?");
$bookingQry->bind_param("i", $booking_id);
$bookingQry->execute();
$bookingQry->bind_result($oldCheckIn, $oldCheckOut, $currentDownpayment);
$bookingQry->fetch();
$bookingQry->close();

if ($payment_input >= ($total_amount - $currentDownpayment)) {
    $downpayment_amount = $total_amount;
    $remaining_balance = 0;
} else {
    $downpayment_amount = $currentDownpayment + $payment_input;
    $remaining_balance = $total_amount - $downpayment_amount;
}

$checkInDate = new DateTime($checkin);
$checkOutDate = new DateTime($checkout);
$interval = $checkInDate->diff($checkOutDate);
$nights = (int) $interval->format('%a');

$updateBooking = $conn->prepare("
    UPDATE bookings
    SET 
        check_in = ?, 
        check_out = ?, 
        nights = ?, 
        total_amount = ?, 
        downpayment_amount = ?, 
        remaining_balance = ?, 
        payment_method = ?,  
        status = ?,
        discount_amount = ?
    WHERE booking_id = ?
");
$updateBooking->bind_param("ssiddsssdi", $checkin, $checkout, $nights, $total_amount, $downpayment_amount, $remaining_balance, $payment_method, $status, $discount_amount, $booking_id);
$updateBooking->execute();


if ($status === "checkin") {

    $changedRooms = [];
    foreach ($rooms as $r) {
        if (
            isset($r['original_room_number_fk_id'], $r['original_room_type_id']) &&
            ($r['original_room_number_fk_id'] != $r['room_number_fk_id'] ||
                $r['original_room_type_id'] != $r['room_type_id'])
        ) {
            $changedRooms[] = $r;
        }
    }

    if (!empty($changedRooms)) {
        date_default_timezone_set('Asia/Manila');
        $transfer_date = date("Y-m-d H:i:s");
        $reason = $resched_reason;

        foreach ($changedRooms as $oldRoom) {

            $booked_room_fk_id = $oldRoom['id'];
            $bookings_fk_id = $booking_id;
            $room_number_fk_id = $oldRoom['original_room_number_fk_id'];
            $room_type_id = $oldRoom['original_room_type_id'];

            $typeQry = $conn->prepare("SELECT room_type, price FROM room_types WHERE room_type_id = ?");
            $typeQry->bind_param("i", $room_type_id);
            $typeQry->execute();
            $typeQry->bind_result($room_type_name, $price);
            $typeQry->fetch();
            $typeQry->close();

            $insert_transfer = "
                INSERT INTO room_transfers (
                    booked_room_fk_id, 
                    bookings_fk_id, 
                    room_number_fk_id, 
                    room_type_id, 
                    room_type_name, 
                    price, 
                    transfer_date, 
                    reason
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($insert_transfer);
            $stmt->bind_param(
                "iiiissss",
                $booked_room_fk_id,
                $bookings_fk_id,
                $room_number_fk_id,
                $room_type_id,
                $room_type_name,
                $price,
                $transfer_date,
                $reason
            );
            $stmt->execute();
        }
    }
}

foreach ($rooms as $r) {
    $typeQry = $conn->prepare("SELECT room_type, price FROM room_types WHERE room_type_id = ?");
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
    $updateRoom->bind_param("iisdi", $r['room_type_id'], $r['room_number_fk_id'], $roomTypeName, $roomPrice, $r['id']);
    $updateRoom->execute();
}

echo "success";
?>