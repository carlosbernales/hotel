<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Update Standard Double Room status
$update_sql = "UPDATE rooms r 
               INNER JOIN room_types rt ON r.room_type_id = rt.room_type_id 
               SET r.status = 'active' 
               WHERE rt.room_type = 'Standard Double Room'";

if (mysqli_query($con, $update_sql)) {
    echo "Successfully updated room status<br>";
    
    // Check the updated status
    $check_sql = "SELECT r.*, rt.room_type 
                  FROM rooms r 
                  INNER JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                  WHERE rt.room_type = 'Standard Double Room'";
    
    $result = mysqli_query($con, $check_sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "Room ID: " . $row['id'] . 
                 ", Type: " . $row['room_type'] . 
                 ", Status: " . $row['status'] . "<br>";
        }
    }
} else {
    echo "Error updating room status: " . mysqli_error($con);
}
?> 