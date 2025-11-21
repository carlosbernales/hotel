<?php
require_once 'db.php';

header('Content-Type: application/json');

$query = "SELECT amenity_id, name, price FROM amenities";
$result = mysqli_query($con, $query);

$amenities = [];
while ($row = mysqli_fetch_assoc($result)) {
    $amenities[] = [
        'id' => (int)$row['amenity_id'],
        'name' => $row['name'],
        'price' => (float)$row['price']
    ];
}

echo json_encode(['success' => true, 'data' => $amenities]);
