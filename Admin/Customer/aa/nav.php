<?php
require_once 'db_con.php';

// Debugging code to check session
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<!-- Session debug: ";
var_dump($_SESSION);
echo " -->";
?>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="home.php">
            <span class="brand-text">E Akomoda</span>
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Mobile Icons for Logged In Users -->
            <div class="mobile-icons d-lg-none">
                <div class="dropdown">
                    <button type="button" class="notification-icon" id="notificationDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php
                        // Get unread notifications count
                        $notifQuery = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
                        $stmt = mysqli_prepare($con, $notifQuery);
                        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
                        mysqli_stmt_execute($stmt);
                        $unreadCount = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'];
                        
                        if ($unreadCount > 0) {
                            echo "<span class='notification-badge'>$unreadCount</span>";
                        }
                        ?>
                    </button>
                    <div class="dropdown-menu notification-dropdown" aria-labelledby="notificationDropdownMenu">
                        <div class="notification-header d-flex justify-content-between align-items-center p-3">
                            <h6 class="mb-0">Notifications</h6>
                            <div class="d-flex align-items-center">
                                <!-- Add Mark as Read button -->
                                <?php if ($unreadCount > 0): ?>
                                    <button type="button" class="btn btn-link btn-sm text-muted me-2" onclick="markAllAsRead()">
                                        Mark all as read
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="btn-close" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="notification-body">
                            <?php
                            // Get recent notifications
                            $notifQuery = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
                            $stmt = mysqli_prepare($con, $notifQuery);
                            mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
                            mysqli_stmt_execute($stmt);
                            $notifications = mysqli_stmt_get_result($stmt);
                            
                            if (mysqli_num_rows($notifications) > 0):
                                while ($notification = mysqli_fetch_assoc($notifications)):
                                    $icon = $notification['type'] === 'order' ? 'fa-shopping-bag' : 
                                           ($notification['type'] === 'booking' ? 'fa-calendar-check' : 'fa-bell');
                                    $iconColor = $notification['type'] === 'order' ? 'text-primary' : 
                                                ($notification['type'] === 'booking' ? 'text-info' : 'text-secondary');
                            ?>
                                <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                                    <div class="notification-content">
                                        <div class="notification-icon-wrapper <?php echo $iconColor; ?>">
                                            <i class="fas <?php echo $icon; ?>"></i>
                                        </div>
                                        <div class="notification-text">
                                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <span class="notification-time">
                                                <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">No notifications</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <a href="messages.php" class="message-icon">
                    <i class="fas fa-envelope"></i>
                    <span class="message-badge" id="mobileMsgBadge">0</span>
                </a>
                <?php 
                $current_page = basename($_SERVER['PHP_SELF']);
                if ($current_page == 'roomss.php'): 
                ?>
                    <a href="#" class="nav-link position-relative" onclick="showBookingList(); return false;">
                        <i class="fas fa-bed booking-list-icon"></i>
                        <span id="bookingBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="display: none;">
                            0
                        </span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Profile Dropdown for Logged In Users -->
            <div class="mobile-profile d-lg-none">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="mobileProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileProfileDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="mybookings.php"><i class="fas fa-bookmark me-2"></i>My Bookings</a></li>
                        <li><a class="dropdown-item" href="myorders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <!-- Mobile Login/Register for Non-Logged In Users -->
            <div class="mobile-auth d-lg-none">
                <!-- Add cart icon for mobile -->
                <?php 
                $current_page = basename($_SERVER['PHP_SELF']);
                if ($current_page == 'roomss.php'): 
                ?>
                    <div class="bed-icon-container me-3" onclick="showBookingList()">
                        <i class="fas fa-bed"></i>
                        <span class="booking-badge" style="display: none;">0</span>
                    </div>
                <?php endif; ?>
                <a href="login.php" class="btn btn-outline-primary btn-sm me-2">Login</a>
                <a href="signup.php" class="btn btn-primary btn-sm">Register</a>
            </div>
        <?php endif; ?>

        <!-- Hamburger Menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="roomss.php">Rooms</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="cafeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Café
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="cafeDropdown">
                        <li><a class="dropdown-item" href="cafes.php"><i class="fas fa-coffee me-2"></i>Menu & Orders</a></li>
                        <li><a class="dropdown-item" href="table.php"><i class="fas fa-chair me-2"></i>Table Reservation</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                
            </ul>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Desktop Icons and Profile for Logged In Users -->
                <div class="desktop-menu d-none d-lg-flex align-items-center">
                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button type="button" class="notification-icon" id="desktopNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-badge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu notification-dropdown" aria-labelledby="desktopNotificationDropdown">
                            <div class="notification-header d-flex justify-content-between align-items-center p-3">
                                <h6 class="mb-0">Notifications</h6>
                                <div class="d-flex align-items-center">
                                    <!-- Add Mark as Read button -->
                                    <?php if ($unreadCount > 0): ?>
                                        <button type="button" class="btn btn-link btn-sm text-muted me-2" onclick="markAllAsRead()">
                                            Mark all as read
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn-close" aria-label="Close"></button>
                                </div>
                            </div>
                            <div class="notification-body">
                                <?php
                                // Get recent notifications
                                $notifQuery = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
                                $stmt = mysqli_prepare($con, $notifQuery);
                                mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
                                mysqli_stmt_execute($stmt);
                                $notifications = mysqli_stmt_get_result($stmt);
                                
                                if (mysqli_num_rows($notifications) > 0):
                                    while ($notification = mysqli_fetch_assoc($notifications)):
                                        $icon = $notification['type'] === 'order' ? 'fa-shopping-bag' : 
                                               ($notification['type'] === 'booking' ? 'fa-calendar-check' : 'fa-bell');
                                        $iconColor = $notification['type'] === 'order' ? 'text-primary' : 
                                                    ($notification['type'] === 'booking' ? 'text-info' : 'text-secondary');
                                ?>
                                    <div class="notification-item">
                                        <div class="notification-content">
                                            <div class="notification-icon-wrapper <?php echo $iconColor; ?>">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </div>
                                            <div class="notification-text">
                                                <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <span class="notification-time">
                                                    <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <div class="text-center py-3">
                                        <p class="text-muted mb-0">No notifications</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages Dropdown -->
                    <div class="dropdown message-dropdown">
                        <a href="messages.php" class="message-icon">
                            <i class="fas fa-envelope"></i>
                            <span class="message-badge" id="desktopMsgBadge">0</span>
                        </a>
                    </div>
                    
                    <!-- Booking Icon -->
                    <?php 
                    $current_page = basename($_SERVER['PHP_SELF']);
                    if ($current_page == 'roomss.php'): 
                    ?>
                        <div class="bed-icon-container" onclick="showBookingList()">
                            <i class="fas fa-bed"></i>
                            <span class="booking-badge" style="display: none;">0</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="desktopProfileDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <span>My Profile</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="desktopProfileDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="mybookings.php"><i class="fas fa-bookmark me-2"></i>My Bookings</a></li>
                            <li><a class="dropdown-item" href="myorders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Desktop Login/Register for Non-Logged In Users -->
                <div class="desktop-auth d-none d-lg-flex align-items-center">
                    <!-- Add cart icon for desktop -->
                    <?php 
                    $current_page = basename($_SERVER['PHP_SELF']);
                    if ($current_page == 'roomss.php'): 
                    ?>
                        <div class="bed-icon-container me-3" onclick="showBookingList()">
                            <i class="fas fa-bed"></i>
                            <span class="booking-badge" style="display: none;">0</span>
                        </div>
                    <?php endif; ?>
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="signup.php" class="btn btn-primary">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // The navigation link fixing was replaced with direct absolute URLs
        // so this script is no longer needed
        /*
        // Fix navigation links for the hosted environment
        const baseUrl = window.location.href.substring(0, window.location.href.indexOf('/Admin/') + 7);
        const customerPath = baseUrl + 'Customer/aa/';
        
        // Fix all navigation links to use absolute paths
        document.querySelectorAll('.dropdown-menu a.dropdown-item').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('http') && !href.startsWith('#')) {
                // Get just the filename
                const filename = href.split('/').pop();
                link.href = customerPath + filename;
            }
        });
        
        console.log('Navigation links fixed for hosted environment');
        */
    });
