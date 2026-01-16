<?php
session_start();
include '../adminBackend/mydb.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if (isset($_POST['save_profile'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $nameParts = explode(' ', $fullname, 2);
    $firstName = $nameParts[0];
    $lastName = $nameParts[1] ?? '';

    $uploadDir = "../../Admin/adminBackend/user_photo/";
    $profilePhoto = null;

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $fileTmp = $_FILES['profile_photo']['tmp_name'];
        $fileExt = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $randomName = uniqid('user_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $randomName;

        if (move_uploaded_file($fileTmp, $uploadPath)) {
            $profilePhoto = $randomName;

            $oldPhoto = '';
            $result = $conn->query("SELECT profile_photo FROM userss WHERE id = $userId");
            if ($result && $result->num_rows > 0) {
                $oldPhoto = $result->fetch_assoc()['profile_photo'];
            }

            if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
                unlink($uploadDir . $oldPhoto);
            }
        }
    }

    $fields = "first_name=?, last_name=?, email=?, contact_number=?";
    $params = [$firstName, $lastName, $email, $contact];

    if ($password) {
        $fields .= ", password=?";
        $params[] = $password;
    }
    if ($profilePhoto) {
        $fields .= ", profile_photo=?";
        $params[] = $profilePhoto;
    }

    $params[] = $userId;

    $types = str_repeat('s', count($params) - 1) . 'i';
    $stmt = $conn->prepare("UPDATE userss SET $fields WHERE id=?");
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: ../../user_profile.php?success=1");
        exit;
    } else {
        echo "Update failed: " . $stmt->error;
    }
}
?>