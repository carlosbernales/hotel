<?php
include '../adminBackend/mydb.php';

function generateRandomFilename($length = 12)
{
    return bin2hex(random_bytes($length));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $max_guests = intval($_POST['max_guests']);
    $duration = intval($_POST['duration']);
    $time_limit = $conn->real_escape_string($_POST['time_limit']);

    $max_pax = intval($_POST['max_pax']);
    $is_available = intval($_POST['is_available']);
    $notes = $conn->real_escape_string($_POST['notes']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $conn->real_escape_string($_POST['status']);

    $targetDir = '../../Admin/adminBackend/event_packages_images/';

    $image_path = $image_path2 = $image_path3 = null;

    if (isset($_FILES['image'])) {
        $images = $_FILES['image'];
        $countImages = count($images['name']);

        for ($i = 0; $i < min($countImages, 3); $i++) {
            if ($images['error'][$i] === 0) {
                $ext = pathinfo($images['name'][$i], PATHINFO_EXTENSION);
                $randomName = generateRandomFilename() . '.' . $ext;
                $destination = $targetDir . $randomName;

                if (move_uploaded_file($images['tmp_name'][$i], $destination)) {
                    if ($i === 0)
                        $image_path = $randomName;
                    if ($i === 1)
                        $image_path2 = $randomName;
                    if ($i === 2)
                        $image_path3 = $randomName;
                }
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO event_packages 
        (name, price, max_guests, duration, time_limit, max_pax, is_available, notes, description, status, image_path, image_path2, image_path3) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sdiisisssssss",
        $name,
        $price,
        $max_guests,
        $duration,
        $time_limit,
        $max_pax,
        $is_available,
        $notes,
        $description,
        $status,
        $image_path,
        $image_path2,
        $image_path3
    );

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?event_management");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>