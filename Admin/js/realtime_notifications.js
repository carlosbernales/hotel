$(document).ready(function() {
    // Check for new notifications every 5 seconds
    setInterval(checkForNewNotifications, 5000);
    
    // Initial check
    checkForNewNotifications();
    
    // Function to check for new notifications
    function checkForNewNotifications() {
        $.ajax({
            url: 'check_new_notifications.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.hasNew) {
                    // Update the notification badge
                    updateNotificationBadge(response.count);
                    
                    // Show the notification popup
                    showNotificationPopup(response.title, response.message, response.type);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking for notifications:', error);
            }
        });
    }
    
    // Function to update the notification badge
    function updateNotificationBadge(count) {
        let badge = $('#notificationBadge');
        
        if (count > 0) {
            badge.text(count).show();
            
            // Add pulse animation
            badge.addClass('pulse');
            setTimeout(() => {
                badge.removeClass('pulse');
            }, 3000);
        } else {
            badge.hide();
        }
    }
    
    // Function to show notification popup
    function showNotificationPopup(title, message, type) {
        // Create or update the notification popup
        let popup = $('#notificationPopup');
        
        if (popup.length === 0) {
            // Create the popup if it doesn't exist
            popup = $(`
                <div class="notification-popup" id="notificationPopup" style="position: fixed; top: 70px; right: 20px; width: 300px; z-index: 1060; display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>${title}</strong>
                            <button type="button" class="close" onclick="$(this).closest('.notification-popup').fadeOut()">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="notification.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append(popup);
        } else {
            // Update existing popup
            popup.find('.card-header strong').text(title);
            popup.find('.card-body p').text(message);
        }
        
        // Show the popup with animation
        popup.fadeIn();
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            popup.fadeOut();
        }, 10000);
    }
    
    // Mark all notifications as read when clicking the bell icon
    $('#notificationIcon').on('click', function(e) {
        // Hide the badge immediately for better UX
        $('#notificationBadge').hide();
        
        // Mark all notifications as read via AJAX
        $.ajax({
            url: 'mark_notifications_read.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update the notification count
                    updateNotificationBadge(0);
                }
            }
        });
    });
});
