<?php
require 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    mysqli_begin_transaction($con);

    // First, let's clear existing data to avoid duplicates
    mysqli_query($con, "TRUNCATE TABLE rooms");
    mysqli_query($con, "TRUNCATE TABLE room_types");

    // Initialize room types
    $room_types = [
        [
            'type' => 'Standard Double Room',
            'price' => 3000.00,
            'description' => 'Comfortable room with basic amenities',
            'beds' => '2 Single Beds',
            'rating' => 4.0,
            'image' => 'standard_double.jpg'
        ],
        [
            'type' => 'Deluxe Family Room',
            'price' => 4500.00,
            'description' => 'Spacious room perfect for families',
            'beds' => '1 Queen Bed, 1 Single Bed',
            'rating' => 4.5,
            'image' => 'deluxe_family.jpg'
        ],
        [
            'type' => 'Family Room',
            'price' => 4000.00,
            'description' => 'Comfortable room for the whole family',
            'beds' => '1 Queen Bed, 2 Single Beds',
            'rating' => 4.0,
            'image' => 'family_room.jpg'
        ]
    ];

    // Insert room types
    $stmt = mysqli_prepare($con, "INSERT INTO room_types (room_type, price, description, beds, rating, image) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($room_types as $type) {
        mysqli_stmt_bind_param($stmt, "sdssds", 
            $type['type'],
            $type['price'],
            $type['description'],
            $type['beds'],
            $type['rating'],
            $type['image']
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error inserting room type: " . mysqli_error($con));
        }
        
        // Get the inserted room type ID
        $room_type_id = mysqli_insert_id($con);
        
        // Initialize room inventory for this room type
        $total_rooms = ($type['type'] === 'Standard Double Room') ? 10 : 
                      (($type['type'] === 'Deluxe Family Room') ? 8 : 6);
        
        // Insert room inventory
        $room_stmt = mysqli_prepare($con, "INSERT INTO rooms (room_type_id, total_rooms, available_rooms, status) VALUES (?, ?, ?, 'Available')");
        mysqli_stmt_bind_param($room_stmt, "iii", $room_type_id, $total_rooms, $total_rooms);
        
        if (!mysqli_stmt_execute($room_stmt)) {
            throw new Exception("Error inserting room inventory: " . mysqli_error($con));
        }
    }

    mysqli_commit($con);
    
    // Verify the data
    echo "<h3>Room Types:</h3>";
    $result = mysqli_query($con, "SELECT * FROM room_types");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Room Type: {$row['room_type']}<br>";
        echo "Price: ₱" . number_format($row['price'], 2) . "<br>";
        echo "Beds: {$row['beds']}<br>";
        echo "Rating: {$row['rating']}<br><br>";
    }
    
    echo "<h3>Room Inventory:</h3>";
    $result = mysqli_query($con, "
        SELECT rt.room_type, r.total_rooms, r.available_rooms, r.status 
        FROM rooms r 
        JOIN room_types rt ON r.room_type_id = rt.room_type_id
    ");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Room Type: {$row['room_type']}<br>";
        echo "Total Rooms: {$row['total_rooms']}<br>";
        echo "Available Rooms: {$row['available_rooms']}<br>";
        echo "Status: {$row['status']}<br><br>";
    }

    echo "Room initialization completed successfully!";

} catch (Exception $e) {
    mysqli_rollback($con);
    echo "Error: " . $e->getMessage();
} finally {
    mysqli_close($con);
}
?> 