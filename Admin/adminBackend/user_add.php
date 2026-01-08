<?php
include '../adminBackend/mydb.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    $check = $conn->prepare("SELECT id FROM userss WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email already exists.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $is_verified = 1;

    $stmt = $conn->prepare(
        "INSERT INTO userss 
        (first_name, last_name, email, contact_number, address, password, user_type, is_verified)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssssssi",
        $first_name,
        $last_name,
        $email,
        $contact_number,
        $address,
        $hashedPassword,
        $user_type,
        $is_verified
    );

    $stmt->execute();
    header("Location: ../index.php?users");
    exit;
}
