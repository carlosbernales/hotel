<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        $uploadDir = "../../Admin/adminBackend/offers_images/";
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $randomName = uniqid('offer_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $randomName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {

            $stmt = $conn->prepare(
                "INSERT INTO offers (title, image, discount, description) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $title, $randomName, $description);

            if ($stmt->execute()) {
                header('Location: ../index.php?offers');
                exit;
            }
        }
    }
}

header('Location: ../index.php?offers&error=1');
exit;
