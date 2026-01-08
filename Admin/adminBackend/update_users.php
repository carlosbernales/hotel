<?php
include '../adminBackend/mydb.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $user_type = $_POST['user_type'];
    $is_verified = $_POST['is_verified'];

    $password = trim($_POST['password']);
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_sql = ", password = ?";
    } else {
        $hashed_password = null;
        $password_sql = "";
    }

    if ($stmt = $conn->prepare("UPDATE userss SET first_name=?, last_name=?, email=?, contact_number=?, address=?, user_type=?, is_verified=? $password_sql WHERE id=?")) {
        if (!empty($password)) {
            $stmt->bind_param("sssssssii", $first_name, $last_name, $email, $contact_number, $address, $user_type, $is_verified, $hashed_password, $id);
        } else {
            $stmt->bind_param("ssssssii", $first_name, $last_name, $email, $contact_number, $address, $user_type, $is_verified, $id);
        }

        if ($stmt->execute()) {
            header("Location: ../index.php?users");

            exit();
        } else {
            echo "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error;
    }
}
?>