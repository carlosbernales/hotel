<?php
include '../adminBackend/mydb.php';

if (isset($_POST['id'], $_POST['status'])) {
    $id = (int) $_POST['id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE event_bookings SET booking_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Database error: ' . $conn->error;
    }
} else {
    echo 'Invalid request';
}
?>