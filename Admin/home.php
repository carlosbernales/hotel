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

        /* Top Navbar */
        .top-navbar {
            background: linear-gradient(135deg, var(--gold) 0%, #b8941f 100%);
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        /* Sidebar */
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
            border-bottom: 1px solid rgba(255,255,255,0.1);
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

        /* Dropdown Styles */
        .sidebar-dropdown {
            position: relative;
        }

        .sidebar-dropdown > a {
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
            background: rgba(0,0,0,0.2);
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

        /* Main Content */
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-sidebar:hover {
            transform: scale(1.1);
        }

        .toggle-sidebar.shifted {
            left: calc(var(--sidebar-width) + 10px);
        }

        /* Breadcrumb */
        .breadcrumb-custom {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .breadcrumb-custom i {
            color: var(--gold);
            margin-right: 8px;
        }

        /* Card */
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .info-card h4 {
            color: #2c2c2c;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        /* Status Badges */
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

        /* DataTable Custom Styling */
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

        /* Responsive */
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
                font-size: 0.85rem;
            }

            .top-navbar .nav-icons {
                gap: 15px;
            }

            .top-navbar .nav-icons a {
                font-size: 1.1rem;
            }
        }

        /* Scrollbar */
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
            <a href="#" class="active"><i class="fas fa-users"></i> Customer Info</a>
            
            <!-- Bookings Dropdown -->
            <div class="sidebar-dropdown">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-calendar-check"></i> Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="#"><i class="fas fa-list"></i> All Bookings</a>
                    <a href="#"><i class="fas fa-plus-circle"></i> New Booking</a>
                    <a href="#"><i class="fas fa-clock"></i> Pending Bookings</a>
                    <a href="#"><i class="fas fa-check-circle"></i> Confirmed Bookings</a>
                </div>
            </div>

            <a href="#"><i class="fas fa-info-circle"></i> Booking Status</a>
            <a href="#"><i class="fas fa-utensils"></i> Table Orders</a>
            <a href="#"><i class="fas fa-sign-in-alt"></i> Checked In</a>
            <a href="#"><i class="fas fa-sign-out-alt"></i> Checked Out</a>
            
            <!-- Staff Section Dropdown -->
            <div class="sidebar-dropdown">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-user-tie"></i> Staff Section</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="#"><i class="fas fa-users-cog"></i> All Staff</a>
                    <a href="#"><i class="fas fa-user-plus"></i> Add Staff</a>
                    <a href="#"><i class="fas fa-calendar-alt"></i> Staff Schedule</a>
                    <a href="#"><i class="fas fa-tasks"></i> Attendance</a>
                </div>
            </div>

            <a href="#"><i class="fas fa-comments"></i> Feedback</a>
            <a href="#"><i class="fas fa-chart-line"></i> Sales Report</a>
            <a href="#"><i class="fas fa-cog"></i> Settings</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Breadcrumb -->
        <div class="breadcrumb-custom">
            <i class="fas fa-home"></i>
            <span>User Information</span>
        </div>

        <!-- User Information Card -->
        <div class="info-card">
            <h4>User Information</h4>
            
            <div class="table-responsive">
                <table id="userTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Carlos Bernales</td>
                            <td>carlosbernales24@gmail.com</td>
                            <td>09951776920</td>
                            <td><span class="badge badge-verified">Verified</span></td>
                        </tr>
                        <tr>
                            <td>Chan Chan</td>
                            <td>chan.christians123@gmail.com</td>
                            <td>09112222222</td>
                            <td><span class="badge badge-verified">Verified</span></td>
                        </tr>
                        <tr>
                            <td>Christian Realisan</td>
                            <td>chano@gmail.com</td>
                            <td>09123456789</td>
                            <td><span class="badge badge-verified">Verified</span></td>
                        </tr>
                        <tr>
                            <td>christian realisan Christian Realisan realisan</td>
                            <td>chanomabalo@gmail.com</td>
                            <td>09124343343</td>
                            <td><span class="badge badge-verified">Verified</span></td>
                        </tr>
                        <tr>
                            <td>Elly Mildred</td>
                            <td>ellymildred846@gmail.com</td>
                            <td>09951779200</td>
                            <td><span class="badge badge-verified">Verified</span></td>
                        </tr>
                        <tr>
                            <td>Fammela De Guzman</td>
                            <td>mysterywoman1242@gmail.com</td>
                            <td>09363960987</td>
                            <td><span class="badge badge-pending">Pending</span></td>
                        </tr>
                        <tr>
                            <td>Lab Mo</td>
                            <td>christianrealisan25@gmail.com</td>
                            <td>09123456799</td>
                            <td><span class="badge badge-verified">Verified</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#userTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "Search users:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        });

        // Sidebar Toggle
        const toggleBtn = document.getElementById('toggleBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('hidden');
            
            // On mobile, add 'show' class instead
            if (window.innerWidth <= 768) {
                if (sidebar.classList.contains('hidden')) {
                    sidebar.classList.remove('show');
                } else {
                    sidebar.classList.add('show');
                }
            }
            
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('shifted');
        });

        // Dropdown Toggle
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const allDropdowns = document.querySelectorAll('.sidebar-dropdown');
                
                // Close other dropdowns
                allDropdowns.forEach(dropdown => {
                    if (dropdown !== parent && dropdown.classList.contains('active')) {
                        dropdown.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                parent.classList.toggle('active');
            });
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = toggleBtn.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('show');
                    mainContent.classList.add('expanded');
                    toggleBtn.classList.remove('shifted');
                }
            }
        });
    </script>
</body>
</html>