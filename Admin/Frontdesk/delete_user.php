<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Get user ID from POST data
    $user_id = $_POST['user_id'] ?? '';
    
    if (empty($user_id)) {
        throw new Exception('User ID is required');
    }

    // Delete the user
    $query = "DELETE FROM userss WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            throw new Exception('User not found');
        }
    } else {
        throw new Exception('Error deleting user');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 