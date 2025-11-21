$(document).ready(function() {
    // Modal handling
    $('.open-chat').click(function() {
        $('#chatModal').fadeIn();
        loadMessages();
    });

    $('.close-btn').click(function() {
        $('#chatModal').fadeOut();
    });

    // Close modal when clicking outside
    $(window).click(function(e) {
        if ($(e.target).is('#chatModal')) {
            $('#chatModal').fadeOut();
        }
    });

    // Function to load messages
    function loadMessages() {
        $.ajax({
            url: 'get_messages.php',
            method: 'GET',
            success: function(data) {
                $('#chat-messages').html(data);
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
            }
        });
    }

    // Load messages every 2 seconds when modal is visible
    setInterval(function() {
        if($('#chatModal').is(':visible')) {
            loadMessages();
        }
    }, 2000);

    // Handle send button click
    $('#send-button').click(function() {
        sendMessage();
    });

    // Handle enter key press
    $('#message-input').keypress(function(e) {
        if(e.which == 13) {
            sendMessage();
        }
    });

    function sendMessage() {
        var message = $('#message-input').val();
        if(message.trim() != '') {
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    message: message
                },
                success: function() {
                    $('#message-input').val('');
                    loadMessages();
                }
            });
        }
    }
}); 