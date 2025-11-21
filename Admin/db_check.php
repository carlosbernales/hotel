<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
require_once 'db.php';

// Get columns from orders table
echo "<h3>Orders Table Structure:</h3>";
$query = "SHOW COLUMNS FROM orders";
$result = $con->query($query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error getting orders table structure: " . $con->error;
}

// Check recent orders
echo "<h3>Last 10 Orders:</h3>";
$orderQuery = "SELECT * FROM orders ORDER BY id DESC LIMIT 10";
$orderResult = $con->query($orderQuery);

if ($orderResult) {
    echo "<table border='1'>";
    echo "<tr>";
    $fields = $orderResult->fetch_fields();
    foreach ($fields as $field) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";
    
    while ($row = $orderResult->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>{$value}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error getting recent orders: " . $con->error;
}

// Check for existing table bookings with orders
echo "<h3>Table Bookings with Advance Orders:</h3>";
$bookingQuery = "SELECT tb.id, tb.name, tb.booking_date, tb.booking_time, tb.package_type, 
                 o.id as order_id, o.total_amount, o.status as order_status
                 FROM table_bookings tb
                 LEFT JOIN orders o ON tb.id = o.table_id
                 ORDER BY tb.id DESC LIMIT 10";
$bookingResult = $con->query($bookingQuery);

if ($bookingResult) {
    echo "<table border='1'>";
    echo "<tr><th>Booking ID</th><th>Customer</th><th>Date</th><th>Time</th><th>Package</th><th>Order ID</th><th>Order Amount</th><th>Order Status</th></tr>";
    
    while ($row = $bookingResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['booking_date']}</td>";
        echo "<td>{$row['booking_time']}</td>";
        echo "<td>{$row['package_type']}</td>";
        echo "<td>" . ($row['order_id'] ? $row['order_id'] : 'None') . "</td>";
        echo "<td>" . ($row['total_amount'] ? $row['total_amount'] : '-') . "</td>";
        echo "<td>" . ($row['order_status'] ? $row['order_status'] : '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error checking table bookings: " . $con->error;
} 