<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {

    $room_number_id = intval($_GET['id']);

    $room_type_id = intval($_POST['room_type_id']);
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $floor_number = intval($_POST['floor_number']);
    $status = $conn->real_escape_string($_POST['status']);

    $check = $conn->query("
        SELECT * FROM room_numbers 
        WHERE room_number = '$room_number' 
        AND room_number_id != $room_number_id
    ");

    if ($check && $check->num_rows > 0) {
        echo "<script>
                alert('Room number already exists!');
                window.location.href='../../Admin/index.php?room_management';
              </script>";
        exit();
    }

    $sql = "UPDATE room_numbers 
            SET room_type_id=?, room_number=?, floor_number=?, status=? 
            WHERE room_number_id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isisi", $room_type_id, $room_number, $floor_number, $status, $room_number_id);

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?room_management");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}
?>