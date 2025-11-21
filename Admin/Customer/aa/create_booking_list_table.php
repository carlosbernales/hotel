<?php
require 'db_con.php';

try {
    // Create booking_list_items table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS booking_list_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        room_type_id INT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (room_type_id)
    )";
    
    $pdo->exec($sql);
    echo "Booking list table created or already exists.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?> 