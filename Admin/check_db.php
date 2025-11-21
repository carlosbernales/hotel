<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check staff table structure
$structure_query = "SHOW CREATE TABLE staff";
$structure_result = mysqli_query($con, $structure_query);

if ($structure_result) {
    echo "<h3>Staff Table Structure:</h3>";
    $row = mysqli_fetch_array($structure_result);
    echo "<pre>" . htmlspecialchars($row[1]) . "</pre>";
} else {
    echo "Error getting table structure: " . mysqli_error($con);
}

// Check staff data
$data_query = "SELECT s.*, st.staff_type 
               FROM staff s 
               JOIN staff_type st ON s.staff_type_id = st.staff_type_id 
               LIMIT 5";
$data_result = mysqli_query($con, $data_query);

if ($data_result) {
    echo "<h3>Sample Staff Data:</h3>";
    echo "<pre>";
    while ($row = mysqli_fetch_assoc($data_result)) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error getting staff data: " . mysqli_error($con);
}

// Check staff_type table
$type_query = "SELECT * FROM staff_type";
$type_result = mysqli_query($con, $type_query);

if ($type_result) {
    echo "<h3>Staff Types:</h3>";
    echo "<pre>";
    while ($row = mysqli_fetch_assoc($type_result)) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error getting staff types: " . mysqli_error($con);
}
?>