</script>

<style>
:root {
    --primary-color: #d4af37;
    --primary-dark: #856f11;
    --text-color: #333;
    --white: #ffffff;
    --transition: all 0.3s ease;
}

.navbar {
    background-color: var(--white);
    padding: 1rem 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: var(--transition);
}

.navbar.scrolled {
    padding: 0.5rem 0;
}

.navbar-brand {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color) !important;
}

.brand-text {
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-link {
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    color: var(--text-color) !important;
    padding: 0.5rem 1rem;
    margin: 0 0.2rem;
    position: relative;
    transition: var(--transition);
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background-color: var(--primary-color);
    transition: var(--transition);
}

.nav-link:hover::after {
    width: 100%;
}

.nav-link:hover {
    color: var(--primary-color) !important;
}

.navbar-toggler {
    border: none;
    padding: 0.5rem;
}

.navbar-toggler:focus {
    box-shadow: none;
}

.navbar-toggler i {
    color: var(--primary-color);
    font-size: 1.5rem;
}

.notification-icon {
    position: relative;
    margin: 0px;
    font-size: 1.2rem;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 0.25rem 0.5rem;
    margin-left: 10px;
    font-size: 0.78rem;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.notification-icon {
    position: relative;
    display: inline-block;
    padding: 0.5rem;
}

.notification-icon i {
    font-size: 1.2rem;
    color: #333;
    transition: color 0.3s ease;
}

.notification-icon:hover i {
    color: #d4af37;
}

/* Animation for badge when count updates */
@keyframes badgePulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.notification-badge.animate {
    animation: badgePulse 0.3s ease-in-out;
}

/* Mobile styles for dropdown */
@media (max-width: 991.98px) {
    .notification-badge {
        top: -5px;
        right: -5px;
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
    
    .notification-icon {
        padding: 0.3rem;
    }
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.7rem 1.5rem;
    font-weight: 500;
    transition: var(--transition);
}

.dropdown-item:hover {
    background-color: rgba(212, 175, 55, 0.1);
    color: var(--primary-color);
}

.dropdown-divider {
    margin: 0.5rem 0;
    border-color: #eee;
}

/* Mobile Styles */
@media (max-width: 991.98px) {
    .navbar {
        padding: 0.5rem 1rem;
    }

    .navbar-brand {
        font-size: 1.5rem;
    }

    .mobile-icons {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-right: 0;
    }

    .mobile-profile {
        margin-left: 12px;
    }

    .navbar-collapse {
        background-color: var(--white);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .nav-item {
        margin: 0.5rem 0;
    }

    .nav-link::after {
        display: none;
    }
    .notification-icon {
        position: relative;
        padding-left: 10px;
        font-size: 1.2rem;
    }

    .dropdown-menu {
        box-shadow: none;
        border: 1px solid #eee;
        margin-top: 0.5rem;
    }
}

/* Animation for dropdown */
.dropdown-menu {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-icon .fa-bed {
    color: var(--text-color);
    transition: var(--transition);
}

.notification-icon:hover .fa-bed {
    color: var(--primary-color);
}

/* Animation for cart badge */
@keyframes cartBounce {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.cart-animation {
    animation: cartBounce 0.5s ease;
}

/* Dropdown styles */
.nav-item.dropdown:hover .dropdown-menu {
    display: block;
    animation: fadeIn 0.2s ease-out;
}

.dropdown-menu {
    margin-top: 0;
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.7rem 1.5rem;
    color: var(--text-color);
    font-weight: 500;
    transition: all 0.3s ease;
}

.dropdown-item i {
    color: var(--primary-color);
    width: 20px;
    text-align: center;
}

.dropdown-item:hover {
    background-color: rgba(212, 175, 55, 0.1);
    color: var(--primary-color);
    transform: translateX(5px);
}

/* Mobile styles for dropdown */
@media (max-width: 991.98px) {
    .dropdown-menu {
        border: none;
        background-color: rgba(255, 251, 251, 0.88);
        box-shadow: none;
        padding: 0;
        margin: 0.5rem 0;
    }

    .dropdown-item {
        padding: 0.7rem 1rem;
    }

    .dropdown-item:hover {
        transform: none;
    }
}

/* Animation for dropdown items */
.dropdown-item {
    position: relative;
    overflow: hidden;
}

.dropdown-item::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--primary-color);
    transition: width 0.3s ease;
}

.dropdown-item:hover::after {
    width: 100%;
}

.notification-panel {
    width: 350px;
    max-height: 600px;
    padding: 0;
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.notification-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    background-color: #fff;
}

.notification-header h6 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.notification-body {
    max-height: 500px;
    overflow-y: auto;
    padding: 0;
    background-color: #fff;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f5f5f5;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.notification-icon-wrapper {
    width: 35px;
    height: 35px;
    min-width: 35px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-icon-wrapper i {
    font-size: 1rem;
}

.notification-text {
    flex: 1;
}

.notification-text p {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #333;
    line-height: 1.4;
}

.notification-time {
    font-size: 12px;
    color: #888;
    display: block;
}

/* Mobile styles */
@media (max-width: 991.98px) {
    .notification-panel {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        max-height: 100vh;
        margin: 0;
        border-radius: 0;
    }

    .notification-header {
        position: sticky;
        top: 0;
        z-index: 1030;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background-color: #fff;
        border-bottom: 1px solid #eee;
    }

    .notification-body {
        height: calc(100vh - 56px);
        max-height: none;
    }

    .notification-item {
        padding: 15px;
    }
}

/* Icon colors */
.text-primary { color: #4e73df; }
.text-info { color: #36b9cc; }
.text-secondary { color: #858796; }

/* Animation for dropdown */
.dropdown-menu.show {
    animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 991.98px) {
    .dropdown-menu.show {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }
        to {
            transform: translateY(0);
        }
    }
}

.message-panel {
    width: 350px;
    padding: 0;
    max-height: 500px;
    overflow-y: auto;
}

.message-header {
    border-bottom: 1px solid #eee;
    background-color: #f8f9fa;
}

.message-body {
    max-height: 400px;
    overflow-y: auto;
}

.message-item {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s;
    cursor: pointer;
}

.message-item:hover {
    background-color: #f8f9fa;
}

.message-item.unread {
    background-color: #f0f7ff;
}

.message-content {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.2rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.message-time {
    font-size: 0.75rem;
    color: #999;
}

.bed-icon-container {
    position: relative;
    cursor: pointer;
    padding: 0.5rem;
}

.bed-icon-container .fa-bed {
    font-size: 1.2rem;
    color: #333;
    transition: color 0.3s ease;
}

.bed-icon-container:hover .fa-bed {
    color: #d4af37;
}

.booking-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.7rem;
    font-weight: bold;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
}

/* Add these new styles */
.desktop-auth, .mobile-auth {
    display: flex;
    align-items: center;
}

.desktop-auth .btn, .mobile-auth .btn {
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.desktop-auth .btn-outline-primary {
    border-color: #ffc107;
    color: #ffc107;
}

.desktop-auth .btn-outline-primary:hover {
    background-color: #ffc107;
    color: #fff;
}

.desktop-auth .btn-primary, .mobile-auth .btn-primary {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #fff;
}

.desktop-auth .btn-primary:hover, .mobile-auth .btn-primary:hover {
    background-color: #e0a800;
    border-color: #e0a800;
}

/* Add these to your existing CSS section */
.mobile-auth .bed-icon-container,
.desktop-auth .bed-icon-container {
    display: flex;
    align-items: center;
}

.mobile-auth .bed-icon-container i,
.desktop-auth .bed-icon-container i {
    font-size: 1.2rem;
    color: #333;
    transition: color 0.3s ease;
}

.mobile-auth .bed-icon-container:hover i,
.desktop-auth .bed-icon-container:hover i {
    color: #d4af37;
}

.notification-icon-wrapper {
    width: 40px;
    height: 40px;
    background-color: #f0f4ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-icon-wrapper .notification-icon {
    color: #4e73df;
    font-size: 1.2rem;
}

.notification-text p {
    margin-bottom: 0.25rem;
    color: #333;
    font-size: 0.95rem;
    line-height: 1.4;
}

.notification-text small {
    font-size: 0.8rem;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-item.unread .notification-text p {
    font-weight: 500;
}

@media (max-width: 991.98px) {
    .dropdown-menu[data-bs-popper] {
        margin-top: 0;
    }

    .notification-panel {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .notification-item:active {
        background-color: #f8f9fa;
    }

    .notification-text p {
        font-size: 0.9rem;
    }

    .btn-close {
        padding: 0.5rem;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
}

/* Add these styles for the notification icon and dropdown */
.mobile-icons .notification-icon {
    background: none;
    border: none;
    padding: 0.5rem;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-icons .notification-icon:focus {
    outline: none;
    box-shadow: none;
}

.mobile-icons .notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    padding: 0.25rem;
}

.mobile-icons .dropdown-menu {
    margin: 0;
    padding: 0;
    border: none;
    border-radius: 0;
    width: 100vw;
    height: 100vh;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    transform: none !important;
    max-width: none;
    max-height: none;
}

.mobile-icons .notification-icon {
    padding: 0.5rem;
    display: flex;
    align-items: center;
    position: relative;
}

.mobile-icons .notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 50%;
    background-color: #dc3545;
    color: white;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
}

.mobile-icons .notification-header {
    background-color: #fff;
    border-bottom: 1px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 1030;
}

.mobile-icons .notification-body {
    height: calc(100vh - 60px);
    overflow-y: auto;
    background-color: #fff;
}

.mobile-icons .notification-item {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.mobile-icons .notification-item:last-child {
    border-bottom: none;
}

.mobile-icons .notification-item.unread {
    background-color: #f0f7ff;
}

.mobile-icons .dropdown-menu.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: none !important;
}

/* Animation for dropdown */
@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

.mobile-icons .dropdown-menu.show {
    animation: slideUp 0.3s ease-out;
}

@media (min-width: 992px) {
    .mobile-icons {
        display: none;
    }
}

/* Compact and user-friendly notification dropdown */
.notification-dropdown {
    width: 260px !important;
    padding: 0;
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-top: 5px;
    background: #fff;
}

.notification-header {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f9fa;
    border-radius: 10px 10px 0 0;
}

.notification-header h6 {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.notification-body {
    max-height: 300px;
    overflow-y: auto;
    padding: 5px 0;
}

.notification-item {
    padding: 8px 10px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.15s;
    cursor: pointer;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f7ff;
    position: relative;
}

.notification-item.unread:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background-color: #0d6efd;
    border-radius: 0 2px 2px 0;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.notification-icon-wrapper {
    width: 28px;
    height: 28px;
    min-width: 28px;
    background-color: #f0f4ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-icon-wrapper i {
    font-size: 12px;
    color: #0d6efd;
}

.notification-text {
    flex: 1;
    min-width: 0;
}

.notification-text p {
    margin: 0;
    font-size: 12px;
    line-height: 1.4;
    color: #333;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.notification-time {
    font-size: 10px;
    color: #888;
    margin-top: 3px;
    display: block;
}

.btn-close {
    padding: 4px;
    font-size: 12px;
    opacity: 0.5;
}

.btn-close:hover {
    opacity: 1;
}

/* Custom scrollbar for notification body */
.notification-body::-webkit-scrollbar {
    width: 4px;
}

.notification-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}

.notification-body::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Mobile notification styles */
@media (max-width: 991.98px) {
    .notification-dropdown {
        position: absolute !important;
        top: 45px !important;
        right: 0 !important;
        left: auto !important;
        width: 300px !important;
        max-height: 400px !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin: 0;
        background: #fff;
        animation: fadeIn 0.2s ease-out;
    }

    .notification-header {
        padding: 10px 15px;
        background: #fff;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .notification-header h6 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .notification-body {
        max-height: 350px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f5f5f5;
        background: #fff;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.unread {
        background-color: #f8f9fa;
    }

    .notification-content {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .notification-icon-wrapper {
        width: 32px;
        height: 32px;
        min-width: 32px;
        background-color: #f0f4ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-text {
        flex: 1;
    }

    .notification-text p {
        margin: 0 0 4px 0;
        font-size: 13px;
        line-height: 1.4;
        color: #333;
    }

    .notification-time {
        font-size: 11px;
        color: #888;
    }

    /* Animation for mobile dropdown */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile icons container */
    .mobile-icons {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-right: 0;
    }

    .mobile-icons .notification-icon,
    .mobile-icons .message-icon {
        padding: 5px;
        min-width: 32px;
        min-height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-icons .notification-icon i,
    .mobile-icons .message-icon i {
        font-size: 18px;
    }

    .mobile-icons .notification-badge,
    .mobile-icons .message-badge {
        top: -2px;
        right: -2px;
        min-width: 16px;
        height: 16px;
        font-size: 10px;
    }

    .navbar > .container {
        padding: 0 10px;
    }

    .navbar-brand {
        margin-right: 0;
    }

    .mobile-profile {
        margin-left: 12px;
    }
}

/* Custom scrollbar for mobile */
@media (max-width: 991.98px) {
    .notification-body::-webkit-scrollbar {
        width: 4px;
    }

    .notification-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-body::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }

    .notification-body::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }
}

/* Header icons spacing */
.mobile-icons {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-right: 15px;
}

.mobile-icons .notification-icon,
.mobile-icons .message-icon {
    position: relative;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    cursor: pointer;
}

.mobile-icons .notification-icon i,
.mobile-icons .message-icon i {
    font-size: 20px;
    color: #333;
}

.notification-badge,
.message-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    min-width: 18px;
    height: 18px;
    background: #dc3545;
    color: #fff;
    border-radius: 50%;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px;
}

/* Desktop icons spacing */
@media (min-width: 992px) {
    .desktop-menu {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .desktop-menu .notification-icon,
    .desktop-menu .message-icon {
        padding: 8px;
        position: relative;
    }
}

/* Update navbar spacing */
.navbar .container {
    padding-left: 15px;
    padding-right: 15px;
}

.navbar-brand {
    margin-right: 20px;
}

/* Adjust mobile spacing */
@media (max-width: 991.98px) {
    .mobile-icons {
        margin-left: auto;
        padding-right: 0;
    }

    .mobile-profile {
        margin-left: 10px;
    }
}

/* Clean icon styles without borders */
.notification-icon,
.message-icon {
    background: none;
    border: none;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    outline: none !important;
    box-shadow: none !important;
}

.notification-icon:hover,
.message-icon:hover {
    background: none;
    border: none;
    outline: none;
}

.notification-icon i,
.message-icon i {
    font-size: 20px;
    color: #333;
}

.notification-badge,
.message-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    min-width: 18px;
    height: 18px;
    background: #dc3545;
    color: #fff;
    border-radius: 50%;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px;
}

/* Desktop menu icons */
.desktop-menu {
    display: flex;
    align-items: center;
    gap: 20px;
}

.desktop-menu .notification-icon,
.desktop-menu .message-icon {
    background: none;
    border: none;
}

/* Mobile icons */
.mobile-icons {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-right: 15px;
}

.mobile-icons .notification-icon,
.mobile-icons .message-icon {
    background: none;
    border: none;
}

/* Remove any Bootstrap button styles */
.notification-icon.btn,
.message-icon.btn {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    padding: 8px !important;
}

.notification-icon:focus,
.message-icon:focus {
    box-shadow: none !important;
    outline: none !important;
}

/* Bed icon styles */
.bed-icon {
    position: relative;
    padding: 5px;
    min-width: 32px;
    min-height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    cursor: pointer;
}

.bed-icon i {
    font-size: 18px;
    color: #333;
}

.booking-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    min-width: 16px;
    height: 16px;
    background: #dc3545;
    color: #fff;
    border-radius: 50%;
    font-size: 10px;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 2px;
}

/* Show badge when has items */
.booking-badge.has-items {
    display: flex;
}

@media (max-width: 991.98px) {
    .mobile-icons {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-right: 0;
    }

    .bed-icon {
        padding: 5px;
    }
}

/* Mobile styles for small devices (max-width: 480px) */
@media (max-width: 480px) {
    .navbar {
        padding: 0.4rem 0.5rem;
    }

    .navbar-brand {
        font-size: 1.3rem;
        margin-right: 5px;
    }

    .mobile-icons {
        gap: 8px;
        margin-right: 5px;
    }

    .mobile-icons .notification-icon,
    .mobile-icons .message-icon {
        padding: 4px;
        min-width: 28px;
        min-height: 28px;
    }

    .mobile-icons .notification-icon i,
    .mobile-icons .message-icon i {
        font-size: 16px;
    }

    .mobile-icons .notification-badge,
    .mobile-icons .message-badge {
        min-width: 14px;
        height: 14px;
        font-size: 9px;
        top: -3px;
        right: -3px;
    }

    .mobile-profile {
        margin-left: 5px;
    }

    .mobile-auth .btn {
        padding: 0.3rem 0.8rem;
        font-size: 0.75rem;
    }

    .navbar-toggler {
        padding: 0.3rem;
    }

    .navbar-toggler i {
        font-size: 1.2rem;
    }

    .notification-dropdown {
        width: 240px !important;
        right: -70px !important;
    }

    .notification-item {
        padding: 6px 8px;
    }

    .notification-icon-wrapper {
        width: 24px;
        height: 24px;
        min-width: 24px;
    }

    .notification-text p {
        font-size: 11px;
    }

    .notification-time {
        font-size: 9px;
    }

    .bed-icon-container {
        padding: 4px;
    }

    .bed-icon-container i {
        font-size: 16px;
    }

    .booking-badge {
        min-width: 14px;
        height: 14px;
        font-size: 9px;
    }

    /* Adjust dropdown positioning for small screens */
    .dropdown-menu[data-bs-popper] {
        left: auto !important;
        right: 0 !important;
    }

    /* Compact dropdown items */
    .dropdown-item {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    /* Adjust container padding */
    .navbar > .container {
        padding: 0 5px;
    }
}

/* Add these styles to your existing CSS section */
.btn-link {
    text-decoration: none;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
    transition: color 0.15s ease-in-out;
}

.btn-link:hover {
    color: #0d6efd;
    text-decoration: underline;
}

.notification-header {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
    border-bottom: 1px solid #dee2e6;
}

/* Mobile styles */
@media (max-width: 991.98px) {
    .notification-header .btn-link {
        font-size: 0.8125rem;
        padding: 0.2rem 0.4rem;
    }
}

/* Update the markAllAsRead function
function markAllAsRead() {
    fetch('mark_all_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread class from all notifications
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            
            // Remove all notification badges
            document.querySelectorAll('.notification-badge').forEach(badge => {
                badge.style.display = 'none';
                // Add fade out animation
                badge.classList.add('fade-out');
                setTimeout(() => {
                    badge.remove();
                }, 300);
            });
            
            // Hide the "Mark all as read" button with fade
            const markAllButton = document.querySelector('.notification-header .btn-link');
            if (markAllButton) {
                markAllButton.classList.add('fade-out');
                setTimeout(() => {
                    markAllButton.style.display = 'none';
                }, 300);
            }
        }
    })
    .catch(error => console.error('Error marking notifications as read:', error));
}

// Add this to your existing CSS
.notification-badge.fade-out {
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease-out;
}

.btn-link.fade-out {
    opacity: 0;
    transition: opacity 0.3s ease-out;
}

// Update the notification badge style
.notification-badge {
    opacity: 1;
    transform: scale(1);
    transition: all 0.3s ease-in-out;
}

/* Add these styles to your existing CSS section */
.desktop-menu .notification-dropdown {
    width: 320px;
    right: 0;
    left: auto;
    margin-top: 0.5rem;
}

.desktop-menu .notification-header {
    border-radius: 0.5rem 0.5rem 0 0;
}

.desktop-menu .btn-link {
    white-space: nowrap;
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

/* Ensure dropdown positioning */
.desktop-menu .dropdown {
    position: relative;
}

.desktop-menu .dropdown-menu {
    position: absolute;
    transform: none !important;
    top: 100% !important;
    right: 0 !important;
    left: auto !important;
}

/* Responsive adjustments */
@media (min-width: 992px) {
    .desktop-menu .notification-dropdown {
        min-width: 320px;
    }
    
    .desktop-menu .notification-header {
        padding: 0.75rem 1rem;
    }
    
    .desktop-menu .btn-link {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>

<script>
// Add shadow on scroll
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Function to update notification badges
function updateNotificationBadges() {
    // Example: Update notifications count from server
    fetch('get_notifications_count.php')
        .then(response => response.json())
        .then(data => {
            const notificationBadges = document.querySelectorAll('.fa-bell + .notification-badge');
            notificationBadges.forEach(badge => {
                if (data.notifications > 0) {
                    badge.style.display = 'block';
                    badge.textContent = data.notifications;
                } else {
                    badge.style.display = 'none';
                }
            });
        });

    // Example: Update messages count from server
    fetch('get_messages_count.php')
        .then(response => response.json())
        .then(data => {
            const messageBadges = document.querySelectorAll('.fa-envelope + .notification-badge');
            messageBadges.forEach(badge => {
                if (data.messages > 0) {
                    badge.style.display = 'block';
                    badge.textContent = data.messages;
                } else {
                    badge.style.display = 'none';
                }
            });
        });

    // Add message count update
    updateMessageCount();
}

// Update badges periodically
setInterval(updateNotificationBadges, 30000); // Update every 30 seconds

// Initial update
document.addEventListener('DOMContentLoaded', updateNotificationBadges);

// Initialize dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl, {
            display: 'static'
        });
    });

    // Handle notification clicks
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.dataset.notificationId;
            markAsRead(notificationId);
        });
    });

    // Handle dropdown show/hide
    const notificationDropdowns = document.querySelectorAll('.mobile-icons .dropdown');
    notificationDropdowns.forEach(dropdown => {
        dropdown.addEventListener('show.bs.dropdown', function () {
            document.body.style.overflow = 'hidden';
        });

        dropdown.addEventListener('hide.bs.dropdown', function () {
            document.body.style.overflow = '';
        });
    });

    // Close dropdown when clicking close button
    const closeButtons = document.querySelectorAll('.btn-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const dropdown = this.closest('.dropdown');
            const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
            if (dropdownInstance) {
                dropdownInstance.hide();
            }
        });
    });
});

