<?php
require_once "db.php";

header('Content-Type: application/json');

if (!isset($_GET['action'])) {
    echo json_encode(['error' => 'No action specified']);
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'categories':
        // Fetch all active menu categories
        $sql = "SELECT id, name, display_name FROM menu_categories ORDER BY id";
        $result = mysqli_query($con, $sql);
        
        if (!$result) {
            echo json_encode(['error' => 'Failed to fetch categories: ' . mysqli_error($con)]);
            exit;
        }
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        
        echo json_encode($categories);
        break;
        
    case 'items':
        if (!isset($_GET['category_id'])) {
            echo json_encode(['error' => 'No category ID specified']);
            exit;
        }
        
        $categoryId = mysqli_real_escape_string($con, $_GET['category_id']);
        
        // Fetch menu items for the specified category
        $sql = "SELECT id, name, price, image_path FROM menu_items WHERE category_id = '$categoryId' ORDER BY name";
        $result = mysqli_query($con, $sql);
        
        if (!$result) {
            echo json_encode(['error' => 'Failed to fetch items: ' . mysqli_error($con)]);
            exit;
        }
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        echo json_encode($items);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?> 