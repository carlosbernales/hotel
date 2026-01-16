<?php
include '../adminBackend/mydb.php';

if (!isset($_GET['id'])) {
    die('Invalid request.');
}

$id = intval($_GET['id']);
$title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
$description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';

if ($title === '' || $description === '') {
    die('Title and Description cannot be empty.');
}

$sql = "UPDATE about_content SET title='$title', description='$description' WHERE id=$id";

if ($conn->query($sql)) {
    header('Location: ../index.php?contact_management');
    exit();
} else {
    die('Error updating About Us: ' . $conn->error);
}
?>