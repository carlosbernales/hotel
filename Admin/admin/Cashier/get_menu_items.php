<?php
require_once 'db.php';

header('Content-Type: application/json');

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($categoryId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid category ID'
    ]);
    exit;
}

try {
    $sql = "SELECT id, name, price FROM menu_items WHERE category_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => number_format($row['price'], 2)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching menu items: ' . $e->getMessage()
    ]);
}

$stmt->close();
$connection->close(); 