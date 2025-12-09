<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $table_name = $conn->real_escape_string($_POST['table_name']);
    $capacity = intval($_POST['capacity']);
    $available_tables = intval($_POST['available_tables']);
    $status = $conn->real_escape_string($_POST['status']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $description = $conn->real_escape_string($_POST['description']);

    $image_paths = [];

    if (isset($_FILES['images'])) {
        $totalImages = count($_FILES['images']['name']);
        if ($totalImages > 5)
            $totalImages = 5;

        for ($i = 0; $i < $totalImages; $i++) {
            $tmp_name = $_FILES['images']['tmp_name'][$i];
            $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);

            $randomName = bin2hex(random_bytes(8)) . "." . $extension;

            $targetDir = "../../Admin/adminBackend/table_types_images/";
            $targetFile = $targetDir . $randomName;

            if (move_uploaded_file($tmp_name, $targetFile)) {
                $image_paths[] = $randomName;
            }
        }
    }

    for ($i = count($image_paths); $i < 5; $i++) {
        $image_paths[$i] = NULL;
    }

    $sql = "INSERT INTO table_types 
            (table_name, capacity, available_tables, img1, img2, img3, img4, img5, status, reason, description)
            VALUES 
            ('$table_name', $capacity, $available_tables,
             '{$image_paths[0]}', '{$image_paths[1]}', '{$image_paths[2]}',
             '{$image_paths[3]}', '{$image_paths[4]}',
             '$status', " . ($reason ? "'$reason'" : "NULL") . ",
             " . ($description ? "'$description'" : "NULL") . ")";

    if ($conn->query($sql) === TRUE) {
        header('Location: ../../Admin/index.php?table_management');
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>