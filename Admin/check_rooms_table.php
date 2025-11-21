<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Room Database Structure Check</h1>";

// Function to check if table exists
function tableExists($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Check if rooms table exists
$roomsTableExists = tableExists($con, 'rooms');
echo "<p>Rooms table exists: " . ($roomsTableExists ? 'Yes' : 'No') . "</p>";

// Describe room_types table
echo "<h2>Room Types Structure:</h2>";
$result = mysqli_query($con, "DESCRIBE room_types");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error describing room_types table: " . mysqli_error($con) . "</p>";
}

// Get room_types data
echo "<h2>Room Types Data:</h2>";
$result = mysqli_query($con, "SELECT * FROM room_types");
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'><tr>";
        $first_row = mysqli_fetch_assoc($result);
        foreach ($first_row as $key => $value) {
            echo "<th>" . $key . "</th>";
        }
        echo "</tr>";
        
        // Reset pointer and show all rows
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No room types found in the database.</p>";
    }
} else {
    echo "<p>Error getting room_types data: " . mysqli_error($con) . "</p>";
}

// Create physical rooms table if it doesn't exist
if (!$roomsTableExists) {
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
        FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($con, $create_rooms_table)) {
        echo "<p>Successfully created rooms table.</p>";
        
        // Now insert some sample rooms based on room types
        echo "<h3>Populating Rooms Table with Sample Data:</h3>";
        
        // Get all room types
        $room_types_result = mysqli_query($con, "SELECT room_type_id, room_type FROM room_types");
        
        if ($room_types_result && mysqli_num_rows($room_types_result) > 0) {
            $insert_count = 0;
            
            // For each room type, create sample room numbers
            while ($room_type = mysqli_fetch_assoc($room_types_result)) {
                $room_type_id = $room_type['room_type_id'];
                $room_type_name = $room_type['room_type'];
                
                // Create 4 rooms for each room type
                for ($i = 1; $i <= 4; $i++) {
                    // Generate room number (e.g., D101 for Double Occupancy room 1)
                    $prefix = strtoupper(substr($room_type_name, 0, 1));
                    $room_number = $prefix . "10" . $i;
                    
                    // Set floor
                    $floor = "1";
                    
                    // Insert the room
                    $insert_sql = "INSERT INTO rooms (room_number, room_type_id, description, floor) 
                                  VALUES ('$room_number', $room_type_id, 'Room $i of type $room_type_name', '$floor')";
                    
                    if (mysqli_query($con, $insert_sql)) {
                        $insert_count++;
                        echo "<p>Created room $room_number for $room_type_name</p>";
                    } else {
                        echo "<p>Error creating room: " . mysqli_error($con) . "</p>";
                    }
                }
            }
            
            echo "<p>Total rooms created: $insert_count</p>";
        } else {
            echo "<p>No room types found to create sample rooms.</p>";
        }
    } else {
        echo "<p>Error creating rooms table: " . mysqli_error($con) . "</p>";
    }
} else {
    // Show existing rooms data
    echo "<h2>Physical Rooms Data:</h2>";
    $result = mysqli_query($con, "SELECT r.*, rt.room_type FROM rooms r 
                                 JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                 ORDER BY r.room_type_id, r.room_number");
    
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1'><tr><th>ID</th><th>Room Number</th><th>Room Type</th><th>Status</th><th>Floor</th><th>Description</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['room_number'] . "</td>";
                echo "<td>" . $row['room_type'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['floor'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No physical rooms found in the database.</p>";
        }
    } else {
        echo "<p>Error getting rooms data: " . mysqli_error($con) . "</p>";
    }
}

// Close the database connection
mysqli_close($con);
?> 