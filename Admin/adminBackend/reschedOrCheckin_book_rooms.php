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

$discount_type = isset($data['discount_type']) ? $data['discount_type'] : null;
$discount_percentage = isset($data['discount_percentage']) ? $data['discount_percentage'] : 0;
$discount_amount = isset($data['discount_amount']) ? $data['discount_amount'] : 0;


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
        discount_type = ?, 
        discount_percentage = ?, 
        discount_amount = ?, 
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
$updateBooking->bind_param("sddssiddsssdi", $discount_type, $discount_percentage, $discount_amount, $checkin, $checkout, $nights, $total_amount, $downpayment_amount, $remaining_balance, $payment_method, $status, $discount_amount, $booking_id);
$updateBooking->execute();


if ($status === 'rescheduled') {
    $dtManila = new DateTime("now", new DateTimeZone("Asia/Manila"));
    $dateResched = $dtManila->format("Y-m-d H:i:s");

    $insertResched = $conn->prepare("
        INSERT INTO reschedule_bookings (booking_fk_id, check_in, check_out, reason, date_resched)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insertResched->bind_param("issss", $booking_id, $checkin, $checkout, $resched_reason, $dateResched);
    if (!$insertResched->execute()) {
        die("Reschedule insert failed: " . $insertResched->error);
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