<?php
// Since this file is included in contact.php, we don't need to start session or output buffering here
// We only need to check login for AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Please login to access chat',
            'redirect' => 'login.php'
        ]);
        exit;
    }
}

require_once 'db_con.php';

// Start the output buffer for the rest of the content
ob_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

?>

<style>
.typing-indicator {
    display: none;
    margin-bottom: 15px;
}

.typing-indicator span {
    height: 8px;
    width: 8px;
    float: left;
    margin: 0 1px;
    background-color: #9E9EA1;
    display: block;
    border-radius: 50%;
    opacity: 0.4;
}

.typing-indicator span:nth-of-type(1) {
    animation: 1s blink infinite 0.3333s;
}

.typing-indicator span:nth-of-type(2) {
    animation: 1s blink infinite 0.6666s;
}

.typing-indicator span:nth-of-type(3) {
    animation: 1s blink infinite 0.9999s;
}

@keyframes blink {
    50% {
        opacity: 1;
    }
}

.message-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    display: none;
}

.chat-message {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 10px;
    max-width: 80%;
}

.admin-message {
    background-color: #e3f2fd;
}

.user-message {
    margin-left: auto;
    background-color: #007bff;
    color: white;
}

.system-message {
    margin-right: auto;
    background-color: #f8f9fa;
}

.message-time {
    font-size: 0.8em;
    color: #6c757d;
}

.admin-icon {
    background-color: #28a745;
}

.system-icon {
    background-color: #007bff;
}

#chatMessages {
    background-color: #f8f9fa;
}

.suggested-messages {
    margin: 8px 0;
}

.suggested-messages button {
    font-size: 12px;
    padding: 4px 12px;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.suggested-messages button:hover {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.chat-window {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    display: none;
    flex-direction: column;
    z-index: 1050;
}

.chat-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
}

.chat-footer {
    padding: 15px;
    border-top: 1px solid #eee;
}

.message {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

.message-content {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    margin-bottom: 5px;
}

.message.received .message-content {
    background: #f0f2f5;
    align-self: flex-start;
}

.message.sent .message-content {
    background: #0084ff;
    color: white;
    align-self: flex-end;
}

.message-time {
    font-size: 0.75rem;
    color: #888;
}

.message.received .message-time {
    align-self: flex-start;
}

.message.sent .message-time {
    align-self: flex-end;
}

@media (max-width: 576px) {
    .chat-window {
        width: 100%;
        height: 100%;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }
}

/* Add these to your existing styles */
.message {
    max-width: 80%;
    margin-bottom: 1rem;
}

.user-message {
    margin-left: auto;
}

.admin-message, .system-message {
    margin-right: auto;
}

.message-content {
    border-radius: 15px;
    padding: 10px 15px;
    position: relative;
}

.admin-icon, .system-icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.admin-icon {
    background-color: #28a745;
}

.system-icon {
    background-color: #007bff;
}

.spinner-border {
    width: 2rem;
    height: 2rem;
}

#chatMessages {
    padding: 1rem;
    overflow-y: auto;
    height: calc(100% - 120px);
}

.message p {
    margin: 0;
    word-break: break-word;
}

.message small {
    font-size: 0.75rem;
    opacity: 0.8;
}

/* Mobile-specific styles */
@media (max-width: 768px) {
    .chat-window {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100vh;
        margin: 0;
        border-radius: 0;
        z-index: 2000;
    }

    .chat-header {
        padding: 15px;
        background-color: #fff;
        border-bottom: 1px solid #eee;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 2001;
    }

    .chat-body {
        padding-top: 60px; /* Height of header */
        padding-bottom: 70px; /* Height of footer */
        height: 100%;
    }

    .chat-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        padding: 10px;
        border-top: 1px solid #eee;
        z-index: 2001;
    }

    #chatButton {
        bottom: 20px !important;
        right: 20px !important;
    }

    .message {
        max-width: 90%;
    }

    .message-content {
        padding: 8px 12px;
    }

    /* Improve touch targets */
    .btn-close {
        padding: 12px;
    }

    .input-group .form-control,
    .input-group .btn {
        padding: 12px;
        font-size: 16px; /* Prevent zoom on iOS */
    }
}

