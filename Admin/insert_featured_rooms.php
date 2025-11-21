<?php
require 'db_con.php';

try {
    // First, get the room type IDs
    $sql = "SELECT room_type_id, room_type FROM room_types WHERE room_type IN ('Deluxe Suite', 'Family Room', 'Standard Double')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $room_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($room_types)) {
        die("Error: Room types not found in the database. Please make sure room types are inserted first.");
    }

    // Map room types to their IDs
    $room_type_map = [];
    foreach ($room_types as $room) {
        $room_type_map[$room['room_type']] = $room['room_type_id'];
    }

    // Featured rooms data exactly as shown on the website
    $featured_rooms = [
        [
            'room_type' => 'Deluxe Suite',
            'priority' => 3,
            'price' => 5100,
            'capacity' => 5,
            'features' => [
                'King Bed',
                'Ocean View',
                'Private Balcony',
                'Mini Bar'
            ]
        ],
        [
            'room_type' => 'Family Room',
            'priority' => 2,
            'price' => 4200,
            'capacity' => 4,
            'features' => [
                '2 Queen Beds',
                'City View',
                'Living Area',
                'Kitchenette'
            ]
        ],
        [
            'room_type' => 'Standard Double',
            'priority' => 1,
            'price' => 3200,
            'capacity' => 2,
            'features' => [
                'Queen Bed',
                'Garden View',
                'Work Desk',
                'En-suite Bath'
            ]
        ]
    ];

    // Set dates
    $start_date = date('Y-m-d'); // Today
    $end_date = date('Y-m-d', strtotime('+1 year')); // One year from today

    // First, update room_types table with prices and capacities
    $update_room_type = $pdo->prepare("UPDATE room_types SET price = ?, capacity = ? WHERE room_type_id = ?");

    // Then insert into featured_rooms
    $insert_featured = $pdo->prepare("INSERT INTO featured_rooms (room_type_id, priority, start_date, end_date, created_at) VALUES (?, ?, ?, ?, NOW())");

    // Then insert features into room_features table (if it exists)
    $insert_feature = $pdo->prepare("INSERT INTO room_features (room_type_id, feature) VALUES (?, ?)");

    foreach ($featured_rooms as $room) {
        if (isset($room_type_map[$room['room_type']])) {
            $room_type_id = $room_type_map[$room['room_type']];
            
            // Update room type with price and capacity
            $update_room_type->execute([
                $room['price'],
                $room['capacity'],
                $room_type_id
            ]);

            // Insert into featured_rooms
            $insert_featured->execute([
                $room_type_id,
                $room['priority'],
                $start_date,
                $end_date
            ]);

            // Insert features
            foreach ($room['features'] as $feature) {
                try {
                    $insert_feature->execute([$room_type_id, $feature]);
                } catch (PDOException $e) {
                    // If feature insertion fails, just continue (table might not exist)
                    echo "Note: Could not insert feature '$feature' (table might not exist)<br>";
                }
            }

            echo "Added {$room['room_type']} to featured rooms with price ₱{$room['price']}, capacity {$room['capacity']} guests, and features.<br>";
        } else {
            echo "Warning: Room type {$room['room_type']} not found in database.<br>";
        }
    }

    echo "<br>Featured rooms have been added successfully with all details as shown on the website!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 