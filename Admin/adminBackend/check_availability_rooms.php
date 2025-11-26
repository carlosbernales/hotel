<?php
include '../adminBackend/mydb.php';
// Inputs from AJAX
$check_in = $_GET['check_in'];   // datetime-local format
$check_out = $_GET['check_out'];
$booking_id = $_GET['booking_id'];

// Manila timezone
date_default_timezone_set('Asia/Manila');

// Convert inputs to MySQL DATETIME
$check_in_dt = date('Y-m-d H:i:s', strtotime($check_in));
$check_out_dt = date('Y-m-d H:i:s', strtotime($check_out));

// Array for unavailable rooms
$unavailable = [];

// 1. Occupied rooms from active bookings
$sql = "
    SELECT br.room_type_id
    FROM booked_rooms br
    JOIN bookings b ON br.booking_id = b.booking_id
    WHERE b.status NOT IN ('finished','rejected','rescheduled')
      AND b.booking_id != $booking_id
      AND (
            b.check_in <= '$check_out_dt' AND b.check_out >= '$check_in_dt'
          )
";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $unavailable[$row['room_type_id']] = 'Occupied';
}

// 2. Rooms with finished bookings + 3h maintenance
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
    $maintenance_end = $finished_check_out + 3 * 3600; // 3h after check_out

    // If selected check_in or check_out overlaps with maintenance window
    if (strtotime($check_in_dt) < $maintenance_end && strtotime($check_out_dt) > $finished_check_out) {
        $available_time = date('Y-m-d H:i', $maintenance_end);
        $unavailable[$row['room_type_id']] = "Maintenance, available at $available_time";
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode(['unavailable' => $unavailable]);