/* Animation for chat window */
@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

@keyframes slideDown {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(100%);
    }
}

.chat-window.opening {
    animation: slideUp 0.3s ease-out forwards;
}

.chat-window.closing {
    animation: slideDown 0.3s ease-out forwards;
}
</style>

<!-- Update the chat button for better mobile visibility -->
<div id="chatButton" class="position-fixed" style="bottom: 30px; right: 30px; z-index: 1999;">
    <button class="btn btn-primary rounded-circle p-3 shadow-lg" onclick="toggleChatWindow()">
        <i class="fas fa-comments fa-lg"></i>
        <span id="messageBadge" class="message-badge"></span>
    </button>
</div>

<!-- Chat Window -->
<div id="chatWindow" class="chat-window">
    <div class="chat-header">
        <h5>Messages</h5>
        <button type="button" class="btn-close" onclick="toggleChatWindow()"></button>
    </div>
    <div class="chat-body" id="chatMessages">
        <!-- Messages will be loaded here -->
    </div>
    <div class="chat-footer">
        <?php if ($isLoggedIn): ?>
            <!-- Regular chat input for logged in users -->
            <form id="messageForm" onsubmit="sendMessage(event)">
                <div class="input-group">
                    <input type="text" class="form-control" id="messageInput" placeholder="Type your message...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Login prompt for non-logged in users -->
            <div class="text-center p-2">
                <p class="mb-2">Please <a href="login.php" class="fw-bold">login</a> to continue the conversation</p>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-outline-primary ms-2">Register</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chat JavaScript -->
<script>
// Chat window state
let isChatOpen = false;
let isMinimized = false;
let lastMessageTime = new Date(0); // Start from earliest time
let userId = null;
let messageInterval = null;
let isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

// FAQ data for non-logged in users
const frequentlyAskedQuestions = [
    {
        question: "What types of events do you host?",
        answer: "We host a variety of events including weddings, birthdays, corporate events, and more. Each event is customized to meet your specific needs."
    },
    {
        question: "How do I book a venue?",
        answer: "To book a venue, you need to create an account, browse our available venues, select your preferred date and time, and complete the booking process. Our team will confirm your booking within 24 hours."
    },
    {
        question: "What is your cancellation policy?",
        answer: "Our standard cancellation policy allows for full refunds if cancelled 30 days before the event, 50% refund if cancelled 15-29 days before, and no refund for cancellations less than 15 days before the event."
    },
    {
        question: "Do you provide catering services?",
        answer: "Yes, we offer comprehensive catering services with various menu options to suit different preferences and dietary requirements."
    },
    {
        question: "How far in advance should I book?",
        answer: "We recommend booking at least 3-6 months in advance for large events like weddings, and 1-2 months for smaller events to ensure availability."
    }
];

// Initialize chat
function initChat() {
    if (isLoggedIn) {
        // For logged in users - regular chat functionality
        // Generate a temporary user ID if not logged in
        if (!userId) {
            // Try to get stored userId from localStorage
            userId = localStorage.getItem('chat_user_id');
            if (!userId) {
                userId = 'guest_' + Math.random().toString(36).substr(2, 9);
                localStorage.setItem('chat_user_id', userId);
            }
        }
        
        // Load previous messages
        loadPreviousMessages();
        
        // Check for new messages periodically when chat is open
        if (messageInterval === null) {
            messageInterval = setInterval(fetchNewMessages, 3000);
        }
        
        // Start checking for unread messages
        updateMessageBadge();
        setInterval(updateMessageBadge, 30000); // Check every 30 seconds
    } else {
        // For non-logged in users - show FAQs
        displayFAQs();
    }

    // Add mobile-specific handlers
    if (window.innerWidth <= 768) {
        const messageInput = document.getElementById('messageInput');
        
        if (messageInput) {
            // Prevent body scroll when input is focused
            messageInput.addEventListener('focus', () => {
                document.body.style.overflow = 'hidden';
                setTimeout(scrollToBottom, 300); // Scroll after keyboard appears
            });
            
            messageInput.addEventListener('blur', () => {
                document.body.style.overflow = '';
            });
        }
    }
}

