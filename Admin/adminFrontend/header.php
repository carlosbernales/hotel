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
    <style>
        :root {
            --gold: #D4AF37;
            --dark-bg: #2c2c2c;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .top-navbar {
            background: linear-gradient(135deg, var(--gold) 0%, #b8941f 100%);
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .top-navbar .navbar-brand {
            color: #2c2c2c;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .top-navbar .nav-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-navbar .nav-icons a {
            color: #2c2c2c;
            font-size: 1.2rem;
            position: relative;
            transition: transform 0.2s;
        }

        .top-navbar .nav-icons a:hover {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .sidebar {
            position: fixed;
            top: 50px;
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - 50px);
            background: var(--dark-bg);
            transition: transform 0.3s ease;
            z-index: 1020;
            overflow-y: auto;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid var(--gold);
            margin-bottom: 10px;
        }

        .sidebar-header h5 {
            color: var(--gold);
            margin: 0;
            font-size: 1.1rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(212, 175, 55, 0.1);
            border-left-color: var(--gold);
            color: var(--gold);
        }

        .sidebar-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .sidebar-dropdown {
            position: relative;
        }

        .sidebar-dropdown>a {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-dropdown .dropdown-icon {
            transition: transform 0.3s;
            margin-left: auto;
            margin-right: 0;
        }

        .sidebar-dropdown.active .dropdown-icon {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
        }

        .sidebar-dropdown.active .sidebar-submenu {
            max-height: 500px;
        }

        .sidebar-submenu a {
            padding: 10px 25px 10px 55px;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }

        .sidebar-submenu a:hover,
        .sidebar-submenu a.active {
            background: rgba(212, 175, 55, 0.15);
            border-left-color: var(--gold);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 50px;
            padding: 30px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 50px);
            background: #f8f9fa;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .toggle-sidebar {
            position: fixed;
            top: 60px;
            left: 10px;
            z-index: 1025;
            background: var(--gold);
            border: none;
            color: #2c2c2c;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-sidebar:hover {
            transform: scale(1.1);
        }

        .toggle-sidebar.shifted {
            left: calc(var(--sidebar-width) + 10px);
        }

        .breadcrumb-custom {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb-custom i {
            color: var(--gold);
            margin-right: 8px;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .info-card h4 {
            color: #2c2c2c;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .badge-verified {
            background: #28a745;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .badge-pending {
            background: #ffc107;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #2c2c2c;
        }

        .dataTables_wrapper {
            padding: 0;
        }

        table.dataTable thead th {
            background: var(--gold);
            color: #2c2c2c;
            font-weight: 600;
            border: none;
        }

        table.dataTable tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .toggle-sidebar.shifted {
                left: 10px;
            }

            .top-navbar .navbar-brand {
                display: none;
            }

            .top-navbar>div {
                justify-content: flex-end !important;
            }

            .top-navbar .nav-icons {
                gap: 15px;
            }

            .top-navbar .nav-icons a {
                font-size: 1.1rem;
            }
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="#"><i class="fas fa-shopping-cart"></i></a>
                <a href="#"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <a href="#"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </nav>

    <!-- Toggle Sidebar Button -->
    <button class="toggle-sidebar shifted" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://via.placeholder.com/80/D4AF37/2c2c2c?text=CE" alt="Logo">
            <h5>Admin</h5>
        </div>
        <div class="sidebar-menu">
            <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

            <a href="<?php echo ($currentPage == 'customers.php') ? 'javascript:void(0)' : 'customers.php'; ?>"
                class="<?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Customer Info
            </a>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['room_types.php', 'room_number.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-user-tie"></i> Rooms</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="<?php echo ($currentPage == 'room_types.php') ? 'javascript:void(0)' : 'room_types.php'; ?>"
                        class="<?php echo ($currentPage == 'room_types.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog"></i> Room Types
                    </a>
                    <a href="<?php echo ($currentPage == 'room_number.php') ? 'javascript:void(0)' : 'room_number.php'; ?>"
                        class="<?php echo ($currentPage == 'room_number.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-plus"></i> Room Number
                    </a>
                </div>
            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['room_management.php', 'table_management.php', 'cafe_management.php', 'event_management.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-user-tie"></i> Settings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>

                <div class="sidebar-submenu">
                    <a href="index.php?room_management"
                        class="<?php echo ($currentPage == 'room_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog"></i> Room Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?table_management"
                        class="<?php echo ($currentPage == 'table_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog"></i> Table Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?cafe_management"
                        class="<?php echo ($currentPage == 'cafe_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog"></i> Cafe Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?event_management"
                        class="<?php echo ($currentPage == 'event_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog"></i> Event Management
                    </a>
                </div>
            </div>


        </div>
    </div>