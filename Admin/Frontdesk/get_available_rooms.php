<?php
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Print connection status
echo "<!-- Debug: Database connected: " . ($con ? "Yes" : "No") . " -->\n";

// Get the current room ID if provided
$current_room_id = isset($_GET['current_room_id']) ? $_GET['current_room_id'] : 0;
echo "<!-- Debug: Current room ID: " . $current_room_id . " -->\n";

// First, let's check what rooms we have and their exact status values
$check_sql = "SELECT r.id, r.room_type_id, r.status, rt.room_type, rt.price 
              FROM rooms r 
              JOIN room_types rt ON r.room_type_id = rt.room_type_id";
$check_result = mysqli_query($con, $check_sql);

echo "<!-- Debug: All rooms in database:\n";
while ($room = mysqli_fetch_assoc($check_result)) {
    echo "Room ID: " . $room['id'] . 
         ", Type ID: " . $room['room_type_id'] . 
         ", Status: '" . $room['status'] . "'" . 
         ", Room Type: " . $room['room_type'] . "\n";
}
echo "-->\n";

// Query to get all rooms except current
$query = "SELECT 
    rt.room_type_id,
    rt.room_type,
    rt.price,
    COUNT(r.id) as room_count
FROM room_types rt
INNER JOIN rooms r ON rt.room_type_id = r.room_type_id
WHERE rt.room_type_id != ?
GROUP BY rt.room_type_id, rt.room_type, rt.price
ORDER BY rt.price ASC";

// Debug output
error_log("Current room ID: " . $current_room_id);
error_log("Query: " . $query);

try {
    // Prepare statement
    if (!($stmt = mysqli_prepare($con, $query))) {
        throw new Exception("Prepare failed: " . mysqli_error($con));
    }

    // Bind parameters
    if (!mysqli_stmt_bind_param($stmt, "i", $current_room_id)) {
        throw new Exception("Binding parameters failed: " . mysqli_stmt_error($stmt));
    }

    // Execute statement
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

    // Get result
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception("Getting result failed: " . mysqli_error($con));
    }

    // Default option
    echo '<option value="">Select New Room</option>';

    // Loop through results and create options
    while ($row = mysqli_fetch_assoc($result)) {
        $count = $row['room_count'];
        $price = number_format($row['price'], 2);
        echo '<option value="' . $row['room_type_id'] . '" data-price="' . $row['price'] . '">' 
            . htmlspecialchars($row['room_type']) . ' - ₱' . $price 
            . '</option>';
    }

    // Log the number of rooms found
    error_log("Number of rooms found: " . mysqli_num_rows($result));

    // Close statement
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    // Log the error
    error_log("Error in get_available_rooms.php: " . $e->getMessage());
    echo '<option value="">Error loading rooms: ' . htmlspecialchars($e->getMessage()) . '</option>';
}

// Debug output
echo "<!-- Debug Output\n";
echo "Current Room ID: " . $current_room_id . "\n";
echo "Query: " . $query . "\n";
if (isset($result)) {
    echo "Number of rooms found: " . mysqli_num_rows($result) . "\n";
}
echo "-->";

// Close the database connection
mysqli_close($con);
?> 