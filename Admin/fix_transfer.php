<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Room Transfer System Fix</h1>";

// 1. Create room_transfers table if it doesn't exist
echo "<h2>Step 1: Checking room_transfers table</h2>";
$check_table = mysqli_query($con, "SHOW TABLES LIKE 'room_transfers'");
if (mysqli_num_rows($check_table) == 0) {
    $create_table = "CREATE TABLE room_transfers (
        transfer_id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) NOT NULL,
        old_room_type_id VARCHAR(50) NOT NULL,
        new_room_type_id VARCHAR(50) NOT NULL,
        transfer_reason TEXT NOT NULL,
        price_difference DECIMAL(10,2) NOT NULL,
        transfer_date DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($con, $create_table)) {
        echo "<p style='color:green'>✓ Successfully created room_transfers table</p>";
    } else {
        echo "<p style='color:red'>✗ Error creating room_transfers table: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>room_transfers table already exists.</p>";
}

// 2. Add last_modified column to bookings table if it doesn't exist
echo "<h2>Step 2: Checking last_modified column in bookings table</h2>";
$check_column = mysqli_query($con, "SHOW COLUMNS FROM bookings LIKE 'last_modified'");
if (mysqli_num_rows($check_column) == 0) {
    $add_column = "ALTER TABLE bookings ADD COLUMN last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    if (mysqli_query($con, $add_column)) {
        echo "<p style='color:green'>✓ Successfully added last_modified column to bookings table</p>";
    } else {
        echo "<p style='color:red'>✗ Error adding last_modified column: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p>last_modified column already exists in bookings table.</p>";
}

// 3. Check and fix process_room_transfer.php
echo "<h2>Step 3: Fixing process_room_transfer.php</h2>";

$process_file = 'process_room_transfer.php';
$backup_file = $process_file . '.bak';

// Make a backup of the original file if one doesn't exist
if (!file_exists($backup_file)) {
    if (copy($process_file, $backup_file)) {
        echo "<p>✓ Created backup of $process_file</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create backup of $process_file</p>";
    }
}

$fixed_content = file_get_contents($process_file);

// Fix 1: Update the SQL query to handle missing last_modified column
$original_update_sql = "SET room_type_id = ?, \n                          total_amount = total_amount + ?,\n                          last_modified = NOW()";
$fixed_update_sql = "SET room_type_id = ?, \n                          total_amount = total_amount + ?";

if (strpos($fixed_content, $original_update_sql) !== false) {
    $fixed_content = str_replace($original_update_sql, $fixed_update_sql, $fixed_content);
    echo "<p style='color:green'>✓ Fixed update SQL query to remove last_modified reference</p>";
} else {
    echo "<p>Update SQL already fixed or uses different format.</p>";
}

// Fix 2: Improve error handling and logging
if (strpos($fixed_content, 'debug_log("Error occurred:') !== false) {
    $fixed_content = str_replace(
        'debug_log("Error occurred:', 
        'debug_log("Room transfer error:', 
        $fixed_content
    );
    echo "<p style='color:green'>✓ Enhanced error logging for easier troubleshooting</p>";
}

// Write the fixed content back to the file
if (file_put_contents($process_file, $fixed_content)) {
    echo "<p style='color:green'>✓ Successfully updated $process_file</p>";
} else {
    echo "<p style='color:red'>✗ Failed to update $process_file</p>";
}

// 4. Check available room types for testing
echo "<h2>Step 4: Available Room Types</h2>";
$room_types = mysqli_query($con, "SELECT room_type_id, room_type, price FROM room_types ORDER BY price ASC");