// Mark notification as read
function markAsRead(notificationId) {
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `notification_id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notification) {
                notification.classList.remove('unread');
                updateNotificationBadge();
            }
        }
    });
}

// Mark all notifications as read
function markAllAsRead() {
    fetch('mark_all_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread class from all notifications
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            
            // Remove all notification badges
            document.querySelectorAll('.notification-badge').forEach(badge => {
                badge.style.display = 'none';
                // Add fade out animation
                badge.classList.add('fade-out');
                setTimeout(() => {
                    badge.remove();
                }, 300);
            });
            
            // Hide the "Mark all as read" button with fade
            const markAllButton = document.querySelector('.notification-header .btn-link');
            if (markAllButton) {
                markAllButton.classList.add('fade-out');
                setTimeout(() => {
                    markAllButton.style.display = 'none';
                }, 300);
            }
        }
    })
    .catch(error => console.error('Error marking notifications as read:', error));
}

// Update notification badge
function updateNotificationBadge() {
    fetch('get_unread_count.php')
        .then(response => response.json())
        .then(data => {
            const badges = document.querySelectorAll('.notification-badge');
            badges.forEach(badge => {
                if (data.count > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = data.count;
                    badge.classList.add('animate');
                    setTimeout(() => badge.classList.remove('animate'), 300);
                } else {
                    badge.style.display = 'none';
                }
            });
        });
}

// Initial update
updateNotificationBadge();

// Update notifications periodically
setInterval(updateNotificationBadge, 30000);

// Function to load notifications in dropdown
function loadNotificationPreviews() {
    const notificationList = document.getElementById('notificationList');
    
    fetch('get_notifications_preview.php')
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                notificationList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-bell text-muted mb-2"></i>
                        <p class="text-muted mb-0">No notifications</p>
                    </div>
                `;
                return;
            }

            notificationList.innerHTML = data.slice(0, 5).map(notification => `
                <div class="notification-item-preview d-flex align-items-start ${notification.is_read ? '' : 'unread'}">
                    <div class="notification-icon-preview ${notification.type}">
                        <i class="fas ${getNotificationIcon(notification.type)} fa-sm"></i>
                    </div>
                    <div>
                        <div class="notification-title-preview">${notification.title}</div>
                        <div class="notification-message-preview">${notification.message}</div>
                        <div class="notification-time-preview">${formatNotificationTime(notification.created_at)}</div>
                    </div>
                </div>
            `).join('');
        });
}

