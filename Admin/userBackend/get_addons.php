<?php
include '../adminBackend/mydb.php';
$menu_fk_id = intval($_POST['menu_fk_id']);

$sql = "SELECT id, name, price FROM menu_items_addons WHERE menu_item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $menu_fk_id);
$stmt->execute();
$result = $stmt->get_result();

$addons = [];
while ($row = $result->fetch_assoc())
    $addons[] = $row;

echo json_encode(['addons' => $addons]);
