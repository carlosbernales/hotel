<?php
require_once 'db.php';

// Check if room numbers exist for room_type_id = 1
$query = "SELECT rn.*, rt.name as room_type_name 
          FROM room_numbers rn 
          LEFT JOIN room_types rt ON rn.room_type_id = rt.id 
          WHERE rn.room_type_id = 1";

$result = $con->query($query);

echo "<h2>Rooms with room_type_id = 1</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Room Number</th><th>Status</th><th>Floor</th><th>Room Type</th><th>Description</th></tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['room_number_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['floor_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['room_type_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description'] ?? '') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No rooms found for room_type_id = 1</td></tr>";
}

echo "</table>";

// Check for any active bookings
$query = "SELECT brn.*, rn.room_number 
          FROM booking_room_numbers brn
          JOIN room_numbers rn ON brn.room_number_id = rn.room_number_id
          WHERE rn.room_type_id = 1 
          AND brn.check_out_date > NOW()";

$result = $con->query($query);

echo "<h2>Active Bookings for Room Type ID 1</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Room Number</th><th>Check In</th><th>Check Out</th></tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['room_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['check_in_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['check_out_date']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No active bookings found</td></tr>";
}

echo "</table>";
?>
