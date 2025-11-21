<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h2>Room Type 2 Availability Fix</h2>";

try {
    // Set consistent collation for the connection
    mysqli_set_charset($con, 'utf8mb4');
    mysqli_query($con, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Check room type 2 details
    $type_check = $con->prepare("SELECT * FROM room_types WHERE room_type_id = 2");
    $type_check->execute();
    $room_type = $type_check->get_result()->fetch_assoc();
    
    if (!$room_type) {
        die("<p>Error: Room type with ID 2 does not exist</p>");
    }
    
    echo "<h3>Room Type: " . htmlspecialchars($room_type['room_type']) . " (ID: 2)</h3>";
    
    // Check for any rooms of type 2 (active or inactive)
    $all_rooms_query = "SELECT * FROM room_numbers WHERE room_type_id = 2";
    $all_rooms_result = $con->query($all_rooms_query);
    $total_rooms = $all_rooms_result->num_rows;
    
    echo "<h4>All Rooms of This Type (Including Inactive):</h4>";
    
    if ($total_rooms > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>Room ID</th><th>Room Number</th><th>Status</th><th>Action</th></tr>";
        while ($room = $all_rooms_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $room['room_number_id'] . "</td>";
            echo "<td>" . htmlspecialchars($room['room_number']) . "</td>";
            echo "<td>" . $room['status'] . "</td>";
            
            if ($room['status'] !== 'active') {
                echo "<td><a href='?activate=" . $room['room_number_id'] . "' class='btn-activate'>Activate</a></td>";
            } else {
                echo "<td>Active</td>";
            }
            
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No rooms found for this room type. Would you like to <a href='room_management.php?add_room=2'>add a room</a>?</p>";
    }
    
    // Handle room activation
    if (isset($_GET['activate'])) {
        $room_id = intval($_GET['activate']);
        $update = $con->prepare("UPDATE room_numbers SET status = 'active' WHERE room_number_id = ? AND room_type_id = 2");
        $update->bind_param("i", $room_id);
        
        if ($update->execute()) {
            echo "<div class='success'>Room #$room_id has been activated. <a href='check_room_availability.php'>Check availability</a></div>";
        } else {
            echo "<div class='error'>Failed to activate room: " . $con->error . "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
    .success { color: green; margin: 10px 0; padding: 10px; background: #e8f5e9; border: 1px solid #c8e6c9; }
    .error { color: #d32f2f; margin: 10px 0; padding: 10px; background: #ffebee; border: 1px solid #ffcdd2; }
    .btn-activate { 
        display: inline-block; 
        padding: 5px 10px; 
        background: #4caf50; 
        color: white; 
        text-decoration: none; 
        border-radius: 3px; 
    }
    .btn-activate:hover { background: #388e3c; }
</style>
