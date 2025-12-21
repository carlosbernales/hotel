<?php
include '../adminBackend/mydb.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['datetime']) || trim($_POST['datetime']) === '') {
    echo json_encode([
        'conflict' => true,
        'message' => 'No datetime received'
    ]);
    exit;
}

$selected = str_replace('T', ' ', $_POST['datetime']) . ':00';

$orderEventHours = 4;
$orderPrepHours = 2;

$eventBookingBufferHours = 6;

$sqlOrders = "
    SELECT date_time
    FROM orders_table
    WHERE status NOT IN ('Finished','Rejected','Cancelled')
    AND date_time BETWEEN
        DATE_SUB(?, INTERVAL {$orderPrepHours} HOUR)
        AND
        DATE_ADD(?, INTERVAL {$orderEventHours} HOUR)
    ORDER BY date_time ASC
    LIMIT 1
";

$stmtOrders = $conn->prepare($sqlOrders);
$stmtOrders->bind_param("ss", $selected, $selected);
$stmtOrders->execute();
$resultOrders = $stmtOrders->get_result();

if ($row = $resultOrders->fetch_assoc()) {
    $bookedTime = $row['date_time'];

    $blockedStart = date('Y-m-d H:i:s', strtotime("$bookedTime -{$orderPrepHours} hours"));
    $blockedEnd = date('Y-m-d H:i:s', strtotime("$bookedTime +{$orderEventHours} hours"));

    $availableBefore = date('g:i A', strtotime("$blockedStart -{$orderPrepHours} hours"));
    $availableAfter = date('g:i A', strtotime("$blockedEnd +1 hour"));

    echo json_encode([
        'conflict' => true,
        'booked_time' => date('g:i A', strtotime($bookedTime)),
        'available_before' => $availableBefore,
        'available_after' => $availableAfter,
        'message' => 'Booking at <b>' . date('g:i A', strtotime($bookedTime)) . '</b>.'
    ]);
    exit;
}

$sqlEvents = "
    SELECT date_time_start, date_time_end
    FROM event_bookings
    WHERE booking_status NOT IN ('Finished','Rejected','Cancelled')
    AND (
        ? BETWEEN DATE_SUB(date_time_start, INTERVAL {$eventBookingBufferHours} HOUR)
          AND DATE_ADD(date_time_end, INTERVAL {$eventBookingBufferHours} HOUR)
    )
    ORDER BY date_time_start ASC
    LIMIT 1
";

$stmtEvents = $conn->prepare($sqlEvents);
$stmtEvents->bind_param("s", $selected);
$stmtEvents->execute();
$resultEvents = $stmtEvents->get_result();

if ($row = $resultEvents->fetch_assoc()) {
    $eventStart = $row['date_time_start'];
    $eventEnd = $row['date_time_end'];

    $blockedStart = date('Y-m-d H:i:s', strtotime("$eventStart -{$eventBookingBufferHours} hours"));
    $blockedEnd = date('Y-m-d H:i:s', strtotime("$eventEnd +{$eventBookingBufferHours} hours"));

    $availableBefore = date('g:i A', strtotime("$blockedStart -1 hour"));
    $availableAfter = date('g:i A', strtotime("$blockedEnd +1 hour"));

    echo json_encode([
        'conflict' => true,
        'booked_time' => date('g:i A', strtotime($eventStart)) . ' - ' . date('g:i A', strtotime($eventEnd)),
        'available_before' => $availableBefore,
        'available_after' => $availableAfter,
        'message' => 'Event booked at <b>' . date('g:i A', strtotime($eventStart)) . ' - ' . date('g:i A', strtotime($eventEnd)) . '</b>.'
    ]);
    exit;
}

echo json_encode([
    'conflict' => false,
    'message' => 'Time slot available.'
]);
exit;
?>