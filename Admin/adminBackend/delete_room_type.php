<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {

    $room_type_id = intval($_GET['id']);

    if ($room_type_id <= 0) {
        die("Invalid room type ID.");
    }

    $getImages = $conn->prepare("SELECT image, image2, image3 FROM room_types WHERE room_type_id = ?");
    $getImages->bind_param("i", $room_type_id);
    $getImages->execute();
    $getImages->bind_result($img1, $img2, $img3);
    $getImages->fetch();
    $getImages->close();

    $folder = "../../Admin/adminBackend/room_type_images/";

    $images = [$img1, $img2, $img3];

    foreach ($images as $img) {
        if (!empty($img)) {
            $fullPath = $folder . $img;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    $stmt = $conn->prepare("DELETE FROM room_types WHERE room_type_id = ?");
    $stmt->bind_param("i", $room_type_id);

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?room_management");
        exit();
    } else {
        echo "Error deleting room type: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>