<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $availability = $_POST['availability'];
    $description = $_POST['description'];

    $image_path = null;
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_path']['tmp_name'];
        $fileExt = pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION);
        $randomName = uniqid('menu_', true) . '.' . $fileExt;
        $destination = '../../Admin/adminBackend/menu_item_images/' . $randomName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            $image_path = $randomName;
        } else {
            die("Failed to move uploaded image.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (category_id, name, price, availability, image_path, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsss", $category_id, $name, $price, $availability, $image_path, $description);

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?cafe_management");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>