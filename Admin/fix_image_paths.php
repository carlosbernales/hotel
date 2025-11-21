<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'db.php';

// Function to clean image path
function cleanImagePath($path) {
    if (empty($path)) return '';
    
    // Remove '../' from the start
    $path = preg_replace('/^\.\.\//', '', $path);
    
    // Remove 'aa/' from the start
    $path = preg_replace('/^aa\//', '', $path);
    
    // If path doesn't start with a slash and isn't empty, add one
    if (!empty($path) && $path[0] !== '/') {
        $path = '/' . $path;
    }
    
    return $path;
}

try {
    // Get all room types
    $query = "SELECT room_type_id, image, image2, image3 FROM room_types";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception("Error fetching room types: " . mysqli_error($con));
    }
    
    $updates = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $room_type_id = $row['room_type_id'];
        
        // Clean all image paths
        $image = cleanImagePath($row['image']);
        $image2 = cleanImagePath($row['image2']);
        $image3 = cleanImagePath($row['image3']);
        
        // Update the database with cleaned paths
        $update_sql = "UPDATE room_types SET 
                      image = ?, 
                      image2 = ?, 
                      image3 = ? 
                      WHERE room_type_id = ?";
        
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("sssi", $image, $image2, $image3, $room_type_id);
        
        if ($stmt->execute()) {
            $updates++;
        } else {
            echo "Error updating room type ID $room_type_id: " . $stmt->error . "<br>";
        }
    }
    
    echo "Successfully updated $updates room type(s)<br>";
    echo "<a href='room_cards.php'>Go to Room Cards</a><br>";
    echo "<a href='room_management.php'>Go to Room Management</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 