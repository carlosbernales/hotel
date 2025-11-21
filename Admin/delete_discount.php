<?php
// Script to delete discount types
require_once 'db.php';
session_start();

// Initialize response
$response = array(
    'success' => false,
    'message' => 'Invalid request'
);

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Delete the discount type
    $query = "DELETE FROM discount_types WHERE id = '$id'";
    
    if (mysqli_query($con, $query)) {
        // Set success message
        $_SESSION['message'] = "Discount type deleted successfully.";
        $_SESSION['message_type'] = 'success';
        
        $response['success'] = true;
        $response['message'] = "Discount type deleted successfully.";
    } else {
        // Set error message
        $_SESSION['message'] = "Error deleting discount type: " . mysqli_error($con);
        $_SESSION['message_type'] = 'danger';
        
        $response['message'] = "Error deleting discount type: " . mysqli_error($con);
    }
} else {
    // Set error message
    $_SESSION['message'] = "No discount ID provided.";
    $_SESSION['message_type'] = 'danger';
    
    $response['message'] = "No discount ID provided.";
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