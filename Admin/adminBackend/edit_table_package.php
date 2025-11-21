<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $package_name = $conn->real_escape_string($_POST['package_name']);
    $price = floatval($_POST['price']);
    $capacity = intval($_POST['capacity']);
    $description = $conn->real_escape_string($_POST['description']);
    $available_tables = intval($_POST['available_tables']);
    $status = $conn->real_escape_string($_POST['status']);

    $imagePath = '../../Admin/adminBackend/table_packages_image/';

    $sql = "SELECT image1, image2, image3, image4, image5 FROM table_packages WHERE id = $id";
    $result = $conn->query($sql);
    $existing_images = $result->fetch_assoc();

    $imagesToUpdate = $existing_images;

    $newImagesUploaded = false;
    foreach ($_FILES['images']['name'] as $name) {
        if (!empty($name)) {
            $newImagesUploaded = true;
            break;
        }
    }

    if ($newImagesUploaded) {
        foreach ($existing_images as $img) {
            if (!empty($img) && file_exists($imagePath . $img)) {
                unlink($imagePath . $img);
            }
        }

        for ($i = 0; $i < 5; $i++) {
            if (!empty($_FILES['images']['name'][$i])) {
                $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                $newName = uniqid('pkg_') . '.' . $ext;
                move_uploaded_file($_FILES['images']['tmp_name'][$i], $imagePath . $newName);
                $imagesToUpdate['image' . ($i + 1)] = $newName;
            } else {
                $imagesToUpdate['image' . ($i + 1)] = null;
            }
        }
    }

    $updateSql = "UPDATE table_packages SET 
        package_name = '$package_name',
        price = $price,
        capacity = $capacity,
        description = '$description',
        available_tables = $available_tables,
        status = '$status',
        image1 = '" . ($imagesToUpdate['image1'] ?? '') . "',
        image2 = '" . ($imagesToUpdate['image2'] ?? '') . "',
        image3 = '" . ($imagesToUpdate['image3'] ?? '') . "',
        image4 = '" . ($imagesToUpdate['image4'] ?? '') . "',
        image5 = '" . ($imagesToUpdate['image5'] ?? '') . "'
        WHERE id = $id";

    if ($conn->query($updateSql)) {
        header("Location: ../../Admin/index.php?table_management");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>