function getNotificationIcon(type) {
    switch (type) {
        case 'booking':
            return 'fa-calendar-check';
        case 'order':
            return 'fa-shopping-bag';
        default:
            return 'fa-bell';
    }
}

function formatNotificationTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // difference in seconds

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return date.toLocaleDateString();
}

// Load notifications when dropdown is opened
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.querySelector('.notification-dropdown');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('show.bs.dropdown', loadNotificationPreviews);
    }
});

function loadMessages() {
    const messageList = document.getElementById('messageList');
    
    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_messages'
    })
    .then(response => response.json())
    .then(data => {
        if (data.messages.length === 0) {
            messageList.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-envelope text-muted mb-2"></i>
                    <p class="text-muted mb-0">No messages</p>
                </div>
            `;
            return;
        }

        messageList.innerHTML = data.messages.map(message => `
            <div class="message-item ${message.read_status ? '' : 'unread'}" 
                 onclick="openChatWithMessage(${message.id})">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle p-2" 
                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas ${message.sender_type === 'admin' ? 'fa-user-shield' : 'fa-hotel'}"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <div class="fw-bold">${message.sender_type === 'admin' ? 'Admin' : 'System'}</div>
                        <div class="message-content">${message.message}</div>
                        <div class="message-time">${formatMessageTime(message.created_at)}</div>
                    </div>
                </div>
            </div>
        `).join('');
    })
    .catch(error => console.error('Error loading messages:', error));
}

function formatMessageTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // difference in seconds

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return date.toLocaleDateString();
}

function openChatWithMessage(messageId) {
    // Mark the message as read
    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=mark_read&message_id=${messageId}`
    })
    .then(response => response.json())
    .then(() => {
        // Open the chat window
        if (typeof toggleChatWindow === 'function') {
            toggleChatWindow();
        }
    });
}

