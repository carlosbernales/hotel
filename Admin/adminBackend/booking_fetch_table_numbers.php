<?php
header('Content-Type: application/json');
include '../adminBackend/mydb.php';

$typeIds = $_POST['type_ids'] ?? [];
$datetime = $_POST['datetime'] ?? null;
$cartTableIds = $_POST['cart_table_ids'] ?? [];

if (empty($typeIds)) {
    echo json_encode([]);
    exit;
}

$cartTableIds = array_map('intval', $cartTableIds);

$placeholders = implode(',', array_fill(0, count($typeIds), '?'));
$types = str_repeat('i', count($typeIds));

$sql = "SELECT id, table_type_fk_id, table_number, status FROM table_number WHERE table_type_fk_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$typeIds);
$stmt->execute();
$result = $stmt->get_result();

$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[$row['id']] = [
        'id' => $row['id'],
        'table_type_fk_id' => $row['table_type_fk_id'],
        'table_number' => $row['table_number'],
        'is_available' => $row['status'] === 'available'
    ];
}

if ($datetime) {
    $bookingTime = strtotime($datetime);

    $placeholders2 = implode(',', array_fill(0, count($typeIds), '?'));
    $types2 = str_repeat('i', count($typeIds));

    $sql2 = "
        SELECT tn.id AS table_number_id, ot.date_time
        FROM table_number tn
        INNER JOIN orders_table_type ott ON tn.id = ott.table_number_fk_id
        INNER JOIN orders_table ot ON ott.table_booking_fk_id = ot.id
        WHERE tn.table_type_fk_id IN ($placeholders2)
    ";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param($types2, ...$typeIds);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    while ($row = $res2->fetch_assoc()) {
        $existingTime = strtotime($row['date_time']);
        $diffHours = abs($bookingTime - $existingTime) / 3600;

        if ($diffHours < 4 && isset($tables[$row['table_number_id']])) {
            $tables[$row['table_number_id']]['is_available'] = false;
        }
    }

}

foreach ($cartTableIds as $id) {
    if (isset($tables[$id])) {
        $tables[$id]['is_available'] = true;
    }
}

echo json_encode(array_values($tables));
