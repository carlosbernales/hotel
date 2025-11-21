<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_POST['booking_id']) || !isset($_POST['new_room_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
$new_room_type_id = mysqli_real_escape_string($con, $_POST['new_room_id']);

// Start transaction
mysqli_begin_transaction($con);

try {
    // 1. Get current room booking details
    $get_current = "SELECT rb.*, rt.room_type, rt.price, b.check_in, b.check_out, b.total_amount 
                    FROM room_bookings rb 
                    JOIN room_types rt ON rb.room_type_id = rt.room_type_id 
                    JOIN bookings b ON rb.booking_id = b.booking_id
                    WHERE rb.booking_id = ?";
    
    $stmt = $con->prepare($get_current);
    $stmt->bind_param("s", $booking_id);
    $stmt->execute();
    $current = $stmt->get_result()->fetch_assoc();

    if (!$current) {
        throw new Exception('Current booking not found');
    }

    // 2. Get new room type details
    $get_new = "SELECT * FROM room_types WHERE room_type_id = ?";
    $stmt = $con->prepare($get_new);
    $stmt->bind_param("s", $new_room_type_id);
    $stmt->execute();
    $new = $stmt->get_result()->fetch_assoc();

    if (!$new) {
        throw new Exception('New room type not found');
    }

    // 3. Calculate price difference
    $nights = (strtotime($current['check_out']) - strtotime($current['check_in'])) / (60 * 60 * 24);
    $price_difference = ($new['price'] - $current['price']) * $nights;
    $new_total = $current['total_amount'] + $price_difference;

    // 4. Update room_bookings table
    $update_booking = "UPDATE room_bookings 
                      SET room_type_id = ?, room_price = ? 
                      WHERE booking_id = ?";
    $stmt = $con->prepare($update_booking);
    $stmt->bind_param("sds", $new_room_type_id, $new['price'], $booking_id);
    $stmt->execute();

    // 5. Update bookings table total amount
    $update_total = "UPDATE bookings 
                    SET total_amount = ? 
                    WHERE booking_id = ?";
    $stmt = $con->prepare($update_total);
    $stmt->bind_param("ds", $new_total, $booking_id);
    $stmt->execute();

    // 6. Update rooms availability
    // Decrease availability for new room
    $update_new_room = "UPDATE rooms 
                       SET available_rooms = available_rooms - 1 
                       WHERE room_type_id = ?";
    $stmt = $con->prepare($update_new_room);
    $stmt->bind_param("s", $new_room_type_id);
    $stmt->execute();

    // Increase availability for old room
    $update_old_room = "UPDATE rooms 
                       SET available_rooms = available_rooms + 1 
                       WHERE room_type_id = ?";
    $stmt = $con->prepare($update_old_room);
    $stmt->bind_param("s", $current['room_type_id']);
    $stmt->execute();

    // If everything is successful, commit the transaction
    mysqli_commit($con);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Room transferred successfully',
        'new_total' => $new_total,
        'price_difference' => $price_difference
    ]);

} catch (Exception $e) {
    // If there's an error, rollback the transaction
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 