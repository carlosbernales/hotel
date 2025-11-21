<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Database connection successful<br>";

// Get database name
$result = mysqli_query($con, "SELECT DATABASE()");
$row = mysqli_fetch_row($result);
echo "Current database: " . $row[0] . "<br><br>";

// Check bookings table
$query = "SELECT * FROM bookings LIMIT 5";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error querying bookings table: " . mysqli_error($con));
}

$count = mysqli_num_rows($result);
echo "Number of bookings found: " . $count . "<br><br>";

if ($count > 0) {
    echo "<h3>Sample Bookings:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>Booking ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Status</th>
          </tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['booking_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
        echo "<td>" . htmlspecialchars($row['check_in']) . "</td>";
        echo "<td>" . htmlspecialchars($row['check_out']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No bookings found in the table.<br>";
}

// Check room_types table
echo "<br><h3>Room Types Table:</h3>";
$room_query = "SELECT * FROM room_types";
$room_result = mysqli_query($con, $room_query);

if (!$room_result) {
    echo "Error querying room_types table: " . mysqli_error($con);
} else {
    $room_count = mysqli_num_rows($room_result);
    echo "Number of room types found: " . $room_count . "<br><br>";
    
    if ($room_count > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>Room Type ID</th>
                <th>Room Type</th>
                <th>Room Name</th>
              </tr>";
        
        while ($room = mysqli_fetch_assoc($room_result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($room['room_type_id']) . "</td>";
            echo "<td>" . htmlspecialchars($room['room_type']) . "</td>";
            echo "<td>" . htmlspecialchars($room['room_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No room types found.<br>";
    }
}

// Check if the view details functionality is working
echo "<br><h3>Test View Details:</h3>";
echo "<form method='post' action='get_booking_details.php'>";
echo "<input type='text' name='booking_id' placeholder='Enter booking ID'>";
echo "<input type='submit' value='Test View Details'>";
echo "</form>";

// Check for payment_proofs directory
$paths = [
    'C:/xampp/htdocs/Admin/aa/uploads/payment_proofs/',
    'aa/uploads/payment_proofs/',
    '../aa/uploads/payment_proofs/',
    'uploads/payment_proofs/'
];

echo "<h3>Payment Proofs Directory Check:</h3>";
foreach ($paths as $path) {
    echo "Checking path: " . $path . " - ";
    if (file_exists($path)) {
        echo "EXISTS";
        echo " (Contents: " . implode(", ", array_diff(scandir($path), array('.', '..'))) . ")";
    } else {
        echo "NOT FOUND";
    }
    echo "<br>";
}
?> 