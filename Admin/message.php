<?php
require_once 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get unique users who have messages
function getUniqueUsers($con) {
    $sql = "SELECT DISTINCT m.user_id, 
           CASE 
               WHEN u.name IS NOT NULL THEN u.name
               WHEN u.first_name IS NOT NULL THEN CONCAT(u.first_name, ' ', COALESCE(u.last_name, ''))
               WHEN m.user_id = 0 THEN 'General'
               WHEN m.user_id = -1 THEN 'All Customers'
               ELSE CONCAT('Customer ', m.user_id)
           END as user_name,
           u.email as user_email,
           (SELECT MAX(created_at) FROM messages WHERE user_id = m.user_id) as last_message_time,
           (SELECT COUNT(*) FROM messages WHERE user_id = m.user_id AND read_status = 0 AND sender_type = 'user') as unread_count
           FROM messages m 
           LEFT JOIN userss u ON m.user_id = u.id
           WHERE m.user_id > 0
           ORDER BY last_message_time DESC";
    
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to get messages for a specific user
function getUserMessages($con, $userId) {
    $sql = "SELECT m.*, 
        CASE 
            WHEN m.sender_type = 'user' AND u.name IS NOT NULL THEN u.name
            WHEN m.sender_type = 'user' AND u.first_name IS NOT NULL THEN CONCAT(u.first_name, ' ', COALESCE(u.last_name, ''))
            WHEN m.sender_type = 'user' THEN CONCAT('Customer ', m.user_id)
            WHEN m.sender_type = 'admin' THEN 'Admin'
            WHEN m.sender_type = 'system' THEN 'System'
            ELSE CONCAT('Customer ', m.user_id)
        END as sender_name
        FROM messages m 
        LEFT JOIN userss u ON m.user_id = u.id
        WHERE m.user_id = ?
        ORDER BY m.created_at ASC";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result();
}

// Get all unique users with messages
$users = getUniqueUsers($con);

// Get currently selected user (if any)
$selectedUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Get messages for selected user
$messages = null;
if ($selectedUserId > 0) {
    $messages = getUserMessages($con, $selectedUserId);
    
    // Mark messages as read when viewing them
    $updateSql = "UPDATE messages SET read_status = 1 WHERE user_id = ? AND sender_type = 'user'";
    $updateStmt = $con->prepare($updateSql);
    $updateStmt->bind_param("i", $selectedUserId);
    $updateStmt->execute();
}

// Get selected user details
$selectedUser = null;
if ($selectedUserId > 0) {
    $userSql = "SELECT u.*,
                CASE 
                    WHEN u.name IS NOT NULL THEN u.name
                    WHEN u.first_name IS NOT NULL THEN CONCAT(u.first_name, ' ', COALESCE(u.last_name, ''))
                    ELSE CONCAT('Customer ', ?)
                END as display_name,
                (SELECT COUNT(*) FROM messages WHERE user_id = ? AND read_status = 0 AND sender_type = 'user') as unread_count
                FROM userss u WHERE u.id = ?";
    $userStmt = $con->prepare($userSql);
    $userStmt->bind_param("iii", $selectedUserId, $selectedUserId, $selectedUserId);
    $userStmt->execute();
    $selectedUser = $userStmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        /* Messenger-style chat interface */
        body {
            overflow-x: hidden;
        }
        .message-container {
            display: flex;
            height: calc(100vh - 120px);
            background-color: #f0f2f5;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .contact-list {
            width: 320px;
            background-color: #fff;
            border-right: 1px solid #e4e6eb;
            overflow-y: auto;
        }
        .search-container {
            padding: 10px;
            border-bottom: 1px solid #e4e6eb;
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 100;
        }
        .search-box {
            width: 100%;
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #e4e6eb;
            background-color: #f0f2f5;
        }
        .contact-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f2f5;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .contact-item:hover {
            background-color: #f5f6f7;
        }
        .contact-item.active {
            background-color: #e6f2fe;
        }
        .contact-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 10px;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }
        .contact-info {
            flex-grow: 1;
            overflow: hidden;
        }
        .contact-name {
            font-weight: 500;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .last-message {
            font-size: 13px;
            color: #65676b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .contact-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 5px;
        }
        .message-time {
            font-size: 12px;
            color: #65676b;
            margin-bottom: 5px;
        }
        .unread-badge {
            background-color: #1877f2;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        .chat-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }
        .chat-header {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background-color: #f5f6f7;
            border-bottom: 1px solid #e4e6eb;
        }
        .chat-header .contact-avatar {
            width: 40px;
            height: 40px;
        }
        .chat-header .contact-name {
            font-weight: 600;
            margin-bottom: 0;
        }
        .messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
        }
        .message-bubble {
            max-width: 70%;
            padding: 8px 12px;
            border-radius: 18px;
            margin-bottom: 8px;
            word-wrap: break-word;
            position: relative;
        }
        .user-message {
            align-self: flex-start;
            background-color: #e4e6eb;
            border-top-left-radius: 4px;
        }
        .admin-message {
            align-self: flex-end;
            background-color: #0084ff;
            color: white;
            border-top-right-radius: 4px;
        }
        .system-message {
            align-self: center;
            background-color: #f2f2f2;
            color: #65676b;
            font-style: italic;
            border-radius: 10px;
            font-size: 13px;
            padding: 5px 10px;
            max-width: 90%;
            text-align: center;
        }
        .message-time-small {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
            display: inline-block;
        }
        .message-input {
            display: flex;
            padding: 10px;
            background-color: #f0f2f5;
            border-top: 1px solid #e4e6eb;
        }
        .message-input input {
            flex-grow: 1;
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #e4e6eb;
            outline: none;
        }
        .send-button {
            margin-left: 10px;
            background-color: #0084ff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .send-button:hover {
            background-color: #0070d9;
        }
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #65676b;
            padding: 20px;
            text-align: center;
        }
        .empty-state i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #ddd;
        }
        .empty-state p {
            font-size: 16px;
            max-width: 70%;
        }
        .message-date-divider {
            display: flex;
            align-items: center;
            margin: 10px 0;
            color: #65676b;
            font-size: 12px;
        }
        .message-date-divider:before,
        .message-date-divider:after {
            content: "";
            flex-grow: 1;
            height: 1px;
            background-color: #e4e6eb;
            margin: 0 10px;
        }
        .message-sender {
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 2px;
        }
        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .message-container {
                flex-direction: column;
                height: calc(100vh - 140px);
            }
            .contact-list {
                width: 100%;
                max-height: 40%;
            }
            .chat-area {
                min-height: 60%;
            }
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Messages</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Customer Messages</h3>
                    </div>
                    <div class="panel-body">
                        <div class="message-container">
                            <!-- Contact List -->
                            <div class="contact-list">
                                <div class="search-container">
                                    <input type="text" class="search-box" placeholder="Search contacts..." id="search-contacts">
                                </div>
                                
                                <?php if ($users->num_rows > 0): ?>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                        <div class="contact-item <?php echo ($selectedUserId == $user['user_id']) ? 'active' : ''; ?>" 
                                            data-user-id="<?php echo $user['user_id']; ?>"
                                            onclick="location.href='message.php?user_id=<?php echo $user['user_id']; ?>'">
                                            <div class="contact-avatar">
                                                <?php echo substr(htmlspecialchars($user['user_name']), 0, 1); ?>
                                            </div>
                                            <div class="contact-info">
                                                <div class="contact-name"><?php echo htmlspecialchars($user['user_name']); ?></div>
                                                <div class="last-message">
                                                    <?php 
                                                        // Get last message
                                                        $lastMsgSql = "SELECT message, sender_type FROM messages WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
                                                        $lastMsgStmt = $con->prepare($lastMsgSql);
                                                        $lastMsgStmt->bind_param("i", $user['user_id']);
                                                        $lastMsgStmt->execute();
                                                        $lastMsg = $lastMsgStmt->get_result()->fetch_assoc();
                                                        
                                                        if ($lastMsg) {
                                                            echo ($lastMsg['sender_type'] == 'admin' ? 'You: ' : '') . 
                                                                htmlspecialchars(substr($lastMsg['message'], 0, 30)) . 
                                                                (strlen($lastMsg['message']) > 30 ? '...' : '');
                                                        } else {
                                                            echo "No messages yet";
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="contact-meta">
                                                <div class="message-time">
                                                    <?php 
                                                        $timestamp = strtotime($user['last_message_time']);
                                                        $now = time();
                                                        $diff = $now - $timestamp;
                                                        
                                                        if ($diff < 86400) { // Less than 24 hours
                                                            echo date('g:i A', $timestamp);
                                                        } else if ($diff < 604800) { // Less than a week
                                                            echo date('D', $timestamp);
                                                        } else {
                                                            echo date('M j', $timestamp);
                                                        }
                                                    ?>
                                                </div>
                                                <?php if ($user['unread_count'] > 0): ?>
                                                    <div class="unread-badge"><?php echo $user['unread_count']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fa fa-comments"></i>
                                        <p>No messages yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Chat Area -->
                            <div class="chat-area">
                                <?php if ($selectedUserId > 0 && $selectedUser): ?>
                                    <div class="chat-header">
                                        <div class="contact-avatar">
                                            <?php echo substr(htmlspecialchars($selectedUser['display_name'] ?? 'Customer ' . $selectedUserId), 0, 1); ?>
                                        </div>
                                        <div class="contact-info">
                                            <div class="contact-name"><?php echo htmlspecialchars($selectedUser['display_name'] ?? 'Customer ' . $selectedUserId); ?></div>
                                            <div class="last-message">
                                                <?php echo $selectedUser['email'] ?? ''; ?>
                                                <?php if ($selectedUser['unread_count'] > 0): ?>
                                                    <span class="badge badge-danger"><?php echo $selectedUser['unread_count']; ?> unread</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="messages" id="messages-container">
                                        <?php 
                                        if ($messages && $messages->num_rows > 0): 
                                            $previousDate = '';
                                            while ($message = $messages->fetch_assoc()):
                                                $messageDate = date('Y-m-d', strtotime($message['created_at']));
                                                // Add date divider if day changes
                                                if ($messageDate != $previousDate):
                                                    $formattedDate = date('F j, Y', strtotime($message['created_at']));
                                                    echo "<div class='message-date-divider'>{$formattedDate}</div>";
                                                    $previousDate = $messageDate;
                                                endif;
                                        ?>
                                            <div class="message-bubble <?php echo $message['sender_type']; ?>-message">
                                                <?php if ($message['sender_type'] == 'user'): ?>
                                                    <div class="message-sender"><?php echo htmlspecialchars($message['sender_name']); ?></div>
                                                <?php endif; ?>
                                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                <div class="message-time-small">
                                                    <?php echo date('g:i A', strtotime($message['created_at'])); ?>
                                                </div>
                                            </div>
                                        <?php 
                                            endwhile; 
                                        else: 
                                        ?>
                                            <div class="empty-state">
                                                <i class="fa fa-comments-o"></i>
                                                <p>No messages in this conversation yet. Send a message to get started.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <form class="message-input" id="send-message-form">
                                        <input type="hidden" name="user_id" value="<?php echo $selectedUserId; ?>">
                                        <input type="text" name="message" placeholder="Type a message..." id="message-input" autocomplete="off">
                                        <button type="submit" class="send-button">
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fa fa-comments-o"></i>
                                        <p>Select a conversation from the left to view messages.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Scroll to bottom of messages on load
            var messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            
            // Handle search in contacts
            $('#search-contacts').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('.contact-item').each(function() {
                    var contactName = $(this).find('.contact-name').text().toLowerCase();
                    if (contactName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // Send message form handling
            $('#send-message-form').on('submit', function(e) {
                e.preventDefault();
                
                var userId = $('input[name="user_id"]').val();
                var message = $('#message-input').val().trim();
                
                if (!message) {
                    return false;
                }
                
                // Disable form during submission
                var $form = $(this);
                var $input = $('#message-input');
                var $button = $('.send-button');
                
                $input.prop('disabled', true);
                $button.prop('disabled', true);
                
                // Create temporary message bubble to show immediately
                var currentTime = new Date();
                var timeString = currentTime.toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'});
                var tempMessage = $('<div class="message-bubble admin-message">' + 
                                  message.replace(/\n/g, '<br>') + 
                                  '<div class="message-time-small">' + timeString + '</div></div>');
                
                $('#messages-container').append(tempMessage);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Send message via AJAX
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: {
                        user_id: userId,
                        message: message,
                        sender_type: 'admin'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Clear input
                            $('#message-input').val('');
                            
                            // Update message with server timestamp if provided
                            if (response.timestamp) {
                                tempMessage.find('.message-time-small').text(response.timestamp);
                            }
                            
                            // Update last message in contact list
                            updateContactLastMessage(userId, message);
                        } else {
                            alert('Error: ' + (response.message || 'Failed to send message'));
                            tempMessage.addClass('error').css('background-color', '#ffdddd');
                        }
                    },
                    error: function() {
                        alert('Error: Could not connect to server');
                        tempMessage.addClass('error').css('background-color', '#ffdddd');
                    },
                    complete: function() {
                        $input.prop('disabled', false).focus();
                        $button.prop('disabled', false);
                    }
                });
            });
            
            // Function to update contact's last message in sidebar
            function updateContactLastMessage(userId, message) {
                var $contact = $('.contact-item[data-user-id="' + userId + '"]');
                if ($contact.length) {
                    $contact.find('.last-message').text('You: ' + (message.length > 30 ? message.substring(0, 27) + '...' : message));
                    
                    // Update time
                    var now = new Date();
                    var hours = now.getHours();
                    var minutes = now.getMinutes();
                    var ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    var timeString = hours + ':' + minutes + ' ' + ampm;
                    
                    $contact.find('.message-time').text(timeString);
                    
                    // Move contact to top of list
                    var $contactList = $('.contact-list');
                    $contact.detach();
                    $contactList.children('.search-container').after($contact);
                }
            }
            
            // Auto-focus message input when a conversation is selected
            if ($('#message-input').length) {
                $('#message-input').focus();
            }
        });
    </script>
</body>
</html> 