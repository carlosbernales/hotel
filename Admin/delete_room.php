<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = array('success' => false, 'error' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_type_id'])) {
    $room_type_id = intval($_POST['room_type_id']);
    
    // First get the images to delete them
    $query = "SELECT image, image2, image3 FROM room_types WHERE room_type_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $room_type_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Delete the image files if they exist
        $images = array($row['image'], $row['image2'], $row['image3']);
        foreach ($images as $image) {
            if (!empty($image) && file_exists($image)) {
                unlink($image);
            }
        }
    }
    
    // Now delete the room type
    $query = "DELETE FROM room_types WHERE room_type_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $room_type_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
    } else {
        $response['error'] = mysqli_error($con);
    }
    
    mysqli_stmt_close($stmt);
}

header('Content-Type: application/json');
echo json_encode($response); 