<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Physical Rooms Setup</h1>";

// Function to check if table exists
function tableExists($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Check if rooms table exists
$roomsTableExists = tableExists($con, 'rooms');
echo "<p>Rooms table exists: " . ($roomsTableExists ? 'Yes' : 'No') . "</p>";

// Drop existing rooms table if it exists (for clean setup)
if ($roomsTableExists) {
    $drop_table = "DROP TABLE rooms";
    if (mysqli_query($con, $drop_table)) {
        echo "<p>Dropped existing rooms table for clean setup.</p>";
        $roomsTableExists = false;
    } else {
        echo "<p>Error dropping rooms table: " . mysqli_error($con) . "</p>";
    }
}

// Create physical rooms table
echo "<h2>Creating Physical Rooms Table:</h2>";

$create_rooms_table = "CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL,
    room_type_id INT NOT NULL,
    description TEXT,
    status ENUM('Available', 'Occupied', 'Maintenance') NOT NULL DEFAULT 'Available',
    floor VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)
)";

if (mysqli_query($con, $create_rooms_table)) {
    echo "<p>Successfully created rooms table.</p>";
    
    // Now insert rooms based on room types
    echo "<h3>Populating Rooms Table with Sample Data:</h3>";
    
    // Get all room types
    $room_types_result = mysqli_query($con, "SELECT room_type_id, room_type FROM room_types ORDER BY room_type_id");
    
    if ($room_types_result && mysqli_num_rows($room_types_result) > 0) {
        $insert_count = 0;
        $floor = 1;
        
        // For each room type, create sample room numbers
        while ($room_type = mysqli_fetch_assoc($room_types_result)) {
            $room_type_id = $room_type['room_type_id'];
            $room_type_name = $room_type['room_type'];
            
            echo "<h4>Creating rooms for: " . htmlspecialchars($room_type_name) . " (ID: $room_type_id)</h4>";
            
            // Create 4 rooms for each room type
            for ($i = 1; $i <= 4; $i++) {
                $room_number = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);
                $description = "Room $room_number - " . $room_type_name;
                
                // Insert the room
                $stmt = $con->prepare("INSERT INTO rooms (room_number, room_type_id, description, floor) 
                              VALUES (?, ?, ?, ?)");
                
                if ($stmt) {
                    $stmt->bind_param("siss", $room_number, $room_type_id, $description, $floor);
                    
                    if ($stmt->execute()) {
                        $insert_count++;
                        echo "<p>Created room $room_number for $room_type_name</p>";
                    } else {
                        echo "<p>Error creating room: " . $stmt->error . "</p>";
                    }
                    
                    $stmt->close();
                } else {
                    echo "<p>Error preparing statement: " . $con->error . "</p>";
                }
            }
            
            // Increment floor for next room type
            $floor++;
        }
        
        echo "<p>Total rooms created: $insert_count</p>";
    } else {
        echo "<p>No room types found to create sample rooms.</p>";
    }
} else {
    echo "<p>Error creating rooms table: " . mysqli_error($con) . "</p>";
}

// Show all rooms
echo "<h2>All Rooms:</h2>";
$result = mysqli_query($con, "SELECT r.*, rt.room_type FROM rooms r 
                             JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                             ORDER BY r.room_type_id, r.room_number");

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Room Number</th><th>Room Type</th><th>Description</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['room_number'] . "</td>";
        echo "<td>" . $row['room_type'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No rooms found.</p>";
}

// Close the database connection
mysqli_close($con);
?> 