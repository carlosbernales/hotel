<?php
include 'mydb.php';

if (!isset($_GET['id'])) {
    header('Location: ../index.php?offers');

    exit;
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT image FROM offers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$offer = $result->fetch_assoc();

if ($offer && !empty($offer['image'])) {
    $imagePath = "../../Admin/adminBackend/offers_images/" . $offer['image'];

    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

$del = $conn->prepare("DELETE FROM offers WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();

header('Location: ../index.php?offers');

exit;
