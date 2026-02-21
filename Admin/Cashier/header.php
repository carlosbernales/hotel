<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard - Casa Estela Boutique Hotel & Cafe</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #b8860b;
            --primary-hover: #9a7209;
            --primary-light: rgba(184, 134, 11, 0.1);
            --primary-light-hover: rgba(184, 134, 11, 0.2);
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #1abc9c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
            padding-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Navbar Styles */
        .cashier-navbar {
            background: #b8860b;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 999;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand i {
            margin-right: 10px;
            color: white;
        }
        
        .navbar-actions {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .nav-item {
            margin-left: 20px;
            position: relative;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            position: relative;
            padding: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 40px;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        
        .nav-link i {
            font-size: 1.2rem;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            z-index: 1000;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .notification-badge.pulse {
            animation: pulse 2s ease-in-out;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }
            50% {
                transform: scale(1.2);
                box-shadow: 0 4px 8px rgba(231, 76, 60, 0.4);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
            color: inherit;
            text-decoration: none;
        }
        
        .user-profile:hover {
            background: rgba(255,255,255,0.2);
            text-decoration: none;
            color: inherit;
        }
        
        .user-profile:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
            text-transform: uppercase;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
            line-height: 1.2;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.7);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-radius: 8px;
            padding: 10px 0;
            min-width: 200px;
            background: white;
            margin-top: 10px;
            z-index: 1050;
            position: absolute;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            font-size: 0.9rem;
            color: #34495e;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
            color: var(--primary-color);
        }
        
        .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            padding-left: 25px;
        }
        
        .dropdown-item.text-danger {
            color: #e74c3c;
        }
        
        .dropdown-item.text-danger:hover {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
        }
        
        .dropdown-item.text-danger i {
            color: #e74c3c;
        }
        
        .dropdown-divider {
            border-top: 1px solid #eee;
            margin: 5px 0;
        }
        
        /* Main Content */
        .main-content {
            padding: 90px 25px 25px;
            min-height: 100vh;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .cashier-navbar {
                left: 0;
            }
            
            body {
                padding-left: 0;
            }
            
            .cashier-sidebar {
                transform: translateX(-100%);
            }
            
            .cashier-sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                padding-left: 25px;
            }
        }
        
        /* Dark theme styles */
        body.dark-theme {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        body.dark-theme .cashier-navbar {
            background: var(--navbar-bg);
        }
        
        body.dark-theme .main-content {
            background-color: var(--bg-primary);
        }
        
        body.dark-theme .dropdown-menu {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }
        
        body.dark-theme .dropdown-item {
            color: var(--text-primary);
        }
        
        body.dark-theme .dropdown-item:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        
        body.dark-theme .dropdown-header {
            color: var(--text-secondary);
        }
        
        body.dark-theme .dropdown-divider {
            border-color: var(--border-color);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="cashier-navbar">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-lg-none me-3" id="sidebarToggle" style="color: white;">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="navbar-brand">
                <i class="fas fa-cash-register"></i>
                <span>Casa Estela Botique Hotel & Cafe </span>
            </h1>
        </div>
        
        <div class="navbar-actions">
            <!-- Notifications -->
            <div class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="far fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                    <h6 class="dropdown-header">Notifications</h6>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-check-circle text-success"></i> 
                        <div>
                            <div>New payment received</div>
                            <small class="text-muted">5 minutes ago</small>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-exclamation-triangle text-warning"></i> 
                        <div>
                            <div>Shift ending in 30 minutes</div>
                            <small class="text-muted">1 hour ago</small>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-info-circle text-info"></i> 
                        <div>
                            <div>New booking requires payment</div>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center" href="#">View all notifications</a>
                </div>
            </div>
            
            <!-- Messages -->
            <div class="nav-item">
                <a class="nav-link" href="index.php?page=messages">
                    <i class="far fa-envelope"></i>
                    <span class="notification-badge" id="message-badge">0</span>
                </a>
            </div>
            
            <!-- User Profile -->
            <div class="nav-item dropdown">
                <a class="nav-link user-profile" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?php 
                        // Check if user has profile photo
                        if (isset($_SESSION['user_id']) && !empty($_SESSION['profile_photo'])) {
                            $profilePhotoPath = '../../Admin/adminBackend/user_photo/' . $_SESSION['profile_photo'];
                            if (file_exists($profilePhotoPath)) {
                                echo '<img src="' . $profilePhotoPath . '" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                            } else {
                                echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
                            }
                        } else {
                            echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
                        }
                        ?>
                    </div>
                    <div class="user-info d-none d-md-block">
                        <span class="user-name"><?php echo $_SESSION['fullname'] ?? 'Cashier'; ?></span>
                        <span class="user-role">Cashier</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="index.php?page=my_profile"><i class="fas fa-user"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="logout(); return false;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>
    

    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script>
    // Global SweetAlert2 configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    // Custom showAlert function using SweetAlert2
    function showAlert(message, type = 'info') {
        const config = {
            title: type.charAt(0).toUpperCase() + type.slice(1),
            text: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        };
        
        // Special handling for different alert types
        switch(type) {
            case 'success':
                Toast.fire({
                    icon: 'success',
                    title: message
                });
                break;
                
            case 'error':
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#dc3545'
                });
                break;
                
            case 'warning':
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: message,
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                break;
                
            case 'question':
                return Swal.fire({
                    icon: 'question',
                    title: 'Confirm',
                    text: message,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                });
                
            default:
                Toast.fire({
                    icon: type,
                    title: message
                });
        }
    }
    
    // Custom confirm dialog
    async function showConfirm(message, title = 'Are you sure?') {
        const result = await Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        });
        return result.isConfirmed;
    }
    
    // Enhanced logout function
    async function logout() {
        const result = await Swal.fire({
            title: 'Logout Confirmation',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#b8860b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                // Show loading state
                Swal.showLoading();
                
                try {
                    // Make AJAX request to logout
                    const response = await fetch('logout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'ajax_logout=true'
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        return data;
                    } else {
                        throw new Error(data.message || 'Logout failed');
                    }
                } catch (error) {
                    // If AJAX fails, fallback to direct logout
                    window.location.href = 'logout.php';
                    return false;
                }
            }
        });
        
        if (result.isConfirmed) {
            // Show success message and redirect
            await Swal.fire({
                title: 'Logged Out!',
                text: 'You have been successfully logged out.',
                icon: 'success',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false
            });
            
            // Redirect to login page
            window.location.href = '../login.php';
        }
    }
    
    // Initialize Bootstrap dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        // Try manual dropdown initialization for user profile
        const userDropdown = document.getElementById('userDropdown');
        const userDropdownMenu = userDropdown.nextElementSibling;
        
        if (userDropdown && userDropdownMenu) {
            userDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Manual dropdown toggle');
                
                // Toggle the dropdown manually
                if (userDropdownMenu.classList.contains('show')) {
                    userDropdownMenu.classList.remove('show');
                    userDropdown.setAttribute('aria-expanded', 'false');
                } else {
                    // Close other dropdowns first
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                    document.querySelectorAll('[aria-expanded="true"]').forEach(trigger => {
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                    
                    userDropdownMenu.classList.add('show');
                    userDropdown.setAttribute('aria-expanded', 'true');
                }
            });
        }
        
        // Initialize other dropdowns normally
        const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]:not(#userDropdown)'));
        const dropdownList = dropdownTriggerList.map(function (dropdownTriggerEl) {
            console.log('Initializing dropdown:', dropdownTriggerEl);
            return new bootstrap.Dropdown(dropdownTriggerEl);
        });
        
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.cashier-sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.cashier-sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('active') &&
                !event.target.closest('.dropdown')) {
                sidebar.classList.remove('active');
            }
            
            // Close dropdown when clicking outside
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = userDropdown.nextElementSibling;
            
            if (userDropdown && userDropdownMenu && 
                !userDropdown.contains(event.target) && 
                !userDropdownMenu.contains(event.target) &&
                userDropdownMenu.classList.contains('show')) {
                userDropdownMenu.classList.remove('show');
                userDropdown.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Update current time
        function updateCurrentTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('en-US', options);
        }
        
        // Update time every second
        setInterval(updateCurrentTime, 1000);
        updateCurrentTime(); // Initial call
        
        // Initialize message badge updates
        updateMessageBadge();
        // Check for new messages every 10 seconds
        setInterval(updateMessageBadge, 10000);
    });
    
    // Function to update message badge with unread count
    function updateMessageBadge() {
        fetch('get_unread_count.php')
            .then(response => response.text())
            .then(count => {
                const badge = document.getElementById('message-badge');
                if (badge) {
                    const unreadCount = parseInt(count) || 0;
                    badge.textContent = unreadCount;
                    
                    // Show/hide badge based on count
                    if (unreadCount > 0) {
                        badge.style.display = 'flex';
                        // Add pulse animation for new messages
                        if (!badge.dataset.lastCount || badge.dataset.lastCount < unreadCount) {
                            badge.classList.add('pulse');
                            setTimeout(() => badge.classList.remove('pulse'), 2000);
                        }
                    } else {
                        badge.style.display = 'none';
                    }
                    
                    badge.dataset.lastCount = unreadCount;
                }
            })
            .catch(error => {
                console.error('Error updating message badge:', error);
            });
    }
    </script>
</body>
</html>
