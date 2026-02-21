<?php
require_once 'db_con.php';

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => '',
    'data' => []
];

try {
    // Fetch categories
    $query = "SELECT id, display_name FROM menu_categories ORDER BY display_name ASC";
    $stmt = $pdo->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch menu items
    $query = "SELECT id, category_id, name, description, price, image_path, availability 
              FROM menu_items 
              WHERE availability = 1 
              ORDER BY name ASC";
    $stmt = $pdo->query($query);
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch add-ons
    $query = "SELECT id, menu_item_id, name, price FROM menu_items_addons ORDER BY name ASC";
    $stmt = $pdo->query($query);
    $addons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organize add-ons by menu item
    $addonsByItem = [];
    foreach ($addons as $addon) {
        if (!isset($addonsByItem[$addon['menu_item_id']])) {
            $addonsByItem[$addon['menu_item_id']] = [];
        }
        $addonsByItem[$addon['menu_item_id']][] = $addon;
    }

    // Organize menu items by category
    $menuByCategory = [];
    foreach ($categories as $category) {
        $menuByCategory[$category['id']] = [
            'id' => $category['id'],
            'name' => $category['display_name'],
            'items' => []
        ];
    }

    // Add uncategorized items
    $menuByCategory[0] = [
        'id' => 0,
        'name' => 'Uncategorized',
        'items' => []
    ];

    // Assign items to categories
    foreach ($menuItems as $item) {
        $categoryId = $item['category_id'] ?? 0;
        if (!isset($menuByCategory[$categoryId])) {
            $categoryId = 0; // Default to uncategorized if category not found
        }
        
        $item['addons'] = $addonsByItem[$item['id']] ?? [];
        $menuByCategory[$categoryId]['items'][] = $item;
    }

    // Remove empty categories
    $menuByCategory = array_filter($menuByCategory, function($category) {
        return !empty($category['items']);
    });

    $response['status'] = 'success';
    $response['data'] = array_values($menuByCategory); // Reset array keys

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
