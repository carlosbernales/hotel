<?php
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
        $offer['image'] = '/Admin/uploads/offers/' . basename($offer['image']);
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
            'image' => !empty($room['image']) ? '../../uploads/rooms/' . basename($room['image']) : 'images/default.jpg',
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
                        <img src="<?php echo htmlspecialchars($offer['image']); ?>" class="offer-img card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($offer['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($offer['description']); ?></p>
                            <?php 
                                $link = 'roomss.php';
                                if (stripos($offer['title'], 'cafe') !== false) {
                                    $link = 'cafes.php';
                                } elseif (stripos($offer['title'], 'event') !== false) {
                                    $link = 'events.php';
                                }
                            ?>
                            <a href="<?php echo $link; ?>" class="btn btn-custom">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Room Availability Search -->
    <section id="check-availability" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <h3 class="text-center mb-4">Check Room Availability</h3>
                            <form action="rooms.php" method="GET" class="row g-3" id="availability-form">
                                <div class="col-md-5">
                                    <label for="checkin" class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control" id="checkin" name="checkin" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-5">
                                    <label for="checkout" class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control" id="checkout" name="checkout" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-custom w-100">Search</button>
                                </div>
                            </form>
                            <div id="availability-results" class="mt-4" style="display: none;">
                                <!-- Results will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
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

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const availabilityForm = document.getElementById('availability-form');
        const resultsDiv = document.getElementById('availability-results');
        
        // Set minimum dates for inputs
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        document.getElementById('checkout').min = today;

        // Update checkout min date when checkin changes
        document.getElementById('checkin').addEventListener('change', function() {
            document.getElementById('checkout').min = this.value;
            if (document.getElementById('checkout').value < this.value) {
                document.getElementById('checkout').value = this.value;
            }
        });

        availabilityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            resultsDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';
            resultsDiv.style.display = 'block';

            fetch('check_availability.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = '<div class="row">';
                    if (data.data.length === 0) {
                        html = '<div class="alert alert-info">No rooms available for the selected dates.</div>';
                    } else {
                        data.data.forEach(roomType => {
                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <img src="${roomType.image}" class="card-img-top" alt="${roomType.type}" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">${roomType.type}</h5>
                                            <p class="card-text">
                                                Price: ₱${roomType.price}/night<br>
                                                Capacity: ${roomType.capacity} guests<br>
                                                Available Rooms: ${roomType.available_count}
                                            </p>
                                            <a href="roomss.php?type=${encodeURIComponent(roomType.type)}&checkin=${formData.get('checkin')}&checkout=${formData.get('checkout')}" 
                                               class="btn btn-custom">Book Now</a>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        html += '</div>';
                    }
                    resultsDiv.innerHTML = html;
                } else {
                    resultsDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                resultsDiv.innerHTML = '<div class="alert alert-danger">Error checking availability. Please try again.</div>';
                console.error('Error:', error);
            });
        });
    });
    </script>
</body>
</html>