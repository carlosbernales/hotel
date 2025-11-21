<?php
require_once 'db.php';
session_start();

// Set the content type to text/html for browser display
header('Content-Type: text/html');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

echo "<h1>Room Transfer Debugger</h1>";

// Create sample test data
$test_data = [
    'booking_id' => '1', // Modify this with a real booking ID from your system
    'current_room_id' => '1', // Modify with an actual room type ID
    'new_room_id' => '2', // Modify with a different actual room type ID
    'transfer_reason' => 'Debug test transfer',
    'price_difference' => 100.00
];

echo "<h2>Test Data:</h2>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

// Check if booking exists
$booking_query = "SELECT * FROM bookings WHERE booking_id = '{$test_data['booking_id']}'";
$booking_result = mysqli_query($con, $booking_query);

echo "<h2>Booking Check:</h2>";
if (mysqli_num_rows($booking_result) > 0) {
    $booking = mysqli_fetch_assoc($booking_result);
    echo "<p style='color:green'>✓ Booking found: </p>";
    echo "<pre>" . print_r($booking, true) . "</pre>";
} else {
    echo "<p style='color:red'>✗ Error: Booking not found! Query: $booking_query</p>";
    echo "<p>Available bookings with 'Checked in' status:</p>";
    $check_bookings = mysqli_query($con, "SELECT booking_id, first_name, last_name, status FROM bookings WHERE status = 'Checked in' LIMIT 5");
    if (mysqli_num_rows($check_bookings) > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($check_bookings)) {
            echo "<li>Booking ID: {$row['booking_id']} - {$row['first_name']} {$row['last_name']} (Status: {$row['status']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No bookings with 'Checked in' status found.</p>";
    }
}

// Check if rooms exist
$room_query = "SELECT * FROM room_types WHERE room_type_id IN ('{$test_data['current_room_id']}', '{$test_data['new_room_id']}')";
$room_result = mysqli_query($con, $room_query);

echo "<h2>Room Types Check:</h2>";
if (mysqli_num_rows($room_result) == 2) {
    echo "<p style='color:green'>✓ Both room types found:</p>";
    echo "<ul>";
    while ($room = mysqli_fetch_assoc($room_result)) {
        echo "<li>Room Type ID: {$room['room_type_id']} - {$room['room_type']} (Price: {$room['price']})</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>✗ Error: One or both room types not found! Query: $room_query</p>";
    echo "<p>Available room types:</p>";
    $check_rooms = mysqli_query($con, "SELECT room_type_id, room_type, price FROM room_types LIMIT 10");
    if (mysqli_num_rows($check_rooms) > 0) {
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($check_rooms)) {
            echo "<li>Room Type ID: {$row['room_type_id']} - {$row['room_type']} (Price: {$row['price']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No room types found.</p>";
    }
}

// Test the database transaction
echo "<h2>Testing Database Transaction:</h2>";

try {
    // Start transaction
    mysqli_begin_transaction($con);
    echo "<p>Transaction started</p>";
    
    // 1. Update the booking with new room type
    $update_booking = "UPDATE bookings 
                      SET room_type_id = ?, 
                          total_amount = total_amount + ?,
                          last_modified = NOW()
                      WHERE booking_id = ?";
    
    $stmt = mysqli_prepare($con, $update_booking);
    if (!$stmt) {
        throw new Exception("Failed to prepare update statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "ids", 
        $test_data['new_room_id'], 
        $test_data['price_difference'], 
        $test_data['booking_id']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update booking: " . mysqli_stmt_error($stmt));
    }
    
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    echo "<p>Updated booking, affected rows: $affected_rows</p>";
    
    if ($affected_rows === 0) {
        throw new Exception("No changes made to booking - check if the booking exists and room_type_id is different");
    }

    // 2. Create a transfer record
    $create_transfer = "INSERT INTO room_transfers 
                       (booking_id, old_room_type_id, new_room_type_id, 
                        transfer_reason, price_difference, transfer_date) 
                       VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = mysqli_prepare($con, $create_transfer);
    if (!$stmt) {
        throw new Exception("Failed to prepare transfer statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "ssssd", 
        $test_data['booking_id'], 
        $test_data['current_room_id'],
        $test_data['new_room_id'], 
        $test_data['transfer_reason'],
        $test_data['price_difference']
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to create transfer record: " . mysqli_stmt_error($stmt));
    }
    
    echo "<p style='color:green'>✓ Created transfer record successfully</p>";

    // Rollback transaction for testing purposes
    mysqli_rollback($con);
    echo "<p>Transaction rolled back (test only)</p>";
    
    echo "<p style='color:green'>✓ Transaction test successful</p>";

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Transaction rolled back due to error</p>";
}

// Check for other errors
echo "<h2>Other Possible Issues:</h2>";

// Check room_transfers table structure
$check_table = mysqli_query($con, "DESCRIBE room_transfers");
if ($check_table) {
    echo "<p style='color:green'>✓ Room transfers table structure:</p>";
    echo "<ul>";
    while ($field = mysqli_fetch_assoc($check_table)) {
        echo "<li>{$field['Field']} - {$field['Type']} " . 
             ($field['Null'] === 'NO' ? '(Required)' : '(Optional)') . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>✗ Error checking room_transfers structure: " . mysqli_error($con) . "</p>";
}

// Check AJAX response format
echo "<h2>Test AJAX Response:</h2>";
$test_response = [
    'success' => true,
    'message' => 'Room transfer processed successfully',
    'data' => [
        'booking_id' => $test_data['booking_id'],
        'new_room_id' => $test_data['new_room_id'],
        'price_difference' => $test_data['price_difference']
    ]
];
echo "<pre>" . json_encode($test_response, JSON_PRETTY_PRINT) . "</pre>";

// Check current PHP errors
$error_log = 'php_errors.log';
echo "<h2>Recent PHP Errors:</h2>";
if (file_exists($error_log) && is_readable($error_log)) {
    // Just check if the file exists and its size
    echo "<p>Error log exists: " . filesize($error_log) . " bytes</p>";
    
    // Read the last 20 lines of the file directly with PHP
    $file = file($error_log);
    $last_lines = array_slice($file, -20);
    $transfer_errors = false;
    
    echo "<pre>";
    foreach ($last_lines as $line) {
        if (stripos($line, 'transfer') !== false) {
            echo htmlspecialchars($line);
            $transfer_errors = true;
        }
    }
    echo "</pre>";
    
    if (!$transfer_errors) {
        echo "<p>No recent transfer-related errors found in log.</p>";
    }
} else {
    echo "<p>Error log not found or not readable.</p>";
}

// Close the connection
mysqli_close($con);

echo "<p><a href='checked_in.php'>Return to Checked In Page</a></p>";
?> 