// Display FAQs for non-logged in users
function displayFAQs() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '';
    
    // Add welcome message
    const welcomeElement = document.createElement('div');
    welcomeElement.className = 'd-flex mb-4';
    welcomeElement.innerHTML = `
        <div class="flex-shrink-0">
            <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-hotel"></i>
            </div>
        </div>
        <div class="ms-3 p-3 bg-white rounded shadow-sm" style="max-width: 90%;">
            <p class="mb-0">Welcome to E Akomoda! Here are some frequently asked questions that might help you:</p>
        </div>
    `;
    chatMessages.appendChild(welcomeElement);
    
    // Add each FAQ as a message with a button to show the answer
    frequentlyAskedQuestions.forEach((faq, index) => {
        const faqElement = document.createElement('div');
        faqElement.className = 'mb-3';
        faqElement.innerHTML = `
            <button class="btn btn-outline-primary w-100 text-start mb-2" onclick="toggleFAQAnswer(${index})">
                <i class="fas fa-question-circle me-2"></i> ${faq.question}
            </button>
            <div id="faqAnswer${index}" class="faq-answer ms-4 p-3 bg-light rounded" style="display: none;">
                ${faq.answer}
            </div>
        `;
        chatMessages.appendChild(faqElement);
    });
    
    // Add login prompt at the bottom
    const loginPrompt = document.createElement('div');
    loginPrompt.className = 'text-center mt-4 p-3 bg-light rounded';
    loginPrompt.innerHTML = `
        <p>For personalized assistance, please log in to chat with our team.</p>
        <a href="login.php" class="btn btn-primary">Login</a>
        <a href="register.php" class="btn btn-outline-primary ms-2">Register</a>
    `;
    chatMessages.appendChild(loginPrompt);
}

// Toggle FAQ answer visibility
function toggleFAQAnswer(index) {
    const answerElement = document.getElementById(`faqAnswer${index}`);
    if (answerElement.style.display === 'none') {
        answerElement.style.display = 'block';
    } else {
        answerElement.style.display = 'none';
    }
    scrollToBottom();
}

// Toggle chat window visibility
function toggleChatWindow() {
    const chatWindow = document.getElementById('chatWindow');
    const chatButton = document.getElementById('chatButton');
    
    if (!isChatOpen) {
        // Opening chat
        chatWindow.style.display = 'flex';
        chatWindow.classList.add('opening');
        chatWindow.classList.remove('closing');
        chatButton.style.display = 'none';
        isChatOpen = true;
        
        // Initialize chat if not already done
        initChat();
        
        // Add mobile-specific event listeners
        if (window.innerWidth <= 768) {
            addMobileSwipeHandler();
        }
    } else {
        // Closing chat
        chatWindow.classList.add('closing');
        chatWindow.classList.remove('opening');
        setTimeout(() => {
            chatWindow.style.display = 'none';
            chatButton.style.display = 'block';
        }, 300);
        isChatOpen = false;
    }
}

// Minimize chat window
function minimizeChatWindow() {
    const chatWindow = document.getElementById('chatWindow');
    
    if (!isMinimized) {
        chatWindow.style.height = '50px';
        document.getElementById('chatMessages').style.display = 'none';
        document.getElementById('messageForm').parentElement.style.display = 'none';
        isMinimized = true;
    } else {
        chatWindow.style.height = '500px';
        document.getElementById('chatMessages').style.display = 'block';
        document.getElementById('messageForm').parentElement.style.display = 'block';
        isMinimized = false;
        scrollToBottom();
    }
}

// Show typing indicator
function showTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'flex';
        scrollToBottom();
    }
}

// Hide typing indicator
function hideTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'none';
    }
}

