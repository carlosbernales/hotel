<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test PHP execution
echo "PHP is working! <br>";

// Test database connection
$host = "localhost";
$username = "u429956055_admin";  // Using production credentials
$password = "Admin@123";
$database = "u429956055_hotelms";

try {
    $con = mysqli_connect($host, $username, $password, $database);
    if ($con) {
        echo "Database connection successful! <br>";
        
        // Test query
        $test_query = "SELECT COUNT(*) as count FROM bookings";
        $result = mysqli_query($con, $test_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "Number of bookings in database: " . $row['count'] . "<br>";
        } else {
            echo "Query failed: " . mysqli_error($con) . "<br>";
        }
    }
} catch (Exception $e) {
    echo "Connection error: " . $e->getMessage() . "<br>";
}
?>

<script>
// Basic JavaScript
console.log("JavaScript is working");
document.write("<p>JavaScript output works</p>");
</script>

<a href="index.php?table_packages">Go to Table Packages</a>
