<?php
require_once 'session.php';
require_once 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming request
error_log("Received notification request");

// Get JSON data from request
$rawData = file_get_contents('php://input');
error_log("Raw request data: " . $rawData);

$data = json_decode($rawData, true);
error_log("Decoded data: " . print_r($data, true));

// Validate required fields
if (!isset($data['user_id']) || !isset($data['title']) || !isset($data['message'])) {
    error_log("Missing required fields in notification data");
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    // Validate database connection
    if (!$con) {
        throw new Exception("Database connection failed");
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO notifications (user_id, title, message, type, reference_id, icon, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP())";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }

    // Log the values being bound
    error_log("Binding parameters: " . print_r([
        'user_id' => $data['user_id'],
        'title' => $data['title'],
        'message' => $data['message'],
        'type' => $data['type'],
        'reference_id' => $data['reference_id'],
        'icon' => $data['icon']
    ], true));

    $stmt->bind_param(
        "isssss",
        $data['user_id'],
        $data['title'],
        $data['message'],
        $data['type'],
        $data['reference_id'],
        $data['icon']
    );

    // Execute and check result
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $notification_id = $stmt->insert_id;
    error_log("Notification created successfully with ID: " . $notification_id);

    echo json_encode([
        'status' => 'success',
        'message' => 'Notification created successfully',
        'notification_id' => $notification_id
    ]);

} catch (Exception $e) {
    error_log("Error creating notification: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($con)) {
        $con->close();
    }
} 