<?php
session_start();
require_once 'db_con.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $roomType = $_POST['room_type'] ?? '';
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields.'
        ]);
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    try {
        // Insert into database
        $sql = "INSERT INTO inquiries (name, email, subject, message, room_type, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name, $email, $subject, $message, $roomType]);
        
        if ($result) {
            // Optional: Send email notification
            $to = "your-email@example.com"; // Change to your email
            $emailSubject = "New Website Inquiry: $subject";
            $emailBody = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";
            $headers = "From: $email";
            
            // Uncomment to enable email sending
            // mail($to, $emailSubject, $emailBody, $headers);
            
            echo json_encode([
                'success' => true,
                'message' => 'Your message has been sent successfully!'
            ]);
        } else {
            throw new Exception("Failed to save inquiry");
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?> 