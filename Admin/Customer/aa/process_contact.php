<?php
session_start();
require 'includes/database.php';

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Please login to send a message.");
    }

    // Get and sanitize form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $userId = $_SESSION['user_id'];

    // Validate form data
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        throw new Exception("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address.");
    }

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, subject, message, user_id, status)
        VALUES (?, ?, ?, ?, ?, 'unread')
    ");
    
    $stmt->execute([$name, $email, $subject, $message, $userId]);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We\'ll get back to you soon.'
    ]);

} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
