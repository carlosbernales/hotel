<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_type_id = intval($_POST['room_type_id']);
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $floor_number = intval($_POST['floor_number']);
    $status = $conn->real_escape_string($_POST['status']);

    if (empty($room_type_id) || empty($room_number) || empty($floor_number) || empty($status)) {
        die("All fields are required.");
    }

    $sql = "INSERT INTO room_numbers (room_type_id, room_number, floor_number, status) 
            VALUES ($room_type_id, '$room_number', $floor_number, '$status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../../Admin/index.php?room_management");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

} else {
    header("Location: ../../Admin/index.php?room_management");
    exit();
}

?>