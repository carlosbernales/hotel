<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $table_name = $conn->real_escape_string($_POST['table_name']);
    $capacity = intval($_POST['capacity']);
    $available_tables = intval($_POST['available_tables']);
    $status = $conn->real_escape_string($_POST['status']);
    $reason = isset($_POST['reason']) ? $conn->real_escape_string($_POST['reason']) : null;
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : null;

    $imageDir = '../../Admin/adminBackend/table_types_images/';

    $sql = "SELECT img1, img2, img3, img4, img5 FROM table_types WHERE id = $id";
    $result = $conn->query($sql);
    $existing_images = $result->fetch_assoc();

    $updatedImages = $existing_images;

    $newImagesUploaded = false;
    foreach ($_FILES['images']['name'] as $name) {
        if (!empty($name)) {
            $newImagesUploaded = true;
            break;
        }
    }

    if ($newImagesUploaded) {

        foreach ($existing_images as $img) {
            if (!empty($img) && file_exists($imageDir . $img)) {
                unlink($imageDir . $img);
            }
        }

        for ($i = 0; $i < 5; $i++) {
            if (!empty($_FILES['images']['name'][$i])) {
                $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                $newName = bin2hex(random_bytes(8)) . '.' . $ext;
                move_uploaded_file($_FILES['images']['tmp_name'][$i], $imageDir . $newName);
                $updatedImages['img' . ($i + 1)] = $newName;
            } else {
                $updatedImages['img' . ($i + 1)] = $existing_images['img' . ($i + 1)];
            }
        }
    }

    $updateSql = "
        UPDATE table_types SET
            table_name = '$table_name',
            capacity = $capacity,
            available_tables = $available_tables,
            status = '$status',
            reason = " . ($reason ? "'$reason'" : "NULL") . ",
            description = " . ($description ? "'$description'" : "NULL") . ",
            img1 = '" . ($updatedImages['img1'] ?? NULL) . "',
            img2 = '" . ($updatedImages['img2'] ?? NULL) . "',
            img3 = '" . ($updatedImages['img3'] ?? NULL) . "',
            img4 = '" . ($updatedImages['img4'] ?? NULL) . "',
            img5 = '" . ($updatedImages['img5'] ?? NULL) . "'
        WHERE id = $id
    ";

    if ($conn->query($updateSql)) {
        header("Location: ../../Admin/index.php?table_management");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
}
?>