<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$reservation_id = mysqli_real_escape_string($con, $_POST['id']);
$status = mysqli_real_escape_string($con, $_POST['status']);

// Validate status
$allowed_statuses = ['confirmed', 'cancelled'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status'
    ]);
    exit;
}

$query = "UPDATE table_reservations SET status = ? WHERE reservation_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "si", $status, $reservation_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Reservation status updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating reservation status: ' . mysqli_error($con)
    ]);
}
?> 