<?php
session_start();
require 'db_con.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    // Insert notification into database
    $stmt = $pdo->prepare("
        INSERT INTO notifications (
            user_id,
            type,
            message,
            reference_id,
            created_at
        ) VALUES (
            :user_id,
            :type,
            :message,
            :reference_id,
            NOW()
        )
    ");

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':type' => $data['type'],
        ':message' => $data['message'],
        ':reference_id' => $data['booking_id']
    ]);

    // Also send notification to admin
    $adminStmt = $pdo->prepare("
        INSERT INTO admin_notifications (
            type,
            message,
            reference_id,
            created_at
        ) VALUES (
            :type,
            :message,
            :reference_id,
            NOW()
        )
    ");

    $adminMessage = $data['type'] === 'booking_cancelled' 
        ? 'A room booking has been cancelled by a user.'
        : 'A table reservation has been cancelled by a user.';

    $adminStmt->execute([
        ':type' => $data['type'],
        ':message' => $adminMessage,
        ':reference_id' => $data['booking_id']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 