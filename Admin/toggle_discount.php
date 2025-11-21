<?php
// Script to toggle discount type active status
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
    
    // Get current status
    $query = "SELECT is_active FROM discount_types WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $discount = mysqli_fetch_assoc($result);
        $current_status = $discount['is_active'];
        
        // Toggle status (0 -> 1, 1 -> 0)
        $new_status = $current_status ? 0 : 1;
        $status_text = $new_status ? 'enabled' : 'disabled';
        
        // Update status - remove reference to updated_at column
        $update_query = "UPDATE discount_types SET is_active = $new_status WHERE id = '$id'";
        
        if (mysqli_query($con, $update_query)) {
            // Set success message
            $_SESSION['message'] = "Discount type has been " . ($new_status ? "activated" : "deactivated") . " successfully.";
            $_SESSION['message_type'] = 'success';
            
            $response['success'] = true;
            $response['message'] = "Discount type has been " . ($new_status ? "activated" : "deactivated") . " successfully.";
            $response['new_status'] = $new_status;
        } else {
            // Set error message
            $_SESSION['message'] = "Error updating discount type: " . mysqli_error($con);
            $_SESSION['message_type'] = 'danger';
            
            $response['message'] = "Error updating discount type: " . mysqli_error($con);
        }
    } else {
        // Set error message
        $_SESSION['message'] = "Discount type not found.";
        $_SESSION['message_type'] = 'danger';
        
        $response['message'] = "Discount type not found.";
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