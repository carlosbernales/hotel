<?php
ob_start(); // Start output buffering
require_once 'db.php'; // Corrected path: db.php is in the same directory

header('Content-Type: application/json');

$response = [
    'success' => false,
    'rooms' => [],
    'message' => ''
];

if (isset($_GET['room_type_id']) && !empty($_GET['room_type_id'])) {
    $room_type_id = mysqli_real_escape_string($con, $_GET['room_type_id']);

    // Query to get available room numbers for the given room_type_id
    // Assuming 'status' column exists in room_numbers table and 'active' means available
    $query = "SELECT room_number_id, room_number FROM room_numbers 
              WHERE room_type_id = '$room_type_id' AND status = 'active' ORDER BY room_number ASC";
    
    $result = mysqli_query($con, $query);

    if ($result) {
        $rooms = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = [
                'id' => $row['room_number_id'],
                'number' => $row['room_number']
            ];
        }
        $response['success'] = true;
        $response['rooms'] = $rooms;
        $response['message'] = 'Available rooms fetched successfully.';
    } else {
        $response['message'] = 'Database query failed: ' . mysqli_error($con);
    }
} else {
    $response['message'] = 'Invalid room_type_id provided.';
}

ob_clean(); // Clean buffer before outputting JSON
echo json_encode($response);

mysqli_close($con);
?>
