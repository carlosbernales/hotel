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
    
    // Get room images
    $images = [];
    if (!empty($room['image'])) {
        $images[] = strpos($room['image'], 'http') === 0 ? $room['image'] : "../../../Admin/adminBackend/room_type_images/" . basename($room['image']);
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
        <!-- Room Image Section -->
        <div class="room-image-section">
            <div class="room-gallery">
                <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
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
                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($image); ?>" 
                                     class="d-block w-100" 
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
        
        <!-- Room Info Section -->
        <div class="room-info-section">
            <div class="room-info">
                <h2><?php echo htmlspecialchars($room['room_type']); ?></h2>
                
                <div class="price-section">
                    <span class="price">
                        ₱<?php echo number_format($room['price'], 2); ?>
                        <span class="per-night">/ night</span>
                    </span>
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
                        <h3>Room Inclusions</h3>
                        <div class="amenities-list">
                            <?php foreach ($amenities as $amenity): ?>
                                <div class="amenity-item">
                                    <?php if (!empty($amenity['icon'])): ?>
                                        <i class="<?php echo htmlspecialchars($amenity['icon']); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($amenity['name']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php
                // Get beds and amenities data from beds table
                $beds_amenities = [];
                $stmt_items = $pdo->prepare("SELECT * FROM beds WHERE available_quantity > 0 ORDER BY item_type");
                $stmt_items->execute();
                $items_result = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($items_result as $item) {
                    $beds_amenities[] = $item;
                }
                
                // Fetch all distinct item_types from the beds table
                $stmt_all_item_types = $pdo->prepare("SELECT DISTINCT item_type FROM beds ORDER BY item_type");
                $stmt_all_item_types->execute();
                $all_item_types = $stmt_all_item_types->fetchAll(PDO::FETCH_COLUMN);
                ?>

                <?php if (!empty($all_item_types)): ?>
                    <div class="all-items-section">
                        <h3>Room Amenities</h3>
                        <div class="all-items-grid">
                            <?php foreach ($all_item_types as $item_type): ?>
                                <div class="all-item-card">
                                    <div class="item-type-name">
                                        <i class="fas fa-box"></i>
                                        <?php echo htmlspecialchars($item_type); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 800px;
        margin: 0 auto;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .room-image-section {
        position: relative;
        height: 400px;
        overflow: hidden;
    }
    
    .carousel-inner {
        height: 400px;
        border-radius: 12px 12px 0 0;
    }
    
    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    .carousel-indicators {
        bottom: 15px;
    }
    
    .carousel-indicators [data-bs-target] {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        border: none;
        margin: 0 4px;
    }
    
    .carousel-indicators .active {
        background-color: #fff;
    }
    
    .room-info-section {
        padding: 0;
    }
    
    .room-info {
        padding: 25px 30px;
    }
    
    .room-info h2 {
        color: #2c3e50;
        margin: 0 0 15px 0;
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .price-section {
        margin: 0 0 20px 0;
        padding: 0 0 20px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .price {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    
    .per-night {
        font-size: 1rem;
        color: #7f8c8d;
        font-weight: 400;
    }
    
    .room-meta {
        margin: 0 0 20px 0;
        padding: 0 0 20px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .capacity {
        color: #5a6a7d;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .capacity i {
        color: #3498db;
        font-size: 0.9rem;
    }
    
    .description {
        margin: 0 0 25px 0;
        padding: 0 0 25px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .description h3 {
        color: #2c3e50;
        margin: 0 0 10px 0;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .description p {
        color: #5a6a7d;
        margin: 0;
        line-height: 1.5;
    }
    
    .room-features {
        margin: 0 0 25px 0;
        padding: 0 0 25px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .room-features h3 {
        color: #2c3e50;
        margin: 0 0 15px 0;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .amenities-list {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .amenity-item {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.9rem;
        color: #4a5568;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #e9ecef;
    }
    
    .amenity-item i {
        color: #28a745;
        font-size: 0.8rem;
    }
    
    .all-items-section {
        margin: 0;
    }
    
    .all-items-section h3 {
        color: #2c3e50;
        margin: 0 0 15px 0;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .all-items-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .all-item-card {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.9rem;
        color: #4a5568;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .all-item-card:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }
    
    .item-type-name {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .item-type-name i {
        color: #ffc107;
        font-size: 0.8rem;
    }
    
    @media (max-width: 768px) {
        .room-details {
            margin: 10px;
            border-radius: 8px;
        }
        
        .room-image-section {
            height: 300px;
        }
        
        .carousel-inner {
            height: 300px;
        }
        
        .room-info {
            padding: 20px;
        }
        
        .room-info h2 {
            font-size: 1.5rem;
        }
        
        .price {
            font-size: 1.5rem;
        }
        
        .amenities-list,
        .all-items-grid {
            gap: 8px;
        }
        
        .amenity-item,
        .all-item-card {
            font-size: 0.85rem;
            padding: 6px 10px;
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