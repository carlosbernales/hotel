<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Get parameters from URL (for easy testing)
$message_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$test_reply = isset($_GET['reply']) ? $_GET['reply'] : "This is a test reply from direct PHP script";

echo "<h1>Test Update Reply</h1>";

if ($message_id <= 0) {
    echo "<p style='color: red;'>Please provide a valid message ID using the 'id' parameter.</p>";
    echo "<p>Example: test_update_reply.php?id=12</p>";
    exit;
}

echo "<p>Attempting to add reply to message ID: {$message_id}</p>";
echo "<p>Reply text: \"{$test_reply}\"</p>";

// First check if the message exists
$check_sql = "SELECT id, message, replies FROM messages WHERE id = ?";
$check_stmt = $con->prepare($check_sql);

if (!$check_stmt) {
    die("<p style='color: red;'>Prepare failed: " . $con->error . "</p>");
}

$check_stmt->bind_param('i', $message_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color: red;'>Message ID {$message_id} not found!</p>";
    exit;
}

$message_data = $result->fetch_assoc();
$check_stmt->close();

echo "<h2>Original Message</h2>";
echo "<p><strong>Message:</strong> " . htmlspecialchars($message_data['message']) . "</p>";
echo "<p><strong>Current Replies:</strong> " . (empty($message_data['replies']) ? "<em>No replies</em>" : nl2br(htmlspecialchars($message_data['replies']))) . "</p>";

// Format the admin reply
$admin_reply = "Admin: " . $test_reply;

// Determine new replies content
if (empty($message_data['replies'])) {
    $new_replies = $admin_reply;
} else {
    $new_replies = $message_data['replies'] . "\n" . $admin_reply;
}

echo "<h2>Updates</h2>";
echo "<p><strong>Adding Reply:</strong> " . htmlspecialchars($admin_reply) . "</p>";
echo "<p><strong>New Replies Content:</strong></p>";
echo "<pre>" . htmlspecialchars($new_replies) . "</pre>";

// Update the database
$update_sql = "UPDATE messages SET replies = ? WHERE id = ?";
$update_stmt = $con->prepare($update_sql);

if (!$update_stmt) {
    die("<p style='color: red;'>Prepare update failed: " . $con->error . "</p>");
}

$update_stmt->bind_param('si', $new_replies, $message_id);

if ($update_stmt->execute()) {
    $rows_affected = $update_stmt->affected_rows;
    echo "<p style='color: green;'><strong>Success!</strong> Updated message with new reply. Rows affected: {$rows_affected}</p>";
} else {
    echo "<p style='color: red;'><strong>Error:</strong> Failed to update message: " . $update_stmt->error . "</p>";
}

$update_stmt->close();

// Verify the update
$verify_sql = "SELECT replies FROM messages WHERE id = ?";
$verify_stmt = $con->prepare($verify_sql);
$verify_stmt->bind_param('i', $message_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();
$verify_data = $verify_result->fetch_assoc();
$verify_stmt->close();

echo "<h2>Verification</h2>";
echo "<p><strong>Replies after update:</strong></p>";
echo "<pre>" . (empty($verify_data['replies']) ? "<em>EMPTY!</em>" : htmlspecialchars($verify_data['replies'])) . "</pre>";

echo "<p><a href='message.php'>Return to Messages</a></p>";
?> 