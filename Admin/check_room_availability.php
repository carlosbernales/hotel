<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h2>Room Type 2 Availability Check</h2>";

try {
    // Check if room type 2 exists
    $type_check = $con->prepare("SELECT * FROM room_types WHERE room_type_id = 2");
    $type_check->execute();
    $room_type = $type_check->get_result()->fetch_assoc();
    
    if (!$room_type) {
        die("<p>Error: Room type with ID 2 does not exist</p>");
    }
    
    echo "<h3>Room Type: " . htmlspecialchars($room_type['room_type']) . " (ID: 2)</h3>";
    
    // Get all active rooms of type 2
    $rooms_query = "SELECT * FROM room_numbers 
                   WHERE room_type_id = 2 
                   AND status = 'active'";
    $rooms_result = $con->query($rooms_query);
    
    $total_rooms = $rooms_result->num_rows;
    echo "<p>Total active rooms of this type: " . $total_rooms . "</p>";
    
    if ($total_rooms > 0) {
        echo "<h4>Rooms:</h4><ul>";
        while ($room = $rooms_result->fetch_assoc()) {
            echo "<li>Room #" . htmlspecialchars($room['room_number']) . " (ID: " . $room['room_number_id'] . ") - " . $room['status'] . "</li>";
        }
        echo "</ul>";
    }
    
    // Check for any active bookings that might be blocking rooms
    $bookings_query = "SELECT b.* FROM bookings b 
                     INNER JOIN room_numbers rn ON b.room_number = rn.room_number
                     WHERE rn.room_type_id = 2
                     AND b.status IN ('confirmed', 'checked_in')
                     AND b.check_out >= CURDATE()
                     ORDER BY b.check_in";
    $bookings_result = $con->query($bookings_query);
    
    $active_bookings = $bookings_result->num_rows;
    echo "<h4>Active/Upcoming Bookings for Room Type 2: " . $active_bookings . "</h4>";
    
    if ($active_bookings > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Booking ID</th><th>Room #</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr>";
        while ($booking = $bookings_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $booking['booking_id'] . "</td>";
            echo "<td>" . htmlspecialchars($booking['room_number']) . "</td>";
            echo "<td>" . $booking['check_in'] . "</td>";
            echo "<td>" . $booking['check_out'] . "</td>";
            echo "<td>" . $booking['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check available rooms using the same query as get_available_rooms.php
    $availability_query = "SELECT rn.room_number_id, rn.room_number 
                         FROM room_numbers rn
                         WHERE rn.room_type_id = 2
                         AND rn.status = 'active'
                         AND NOT EXISTS (
                             SELECT 1 
                             FROM bookings b 
                             WHERE b.room_number = rn.room_number
                             AND b.status IN ('confirmed', 'checked_in')
                             AND b.check_in <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                             AND b.check_out >= CURDATE()
                         )";
    $available_result = $con->query($availability_query);
    $available_count = $available_result->num_rows;
    
    echo "<h3>Available Rooms for Check-in Today or Tomorrow: " . $available_count . "</h3>";
    
    if ($available_count > 0) {
        echo "<ul>";
        while ($room = $available_result->fetch_assoc()) {
            echo "<li>Room #" . htmlspecialchars($room['room_number']) . " (ID: " . $room['room_number_id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No rooms available for check-in today or tomorrow.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    error_log("Room availability check error: " . $e->getMessage());
}

// Close connection
if (isset($con) && $con) {
    $con->close();
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
</style>php
require_once 'db.php';

if (!isset($_POST['room_type_id'])) {
    echo json_encode(['error' => 'Room type ID not provided']);
    exit;
}

$room_type_id = mysqli_real_escape_string($con, $_POST['room_type_id']);

// Query to get available rooms
$query = "SELECT r.available_rooms 
          FROM rooms r 
          WHERE r.room_type_id = '$room_type_id'";

$result = mysqli_query($con, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'available_rooms' => $row['available_rooms']
    ]);
} else {
    echo json_encode([
        'error' => 'Failed to fetch room availability',
        'available_rooms' => 0
    ]);
}

mysqli_close($con); 