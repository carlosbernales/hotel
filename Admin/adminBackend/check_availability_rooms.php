<?php
include '../adminBackend/mydb.php';

$check_in = $_GET['check_in'];
$booking_id = $_GET['booking_id'];

date_default_timezone_set('Asia/Manila');

$check_in_dt = date('Y-m-d H:i:s', strtotime($check_in));
$unavailable = [];

// Occupied rooms: only check if check_in < existing booking check_out
$sql = "
    SELECT br.room_type_id, b.check_out
    FROM booked_rooms br
    JOIN bookings b ON br.booking_id = b.booking_id
    WHERE b.status NOT IN ('finished','rejected','rescheduled')
      AND b.booking_id != $booking_id
";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $existing_check_out = strtotime($row['check_out']);
    if (strtotime($check_in_dt) < $existing_check_out) {
        $unavailable[$row['room_type_id']] = 'Occupied';
    }
}

// Finished bookings + 3h maintenance
$sql2 = "
    SELECT br.room_type_id, b.check_out
    FROM booked_rooms br
    JOIN bookings b ON br.booking_id = b.booking_id
    WHERE b.status = 'finished'
      AND b.booking_id != $booking_id
";
$res2 = $conn->query($sql2);
while ($row = $res2->fetch_assoc()) {
    $finished_check_out = strtotime($row['check_out']);
    $maintenance_end = $finished_check_out + 3 * 3600; // 3 hours

    if (strtotime($check_in_dt) < $maintenance_end) {
        $available_time = date('Y-m-d H:i', $maintenance_end);
        $unavailable[$row['room_type_id']] = "Maintenance until $available_time";
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode(['unavailable' => $unavailable]);
