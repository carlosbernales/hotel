<?php
require_once 'db.php';

$response = array('success' => false, 'error' => '', 'data' => null);

if (isset($_GET['id'])) {
    $room_id = intval($_GET['id']);
    
    $query = "SELECT rn.room_number_id, rn.room_number, rn.status, 
                     rt.room_type_id, rt.room_type, rt.price, 
                     rt.capacity, rt.beds, rt.description
              FROM room_numbers rn
              JOIN room_types rt ON rn.room_type_id = rt.room_type_id 
              WHERE rn.room_number_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $room_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($room = mysqli_fetch_assoc($result)) {
            $response['success'] = true;
            $response['data'] = $room;
        } else {
            $response['error'] = 'Room type not found';
        }
    } else {
        $response['error'] = mysqli_error($con);
    }
    
    mysqli_stmt_close($stmt);
} else {
    $response['error'] = 'Room type ID not provided';
}

header('Content-Type: application/json');
echo json_encode($response);
?> 