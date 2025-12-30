<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $booking_id = $_POST['booking_id'] ?? null;
    $rooms = $_POST['rooms'] ?? null;
    $total_amount = $_POST['total_amount'] ?? null;

    if (!$booking_id || !$rooms || !$total_amount) {
        echo "error";
        exit;
    }

    $rooms = json_decode($rooms, true);
    if (!is_array($rooms)) {
        echo "error";
        exit;
    }

    $stmt = $conn->prepare("
        SELECT downpayment_amount 
        FROM bookings 
        WHERE booking_id = ?
    ");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "error";
        exit;
    }

    $row = $result->fetch_assoc();
    $downpayment = (float) $row['downpayment_amount'];
    $stmt->close();

    $remaining_balance = $total_amount - $downpayment;
    if ($remaining_balance < 0) {
        $remaining_balance = 0;
    }

    $stmt = $conn->prepare("
        UPDATE bookings 
        SET 
            status = 'accepted',
            total_amount = ?,
            remaining_balance = ?
        WHERE booking_id = ?
    ");
    $stmt->bind_param("ddi", $total_amount, $remaining_balance, $booking_id);

    if (!$stmt->execute()) {
        echo "error";
        exit;
    }
    $stmt->close();

    $update_stmt = $conn->prepare("
        UPDATE booked_rooms 
        SET 
            room_number_fk_id = ?,
            room_type_id = ?,
            room_type_name = ?,
            price = ?
        WHERE id = ? AND booking_id = ?
    ");

    foreach ($rooms as $room) {
        $update_stmt->bind_param(
            "iisdii",
            $room['room_number_fk_id'],
            $room['room_type_id'],
            $room['room_type_name'],
            $room['price'],
            $room['id'],
            $booking_id
        );

        if (!$update_stmt->execute()) {
            echo "error";
            exit;
        }
    }

    $update_stmt->close();
    $conn->close();

    echo "success";

} else {
    echo "error";
}
