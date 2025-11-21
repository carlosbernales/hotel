<?php
require_once 'db.php';

// Create room_transfers table if it doesn't exist
$create_transfers_table = "CREATE TABLE IF NOT EXISTS room_transfers (
    transfer_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) NOT NULL,
    old_room_type_id VARCHAR(50) NOT NULL,
    new_room_type_id VARCHAR(50) NOT NULL,
    transfer_reason TEXT NOT NULL,
    price_difference DECIMAL(10,2) NOT NULL,
    transfer_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $create_transfers_table)) {
    echo "Room transfers table created successfully or already exists.";
} else {
    echo "Error creating room transfers table: " . mysqli_error($con);
}

mysqli_close($con); 