<?php
require_once 'db.php';

header('Content-Type: application/json');

$type = $_GET['type'];
$data = array(
    'labels' => array(),
    'values' => array()
);

// Get the last 6 months
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $data['labels'][] = date('M Y', strtotime($month));
    
    if ($type == 'reservations') {
        $query = "SELECT COUNT(*) as count FROM booking WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'";
    } else {
        $query = "SELECT COUNT(*) as count FROM booking WHERE DATE_FORMAT(check_in_date, '%Y-%m') = '$month' AND status = 'checked_in'";
    }
    
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $data['values'][] = (int)$row['count'];
    } else {
        $data['values'][] = 0;
    }
}

echo json_encode($data);
?>