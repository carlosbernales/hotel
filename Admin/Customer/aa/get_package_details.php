<?php
require_once 'db_con.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$packageName = $data['packageName'] ?? '';

try {
    // Prepare and execute query
    $stmt = $pdo->prepare("SELECT * FROM event_packages WHERE name = ?");
    $stmt->execute([$packageName]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($package) {
        // Parse menu items from the database string
        $menuItems = [];
        if (!empty($package['menu_items'])) {
            // Split the menu items string into an array
            $menuItemsList = explode(', ', $package['menu_items']);
            
            // Categorize menu items
            foreach ($menuItemsList as $item) {
                if (strpos($item, 'Appetizer') !== false) {
                    $menuItems['Appetizers'][] = $item;
                } elseif (strpos($item, 'Pasta') !== false) {
                    $menuItems['Pasta'][] = $item;
                } elseif (strpos($item, 'Main') !== false || strpos($item, 'Wagyu') !== false) {
                    $menuItems['Mains'][] = $item;
                } elseif (strpos($item, 'Salad') !== false || strpos($item, 'Rice') !== false) {
                    $menuItems['Sides'][] = $item;
                } elseif (strpos($item, 'Dessert') !== false) {
                    $menuItems['Desserts'][] = $item;
                } elseif (strpos($item, 'Drink') !== false) {
                    $menuItems['Drinks'][] = $item;
                }
            }
        }

        // Format package details
        $details = [
            ['icon' => 'clock', 'text' => ($package['time_limit'] ?? '5') . '-hour venue rental'],
            ['icon' => 'users', 'text' => 'Up to ' . ($package['max_pax'] ?? '50') . ' guests'],
            ['icon' => 'chair', 'text' => 'Tables and Tiffany chairs'],
            ['icon' => 'snowflake', 'text' => 'Air-conditioned venue']
        ];

        // Format notes
        $notes = [
            'Operating hours: 6:30 AM to 11:00 PM',
            'Exclusive use of air-conditioned tent area for ' . ($package['time_limit'] ?? '5') . ' hours',
            'Corkage fee applies for outside food and beverages',
            '50% non-refundable down payment required',
            'Extension rate: ₱2,000/hour (₱3,000/hour after midnight)'
        ];

        // Add any additional notes from the database
        if (!empty($package['notes'])) {
            $notes[] = $package['notes'];
        }

        // Format response
        $response = [
            'status' => 'success',
            'package' => [
                'name' => $package['name'],
                'price' => floatval($package['price']),
                'description' => $package['description'],
                'image_path' => $package['image_path'] ?? 'images/hall.jpg',
                'menu_items' => $menuItems,
                'details' => $details,
                'notes' => $notes,
                'status' => $package['status'] ?? 'Available',
                'is_venue_only' => $package['name'] === 'Venue Rental Only'
            ]
        ];

    } else {
        $response = [
            'status' => 'error',
            'message' => 'Package not found'
        ];
    }
} catch (PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ];
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response); 