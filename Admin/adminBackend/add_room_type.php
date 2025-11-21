<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_type = $conn->real_escape_string($_POST['room_type']);
    $beds = $conn->real_escape_string($_POST['beds']);
    $price = floatval($_POST['price']);
    $capacity = intval($_POST['capacity']);
    $description = $conn->real_escape_string($_POST['description']);

    $rating = floatval($_POST['rating']);
    $rating_count = intval($_POST['rating_count']);

    $imagePaths = ["", "", ""];
    $uploadDir = "../../Admin/adminBackend/room_type_images/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $totalFiles = count($files['name']);
        $totalFiles = min($totalFiles, 3);

        for ($i = 0; $i < $totalFiles; $i++) {
            $tmpName = $files['tmp_name'][$i];
            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $fileName = uniqid('room_', true) . "." . $ext;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetFile)) {
                $imagePaths[$i] = $fileName;
            }
        }
    }

    $sql = "INSERT INTO room_types 
            (room_type, beds, price, capacity, description, image, image2, image3, rating, rating_count) 
            VALUES 
            ('$room_type', '$beds', '$price', '$capacity', '$description',
            '{$imagePaths[0]}', '{$imagePaths[1]}', '{$imagePaths[2]}',
            '$rating', '$rating_count')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../../Admin/index.php?room_management");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>