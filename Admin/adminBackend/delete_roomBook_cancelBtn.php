<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['booking_id'])) {
    die("Booking ID is required.");
}

$booking_id = (int) $_GET['booking_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("DELETE FROM booked_rooms WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM guest_names WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM booking_amenities WHERE booking_fk_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM booking_check_inout WHERE booking_fk_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header("Location: ../../Admin/index.php?room_booking");

    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Error deleting booking: " . $e->getMessage());
}
?>