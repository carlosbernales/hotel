<?php
session_start();
include 'includes/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
        }

        .messages-container {
            max-width: 1200px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: calc(100vh - 4rem);
        }

        .chat-sidebar {
            background: #fff;
            border-right: 1px solid #e9ecef;
            height: 100%;
            overflow-y: auto;
        }

        .chat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .chat-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .chat-item:hover {
            background-color: #f8f9fa;
        }

        .chat-item.active {
            background-color: #e9ecef;
        }

        .chat-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .chat-item-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .chat-item-time {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .chat-item-preview {
            color: #6c757d;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-main {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            background: #fff;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message {
            max-width: 75%;
            margin-bottom: 1rem;
            clear: both;
        }

        .message-content {
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            display: inline-block;
        }

        .message.received .message-content {
            background: #fff;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 0.25rem;
        }

        .message.sent {
            float: right;
        }

        .message.sent .message-content {
            background: #007bff;
            color: white;
            border-bottom-right-radius: 0.25rem;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .chat-input {
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #e9ecef;
        }

        .chat-input form {
            display: flex;
            gap: 1rem;
        }

        .chat-input textarea {
            resize: none;
            height: 45px;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #adb5bd;
        }

        @media (max-width: 768px) {
            .messages-container {
                margin: 0;
                height: 100vh;
                border-radius: 0;
            }

            .chat-sidebar {
                display: none;
            }

            .chat-sidebar.active {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1000;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid messages-container">
        <div class="row h-100">
            <!-- Chat Sidebar -->
            <div class="col-md-4 col-lg-3 p-0 chat-sidebar">
                <div class="p-3 border-bottom">
                    <h4 class="mb-0">Messages</h4>
                </div>
                <ul class="chat-list">
                    <?php
                    // Fetch conversations from database
                    $user_id = $_SESSION['user_id'] ?? 0;
                    $query = "SELECT DISTINCT c.*, u.name, u.profile_image,
                            (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                            (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time
                            FROM conversations c
                            JOIN users u ON (c.user1_id = u.id OR c.user2_id = u.id)
                            WHERE c.user1_id = ? OR c.user2_id = ?
                            ORDER BY last_message_time DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ii", $user_id, $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($chat = $result->fetch_assoc()) {
                            $other_user_id = ($chat['user1_id'] == $user_id) ? $chat['user2_id'] : $chat['user1_id'];
                            ?>
                            <li class="chat-item" data-conversation="<?php echo $chat['id']; ?>">
                                <div class="chat-item-header">
                                    <span class="chat-item-name"><?php echo htmlspecialchars($chat['name']); ?></span>
                                    <span class="chat-item-time">
                                        <?php echo date('M d', strtotime($chat['last_message_time'])); ?>
                                    </span>
                                </div>
                                <div class="chat-item-preview">
                                    <?php echo htmlspecialchars(substr($chat['last_message'], 0, 50)) . '...'; ?>
                                </div>
                            </li>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h4>No Messages Yet</h4>
                            <p>Start a conversation with someone!</p>
                        </div>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <!-- Chat Main -->
            <div class="col-md-8 col-lg-9 p-0 chat-main">
                <div id="initial-state" class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h4>Select a Conversation</h4>
                    <p>Choose a conversation from the list to start chatting</p>
                </div>

                <div id="chat-view" class="d-none h-100">
                    <div class="chat-header">
                        <h5 class="mb-0" id="chat-title"></h5>
                    </div>
                    
                    <div class="chat-messages" id="messages-container"></div>

                    <div class="chat-input">
                        <form id="message-form">
                            <input type="hidden" id="conversation-id" value="">
                            <div class="input-group">
                                <textarea class="form-control" placeholder="Type your message..." required></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentConversation = null;

            // Load conversation
            $('.chat-item').click(function() {
                const conversationId = $(this).data('conversation');
                currentConversation = conversationId;
                
                // Update UI
                $('.chat-item').removeClass('active');
                $(this).addClass('active');
                $('#initial-state').addClass('d-none');
                $('#chat-view').removeClass('d-none');
                
                // Load messages
                loadMessages(conversationId);
                
                // Update form
                $('#conversation-id').val(conversationId);
            });

            // Load messages
            function loadMessages(conversationId) {
                $.get('ajax/get_messages.php', {
                    conversation_id: conversationId
                }, function(response) {
                    if (response.success) {
                        const messagesContainer = $('#messages-container');
                        messagesContainer.empty();
                        
                        response.messages.forEach(message => {
                            const messageHtml = createMessageElement(message);
                            messagesContainer.append(messageHtml);
                        });
                        
                        // Scroll to bottom
                        messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
                    }
                });
            }

            // Create message element
            function createMessageElement(message) {
                const messageClass = message.is_sent ? 'sent' : 'received';
                return `
                    <div class="message ${messageClass}">
                        <div class="message-content">
                            ${message.content}
                        </div>
                        <div class="message-time">
                            ${message.time}
                        </div>
                    </div>
                `;
            }

            // Send message
            $('#message-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const textarea = form.find('textarea');
                const message = textarea.val().trim();
                
                if (!message) return;
                
                $.post('ajax/send_message.php', {
                    conversation_id: currentConversation,
                    message: message
                }, function(response) {
                    if (response.success) {
                        textarea.val('');
                        loadMessages(currentConversation);
                    }
                });
            });

            // Auto-refresh messages
            setInterval(function() {
                if (currentConversation) {
                    loadMessages(currentConversation);
                }
            }, 5000);
        });
    </script>
</body>
</html>
