// Chat window state
let isChatOpen = false;
let isMinimized = false;
let lastMessageTime = new Date();
let userId = null; // Will be set when user starts chatting
let messageInterval = null;

// Initialize chat
function initChat() {
    // Generate a temporary user ID if not logged in
    if (!userId) {
        userId = 'guest_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Check for new messages periodically when chat is open
    if (messageInterval === null) {
        messageInterval = setInterval(fetchNewMessages, 3000);
    }
}

// Toggle chat window visibility
function toggleChatWindow() {
    const chatWindow = document.getElementById('chatWindow');
    const chatButton = document.getElementById('chatButton');
    
    if (isChatOpen) {
        chatWindow.style.display = 'none';
        chatButton.style.display = 'block';
        isChatOpen = false;
        isMinimized = false;
        
        // Clear message checking interval when chat is closed
        if (messageInterval !== null) {
            clearInterval(messageInterval);
            messageInterval = null;
        }
    } else {
        chatWindow.style.display = 'block';
        isChatOpen = true;
        initChat();
        scrollToBottom();
    }
}

// Minimize chat window
function minimizeChatWindow() {
    const chatWindow = document.getElementById('chatWindow');
    
    if (!isMinimized) {
        chatWindow.style.height = '50px';
        document.getElementById('chatMessages').style.display = 'none';
        document.getElementById('chatForm').parentElement.style.display = 'none';
        isMinimized = true;
    } else {
        chatWindow.style.height = '450px';
        document.getElementById('chatMessages').style.display = 'block';
        document.getElementById('chatForm').parentElement.style.display = 'block';
        isMinimized = false;
        scrollToBottom();
    }
}

// Send a message
function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (message === '') return;
    
    // Add message to UI immediately
    addMessageToUI(message, 'user');
    
    // Clear input
    messageInput.value = '';
    
    // Send to server
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('message', message);
    formData.append('action', 'send');
    
    fetch('chat_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // If server responds with a reply message
            if (data.reply) {
                setTimeout(() => {
                    addMessageToUI(data.reply, 'system');
                }, 1000);
            }
        } else {
            console.error('Error sending message:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
    
    scrollToBottom();
}

// Add a message to the UI
function addMessageToUI(message, sender) {
    const chatMessages = document.getElementById('chatMessages');
    const messageTime = new Date();
    
    // Format time
    const timeStr = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    // Create message element
    const messageElement = document.createElement('div');
    messageElement.className = 'd-flex mb-3' + (sender === 'user' ? ' justify-content-end' : '');
    
    if (sender === 'user') {
        messageElement.innerHTML = `
            <div class="p-3 bg-primary text-white rounded shadow-sm" style="max-width: 80%;">
                <p class="mb-0">${message}</p>
                <small class="text-white-50">${timeStr}</small>
            </div>
        `;
    } else {
        messageElement.innerHTML = `
            <div class="flex-shrink-0">
                <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-hotel"></i>
                </div>
            </div>
            <div class="ms-3 p-3 bg-white rounded shadow-sm" style="max-width: 80%;">
                <p class="mb-0">${message}</p>
                <small class="text-muted">${timeStr}</small>
            </div>
        `;
    }
    
    chatMessages.appendChild(messageElement);
    lastMessageTime = messageTime;
    scrollToBottom();
}

// Fetch new messages from server
function fetchNewMessages() {
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('last_time', lastMessageTime.toISOString());
    formData.append('action', 'fetch');
    
    fetch('chat_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
                addMessageToUI(msg.content, msg.sender_type);
            });
        }
    })
    .catch(error => {
        console.error('Error fetching messages:', error);
    });
}

// Scroll to bottom of chat
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Check if user is logged in and set userId
document.addEventListener('DOMContentLoaded', function() {
    // Set userId from PHP session if available
    const sessionUserId = document.getElementById('chatWindow').getAttribute('data-user-id');
    if (sessionUserId) {
        userId = sessionUserId;
    }
}); 