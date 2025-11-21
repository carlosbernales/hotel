<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_package_options.php');
    exit();
}

$note_id = $_GET['id'];

$query = "DELETE FROM package_notes WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $note_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Note deleted successfully!";
} else {
    $_SESSION['error_message'] = "Error deleting note: " . $con->error;
}

header('Location: manage_package_options.php');
exit(); 