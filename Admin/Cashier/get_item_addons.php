<?php
require_once 'db.php';

header('Content-Type: application/json');

$itemId = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;

if (!$itemId) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid item ID'
    ]);
    exit;
}

try {
    $sql = "SELECT id, name, price FROM menu_items_addons 
            WHERE menu_item_id = ?";
            
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $addons = [];
    while ($row = $result->fetch_assoc()) {
        $addons[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price']
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