function markAllMessagesAsRead() {
    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_all_read'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateMessageBadge();
            // Refresh message list if it's open
            if (typeof loadMessages === 'function') {
                loadMessages();
            }
        }
    });
}

// Load messages when dropdown is opened
document.addEventListener('DOMContentLoaded', function() {
    const messageDropdown = document.querySelector('.message-dropdown');
    if (messageDropdown) {
        messageDropdown.addEventListener('show.bs.dropdown', loadMessages);
    }
});

// Add this function to your existing JavaScript
function openChatWindow() {
    // Close the message dropdown
    const messageDropdown = document.querySelector('.message-dropdown .dropdown-menu');
    bootstrap.Dropdown.getInstance(messageDropdown).hide();
    
    // Call the chat window toggle function from message_box.php
    if (typeof toggleChatWindow === 'function') {
        toggleChatWindow();
    } else {
        // If chat window is not loaded yet, load it dynamically
        loadChatWindow();
    }
}

function loadChatWindow() {
    // Create a container for the chat window if it doesn't exist
    let chatContainer = document.getElementById('chatWindowContainer');
    if (!chatContainer) {
        chatContainer = document.createElement('div');
        chatContainer.id = 'chatWindowContainer';
        document.body.appendChild(chatContainer);
    }

    // Load the chat window content
    fetch('message_box.php')
        .then(response => response.text())
        .then(html => {
            chatContainer.innerHTML = html;
            // Initialize chat after loading
            if (typeof initChat === 'function') {
                initChat();
            }
            toggleChatWindow();
        });
}

