<?php
include 'mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'];
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $availability = $_POST['availability'];

    $result = $conn->query("SELECT image_path FROM menu_items WHERE id = $id");
    $existingImage = '';
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $existingImage = $row['image_path'];
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../Admin/adminBackend/menu_item_images/';
        $tmpName = $_FILES['image']['tmp_name'];

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = bin2hex(random_bytes(8)) . '.' . $ext;
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($tmpName, $uploadFile)) {
            if (!empty($existingImage) && file_exists($uploadDir . $existingImage)) {
                unlink($uploadDir . $existingImage);
            }
            $image_path_sql = ", image_path = '$imageName'";
        } else {
            $image_path_sql = "";
        }
    } else {
        $image_path_sql = "";
    }

    $sql = "UPDATE menu_items SET 
                category_id = '$category_id',
                name = '$name',
                price = '$price',
                description = '$description',
                availability = '$availability'
                $image_path_sql
            WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../../Admin/index.php?cafe_management");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>