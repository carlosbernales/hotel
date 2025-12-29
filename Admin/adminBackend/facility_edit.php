<?php
include '../adminBackend/mydb.php';


if (isset($_GET['id']) && isset($_POST['category_id'], $_POST['display_order'], $_POST['active'])) {
    $id = $_GET['id'];
    $category_id = $_POST['category_id'];
    $display_order = $_POST['display_order'];
    $active = $_POST['active'];
    $name = $_POST['name'];


    $stmt = $conn->prepare("UPDATE facilities SET category_id = ?, display_order = ?, active = ?, name = ? WHERE id = ?");
    $stmt->bind_param("iiisi", $category_id, $display_order, $active, $name, $id);

    if ($stmt->execute()) {
        header("Location: ../index.php?facilities");
        exit();
    } else {
        echo "Error updating facility: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>