// Add this to your existing JavaScript
function updateMessageCount() {
    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=check_unread'
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.getElementById('messageBadge');
        if (data.success && data.unread_count > 0) {
            badge.style.display = 'block';
            badge.textContent = data.unread_count;
        } else {
            badge.style.display = 'none';
        }
    });
}

// Add this to your existing JavaScript
function showBookingList() {
    // Show booking list functionality without login check
    // Your existing showBookingList code here
    const bookingList = JSON.parse(localStorage.getItem('bookingList')) || [];
    // Add your code to display the booking list
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    var dropdownList = dropdownElementList.map(function (dropdownToggle) {
        return new bootstrap.Dropdown(dropdownToggle, {
            display: 'static'
        });
    });

    // Handle notification clicks
    const notificationDropdown = document.querySelector('#notificationDropdownMenu');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('show.bs.dropdown', function () {
            document.body.classList.add('dropdown-open');
        });

        notificationDropdown.addEventListener('hide.bs.dropdown', function () {
            document.body.classList.remove('dropdown-open');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.notification-dropdown') && !e.target.closest('.notification-icon')) {
                const dropdown = bootstrap.Dropdown.getInstance(notificationDropdown);
                if (dropdown) {
                    dropdown.hide();
                }
            }
        });
    }

    // Handle notification items
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('unread')) {
                this.classList.remove('unread');
                updateNotificationCount();
            }
        });

        // Add touch feedback for mobile
        item.addEventListener('touchstart', function() {
            this.style.backgroundColor = '#f0f0f0';
        });

        item.addEventListener('touchend', function() {
            this.style.backgroundColor = '';
        });
    });

    // Update notification count
    function updateNotificationCount() {
        fetch('get_notification_count.php')
            .then(response => response.json())
            .then(data => {
                const badges = document.querySelectorAll('.notification-badge');
                badges.forEach(badge => {
                    if (data.count > 0) {
                        badge.style.display = 'flex';
                        badge.textContent = data.count;
                        badge.classList.add('animate');
                        setTimeout(() => badge.classList.remove('animate'), 300);
                    } else {
                        badge.style.display = 'none';
                    }
                });
            });
    }

    // Initial count update
    updateNotificationCount();

    // Update count periodically
    setInterval(updateNotificationCount, 30000);
});

