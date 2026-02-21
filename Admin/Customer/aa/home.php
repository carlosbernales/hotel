<?php

// Load security helper
require_once 'includes/security_helper.php';

// For home page, we allow access but will show different content based on login status
// If user is logged in but not a customer, redirect them to their proper area
if (isLoggedIn() && getUserRole() !== 'customer') {
    $userRole = getUserRole();
    switch($userRole) {
        case 'admin':
            header('Location: /Admin/index.php?dashboard');
            break;
        case 'cashier':
            header('Location: /Admin/Cashier/index.php?pos');
            break;
        case 'frontdesk':
            header('Location: /Admin/Frontdesk/index.php?dashboard');
            break;
        default:
            header('Location: /login.php');
    }
    exit();
}

// Add this at the very beginning of index.php, before any output
require 'maintenance_config.php';

if ($maintenanceConfig->isMaintenanceMode() && !$maintenanceConfig->isAllowedIP()) {
    include 'maintenance.php';
    exit();
}

require 'db_con.php';

$userid = $_SESSION['userid'] ?? 1; 
try {
    $sql = "SELECT profile_photo FROM userss WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profilePhoto = $user && !empty($user['profile_photo']) ? $user['profile_photo'] : 'images/default.jpg';
} catch (PDOException $e) {
    $profilePhoto = 'images/default.jpg';
}

// Fetch best offers from database
try {
    $sql = "SELECT * FROM offers WHERE active = 1 ORDER BY id DESC LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $bestOffers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process each offer to ensure the image path is correct
    foreach ($bestOffers as &$offer) {
        $offer['image'] = '../../Admin/adminBackend/offers_images/' . basename($offer['image']);
        
        // Set link based on promo_type
        switch(strtolower($offer['promo_type'])) {
            case 'table':
                $offer['link'] = 'table.php';
                break;
            case 'events':
            case 'event':
                $offer['link'] = 'events.php';
                break;
            case 'room':
            default:
                $offer['link'] = 'roomss.php';
                break;
        }
    }
    unset($offer); // Unset the reference
    
} catch (PDOException $e) {
    // Log the error and set empty array if there's an error
    error_log('Error fetching offers: ' . $e->getMessage());
    $bestOffers = [];
}

// Fetch facilities from database
try {
    // Get active categories
    $sql = "SELECT * FROM facility_categories WHERE active = 1 ORDER BY display_order ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get facilities for each category
    $facilities = [];
    foreach ($categories as $category) {
        $sql = "SELECT * FROM facilities WHERE category_id = :category_id AND active = 1 ORDER BY display_order ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category['id'], PDO::PARAM_INT);
        $stmt->execute();
        $categoryFacilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($categoryFacilities)) {
            $facilities[$category['name']] = $categoryFacilities;
        }
    }
    
    // If no facilities found in database, use default hardcoded values
    if (empty($facilities)) {
        $facilities = [
            'Parking' => [
                ['name' => 'Free private parking spaces'],
                ['name' => 'Valet parking'],
                ['name' => 'Parking garage'],
                ['name' => 'Accessible parking']
            ],
            'Safety & Security' => [
                ['name' => 'Fire extinguishers'],
                ['name' => 'CCTV'],
                ['name' => 'Smoke alarms'],
                ['name' => 'Security alarm'],
                ['name' => 'Key card access'],
                ['name' => '24-hour security']
            ],
            'Food & Drink' => [
                ['name' => 'Coffee house'],
                ['name' => 'Snack bar'],
                ['name' => 'Restaurant']
            ],
            'Reception Services' => [
                ['name' => 'Private check-in/check-out'],
                ['name' => 'Luggage storage'],
                ['name' => '24-hour front desk']
            ],
            'Languages Spoken' => [
                ['name' => 'English'],
                ['name' => 'Filipino']
            ],
            'Internet' => [
                ['name' => 'Free Wi-Fi']
            ],
            'Bathroom' => [
                ['name' => 'Toilet paper'],
                ['name' => 'Bidet'],
                ['name' => 'Slippers'],
                ['name' => 'Private bathroom'],
                ['name' => 'Toilet'],
                ['name' => 'Hairdryer'],
                ['name' => 'Shower']
            ]
        ];
    }
} catch (PDOException $e) {
    // Use default hardcoded values if there's an error
    $facilities = [
        'Parking' => [
            ['name' => 'Free private parking spaces'],
            ['name' => 'Valet parking'],
            ['name' => 'Parking garage'],
            ['name' => 'Accessible parking']
        ],
        'Safety & Security' => [
            ['name' => 'Fire extinguishers'],
            ['name' => 'CCTV'],
            ['name' => 'Smoke alarms'],
            ['name' => 'Security alarm'],
            ['name' => 'Key card access'],
            ['name' => '24-hour security']
        ],
        'Food & Drink' => [
            ['name' => 'Coffee house'],
            ['name' => 'Snack bar'],
            ['name' => 'Restaurant']
        ],
        'Reception Services' => [
            ['name' => 'Private check-in/check-out'],
            ['name' => 'Luggage storage'],
            ['name' => '24-hour front desk']
        ],
        'Languages Spoken' => [
            ['name' => 'English'],
            ['name' => 'Filipino']
        ],
        'Internet' => [
            ['name' => 'Free Wi-Fi']
        ],
        'Bathroom' => [
            ['name' => 'Toilet paper'],
            ['name' => 'Bidet'],
            ['name' => 'Slippers'],
            ['name' => 'Private bathroom'],
            ['name' => 'Toilet'],
            ['name' => 'Hairdryer'],
            ['name' => 'Shower']
        ]
    ];
}

// Fetch all room types for booking dropdown
try {
    $sql = "SELECT room_type_id, room_type, price 
            FROM room_types 
            WHERE status = 'active' 
            ORDER BY room_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $allRoomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching room types: ' . $e->getMessage());
    $allRoomTypes = [];
}

