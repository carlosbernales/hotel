<?php
require 'db_con.php';

try {
    // Create booking_rooms table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS booking_rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        room_type_id INT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (booking_id),
        INDEX (room_type_id)
    )";
    
    $pdo->exec($sql);
    echo "Booking rooms table created or already exists.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?> 