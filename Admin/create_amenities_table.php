<?php
require_once 'db.php';

// Create amenities table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS amenities (
    amenity_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $sql)) {
    die("Error creating amenities table: " . mysqli_error($con));
}

echo "Amenities table created successfully.\n";

// Insert default amenities
$amenities = [
    ['name' => 'Extra Bed', 'price' => 1000.00, 'description' => 'Additional bed for extra guest'],
    ['name' => 'Breakfast', 'price' => 0.00, 'description' => 'Daily breakfast included'],
    ['name' => 'WiFi', 'price' => 0.00, 'description' => 'High-speed internet access'],
    ['name' => 'Parking', 'price' => 0.00, 'description' => 'Free parking space'],
    ['name' => 'Gym Access', 'price' => 0.00, 'description' => 'Access to hotel gym facilities']
];

foreach ($amenities as $amenity) {
    $stmt = mysqli_prepare($con, "INSERT INTO amenities (name, price, description) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sds", $amenity['name'], $amenity['price'], $amenity['description']);
    
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error inserting amenity: " . mysqli_stmt_error($stmt) . "\n";
    } else {
        echo "Added amenity: " . $amenity['name'] . "\n";
    }
}

echo "Default amenities added successfully.\n";
?>
