<?php
$currentPage = basename($page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/header.css">
</head>

<body>
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="#"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <a href="#"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </nav>

    <button class="toggle-sidebar shifted" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://via.placeholder.com/80/D4AF37/2c2c2c?text=CE" alt="Logo">
            <h5>Admin</h5>
        </div>
        <div class="sidebar-menu">

            <a href="index.php?admin-dashboard"
                class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>

            <a href="<?php echo ($currentPage == 'customers.php') ? 'javascript:void(0)' : 'customers.php'; ?>"
                class="<?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Customer Info
            </a>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['pending_room_bookings_list.php', 'accepted_room_bookings_list.php', 'room_booking_list.php', 'finished_room_bookings_list.php', 'checkInDetails_room_booking.php', 'accepted_room_bookDetails.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-bed"></i> Room Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?pending_room_bookings_list"
                        class="<?php echo ($currentPage == 'pending_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> Pending
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?accepted_room_bookings_list"
                        class="<?php echo ($currentPage == 'accepted_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Accepted
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?room_booking_list"
                        class="<?php echo ($currentPage == 'room_booking_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> Check Ins
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?finished_room_bookings_list"
                        class="<?php echo ($currentPage == 'finished_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-double"></i> Finished
                    </a>
                </div>
            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['table_booking_accepted.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-chair"></i> Table Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?table-booking-acptd"
                        class="<?php echo ($currentPage == 'table_booking_accepted.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Accepted
                    </a>
                </div>
            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['event_accepted_booking.php', 'event_ongoing_booking.php', 'event_completed_booking.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-calendar-alt"></i> Event Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?event-acp-list"
                        class="<?php echo ($currentPage == 'event_accepted_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Accepted
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?event-ongoing"
                        class="<?php echo ($currentPage == 'event_ongoing_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-spinner"></i> Ongoing
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?event-complete-list"
                        class="<?php echo ($currentPage == 'event_completed_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-double"></i> Completed
                    </a>
                </div>
            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['room_booking.php', 'table_booking.php', 'event_booking.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-book-open"></i> Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?room_booking"
                        class="<?php echo ($currentPage == 'room_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-bed"></i> Room Booking
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?table-booking"
                        class="<?php echo ($currentPage == 'table_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chair"></i> Table Booking
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?event-booking"
                        class="<?php echo ($currentPage == 'event_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i> Event Booking
                    </a>
                </div>
            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['room_management.php', 'table_management.php', 'cafe_management.php', 'event_management.php', 'amenity_list.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-cogs"></i> Settings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?room_management"
                        class="<?php echo ($currentPage == 'room_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-bed"></i> Room Management
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?amenity_list"
                        class="<?php echo ($currentPage == 'amenity_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-concierge-bell"></i> Amenities
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?table_management"
                        class="<?php echo ($currentPage == 'table_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chair"></i> Table Management
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?cafe_management"
                        class="<?php echo ($currentPage == 'cafe_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-coffee"></i> Cafe Management
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?event_management"
                        class="<?php echo ($currentPage == 'event_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i> Event Management
                    </a>
                </div>
            </div>
        </div>
    </div>