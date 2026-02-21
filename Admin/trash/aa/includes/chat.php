<!-- Chat Messenger Button -->
<div id="chatButton" class="position-fixed" style="bottom: 30px; right: 30px; z-index: 1000;">
    <button class="btn btn-primary rounded-circle p-3 shadow" onclick="toggleChatWindow()">
        <i class="fas fa-comments"></i>
    </button>
</div>

<!-- Chat Window -->
<div id="chatWindow" class="position-fixed shadow" style="bottom: 100px; right: 30px; width: 350px; height: 450px; background: white; border-radius: 10px; display: none; z-index: 1000; overflow: hidden;">
    <!-- Chat Header -->
    <div class="d-flex justify-content-between align-items-center p-3 bg-primary text-white">
        <div>
            <h5 class="mb-0"><i class="fas fa-comments me-2"></i>E Akomoda Chat</h5>
        </div>
        <div>
            <button class="btn btn-sm text-white" onclick="minimizeChatWindow()">
                <i class="fas fa-minus"></i>
            </button>
            <button class="btn btn-sm text-white" onclick="toggleChatWindow()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- Chat Messages Container -->
    <div id="chatMessages" class="p-3" style="height: 320px; overflow-y: auto; background-color: #f5f5f5;">
        <!-- System welcome message -->
        <div class="d-flex mb-3">
            <div class="flex-shrink-0">
                <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-hotel"></i>
                </div>
            </div>
            <div class="ms-3 p-3 bg-white rounded shadow-sm" style="max-width: 80%;">
                <p class="mb-0">Welcome to E Akomoda! How can we help you today?</p>
                <small class="text-muted">Just now</small>
            </div>
        </div>
        <!-- Messages will be loaded here -->
    </div>
    
    <!-- Chat Input -->
    <div class="p-3 border-top">
        <form id="chatForm" onsubmit="sendMessage(event)">
            <div class="input-group">
                <input type="text" id="messageInput" class="form-control" placeholder="Type a message..." required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div> 