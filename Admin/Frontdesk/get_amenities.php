<?php
require_once 'includes/init.php';

// Get all amenities
$query = "SELECT * FROM amenities ORDER BY name";
$result = $con->query($query);
$amenities = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Check if icon already has a FA prefix (fa, fas, far)
        $icon = $row['icon'];
        if ($icon && !preg_match('/^(fa|fas|far|fab|fal|fad)\s/i', $icon)) {
            // If it doesn't have a prefix, add 'fas' prefix
            $icon = 'fas ' . $icon;
        }
        
        $amenities[] = [
            'amenity_id' => $row['amenity_id'],
            'name' => $row['name'],
            'icon' => $icon
        ];
    }
}

echo json_encode($amenities); 