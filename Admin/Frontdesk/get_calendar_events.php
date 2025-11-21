<?php
require_once "db.php";

// Get current date
$current_date = date('Y-m-d');

// Fetch all check-ins and check-outs from bookings
$query = "SELECT 
    DATE(check_in) as date,
    COUNT(*) as count,
    'check-in' as type
FROM bookings 
WHERE check_in >= ?
AND status = 'Pending'
GROUP BY DATE(check_in)
UNION ALL
SELECT 
    DATE(check_out) as date,
    COUNT(*) as count,
    'check-out' as type
FROM bookings 
WHERE check_out >= ?
AND status = 'Pending'
GROUP BY DATE(check_out)";

$result = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($result, "ss", $current_date, $current_date);
mysqli_stmt_execute($result);
$result = mysqli_stmt_get_result($result);

$events = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['type'] === 'check-in') {
            $events[] = array(
                'title' => $row['count'] . ' Check-in',
                'start' => $row['date'],
                'display' => 'list-item',
                'extendedProps' => array(
                    'type' => 'check-in',
                    'count' => $row['count']
                )
            );
        } else {
            $events[] = array(
                'title' => $row['count'] . ' Check-out',
                'start' => $row['date'],
                'display' => 'list-item',
                'extendedProps' => array(
                    'type' => 'check-out',
                    'count' => $row['count']
                )
            );
        }
    }
}

// Return events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?> 