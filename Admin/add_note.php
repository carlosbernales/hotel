<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_type = $_POST['note_type'];
    $note_text = $_POST['note_text'];

    if (empty($note_type) || empty($note_text)) {
        $_SESSION['error_message'] = "All fields are required.";
    } else {
        $query = "INSERT INTO package_notes (note_type, note_text) VALUES (?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ss", $note_type, $note_text);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Note added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding note: " . $con->error;
        }
    }
}

header('Location: manage_package_options.php');
exit(); 