// Modify the sendMessage function
function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (message === '') return;
    
    // Add message to UI immediately
    addMessageToUI(message, 'user');
    
    // Clear input and blur to hide mobile keyboard
    messageInput.value = '';
    messageInput.blur();
    
    // Show typing indicator
    showTypingIndicator();
    
    const formData = new FormData();
    formData.append('message', message);
    formData.append('action', 'send');
    
    fetch('chat_handler.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin' // Important for session handling
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.reply) {
                setTimeout(() => {
                    hideTypingIndicator();
                    addMessageToUI(data.reply, 'system');
                }, 1000);
            }
        } else {
            hideTypingIndicator();
            console.error('Error sending message:', data.message);
            alert('Failed to send message. Please try again.');
        }
    })
    .catch(error => {
        hideTypingIndicator();
        console.error('Error:', error);
        alert('Failed to send message. Please try again.');
    });
    
    scrollToBottom();
}

// Modify your addMessageToUI function to handle the typing indicator
function addMessageToUI(message, sender, timestamp = null, repliedMessage = null) {
    hideTypingIndicator();
    
    const chatMessages = document.getElementById('chatMessages');
    const messageTime = timestamp ? new Date(timestamp) : new Date();
    const timeStr = messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    const messageElement = document.createElement('div');
    
    let repliedHtml = '';
    if (repliedMessage) {
        repliedHtml = `
            <div class="replied-message small text-muted mb-2">
                <i class="fas fa-reply"></i> ${repliedMessage.substring(0, 50)}...
            </div>
        `;
    }
    
    if (sender === 'user') {
        // User messages (right-aligned)
        messageElement.className = 'd-flex mb-3 justify-content-end';
        messageElement.innerHTML = `
            <div class="p-3 bg-primary text-white rounded shadow-sm" style="max-width: 80%;">
                ${repliedHtml}
                <p class="mb-0">${message}</p>
                <small class="text-white-50">${timeStr}</small>
            </div>
        `;
    } else if (sender === 'admin') {
        // Admin messages (left-aligned with admin icon)
        messageElement.className = 'd-flex mb-3';
        messageElement.innerHTML = `
            <div class="flex-shrink-0">
                <div class="bg-success text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="ms-3 p-3 bg-white rounded shadow-sm" style="max-width: 80%;">
                ${repliedHtml}
                <p class="mb-0">${message}</p>
                <small class="text-muted">${timeStr}</small>
            </div>
        `;
    } else {
        // System messages (left-aligned with hotel icon)
        messageElement.className = 'd-flex mb-3';
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

// Load previous messages
function loadPreviousMessages() {
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('action', 'load_history');
    
    fetch('chat_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages) {
            // Clear existing messages
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            
            // Add each message to the UI
            data.messages.forEach(msg => {
                addMessageToUI(msg.content, msg.sender_type, new Date(msg.timestamp));
            });
            
            scrollToBottom();
        }
    })
    .catch(error => {
        console.error('Error loading message history:', error);
    });
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
                addMessageToUI(msg.content, msg.sender_type, new Date(msg.timestamp));
            });
            updateMessageBadge();
            
            // Play notification sound for admin messages
            const hasAdminMessage = data.messages.some(msg => msg.sender_type === 'admin');
            if (hasAdminMessage && !isChatOpen) {
                playNotificationSound();
            }
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
    
    // Ensure the page doesn't scroll on mobile when the chat is open
    if (window.innerWidth <= 768 && isChatOpen) {
        document.body.style.overflow = 'hidden';
    }
}

// Check if user is logged in and set userId
document.addEventListener('DOMContentLoaded', function() {
    // You can set the userId here if the user is logged in
    // For example: userId = '<?php echo isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null; ?>';
});

// Function to update the message badge
function updateMessageBadge() {
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('action', 'check_unread');
    
    fetch('chat_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.getElementById('messageBadge');
        if (data.unread_count > 0) {
            badge.style.display = 'block';
            badge.textContent = data.unread_count;
        } else {
            badge.style.display = 'none';
        }
    })
    .catch(error => console.error('Error checking unread messages:', error));
}

