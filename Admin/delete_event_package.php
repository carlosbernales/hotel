<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Log function that works even if database fails
function logError($message) {
    $logFile = __DIR__ . '/delete_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    error_log($message); // Also log to PHP error log
}

// Include database connection
require_once "db.php";

// Check database connection
if (!$con) {
    $error = "Failed to connect to database: " . mysqli_connect_error();
    logError($error);
    die($error);
}

// Set character set
if (!$con->set_charset("utf8mb4")) {
    $error = "Error loading character set utf8mb4: " . $con->error;
    logError($error);
    die($error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to perform this action');
}

// Get package ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Invalid package ID');
}

logError("=== Starting package deletion for ID: $id ===");

// 1. First verify the package exists
$check_sql = "SELECT id, name FROM event_packages WHERE id = ?";
if ($stmt = $con->prepare($check_sql)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();
    
    if (!$package) {
        logError("Error: Package with ID $id not found in database");
        die("Package not found");
    }
    
    logError("Found package: ID={$package['id']}, Name={$package['name']}");
} else {
    $error = "Prepare failed: " . $con->error;
    logError($error);
    die($error);
}

// 2. Check for active bookings
$check_bookings = "SELECT COUNT(*) as count FROM event_bookings 
                 WHERE package_type = ? AND status NOT IN ('cancelled', 'finished', 'completed')";
                 
if ($stmt = $con->prepare($check_bookings)) {
    $stmt->bind_param("s", $package['name']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row['count'] > 0) {
        $error = "Cannot delete: Package has active bookings";
        logError($error);
        die($error);
    }
} else {
    $error = "Prepare failed (check bookings): " . $con->error;
    logError($error);
    die($error);
}

// Start transaction
logError("Starting database transaction...");
if (!$con->begin_transaction()) {
    $error = "Failed to start transaction: " . $con->error;
    logError($error);
    die($error);
}

try {
    // 3. Delete associated images first
    $get_images = "SELECT image_path, image_path2, image_path3 FROM event_packages WHERE id = ?";
    if ($stmt = $con->prepare($get_images)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = $result->fetch_assoc();
        $stmt->close();
        
        // Delete image files
        foreach (['image_path', 'image_path2', 'image_path3'] as $image_field) {
            if (!empty($images[$image_field]) && file_exists($images[$image_field])) {
                if (!unlink($images[$image_field])) {
                    logError("Warning: Could not delete image: " . $images[$image_field]);
                } else {
                    logError("Deleted image: " . $images[$image_field]);
                }
            }
        }
    } else {
        throw new Exception("Failed to prepare image select: " . $con->error);
    }
    
    // 4. Delete the package
    logError("Attempting to delete package from database...");
    $delete_sql = "DELETE FROM event_packages WHERE id = ?";
    if ($stmt = $con->prepare($delete_sql)) {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Delete failed: " . $stmt->error);
        }
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        if ($affected === 0) {
            throw new Exception("No rows affected. Package may not exist or already deleted.");
        }
        
        logError("Successfully deleted package. Rows affected: " . $affected);
        
        // Commit transaction
        if (!$con->commit()) {
            throw new Exception("Commit failed: " . $con->error);
        }
        
        logError("Transaction committed successfully");
        $_SESSION['success_message'] = 'Package deleted successfully';
        
    } else {
        throw new Exception("Prepare failed for delete: " . $con->error);
    }
    
} catch (Exception $e) {
    // Rollback on error
    $con->rollback();
    $error = $e->getMessage();
    logError("ERROR: " . $error);
    $_SESSION['error_message'] = "Failed to delete package: " . $error;
}

// Close connection
$con->close();

// Redirect back to event management page
header('Location: event_management.php');
exit();