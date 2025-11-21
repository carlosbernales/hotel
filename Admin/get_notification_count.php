<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit(json_encode(['error' => 'Not logged in']));
}

// Count unread notifications
$count_sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = FALSE";
$result = mysqli_query($con, $count_sql);
$row = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode([
    'count' => (int)$row['count']
]);
?>
