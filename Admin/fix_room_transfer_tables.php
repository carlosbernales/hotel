<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Room Transfer System Database Fix</h1>";

// Function to check if a column exists in a table
function columnExists($con, $table, $column) {
    $result = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

// Function to check if a table exists
function tableExists($con, $table) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    return mysqli_num_rows($result) > 0;
}

// 1. Check and fix room_numbers table
echo "<h2>Checking room_numbers table...</h2>";
if (!tableExists($con, 'room_numbers')) {
    $create_room_numbers = "CREATE TABLE room_numbers (
        room_number_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        room_type_id INT(11) NOT NULL,
        room_number VARCHAR(20) NOT NULL,
        floor_number INT(11) DEFAULT NULL,
        status ENUM('active', 'maintenance', 'occupied') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($con, $create_room_numbers)) {
        echo "<p style='color:green'>✓ Created room_numbers table</p>";
    } else {
        echo "<p style='color:red'>✗ Error creating room_numbers table: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>room_numbers table exists</p>";
    
    // Check status column values
    $update_status = "ALTER TABLE room_numbers MODIFY COLUMN status 
                      ENUM('active', 'maintenance', 'occupied') DEFAULT 'active'";
    if (mysqli_query($con, $update_status)) {
        echo "<p style='color:green'>✓ Updated status column values</p>";
    }
}

// 2. Check and fix room_transfers table
echo "<h2>Checking room_transfers table...</h2>";
if (!tableExists($con, 'room_transfers')) {
    $create_transfers = "CREATE TABLE room_transfers (
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
    
    if (mysqli_query($con, $create_transfers)) {
        echo "<p style='color:green'>✓ Created room_transfers table</p>";
    } else {
        echo "<p style='color:red'>✗ Error creating room_transfers table: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>room_transfers table exists</p>";
}

// 3. Check and fix bookings table
echo "<h2>Checking bookings table...</h2>";
if (!columnExists($con, 'bookings', 'room_type_id')) {
    $add_room_type = "ALTER TABLE bookings ADD COLUMN room_type_id INT(11) DEFAULT NULL,
                      ADD FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)";
    if (mysqli_query($con, $add_room_type)) {
        echo "<p style='color:green'>✓ Added room_type_id column to bookings</p>";
    }
}

if (!columnExists($con, 'bookings', 'room_number')) {
    $add_room_number = "ALTER TABLE bookings ADD COLUMN room_number VARCHAR(20) DEFAULT NULL";
    if (mysqli_query($con, $add_room_number)) {
        echo "<p style='color:green'>✓ Added room_number column to bookings</p>";
    }
}

// 4. Check current room statuses
echo "<h2>Current Room Statuses:</h2>";
$room_status_query = "SELECT room_number, room_type_id, status FROM room_numbers ORDER BY room_number";
$result = mysqli_query($con, $room_status_query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Room Number</th><th>Room Type ID</th><th>Status</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['room_type_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No rooms found in the database.</p>";
}

// Update room status values to match ENUM
$update_room_status = "UPDATE room_numbers 
                      SET status = CASE 
                          WHEN LOWER(status) IN ('available', 'active') THEN 'active'
                          WHEN LOWER(status) = 'maintenance' THEN 'maintenance'
                          WHEN LOWER(status) = 'booked' THEN 'booked'
                          WHEN LOWER(status) IN ('occupied', 'checkedin') THEN 'occupied'
                          ELSE 'active'
                      END";

if (mysqli_query($con, $update_room_status)) {
    echo "<p>Successfully updated room status values.</p>";
} else {
    echo "<p>Error updating room status values: " . mysqli_error($con) . "</p>";
}

echo "<p><a href='checked_in.php'>Return to Checked In Page</a></p>";

mysqli_close($con);
?> 