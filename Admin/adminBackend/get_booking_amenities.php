<?php
include '../adminBackend/mydb.php';
header("Content-Type: application/json");

$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT ba.*, al.amenity_name 
        FROM booking_amenities ba
        LEFT JOIN amenity_list al ON ba.amenities_fk_id = al.id
        WHERE ba.booking_fk_id=? AND ba.bedOrNot='no'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