// Add this to your existing JavaScript
function openSpecificMessage(messageId) {
    // Load the specific message and its conversation
    const formData = new FormData();
    formData.append('action', 'get_message');
    formData.append('message_id', messageId);
    
    fetch('chat_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.message) {
            // Clear existing messages
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            
            // Add the system welcome message
            addMessageToUI('Welcome to E Akomoda! How can we help you today?', 'system');
            
            // Add the specific message
            addMessageToUI(data.message.content, data.message.sender_type, new Date(data.message.created_at));
            
            scrollToBottom();
        }
    });
}

// Add notification sound function
function playNotificationSound() {
    const audio = new Audio('notification.mp3'); // Make sure to add this file to your project
    audio.play().catch(e => console.log('Audio play failed:', e));
}

function sendSuggestedMessage(message) {
    document.getElementById('messageInput').value = message;
    document.getElementById('messageForm').dispatchEvent(new Event('submit'));
}

function loadMessages() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';
    
    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_messages'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            chatMessages.innerHTML = '';
            
            if (data.messages.length === 0) {
                chatMessages.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-comments text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted">No messages yet. Start a conversation!</p>
                    </div>
                `;
                return;
            }

            data.messages.forEach(msg => {
                const messageElement = document.createElement('div');
                const time = new Date(msg.created_at).toLocaleTimeString([], { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });

                if (msg.sender_type === 'user') {
                    // User messages (right-aligned)
                    messageElement.className = 'd-flex justify-content-end mb-3';
                    messageElement.innerHTML = `
                        <div class="message user-message">
                            <div class="message-content bg-primary text-white p-3 rounded-3">
                                <p class="mb-1">${msg.message}</p>
                                <small class="text-white-50">${time}</small>
                            </div>
                        </div>
                    `;
                } else if (msg.sender_type === 'admin') {
                    // Admin messages (left-aligned)
                    messageElement.className = 'd-flex mb-3';
                    messageElement.innerHTML = `
                        <div class="message admin-message">
                            <div class="d-flex align-items-start">
                                <div class="admin-icon rounded-circle p-2 me-2">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div class="message-content bg-light p-3 rounded-3">
                                    <p class="mb-1">${msg.message}</p>
                                    <small class="text-muted">${time}</small>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // System messages (left-aligned)
                    messageElement.className = 'd-flex mb-3';
                    messageElement.innerHTML = `
                        <div class="message system-message">
                            <div class="d-flex align-items-start">
                                <div class="system-icon rounded-circle p-2 me-2">
                                    <i class="fas fa-robot text-white"></i>
                                </div>
                                <div class="message-content bg-light p-3 rounded-3">
                                    <p class="mb-1">${msg.message}</p>
                                    <small class="text-muted">${time}</small>
                                </div>
                            </div>
                        </div>
                    `;
                }

                chatMessages.appendChild(messageElement);
            });

            scrollToBottom();
            updateMessageBadge(); // Update the message badge count
        } else {
            chatMessages.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 2rem;"></i>
                    <p class="text-danger">Failed to load messages. Please try again.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        chatMessages.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 2rem;"></i>
                <p class="text-danger">An error occurred. Please try again later.</p>
            </div>
        `;
    });
}

function addMobileSwipeHandler() {
    const chatWindow = document.getElementById('chatWindow');
    let touchStartY = 0;
    let touchEndY = 0;
    
    chatWindow.addEventListener('touchstart', (e) => {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    chatWindow.addEventListener('touchmove', (e) => {
        touchEndY = e.touches[0].clientY;
    }, { passive: true });
    
    chatWindow.addEventListener('touchend', () => {
        const swipeDistance = touchEndY - touchStartY;
        
        // If swipe down is more than 100px, close the chat
        if (swipeDistance > 100) {
            toggleChatWindow();
        }
    });
}

// Handle orientation changes
window.addEventListener('orientationchange', () => {
    if (isChatOpen) {
        setTimeout(scrollToBottom, 100);
    }
});

// Handle resize events
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        if (isChatOpen) {
            scrollToBottom();
        }
    }, 250);
});
</script>

<!-- Create a new file for handling chat messages -->
<script>
// This is just a reminder to create the chat_handler.php file
// The actual file needs to be created separately
</script>

<!-- Modify your existing message icon in the navigation -->