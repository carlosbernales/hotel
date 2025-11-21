<?php
require 'Customer/aa/db_con.php';

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test room_type_id (change this to the one you're testing with)
$room_type_id = 3; // Family room

echo "<h2>Testing Room Type ID: $room_type_id</h2>";

// Test 1: Check if room type exists in room_types table
$sql = "SELECT * FROM room_types WHERE room_type_id = ?";
$stmt = mysqli_prepare($con, $sql);
if (!$stmt) die("Prepare failed: " . mysqli_error($con));

mysqli_stmt_bind_param($stmt, 'i', $room_type_id);
if (!mysqli_stmt_execute($stmt)) die("Execute failed: " . mysqli_stmt_error($stmt));

$result = mysqli_stmt_get_result($stmt);
$room_type = mysqli_fetch_assoc($result);

echo "<h3>1. Room Type Check:</h3>";
if ($room_type) {
    echo "Found room type: " . htmlspecialchars($room_type['room_type']) . "<br>";
    echo "Room Type ID: " . $room_type['room_type_id'] . "<br>";
} else {
    die("Error: Room type with ID $room_type_id not found in room_types table");
}

// Test 2: Check available rooms in room_numbers table
echo "<h3>2. Checking room_numbers table:</h3>";
$sql = "SELECT * FROM room_numbers WHERE room_type_id = ?";
$stmt = mysqli_prepare($con, $sql);
if (!$stmt) die("Prepare failed: " . mysqli_error($con));

mysqli_stmt_bind_param($stmt, 'i', $room_type_id);
if (!mysqli_stmt_execute($stmt)) die("Execute failed: " . mysqli_stmt_error($stmt));

$result = mysqli_stmt_get_result($stmt);
$all_rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (count($all_rooms) > 0) {
    echo "<h4>All Rooms in room_numbers for this type:</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Room Number</th><th>Status</th><th>Room Type ID</th></tr>";
    foreach ($all_rooms as $room) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($room['room_number']) . "</td>";
        echo "<td>" . htmlspecialchars($room['status']) . "</td>";
        echo "<td>" . htmlspecialchars($room['room_type_id']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count active rooms
    $active_rooms = array_filter($all_rooms, function($room) {
        return isset($room['status']) && strtolower($room['status']) === 'active';
    });
    
    echo "<p>Total active rooms: " . count($active_rooms) . "</p>";
} else {
    echo "<p>No rooms found in room_numbers for this room type.</p>";
}

// Test 3: Check for any active bookings
echo "<h3>3. Checking Active Bookings:</h3>";
try {
    // First, let's check the structure of the bookings table
    $result = mysqli_query($con, "DESCRIBE bookings");
    if (!$result) {
        throw new Exception("Couldn't describe bookings table: " . mysqli_error($con));
    }
    
    echo "<h4>Bookings Table Structure:</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Now check for active bookings
    $sql = "SELECT * FROM bookings 
            WHERE room_type_id = ? 
            AND status NOT IN ('cancelled', 'completed')
            AND check_out >= CURDATE()";
            
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) throw new Exception("Prepare failed: " . mysqli_error($con));
    
    mysqli_stmt_bind_param($stmt, 'i', $room_type_id);
    if (!mysqli_stmt_execute($stmt)) throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    
    $result = mysqli_stmt_get_result($stmt);
    $active_bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    echo "<h4>Active Bookings for This Room Type:</h4>";
    if (count($active_bookings) > 0) {
        echo "<pre>";
        print_r($active_bookings);
        echo "</pre>";
    } else {
        echo "No active bookings found for this room type.<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color:red'>Error checking bookings: " . $e->getMessage() . "</div>";
}

// Close connection
mysqli_close($con);
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
