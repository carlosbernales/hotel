<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

header('Content-Type: application/json');

// Check database connection
if (!$con) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit;
}

try {
    // Get all available amenities
    $query = "SELECT amenity_id as id, name, icon, price, description FROM amenities ORDER BY name";
    $result = mysqli_query($con, $query);
    
    if ($result === false) {
        throw new Exception('Query failed: ' . mysqli_error($con));
    }
    
    $amenities = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if icon already has a FA prefix
        $icon = $row['icon'];
        if ($icon && !preg_match('/^(fa|fas|far|fab|fal|fad)\s/i', $icon)) {
            // If it doesn't have a prefix, add 'fas' prefix
            $icon = 'fas ' . $icon;
        }
        
        $amenities[] = [
            'id' => (int)$row['id'],
            'name' => htmlspecialchars($row['name']),
            'icon' => $icon,
            'price' => (float)($row['price'] ?? 0),
            'description' => $row['description'] ?? ''
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $amenities,
        'debug' => [
            'count' => count($amenities),
            'query' => $query
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Error in get_amenities.php: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching amenities',
        'error' => $e->getMessage(),
        'debug' => [
            'mysql_error' => mysqli_error($con) ?? 'No error'
        ]
    ]);
}