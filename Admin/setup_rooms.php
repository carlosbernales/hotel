<?php
require 'db_con.php';

try {
    // First, create room_types if they don't exist
    $room_types_data = [
        [
            'room_type' => 'Deluxe Suite',
            'price' => 5100,
            'capacity' => 5,
            'beds' => 'King Bed',
            'description' => 'Luxurious suite with ocean view and private balcony',
            'image' => 'images/5.jpg'
        ],
        [
            'room_type' => 'Family Room',
            'price' => 4200,
            'capacity' => 4,
            'beds' => '2 Queen Beds',
            'description' => 'Spacious room perfect for families with kitchenette',
            'image' => 'images/3.jpg'
        ],
        [
            'room_type' => 'Standard Double',
            'price' => 3200,
            'capacity' => 2,
            'beds' => 'Queen Bed',
            'description' => 'Comfortable room with garden view',
            'image' => 'images/double.jpg'
        ]
    ];

    // Insert or update room types
    $stmt = $pdo->prepare("INSERT INTO room_types (room_type, price, capacity, beds, description, image) 
                          VALUES (?, ?, ?, ?, ?, ?) 
                          ON DUPLICATE KEY UPDATE 
                          price = VALUES(price),
                          capacity = VALUES(capacity),
                          beds = VALUES(beds),
                          description = VALUES(description),
                          image = VALUES(image)");

    foreach ($room_types_data as $room) {
        $stmt->execute([
            $room['room_type'],
            $room['price'],
            $room['capacity'],
            $room['beds'],
            $room['description'],
            $room['image']
        ]);
        echo "Room type {$room['room_type']} has been inserted/updated.<br>";
    }

    // Now get the room type IDs
    $room_types = [];
    $stmt = $pdo->prepare("SELECT room_type_id, room_type FROM room_types WHERE room_type IN (?, ?, ?)");
    $stmt->execute(array_column($room_types_data, 'room_type'));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $room_types[$row['room_type']] = $row['room_type_id'];
    }

    // Clear existing featured rooms
    $pdo->exec("DELETE FROM featured_rooms");
    echo "Cleared existing featured rooms.<br>";

    // Set dates
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+1 year')); // Featured for one year

    // Featured rooms data
    $featured_rooms = [
        [
            'room_type' => 'Deluxe Suite',
            'priority' => 3
        ],
        [
            'room_type' => 'Family Room',
            'priority' => 2
        ],
        [
            'room_type' => 'Standard Double',
            'priority' => 1
        ]
    ];

    // Insert featured rooms
    $stmt = $pdo->prepare("INSERT INTO featured_rooms (room_type_id, priority, start_date, end_date, created_at) VALUES (?, ?, ?, ?, NOW())");
    
    foreach ($featured_rooms as $room) {
        if (isset($room_types[$room['room_type']])) {
            $stmt->execute([
                $room_types[$room['room_type']],
                $room['priority'],
                $start_date,
                $end_date
            ]);
            echo "Added {$room['room_type']} to featured rooms.<br>";
        } else {
            echo "Warning: Room type {$room['room_type']} not found in database.<br>";
        }
    }

    echo "<br>Setup completed successfully! You can now check your homepage to see the featured rooms.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 