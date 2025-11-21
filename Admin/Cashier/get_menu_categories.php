        <?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, name, display_name FROM menu_categories ORDER BY display_name";
    $result = $connection->query($sql);
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'display_name' => $row['display_name']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching categories: ' . $e->getMessage()
    ]);
}

$connection->close(); 