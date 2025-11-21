<?php
require_once 'db.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the incoming request
error_log('Received request: ' . print_r($_POST, true));

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = ['success' => false, 'message' => 'Invalid request method'];
    error_log('Error: ' . $response['message']);
    echo json_encode($response);
    exit;
}

// Get and validate input
$itemId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$isAvailable = isset($_POST['is_available']) ? ($_POST['is_available'] === '1' ? 1 : 0) : 1;

error_log("Processing item ID: $itemId, is_available: $isAvailable");

if (!$itemId) {
    $response = ['success' => false, 'message' => 'Invalid item ID'];
    error_log('Error: ' . $response['message']);
    echo json_encode($response);
    exit;
}

// Verify the item exists
$check_query = "SELECT id FROM menu_items WHERE id = ?";
$check_stmt = mysqli_prepare($con, $check_query);

if (!$check_stmt) {
    $response = ['success' => false, 'message' => 'Database prepare error: ' . mysqli_error($con)];
    error_log('Error: ' . $response['message']);
    echo json_encode($response);
    exit;
}

mysqli_stmt_bind_param($check_stmt, 'i', $itemId);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($result) === 0) {
    $response = ['success' => false, 'message' => 'Menu item not found'];
    error_log('Error: ' . $response['message']);
    echo json_encode($response);
    exit;
}

// Update the menu item status
$query = "UPDATE menu_items SET availability = ? WHERE id = ?";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ii', $isAvailable, $itemId);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        $response = ['success' => true, 'message' => 'Status updated successfully'];
        error_log('Success: ' . $response['message'] . ' for item ID: ' . $itemId);
    } else {
        $response = ['success' => false, 'message' => 'Failed to update menu item status: ' . mysqli_error($con)];
        error_log('Error: ' . $response['message']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($con)];
    error_log('Error: ' . $response['message']);
}

echo json_encode($response);

// Close database connection
if (isset($check_stmt)) {
    mysqli_stmt_close($check_stmt);
}
if (isset($con)) {
    mysqli_close($con);
}
?>
