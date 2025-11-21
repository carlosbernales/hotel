<?php
include '../adminBackend/mydb.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT image1, image2, image3, image4, image5 FROM table_packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image1, $image2, $image3, $image4, $image5);
    $stmt->fetch();
    $stmt->close();

    $images = [$image1, $image2, $image3, $image4, $image5];
    $path = "../../Admin/adminBackend/table_packages_image/";

    foreach ($images as $img) {
        if (!empty($img) && file_exists($path . $img)) {
            unlink($path . $img);
        }
    }

    $stmt = $conn->prepare("DELETE FROM table_packages WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../../Admin/index.php?table_management");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "No ID specified.";
}

$conn->close();
?>