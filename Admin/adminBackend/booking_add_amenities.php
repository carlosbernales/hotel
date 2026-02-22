<?php
include '../adminBackend/mydb.php';
header("Content-Type: application/json");

// Get data
$data = json_decode(file_get_contents("php://input"), true);

$booking_id = $data['booking_id'] ?? null;
$items = $data['items'] ?? [];

if (!$booking_id) {
    echo json_encode(["status" => "error", "message" => "Booking ID missing"]);
    exit;
}

$bookingStmt = $conn->prepare("SELECT total_amount, discount_amount, discount_percentage, nights, downpayment_amount FROM bookings WHERE booking_id=? LIMIT 1");
$bookingStmt->bind_param("i", $booking_id);
$bookingStmt->execute();
$bookingRes = $bookingStmt->get_result();

if ($bookingRes->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Booking not found"]);
    exit;
}

$booking = $bookingRes->fetch_assoc();
$nights = (int) $booking['nights'];
$discountPercentage = (float) $booking['discount_percentage'];
$downpayment = (float) $booking['downpayment_amount'];
$currentTotal = (float) $booking['total_amount'];
$currentDiscount = (float) $booking['discount_amount'];

$existingAmenities = [];
$res = $conn->prepare("SELECT id, amenities_fk_id FROM booking_amenities WHERE booking_fk_id=?");
$res->bind_param("i", $booking_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $existingAmenities[$row['amenities_fk_id']] = $row['id'];
}

$submittedIds = [];
$totalAmenitiesPrice = 0;

foreach ($items as $item) {
    $amenities_fk_id = (int) $item['amenity_id'];
    $quantity = (int) $item['quantity'];

    $amenityStmt = $conn->prepare("SELECT item_type, price FROM beds WHERE id=? LIMIT 1");
    $amenityStmt->bind_param("i", $amenities_fk_id);
    $amenityStmt->execute();
    $amenityRes = $amenityStmt->get_result();
    $amenityData = $amenityRes->fetch_assoc();
    if (!$amenityData)
        continue;

    $item_type = $amenityData['item_type'];
    $price = (float) $amenityData['price'];

    $submittedIds[] = $amenities_fk_id;

    $totalAmenitiesPrice += $price * $quantity * $nights;

    if (isset($existingAmenities[$amenities_fk_id])) {
        $update = $conn->prepare("UPDATE booking_amenities SET quantity=?, amenity_name=?, price=? WHERE id=?");
        $update->bind_param("isdi", $quantity, $item_type, $price, $existingAmenities[$amenities_fk_id]);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO booking_amenities (amenities_fk_id, booking_fk_id, quantity, amenity_name, price) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iiisd", $amenities_fk_id, $booking_id, $quantity, $item_type, $price);
        $insert->execute();
    }
}

$idsToDelete = array_diff(array_keys($existingAmenities), $submittedIds);
if (!empty($idsToDelete)) {
    $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
    $stmt = $conn->prepare("DELETE FROM booking_amenities WHERE booking_fk_id=? AND amenities_fk_id IN ($placeholders)");

    $bindValues = array_merge([$booking_id], $idsToDelete);
    $refs = [];
    foreach ($bindValues as $key => $value) {
        $refs[$key] = &$bindValues[$key];
    }
    array_unshift($refs, str_repeat('i', count($bindValues)));
    call_user_func_array([$stmt, 'bind_param'], $refs);
    $stmt->execute();
}

$originalTotal = $currentTotal + $currentDiscount;

$originalTotal += $totalAmenitiesPrice;

$newDiscountAmount = round($originalTotal * ($discountPercentage / 100), 2);

$newTotalAmount = round($originalTotal - $newDiscountAmount, 2);

$remainingBalance = $newTotalAmount - $downpayment;

$updateBooking = $conn->prepare("UPDATE bookings SET total_amount=?, discount_amount=?, remaining_balance=? WHERE booking_id=?");
$updateBooking->bind_param("dddi", $newTotalAmount, $newDiscountAmount, $remainingBalance, $booking_id);
$updateBooking->execute();

echo json_encode([
    "status" => "success",
    "total_amount" => $newTotalAmount,
    "discount_amount" => $newDiscountAmount,
    "remaining_balance" => $remainingBalance
]);
?>