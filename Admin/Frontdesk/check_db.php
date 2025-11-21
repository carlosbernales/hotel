<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Show all tables
$tables_query = "SHOW TABLES FROM hotelms";
$tables_result = mysqli_query($con, $tables_query);

echo "<h2>Tables in hotelms database:</h2>";
if ($tables_result) {
    while ($table = mysqli_fetch_array($tables_result)) {
        echo $table[0] . "<br>";
        
        // Show structure for each table
        $desc_query = "DESCRIBE " . $table[0];
        $desc_result = mysqli_query($con, $desc_query);
        
        if ($desc_result) {
            echo "<pre>";
            while ($row = mysqli_fetch_assoc($desc_result)) {
                print_r($row);
            }
            echo "</pre><hr>";
        }
    }
} else {
    echo "Error: " . mysqli_error($con);
}
?>
