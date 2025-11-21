<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $room_type_id = intval($_GET['id']);

    $room_type = $conn->real_escape_string($_POST['room_type']);
    $beds = intval($_POST['beds']);
    $price = floatval($_POST['price']);
    $capacity = intval($_POST['capacity']);
    $description = $conn->real_escape_string($_POST['description']);
    $rating = floatval($_POST['rating']);
    $rating_count = intval($_POST['rating_count']);

    $res = $conn->query("SELECT image, image2, image3 FROM room_types WHERE room_type_id = $room_type_id");
    if ($res && $res->num_rows > 0) {
        $existing = $res->fetch_assoc();
    } else {
        die("Room type not found.");
    }

    $imagePath = "../../Admin/adminBackend/room_type_images/";
    $newImages = [
        'image' => $existing['image'],
        'image2' => $existing['image2'],
        'image3' => $existing['image3']
    ];

    $uploadedFiles = $_FILES['images'];

    $anyUploaded = false;
    foreach ($uploadedFiles['name'] as $filename) {
        if (!empty($filename)) {
            $anyUploaded = true;
            break;
        }
    }

    if ($anyUploaded) {
        foreach ($newImages as $key => $file) {
            if (!empty($file) && file_exists($imagePath . $file)) {
                unlink($imagePath . $file);
                $newImages[$key] = null;
            }
        }

        for ($i = 0; $i < min(3, count($uploadedFiles['name'])); $i++) {
            if (!empty($uploadedFiles['name'][$i])) {
                $ext = pathinfo($uploadedFiles['name'][$i], PATHINFO_EXTENSION);
                $newName = uniqid('room_', true) . '.' . $ext;

                if (move_uploaded_file($uploadedFiles['tmp_name'][$i], $imagePath . $newName)) {
                    if ($i == 0)
                        $newImages['image'] = $newName;
                    if ($i == 1)
                        $newImages['image2'] = $newName;
                    if ($i == 2)
                        $newImages['image3'] = $newName;
                }
            }
        }
    }

    $stmt = $conn->prepare("
        UPDATE room_types 
        SET room_type=?, beds=?, price=?, capacity=?, description=?, rating=?, rating_count=?, image=?, image2=?, image3=?
        WHERE room_type_id=?
    ");

    $stmt->bind_param(
        "sidididsssi",
        $room_type,
        $beds,
        $price,
        $capacity,
        $description,
        $rating,
        $rating_count,
        $newImages['image'],
        $newImages['image2'],
        $newImages['image3'],
        $room_type_id
    );

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?room_management");
        exit;
    } else {
        echo "Error updating room type: " . $conn->error;
    }
}
?>