// Add this to your existing JavaScript
function updateBookingBadge() {
    const bookingList = JSON.parse(localStorage.getItem('bookingList')) || [];
    const badges = document.querySelectorAll('.booking-badge');
    
    badges.forEach(badge => {
        if (bookingList.length > 0) {
            badge.textContent = bookingList.length;
            badge.classList.add('has-items');
        } else {
            badge.textContent = '0';
            badge.classList.remove('has-items');
        }
    });
}

// Call this function when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateBookingBadge();
});

// Call this function whenever the booking list changes
window.addEventListener('storage', function(e) {
    if (e.key === 'bookingList') {
        updateBookingBadge();
    }
});

// Update badge when adding/removing rooms
function addToBookingList(roomData) {
    let bookingList = JSON.parse(localStorage.getItem('bookingList')) || [];
    bookingList.push(roomData);
    localStorage.setItem('bookingList', JSON.stringify(bookingList));
    updateBookingBadge();
}

function removeFromBookingList(roomId) {
    let bookingList = JSON.parse(localStorage.getItem('bookingList')) || [];
    bookingList = bookingList.filter(item => item.id !== roomId);
    localStorage.setItem('bookingList', JSON.stringify(bookingList));
    updateBookingBadge();
}

// Function to update message badge count
function updateMessageBadge() {
    if (!document.getElementById('mobileMsgBadge') || !document.getElementById('desktopMsgBadge')) {
        return;
    }

    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=check_unread'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const count = data.unread_count;
            const mobileBadge = document.getElementById('mobileMsgBadge');
            const desktopBadge = document.getElementById('desktopMsgBadge');
            
            // Update mobile badge
            if (count > 0) {
                mobileBadge.style.display = 'flex';
                mobileBadge.textContent = count;
                mobileBadge.classList.add('badge-animation');
            } else {
                mobileBadge.style.display = 'none';
            }
            
            // Update desktop badge
            if (count > 0) {
                desktopBadge.style.display = 'flex';
                desktopBadge.textContent = count;
                desktopBadge.classList.add('badge-animation');
            } else {
                desktopBadge.style.display = 'none';
            }
            
            // Remove animation class after animation completes
            setTimeout(() => {
                mobileBadge.classList.remove('badge-animation');
                desktopBadge.classList.remove('badge-animation');
            }, 300);
        }
    })
    .catch(error => console.error('Error updating message badge:', error));
}

// Update badge when page loads
document.addEventListener('DOMContentLoaded', updateMessageBadge);

// Update badge periodically
setInterval(updateMessageBadge, 30000); // Every 30 seconds

// Function to mark all messages as read
function markAllMessagesAsRead() {
    fetch('chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_all_read'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateMessageBadge();
            // Refresh message list if it's open
            if (typeof loadMessages === 'function') {
                loadMessages();
            }
        }
    });
}

// Function to update the booking badge count
function updateNavBadge() {
    fetch('get_booking_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('bookingBadge');
            if (badge) {
                if (data.success && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                    badge.style.animation = 'badgePulse 0.5s ease-in-out';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error updating badge:', error));
}

// Update badge when page loads
document.addEventListener('DOMContentLoaded', updateNavBadge);
</script>

