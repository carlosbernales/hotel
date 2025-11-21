<?php
// Ensure database connection
if (!isset($conn)) {
    $database = new Database();
    $conn = $database->connect();
}

// Get notifications
$user_id = $_SESSION['user_id'] ?? 0;
$notifications_query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($notifications_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();

// Get messages
$messages_query = "SELECT DISTINCT c.*, u.name, u.profile_image,
    (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
    (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time
    FROM conversations c
    JOIN users u ON (c.user1_id = u.id OR c.user2_id = u.id)
    WHERE c.user1_id = ? OR c.user2_id = ?
    ORDER BY last_message_time DESC LIMIT 5";
$stmt = $conn->prepare($messages_query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$messages = $stmt->get_result();

// Count unread notifications
$unread_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($unread_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$unread_count = $stmt->get_result()->fetch_assoc()['count'];
?>

<!-- Notification and Message Icons -->
<div class="ms-auto d-flex align-items-center">
    <!-- Notifications Dropdown -->
    <div class="dropdown me-3">
        <button class="btn btn-link text-dark position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell fs-5"></i>
            <?php if ($unread_count > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $unread_count; ?>
            </span>
            <?php endif; ?>
        </button>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown">
            <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">Notifications</h6>
                <a href="notification.php" class="text-primary text-decoration-none">View All</a>
            </div>
            <div class="notification-body" style="max-height: 400px; overflow-y: auto;">
                <?php if ($notifications->num_rows > 0): ?>
                    <?php while ($notification = $notifications->fetch_assoc()): ?>
                        <div class="dropdown-item notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" style="white-space: normal;">
                            <div class="d-flex align-items-center">
                                <i class="<?php echo $notification['icon'] ?? 'fas fa-bell'; ?> me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                                    <div class="notification-text small"><?php echo htmlspecialchars($notification['message']); ?></div>
                                    <div class="notification-time small text-muted">
                                        <?php echo date('M d, H:i', strtotime($notification['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="dropdown-item text-center py-3">
                        <i class="fas fa-bell-slash mb-2"></i>
                        <p class="mb-0">No notifications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Messages Dropdown -->
    <div class="dropdown">
        <button class="btn btn-link text-dark" type="button" id="messagesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-envelope fs-5"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end message-dropdown" aria-labelledby="messagesDropdown">
            <div class="message-header d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">Messages</h6>
                <a href="message.php" class="text-primary text-decoration-none">View All</a>
            </div>
            <div class="message-body" style="max-height: 400px; overflow-y: auto;">
                <?php if ($messages->num_rows > 0): ?>
                    <?php while ($message = $messages->fetch_assoc()): ?>
                        <div class="dropdown-item message-item" style="white-space: normal;">
                            <div class="d-flex align-items-center">
                                <div class="message-avatar me-3">
                                    <?php if ($message['profile_image']): ?>
                                        <img src="<?php echo htmlspecialchars($message['profile_image']); ?>" 
                                             class="rounded-circle" width="40" height="40" alt="Profile">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($message['name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="message-sender"><?php echo htmlspecialchars($message['name']); ?></div>
                                    <div class="message-preview small text-muted">
                                        <?php echo htmlspecialchars(substr($message['last_message'], 0, 50)) . '...'; ?>
                                    </div>
                                    <div class="message-time small text-muted">
                                        <?php echo date('M d, H:i', strtotime($message['last_message_time'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="dropdown-item text-center py-3">
                        <i class="fas fa-envelope mb-2"></i>
                        <p class="mb-0">No messages</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.notification-dropdown,
.message-dropdown {
    width: 350px;
    padding: 0;
    border: 0;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
}

.notification-item,
.message-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.notification-item:hover,
.message-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-title {
    font-weight: 600;
    font-size: 0.9rem;
}

.notification-text,
.message-preview {
    color: #6c757d;
    margin-top: 0.25rem;
}

.message-sender {
    font-weight: 600;
    font-size: 0.9rem;
}

.dropdown-item:last-child {
    border-bottom: none;
}

@media (max-width: 576px) {
    .notification-dropdown,
    .message-dropdown {
        width: 300px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Mark notification as read when clicked
    $('.notification-item').click(function() {
        const notificationId = $(this).data('id');
        if (!$(this).hasClass('read')) {
            $.post('ajax/mark_notification_read.php', {
                notification_id: notificationId
            }, function(response) {
                if (response.success) {
                    location.reload();
                }
            });
        }
    });

    // Open chat when message item is clicked
    $('.message-item').click(function() {
        const conversationId = $(this).data('conversation');
        window.location.href = 'message.php?conversation=' + conversationId;
    });
});
</script>
