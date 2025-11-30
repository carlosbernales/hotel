<?php
include '../adminBackend/mydb.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$booking_id = $data['booking_id'] ?? null;
$items = $data['items'] ?? [];

if (!$booking_id) {
    echo json_encode(["status" => "error", "message" => "Booking ID missing"]);
    exit;
}

// Get existing amenities for this booking (bedOrNot = 'no')
$existingAmenities = [];
$res = $conn->prepare("SELECT id, amenities_fk_id FROM booking_amenities WHERE booking_fk_id=? AND bedOrNot='no'");
$res->bind_param("i", $booking_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $existingAmenities[$row['amenities_fk_id']] = $row['id'];
}

$submittedIds = [];

foreach ($items as $item) {
    $amenities_fk_id = $item['amenity_id'];
    $quantity = $item['quantity'];
    $amenity_name = $item['amenity_name'];
    $bedOrNot = 'no';

    $submittedIds[] = $amenities_fk_id;

    if (isset($existingAmenities[$amenities_fk_id])) {
        // Update existing
        $update = $conn->prepare("UPDATE booking_amenities SET quantity=?, amenity_name=? WHERE id=?");
        $update->bind_param("isi", $quantity, $amenity_name, $existingAmenities[$amenities_fk_id]);
        $update->execute();
    } else {
        // Insert new
        $insert = $conn->prepare("INSERT INTO booking_amenities (amenities_fk_id, booking_fk_id, quantity, amenity_name, bedOrNot) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iiiss", $amenities_fk_id, $booking_id, $quantity, $amenity_name, $bedOrNot);
        $insert->execute();
    }
}

// Delete removed amenities
$idsToDelete = array_diff(array_keys($existingAmenities), $submittedIds);
if (!empty($idsToDelete)) {
    $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
    $types = str_repeat('i', count($idsToDelete) + 1);
    $stmt = $conn->prepare("DELETE FROM booking_amenities WHERE booking_fk_id=? AND bedOrNot='no' AND amenities_fk_id IN ($placeholders)");

    $bindValues = array_merge([$booking_id], $idsToDelete);
    $refs = [];
    foreach ($bindValues as $key => $value) {
        $refs[$key] = &$bindValues[$key];
    }
    array_unshift($refs, str_repeat('i', count($bindValues)));
    call_user_func_array([$stmt, 'bind_param'], $refs);
    $stmt->execute();
}

echo json_encode(["status" => "success"]);
