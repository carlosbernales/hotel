<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale - E Akomoda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d4af37;
            --primary-dark: #856f11;
            --text-color: #333;
            --white: #ffffff;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 76px;
        }

        .container {
            max-width: 1400px;
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--white);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .brand-text {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-color) !important;
            padding: 0.5rem 1rem;
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

        .notification-icon,
        .message-icon {
            position: relative;
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
        }

        .notification-icon i,
        .message-icon i {
            font-size: 1.2rem;
            color: var(--text-color);
            transition: var(--transition);
        }

        .notification-icon:hover i,
        .message-icon:hover i {
            color: var(--primary-color);
        }

        .notification-badge,
        .message-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid #fff;
        }

        .bed-icon-container {
            position: relative;
            cursor: pointer;
            padding: 0.5rem;
        }

        .bed-icon-container .fa-bed {
            font-size: 1.2rem;
            color: var(--text-color);
            transition: var(--transition);
        }

        .bed-icon-container:hover .fa-bed {
            color: var(--primary-color);
        }

        /* Mobile Navigation Layout */
        .mobile-icons {
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0;
        }

        .mobile-icons .notification-icon,
        .mobile-icons .message-icon,
        .mobile-icons .nav-link {
            padding: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            min-height: 36px;
        }

        .mobile-icons .nav-link {
            padding: 0.4rem 0.5rem;
        }

        .mobile-profile {
            margin-left: 4px;
            display: flex;
            align-items: center;
        }

        .mobile-profile .nav-link {
            padding: 0.4rem;
            display: flex;
            align-items: center;
            min-width: 36px;
            min-height: 36px;
        }

        .mobile-profile .fa-user-circle {
            font-size: 1.3rem;
            color: var(--text-color);
        }

        .navbar-toggler {
            margin-left: 8px;
            padding: 0.4rem;
            border: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler i {
            font-size: 1.3rem;
            color: var(--primary-color);
        }

        .notification-dropdown-mobile {
            min-width: 280px !important;
        }

        .notification-dropdown-mobile .dropdown-item {
            white-space: normal;
            font-size: 0.875rem;
            padding: 0.6rem 1rem;
        }

        .notification-dropdown-mobile .dropdown-header {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        /* Mobile Category Dropdown */
        .btn-category-dropdown {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1rem;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
            transition: var(--transition);
        }

        .btn-category-dropdown:hover {
            background: var(--primary-dark);
            color: white;
        }

        .btn-category-dropdown:focus {
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }

        .category-dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
        }

        .category-dropdown-item {
            padding: 0.7rem 1.2rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .category-dropdown-item:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--primary-color);
        }

        .category-dropdown-item.active {
            background: var(--primary-color);
            color: white;
        }

        .category-dropdown-item.active:hover {
            background: var(--primary-dark);
            color: white;
        }

        .desktop-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* POS Layout */
        .pos-container {
            padding: 2rem 0;
        }

        .menu-sidebar {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 90px;
        }

        .menu-sidebar h4 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .category-btn {
            width: 100%;
            text-align: left;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            border: none;
            background: #f8f9fa;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
            cursor: pointer;
        }

        .category-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }

        .category-btn.active {
            background: var(--primary-color);
            color: white;
        }

        /* Store Hours */
        .store-hours {
            background: linear-gradient(135deg, #d4f1e8 0%, #c8e6f5 100%);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .store-hours h5 {
            color: #2c5f4f;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .store-hours p {
            margin: 0.25rem 0;
            color: #3d5a6b;
        }

        /* Menu Items */
        
        .menu-section {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.section-title {
    color: var(--text-color);
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
    font-size: 2rem;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
    gap: 1.5rem;
}

.menu-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: var(--transition);
    display: flex; /* Makes the card a flex container */
    flex-direction: column; /* Stacks image and body vertically */
    min-height: 400px; /* Set a reasonable minimum height for all cards */
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.menu-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.menu-card-body {
    padding: 1.5rem;
    text-align: center;
    flex-grow: 1; /* Allows the body to take up all remaining space in the card */
    display: flex; /* Makes the body a flex container */
    flex-direction: column; /* Stacks title, price, description, and button */
}

.menu-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.menu-card-price {
    color: var(--primary-color);
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.menu-card-description {
    min-height: 45px; 
    max-height: 45px;
    overflow: hidden; /* Hides content that exceeds max-height */
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Adjust this number to change visible lines (e.g., 3) */
    -webkit-box-orient: vertical;
    margin-bottom: 1rem; /* Space before the button */
    color: #6c757d; /* Soften the text color for descriptions */
}

.add-to-cart-btn, .not-available-btn {
    width: 100%;
    padding: 0.7rem;
    border-radius: 8px;
    font-weight: 600;
    margin-top: auto; /* Pushes the button to the bottom of the flex container (menu-card-body) */
}

.add-to-cart-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    transition: var(--transition);
    cursor: pointer;
}

.add-to-cart-btn:hover {
    background: var(--primary-dark);
    transform: scale(1.02);
}

.not-available-btn {
    background: #6c757d; /* A neutral grey color */
    color: white;
    border: none;
    cursor: not-allowed;
    opacity: 0.8;
}

.available-text {
    color: #28a745; /* Green */
}

.unavailable-text {
    color: #dc3545; /* Red */
}

        /* Order Panel */
        .order-panel {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 90px;
            height: fit-content;
        }

        .order-panel h4 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
        }

        .order-summary {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .order-total.final {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--primary-color);
        }

        .place-order-btn {
            width: 100%;
            padding: 1rem;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 1rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .place-order-btn:hover {
            background: #218838;
            transform: scale(1.02);
        }

        .empty-cart {
            text-align: center;
            color: #999;
            padding: 3rem 0;
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: #f8f9fa;
            margin-top: 4rem;
        }

        .text-gold {
            color: var(--primary-color);
        }

        .social-links a {
            color: #f8f9fa;
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #f8f9fa;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            body {
                padding-top: 70px;
            }

            .navbar {
                padding: 0.6rem 0;
            }

            .navbar .container {
                padding: 0 0.75rem;
            }

            .navbar-brand {
                font-size: 1.4rem;
            }

            .menu-sidebar,
            .order-panel {
                position: static;
                margin-bottom: 1.5rem;
            }

            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .desktop-menu {
                display: none !important;
            }

            .navbar-collapse {
                margin-top: 1rem;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 65px;
            }

            .navbar {
                padding: 0.5rem 0;
            }

            .navbar .container {
                padding: 0 0.5rem;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .store-hours {
                padding: 1rem;
            }

            .store-hours h5 {
                font-size: 1rem;
            }

            .store-hours p {
                font-size: 0.875rem;
            }

            .order-panel {
                padding: 1rem;
            }

            .btn-category-dropdown {
                font-size: 0.95rem;
                padding: 0.7rem 0.9rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 60px;
            }

            .navbar {
                padding: 0.4rem 0;
            }

            .navbar .container {
                padding: 0 0.5rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .mobile-icons {
                gap: 2px;
            }

            .mobile-icons .notification-icon,
            .mobile-icons .message-icon,
            .mobile-icons .nav-link {
                padding: 0.3rem;
                min-width: 32px;
                min-height: 32px;
            }

            .mobile-icons .notification-icon i,
            .mobile-icons .message-icon i,
            .mobile-icons .nav-link i {
                font-size: 1rem;
            }

            .mobile-profile {
                margin-left: 2px;
            }

            .mobile-profile .nav-link {
                padding: 0.3rem;
                min-width: 32px;
                min-height: 32px;
            }

            .mobile-profile .fa-user-circle {
                font-size: 1.2rem;
            }

            .navbar-toggler {
                margin-left: 4px;
                padding: 0.3rem;
            }

            .navbar-toggler i {
                font-size: 1.2rem;
            }

            .notification-badge,
            .message-badge {
                min-width: 15px;
                height: 15px;
                font-size: 9px;
                border-width: 1.5px;
            }

            .menu-card img {
                height: 180px;
            }

            .menu-card-body {
                padding: 1rem;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .btn-category-dropdown {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }

            .category-dropdown-item {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }

            .notification-dropdown-mobile {
                min-width: 260px !important;
            }
        }

        @media (max-width: 375px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .mobile-icons .notification-icon,
            .mobile-icons .message-icon,
            .mobile-icons .nav-link,
            .mobile-profile .nav-link {
                padding: 0.25rem;
                min-width: 30px;
                min-height: 30px;
            }

            .mobile-icons .notification-icon i,
            .mobile-icons .message-icon i,
            .mobile-icons .nav-link i {
                font-size: 0.95rem;
            }

            .mobile-profile .fa-user-circle {
                font-size: 1.1rem;
            }

            .navbar-toggler i {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="brand-text">E Akomoda</span>
            </a>

            <div class="mobile-icons d-lg-none">
                <button type="button" class="notification-icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">2</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown-mobile">
                    <li class="dropdown-header">Notifications</li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-check me-2 text-info"></i>Room booking confirmed</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-bag me-2 text-primary"></i>Order ready for pickup</a></li>
                </ul>
                <a href="#" class="message-icon">
                    <i class="fas fa-envelope"></i>
                    <span class="message-badge">0</span>
                </a>
                <a href="#" class="nav-link position-relative">
                    <i class="fas fa-bed"></i>
                </a>
            </div>

            <div class="mobile-profile d-lg-none">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-bookmark me-2"></i>My Bookings</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Rooms</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="cafeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Café
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="cafeDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-coffee me-2"></i>Menu & Orders</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chair me-2"></i>Table Reservation</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>

                <div class="desktop-menu d-none d-lg-flex">
                    <button type="button" class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </button>
                    <a href="#" class="message-icon">
                        <i class="fas fa-envelope"></i>
                        <span class="message-badge">0</span>
                    </a>
                    <div class="bed-icon-container">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <span>My Profile</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-bookmark me-2"></i>My Bookings</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>