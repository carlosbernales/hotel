<?php
include '../adminBackend/mydb.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}


$booking_id = (int) $data['booking_id'];
$checkin = $data['check_in'];
$checkout = $data['check_out'];
$total_amount = (float) $data['total_amount'];
$down_payment = (float) $data['down_payment'];
$payment_input = (float) $data['payment_input'];
$change_amount = isset($data['change_amount']) ? (float) $data['change_amount'] : 0;
$rooms = $data['rooms'];
$payment_method = $data['payment_method'];
$status = $data['status'];
$discount_amount = isset($data['discount_amount']) ? (float) $data['discount_amount'] : 0;
$resched_reason = isset($data['resched_reason']) ? $data['resched_reason'] : null;

$payment_amount = $down_payment + $payment_input;

$getBooking = $conn->prepare("SELECT downpayment_amount FROM bookings WHERE booking_id = ?");
$getBooking->bind_param("i", $booking_id);
$getBooking->execute();
$getBooking->bind_result($currentDownpayment);
$getBooking->fetch();
$getBooking->close();

if ($payment_input >= ($total_amount - $currentDownpayment)) {
    $downpayment_amount = $total_amount;
    $remaining_balance = 0;
} else {
    $downpayment_amount = $currentDownpayment + $payment_input;
    $remaining_balance = $total_amount - $downpayment_amount;
}


$checkInDate = new DateTime($checkin);
$checkOutDate = new DateTime($checkout);
$nights = (int) $checkInDate->diff($checkOutDate)->format('%a');


$sql = "
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
        discount_amount = ?,
        payment_amount = ?,
        payment_change = ?
    WHERE booking_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssidddssdddi",
    $checkin,
    $checkout,
    $nights,
    $total_amount,
    $downpayment_amount,
    $remaining_balance,
    $payment_method,
    $status,
    $discount_amount,
    $payment_amount,
    $change_amount,
    $booking_id
);

if (!$stmt->execute()) {
    echo json_encode(["error" => $stmt->error]);
    exit;
}


if ($status === "checkin") {

    date_default_timezone_set('Asia/Manila');
    $transfer_date = date("Y-m-d H:i:s");

    foreach ($rooms as $r) {

        if (
            isset($r['original_room_number_fk_id'], $r['original_room_type_id']) &&
            (
                $r['original_room_number_fk_id'] != $r['room_number_fk_id'] ||
                $r['original_room_type_id'] != $r['room_type_id']
            )
        ) {

            $booked_room_fk_id = (int) $r['id'];
            $room_number_fk_id = (int) $r['original_room_number_fk_id'];
            $room_type_id = (int) $r['original_room_type_id'];

            $typeQry = $conn->prepare("SELECT room_type, price FROM room_types WHERE room_type_id = ?");
            $typeQry->bind_param("i", $room_type_id);
            $typeQry->execute();
            $typeQry->bind_result($room_type_name, $price);
            $typeQry->fetch();
            $typeQry->close();

            $insert = $conn->prepare("
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
            ");

            $insert->bind_param(
                "iiiisdss",
                $booked_room_fk_id,
                $booking_id,
                $room_number_fk_id,
                $room_type_id,
                $room_type_name,
                $price,
                $transfer_date,
                $resched_reason
            );

            $insert->execute();
        }
    }
}

foreach ($rooms as $r) {

    $room_type_id = (int) $r['room_type_id'];
    $room_number = (int) $r['room_number_fk_id'];
    $booked_id = (int) $r['id'];

    $typeQry = $conn->prepare("SELECT room_type, price FROM room_types WHERE room_type_id = ?");
    $typeQry->bind_param("i", $room_type_id);
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
        $room_type_id,
        $room_number,
        $roomTypeName,
        $roomPrice,
        $booked_id
    );

    if (!$updateRoom->execute()) {
        echo json_encode(["error" => $updateRoom->error]);
        exit;
    }
}

echo json_encode(["success" => true]);
?>