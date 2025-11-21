<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Include database connection
require_once 'db.php';

echo "<h2>Fix Message Replies Tool</h2>";

// Check for post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
    $reply_text = isset($_POST['reply_text']) ? trim($_POST['reply_text']) : '';
    
    if ($message_id > 0 && !empty($reply_text)) {
        // Update the message
        $stmt = $con->prepare("UPDATE messages SET replies = ? WHERE id = ?");
        $stmt->bind_param("si", $reply_text, $message_id);
        
        if ($stmt->execute()) {
            echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>
                  Message ID {$message_id} has been updated successfully with the new reply text.
                  </div>";
        } else {
            echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>
                  Error updating message: " . $stmt->error . "
                  </div>";
        }
        
        $stmt->close();
    } else {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>
              Please enter a valid message ID and reply text.
              </div>";
    }
}

// Get all messages to display
$messages = $con->query("SELECT id, message, replies, created_at FROM messages ORDER BY id DESC");
?>

<div style="margin: 20px 0;">
    <form method="post" action="">
        <h3>Update Message Reply</h3>
        <div style="margin-bottom: 10px;">
            <label for="message_id">Message ID:</label>
            <input type="number" name="message_id" id="message_id" required style="padding: 5px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label for="reply_text">Reply Text:</label>
            <textarea name="reply_text" id="reply_text" rows="4" cols="50" required style="padding: 5px;"></textarea>
        </div>
        <div>
            <button type="submit" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
                Update Reply
            </button>
        </div>
    </form>
</div>

<div style="margin: 20px 0;">
    <h3>Current Messages</h3>
    <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>ID</th>
                <th>Message</th>
                <th>Replies</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $messages->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : ''); ?></td>
                <td>
                    <?php if (!empty($row['replies'])): ?>
                        <pre style="max-height: 100px; overflow: auto; white-space: pre-wrap;"><?php echo htmlspecialchars($row['replies']); ?></pre>
                    <?php else: ?>
                        <em>No replies</em>
                    <?php endif; ?>
                </td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <button onclick="populateForm(<?php echo $row['id']; ?>, <?php echo json_encode($row['replies'] ?: ''); ?>)" style="padding: 5px 10px; background-color: #2196F3; color: white; border: none; cursor: pointer;">
                        Edit
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<p><a href="message.php">Return to messages page</a></p>

<script>
function populateForm(id, replies) {
    document.getElementById('message_id').value = id;
    document.getElementById('reply_text').value = replies;
    // Scroll to the form
    document.getElementById('message_id').scrollIntoView();
}
</script> 