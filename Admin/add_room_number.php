<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if column exists
function column_exists($conn, $table, $column) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Start output
echo "<h1>Add Room Number to Bookings</h1>";

// Process form submission if room number updates are submitted
if (isset($_POST['update_room_numbers'])) {
    $booking_ids = $_POST['booking_id'];
    $room_numbers = $_POST['room_number'];
    $success_count = 0;
    $error_count = 0;
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        foreach ($booking_ids as $index => $booking_id) {
            if (!empty($room_numbers[$index])) {
                $room_number = mysqli_real_escape_string($con, $room_numbers[$index]);
                $booking_id = intval($booking_id);
                
                $update_query = "UPDATE bookings SET room_number = '$room_number' WHERE booking_id = $booking_id";
                if (mysqli_query($con, $update_query)) {
                    $success_count++;
                } else {
                    $error_count++;
                    echo "<p style='color:red'>Error updating booking ID $booking_id: " . mysqli_error($con) . "</p>";
                }
            }
        }
        
        // Commit the transaction if everything is successful
        mysqli_commit($con);
        
        echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
        echo "<h4>Update Results</h4>";
        echo "<p>Successfully updated $success_count bookings.</p>";
        if ($error_count > 0) {
            echo "<p>Failed to update $error_count bookings.</p>";
        }
        echo "</div>";
        
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($con);
        echo "<p style='color:red'>Transaction failed: " . $e->getMessage() . "</p>";
    }
}

// Step 1: Check if the room_number column exists
if (!column_exists($con, 'bookings', 'room_number')) {
    echo "<h2>Step 1: Add Room Number Column</h2>";
    
    // Add the room_number column
    $alter_table_sql = "ALTER TABLE bookings ADD COLUMN room_number VARCHAR(20) NULL AFTER room_type_id";
    
    if (mysqli_query($con, $alter_table_sql)) {
        echo "<p style='color:green'>✓ Successfully added 'room_number' column to the bookings table.</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to add 'room_number' column: " . mysqli_error($con) . "</p>";
        // Exit if we couldn't add the column
        exit;
    }
} else {
    echo "<p style='color:green'>✓ 'room_number' column already exists in the bookings table.</p>";
}

// Step 2: Display existing bookings without room numbers
echo "<h2>Step 2: Assign Room Numbers to Bookings</h2>";

// Get bookings with missing room numbers
$query = "SELECT booking_id, first_name, last_name, room_type_id, check_in, check_out, status 
          FROM bookings 
          WHERE (room_number IS NULL OR room_number = '') 
          AND (status = 'Confirmed' OR status = 'Checked in' OR status = 'Extended')
          ORDER BY check_in DESC 
          LIMIT 100";

$result = mysqli_query($con, $query);

if (!$result) {
    echo "<p style='color:red'>Error retrieving bookings: " . mysqli_error($con) . "</p>";
} elseif (mysqli_num_rows($result) > 0) {
    // Get room types for dropdown
    $room_types_query = "SELECT DISTINCT room_type_id, room_type FROM room_types ORDER BY room_type";
    $room_types_result = mysqli_query($con, $room_types_query);
    $room_types = [];
    
    while ($type = mysqli_fetch_assoc($room_types_result)) {
        $room_types[$type['room_type_id']] = $type['room_type'];
    }
    
    // Display form for adding room numbers
    echo "<form method='post' action=''>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
    echo "<tr><th>Booking ID</th><th>Guest Name</th><th>Room Type</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Room Number</th></tr>";
    
    while ($booking = mysqli_fetch_assoc($result)) {
        $room_type_name = isset($room_types[$booking['room_type_id']]) ? $room_types[$booking['room_type_id']] : 'Unknown';
        
        echo "<tr>";
        echo "<td>{$booking['booking_id']}</td>";
        echo "<td>{$booking['first_name']} {$booking['last_name']}</td>";
        echo "<td>$room_type_name (ID: {$booking['room_type_id']})</td>";
        echo "<td>{$booking['check_in']}</td>";
        echo "<td>{$booking['check_out']}</td>";
        echo "<td>{$booking['status']}</td>";
        echo "<td><input type='text' name='room_number[]' placeholder='Enter room #'>";
        echo "<input type='hidden' name='booking_id[]' value='{$booking['booking_id']}'></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p><button type='submit' name='update_room_numbers' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Update Room Numbers</button></p>";
    echo "</form>";
    
    // Show guidance for room number format
    echo "<div style='background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px;'>";
    echo "<h4>Room Number Format Guidance</h4>";
    echo "<p>When assigning room numbers, follow a consistent format such as:</p>";
    echo "<ul>";
    echo "<li><strong>Floor + Room Number:</strong> 101, 102, 201, 202, etc.</li>";
    echo "<li><strong>Wing + Number:</strong> A101, B202, etc.</li>";
    echo "<li><strong>Building + Floor + Room:</strong> M101, H202, etc.</li>";
    echo "</ul>";
    echo "<p>Choose a format that makes sense for your property layout.</p>";
    echo "</div>";
} else {
    echo "<p>No bookings found without room numbers. All bookings have been assigned a room number.</p>";
}

// Step 3: Create a tool for viewing and editing assigned room numbers
echo "<h2>Step 3: View and Edit Assigned Room Numbers</h2>";

// Get bookings with room numbers
$assigned_query = "SELECT booking_id, first_name, last_name, room_type_id, room_number, check_in, check_out, status 
                  FROM bookings 
                  WHERE room_number IS NOT NULL AND room_number != '' 
                  ORDER BY check_in DESC 
                  LIMIT 50";

$assigned_result = mysqli_query($con, $assigned_query);

if (!$assigned_result) {
    echo "<p style='color:red'>Error retrieving assigned bookings: " . mysqli_error($con) . "</p>";
} elseif (mysqli_num_rows($assigned_result) > 0) {
    echo "<h3>Bookings with Assigned Room Numbers</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
    echo "<tr><th>Booking ID</th><th>Guest Name</th><th>Room Type</th><th>Room Number</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr>";
    
    while ($booking = mysqli_fetch_assoc($assigned_result)) {
        $room_type_name = isset($room_types[$booking['room_type_id']]) ? $room_types[$booking['room_type_id']] : 'Unknown';
        
        echo "<tr>";
        echo "<td>{$booking['booking_id']}</td>";
        echo "<td>{$booking['first_name']} {$booking['last_name']}</td>";
        echo "<td>$room_type_name (ID: {$booking['room_type_id']})</td>";
        echo "<td>{$booking['room_number']}</td>";
        echo "<td>{$booking['check_in']}</td>";
        echo "<td>{$booking['check_out']}</td>";
        echo "<td>{$booking['status']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No bookings found with assigned room numbers yet.</p>";
}

// Step 4: Update front-end to display room numbers
echo "<h2>Step 4: Update Front-end Code</h2>";
echo "<p>To display room numbers on the booking details page, you'll need to:</p>";
echo "<ol>";
echo "<li>Update the checked_in.php file to show the room number column in the table</li>";
echo "<li>Update booking details views to show the assigned room number</li>";
echo "<li>Update the print receipt/invoice templates to include room number</li>";
echo "</ol>";

echo "<p>Would you like me to create these updates for you?</p>";
echo "<a href='#' onclick='history.go(0)' style='padding: 10px; background-color: #337ab7; color: white; text-decoration: none; border-radius: 4px;'>Refresh Page</a>";

// Close connection
mysqli_close($con);
?> 