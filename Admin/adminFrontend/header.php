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
                <a href="index.php?messages"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <div class="user-dropdown">
                    <a href="#" class="user-icon-link"><i class="fas fa-user"></i></a>
                    <div class="dropdown-menu-custom">
                        <a href="profile.php" class="dropdown-item-custom">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <a href="logout.php" class="dropdown-item-custom">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <style>
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-icon-link {
            cursor: pointer;
            color: inherit;
            text-decoration: none;
        }

        .dropdown-menu-custom {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 10px;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 4px;
            overflow: hidden;
        }

        .user-dropdown:hover .dropdown-menu-custom,
        .dropdown-menu-custom:hover {
            display: block;
        }

        .dropdown-item-custom {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-item-custom:hover {
            background-color: #f1f1f1;
            color: #333;
        }

        .dropdown-item-custom i {
            margin-right: 8px;
            width: 16px;
        }
    </style>

    <script>
        // Alternative: Click to toggle instead of hover
        document.addEventListener('DOMContentLoaded', function () {
            const userIcon = document.querySelector('.user-icon-link');
            const dropdown = document.querySelector('.dropdown-menu-custom');

            userIcon.addEventListener('click', function (e) {
                e.preventDefault();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.user-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        });
    </script>

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

            <a href="index.php?sales-report"
                class="<?php echo ($currentPage == 'sales_report.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Sales Report
            </a>

            <a href="index.php?users" class="<?php echo ($currentPage == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>


            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['rejected_room_bookings_list.php', 'pending_room_bookings_list.php', 'accepted_room_bookings_list.php', 'room_booking_list.php', 'finished_room_bookings_list.php', 'checkInDetails_room_booking.php', 'accepted_room_bookDetails.php']) ? 'active' : ''; ?>">
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

                <div class="sidebar-submenu">
                    <a href="index.php?rejected-room-bookings"
                        class="<?php echo ($currentPage == 'rejected_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-exclamation-circle"></i>
                        Rejected
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
                class="sidebar-dropdown <?php echo in_array($currentPage, ['contact_management.php', 'room_management.php', 'table_management.php', 'cafe_management.php', 'event_management.php', 'amenity_list.php', 'facilities_management.php']) ? 'active' : ''; ?>">
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
                        <i class="fas fa-calendar-check"></i> Event Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?facilities"
                        class="<?php echo ($currentPage == 'facilities_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-building"></i> Facility Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?contact_management"
                        class="<?php echo ($currentPage == 'contact_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-address-book"></i> Contact Management
                    </a>
                </div>

            </div>
        </div>
    </div>