<?php
header('Content-Type: application/json');
include '../adminBackend/mydb.php';

$datetime = $_POST['datetime'] ?? '';
$typeIds = $_POST['type_ids'] ?? [];

if (empty($typeIds)) {
    echo json_encode(['counts' => [], 'available_type_ids' => []]);
    exit;
}

$bookingTime = $datetime ? strtotime($datetime) : null;

$placeholders = implode(',', array_fill(0, count($typeIds), '?'));
$types = str_repeat('i', count($typeIds));

$sql = "
    SELECT tn.id AS table_number_id, tn.table_type_fk_id, ot.date_time
    FROM table_number tn
    LEFT JOIN orders_table_type ott ON tn.id = ott.table_number_fk_id
    LEFT JOIN orders_table ot ON ott.table_booking_fk_id = ot.id
    WHERE tn.table_type_fk_id IN ($placeholders)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$typeIds);
$stmt->execute();
$result = $stmt->get_result();

$unavailableTableIds = [];

while ($row = $result->fetch_assoc()) {
    if ($row['date_time'] && $bookingTime) {
        $existingTime = strtotime($row['date_time']);
        $diffHours = abs($bookingTime - $existingTime) / 3600;

        if ($diffHours < 4 && !in_array($row['table_number_id'], $unavailableTableIds)) {
            $unavailableTableIds[] = $row['table_number_id'];
        }
    }
}


$counts = [];
$availableTypeIds = [];

foreach ($typeIds as $typeId) {
    $placeholders2 = implode(',', array_fill(0, count($unavailableTableIds), '?'));
    $types2 = str_repeat('i', count($unavailableTableIds));

    if (!empty($unavailableTableIds)) {
        $sql2 = "
            SELECT id, table_type_fk_id
            FROM table_number
            WHERE table_type_fk_id = ?
              AND status='available'
              AND id NOT IN ($placeholders2)
        ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param('i' . $types2, $typeId, ...$unavailableTableIds);
    } else {
        $sql2 = "
            SELECT id, table_type_fk_id
            FROM table_number
            WHERE table_type_fk_id = ?
              AND status='available'
        ";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param('i', $typeId);
    }

    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $availableCount = 0;
    $hasAvailable = false;
    while ($row2 = $res2->fetch_assoc()) {
        $availableCount++;
        $hasAvailable = true;
    }

    $counts[$typeId] = $availableCount;
    if ($hasAvailable)
        $availableTypeIds[] = $typeId;
}

echo json_encode(['counts' => $counts, 'available_type_ids' => $availableTypeIds]);
