<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $package_name = $conn->real_escape_string($_POST['package_name']);
    $price = floatval($_POST['price']);
    $capacity = intval($_POST['capacity']);
    $description = $conn->real_escape_string($_POST['description']);
    $available_tables = intval($_POST['available_tables']);
    $status = $conn->real_escape_string($_POST['status']);

    $image_paths = [];

    if (isset($_FILES['images'])) {
        $totalImages = count($_FILES['images']['name']);
        if ($totalImages > 5)
            $totalImages = 5;

        for ($i = 0; $i < $totalImages; $i++) {
            $tmp_name = $_FILES['images']['tmp_name'][$i];
            $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);

            $randomName = bin2hex(random_bytes(8)) . "." . $extension;

            $targetDir = "../../Admin/adminBackend/table_packages_image/";
            $targetFile = $targetDir . $randomName;

            if (move_uploaded_file($tmp_name, $targetFile)) {
                $image_paths[] = $randomName;
            }
        }
    }


    for ($i = count($image_paths); $i < 5; $i++) {
        $image_paths[$i] = NULL;
    }

    $sql = "INSERT INTO table_packages 
            (package_name, price, capacity, description, available_tables, status, image1, image2, image3, image4, image5) 
            VALUES 
            ('$package_name', $price, $capacity, '$description', $available_tables, '$status', 
             '{$image_paths[0]}', '{$image_paths[1]}', '{$image_paths[2]}', '{$image_paths[3]}', '{$image_paths[4]}')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../../Admin/index.php?table_management");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>