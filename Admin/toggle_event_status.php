<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once "db.php";

// Function to log errors
function logStatusChange($message) {
    $logFile = __DIR__ . '/status_change.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    logStatusChange("Unauthorized access attempt to toggle status");
    $_SESSION['error_message'] = 'You must be logged in to perform this action';
    header('Location: login.php');
    exit();
}

// Set default response header
header('Content-Type: application/json; charset=UTF-8');

// Function to send JSON response
function sendJsonResponse($success, $data = []) {
    $response = array_merge(['success' => $success], $data);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = "Invalid or missing package ID";
    logStatusChange($error);
    http_response_code(400);
    sendJsonResponse(false, ['message' => $error]);
}

$package_id = (int)$_GET['id'];
logStatusChange("Processing status toggle for package ID: $package_id");

try {
    // Start transaction
    $con->begin_transaction();
    
    // Get current status and check for active bookings
    $query = "SELECT id, name, status, 
              (SELECT COUNT(*) FROM event_bookings 
               WHERE package_name = ep.name 
               AND status NOT IN ('cancelled', 'finished', 'completed')) as active_bookings 
              FROM event_packages ep 
              WHERE id = ? FOR UPDATE";

    if (!$stmt = $con->prepare($query)) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param("i", $package_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();
    
    if (!$package) {
        throw new Exception("Package not found with ID: " . $package_id);
    }
    
    logStatusChange("Package found - ID: {$package['id']}, Name: {$package['name']}, Current Status: {$package['status']}, Active Bookings: {$package['active_bookings']}");
    
    // Determine new status based on current status
    if ($package['active_bookings'] > 0) {
        // If there are active bookings, we can't mark as available
        if ($package['status'] !== 'Occupied') {
            $new_status = 'Occupied';
            $message = "Status set to Occupied due to active bookings";
        } else {
            $new_status = 'Occupied';
            $message = "Cannot mark as Available - Package has active bookings";
        }
    } else {
        // Toggle between Available and Occupied
        $new_status = ($package['status'] === 'Available') ? 'Occupied' : 'Available';
        $message = "Package status changed to " . $new_status;
    }
    
    // Update the status
    $update_query = "UPDATE event_packages SET status = ? WHERE id = ?";
    if (!$update_stmt = $con->prepare($update_query)) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $update_stmt->bind_param("si", $new_status, $package_id);
    if (!$update_stmt->execute()) {
        throw new Exception("Update failed: " . $update_stmt->error);
    }
    $update_stmt->close();
    
    // Commit transaction
    $con->commit();
    
    logStatusChange("Success: $message");
    $_SESSION['success_message'] = $message;
    sendJsonResponse(true, [
        'newStatus' => $new_status,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con)) {
        $con->rollback();
    }
    
    $error = $e->getMessage();
    logStatusChange("Error: $error");
    http_response_code(500);
    sendJsonResponse(false, ['message' => $error]);
}

// Close connection
if (isset($con)) {
    $con->close();
} 