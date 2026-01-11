<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No contact ID provided.");
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_text = $conn->real_escape_string($_POST['display_text']);
    $link = $conn->real_escape_string($_POST['link']);
    $display_order = intval($_POST['display_order']);
    $is_external = ($_POST['is_external'] == 1) ? 1 : 0;


    $sql = "UPDATE contact_info 
            SET display_text = '$display_text', 
                link = '$link', 
                display_order = $display_order, 
                is_external = $is_external
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../index.php?contact_management");
        exit;
    } else {
        echo "Error updating contact: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
}
?>