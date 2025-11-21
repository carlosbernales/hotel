<?php
// Script to add new discount types
require_once 'db.php';

// Only start session if one hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to set session message
function setMessage($message, $type = 'error') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Initialize response
$response = array(
    'success' => false,
    'message' => 'Invalid request'
);

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = strtolower(trim($_POST['name']));
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Always set percentage to 10.00
    $percentage = 10.00;
    
    // Validate name
    if (empty($name)) {
        setMessage('Discount type name is required.');
        header('Location: discount_settings.php');
        exit;
    }
    
    // Check if name already exists
    $check_query = "SELECT id FROM discount_types WHERE name = ?";
    $stmt = $con->prepare($check_query);
    
    if ($stmt === false) {
        setMessage('Database error: ' . $con->error);
        header('Location: discount_settings.php');
        exit;
    }
    
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        setMessage('A discount type with this name already exists.');
        header('Location: discount_settings.php');
        exit;
    }
    
    // Insert new discount type
    $insert_query = "INSERT INTO discount_types (name, percentage, description, is_active) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($insert_query);
    
    if ($stmt === false) {
        setMessage('Database error: ' . $con->error);
        header('Location: discount_settings.php');
        exit;
    }
    
    $stmt->bind_param('sdsi', $name, $percentage, $description, $is_active);
    
    if ($stmt->execute()) {
        setMessage('Discount type added successfully!', 'success');
    } else {
        setMessage('Error adding discount type: ' . $con->error);
    }
    
    header('Location: discount_settings.php');
    exit;
} else {
    $response['message'] = "Invalid request method.";
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = 'danger';
}

// Check if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Return JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Redirect to discount settings page for regular requests
    header('Location: discount_settings.php');
    exit;
}
?> 