
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale</title>
    <!-- Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="home.php">
            <span class="brand-text">E Akomoda</span>
        </a>

        <div class="mobile-icons d-lg-none">
            <div class="dropdown">
                <button type="button" class="notification-icon" id="notificationDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">2</span>
                </button>
                <div class="dropdown-menu notification-dropdown" aria-labelledby="notificationDropdownMenu">
                    <div class="notification-header d-flex justify-content-between align-items-center p-3">
                        <h6 class="mb-0">Notifications</h6>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-link btn-sm text-muted me-2" onclick="markAllAsRead()">
                                Mark all as read
                            </button>
                            <button type="button" class="btn-close" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="notification-body">
                        <div class="notification-item unread">
                            <div class="notification-content">
                                <div class="notification-icon-wrapper text-info">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="notification-text">
                                    <p>Your room booking is confirmed!</p>
                                    <span class="notification-time">Nov 23, 2025 10:30 AM</span>
                                </div>
                            </div>
                        </div>
                        <div class="notification-item unread">
                            <div class="notification-content">
                                <div class="notification-icon-wrapper text-primary">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="notification-text">
                                    <p>Your cafe order is ready for pickup.</p>
                                    <span class="notification-time">Nov 23, 2025 11:55 AM</span>
                                </div>
                            </div>
                        </div>
                         <div class="notification-item">
                            <div class="notification-content">
                                <div class="notification-icon-wrapper text-secondary">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="notification-text">
                                    <p>Welcome to E Akomoda!</p>
                                    <span class="notification-time">Nov 22, 2025 09:00 AM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="messages.php" class="message-icon">
                <i class="fas fa-envelope"></i>
                <span class="message-badge" id="mobileMsgBadge">0</span>
            </a>
            <a href="#" class="nav-link position-relative" onclick="showBookingList(); return false;">
                <i class="fas fa-bed booking-list-icon"></i>
                <span id="bookingBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                        style="display: none;">
                    0
                </span>
            </a>
        </div>

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
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

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

            <div class="desktop-menu d-none d-lg-flex align-items-center">
                <div class="dropdown">
                    <button type="button" class="notification-icon" id="desktopNotificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </button>
                    <div class="dropdown-menu notification-dropdown" aria-labelledby="desktopNotificationDropdown">
                        <div class="notification-header d-flex justify-content-between align-items-center p-3">
                            <h6 class="mb-0">Notifications</h6>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-link btn-sm text-muted me-2" onclick="markAllAsRead()">
                                    Mark all as read
                                </button>
                                <button type="button" class="btn-close" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="notification-body">
                            <div class="notification-item unread">
                                <div class="notification-content">
                                    <div class="notification-icon-wrapper text-info">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="notification-text">
                                        <p>Your room booking is confirmed!</p>
                                        <span class="notification-time">Nov 23, 2025 10:30 AM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div class="notification-content">
                                    <div class="notification-icon-wrapper text-primary">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="notification-text">
                                        <p>Your cafe order is ready for pickup.</p>
                                        <span class="notification-time">Nov 23, 2025 11:55 AM</span>
                                    </div>
                                </div>
                            </div>
                             <div class="notification-item">
                                <div class="notification-content">
                                    <div class="notification-icon-wrapper text-secondary">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div class="notification-text">
                                        <p>Welcome to E Akomoda!</p>
                                        <span class="notification-time">Nov 22, 2025 09:00 AM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown message-dropdown">
                    <a href="messages.php" class="message-icon">
                        <i class="fas fa-envelope"></i>
                        <span class="message-badge" id="desktopMsgBadge">0</span>
                    </a>
                </div>
                
                <div class="bed-icon-container" onclick="showBookingList()">
                    <i class="fas fa-bed"></i>
                    <span class="booking-badge" style="display: none;">0</span>
                </div>
                
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
        </div>
    </div>
</nav>


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


<footer class="footer bg-dark text-light py-5">
    <div class="container">
        <div class="row">
            <!-- Hotel Info -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-gold mb-3">Casa Estela</h5>
                <p>Your luxurious home away from home in the heart of the city. Experience comfort, elegance, and exceptional service.</p>
                <div class="social-links mt-3">
                    <a href="https://www.facebook.com/casaestelahotelcafe?_rdc=1&_rdr#" class="me-3"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/accounts/login/?next=%2Fcasaestelahotelcafe%2F&source=omni_redirect" class="me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-gold mb-3">Quick Links</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="roomss.php">Our Rooms</a></li>
                    <li><a href="events.php">Events & Celebrations</a></li>
                    <li><a href="cafes.php">Cafe</a></li>
                    <li><a href="table.php">Table Reservation</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-gold mb-3">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Gov. B Marasigan St., Libis Calapan City 
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        0908-7474-892 / 043-441-6924
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        casaestelahotelcafe@gmail.com
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row mt-4">
            <div class="col-12">
                <hr class="bg-light">
                <p class="text-center mb-0">
                    &copy; <?php echo date('Y'); ?> Casa Estela. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
