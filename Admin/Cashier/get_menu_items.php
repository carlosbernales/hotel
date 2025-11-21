<?php
require_once 'db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    // Validate category ID
    if (!isset($_GET['category_id'])) {
        throw new Exception('Category ID is required');
    }
    
    $categoryId = filter_var($_GET['category_id'], FILTER_VALIDATE_INT);
    if ($categoryId === false || $categoryId <= 0) {
        throw new Exception('Invalid category ID');
    }

    // Prepare SQL query - simplified to match your database structure
    $sql = "SELECT id, name, price 
            FROM menu_items 
            WHERE category_id = ?";
            
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        throw new Exception($connection->error);
    }

    $stmt->bind_param('i', $categoryId);
    
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $result = $stmt->get_result();
    $items = [];

    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'price' => (float)$row['price']
        ];
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'items' => $items
    ]);

} catch (Exception $e) {
    // Log error for debugging
    error_log('Menu Items Error: ' . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load menu items'
    ]);

} finally {
    // Clean up
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($connection)) {
        $connection->close();
    }
} 