// Fetch featured rooms
try {
    // Get room types with their amenities - removed availability check
    $sql = "SELECT rt.*, rt.beds, rt.description
            FROM room_types rt 
            WHERE rt.status = 'active'
            ORDER BY rt.room_type_id 
            LIMIT 3";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $featuredRooms = [];
    
    while ($room = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Get amenities for each room type
        $amenityQuery = "SELECT a.name, a.icon 
                        FROM amenities a 
                        INNER JOIN room_type_amenities rta ON a.amenity_id = rta.amenity_id 
                        WHERE rta.room_type_id = :room_type_id";
        $amenityStmt = $pdo->prepare($amenityQuery);
        $amenityStmt->bindParam(':room_type_id', $room['room_type_id'], PDO::PARAM_INT);
        $amenityStmt->execute();
        $amenities = $amenityStmt->fetchAll(PDO::FETCH_ASSOC);

        // Format amenities as features
        $features = [];
        foreach ($amenities as $amenity) {
            $features[] = $amenity['name'];
        }

        // Add bed type to features if available
        if (!empty($room['beds'])) {
            array_unshift($features, $room['beds']);
        }

        // Format the room data
        $featuredRooms[] = [
            'name' => $room['room_type'],
            'image' => !empty($room['image']) ? '../../../Admin/adminBackend/room_type_images/' . basename($room['image']) : 'images/default.jpg',
            'price' => number_format($room['price'], 0, '.', ','),
            'capacity' => $room['capacity'] . ' Guests',
            'features' => $features ?: ['Standard Amenities']
        ];
    }

    // If no rooms found in database, use default rooms
    if (empty($featuredRooms)) {
        throw new Exception("No featured rooms found");
    }

} catch (Exception $e) {
    // Use default rooms if there's an error
    error_log("Error fetching featured rooms: " . $e->getMessage());
    $featuredRooms = [
        [
            'name' => 'Deluxe Suite',
            'image' => 'images/5.jpg',
            'price' => '5,100',
            'capacity' => '5 Guests',
            'features' => ['King Bed', 'Ocean View', 'Private Balcony', 'Mini Bar']
        ],
        [
            'name' => 'Family Room',
            'image' => 'images/3.jpg',
            'price' => '4,200',
            'capacity' => '4 Guests',
            'features' => ['2 Queen Beds', 'City View', 'Living Area', 'Kitchenette']
        ],
        [
            'name' => 'Standard Double',
            'image' => 'images/double.jpg',
            'price' => '3,200',
            'capacity' => '2 Guests',
            'features' => ['Queen Bed', 'Garden View', 'Work Desk', 'En-suite Bath']
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela - Your Home Away From Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="room-availability.css">
    <style>
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('images/casa.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: fadeIn 1.5s ease-in-out;
        }

        .hero-content {
            text-align: center;
            color: white;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            backdrop-filter: blur(5px);
            padding: 40px;
        }

        .hero-title {
            font-size: 50px;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.8rem;
            margin-bottom: 2.5rem;
            font-weight: 300;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out 0.5s backwards;
        }

        .btn-custom {
            padding: 15px 40px;
            font-size: 1.2rem;
            background-color: #d4af37;
            border: 2px solid #d4af37;
            color: white;
            border-radius: 50px;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out 1s backwards;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .btn-custom:hover {
            background-color: transparent;
            color: #d4af37;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 3rem;
            }
            .hero-subtitle {
                font-size: 1.4rem;
            }
            .hero-content {
                padding: 30px;
                margin: 0 20px;
            }
        }

        /* Button size adjustments */
        .btn {
            padding: 6px 15px !important;
            font-size: 0.9rem !important;
        }

        .btn-lg {
            padding: 8px 20px !important;
            font-size: 1rem !important;
        }

        .btn-custom {
            padding: 8px 20px !important;
            font-size: 0.95rem !important;
            letter-spacing: 0.5px !important;
        }

        .btn-custom.btn-lg {
            padding: 10px 25px !important;
            font-size: 1rem !important;
        }

        .btn i {
            font-size: 0.9rem !important;
        }

        /* Keep hero section button slightly larger but still reduced */
        .hero-section .btn-custom {
            padding: 10px 30px !important;
            font-size: 1.1rem !important;
        }

        /* Adjust other section buttons */
        .best-offers .btn-custom,
        .featured-rooms .btn-custom,
        .events-tables .btn-custom {
            padding: 6px 15px !important;
            font-size: 0.9rem !important;
        }

        /* Search section button */
        #check-availability .btn-custom {
            padding: 6px 15px !important;
            font-size: 0.9rem !important;
        }

        /* Maintain proper spacing */
        .btn + .btn {
            margin-left: 5px !important;
        }

        .developers-section {
            background-color: #f8f9fa;
        }

        .developer-card {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .developer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .developer-image {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 5px solid #d4af37;
            border-radius: 50%;
        }

        .developer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .developer-info h4 {
            color: #333;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .developer-info .role {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .social-links a {
            color: #d4af37;
            margin: 0 15px;  /* Increased margin for better spacing with fewer icons */
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: #b08f2a;
        }

        @media (max-width: 768px) {
            .developer-image {
                width: 120px;
                height: 120px;
            }
        }

        /* Discount badge overlay on images */
        .discount-badge-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }
        
        .discount-end-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        
        .discount-badge-overlay .badge,
        .discount-end-overlay .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.4rem 0.6rem;
            border-radius: 50px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .discount-badge-overlay .badge {
            animation: pulse 2s infinite;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        }
        
        .discount-end-overlay .badge {
            background-color: #343a40 !important;
            box-shadow: 0 2px 8px rgba(52, 58, 64, 0.4);
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .discount-badge-overlay .badge:hover,
        .discount-end-overlay .badge:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        /* Footer styles */
        .footer {
            background-color: #1a1a1a !important;
        }

        .text-gold {
            color: #d4af37 !important;
        }

        .footer-links a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            margin-bottom: 10px;
        }

        .footer-links a:hover {
            color: #d4af37;
        }

        .footer .social-links a {
            color: #fff;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .footer .social-links a:hover {
            color: #d4af37;
        }

        .footer hr {
            opacity: 0.2;
        }

        .footer i {
            color: #d4af37;
        }

        @media (max-width: 768px) {
            .footer { 
                text-align: center;
            }

            .footer .social-links {
                justify-content: center;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
     <?php include 'message_box.php'; ?>

    <!-- Welcome Alert -->
    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
    <div id="welcomeAlert" class="alert alert-success alert-dismissible fade show" role="alert">
        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are now logged in to CASA ESTELA.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Welcome to Casa Estela</h1>
                <p class="hero-subtitle">Experience luxury and comfort in the heart of the city</p>
                <a href="#check-availability" class="btn btn-custom btn-lg">Book Your Stay</a>
            </div>
        </div>
    </section>

    <!-- Best Offers Section -->
    <section class="best-offers">
        <div class="container">
            <h2 class="section-title">Best Offers</h2>
            <div class="row">
                <?php foreach ($bestOffers as $offer): ?>
                <div class="col-md-4">
                    <div class="offer-card card">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($offer['image']); ?>" class="offer-img card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>">
                            <?php 
                            $showDiscountBadges = false;
                            $currentDate = date('Y-m-d');
                            
                            // Check if discount period is active
                            if (!empty($offer['discount_start']) && !empty($offer['discount_end'])) {
                                $startDate = date('Y-m-d', strtotime($offer['discount_start']));
                                $endDate = date('Y-m-d', strtotime($offer['discount_end']));
                                
                                if ($currentDate >= $startDate && $currentDate <= $endDate) {
                                    $showDiscountBadges = true;
                                }
                            }
                            ?>
                            
                            <?php if ($showDiscountBadges): ?>
                            <div class="discount-badge-overlay">
                                <span class="badge bg-danger text-white fs-6">
                                    <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($offer['discount']); ?> OFF
                                </span>
                            </div>
                            <div class="discount-end-overlay">
                                <span class="badge bg-dark text-white fs-6">
                                    <i class="fas fa-clock me-1"></i>Until <?php echo !empty($offer['discount_end']) ? date('M d', strtotime($offer['discount_end'])) : 'Limited'; ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?php echo htmlspecialchars($offer['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($offer['description']); ?></p>
                            <?php if (!empty($offer['price'])): ?>
                            <div class="price-display mb-2">
                                <?php 
                                $showDiscountedPrice = false;
                                $currentDate = date('Y-m-d');
                                
                                // Check if discount period is active
                                if (!empty($offer['discount_start']) && !empty($offer['discount_end'])) {
                                    $startDate = date('Y-m-d', strtotime($offer['discount_start']));
                                    $endDate = date('Y-m-d', strtotime($offer['discount_end']));
                                    
                                    if ($currentDate >= $startDate && $currentDate <= $endDate) {
                                        $showDiscountedPrice = true;
                                    }
                                }
                                ?>
                                
                                <?php if ($showDiscountedPrice): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted text-decoration-line-through">₱<?php echo number_format($offer['price'], 2); ?></span>
                                    <h4 class="text-primary mb-0">₱<?php echo number_format($offer['discounted_price'], 2); ?></h4>
                                </div>
                                <?php else: ?>
                                <h4 class="text-primary">₱<?php echo number_format($offer['price'], 2); ?></h4>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <button type="button" class="btn btn-custom view-details-btn" 
                                    data-title="<?php echo htmlspecialchars($offer['title']); ?>" 
                                    data-description="<?php echo htmlspecialchars($offer['description']); ?>" 
                                    data-price="<?php echo !empty($offer['price']) ? number_format($offer['price'], 2) : '0.00'; ?>" 
                                    data-discount="<?php echo !empty($offer['discount']) ? htmlspecialchars($offer['discount']) : ''; ?>" 
                                    data-image="<?php echo htmlspecialchars($offer['image']); ?>" 
                                    data-date="<?php echo !empty($offer['created_at']) ? date('M d, Y', strtotime($offer['created_at'])) : ''; ?>"
                                    data-link="<?php echo $offer['link']; ?>"
                                    data-promo-type="<?php echo htmlspecialchars($offer['promo_type']); ?>">
                                View Details
                            </button>
                            <button type="button" class="btn btn-success book-now-btn" 
                                    data-promo-type="<?php echo htmlspecialchars(strtolower($offer['promo_type'])); ?>"
                                    data-title="<?php echo htmlspecialchars($offer['title']); ?>"
                                    data-price="<?php echo !empty($offer['price']) ? number_format($offer['price'], 2) : '0.00'; ?>"
                                    data-discounted-price="<?php echo !empty($offer['discounted_price']) ? number_format($offer['discounted_price'], 2) : '0.00'; ?>">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Rooms -->
    <section class="featured-rooms">
        <div class="container">
            <h2 class="section-title">Featured Rooms</h2>
            <div class="row">
                <?php foreach ($featuredRooms as $room): ?>
                <div class="col-md-4">
                    <div class="room-card card">
                        <img src="<?php echo htmlspecialchars($room['image']); ?>" 
                             class="room-img card-img-top" 
                             alt="<?php echo htmlspecialchars($room['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                            <p class="room-price">₱<?php echo htmlspecialchars($room['price']); ?> / night</p>
                            <p class="text-muted">
                                <i class="fas fa-users"></i> <?php echo htmlspecialchars($room['capacity']); ?>
                            </p>
                            <ul class="room-features">
                                <?php foreach ($room['features'] as $feature): ?>
                                <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="roomss.php?type=<?php echo urlencode($room['name']); ?>" 
                               class="btn btn-custom w-100">Book Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="container section-box">
        <h3 class="section-title text-center">Casa Estela Boutique Hotel and Café Facilities</h3>
        <div class="row facilities-container">
            <div class="col-md-4">
                <?php
                // Split facilities into 3 columns
                $totalCategories = count($facilities);
                $categoriesPerColumn = ceil($totalCategories / 3);
                $categoryCount = 0;
                $columnCount = 1;
                
                foreach ($facilities as $category => $categoryFacilities):
                    $categoryCount++;
                    if ($columnCount == 1 && $categoryCount > $categoriesPerColumn) {
                        echo '</div><div class="col-md-4">';
                        $categoryCount = 1;
                        $columnCount++;
                    } elseif ($columnCount == 2 && $categoryCount > $categoriesPerColumn) {
                        echo '</div><div class="col-md-4">';
                        $categoryCount = 1;
                        $columnCount++;
                    }
                ?>
                <h5 class="facility-category"><?php echo htmlspecialchars($category); ?></h5>
                <ul class="facilities-list">
                    <?php foreach ($categoryFacilities as $facility): ?>
                    <li>
                        <?php if (!empty($facility['icon'])): ?>
                        <i class="<?php echo htmlspecialchars($facility['icon']); ?>"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($facility['name']); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Events & Tables Preview -->
    <section class="events-tables">
        <div class="container">
            <h2 class="section-title">Special Events & Dining</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="preview-card">
                        <div class="preview-icon">
                            <i class="fas fa-glass-cheers"></i>
                        </div>
                        <h3>Event Venues</h3>
                        <p>Perfect spaces for weddings, conferences, and special celebrations</p>
                        <a href="events.php" class="btn btn-custom">View Events</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="preview-card">
                        <div class="preview-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>Restaurant & Dining</h3>
                        <p>Experience exquisite dining with our world-class cuisine</p>
                        <a href="table.php" class="btn btn-custom">Reserve a Table</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Developers Section -->
    <section class="developers-section py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Our Development Team</h2>
            <div class="developers-circle">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="developer-card">
                            <div class="developer-image">
                                <img src="images/aizzy.jpeg" alt="Developer 1" class="rounded-circle">
                            </div>
                            <div class="developer-info">
                                <h4>Aizzy Villanueva</h4>
                                <p class="role">Project Manager / Technical Writer</p>
                                <div class="social-links">
                                    <a href="mailto:aizzyvillanueva43@gmail.com"><i class="fas fa-envelope"></i></a>
                                    <a href="https://www.facebook.com/itsaizzycv04"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="developer-card">
                            <div class="developer-image">
                                <img src="images/chano.jpg" alt="Developer 2" class="rounded-circle">
                            </div>
                            <div class="developer-info">
                                <h4>Christian Realisan</h4>
                                <p class="role">Frontend Developer / Backend Developer</p>
                                <div class="social-links">
                                    <a href="mailto:christianrealisan3@gmail.com"><i class="fas fa-envelope"></i></a>
                                    <a href="https://www.facebook.com/christian.realisan.2024"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="developer-card">
                            <div class="developer-image">
                                <img src="images/al.jpeg" alt="Developer 3" class="rounded-circle">
                            </div>
                            <div class="developer-info">
                                <h4>Alfred Hendrik Aceveda</h4>
                                <p class="role">Frontend Developer / Backend Developer</p>
                                <div class="social-links">
                                    <a href="mailto:alfredaceveda.3@gmail.com"><i class="fas fa-envelope"></i></a>
                                    <a href="https://www.facebook.com/alfredacevedaa"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="developer-card">
                            <div class="developer-image">
                                <img src="images/fam.jpg" alt="Developer 4" class="rounded-circle">
                            </div>
                            <div class="developer-info">
                                <h4>Fammela Nicole De Guzman</h4>
                                <p class="role">System Analyst / Technical Writer </p>
                                <div class="social-links">
                                    <a href="mailto:fammeladeguzman21@gmail.com"><i class="fas fa-envelope"></i></a>
                                    <a href="https://www.facebook.com/feneloepe.nics"><i class="fab fa-facebook"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Offer Details Modal -->
    <div class="modal fade" id="offerDetailsModal" tabindex="-1" aria-labelledby="offerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="offerDetailsModalLabel">Offer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img id="modalOfferImage" src="" class="img-fluid rounded" alt="Offer Image">
                        </div>
                        <div class="col-md-6">
                            <h4 id="modalOfferTitle"></h4>
                            <div id="modalOfferDiscount" class="mb-2"></div>
                            <p id="modalOfferDescription"></p>
                            <div class="price-display mb-3">
                                <h4 class="text-primary">₱<span id="modalOfferPrice"></span></h4>
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="fas fa-calendar-alt"></i> 
                                <span id="modalOfferDate"></span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="modalBookNowBtn" class="btn btn-success">Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Booking Modal -->
    <div class="modal fade" id="roomBookingModal" tabindex="-1" aria-labelledby="roomBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="roomBookingModalLabel">
                        <i class="fas fa-bed me-2"></i>Book Room - <span id="roomOfferTitle"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Promo Price:</strong> <span id="roomOfferPrice" class="fw-bold text-primary">₱0.00</span>
                        </div>
                    </div>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-calculator me-2"></i>
                        <div>
                            <strong>Total Amount:</strong> <span id="totalAmount" class="fw-bold text-success">₱0.00</span>
                            <small class="text-muted d-block" id="calculationDetails"></small>
                        </div>
                    </div>
                    <form id="roomBookingForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="roomCheckIn" class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control" id="roomCheckIn" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="roomCheckOut" class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control" id="roomCheckOut" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="roomGuests" class="form-label">Number of Guests</label>
                                    <select class="form-control" id="roomGuests" required>
                                        <option value="">Select Guests</option>
                                        <option value="1">1 Guest</option>
                                        <option value="2">2 Guests</option>
                                        <option value="3">3 Guests</option>
                                        <option value="4">4 Guests</option>
                                        <option value="5">5+ Guests</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="roomType" class="form-label">Room Type</label>
                                    <select class="form-control" id="roomType" required>
                                        <option value="">Select Room Type</option>
                                        <?php foreach ($allRoomTypes as $roomType): ?>
                                            <option value="<?php echo htmlspecialchars($roomType['room_type_id']); ?>" 
                                                    data-price="<?php echo htmlspecialchars($roomType['price']); ?>">
                                                <?php echo htmlspecialchars($roomType['room_type']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guestFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="guestFirstName" name="guestFirstName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guestLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="guestLastName" name="guestLastName" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guestEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="guestEmail" name="guestEmail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="guestPhone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="guestPhone" name="guestPhone" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="roomSpecialRequests" class="form-label">Special Requests</label>
                            <textarea class="form-control" id="roomSpecialRequests" rows="3" placeholder="Any special requests..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-control" id="paymentMethod" required>
                                <option value="">Select Payment Method</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="roomPaymentOption" class="form-label">Payment Option</label>
                            <select class="form-control" id="roomPaymentOption" required>
                                <option value="">Select Payment Option</option>
                                <option value="full">Full Payment</option>
                                <option value="downpayment">50% Downpayment</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitRoomBooking()">
                        <i class="fas fa-calendar-check me-2"></i>Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Reservation Modal -->
    <div class="modal fade" id="tableBookingModal" tabindex="-1" aria-labelledby="tableBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="tableBookingModalLabel">
                        <i class="fas fa-utensils me-2"></i>Reserve Table - <span id="tableOfferTitle"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Offer Price:</strong> <span id="tableOfferPrice" class="fw-bold text-success">₱0.00</span>
                        </div>
                    </div>
                    <div class="alert alert-secondary d-flex align-items-center" role="alert">
                        <i class="fas fa-calculator me-2"></i>
                        <div>
                            <strong>Total Amount:</strong> <span id="tableTotalAmount" class="fw-bold text-primary">₱0.00</span>
                        </div>
                    </div>
                    <form id="tableBookingForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tableDate" class="form-label">Reservation Date</label>
                                    <input type="date" class="form-control" id="tableDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tableTime" class="form-label">Reservation Time</label>
                                    <select class="form-control" id="tableTime" required>
                                        <option value="">Select Time</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="13:00">1:00 PM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="17:00">5:00 PM</option>
                                        <option value="18:00">6:00 PM</option>
                                        <option value="19:00">7:00 PM</option>
                                        <option value="20:00">8:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tableGuests" class="form-label">Number of Guests</label>
                                    <select class="form-control" id="tableGuests" required>
                                        <option value="">Select Guests</option>
                                        <option value="1">1 Person</option>
                                        <option value="2">2 People</option>
                                        <option value="3">3 People</option>
                                        <option value="4">4 People</option>
                                        <option value="5">5 People</option>
                                        <option value="6">6+ People</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tableOccasion" class="form-label">Occasion</label>
                                    <select class="form-control" id="tableOccasion">
                                        <option value="">Select Occasion (Optional)</option>
                                        <option value="birthday">Birthday</option>
                                        <option value="anniversary">Anniversary</option>
                                        <option value="business">Business Meeting</option>
                                        <option value="date">Date Night</option>
                                        <option value="family">Family Gathering</option>
                                        <option value="casual">Casual Dining</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tableSpecialRequests" class="form-label">Special Requests</label>
                            <textarea class="form-control" id="tableSpecialRequests" rows="3" placeholder="Dietary restrictions, seating preferences, etc..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tablePaymentMethod" class="form-label">Payment Method</label>
                            <select class="form-control" id="tablePaymentMethod" required>
                                <option value="">Select Payment Method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash on Arrival</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tablePaymentOption" class="form-label">Payment Option</label>
                            <select class="form-control" id="tablePaymentOption" required>
                                <option value="full">Full Payment</option>
                                <option value="downpayment">50% Downpayment</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="console.log('Button clicked directly!'); submitTableBooking();">
                        <i class="fas fa-calendar-plus me-2"></i>Reserve Table
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Booking Modal -->
    <div class="modal fade" id="eventBookingModal" tabindex="-1" aria-labelledby="eventBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="eventBookingModalLabel">
                        <i class="fas fa-glass-cheers me-2"></i>Book Event - <span id="eventOfferTitle"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Offer Price:</strong> <span id="eventOfferPrice" class="fw-bold text-warning">₱0.00</span>
                        </div>
                    </div>
                    <form id="eventBookingForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eventDate" class="form-label">Event Date</label>
                                    <input type="date" class="form-control" id="eventDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eventTime" class="form-label">Event Time</label>
                                    <input type="time" class="form-control" id="eventTime" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eventGuests" class="form-label">Expected Guests</label>
                                    <input type="number" class="form-control" id="eventGuests" min="10" max="500" placeholder="10-500 guests" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eventType" class="form-label">Event Type</label>
                                    <select class="form-control" id="eventType" required>
                                        <option value="">Select Event Type</option>
                                        <option value="wedding">Wedding</option>
                                        <option value="birthday">Birthday Party</option>
                                        <option value="corporate">Corporate Event</option>
                                        <option value="conference">Conference</option>
                                        <option value="reunion">Family Reunion</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="eventDetails" class="form-label">Event Details</label>
                            <textarea class="form-control" id="eventDetails" rows="3" placeholder="Please describe your event requirements..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="eventContact" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="eventContact" placeholder="Your contact number" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventPaymentMethod" class="form-label">Payment Method</label>
                            <select class="form-control" id="eventPaymentMethod" required>
                                <option value="">Select Payment Method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash on Arrival</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eventPaymentOption" class="form-label">Payment Option</label>
                            <select class="form-control" id="eventPaymentOption" required>
                                <option value="full">Full Payment</option>
                                <option value="downpayment">50% Downpayment</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="submitEventBooking()">
                        <i class="fas fa-calendar-star me-2"></i>Book Event
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    // Test if submitTableBooking function is defined
    console.log('Function test:', typeof submitTableBooking);
    
    // Global function definition - accessible to all onclick handlers
    function submitTableBooking() {
        console.log('Submit table booking called - START');
        
        try {
            const form = document.getElementById('tableBookingForm');
            console.log('Form element found:', form);
            
            if (!form) {
                console.error('Form not found!');
                return;
            }
            
            // Collect form data
            const formData = {
                tableDate: document.getElementById('tableDate').value,
                tableTime: document.getElementById('tableTime').value,
                tableGuests: document.getElementById('tableGuests').value,
                tablePaymentMethod: document.getElementById('tablePaymentMethod').value,
                tablePaymentOption: document.getElementById('tablePaymentOption').value,
                tableSpecialRequests: document.getElementById('tableSpecialRequests').value,
                tableOccasion: document.getElementById('tableOccasion').value,
                offerTitle: document.getElementById('tableOfferTitle').textContent,
                offerPrice: document.getElementById('tableOfferPrice').textContent.replace('₱', '').replace(/,/g, ''),
                totalAmount: document.getElementById('tableTotalAmount').textContent.replace('₱', '').replace(/,/g, '')
            };
            
            console.log('Collected form data:', formData);
            
            // Basic validation
            const requiredFields = ['tableDate', 'tableTime', 'tableGuests', 'tablePaymentMethod', 'tablePaymentOption'];
            let missingFields = [];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                console.log(`Field ${fieldId}:`, field, 'Value:', field ? field.value : 'NOT FOUND');
                if (!field || !field.value) {
                    missingFields.push(fieldId);
                    console.log(`Missing field: ${fieldId}`);
                }
            });
            
            console.log('Missing fields:', missingFields);
            
            if (missingFields.length > 0) {
                console.log('Showing validation warning');
                Swal.fire({
                    icon: 'warning',
                    title: 'Please fill in all required fields',
                    text: 'Please complete the reservation date, time, number of guests, payment method, and payment option.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            console.log('All fields filled, redirecting to PHP');
            
            // Create URL parameters and redirect
            const params = new URLSearchParams(formData);
            const redirectUrl = 'table_promo_xendit_payment.php?' + params.toString();
            
            console.log('Redirecting to:', redirectUrl);
            window.location.href = redirectUrl;
            
        } catch (error) {
            console.error('Error in submitTableBooking:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while processing your reservation. Please try again.',
                confirmButtonText: 'OK'
            });
        }
        
        console.log('Submit table booking called - END');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        
        // Handle View Details modal
        const viewDetailsBtns = document.querySelectorAll('.view-details-btn');
        const offerDetailsModal = new bootstrap.Modal(document.getElementById('offerDetailsModal'));
        
        viewDetailsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const title = this.dataset.title;
                const description = this.dataset.description;
                const price = this.dataset.price;
                const discount = this.dataset.discount;
                const image = this.dataset.image;
                const date = this.dataset.date;
                const link = this.dataset.link;
                
                // Populate modal with offer details
                document.getElementById('modalOfferTitle').textContent = title;
                document.getElementById('modalOfferDescription').textContent = description;
                document.getElementById('modalOfferPrice').textContent = price;
                document.getElementById('modalOfferImage').src = image;
                document.getElementById('modalOfferImage').alt = title;
                document.getElementById('modalOfferDate').textContent = date;
                
                // Handle discount badge
                const discountDiv = document.getElementById('modalOfferDiscount');
                if (discount) {
                    discountDiv.innerHTML = `<span class="badge bg-danger text-white">${discount}</span>`;
                } else {
                    discountDiv.innerHTML = '';
                }
                
                // Set Book Now button link
                const bookNowBtn = document.getElementById('modalBookNowBtn');
                bookNowBtn.onclick = function() {
                    window.location.href = link;
                };
                
                // Show modal
                offerDetailsModal.show();
            });
        });

        // Handle Book Now button clicks
        const bookNowBtns = document.querySelectorAll('.book-now-btn');
        
        bookNowBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const promoType = this.dataset.promoType;
                const title = this.dataset.title;
                const price = this.dataset.price; // Get price from data attribute
                const discountedPrice = this.dataset.discountedPrice || price; // Get discounted price, fallback to regular price
                
                // Normalize promo_type to lowercase for consistent comparison
                const normalizedPromoType = promoType ? promoType.toLowerCase() : 'room';
                
                let modal, titleElement, priceElement;
                
                switch(normalizedPromoType) {
                    case 'table':
                        modal = new bootstrap.Modal(document.getElementById('tableBookingModal'));
                        titleElement = document.getElementById('tableOfferTitle');
                        priceElement = document.getElementById('tableOfferPrice');
                        break;
                        
                    case 'events':
                    case 'event':
                        modal = new bootstrap.Modal(document.getElementById('eventBookingModal'));
                        titleElement = document.getElementById('eventOfferTitle');
                        priceElement = document.getElementById('eventOfferPrice');
                        break;
                        
                    case 'room':
                    default:
                        modal = new bootstrap.Modal(document.getElementById('roomBookingModal'));
                        titleElement = document.getElementById('roomOfferTitle');
                        priceElement = document.getElementById('roomOfferPrice');
                        break;
                }
                
                // Set the offer title in the modal
                titleElement.textContent = title;
                
                // Set the price in the modal if price element exists
                if (priceElement && price) {
                    // Use discounted price for room and table bookings, regular price for others
                    const priceToUse = (normalizedPromoType === 'room' || normalizedPromoType === 'table') ? discountedPrice : price;
                    priceElement.textContent = '₱' + priceToUse;
                }
                
                // Initialize total amount calculation for room booking
                if (normalizedPromoType === 'room') {
                    document.getElementById('totalAmount').textContent = '₱0.00';
                    document.getElementById('calculationDetails').textContent = 'Select check-in and check-out dates';
                }
                
                // Initialize total amount for table booking
                if (normalizedPromoType === 'table') {
                    document.getElementById('tableTotalAmount').textContent = '₱0.00';
                    // Set payment option to full payment by default
                    document.getElementById('tablePaymentOption').value = 'full';
                    // Call applyTablePaymentOption after a short delay to ensure the offer price is set
                    setTimeout(() => applyTablePaymentOption(), 100);
                }
                
                // Show the appropriate modal
                modal.show();
            });
        });

        function submitTableBooking() {
            console.log('Submit table booking called - START');
            
            try {
                const form = document.getElementById('tableBookingForm');
                console.log('Form element found:', form);
                
                if (!form) {
                    console.error('Form not found!');
                    return;
                }
                
                // Collect form data
                const formData = {
                    tableDate: document.getElementById('tableDate').value,
                    tableTime: document.getElementById('tableTime').value,
                    tableGuests: document.getElementById('tableGuests').value,
                    tablePaymentMethod: document.getElementById('tablePaymentMethod').value,
                    tablePaymentOption: document.getElementById('tablePaymentOption').value,
                    tableSpecialRequests: document.getElementById('tableSpecialRequests').value,
                    tableOccasion: document.getElementById('tableOccasion').value,
                    offerTitle: document.getElementById('tableOfferTitle').textContent,
                    offerPrice: document.getElementById('tableOfferPrice').textContent.replace('₱', '').replace(/,/g, ''),
                    totalAmount: document.getElementById('tableTotalAmount').textContent.replace('₱', '').replace(/,/g, '')
                };
                
                console.log('Collected form data:', formData);
                
                // Basic validation
                const requiredFields = ['tableDate', 'tableTime', 'tableGuests', 'tablePaymentMethod', 'tablePaymentOption'];
                let missingFields = [];
                
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    console.log(`Field ${fieldId}:`, field, 'Value:', field ? field.value : 'NOT FOUND');
                    if (!field || !field.value) {
                        missingFields.push(fieldId);
                        console.log(`Missing field: ${fieldId}`);
                    }
                });
                
                console.log('Missing fields:', missingFields);
                
                if (missingFields.length > 0) {
                    console.log('Showing validation warning');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please fill in all required fields',
                        text: 'Please complete the reservation date, time, number of guests, payment method, and payment option.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                console.log('All fields filled, redirecting to PHP');
                
                // Create URL parameters and redirect
                const params = new URLSearchParams(formData);
                const redirectUrl = 'table_promo_xendit_payment.php?' + params.toString();
                
                console.log('Redirecting to:', redirectUrl);
                window.location.href = redirectUrl;
                
            } catch (error) {
                console.error('Error in submitTableBooking:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your reservation. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
            
            console.log('Submit table booking called - END');
        }

        function submitEventBooking() {
            const form = document.getElementById('eventBookingForm');
            if (form.checkValidity()) {
                // Here you would typically send the data to your server
                Swal.fire({
                    icon: 'success',
                    title: 'Event Booking Confirmed!',
                    text: 'Your event booking has been successfully submitted. We will contact you soon.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('eventBookingModal')).hide();
                    form.reset();
                });
            } else {
                form.reportValidity();
            }
        }

        // Calculate total amount function
        function calculateTotalAmount() {
            const checkIn = document.getElementById('roomCheckIn').value;
            const checkOut = document.getElementById('roomCheckOut').value;
            const offerPriceElement = document.getElementById('roomOfferPrice');
            const offerPriceText = offerPriceElement.textContent.replace('₱', '').replace(',', '');
            const offerPrice = parseFloat(offerPriceText) || 0;
            let fullTotalAmount = 0;
            
            if (checkIn && checkOut && offerPrice > 0) {
                const startDate = new Date(checkIn);
                const endDate = new Date(checkOut);
                const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    fullTotalAmount = offerPrice * nights;
                    document.getElementById('calculationDetails').textContent = `${nights} night(s) × ₱${offerPrice.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                } else {
                    document.getElementById('totalAmount').textContent = '₱0.00';
                    document.getElementById('calculationDetails').textContent = 'Check-out must be after check-in';
                }
            } else {
                document.getElementById('totalAmount').textContent = '₱0.00';
                document.getElementById('calculationDetails').textContent = 'Select check-in and check-out dates';
            }
            
            // Store the full total amount for payment option calculation
            document.getElementById('totalAmount').dataset.fullAmount = fullTotalAmount;
            applyPaymentOption();
        }

        function applyPaymentOption() {
            const totalAmountElement = document.getElementById('totalAmount');
            const fullAmount = parseFloat(totalAmountElement.dataset.fullAmount) || 0;
            const paymentOptionSelect = document.getElementById('roomPaymentOption');
            const selectedOption = paymentOptionSelect.value;
            let displayAmount = fullAmount;

            if (selectedOption === 'downpayment') {
                displayAmount = fullAmount * 0.5;
            }
            // 'full' payment shows the full amount

            totalAmountElement.textContent = `₱${displayAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        function applyTablePaymentOption() {
            const tableTotalAmountElement = document.getElementById('tableTotalAmount');
            const tableOfferPriceElement = document.getElementById('tableOfferPrice');
            const offerPriceText = tableOfferPriceElement.textContent.replace('₱', '').replace(/,/g, '');
            const offerPrice = parseFloat(offerPriceText) || 0;
            const paymentOptionSelect = document.getElementById('tablePaymentOption');
            const selectedOption = paymentOptionSelect.value;
            let displayAmount = offerPrice;

            if (selectedOption === 'downpayment') {
                displayAmount = offerPrice * 0.5;
            }
            // 'full' payment shows the full amount

            tableTotalAmountElement.textContent = `₱${displayAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
        
        // Add event listeners for date changes
        document.getElementById('roomCheckIn').addEventListener('change', calculateTotalAmount);
        document.getElementById('roomCheckOut').addEventListener('change', calculateTotalAmount);
        document.getElementById('roomPaymentOption').addEventListener('change', applyPaymentOption);
        document.getElementById('tablePaymentOption').addEventListener('change', applyTablePaymentOption);
        document.getElementById('roomType').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            
            if (price) {
                console.log('Selected room price: ₱' + price);
                // Room type change doesn't affect total calculation anymore since we use offer price
                // But we still call calculateTotalAmount to refresh the display
                calculateTotalAmount();
            }
        });
        
        // Set minimum dates for room booking inputs
        const roomToday = new Date().toISOString().split('T')[0];
        const roomTomorrow = new Date();
        roomTomorrow.setDate(roomTomorrow.getDate() + 1);
        const roomTomorrowStr = roomTomorrow.toISOString().split('T')[0];
        
        document.getElementById('roomCheckIn').min = roomToday;
        document.getElementById('roomCheckOut').min = roomTomorrowStr;

        // Update checkout min date when checkin changes for room booking
        document.getElementById('roomCheckIn').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckOut = checkInDate.toISOString().split('T')[0];
            document.getElementById('roomCheckOut').min = minCheckOut;
            
            if (document.getElementById('roomCheckOut').value && document.getElementById('roomCheckOut').value < minCheckOut) {
                document.getElementById('roomCheckOut').value = '';
            }
        });
        
        // Set minimum dates for inputs
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        
        document.getElementById('checkin').min = today;
        document.getElementById('checkout').min = tomorrowStr;

        // Update checkout min date when checkin changes
        document.getElementById('checkin').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckOut = checkInDate.toISOString().split('T')[0];
            document.getElementById('checkout').min = minCheckOut;
            
            if (document.getElementById('checkout').value && document.getElementById('checkout').value < minCheckOut) {
                document.getElementById('checkout').value = '';
            }
        });

        searchBtn.addEventListener('click', async function() {
            const checkIn = document.getElementById('checkin').value;
            const checkOut = document.getElementById('checkout').value;

            if (!checkIn || !checkOut) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select both check-in and check-out dates.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading state
            const originalText = searchBtn.innerHTML;
            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

            try {
                const response = await fetch(`check_room_availability.php?check_in=${checkIn}&check_out=${checkOut}`);
                const data = await response.json();

                if (!data.success) {
                    // Show unavailable status with table
                    let alertHtml = `<div class="text-center">
                        <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                        <h5 class="mb-3">No Rooms Available</h5>
                        <p class="mb-3">${data.message}</p>`;

                    if (data.availability_info && data.availability_info.length > 0) {
                        alertHtml += `<div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Room Type</th>
                                        <th>Total</th>
                                        <th>Available</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        data.availability_info.forEach(room => {
                            const isAvailable = room.available_rooms > 0;
                            const statusClass = isAvailable ? 'text-success' : 'text-danger';
                            const statusIcon = isAvailable ? 'fas fa-check-circle' : 'fas fa-times-circle';

                            alertHtml += `
                                <tr>
                                    <td>${room.room_type}</td>
                                    <td>${room.total_rooms}</td>
                                    <td>${room.available_rooms}</td>
                                    <td class="${statusClass}">
                                        <i class="${statusIcon}"></i>
                                        ${isAvailable ? 'Available' : 'Not Available'}
                                    </td>
                                </tr>`;
                        });

                        alertHtml += `</tbody></table></div>`;
                    }

                    alertHtml += `</div>`;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Room Availability Check',
                        html: alertHtml,
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                } else {
                    // Show success with table
                    let successHtml = `<div class="text-center">
                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                        <h5 class="mb-3">Rooms Available!</h5>
                        <p class="mb-3">Check-in: ${checkIn} | Check-out: ${checkOut}</p>`;

                    if (data.availability_info && data.availability_info.length > 0) {
                        successHtml += `<div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Room Type</th>
                                        <th>Total</th>
                                        <th>Available</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        data.availability_info.forEach(room => {
                            const isAvailable = room.available_rooms > 0;
                            const statusClass = isAvailable ? 'text-success' : 'text-danger';
                            const statusIcon = isAvailable ? 'fas fa-check-circle' : 'fas fa-times-circle';

                            successHtml += `
                                <tr>
                                    <td>${room.room_type}</td>
                                    <td>${room.total_rooms}</td>
                                    <td>${room.available_rooms}</td>
                                    <td class="${statusClass}">
                                        <i class="${statusIcon}"></i>
                                        ${isAvailable ? 'Available' : 'Not Available'}
                                    </td>
                                </tr>`;
                        });

                        successHtml += `</tbody></table></div>`;
                    }

                    successHtml += `<p class="mt-3 text-muted">Click below to view and book available rooms.</p></div>`;

                    Swal.fire({
                        icon: 'success',
                        title: 'Availability Confirmed',
                        html: successHtml,
                        confirmButtonText: 'View Rooms',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        width: '600px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'roomss.php';
                        }
                    });
                }
            } catch (error) {
                console.error('Error checking availability:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to check room availability. Please try again.',
                    confirmButtonText: 'OK'
                });
            } finally {
                // Restore button state
                searchBtn.disabled = false;
                searchBtn.innerHTML = originalText;
            }
        });
    });
    </script>
    
    <!-- Global Functions -->
    <script>
        function submitRoomBooking() {
            console.log('submitRoomBooking function called');
            const form = document.getElementById('roomBookingForm');
            if (!form.checkValidity()) {
                console.log('Form validation failed');
                form.reportValidity();
                return;
            }

            const checkInDate = document.getElementById('roomCheckIn').value;
            const checkOutDate = document.getElementById('roomCheckOut').value;
            const numberOfGuests = document.getElementById('roomGuests').value;
            const roomType = document.getElementById('roomType').value;
            const specialRequests = document.getElementById('roomSpecialRequests').value;
            const guestFirstName = document.getElementById('guestFirstName').value;
            const guestLastName = document.getElementById('guestLastName').value;
            const guestEmail = document.getElementById('guestEmail').value;
            const guestPhone = document.getElementById('guestPhone').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const paymentOption = document.getElementById('roomPaymentOption').value;
            const totalAmountElement = document.getElementById('totalAmount');
            const displayAmountText = totalAmountElement.textContent.replace('₱', '').replace(/,/g, '');
            const displayAmount = parseFloat(displayAmountText);
            const fullAmount = parseFloat(totalAmountElement.dataset.fullAmount) || 0;

            console.log('Collected data:', {
                checkInDate, checkOutDate, numberOfGuests, roomType, 
                guestFirstName, guestLastName, guestEmail, guestPhone,
                paymentMethod, paymentOption, displayAmount, fullAmount
            });

            // Basic validation
            if (!checkInDate || !checkOutDate || !numberOfGuests || !roomType || 
                !guestFirstName || !guestLastName || !guestEmail || 
                !paymentMethod || !paymentOption || isNaN(displayAmount) || displayAmount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in all required booking details and ensure a valid amount is calculated.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const bookingDetails = {
                checkInDate: checkInDate,
                checkOutDate: checkOutDate,
                numberOfGuests: numberOfGuests,
                roomType: roomType,
                specialRequests: specialRequests,
                guestFirstName: guestFirstName,
                guestLastName: guestLastName,
                guestEmail: guestEmail,
                guestPhone: guestPhone,
                paymentMethod: paymentMethod,
                paymentOption: paymentOption,
                amountToPay: displayAmount, // This is the 50% or full amount
                fullBookingAmount: fullAmount, // This is the full booking amount
                offerTitle: document.getElementById('roomOfferTitle').textContent,
                offerPrice: document.getElementById('roomOfferPrice').textContent
            };

            console.log('Validation passed. About to send booking details:', bookingDetails);

            // Show loading state
            Swal.fire({
                title: 'Processing Payment...',
                text: 'Please wait while we redirect you to Xendit payment page.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('About to make fetch call to xendit_payment.php');

            // Send data to backend for Xendit payment processing
            console.log('Making fetch request...');
            fetch('xendit_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookingDetails)
            })
            .then(response => {
                console.log('Fetch response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success && data.redirect_url) {
                    // Redirect to Xendit payment page
                    window.location.href = data.redirect_url;
                } else if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Processing Error',
                        text: data.error,
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Error',
                        text: 'An unexpected error occurred during payment processing.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Payment processing error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Unable to connect to payment processor. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        }
    </script>
    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>