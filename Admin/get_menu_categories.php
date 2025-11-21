<?php
// Include database connection
include 'db.php';

// Fetch menu categories
$query = "SELECT * FROM menu_categories WHERE status = 1 ORDER BY name ASC";
$result = mysqli_query($con, $query);

$categories = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
}

// If no categories, provide some default ones for testing
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'Main Course'],
        ['id' => 2, 'name' => 'Appetizers'],
        ['id' => 3, 'name' => 'Desserts'],
        ['id' => 4, 'name' => 'Beverages']
    ];
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($categories);
?> 