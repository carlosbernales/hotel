<?php
include '../adminBackend/mydb.php';
header("Content-Type: application/json");

$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT * FROM booking_amenities WHERE booking_fk_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
