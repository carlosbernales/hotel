<?php
require_once 'db.php';

// Drop existing table if it exists
$dropTable = "DROP TABLE IF EXISTS event_spaces";
if (!$con->query($dropTable)) {
    die("Error dropping table: " . $con->error);
}

// Create new table
$createTable = "CREATE TABLE IF NOT EXISTS event_spaces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    space_name VARCHAR(100) NOT NULL,
    Type VARCHAR(50) NOT NULL,
    category VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    description TEXT,
    amenities TEXT,
    image_path VARCHAR(255),
    gallery_images TEXT,
    status ENUM('Available', 'Occupied', 'Maintenance') NOT NULL DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($con->query($createTable)) {
    echo "Table event_spaces recreated successfully!";
} else {
    echo "Error creating table: " . $con->error;
}
