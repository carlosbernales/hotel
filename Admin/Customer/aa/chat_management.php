<?php
require_once 'includes/config.php';
require_once 'admin/includes/auth_check.php';
require_once 'admin/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: 60vh;
            overflow-y: auto;
        }
        .message {
            margin-bottom: 15px;
        }
        .message.admin {
            text-align: right;
        }
        .message-content {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 15px;
            max-width: 70%;
        }
        .user .message-content {
            background-color: #f0f0f0;
        }
        .admin .message-content {
            background-color: #007bff;
            color: white;
        }
        .timestamp {
            font-size: 0.75rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Conversation List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Active Conversations</h5>
                    </div>
                    <div class="list-group list-group-flush" id="conversationList">
                        <!-- Conversations will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Chat Window -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" id="chatTitle">Select a conversation</h5>
                    </div>
                    <div class="card-body chat-container" id="chatMessages">
                        <!-- Messages will be loaded here -->
                    </div>
                    <div class="card-footer">
                        <form id="replyForm" class="d-none">
                            <div class="input-group">
                                <input type="text" class="form-control" id="replyMessage" placeholder="Type your reply...">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentUserId = null;
        let lastCheck = new Date();

        // Load conversations every 5 seconds
        function loadConversations() {
            $.post('admin_chat_handler.php', {
                action: 'get_conversations'
            }).done(function(response) {
                if (response.success) {
                    let html = '';
                    response.conversations.forEach(function(conv) {
                        html += `
                            <a href="#" class="list-group-item list-group-item-action conversation-item" 
                               data-user-id="${conv.user_id}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>User: ${conv.user_id}</strong>
                                    ${conv.unread_count > 0 ? 
                                        `<span class="badge bg-primary rounded-pill">${conv.unread_count}</span>` : ''}
                                </div>
                                <small class="text-muted">${conv.latest_message}</small>
                            </a>
                        `;
                    });
                    $('#conversationList').html(html);
                }
            });
        }

        // Load messages for a specific user
        function loadMessages(userId) {
            currentUserId = userId;
            $('#chatTitle').text('Chat with User: ' + userId);
            $('#replyForm').removeClass('d-none');
            
            $.post('chat_handler.php', {
                action: 'fetch',
                user_id: userId,
                last_time: '2000-01-01'
            }).done(function(response) {
                if (response.success) {
                    let html = '';
                    response.messages.forEach(function(msg) {
                        html += `
                            <div class="message ${msg.sender_type}">
                                <div class="message-content">
                                    ${msg.content}
                                </div>
                                <div class="timestamp">
                                    ${new Date(msg.timestamp).toLocaleString()}
                                </div>
                            </div>
                        `;
                    });
                    $('#chatMessages').html(html).scrollTop($('#chatMessages')[0].scrollHeight);
                }
            });
        }

        // Send admin reply
        $('#replyForm').on('submit', function(e) {
            e.preventDefault();
            if (!currentUserId) return;

            const message = $('#replyMessage').val().trim();
            if (!message) return;

            $.post('admin_chat_handler.php', {
                action: 'send_reply',
                user_id: currentUserId,
                message: message
            }).done(function(response) {
                if (response.success) {
                    $('#replyMessage').val('');
                    loadMessages(currentUserId);
                    loadConversations();
                }
            });
        });

        // Click handler for conversation items
        $(document).on('click', '.conversation-item', function(e) {
            e.preventDefault();
            const userId = $(this).data('user-id');
            loadMessages(userId);
        });

        // Initial load
        loadConversations();
        
        // Refresh conversations every 5 seconds
        setInterval(loadConversations, 5000);
    </script>
</body>
</html> 