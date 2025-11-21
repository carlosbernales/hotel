<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'categories':
        // Get all menu categories from existing table
        $sql = "SELECT id, name, display_name FROM menu_categories ORDER BY id";
        $result = mysqli_query($con, $sql);
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'display_name' => $row['display_name']
            ];
        }
        
        echo json_encode($categories);
        break;

    case 'items':
        $categoryId = $_GET['category_id'] ?? 0;
        
        // Get menu items for the selected category using existing structure
        $sql = "SELECT id, category_id, name, price, image_path 
               FROM menu_items 
               WHERE category_id = ?
               ORDER BY name";
        
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image_path' => $row['image_path']
            ];
        }
        
        echo json_encode($items);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?> 