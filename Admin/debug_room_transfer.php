<?php
require_once 'db.php';
session_start();

// Set the content type to HTML
header('Content-Type: text/html');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

echo "<h1>Room Transfer Debug Tool</h1>";

// Check direct test parameters
$test_mode = isset($_GET['test']) && $_GET['test'] === 'true';
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';
$current_room_id = isset($_GET['current_room_id']) ? $_GET['current_room_id'] : '';
$new_room_id = isset($_GET['new_room_id']) ? $_GET['new_room_id'] : '';

// Function to display status
function check_status($condition, $success_message, $failure_message) {
    if ($condition) {
        echo "<p style='color:green'>✓ $success_message</p>";
    } else {
        echo "<p style='color:red'>✗ $failure_message</p>";
    }
}

// Check database connection
echo "<h2>Database Connection</h2>";
check_status(isset($con) && $con, 
    "Database connection successful", 
    "Database connection failed: " . mysqli_connect_error());

// Check tables exist
echo "<h2>Required Tables Check</h2>";
$tables = ['bookings', 'room_types', 'room_transfers', 'payments', 'notifications'];
foreach ($tables as $table) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    check_status(mysqli_num_rows($result) > 0, 
        "Table '$table' exists", 
        "Table '$table' does not exist");
}

// Display available room types
echo "<h2>Available Room Types</h2>";
$room_types_query = "SELECT room_type_id, room_type, price FROM room_types ORDER BY price ASC";
$room_types_result = mysqli_query($con, $room_types_query);

