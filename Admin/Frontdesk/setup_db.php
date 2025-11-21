<?php
require 'db.php';

// Create room_type table
$query = "CREATE TABLE IF NOT EXISTS room_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    amenities TEXT,
    image_url VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 5.0
)";
mysqli_query($con, $query) or die(mysqli_error($con));

// Create room table
$query = "CREATE TABLE IF NOT EXISTS room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(10) NOT NULL UNIQUE,
    room_type VARCHAR(50) NOT NULL,
    status ENUM('Available', 'Occupied', 'Maintenance') DEFAULT 'Available'
)";
mysqli_query($con, $query) or die(mysqli_error($con));

// Insert sample room types
$room_types = [
    [
        'room_type' => 'Standard Double Room',
        'description' => 'Cozy and comfortable room with 2 single beds, perfect for friends or business travelers.',
        'price' => 1500,
        'capacity' => 2,
        'amenities' => 'Air Conditioning,Private Bathroom,TV,WiFi,Hot Shower',
        'image_url' => 'Capstone/images/double.jpg',
        'rating' => 4.5
    ],
    [
        'room_type' => 'Deluxe Family Room',
        'description' => 'Spacious room with 1 queen bed and 1 single bed, ideal for small families.',
        'price' => 2000,
        'capacity' => 4,
        'amenities' => 'Air Conditioning,Private Bathroom,TV,WiFi,Hot Shower,Mini Fridge',
        'image_url' => 'Capstone/images/garden.jpg',
        'rating' => 4.8
    ],
    [
        'room_type' => 'Family Room',
        'description' => 'Our largest room with 1 queen bed and 2 single beds, perfect for families or groups.',
        'price' => 2500,
        'capacity' => 5,
        'amenities' => 'Air Conditioning,Private Bathroom,TV,WiFi,Hot Shower,Mini Fridge,Balcony',
        'image_url' => 'Capstone/images/family.jpg',
        'rating' => 5.0
    ]
];

// Clear existing room types
mysqli_query($con, "TRUNCATE TABLE room_type");

// Insert room types
foreach ($room_types as $type) {
    $query = "INSERT INTO room_type (room_type, description, price, capacity, amenities, image_url, rating) 
              VALUES (
                  '{$type['room_type']}',
                  '{$type['description']}',
                  {$type['price']},
                  {$type['capacity']},
                  '{$type['amenities']}',
                  '{$type['image_url']}',
                  {$type['rating']}
              )";
    mysqli_query($con, $query) or die(mysqli_error($con));
}

// Clear existing rooms
mysqli_query($con, "TRUNCATE TABLE room");

// Insert sample rooms
$rooms = [
    ['101', 'Standard Double Room'],
    ['102', 'Standard Double Room'],
    ['201', 'Deluxe Family Room'],
    ['202', 'Deluxe Family Room'],
    ['301', 'Family Room'],
    ['302', 'Family Room']
];

foreach ($rooms as $room) {
    $query = "INSERT INTO room (room_id, room_type) VALUES ('{$room[0]}', '{$room[1]}')";
    mysqli_query($con, $query) or die(mysqli_error($con));
}

echo "Database setup complete!";
?>
