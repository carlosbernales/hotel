<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Get POST data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    $password = $_POST['password'] ?? ''; // This is the actual password
    $contact_number = $_POST['contact_number'] ?? '';
    $address = $_POST['address'] ?? '';

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($user_type) || empty($password)) {
        throw new Exception('All required fields must be filled out');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email already exists
    $check_email = "SELECT id FROM userss WHERE email = ?";
    $stmt = $con->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }

    // Hash the password for secure storage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user with both hashed and actual password
    $query = "INSERT INTO userss (first_name, last_name, email, user_type, password, actual_password, contact_number, address) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssssssss", 
        $first_name,
        $last_name,
        $email,
        $user_type,
        $hashed_password,
        $password, // Store actual password
        $contact_number,
        $address
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        throw new Exception('Error adding user');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>