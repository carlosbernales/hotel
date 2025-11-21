<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Include database connection
require_once 'db.php';

// Fetch user details with first_name
$userId = $_SESSION['user_id'];
$userQuery = "SELECT id, first_name, name, user_type FROM userss WHERE id = ?";
$stmt = $con->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Store user details in session
$_SESSION['first_name'] = $user['first_name'] ?? '';
$_SESSION['name'] = $user['name'] ?? '';
$_SESSION['user_type'] = $user['user_type'] ?? '';
$_SESSION['user_name'] = $user['name'] ?? 'Guest';

// Set timezone
date_default_timezone_set('Asia/Manila');

// Fetch notifications for orders
$notificationQuery = "SELECT n.*, u.name as sender_name 
                     FROM notifications n 
                     LEFT JOIN userss u ON n.user_id = u.id 
                     WHERE n.type = 'order' 
                     AND n.is_read = 0 
                     ORDER BY n.created_at DESC";
$stmt = $con->prepare($notificationQuery);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$notificationCount = count($notifications);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Casa Estela Boutique Hotel & Cafe</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/notification.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!--Custom Font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <script src="js/notifications.js"></script>

    <!-- Add this in the head section with your other resource links -->
    <audio id="notificationSound" src="notification.mp3" preload="auto"></audio>

    <style>
    /* Add pulse animation for notifications */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    .label-danger.pulse {
        animation: pulse 0.5s ease-in-out;
        display: inline-block !important;
    }
    
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    /* Ensure all main content pages use these base styles */
    .container, 
    .content-wrapper, 
    .main-section {
        width: 100%;
        transition: all 0.3s ease;
    }

    /* Add responsive styles for tables and other content */
    table {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        display: block;
    }

    @media screen and (max-width: 768px) {
        .main-content {
            margin-left: 60px !important;
            padding: 10px;
        }
        
        .sidebar:hover + .main-content {
            margin-left: 60px !important;
        }
        
        .sidebar {
            width: 0;
        }
        
        .sidebar:hover {
            width: 200px;
        }
    }

    .cashier-name {
        margin-left: 5px;
        font-size: 14px;
    }
    
    .user-profile-section {
        padding: 15px;
        min-width: 200px;
    }
    
    .dropdown-menu.dropdown-alerts {
        min-width: 250px;
    }
    
    .dropdown-menu .divider {
        margin: 5px 0;
    }
    
    .dropdown-menu > li > a {
        padding: 10px 15px;
    }
    
    .dropdown-menu > li > a i {
        margin-right: 10px;
    }
    
    .img-circle {
        border-radius: 50%;
        object-fit: cover;
    }

    .notification-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
    }

    .notifications {
        max-height: 300px;
        overflow-y: auto;
    }

    .notifications .dropdown-item {
        white-space: normal;
        padding: 10px 15px;
    }

    .notifications .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .notifications .text-muted {
        font-size: 0.8em;
        margin-top: 5px;
    }

    /* Add these styles to your existing style section */
    .navbar-brand {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 24px;
        color: #fff !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        letter-spacing: 1px;
    }

    .navbar-custom {
        background: linear-gradient(to right, #b8860b, #daa520);
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar-nav > li > a {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: 15px;
        color: #fff !important;
        padding: 15px 20px;
        transition: all 0.3s ease;
    }

    .navbar-nav > li > a:hover {
        background-color: rgba(255,255,255,0.1) !important;
    }

    .notification-badge {
        background: #ff4444;
        font-weight: 600;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: none;
        padding: 8px 0;
    }

    .dropdown-header {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #333;
        padding: 12px 20px;
        font-size: 14px;
    }

    .dropdown-item {
        font-family: 'Montserrat', sans-serif;
        padding: 10px 20px;
        color: #555;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #b8860b;
    }

    .dropdown-item i {
        margin-right: 10px;
        color: #b8860b;
    }

    .navbar-toggle {
        border-color: #fff;
    }

    .navbar-toggle .icon-bar {
        background-color: #fff;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .navbar-brand {
            font-size: 20px;
        }
        
        .navbar-nav > li > a {
            font-size: 14px;
            padding: 12px 15px;
        }
    }

    /* Add these styles to your existing style section */
    .navbar-nav.navbar-right {
        margin-right: 30px; /* Increase right margin for the entire right nav */
    }

    .navbar-nav.navbar-right > li {
        margin-left: 20px; /* Space between notification and profile items */
    }

    .notification-toggle,
    .profile-dropdown {
        padding: 15px 10px !important; /* Adjust padding for the icons */
    }

    /* Adjust icon spacing */
    .notification-toggle i,
    .profile-dropdown i {
        margin-right: 8px;
    }

    /* Adjust badge position */
    .notification-badge {
        right: 0;
        top: 10px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .navbar-nav.navbar-right {
            margin-right: 15px;
        }
        
        .navbar-nav.navbar-right > li {
            margin-left: 10px;
        }
        
        .notification-toggle,
        .profile-dropdown {
            padding: 12px 8px !important;
        }
    }

    /* Add these styles to your existing style section */
    .user-menu {
        margin-left: 5px;
    }

    .profile-menu {
        padding: 0;
        width: 250px;
    }

    .user-header {
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .user-header i {
        color: #b8860b;
        margin-bottom: 10px;
    }

    .user-header p {
        margin: 0;
        color: #333;
    }

    .user-header small {
        display: block;
        font-size: 12px;
        color: #777;
        margin-top: 5px;
    }

    .profile-menu .dropdown-item {
        padding: 12px 20px;
    }

    .profile-menu .divider {
        margin: 0;
    }

    .profile-dropdown .fa-chevron-down {
        font-size: 12px;
        margin-left: 5px;
    }
    </style>
    <?php include 'sidebar.php'; ?>
    <!-- Add this JavaScript code before </head> tag -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function markAsRead(notificationId) {
            return fetch('mark_notification_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ notification_id: notificationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge with remaining count from server
                    const badge = document.querySelector('.notification-badge');
                    if (data.remaining_count > 0) {
                        badge.style.display = 'inline';
                        badge.textContent = data.remaining_count;
                    } else {
                        badge.style.display = 'none';
                        badge.textContent = '0';
                    }
                } else {
                    console.error('Failed to mark notification as read:', data.error);
                }
                return data;
            });
        }

        function updateNotifications() {
            let previousCount = parseInt(document.querySelector('.notification-badge')?.textContent || '0');
            
            fetch('get_notifications.php')
                .then(response => response.json())
                .then(data => {
                    const notificationBadge = document.querySelector('.notification-badge');
                    const notificationsList = document.querySelector('.notifications');
                    
                    // Check if there are new notifications
                    if (data.count > previousCount) {
                        showOrderAlert('You have a new order. Please check it!');
                        // Optional: Play a notification sound
                        const audio = new Audio('notification.mp3'); // Make sure to add this audio file
                        audio.play().catch(e => console.log('Audio play failed:', e));
                    }

                    // Update badge
                    if (data.count > 0) {
                        notificationBadge.style.display = 'inline';
                        notificationBadge.textContent = data.count;
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                    
                    // Update dropdown content
                    updateNotificationDropdown(data.notifications);
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        function updateNotificationDropdown(notifications) {
            const notificationsList = document.querySelector('.notifications');
            
            if (notifications.length > 0) {
                notificationsList.innerHTML = `
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        Notifications
                        <button class="btn btn-sm btn-link mark-all-read">Mark All as Read</button>
                    </li>
                    <li class="divider"></li>
                `;
                
                notifications.forEach(notification => {
                    const orderIdMatch = notification.message.match(/\#(\d+)/);
                    const orderId = orderIdMatch ? orderIdMatch[1] : '';
                    
                    const notificationItem = document.createElement('li');
                    notificationItem.innerHTML = `
                        <div class="dropdown-item-wrapper d-flex justify-content-between align-items-center">
                            <a href="index.php?Order?" 
                               class="dropdown-item" 
                               data-notification-id="${notification.id}"
                               data-order-id="${orderId}">
                                <i class="fa fa-shopping-cart"></i>
                                ${notification.message}
                                <small class="text-muted d-block">
                                    ${new Date(notification.created_at).toLocaleString()}
                                </small>
                            </a>
                            <button class="btn btn-sm btn-link mark-read" 
                                    data-notification-id="${notification.id}">
                                <i class="fa fa-check"></i>
                            </button>
                        </div>
                    `;
                    notificationsList.appendChild(notificationItem);
                });
            } else {
                notificationsList.innerHTML = `
                    <li class="dropdown-header">Notifications</li>
                    <li class="divider"></li>
                    <li><a href="#" class="dropdown-item">No new notifications</a></li>
                `;
                // Hide badge when no notifications
                document.querySelector('.notification-badge').style.display = 'none';
            }
        }

        // Add dropdown open/close handler
        const notificationToggle = document.querySelector('.notification-toggle');
        notificationToggle.addEventListener('click', function(e) {
            // Update notifications when dropdown is opened
            updateNotifications();
        });

        function updateBadgeCount(change = -1) {
            const badge = document.querySelector('.notification-badge');
            if (!badge) return;

            const currentCount = parseInt(badge.textContent || '0');
            const newCount = Math.max(0, currentCount + change);
            
            if (newCount <= 0) {
                badge.style.display = 'none';
                badge.textContent = '0';
            } else {
                badge.style.display = 'inline';
                badge.textContent = newCount;
            }
            
            return newCount;
        }

        // Update the click handler
        document.querySelector('.notifications').addEventListener('click', function(e) {
            // Handle mark all as read button
            if (e.target.closest('.mark-all-read')) {
                e.preventDefault();
                const notifications = document.querySelectorAll('[data-notification-id]');
                
                if (notifications.length > 0) {
                    Promise.all(
                        Array.from(notifications).map(notification => 
                            markAsRead(notification.dataset.notificationId)
                        )
                    ).then(() => {
                        // Clear notifications and hide badge
                        const notificationsList = document.querySelector('.notifications');
                        notificationsList.innerHTML = `
                            <li class="dropdown-header">Notifications</li>
                            <li class="divider"></li>
                            <li><a href="#" class="dropdown-item">No new notifications</a></li>
                        `;
                        const badge = document.querySelector('.notification-badge');
                        badge.style.display = 'none';
                        badge.textContent = '0';
                    });
                }
                return;
            }

            // Handle individual mark as read button
            if (e.target.closest('.mark-read')) {
                e.preventDefault();
                const button = e.target.closest('.mark-read');
                const notificationId = button.dataset.notificationId;
                markAsRead(notificationId).then(() => {
                    const listItem = button.closest('li');
                    listItem.remove();
                    
                    const remainingCount = updateBadgeCount();
                    if (remainingCount <= 0) {
                        // Update dropdown to show no notifications
                        const notificationsList = document.querySelector('.notifications');
                        notificationsList.innerHTML = `
                            <li class="dropdown-header">Notifications</li>
                            <li class="divider"></li>
                            <li><a href="#" class="dropdown-item">No new notifications</a></li>
                        `;
                    }
                });
                return;
            }

            // Handle notification link click
            const notificationLink = e.target.closest('.dropdown-item');
            if (notificationLink && notificationLink.dataset.notificationId) {
                e.preventDefault();
                const notificationId = notificationLink.dataset.notificationId;
                const orderId = notificationLink.dataset.orderId;
                
                markAsRead(notificationId).then(data => {
                    if (data.success) {
                        updateBadgeCount();
                        // Redirect to processing order page
                        window.location.href = `index.php?Order&order_id=${orderId}`;
                    }
                });
            }
        });

        // Initial update
        updateNotifications();

        // Update notifications every 30 seconds
        setInterval(updateNotifications, 30000);
    });

    // Add the alert styles
    const alertStyles = document.createElement('style');
    alertStyles.textContent = `
        .order-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 18px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateX(0);
            max-width: 400px;
            animation: slideIn 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            border-left: 5px solid #388E3C;
            backdrop-filter: blur(5px);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .order-alert i {
            font-size: 24px;
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-alert:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .order-alert:active {
            transform: translateY(0);
        }


        @keyframes slideIn {
            from {
                transform: translateX(120%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }


        .order-alert.fade-out {
            animation: fadeOut 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(120%);
            }
        }
        
        @keyframes bellShake {
            0% { transform: rotate(0); }
            25% { transform: rotate(10deg); }
            50% { transform: rotate(-10deg); }
            75% { transform: rotate(5deg); }
            100% { transform: rotate(0); }
        }
    `;
    document.head.appendChild(alertStyles);

    // Function to show the alert
    function showOrderAlert(message) {
        // Remove any existing alerts
        const existingAlert = document.querySelector('.order-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Create new alert
        const alert = document.createElement('div');
        alert.className = 'order-alert';
        alert.innerHTML = `
            <i class="fa fa-bell"></i>
            <span>${message}</span>
        `;

        // Add to document
        document.body.appendChild(alert);

        // Add click handler to dismiss
        alert.addEventListener('click', () => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        });

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentElement) {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    }
    </script>
</head>
<body>

<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <i class="fa fa-home"></i> CASA ESTELA
                <small style="font-size: 14px; display: block;">BOUTIQUE HOTEL & CAFE</small>
            </a>
            <ul class="nav navbar-nav navbar-right">
                <!-- Notifications Dropdown -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle notification-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="notification-badge" style="display: none;">0</span>
                    </a>
                    <ul class="dropdown-menu notifications">
                        <li class="dropdown-header">
                            <i class="fa fa-bell"></i> Notifications
                        </li>
                        <li class="divider"></li>
                        <!-- Notifications will be dynamically inserted here -->
                        <li><a href="#" class="dropdown-item">No new notifications</a></li>
                    </ul>
                </li>

                <!-- Profile Dropdown -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle profile-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle"></i> 
                        <span class="user-menu">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu profile-menu">
                        <li class="user-header">
                            <i class="fa fa-user-circle fa-3x"></i>
                            <p>
                                <?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : ''; ?>
                                <small><?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?></small>
                            </p>
                        </li>
                        <li class="divider"></li>
                        <li><a href="profile.php" class="dropdown-item"><i class="fa fa-id-card"></i> Profile</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php" class="dropdown-item"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>