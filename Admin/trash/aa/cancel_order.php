<?php
require_once 'db_con.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['orderId']) || !isset($_POST['reason'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$orderId = $_POST['orderId'];
$reason = $_POST['reason'];
$userId = $_SESSION['user_id'];

try {
    // Verify the order belongs to the user
    $stmt = mysqli_prepare($con, "SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'Pending'");
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order or not eligible for cancellation']);
        exit;
    }

    // Update order status and add cancellation reason
    $stmt = mysqli_prepare($con, "UPDATE orders SET status = 'Cancelled', cancellation_reason = ?, cancelled_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $reason, $orderId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel order']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
