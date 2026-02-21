<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
        
        // Get menu items for the specified category
        $stmt = $conn->prepare("
            SELECT m.*, c.name as category_name 
            FROM menu_items m
            JOIN menu_categories c ON m.category_id = c.id
            WHERE c.name = :category
        ");
        
        $stmt->execute(['category' => $category]);
        $menuItems = [];
        
        while ($item = $stmt->fetch()) {
            // Get addons for this menu item
            $addonStmt = $conn->prepare("
                SELECT name, price 
                FROM menu_item_addons 
                WHERE menu_item_id = :item_id
            ");
            
            $addonStmt->execute(['item_id' => $item['id']]);
            $addons = $addonStmt->fetchAll();
            
            $item['addons'] = $addons;
            $menuItems[] = $item;
        }
        
        echo json_encode(['status' => 'success', 'data' => $menuItems]);
    } else {
        // Get all categories
        $stmt = $conn->query("SELECT * FROM menu_categories ORDER BY id");
        $categories = $stmt->fetchAll();
        
        echo json_encode(['status' => 'success', 'data' => $categories]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
?> 