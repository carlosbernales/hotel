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

$existingBeds = [];
$res = $conn->prepare("SELECT id, amenities_fk_id FROM booking_amenities WHERE booking_fk_id=?");
$res->bind_param("i", $booking_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $existingBeds[$row['amenities_fk_id']] = $row['id'];
}

$submittedIds = [];

foreach ($items as $item) {
    $amenities_fk_id = (int) $item['amenity_id'];
    $quantity = (int) $item['quantity'];

    $bedStmt = $conn->prepare("SELECT item_type, price FROM beds WHERE id=? LIMIT 1");
    $bedStmt->bind_param("i", $amenities_fk_id);
    $bedStmt->execute();
    $bedRes = $bedStmt->get_result();
    $bedData = $bedRes->fetch_assoc();
    if (!$bedData)
        continue;

    $item_type = $bedData['item_type'];
    $price = (float) $bedData['price'];

    $submittedIds[] = $amenities_fk_id;

    if (isset($existingBeds[$amenities_fk_id])) {
        $update = $conn->prepare("UPDATE booking_amenities SET quantity=?, amenity_name=?, price=? WHERE id=?");
        $update->bind_param("isdi", $quantity, $item_type, $price, $existingBeds[$amenities_fk_id]);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO booking_amenities (amenities_fk_id, booking_fk_id, quantity, amenity_name, price) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iiisd", $amenities_fk_id, $booking_id, $quantity, $item_type, $price);
        $insert->execute();

    }
}

$idsToDelete = array_diff(array_keys($existingBeds), $submittedIds);
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

echo json_encode(["status" => "success"]);
