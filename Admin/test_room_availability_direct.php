<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Test room type ID (change this to test different room types)
$room_type_id = 3; // Testing Family Room

echo "<h2>Room Availability Diagnostic Tool</h2>";
echo "<h3>Testing Room Type ID: $room_type_id</h3>";

try {
    // 1. Check if room type exists
    $type_check = $con->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
    $type_check->bind_param("i", $room_type_id);
    $type_check->execute();
    $room_type = $type_check->get_result()->fetch_assoc();
    
    if (!$room_type) {
        die("<div style='color:red'>Error: Room type with ID $room_type_id does not exist in room_types table</div>");
    }
    
    echo "<h4>Room Type Found:</h4>";
    echo "<pre>" . print_r($room_type, true) . "</pre>";
    
    // 2. Get all rooms of this type from room_numbers
    $rooms_query = $con->prepare("SELECT * FROM room_numbers WHERE room_type_id = ?");
    $rooms_query->bind_param("i", $room_type_id);
    $rooms_query->execute();
    $all_rooms = $rooms_query->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo "<h4>All Rooms in room_numbers for this type:</h4>";
    if (count($all_rooms) > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Room ID</th><th>Room Number</th><th>Status</th></tr>";
        foreach ($all_rooms as $room) {
            echo "<tr>";
            echo "<td>" . $room['room_number_id'] . "</td>";
            echo "<td>" . htmlspecialchars($room['room_number']) . "</td>";
            echo "<td>" . htmlspecialchars($room['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No rooms found in room_numbers for this room type.</p>";
    }
    
    // 3. Count active rooms
    $active_rooms = array_filter($all_rooms, function($room) {
        return strtolower($room['status'] ?? '') === 'active';
    });
    
    echo "<h4>Active Rooms: " . count($active_rooms) . "</h4>";
    
    if (count($active_rooms) === 0) {
        echo "<div style='color:red'>No active rooms found for this room type. This is why you're seeing the 'no available rooms' message.</div>";
    }
    
    // 4. Check for any active bookings that might be blocking rooms
    $bookings_query = $con->prepare("
        SELECT b.*, rn.room_number 
        FROM bookings b 
        JOIN room_numbers rn ON b.room_number = rn.room_number
        WHERE rn.room_type_id = ?
        AND b.status IN ('confirmed', 'checked_in')
        AND b.check_out >= CURDATE()
        ORDER BY b.check_in
    ");
    $bookings_query->bind_param("i", $room_type_id);
    $bookings_query->execute();
    $active_bookings = $bookings_query->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo "<h4>Active/Upcoming Bookings: " . count($active_bookings) . "</h4>";
    if (count($active_bookings) > 0) {
        echo "<pre>" . print_r($active_bookings, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
}

echo "<h3>SQL Query for Room Availability Check:</h3>";
$sql = "SELECT COUNT(*) as available_count 
        FROM room_numbers 
        WHERE room_type_id = $room_type_id 
        AND status = 'active'";
        
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

// Run the query to see the exact count
$result = $con->query($sql);
$count = $result->fetch_assoc()['available_count'] ?? 0;
echo "<h4>Query Result: $count available rooms</h4>";

// Show all room_types for reference
$room_types = $con->query("SELECT * FROM room_types");
echo "<h3>All Room Types in Database:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Room Type</th><th>Price</th><th>Capacity</th><th>Available</th></tr>";
while ($rt = $room_types->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $rt['room_type_id'] . "</td>";
    echo "<td>" . htmlspecialchars($rt['room_type']) . "</td>";
    echo "<td>" . $rt['price_per_night'] . "</td>";
    echo "<td>" . $rt['capacity'] . "</td>";
    echo "<td>" . $rt['available_rooms'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>

<style>
table {
    border-collapse: collapse;
    margin: 10px 0;
}
td, th {
    padding: 5px 10px;
    border: 1px solid #ccc;
}
</style>
