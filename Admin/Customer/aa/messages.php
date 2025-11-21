<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_con.php';

// Only start session if one isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get message status if set
$messageStatus = null;
if (isset($_SESSION['message_status'])) {
    $messageStatus = $_SESSION['message_status'];
    unset($_SESSION['message_status']); // Clear the status after using it
}

// Mark all messages as read when visiting this page
$user_id = $_SESSION['user_id'];

// Check connection
if (!isset($con) || !$con) {
    // Handle connection error silently
    $connectionError = true;
} else {
    $connectionError = false;
    
    // Mark messages as read when visiting this page
    $markReadQuery = "UPDATE messages SET read_status = 1 WHERE user_id = ? AND sender_type != 'user'";
    $markReadStmt = mysqli_prepare($con, $markReadQuery);
    
    if ($markReadStmt) {
        mysqli_stmt_bind_param($markReadStmt, "i", $user_id);
        mysqli_stmt_execute($markReadStmt);
        mysqli_stmt_close($markReadStmt);
    }

    // Get all messages for this user with sender information
    $messagesQuery = "SELECT m.*, 
                     CASE 
                        WHEN m.sender_type = 'admin' THEN 'Admin' 
                        WHEN m.sender_type = 'system' THEN 'System' 
                        WHEN m.sender_type = 'user' THEN 'You'
                        ELSE m.sender_type
                     END as sender_name,
                     CASE
                        WHEN m.sender_type = 'admin' THEN 'primary'
                        WHEN m.sender_type = 'system' THEN 'info'
                        WHEN m.sender_type = 'user' THEN 'secondary'
                        ELSE 'dark'
                     END as sender_color
                     FROM messages m
                     WHERE m.user_id = ?
                     ORDER BY m.created_at ASC";  // Changed to ASC for chronological order

    $stmt = mysqli_prepare($con, $messagesQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Output basic HTML structure first, before including nav.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            height: 100vh;
            overflow: hidden;
        }
        
        .chat-container {
            height: calc(100vh - 150px);
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            position: relative;
        }
        
        .chat-sidebar {
            width: 280px;
            background-color: #f8f9fa;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        
        .chat-sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background-color: #f1f3f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
        }
        
        .chat-header .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }
        
        .chat-input {
            padding: 15px;
            border-top: 1px solid #e9ecef;
            background-color: #fff;
        }
        
        .message {
            margin-bottom: 20px;
            max-width: 80%;
            position: relative;
        }
        
        .message.outgoing {
            margin-left: auto;
            text-align: right;
        }
        
        .message.incoming {
            margin-right: auto;
            text-align: left;
        }
        
        .message-content {
            padding: 12px 16px;
            border-radius: 18px;
            display: inline-block;
            word-break: break-word;
            text-align: left;
        }
        
        .message.outgoing .message-content {
            background-color: #007bff;
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message.incoming .message-content {
            background-color: #e9ecef;
            color: #212529;
            border-bottom-left-radius: 4px;
        }
        
        .message.incoming.system .message-content {
            background-color: #17a2b8;
            color: white;
        }
        
        .message.incoming.admin .message-content {
            background-color: #6c757d;
            color: white;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .message-sender {
            margin-bottom: 5px;
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
            text-align: center;
            padding: 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .conversation-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .conversation-item:hover, .conversation-item.active {
            background-color: #e9ecef;
        }
        
        .conversation-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }
        
        .conversation-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .conversation-last-message {
            font-size: 0.85rem;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }
        
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            margin-right: 15px;
            color: #6c757d;
            cursor: pointer;
        }
        
        .sidebar-close {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #6c757d;
            cursor: pointer;
        }
        
        .sidebar-backdrop {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 1;
        }
        
        .sidebar-toggle-fixed {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            font-size: 1.25rem;
        }
        
        .message-day-divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #6c757d;
        }
        
        .message-day-divider::before,
        .message-day-divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .message-day-divider span {
            padding: 0 10px;
            font-size: 0.85rem;
            background-color: #f9f9f9;
        }
        
        /* Typing indicator */
        .typing-indicator {
            display: none;
            margin-bottom: 20px;
            margin-right: auto;
            text-align: left;
        }
        
        .typing-indicator .dots {
            display: inline-block;
            padding: 8px 16px;
            background-color: #e9ecef;
            border-radius: 18px;
            border-bottom-left-radius: 4px;
        }
        
        .typing-indicator .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #adb5bd;
            margin-right: 4px;
            animation: typing-dot 1.4s infinite ease-in-out;
        }
        
        .typing-indicator .dot:nth-child(1) {
            animation-delay: 0s;
        }
        
        .typing-indicator .dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-indicator .dot:nth-child(3) {
            animation-delay: 0.4s;
            margin-right: 0;
        }
        
        @keyframes typing-dot {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .chat-sidebar {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 2;
                width: 80%;
                max-width: 280px;
                transform: translateX(-100%);
            }
            
            .chat-sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar-backdrop.show {
                display: block;
            }
            
            .mobile-toggle, .sidebar-close, .sidebar-toggle-fixed {
                display: block;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Try to include nav, but catch any errors
    try {
        include 'nav.php';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error loading navigation: ' . $e->getMessage() . '</div>';
    }
    ?>

    <div class="container-fluid mt-5 pt-4">
        <?php if ($messageStatus === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your message has been sent.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($messageStatus === 'error'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> There was a problem sending your message. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="chat-container">
            <!-- Backdrop for mobile -->
            <div class="sidebar-backdrop"></div>
            
            <!-- Sidebar with conversation list -->
            <div class="chat-sidebar">
                <div class="chat-sidebar-header">
                    <h5 class="mb-0">Messages</h5>
                    <button class="sidebar-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="chat-sidebar-content">
                    <div class="conversation-item active">
                        <div class="d-flex align-items-center">
                            <div class="conversation-avatar bg-primary">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <div class="conversation-name">E Akomoda Team</div>
                                <div class="conversation-last-message">
                                    <?php 
                                    if (!empty($messages)) {
                                        echo htmlspecialchars(substr($messages[count($messages)-1]['message'], 0, 30));
                                        if (strlen($messages[count($messages)-1]['message']) > 30) echo '...';
                                    } else {
                                        echo 'No messages yet';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main chat area -->
            <div class="chat-main">
                <div class="chat-header">
                    <button class="mobile-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="avatar">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">E Akomoda Team</h5>
                        <small class="text-muted">
                            <?php if (!empty($messages)): ?>
                                Last active: <?php echo date('M d, Y h:i A', strtotime($messages[count($messages)-1]['created_at'])); ?>
                            <?php else: ?>
                                Available to help
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <?php if (!isset($messages) || empty($messages)): ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h5>No messages yet</h5>
                            <p>Send a message to start a conversation with our team.</p>
                        </div>
                    <?php else: ?>
                        <?php 
                        $currentDate = null;
                        foreach ($messages as $message): 
                            $messageDate = date('Y-m-d', strtotime($message['created_at']));
                            if ($currentDate !== $messageDate) {
                                $currentDate = $messageDate;
                                $displayDate = date('F j, Y', strtotime($message['created_at']));
                                echo '<div class="message-day-divider"><span>' . $displayDate . '</span></div>';
                            }
                        ?>
                            <div class="message <?php echo ($message['sender_type'] == 'user') ? 'outgoing' : 'incoming ' . $message['sender_type']; ?>">
                                <?php if ($message['sender_type'] != 'user'): ?>
                                    <div class="message-sender">
                                        <span class="badge bg-<?php echo $message['sender_color']; ?>"><?php echo $message['sender_name']; ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="message-content">
                                    <?php echo htmlspecialchars($message['message']); ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('h:i A', strtotime($message['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Typing indicator -->
                        <div class="typing-indicator" id="typingIndicator">
                            <div class="dots">
                                <span class="dot"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="chat-input">
                    <form id="messageForm" method="post" action="send_message.php">
                        <div class="input-group">
                            <input type="text" class="form-control" name="message" id="messageInput" placeholder="Type your message..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Fixed button to show sidebar on mobile -->
        <button class="sidebar-toggle-fixed" id="sidebarToggleFixed">
            <i class="fas fa-comments"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chatMessages');
            const messageForm = document.getElementById('messageForm');
            const messageInput = document.getElementById('messageInput');
            const typingIndicator = document.getElementById('typingIndicator');
            
            // Scroll to bottom of chat messages
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Mobile sidebar toggle
            const mobileToggle = document.querySelector('.mobile-toggle');
            const sidebarToggleFixed = document.getElementById('sidebarToggleFixed');
            const sidebarClose = document.querySelector('.sidebar-close');
            const chatSidebar = document.querySelector('.chat-sidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            
            function showSidebar() {
                chatSidebar.classList.add('show');
                backdrop.classList.add('show');
            }
            
            function hideSidebar() {
                chatSidebar.classList.remove('show');
                backdrop.classList.remove('show');
            }
            
            if (mobileToggle) {
                mobileToggle.addEventListener('click', showSidebar);
            }
            
            if (sidebarToggleFixed) {
                sidebarToggleFixed.addEventListener('click', showSidebar);
            }
            
            if (sidebarClose) {
                sidebarClose.addEventListener('click', hideSidebar);
            }
            
            if (backdrop) {
                backdrop.addEventListener('click', hideSidebar);
            }
            
            // Real-time chat functionality
            let lastMessageId = 0;
            
            // Get the ID of the last message if any messages exist
            const messageElements = document.querySelectorAll('.message');
            if (messageElements.length > 0) {
                // Add data-id attribute to each message in PHP
                // This is a fallback in case we can't get the last message ID
                lastMessageId = <?php echo !empty($messages) ? $messages[count($messages)-1]['id'] : 0; ?>;
            }
            
            // Function to format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
            
            // Function to add a new message to the chat
            function addMessage(message) {
                const messageClass = message.sender_type === 'user' ? 'outgoing' : 'incoming ' + message.sender_type;
                
                let senderHtml = '';
                if (message.sender_type !== 'user') {
                    let senderColor = 'dark';
                    if (message.sender_type === 'admin') senderColor = 'primary';
                    if (message.sender_type === 'system') senderColor = 'info';
                    
                    let senderName = message.sender_type === 'admin' ? 'Admin' : 
                                    message.sender_type === 'system' ? 'System' : message.sender_type;
                    
                    senderHtml = `<div class="message-sender">
                        <span class="badge bg-${senderColor}">${senderName}</span>
                    </div>`;
                }
                
                const messageHtml = `
                    <div class="message ${messageClass}" data-id="${message.id}">
                        ${senderHtml}
                        <div class="message-content">
                            ${message.message}
                        </div>
                        <div class="message-time">
                            ${formatDate(message.created_at)}
                        </div>
                    </div>
                `;
                
                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Update last message in sidebar
                const conversationLastMessage = document.querySelector('.conversation-last-message');
                if (conversationLastMessage) {
                    let previewText = message.message.substring(0, 30);
                    if (message.message.length > 30) previewText += '...';
                    conversationLastMessage.textContent = previewText;
                }
                
                // Update last active time
                const lastActiveElement = document.querySelector('.chat-header small');
                if (lastActiveElement) {
                    const date = new Date(message.created_at);
                    const formattedDate = date.toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric', 
                        year: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });
                    lastActiveElement.textContent = 'Last active: ' + formattedDate;
                }
                
                // Update lastMessageId
                lastMessageId = message.id;
            }
            
            // Function to check for new messages
            function checkNewMessages() {
                fetch(`get_new_messages.php?last_id=${lastMessageId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.messages.length > 0) {
                            data.messages.forEach(message => {
                                addMessage(message);
                            });
                        }
                    })
                    .catch(error => console.error('Error checking for new messages:', error));
            }
            
            // Check for new messages every 3 seconds
            setInterval(checkNewMessages, 3000);
            
            // Handle form submission with AJAX
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Don't submit if message is empty
                    if (!messageInput.value.trim()) return;
                    
                    const formData = new FormData(messageForm);
                    
                    // Show the message immediately (optimistic UI)
                    const userMessage = {
                        id: 'temp-' + Date.now(),
                        message: messageInput.value.trim(),
                        sender_type: 'user',
                        created_at: new Date().toISOString()
                    };
                    addMessage(userMessage);
                    
                    // Clear input
                    messageInput.value = '';
                    
                    // Show typing indicator
                    setTimeout(function() {
                        typingIndicator.style.display = 'block';
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        
                        // Hide typing indicator after a random time (1.5-3.5 seconds)
                        setTimeout(function() {
                            typingIndicator.style.display = 'none';
                        }, Math.random() * 2000 + 1500);
                    }, 1000);
                    
                    // Send the message to the server
                    fetch('send_message.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error sending message:', data.message);
                            // You could show an error message here
                        }
                        // The new message will be picked up by the polling function
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            }
            
            // Focus on message input when page loads
            if (messageInput) {
                messageInput.focus();
            }
        });
    </script>
</body>
</html> 