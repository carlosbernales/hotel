<?php
include '../adminBackend/mydb.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);

$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$booking_id = $_GET['booking_id'] ?? 0;

if (!$check_in || !$check_out) {
    echo json_encode([]);
    exit;
}

$check_in_dt = strtotime($check_in);
$check_out_dt = strtotime($check_out);

$roomNumbers = [];
$rn_res = $conn->query("SELECT * FROM room_numbers WHERE status='active'");
while ($rn = $rn_res->fetch_assoc()) {
    $roomNumbers[$rn['room_type_id']][] = $rn;
}

$sql = "
SELECT br.room_number_fk_id, b.check_in, b.check_out, b.status
FROM booked_rooms br
JOIN bookings b ON br.booking_id = b.booking_id
WHERE b.booking_id != $booking_id
AND b.status NOT IN ('rejected', 'cancelled', 'uncounted')
";

$res = $conn->query($sql);

$unavailable = [];
$maintenance_times = [];

while ($row = $res->fetch_assoc()) {
    $booked_start = strtotime($row['check_in']);
    $booked_end = strtotime($row['check_out']);

    if ($row['status'] === 'finished') {
        $booked_end += 3 * 3600;
    }

    if (!($check_out_dt < $booked_start || $check_in_dt > $booked_end)) {
        $unavailable[$row['room_number_fk_id']] = true;

        if ($row['status'] === 'finished') {
            $maintenance_times[$row['room_number_fk_id']] = date('Y-m-d H:i', $booked_end);
        }
    }
}

foreach ($roomNumbers as $type_id => &$rooms) {
    $available_rooms = [];
    foreach ($rooms as $r) {
        if (!isset($unavailable[$r['room_number_id']])) {
            $available_rooms[] = $r;
        } elseif (isset($maintenance_times[$r['room_number_id']])) {
            $maintenance_date = date('Y-m-d', strtotime($maintenance_times[$r['room_number_id']]));
            $check_in_date = date('Y-m-d', $check_in_dt);

            if ($check_in_date >= $maintenance_date) {
                $r['note'] = "Available at " . $maintenance_times[$r['room_number_id']];
                $available_rooms[] = $r;
            } else {
                $r['note'] = "Available at " . $maintenance_times[$r['room_number_id']] . " (Unavailable)";
                $r['disabled'] = true;
                $available_rooms[] = $r;
            }
        }
    }
    $rooms = $available_rooms;
}

unset($rooms);

$roomNumbers = array_filter($roomNumbers, fn($rooms) => count($rooms) > 0);

echo json_encode($roomNumbers);
