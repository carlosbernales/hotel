<?php
include '../adminBackend/mydb.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT image_path, image_path2, image_path3 FROM event_packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image1, $image2, $image3);
    $stmt->fetch();
    $stmt->close();

    $imageFolder = "../../Admin/adminBackend/event_packages_images/";

    if (!empty($image1) && file_exists($imageFolder . $image1)) {
        unlink($imageFolder . $image1);
    }
    if (!empty($image2) && file_exists($imageFolder . $image2)) {
        unlink($imageFolder . $image2);
    }
    if (!empty($image3) && file_exists($imageFolder . $image3)) {
        unlink($imageFolder . $image3);
    }

    $stmt = $conn->prepare("DELETE FROM event_packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: ../../Admin/index.php?event_management");
        exit();
    } else {
        $stmt->close();
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid ID.";
}
?>
