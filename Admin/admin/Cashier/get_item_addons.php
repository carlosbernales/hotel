<?php
require_once 'db.php';

header('Content-Type: application/json');

$itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

if ($itemId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid item ID'
    ]);
    exit;
}

try {
    $sql = "SELECT mia.id, mia.name, mia.price 
            FROM menu_item_addons mia 
            WHERE mia.menu_item_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $addons = [];
    while ($row = $result->fetch_assoc()) {
        $addons[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => number_format($row['price'], 2)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'addons' => $addons
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching add-ons: ' . $e->getMessage()
    ]);
}

$stmt->close();
$connection->close(); 