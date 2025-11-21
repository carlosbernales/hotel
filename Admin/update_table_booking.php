<?php
include 'db.php';
include 'auth_check.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['id']) && isset($data['status'])) {
    $id = mysqli_real_escape_string($con, $data['id']);
    $status = mysqli_real_escape_string($con, $data['status']);
    
    // Update the booking status
    $query = "UPDATE table_bookings SET status = '$status' WHERE id = $id";
    $result = mysqli_query($con, $query);
    
    if($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data received']);
}
?>
