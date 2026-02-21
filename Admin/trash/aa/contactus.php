<?php
session_start();
require_once 'db_con.php'; // Include your PDO connection setup

header('Content-Type: application/json'); // Ensure JSON response

$response = [
    'success' => false,
    'message' => 'An unexpected error occurred.'
];

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate and sanitize inputs
        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        // Validate required fields
        if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }
        
        // Insert into database
        $sql = "INSERT INTO contact_messages (first_name, last_name, email, message, created_at) 
                VALUES (:first_name, :last_name, :email, :message, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        
        if ($stmt->execute()) {
            // Send email notification (optional)
            $to = "casaestelahotelcafe@gmail.com"; // Replace with your email
            $subject = "New Contact Form Submission";
            $emailBody = "You have received a new message from the contact form:\n\n";
            $emailBody .= "Name: " . $firstName . " " . $lastName . "\n";
            $emailBody .= "Email: " . $email . "\n";
            $emailBody .= "Message:\n" . $message . "\n";
            
            // Use mail() function or a proper email library like PHPMailer
            @mail($to, $subject, $emailBody);
            
            echo json_encode(['success' => true, 'message' => 'Thank you for your message. We will get back to you soon!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'There was an error sending your message. Please try again later.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
