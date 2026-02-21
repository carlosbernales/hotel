<?php
require_once 'db_con.php';

header('Content-Type: text/html; charset=utf-8');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="error-message">Invalid room ID provided.</div>';
    exit;
}

$room_id = (int)$_GET['id'];

try {
    // Get room details
    $stmt = $pdo->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$room) {
        echo '<div class="error-message">Room not found.</div>';
        exit;
    }
    
    // Check for discount
    $hasDiscount = !empty($room['discount_percent']) && 
                  strtotime($room['discount_valid_until']) >= time();
    
    if ($hasDiscount) {
        $discountedPrice = $room['price'] * (1 - ($room['discount_percent'] / 100));
        $discountedPrice = number_format($discountedPrice, 2);
    }
    
    // Get room images
    $images = [];
    if (!empty($room['image'])) {
        $images[] = strpos($room['image'], 'http') === 0 ? $room['image'] : "../../../Admin/uploads/rooms/" . basename($room['image']);
    }
    
    // Add additional images if they exist
    for ($i = 2; $i <= 3; $i++) {
        $imageField = 'image' . $i;
        if (!empty($room[$imageField])) {
            $images[] = strpos($room[$imageField], 'http') === 0 ? $room[$imageField] : "../../../Admin/uploads/rooms/" . basename($room[$imageField]);
        }
    }
    
    // If no images, use placeholder
    if (empty($images)) {
        $images[] = 'https://via.placeholder.com/800x500?text=Room+Image';
    }
    
    // Start output buffering
    ob_start();
    ?>
    
    <div class="room-details">
        <div class="row g-0">
            <!-- Left Column - Carousel -->
            <div class="col-lg-6">
                <div class="room-gallery h-100">
                    <div id="roomCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                        <!-- Indicators -->
                        <?php if (count($images) > 1): ?>
                            <div class="carousel-indicators">
                                <?php foreach ($images as $index => $image): ?>
                                    <button type="button" data-bs-target="#roomCarousel" 
                                            data-bs-slide-to="<?php echo $index; ?>" 
                                            class="<?php echo $index === 0 ? 'active' : ''; ?>">
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Slides -->
                        <div class="carousel-inner h-100">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="carousel-item h-100 <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars($image); ?>" 
                                         class="d-block w-100 h-100" 
                                         alt="<?php echo htmlspecialchars($room['room_type']); ?> - Image <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Controls -->
                        <?php if (count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Room Info -->
            <div class="col-lg-6">
                <div class="room-info h-100 d-flex flex-column">
                    <h2><?php echo htmlspecialchars($room['room_type']); ?></h2>
                    
                    <div class="price-section">
                        <span class="price">
                            ₱<?php echo $hasDiscount ? $discountedPrice : number_format($room['price'], 2); ?>
                            <span class="per-night">/ night</span>
                        </span>
                        <?php if ($hasDiscount): ?>
                            <span class="original-price">₱<?php echo number_format($room['price'], 2); ?></span>
                            <span class="discount-badge">Save <?php echo $room['discount_percent']; ?>%</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="room-meta">
                        <span class="capacity">
                            <i class="fas fa-users"></i> 
                            Capacity: <?php echo $room['capacity']; ?> persons
                        </span>
                    </div>
                    
                    <?php if (!empty($room['description'])): ?>
                        <div class="description">
                            <h3>Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    // Fetch amenities for this room type
                    $amenitiesStmt = $pdo->prepare("
                        SELECT a.name, a.icon 
                        FROM amenities a
                        JOIN room_type_amenities rta ON a.amenity_id = rta.amenity_id
                        WHERE rta.room_type_id = ?
                        ORDER BY a.name
                    ");
                    $amenitiesStmt->execute([$room_id]);
                    $amenities = $amenitiesStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($amenities)): ?>
                        <div class="room-features">
                            <h3>Room Amenities</h3>
                            <ul class="row">
                                <?php foreach ($amenities as $amenity): ?>
                                    <li class="col-md-6">
                                        <?php if (!empty($amenity['icon'])): ?>
                                            <i class="<?php echo htmlspecialchars($amenity['icon']); ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-check-circle"></i>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($amenity['name']); ?>
                                    </li>
                                <?php endforeach; ?>
                                <!-- Always show bed and size information -->
                                <li class="col-md-6">
                                    <i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['beds'] ?? 'Not specified'); ?>
                                </li>
                                <?php if (!empty($room['size'])): ?>
                                    <li class="col-md-6">
                                        <i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($room['size']); ?> sqm
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fa;
    }
    
    .room-details {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 1200px;
        margin: 20px auto;
        border: 1px solid rgba(0,0,0,0.1);
        height: auto;
        display: flex;
        flex-direction: column;
    }
    
    .room-gallery {
        margin-bottom: 0;
    }
    
    .carousel-inner {
        border-radius: 16px 0 0 16px;
        height: 100%;
        min-height: 400px;
        overflow: hidden;
    }
    
    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
    }
    
    @media (max-width: 768px) {
        .carousel-item img {
            height: 350px;
        }
    }
    
    .carousel-control-next {
        border-radius: 0 10px 0 0;
    }
    
    .room-info {
        padding: 2.5rem;
        overflow-y: auto;
        height: auto;
        max-height: 80vh;
        scrollbar-width: thin;
        scrollbar-color: #e0e0e0 transparent;
    }
    
    .room-info::-webkit-scrollbar {
        width: 6px;
    }
    
    .room-info::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .room-info::-webkit-scrollbar-thumb {
        background-color: #e0e0e0;
        border-radius: 3px;
    }
    
    @media (max-width: 768px) {
        .room-info {
            padding: 1.5rem;
        }
    }
    
    .room-meta i {
        margin-right: 5px;
        color: #3498db;
    }
    
    .room-details .row {
        margin: 0;
        height: 100%;
    }
    
    .room-details .col-lg-6 {
        padding: 0;
    }
    
    .price-section {
        margin: 1.5rem 0;
        padding: 1rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .price {
        font-size: 2.2rem;
        font-weight: 800;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 0.5rem 0;
    }
    
    .per-night {
        font-size: 1rem;
        color: #7f8c8d;
        font-weight: 500;
    }
    
    .original-price {
        text-decoration: line-through;
        color: #95a5a6;
        font-size: 1.5rem;
        font-weight: 500;
    }
    
    .discount-badge {
        background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 2px 5px rgba(255, 107, 107, 0.3);
    }
    
    .room-meta {
        display: flex;
        gap: 1.5rem;
        margin: 1.5rem 0;
        padding: 1rem 0;
        color: #5a6a7d;
        flex-wrap: wrap;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .room-features {
        margin: 1.5rem 0;
        padding: 1rem 0;
    }
    
    .room-features h3 {
        color: #2c3e50;
        margin: 0 0 1.5rem 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .room-features ul {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .room-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #4a5568;
        padding: 0.25rem 0;
        font-size: 0.9rem;
    }
    
    .room-features li::before {
        content: '✓';
        color: #2ecc71;
        font-weight: bold;
    }
    
    .room-features i {
        display: none; /* Hide the default icon since we're using checkmarks */
    }
    
    @media (max-width: 992px) {
        .carousel-inner {
            border-radius: 16px 16px 0 0;
            min-height: 400px;
            height: 50vh;
        }
        
        .room-info {
            height: auto;
            max-height: none;
        }
    }
    
    @media (max-width: 768px) {
        .carousel-inner {
            height: 300px;
        }
        
        .room-info {
            padding: 1.5rem;
        }
        
        .room-meta {
            flex-direction: column;
            gap: 10px;
            padding: 0.75rem 0;
            margin: 1rem 0;
        }
        
        .price {
            font-size: 1.8rem;
        }
        
        .room-features ul {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
    }
    
    </style>
    
    <script>
    // Function to initialize all event listeners
    function initializeEventListeners() {
        // Initialize thumbnail click handlers
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.getElementById('main-room-image');
        
        // Handle thumbnail clicks
        if (thumbnails && mainImage) {
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const newSrc = this.getAttribute('data-full');
                    mainImage.src = newSrc;
                    
                    // Update active class on thumbnails
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }
    }
    
    // Initialize all event listeners when the DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
    });
    </script>
    
    <?php
    // Get the buffered content and clean the buffer
    $content = ob_get_clean();
    echo $content;
    
} catch (Exception $e) {
    // Handle any errors
    echo '<div class="error-message">Error loading room details. Please try again later.</div>';
    error_log('Error in get_room_details.php: ' . $e->getMessage());
}
?>