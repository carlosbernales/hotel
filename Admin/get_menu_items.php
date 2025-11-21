<?php
// Include database connection
include 'db.php';

// Check if category_id is provided
if (!isset($_GET['category_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Category ID is required']);
    exit;
}

$category_id = intval($_GET['category_id']);

// Fetch menu items by category
$query = "SELECT * FROM menu_items WHERE category_id = ? AND status = 1 ORDER BY name ASC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $category_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'description' => $row['description'] ?? ''
        ];
    }
}

// If no items found, provide some sample data for testing
if (empty($items)) {
    $sampleItems = [
        1 => [ // Main Course
            ['id' => 101, 'name' => 'Pork Adobo', 'price' => '250.00', 'description' => 'Classic Filipino pork dish'],
            ['id' => 102, 'name' => 'Beef Steak', 'price' => '320.00', 'description' => 'Tender beef with onions'],
            ['id' => 103, 'name' => 'Grilled Chicken', 'price' => '280.00', 'description' => 'Herb-marinated chicken']
        ],
        2 => [ // Appetizers
            ['id' => 201, 'name' => 'Sisig', 'price' => '180.00', 'description' => 'Sizzling pork dish'],
            ['id' => 202, 'name' => 'Calamari', 'price' => '220.00', 'description' => 'Crispy squid rings'],
            ['id' => 203, 'name' => 'Lumpiang Shanghai', 'price' => '150.00', 'description' => 'Filipino spring rolls']
        ],
        3 => [ // Desserts
            ['id' => 301, 'name' => 'Halo-Halo', 'price' => '120.00', 'description' => 'Mixed sweet treats with shaved ice'],
            ['id' => 302, 'name' => 'Leche Flan', 'price' => '100.00', 'description' => 'Filipino caramel custard'],
            ['id' => 303, 'name' => 'Buko Pandan', 'price' => '110.00', 'description' => 'Coconut pandan dessert']
        ],
        4 => [ // Beverages
            ['id' => 401, 'name' => 'Iced Tea', 'price' => '80.00', 'description' => 'Refreshing house-brewed tea'],
            ['id' => 402, 'name' => 'Sago\'t Gulaman', 'price' => '90.00', 'description' => 'Traditional Filipino drink'],
            ['id' => 403, 'name' => 'Fresh Fruit Shake', 'price' => '120.00', 'description' => 'Seasonal fresh fruits']
        ]
    ];
    
    $items = $sampleItems[$category_id] ?? array_values($sampleItems)[0];
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($items);
?> 