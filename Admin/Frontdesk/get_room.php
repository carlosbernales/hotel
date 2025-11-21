<?php
require_once 'db.php';

$response = array('success' => false, 'error' => '', 'data' => null);

if (isset($_GET['room_type_id'])) {
    $room_type_id = intval($_GET['room_type_id']);
    
    $query = "SELECT * FROM room_types WHERE room_type_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $room_type_id);
    
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