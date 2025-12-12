<?php
header('Content-Type: application/json');
include '../adminBackend/mydb.php';

// Receive multiple table type IDs
$typeIds = $_POST['type_ids'] ?? [];

if (empty($typeIds)) {
    echo json_encode([]);
    exit;
}

// Prepare placeholders for IN clause
$placeholders = implode(',', array_fill(0, count($typeIds), '?'));
$types = str_repeat('i', count($typeIds));

$sql = "SELECT id, table_number, table_type_fk_id 
        FROM table_number 
        WHERE table_type_fk_id IN ($placeholders) 
          AND status = 'available'";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$typeIds);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
