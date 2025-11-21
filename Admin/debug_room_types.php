<?php
require_once 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get all room types with their IDs
$query = "SELECT * FROM room_types";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error: " . mysqli_error($con));
}

echo "<h2>Room Types in Database</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Room Type</th><th>Description</th><th>Price</th><th>Capacity</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['room_type_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['room_type']) . "</td>";
    echo "<td>" . htmlspecialchars($row['description'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($row['price_per_night'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($row['capacity'] ?? 'N/A') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show the last booking
$last_booking = mysqli_query($con, "SELECT * FROM bookings ORDER BY booking_id DESC LIMIT 1");
if ($last_booking && $last_booking->num_rows > 0) {
    $booking = $last_booking->fetch_assoc();
    echo "<h2>Last Booking</h2>";
    echo "<pre>";
    print_r($booking);
    echo "</pre>";
    
    // Get the room type for this booking
    $room_type_query = mysqli_query($con, "SELECT room_type FROM room_types WHERE room_type_id = " . (int)$booking['room_type_id']);
    if ($room_type_query && $room_type = $room_type_query->fetch_assoc()) {
        echo "<p>Room Type for this booking: " . htmlspecialchars($room_type['room_type']) . "</p>";
    }
}

// Show POST data if any
echo "<h2>Last POST Data</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Show error log
echo "<h2>Error Log</h2>";
$log_file = 'booking_errors.log';
if (file_exists($log_file)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($log_file)) . "</pre>";
} else {
    echo "No error log found.";
}
?>
