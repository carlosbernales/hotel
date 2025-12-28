<?php
include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id'])) {
    $id = (int) $data['id'];
    $paid_amount = isset($data['paid_amount']) ? (float) $data['paid_amount'] : 0;
    $remaining_balance = isset($data['remaining_balance']) ? (float) $data['remaining_balance'] : 0;
    $booking_status = isset($data['booking_status']) ? $conn->real_escape_string($data['booking_status']) : 'Pending';

    $overtime_hours = isset($data['overtime_hours']) ? (float) $data['overtime_hours'] : 0;
    $overtime_charge = isset($data['overtime_charge']) ? (float) $data['overtime_charge'] : 0;
    $total_amount = isset($data['total_amount']) ? (float) $data['total_amount'] : 0;

    $sql = "UPDATE event_bookings SET 
            paid_amount = ?, 
            remaining_balance = ?, 
            booking_status = ?,
            overtime_hours = ?,
            overtime_charge = ?,
            total_amount = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ddsdddi",
        $paid_amount,
        $remaining_balance,
        $booking_status,
        $overtime_hours,
        $overtime_charge,
        $total_amount,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}

$conn->close();
?>