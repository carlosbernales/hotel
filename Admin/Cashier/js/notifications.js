// Track the last known notification count
let lastNotificationCount = 0;

// Function to show a notification popup
function showNewOrderNotification(order) {
    const orderType = order.order_type.charAt(0).toUpperCase() + order.order_type.slice(1);
    const message = `New ${orderType} Order from ${order.customer_name} - ₱${parseFloat(order.total_amount).toFixed(2)}`;
    
    // Use the existing showOrderAlert function from header.php
    if (typeof showOrderAlert === 'function') {
        showOrderAlert(message);
    } else {
        // Fallback if showOrderAlert is not available
        alert(message);
    }
}

$(document).ready(function() {
    // Function to check for new notifications
    function checkNotifications() {
        $.ajax({
            url: 'check_new_orders.php',
            type: 'GET',
            dataType: 'json',
            success: function(notifications) {
                if (Array.isArray(notifications)) {
                    const currentCount = notifications.length;
                    
                    // Check for new notifications
                    if (currentCount > lastNotificationCount) {
                        // Find the new notifications
                        const newNotifications = notifications.slice(0, currentCount - lastNotificationCount);
                        
                        // Show popup for each new notification (most recent first)
                        newNotifications.reverse().forEach(notification => {
                            showNewOrderNotification(notification);
                        });
                        
                        // Play sound for new notifications
                        const notificationSound = document.getElementById('notificationSound');
                        if (notificationSound) {
                            notificationSound.play().catch(e => console.log('Audio play failed:', e));
                        }
                    }
                    
                    // Update the UI
                    updateNotificationBadge(currentCount);
                    updateNotificationDropdown(notifications);
                    
                    // Update the last known count
                    lastNotificationCount = currentCount;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Function to update the notification badge
    function updateNotificationBadge(count) {
        const badge = $('.label-danger');
        const currentCount = parseInt(badge.text()) || 0;
        
        // Only update if count has changed
        if (currentCount !== count) {
            badge.text(count);
            badge.toggle(count > 0);
            
            // Add animation for new notifications
            if (count > currentCount) {
                badge.addClass('pulse');
                setTimeout(() => badge.removeClass('pulse'), 1000);
            }
        }
    }

    // Function to update the notification dropdown
    function updateNotificationDropdown(notifications) {
        const messageCenter = $('.message-center');
        const currentNotifications = [];
        
        // Get current notification IDs
        $('.message-item[data-order-id]').each(function() {
            currentNotifications.push($(this).data('order-id'));
        });
        
        // Only update if there are changes
        const newNotifications = notifications.filter(n => !currentNotifications.includes(n.order_id.toString()));
        
        if (newNotifications.length === 0 && notifications.length > 0) {
            return; // No new notifications to add
        }
        
        messageCenter.empty();

        if (notifications.length === 0) {
            messageCenter.html(`
                <a href="#" class="message-item">
                    <div class="message-content">
                        <h5 class="message-title">No new notifications</h5>
                        <span class="time">Just now</span>
                    </div>
                </a>
            `);
            return;
        }


        notifications.forEach(notification => {
            const orderType = notification.order_type.charAt(0).toUpperCase() + notification.order_type.slice(1);
            const html = `
                <a href="ProcessingOrder.php?order_id=${notification.order_id}" class="message-item" data-order-id="${notification.order_id}">
                    <div class="message-content">
                        <h5 class="message-title">New ${orderType} Order</h5>
                        <p>Customer: ${notification.customer_name}</p>
                        <p>Amount: ₱${parseFloat(notification.total_amount).toFixed(2)}</p>
                        <span class="time">${formatDate(notification.order_date)}</span>
                    </div>
                </a>
            `;
            messageCenter.append(html);
        });
    }

    // Format date to a readable string
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds/60)}m ago`;
        if (diffInSeconds < 86400) return date.toLocaleTimeString();
        return date.toLocaleDateString();
    }

    // Initialize notifications
    checkNotifications();
    
    // Check for new notifications every 10 seconds
    const notificationInterval = setInterval(checkNotifications, 10000);
    
    // Mark notification as read when clicked
    $(document).on('click', '.message-item', function(e) {
        const orderId = $(this).data('order-id');
        if (!orderId) return;
        
        // Don't prevent default so the link will still work
        
        // Mark as read in the background
        $.ajax({
            url: 'mark_notification_read.php',
            type: 'POST',
            data: { order_id: orderId },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });
    
    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        clearInterval(notificationInterval);
    });
});