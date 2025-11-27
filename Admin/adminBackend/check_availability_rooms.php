<?php
include '../adminBackend/mydb.php';

$check_in = $_GET['check_in'];
$booking_id = $_GET['booking_id'];

date_default_timezone_set('Asia/Manila');
$check_in_dt = strtotime($check_in);
$unavailable = [];

$sql = "
    SELECT br.room_type_id, b.check_out, b.status
    FROM booked_rooms br
    JOIN bookings b ON br.booking_id = b.booking_id
    WHERE b.booking_id != $booking_id
      AND b.status NOT IN ('rejected','rescheduled')
";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $room_id = $row['room_type_id'];
    $check_out_time = strtotime($row['check_out']);

    if (in_array($row['status'], ['pending', 'accepted'])) {
        if ($check_in_dt <= $check_out_time + 3 * 3600) {
            $available_time = date('Y-m-d H:i', $check_out_time + 3 * 3600);
            $unavailable[$room_id] = "Occupied, Available at $available_time";
        }
    } elseif ($row['status'] === 'finished') {
        $maintenance_end = $check_out_time + 3 * 3600;
        if ($check_in_dt <= $maintenance_end) {
            $available_time = date('Y-m-d H:i', $maintenance_end);
            $unavailable[$room_id] = "Maintenance until $available_time";
        }
    }
}

header('Content-Type: application/json');
echo json_encode(['unavailable' => $unavailable]);
