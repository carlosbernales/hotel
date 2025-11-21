<?php
include 'db.php';
include 'auth_check.php';

header('Content-Type: application/json');

if(isset($_POST['id']) && isset($_POST['status']) && isset($_POST['type'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    
    // Select table based on booking type
    switch($type) {
        case 'room':
            $table = 'bookings';
            $status_field = 'payment_status';
            break;
        case 'event':
            $table = 'event_bookings';
            $status_field = 'payment_status';
            break;
        case 'table':
            $table = 'table_bookings';
            $status_field = 'payment_status';
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid booking type']);
            exit;
    }
    
    $query = "UPDATE $table SET $status_field = '$status' WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request parameters']);
}
?>
