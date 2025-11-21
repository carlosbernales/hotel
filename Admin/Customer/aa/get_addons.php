<?php
require_once 'db_con.php';

header('Content-Type: application/json');

if (!isset($_GET['item_id'])) {
    echo json_encode([]);
    exit;
}

$itemId = mysqli_real_escape_string($con, $_GET['item_id']);

$query = "
    SELECT id, name, price 
    FROM menu_item_addons 
    WHERE menu_item_id = ? 
    ORDER BY name
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $itemId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$addons = [];
while ($row = mysqli_fetch_assoc($result)) {
    $addons[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => floatval($row['price'])
    ];
}

echo json_encode($addons); 