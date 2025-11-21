<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Messages</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: #f8c01a;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.2em;
        }

        .close-btn {
            background: none;
            border: none;
            color: #333;
            font-size: 1.5em;
            cursor: pointer;
            padding: 0;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: white;
            min-height: calc(100vh - 140px);
        }

        .chat-input-container {
            padding: 15px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .send-button {
            padding: 12px 24px;
            background: #f8c01a;
            border: none;
            border-radius: 4px;
            color: #333;
            font-weight: 500;
            cursor: pointer;
        }

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            max-width: 80%;
        }

        .message.sent {
            background: #f8c01a;
            margin-left: auto;
        }

        .message.received {
            background: #f1f1f1;
            margin-right: auto;
        }

        .message-time {
            font-size: 0.8em;
            color: #666;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat Messages</h2>
            <button class="close-btn">&times;</button>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <!-- Messages will be loaded here -->
        </div>

        <div class="chat-input-container">
            <input type="text" class="chat-input" id="message-input" placeholder="Type your message...">
            <button class="send-button" id="send-button">Send</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        function loadMessages() {
            $.ajax({
                url: 'get_messages.php',
                method: 'GET',
                success: function(data) {
                    $('#chat-messages').html(data);
                    scrollToBottom();
                }
            });
        }

        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function sendMessage() {
            const message = $('#message-input').val();
            if(message.trim() !== '') {
                $.ajax({
                    url: 'send_message.php',
                    method: 'POST',
                    data: { message: message },
                    success: function() {
                        $('#message-input').val('');
                        loadMessages();
                    }
                });
            }
        }

        // Load messages every 2 seconds
        setInterval(loadMessages, 2000);

        // Send message on button click
        $('#send-button').click(sendMessage);

        // Send message on Enter key
        $('#message-input').keypress(function(e) {
            if(e.which == 13) {
                sendMessage();
            }
        });

        // Initial load
        loadMessages();
    });
    </script>
</body>
</html> 