<?php
// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'db.php';

// Part 1: List all columns in event_bookings table
echo "<h2>Columns in event_bookings table:</h2>";
$columns_query = "SHOW COLUMNS FROM event_bookings";
$columns_result = mysqli_query($con, $columns_query);

if (!$columns_result) {
    echo "Error getting columns: " . mysqli_error($con);
} else {
    echo "<ul>";
    while ($column = mysqli_fetch_assoc($columns_result)) {
        echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
    }
    echo "</ul>";
}

// Part 2: Test a simple query
echo "<h2>Testing a simple query:</h2>";
$test_query = "SELECT id, customer_name, package_name, total_amount, paid_amount FROM event_bookings LIMIT 5";
$test_result = mysqli_query($con, $test_query);

if (!$test_result) {
    echo "Error executing query: " . mysqli_error($con);
} else {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Customer Name</th><th>Package</th><th>Total Amount</th><th>Paid Amount</th></tr>";
    while ($row = mysqli_fetch_assoc($test_result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . $row['package_name'] . "</td>";
        echo "<td>" . $row['total_amount'] . "</td>";
        echo "<td>" . $row['paid_amount'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Part 3: Test the problematic query
echo "<h2>Testing the problematic query:</h2>";
$problem_query = "SELECT 
    'Event Booking' as booking_type,
    id as booking_id,
    customer_name as first_name,
    '' as last_name,
    '' as email,
    event_date as booking_date,
    COALESCE(end_time, event_date) as end_date,
    COALESCE(number_of_guests, 0) as number_of_guests,
    COALESCE(payment_method, '') as payment_method,
    COALESCE(event_type, package_name) as payment_option,
    total_amount,
    paid_amount as amount_paid,
    0 as change_amount,
    COALESCE(extra_guest_charge, 0) as extra_fee,
    COALESCE(booking_status, 'Confirmed') as status,
    COALESCE(created_at, reservation_date) as created_at,
    COALESCE(payment_reference, '') as payment_reference,
    COALESCE(payment_proof, '') as payment_proof,
    'admin' as source,
    remaining_balance
FROM event_bookings
WHERE user_id = 1
AND total_amount = paid_amount
LIMIT 5";

$problem_result = mysqli_query($con, $problem_query);

if (!$problem_result) {
    echo "Error executing problematic query: " . mysqli_error($con);
} else {
    echo "Query executed successfully!";
    echo "<table border='1'>";
    echo "<tr><th>Booking Type</th><th>ID</th><th>Name</th><th>Booking Date</th><th>Total</th><th>Paid</th></tr>";
    while ($row = mysqli_fetch_assoc($problem_result)) {
        echo "<tr>";
        echo "<td>" . $row['booking_type'] . "</td>";
        echo "<td>" . $row['booking_id'] . "</td>";
        echo "<td>" . $row['first_name'] . "</td>";
        echo "<td>" . $row['booking_date'] . "</td>";
        echo "<td>" . $row['total_amount'] . "</td>";
        echo "<td>" . $row['amount_paid'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?> 