if (mysqli_num_rows($room_types) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
    echo "<tr><th>Type ID</th><th>Room Type</th><th>Price</th></tr>";
    
    while ($type = mysqli_fetch_assoc($room_types)) {
        echo "<tr>";
        echo "<td>{$type['room_type_id']}</td>";
        echo "<td>{$type['room_type']}</td>";
        echo "<td>₱{$type['price']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p>Use these IDs for testing room transfers. Make sure the current and new room IDs exist!</p>";
} else {
    echo "<p style='color:red'>✗ No room types found in the database!</p>";
}

// 5. Find available bookings for testing
echo "<h2>Step 5: Available Checked-in Bookings</h2>";
$checked_in = mysqli_query($con, "SELECT booking_id, first_name, last_name, room_type_id, status 
                                 FROM bookings 
                                 WHERE status = 'Checked in' OR status = 'Extended'
                                 LIMIT 10");

if (mysqli_num_rows($checked_in) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse'>";
    echo "<tr><th>Booking ID</th><th>Guest Name</th><th>Room Type ID</th><th>Status</th></tr>";
    
    while ($booking = mysqli_fetch_assoc($checked_in)) {
        echo "<tr>";
        echo "<td>{$booking['booking_id']}</td>";
        echo "<td>{$booking['first_name']} {$booking['last_name']}</td>";
        echo "<td>{$booking['room_type_id']}</td>";
        echo "<td>{$booking['status']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p>These are available bookings you can test the room transfer with.</p>";
} else {
    echo "<p style='color:orange'>⚠ No checked-in bookings found. You'll need at least one checked-in booking to test transfers.</p>";
}

// 6. Test transfer simulation
echo "<h2>Step 6: Transfer Simulation</h2>";

$simulation_html = <<<HTML
<form id="simulation-form" method="post" action="fix_transfer.php">
    <p>Simulate a room transfer to test functionality:</p>
    <div style="margin-bottom: 10px;">
        <label>Booking ID: </label>
        <input type="text" name="booking_id" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Current Room Type ID: </label>
        <input type="text" name="current_room_id" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>New Room Type ID: </label>
        <input type="text" name="new_room_id" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Transfer Reason: </label>
        <input type="text" name="transfer_reason" value="Test transfer" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Price Difference: </label>
        <input type="number" name="price_difference" value="0" step="0.01" required>
    </div>
    <button type="submit" name="simulate_transfer">Simulate Transfer</button>
</form>
HTML;

echo $simulation_html;

// Handle transfer simulation if the form was submitted
if (isset($_POST['simulate_transfer'])) {
    echo "<h3>Simulation Results:</h3>";
    
    $booking_id = $_POST['booking_id'];
    $current_room_id = $_POST['current_room_id'];
    $new_room_id = $_POST['new_room_id'];
    $transfer_reason = $_POST['transfer_reason'];
    $price_difference = floatval($_POST['price_difference']);
    
    echo "<p>Attempting transfer with these parameters:</p>";
    echo "<ul>";
    echo "<li>Booking ID: $booking_id</li>";
    echo "<li>Current Room ID: $current_room_id</li>";
    echo "<li>New Room ID: $new_room_id</li>";
    echo "<li>Transfer Reason: $transfer_reason</li>";
    echo "<li>Price Difference: $price_difference</li>";
    echo "</ul>";
    
    // Verify booking exists
    $booking_check = mysqli_query($con, "SELECT * FROM bookings WHERE booking_id = '$booking_id'");
    if (mysqli_num_rows($booking_check) == 0) {
        echo "<p style='color:red'>✗ Booking ID $booking_id not found!</p>";
    } else {
        $booking = mysqli_fetch_assoc($booking_check);
        if ($booking['status'] != 'Checked in' && $booking['status'] != 'Extended') {
            echo "<p style='color:red'>✗ Booking is not in 'Checked in' or 'Extended' status! Current status: {$booking['status']}</p>";
        } else {
            echo "<p style='color:green'>✓ Booking exists and is in correct status</p>";
            
            // Verify room types exist
            $room_check = mysqli_query($con, "SELECT * FROM room_types WHERE room_type_id IN ('$current_room_id', '$new_room_id')");
            if (mysqli_num_rows($room_check) < 2) {
                echo "<p style='color:red'>✗ One or both room types don't exist!</p>";
                echo "<p>Available room IDs: ";
                $room_ids = mysqli_query($con, "SELECT room_type_id FROM room_types LIMIT 10");
                $ids = [];
                while ($row = mysqli_fetch_assoc($room_ids)) {
                    $ids[] = $row['room_type_id'];
                }
                echo implode(", ", $ids);
                echo "</p>";
            } else {
                echo "<p style='color:green'>✓ Both room types exist</p>";
                
                // All checks passed, we can perform the test
                try {
                    // Start transaction
                    mysqli_begin_transaction($con);
                    
                    // 1. Update booking
                    $update_booking = "UPDATE bookings 
                                      SET room_type_id = ?, 
                                          total_amount = total_amount + ?
                                      WHERE booking_id = ?";
                                      
                    $stmt = mysqli_prepare($con, $update_booking);
                    if (!$stmt) {
                        throw new Exception("Failed to prepare update statement: " . mysqli_error($con));
                    }
                    
                    mysqli_stmt_bind_param($stmt, "ids", $new_room_id, $price_difference, $booking_id);
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to update booking: " . mysqli_stmt_error($stmt));
                    }
                    
                    $affected_rows = mysqli_stmt_affected_rows($stmt);
                    echo "<p>Updated booking, affected rows: $affected_rows</p>";
                    
                    if ($affected_rows === 0) {
                        throw new Exception("No changes made to booking - check if room_type_id is already set to new value");
                    }
                    
                    // 2. Create transfer record
                    $create_transfer = "INSERT INTO room_transfers 
                                      (booking_id, old_room_type_id, new_room_type_id, 
                                       transfer_reason, price_difference, transfer_date) 
                                      VALUES (?, ?, ?, ?, ?, NOW())";
                    
                    $stmt = mysqli_prepare($con, $create_transfer);
                    if (!$stmt) {
                        throw new Exception("Failed to prepare transfer statement: " . mysqli_error($con));
                    }
                    
                    mysqli_stmt_bind_param($stmt, "ssssd", 
                        $booking_id, 
                        $current_room_id, 
                        $new_room_id, 
                        $transfer_reason, 
                        $price_difference
                    );
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to create transfer record: " . mysqli_stmt_error($stmt));
                    }
                    
                    echo "<p style='color:green'>✓ Created transfer record successfully</p>";
                    
                    // Rollback for testing purposes
                    mysqli_rollback($con);
                    echo "<p>Transaction rolled back (this was just a simulation)</p>";
                    echo "<p style='color:green'>✓ Simulation successful! Your room transfer functionality should now work correctly.</p>";
                    
                } catch (Exception $e) {
                    mysqli_rollback($con);
                    echo "<p style='color:red'>✗ Error during simulation: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
}

echo "<p><a href='checked_in.php'>Return to Checked In Page</a></p>";

// Close connection
mysqli_close($con);
?> 