if (mysqli_num_rows($room_types_result) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
    echo "<tr><th>Room Type ID</th><th>Room Type</th><th>Price</th></tr>";
    
    while ($room_type = mysqli_fetch_assoc($room_types_result)) {
        echo "<tr>";
        echo "<td>{$room_type['room_type_id']}</td>";
        echo "<td>{$room_type['room_type']}</td>";
        echo "<td>₱{$room_type['price']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:red'>No room types found in database!</p>";
}

// Display checked-in bookings
echo "<h2>Checked-in Bookings</h2>";
$bookings_query = "SELECT booking_id, first_name, last_name, room_type_id, status FROM bookings 
                  WHERE status = 'Checked in' OR status = 'Extended' 
                  ORDER BY booking_id DESC LIMIT 10";
$bookings_result = mysqli_query($con, $bookings_query);

if (mysqli_num_rows($bookings_result) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
    echo "<tr><th>Booking ID</th><th>Guest Name</th><th>Room Type ID</th><th>Status</th><th>Action</th></tr>";
    
    while ($booking = mysqli_fetch_assoc($bookings_result)) {
        echo "<tr>";
        echo "<td>{$booking['booking_id']}</td>";
        echo "<td>{$booking['first_name']} {$booking['last_name']}</td>";
        echo "<td>{$booking['room_type_id']}</td>";
        echo "<td>{$booking['status']}</td>";
        echo "<td><a href='?test=true&booking_id={$booking['booking_id']}&current_room_id={$booking['room_type_id']}'>Test this booking</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:orange'>No checked-in bookings found. You need at least one checked-in booking to test transfers.</p>";
}

// Show debug form if test is enabled
if ($test_mode) {
    echo "<h2>Manual Transfer Test</h2>";
    
    if (empty($booking_id)) {
        echo "<p style='color:red'>Please select a booking from the table above or provide a booking ID.</p>";
    } else {
        // Check if booking exists
        $booking_query = "SELECT * FROM bookings WHERE booking_id = ?";
        $stmt = mysqli_prepare($con, $booking_query);
        mysqli_stmt_bind_param($stmt, "s", $booking_id);
        mysqli_stmt_execute($stmt);
        $booking_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($booking_result) > 0) {
            $booking = mysqli_fetch_assoc($booking_result);
            
            echo "<p><strong>Selected Booking:</strong> ID {$booking['booking_id']} - {$booking['first_name']} {$booking['last_name']}</p>";
            echo "<p><strong>Current Room Type:</strong> {$booking['room_type_id']}</p>";
            echo "<p><strong>Status:</strong> {$booking['status']}</p>";
            
            // Check if status is valid
            if ($booking['status'] !== 'Checked in' && $booking['status'] !== 'Extended') {
                echo "<p style='color:red'>Error: Booking must be in 'Checked in' or 'Extended' status. Current status: {$booking['status']}</p>";
            } else {
                // Show transfer form
                echo "<form method='post' action='debug_room_transfer.php'>";
                echo "<input type='hidden' name='booking_id' value='{$booking['booking_id']}'>";
                echo "<input type='hidden' name='current_room_id' value='{$booking['room_type_id']}'>";
                
                echo "<div style='margin: 10px 0'>";
                echo "<label>New Room Type:</label>";
                echo "<select name='new_room_id' required>";
                echo "<option value=''>Select New Room</option>";
                
                // Reset the pointer to the beginning of the result set
                mysqli_data_seek($room_types_result, 0);
                while ($room_type = mysqli_fetch_assoc($room_types_result)) {
                    if ($room_type['room_type_id'] != $booking['room_type_id']) {
                        echo "<option value='{$room_type['room_type_id']}'>{$room_type['room_type']} - ₱{$room_type['price']}</option>";
                    }
                }
                
                echo "</select>";
                echo "</div>";
                
                echo "<div style='margin: 10px 0'>";
                echo "<label>Transfer Reason:</label>";
                echo "<input type='text' name='transfer_reason' value='Transfer test' required>";
                echo "</div>";
                
                echo "<div style='margin: 10px 0'>";
                echo "<label>Skip Transaction (Test Only):</label>";
                echo "<input type='checkbox' name='skip_transaction' value='1'>";
                echo "</div>";
                
                echo "<button type='submit' name='test_transfer'>Test Transfer</button>";
                echo "</form>";
            }
        } else {
            echo "<p style='color:red'>Error: Booking with ID $booking_id not found.</p>";
        }
    }
}

// Process test transfer
if (isset($_POST['test_transfer'])) {
    echo "<h2>Transfer Test Results</h2>";
    
    $booking_id = $_POST['booking_id'];
    $current_room_id = $_POST['current_room_id'];
    $new_room_id = $_POST['new_room_id'];
    $transfer_reason = $_POST['transfer_reason'];
    $skip_transaction = isset($_POST['skip_transaction']) && $_POST['skip_transaction'] == 1;
    
    // Get room prices
    $current_room_query = "SELECT price FROM room_types WHERE room_type_id = ?";
    $stmt = mysqli_prepare($con, $current_room_query);
    mysqli_stmt_bind_param($stmt, "s", $current_room_id);
    mysqli_stmt_execute($stmt);
    $current_room_result = mysqli_stmt_get_result($stmt);
    $current_room = mysqli_fetch_assoc($current_room_result);
    
    $new_room_query = "SELECT price FROM room_types WHERE room_type_id = ?";
    $stmt = mysqli_prepare($con, $new_room_query);
    mysqli_stmt_bind_param($stmt, "s", $new_room_id);
    mysqli_stmt_execute($stmt);
    $new_room_result = mysqli_stmt_get_result($stmt);
    $new_room = mysqli_fetch_assoc($new_room_result);
    
    // Get booking nights
    $booking_query = "SELECT nights FROM bookings WHERE booking_id = ?";
    $stmt = mysqli_prepare($con, $booking_query);
    mysqli_stmt_bind_param($stmt, "s", $booking_id);
    mysqli_stmt_execute($stmt);
    $booking_result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($booking_result);
    $nights = intval($booking['nights']);
    
    // Calculate price difference
    $current_price = floatval($current_room['price']);
    $new_price = floatval($new_room['price']);
    $price_difference = ($new_price - $current_price) * $nights;
    
    echo "<p><strong>Current Room Price:</strong> ₱" . number_format($current_price, 2) . "</p>";
    echo "<p><strong>New Room Price:</strong> ₱" . number_format($new_price, 2) . "</p>";
    echo "<p><strong>Number of Nights:</strong> $nights</p>";
    echo "<p><strong>Price Difference:</strong> ₱" . number_format($price_difference, 2) . "</p>";
    
    if ($skip_transaction) {
        echo "<p style='color:blue'>Transaction skipped (Test Mode). No changes made to the database.</p>";
    } else {
        // Process the transfer
        try {
            // Start transaction
            mysqli_begin_transaction($con);
            
            // 1. Update the booking
            $update_query = "UPDATE bookings SET room_type_id = ?, total_amount = total_amount + ? WHERE booking_id = ?";
            $stmt = mysqli_prepare($con, $update_query);
            if (!$stmt) {
                throw new Exception("Failed to prepare update statement: " . mysqli_error($con));
            }
            
            mysqli_stmt_bind_param($stmt, "sds", $new_room_id, $price_difference, $booking_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update booking: " . mysqli_stmt_error($stmt));
            }
            
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            echo "<p>Updated booking, affected rows: $affected_rows</p>";
            
            if ($affected_rows === 0) {
                throw new Exception("No changes made to booking. Check if room ID is already set to the new value.");
            }
            
            // 2. Create transfer record
            $transfer_query = "INSERT INTO room_transfers (booking_id, old_room_type_id, new_room_type_id, transfer_reason, price_difference, transfer_date) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($con, $transfer_query);
            if (!$stmt) {
                throw new Exception("Failed to prepare transfer statement: " . mysqli_error($con));
            }
            
            mysqli_stmt_bind_param($stmt, "ssssd", $booking_id, $current_room_id, $new_room_id, $transfer_reason, $price_difference);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to create transfer record: " . mysqli_stmt_error($stmt));
            }
            
            // Commit the transaction
            mysqli_commit($con);
            
            echo "<p style='color:green'>✓ Transfer completed successfully!</p>";
            echo "<p>The booking has been updated with the new room type and the transfer has been recorded.</p>";
            
        } catch (Exception $e) {
            // Rollback the transaction
            mysqli_rollback($con);
            
            echo "<p style='color:red'>✗ Transfer failed: " . $e->getMessage() . "</p>";
            echo "<p>No changes were made to the database.</p>";
        }
    }
}

// Display recent error logs
echo "<h2>Recent Error Logs</h2>";
$error_log_path = 'php_errors.log';
if (file_exists($error_log_path) && is_readable($error_log_path)) {
    $file = file($error_log_path);
    $last_lines = array_slice($file, -20);
    $transfer_errors = false;
    
    echo "<pre style='background-color:#f8f8f8; padding:10px; border:1px solid #ddd; max-height:300px; overflow:auto'>";
    foreach ($last_lines as $line) {
        if (stripos($line, 'transfer') !== false || stripos($line, 'error') !== false) {
            echo htmlspecialchars($line);
            $transfer_errors = true;
        }
    }
    echo "</pre>";
    
    if (!$transfer_errors) {
        echo "<p>No recent transfer errors found in the log.</p>";
    }
} else {
    echo "<p>Error log not found or not readable.</p>";
}

// Close the database connection
mysqli_close($con);

// Navigation links
echo "<div style='margin-top: 20px'>";
echo "<a href='checked_in.php'>Return to Checked In Page</a> | ";
echo "<a href='debug_room_transfer.php'>Refresh Debug Tool</a>";
echo "</div>";
?> 