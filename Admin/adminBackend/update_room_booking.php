<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action']; // process, reschedule, reject, cancel

    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $extra_bed = $_POST['extra_bed'];
    $total_amount = $_POST['total_amount'];
    $downpayment = $_POST['downpayment'];
    $remaining_balance = $_POST['remaining_balance'];

    // Determine status
    $status = '';
    switch ($action) {
        case 'process':
            $status = 'accepted';
            break;
        case 'reschedule':
            $status = 'reschedule';
            break;
        case 'reject':
            $status = 'rejected';
            break;
        case 'cancel':
            $status = 'cancelled';
            break;
    }

    // Update remaining balance if downpayment covers it
    if ($downpayment >= $remaining_balance) {
        $remaining_balance = 0;
    } else {
        $remaining_balance -= $downpayment;
    }

    // Update bookings table
    $stmt = $conn->prepare("UPDATE bookings SET extra_bed = ?, check_in = ?, check_out = ?, total_amount = ?, remaining_balance = ?, status = ? WHERE booking_id = ?");
    $stmt->bind_param("issddsi", $extra_bed, $check_in, $check_out, $total_amount, $remaining_balance, $status, $booking_id);
    $stmt->execute();

    // Update booked_rooms table if room_type_id is provided (from dropdowns)
    if (isset($_POST['room_types']) && is_array($_POST['room_types'])) {
        foreach ($_POST['room_types'] as $booked_room_id => $room_type_id) {
            $stmt2 = $conn->prepare("UPDATE booked_rooms SET room_type_id = ?, status = ? WHERE id = ?");
            $stmt2->bind_param("isi", $room_type_id, $status, $booked_room_id);
            $stmt2->execute();
        }
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'status' => $status]);
}
