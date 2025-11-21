<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['user_id']) || !isset($_POST['new_password'])) {
        throw new Exception('Missing required parameters');
    }

    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $query = "UPDATE userss SET password = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $con->error);
    }

    $stmt->bind_param('si', $hashed_password, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update password: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('No user found with the provided ID');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Password has been reset successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 