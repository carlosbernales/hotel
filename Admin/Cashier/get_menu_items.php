<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Fetch menu items with categories
    $stmt = $pdo->prepare("
        SELECT 
            mi.id,
            mi.name,
            mi.price,
            mi.description,
            mi.category_id,
            mc.display_name as category_name,
            mi.image_path
        FROM menu_items mi
        LEFT JOIN menu_categories mc ON mi.category_id = mc.id
        WHERE mi.availability = 1
        ORDER BY mc.display_name, mi.name
    ");
    $stmt->execute();
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group items by category
    $groupedItems = [];
    foreach ($menuItems as $item) {
        $category = $item['category_name'] ?: 'Uncategorized';
        if (!isset($groupedItems[$category])) {
            $groupedItems[$category] = [];
        }
        $groupedItems[$category][] = $item;
    }
    
    echo json_encode([
        'success' => true,
        'menu_items' => $groupedItems
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching menu items: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
