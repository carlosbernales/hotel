<?php
// This script fixes booking IDs that are set to 0 by assigning them unique IDs
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Booking IDs</h1>";
echo "<p>This script will update any bookings with ID 0 to have unique IDs.</p>";

// Create a log table if it doesn't exist
$create_log_table = "CREATE TABLE IF NOT EXISTS fix_booking_ids_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $create_log_table)) {
    die("Error creating log table: " . mysqli_error($con));
}

function log_message($message) {
    global $con;
    
    echo $message . "<br>";
    
    $sql = "INSERT INTO fix_booking_ids_log (message) VALUES (?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $message);
    $stmt->execute();
}

// Check if bookings table has an auto_increment column for booking_id
$check_columns = mysqli_query($con, "SHOW COLUMNS FROM bookings LIKE 'booking_id'");
if (!$check_columns) {
    log_message("Error checking bookings table structure: " . mysqli_error($con));
    die("Could not check table structure");
}

$column = mysqli_fetch_assoc($check_columns);
log_message("Current booking_id column configuration: " . print_r($column, true));

// Get all bookings with ID 0
$query = "SELECT * FROM bookings WHERE booking_id = '0'";
$result = mysqli_query($con, $query);

if (!$result) {
    log_message("Error querying bookings: " . mysqli_error($con));
    die("Could not query bookings");
}

$count = mysqli_num_rows($result);
log_message("Found $count bookings with ID 0");

if ($count > 0) {
    // Start a transaction
    mysqli_begin_transaction($con);
    
    try {
        // Get the current max booking_id
        $max_id_query = "SELECT MAX(CAST(booking_id AS SIGNED)) as max_id FROM bookings WHERE booking_id != '0'";
        $max_id_result = mysqli_query($con, $max_id_query);
        
        if (!$max_id_result) {
            throw new Exception("Error getting max booking ID: " . mysqli_error($con));
        }
        
        $max_id_row = mysqli_fetch_assoc($max_id_result);
        $next_id = $max_id_row['max_id'] ? intval($max_id_row['max_id']) + 1 : 1;
        
        log_message("Next booking ID will be: $next_id");
        
        // Update each booking with ID 0
        while ($booking = mysqli_fetch_assoc($result)) {
            $update_query = "UPDATE bookings SET booking_id = ? WHERE 
                            booking_id = '0' AND 
                            first_name = ? AND 
                            last_name = ? AND 
                            email = ? AND 
                            check_in = ? AND 
                            check_out = ? 
                            LIMIT 1";
            
            $stmt = $con->prepare($update_query);
            
            if (!$stmt) {
                throw new Exception("Error preparing update statement: " . mysqli_error($con));
            }
            
            $stmt->bind_param("ssssss", 
                $next_id, 
                $booking['first_name'], 
                $booking['last_name'], 
                $booking['email'], 
                $booking['check_in'], 
                $booking['check_out']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating booking ID: " . $stmt->error);
            }
            
            $affected = $stmt->affected_rows;
            
            if ($affected > 0) {
                log_message("Updated booking for {$booking['first_name']} {$booking['last_name']} to ID: $next_id");
                $next_id++;
            } else {
                log_message("No update needed for {$booking['first_name']} {$booking['last_name']}");
            }
        }
        
        // If auto_increment is not set on booking_id, alter the table to add it
        if ($column['Extra'] != 'auto_increment') {
            // First ensure the column type is INT
            $alter_type = "ALTER TABLE bookings MODIFY booking_id INT NOT NULL";
            if (!mysqli_query($con, $alter_type)) {
                throw new Exception("Error updating booking_id to INT: " . mysqli_error($con));
            }
            
            // Then add AUTO_INCREMENT
            $alter_table = "ALTER TABLE bookings MODIFY booking_id INT AUTO_INCREMENT PRIMARY KEY";
            if (!mysqli_query($con, $alter_table)) {
                throw new Exception("Error adding AUTO_INCREMENT to booking_id: " . mysqli_error($con));
            }
            
            log_message("Updated booking_id column to INT AUTO_INCREMENT PRIMARY KEY");
        }
        
        // Set the auto_increment value to the next available ID
        $set_auto_increment = "ALTER TABLE bookings AUTO_INCREMENT = $next_id";
        if (!mysqli_query($con, $set_auto_increment)) {
            throw new Exception("Error setting AUTO_INCREMENT value: " . mysqli_error($con));
        }
        
        log_message("Set AUTO_INCREMENT value to $next_id");
        
        // Commit the transaction
        mysqli_commit($con);
        
        echo "<p style='color:green; font-weight:bold;'>Successfully fixed all booking IDs!</p>";
        echo "<p>The booking_id column has been set to AUTO_INCREMENT, which will prevent this issue from happening again.</p>";
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($con);
        
        log_message("Error: " . $e->getMessage());
        echo "<p style='color:red; font-weight:bold;'>An error occurred: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>No bookings with ID 0 were found. No changes needed.</p>";
    
    // Still check and fix the auto_increment property if needed
    if ($column['Extra'] != 'auto_increment') {
        try {
            // First ensure the column type is INT
            $alter_type = "ALTER TABLE bookings MODIFY booking_id INT NOT NULL";
            if (!mysqli_query($con, $alter_type)) {
                throw new Exception("Error updating booking_id to INT: " . mysqli_error($con));
            }
            
            // Then add AUTO_INCREMENT
            $alter_table = "ALTER TABLE bookings MODIFY booking_id INT AUTO_INCREMENT PRIMARY KEY";
            if (!mysqli_query($con, $alter_table)) {
                throw new Exception("Error adding AUTO_INCREMENT to booking_id: " . mysqli_error($con));
            }
            
            log_message("Updated booking_id column to INT AUTO_INCREMENT PRIMARY KEY");
            echo "<p style='color:green'>The booking_id column has been set to AUTO_INCREMENT, which will prevent this issue from happening in the future.</p>";
            
        } catch (Exception $e) {
            log_message("Error: " . $e->getMessage());
            echo "<p style='color:red; font-weight:bold;'>An error occurred: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>The booking_id column is already set to AUTO_INCREMENT. No changes needed.</p>";
    }
}

echo "<p><a href='booking_status.php' class='btn btn-primary'>Return to Booking Status</a></p>";
?> 