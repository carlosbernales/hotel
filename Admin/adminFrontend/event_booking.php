<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Event Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #D4AF37;
            --gold-dark: #b8941f;
            --gold-light: #e8d089;
            --dark-bg: #1a1a1a;
            --darker-bg: #0f0f0f;
            --card-bg: #ffffff;
            --text-primary: #2c2c2c;
            --text-secondary: #666;
            --border-color: #e0e0e0;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.16);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            min-height: 100vh;
        }

        /* Top Navbar - Original Design */
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
            text-decoration: none;
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

        .main-content {
            margin-top: 50px;
            padding: 30px;
            min-height: calc(100vh - 50px);
        }

        /* Enhanced Filter Section */
        .filter-section {
            background: var(--card-bg);
            padding: 24px 28px;
            border-radius: 16px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .filter-btn {
            padding: 10px 24px;
            border-radius: 30px;
            border: 2px solid var(--border-color);
            background: white;
            color: var(--text-primary);
            font-weight: 600;
            margin: 6px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .filter-btn:hover {
            background: var(--gold-light);
            border-color: var(--gold);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: white;
            border-color: var(--gold);
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
        }

        /* Enhanced Event Package Cards */
        .event-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            /* make the card take full height of column */
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }


        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, var(--gold-light) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--gold-light);
        }

        .event-card:hover::before {
            opacity: 1;
        }

        .event-card.unavailable {
            opacity: 0.75;
        }

        /* Image Gallery Section */
        .event-image-gallery {
            position: relative;
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
        }

        .main-event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .event-card:hover .main-event-image {
            transform: scale(1.08);
        }

        .image-thumbnails {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .thumbnail-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .thumbnail-img:hover,
        .thumbnail-img.active {
            border-color: var(--gold);
            transform: scale(1.1);
        }

        .event-status-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        .status-available {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .status-unavailable {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .event-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            /* takes remaining space */
            padding: 15px;
        }

        .event-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .event-price {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--gold-dark);
            margin-bottom: 16px;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .event-price .currency {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .event-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 16px;
            line-height: 1.6;
            min-height: 60px;
        }

        .event-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
            padding: 16px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            border-left: 4px solid var(--gold);
        }

        .event-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .event-detail-item i {
            color: var(--gold);
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .event-notes {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border-left: 4px solid #ffc107;
            padding: 12px 14px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #856404;
            border-radius: 8px;
            font-weight: 500;
            line-height: 1.5;
        }

        .event-actions {
            margin-top: auto;
            /* push to bottom */
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-view-menu {
            flex: 1;
            padding: 12px;
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
            box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3);
        }

        .btn-view-menu:hover:not(:disabled) {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }

        .btn-book-now {
            flex: 2;
            padding: 12px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none;
            color: white;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);
        }

        .btn-book-now:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .btn-book-now:disabled,
        .btn-view-menu:disabled {
            background: linear-gradient(135deg, #ccc 0%, #aaa 100%);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Booking Modal */
        .booking-modal .modal-content {
            border-radius: 20px;
        }

        .booking-modal .modal-header {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            padding: 24px 28px;
            color: white;
            border-radius: 20px 20px 0 0;
        }

        .booking-modal .modal-title {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .booking-modal .modal-body {
            padding: 32px 28px;
        }

        .booking-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            border-left: 4px solid var(--gold);
        }

        .booking-summary h6 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .booking-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.95rem;
            color: var(--text-secondary);
        }

        .booking-summary-item strong {
            color: var(--text-primary);
        }

        .booking-summary .total-price {
            border-top: 2px solid var(--border-color);
            padding-top: 12px;
            margin-top: 12px;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold-dark);
        }

        .btn-confirm-booking {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-confirm-booking:hover {
            background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-cancel {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #5a6268 0%, #545b62 100%);
            transform: translateY(-2px);
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--darker-bg) 0%, var(--dark-bg) 100%);
            padding: 20px 24px;
            color: white;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.4rem;
        }

        .modal-body {
            padding: 28px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .menu-item {
            padding: 16px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            margin-bottom: 16px;
            border-left: 4px solid var(--gold);
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            transform: translateX(4px);
            box-shadow: var(--shadow-md);
        }

        .menu-item:last-child {
            margin-bottom: 0;
        }

        .menu-separator {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%);
            margin: 20px 0;
            opacity: 0.3;
        }

        .close-cart {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-cart:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        /* Cart Sidebar */
        .cart-sidebar {
            position: fixed;
            right: -420px;
            top: 50px;
            width: 420px;
            height: calc(100vh - 50px);
            background: var(--card-bg);
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1025;
            display: flex;
            flex-direction: column;
        }

        .cart-sidebar.open {
            right: 0;
        }

        .cart-header {
            padding: 24px;
            background: linear-gradient(135deg, var(--darker-bg) 0%, var(--dark-bg) 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }

        .cart-header h4 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #fafafa;
        }

        .cart-item {
            background: white;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateX(-4px);
        }

        .cart-item-info h6 {
            margin: 0 0 6px 0;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.05rem;
        }

        .cart-item-info small {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .remove-item {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .remove-item:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: scale(1.1) rotate(90deg);
        }

        .cart-footer {
            padding: 24px;
            border-top: 2px solid var(--border-color);
            background: white;
            box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.05);
        }

        .btn-checkout {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-checkout:hover:not(:disabled) {
            background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-cart i {
            font-size: 5rem;
            margin-bottom: 24px;
            color: #ddd;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                padding: 20px 16px;
            }

            .cart-sidebar {
                width: 100%;
                right: -100%;
            }

            .filter-section {
                overflow-x: auto;
                white-space: nowrap;
                padding: 16px;
            }

            .event-card {
                margin-bottom: 20px;
            }

            .event-details-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .event-card {
            animation: fadeInUp 0.5s ease backwards;
        }

        .event-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .event-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .event-card:nth-child(3) {
            animation-delay: 0.3s;
        }
    </style>
</head>

<body>
    <!-- Top Navbar (Original Design) -->
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

    <?php
    include 'adminBackend/mydb.php';

    $sql = "SELECT * FROM event_packages";
    $result = mysqli_query($conn, $sql);
    ?>

    <main class="main-content">
        <div class="position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close"
                onclick="window.location.href='../Admin/index.php?room_booking'">
            </button>

            <!-- Filter Section -->
            <div class="filter-section d-flex flex-wrap align-items-center gap-2 mb-3">
                <button class="filter-btn active" data-filter="all">All Event Packages</button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <input type="datetime-local" id="globalBookingDateTime" class="form-control"
                        style="max-width: 250px;">

                    <button class="btn btn-info me-5" id="checkGlobalAvailabilityBtn" style="width: 200px;">
                        Check Availability
                    </button>
                </div>
            </div>
        </div>

        <div class="row" id="eventsContainer">
            <?php while ($row = mysqli_fetch_assoc($result)):
                $status = strtolower($row['status']);
                $badgeClass = $status === 'available' ? 'status-available' : 'status-unavailable';
                $menuItems = array_map('trim', explode(',', $row['menu_items']));
                ?>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="event-card" data-status="<?= $status ?>">

                                <!-- Image Gallery -->
                                <div class="event-image-gallery">
                                    <img src="<?= $row['image_path'] ?>" class="main-event-image" id="mainImg<?= $row['id'] ?>">

                                    <span class="event-status-badge <?= $badgeClass ?>">
                                        <?= strtoupper($row['status']) ?>
                                    </span>

                                    <div class="image-thumbnails">
                                        <?php if ($row['image_path']): ?>
                                                    <img src="<?= $row['image_path'] ?>" class="thumbnail-img active">
                                        <?php endif; ?>
                                        <?php if ($row['image_path2']): ?>
                                                    <img src="<?= $row['image_path2'] ?>" class="thumbnail-img">
                                        <?php endif; ?>
                                        <?php if ($row['image_path3']): ?>
                                                    <img src="<?= $row['image_path3'] ?>" class="thumbnail-img">
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Event Details -->
                                <div class="event-body">
                                    <h5 class="event-name"><?= htmlspecialchars($row['name']) ?></h5>

                                    <div class="event-price">
                                        <span class="currency">₱</span>
                                        <span><?= number_format($row['price']) ?></span>
                                    </div>

                                    <p class="event-description"><?= nl2br(htmlspecialchars($row['description'])) ?></p>

                                    <div class="event-details-grid">
                                        <div class="event-detail-item">
                                            <i class="fas fa-users"></i>
                                            <span><?= $row['max_guests'] ?> Max Guests</span>
                                        </div>
                                        <div class="event-detail-item">
                                            <i class="fas fa-user-friends"></i>
                                            <span><?= $row['max_pax'] ?> Max Pax</span>
                                        </div>
                                        <div class="event-detail-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?= $row['time_limit'] ?> Hours</span>
                                        </div>
                                    </div>

                                    <div class="event-notes">
                                        <i class="fas fa-info-circle"></i>
                                        <?= nl2br(htmlspecialchars($row['notes'])) ?>
                                    </div>

                                    <div class="event-actions">
                                        <!-- Menu Modal Trigger -->
                                        <button class="btn-view-menu" data-bs-toggle="modal"
                                            data-bs-target="#menuModal<?= $row['id'] ?>">
                                            <i class="fas fa-utensils"></i> View Menu
                                        </button>

                                        <?php if ($status === 'available'): ?>
                                                    <button class="btn-book-now" data-package-id="<?= $row['id'] ?>"
                                                        data-package-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                                        data-package-price="<?= number_format($row['price']) ?>">
                                                        <i class="fas fa-calendar-check"></i> Book Now
                                                    </button>
                                        <?php endif; ?>


                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Menu Modal (PHP-rendered) -->
                        <div class="modal fade" id="menuModal<?= $row['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-utensils"></i>
                                            <?= htmlspecialchars($row['name']) ?> Menu
                                        </h5>
                                        <button type="button" class="close-cart" data-bs-dismiss="modal">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <?php if (!empty($menuItems)): ?>
                                                    <?php $count = 0;
                                                    $total = count($menuItems); ?>
                                                    <?php foreach ($menuItems as $item):
                                                        $count++; ?>
                                                                <div class="menu-item">
                                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                                    <?= htmlspecialchars($item) ?>
                                                                </div>
                                                                <?php if ($count < $total): ?>
                                                                            <div class="menu-separator"></div>
                                                                <?php endif; ?>
                                                    <?php endforeach; ?>
                                        <?php else: ?>
                                                    <p class="text-muted text-center">No menu items available.</p>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>

            <?php endwhile; ?>
        </div>
    </main>


    <!-- Booking Modal -->
    <div class="modal fade booking-modal" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check"></i> Complete Your Booking
                    </h5>
                    <button type="button" class="close-cart" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Booking Summary -->
                    <div class="booking-summary">
                        <h6><i class="fas fa-info-circle"></i> Event Details</h6>
                        <div class="booking-summary-item">
                            <span>Package:</span>
                            <strong id="bookingPackageName">-</strong>
                        </div>
                        <div class="booking-summary-item">
                            <span>Price:</span>
                            <strong id="bookingPackagePrice">₱0</strong>
                        </div>
                        <div class="booking-summary-item total-price">
                            <span>Total Amount:</span>
                            <span id="bookingTotalPrice">₱0</span>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form id="bookingForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" class="form-control" id="customerName" required
                                    placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i> Email Address *
                                </label>
                                <input type="email" class="form-control" id="customerEmail" required
                                    placeholder="your.email@example.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i> Contact Number *
                                </label>
                                <input type="tel" class="form-control" id="customerPhone" required
                                    placeholder="+63 XXX XXX XXXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Event Date *
                                </label>
                                <input type="date" class="form-control" id="eventDate" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Event Time *
                                </label>
                                <input type="time" class="form-control" id="eventTime" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-users"></i> Number of Guests *
                                </label>
                                <input type="number" class="form-control" id="guestCount" required
                                    placeholder="e.g., 50" min="1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment-alt"></i> Special Requests / Notes
                            </label>
                            <textarea class="form-control" id="specialRequests" rows="4"
                                placeholder="Any special requirements or requests for your event..."></textarea>
                        </div>

                        <button type="submit" class="btn-confirm-booking">
                            <i class="fas fa-check-circle"></i> Confirm Booking
                        </button>
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                            <i class="fas fa-times-circle"></i> Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalTitle">
                        <i class="fas fa-utensils"></i> Event Menu
                    </h5>
                    <button type="button" class="close-cart" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body" id="menuModalBody">
                    <!-- Menu items will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let availabilityChecked = false;

        const checkBtn = document.getElementById('checkGlobalAvailabilityBtn');
        const bookingDateInput = document.getElementById('globalBookingDateTime');
        const bookNowButtons = document.querySelectorAll('.btn-book-now');

        const bookingModalEl = document.getElementById('bookingModal');
        const bookingModal = new bootstrap.Modal(bookingModalEl);

        checkBtn.addEventListener('click', function () {
            const selectedDate = bookingDateInput.value;
            if (!selectedDate) {
                alert('Please select a date first.');
                return;
            }

            availabilityChecked = true;
        });

        bookNowButtons.forEach(btn => {
            btn.addEventListener('click', function (event) {
                if (!availabilityChecked) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    alert('Please select a date and check availability before booking.');
                    return;
                }

                // Populate modal with package details
                const packageName = btn.getAttribute('data-package-name');
                const packagePrice = btn.getAttribute('data-package-price');

                document.getElementById('bookingPackageName').textContent = packageName;
                document.getElementById('bookingPackagePrice').textContent = '₱' + packagePrice;
                document.getElementById('bookingTotalPrice').textContent = '₱' + packagePrice;

                bookingModal.show();
            });
        });

        bookingDateInput.addEventListener('change', function () {
            availabilityChecked = false;
        });
    </script>

</body>

</html>