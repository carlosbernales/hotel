<?php
include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
    header('Location: ../offers.php');
    exit;
}

$id = intval($_GET['id']);

$title = trim($_POST['title']);
$discount = trim($_POST['discount']);
$description = trim($_POST['description']);

$uploadDir = "../../Admin/adminBackend/offers_images/";

$stmt = $conn->prepare("SELECT image FROM offers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();

if (!$current) {
    header('Location: ../index.php?offers');
    exit;
}

$imageName = $current['image'];


if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {

    $oldImagePath = $uploadDir . $imageName;

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $newImage = uniqid('offer_', true) . '.' . $ext;
    $newPath = $uploadDir . $newImage;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $newPath)) {

        if (!empty($imageName) && file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        $imageName = $newImage;
    }
}


$update = $conn->prepare("
    UPDATE offers 
    SET title = ?, image = ?, discount = ?, description = ?
    WHERE id = ?
");
$update->bind_param(
    "ssssi",
    $title,
    $imageName,
    $discount,
    $description,
    $id
);

$update->execute();

header('Location: ../index.php?offers');
exit;
