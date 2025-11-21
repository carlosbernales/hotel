<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create room_transfers table if it doesn't exist
$create_transfers_table = "CREATE TABLE IF NOT EXISTS room_transfers (
    transfer_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    old_room_type_id INT NOT NULL,
    new_room_type_id INT NOT NULL,
    old_room_number VARCHAR(20),
    new_room_number VARCHAR(20) NOT NULL,
    transfer_reason TEXT NOT NULL,
    transfer_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (old_room_type_id) REFERENCES room_types(room_type_id),
    FOREIGN KEY (new_room_type_id) REFERENCES room_types(room_type_id)
)";

if (mysqli_query($con, $create_transfers_table)) {
    echo "<h3>Success: Room transfers table created successfully.</h3>";
    echo "<p>You should now be able to transfer rooms properly.</p>";
    echo "<p><a href='checked_in.php'>Return to Checked In Page</a></p>";
} else {
    echo "<h3>Error creating room transfers table:</h3> " . mysqli_error($con);
}

mysqli_close($con); 