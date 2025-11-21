<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check if the database connection was successful
if (!$con) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Query to get all booked dates from the table_reservations table
$query = "SELECT DISTINCT DATE(reservation_date) as date 
          FROM table_reservations 
          WHERE status IN ('confirmed', 'pending', 'pending_guest')
          AND reservation_date >= CURDATE()";

$result = mysqli_query($con, $query);

if (!$result) {
    echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
    exit;
}

$bookedDates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookedDates[] = $row['date'];
}

// Also check the bookings table for room bookings that might affect table availability
$query = "SELECT DISTINCT DATE(check_in) as date 
          FROM bookings 
          WHERE status IN ('Confirmed', 'Checked In', 'Pending')
          AND check_in >= CURDATE()";

$result = mysqli_query($con, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $date = $row['date'];
        if (!in_array($date, $bookedDates)) {
            $bookedDates[] = $date;
        }
    }
}

echo json_encode([
    'success' => true,
    'dates' => $bookedDates
]);

// Close the database connection
mysqli_close($con);
?>
