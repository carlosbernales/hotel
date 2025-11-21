<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $room_number_id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM room_numbers WHERE room_number_id = ?");
    $stmt->bind_param("i", $room_number_id);

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?room_management");
        exit();
    } else {
        echo "Error deleting room: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>