<?php
require_once 'includes/db_connect.php';

header('Content-Type: text/html; charset=utf-8');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="error-message">Invalid room ID provided.</div>';
    exit;
}

$room_id = (int)$_GET['id'];

try {
    // Get room details
    $stmt = $conn->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    
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
        <div class="room-gallery mb-4">
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
                <div class="carousel-inner rounded">
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
        
        <div class="room-info">
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
                <?php if (!empty($room['rating'])): ?>
                    <span class="rating">
                        <i class="fas fa-star"></i> 
                        <?php echo number_format($room['rating'], 1); ?> 
                        (<?php echo $room['rating_count'] ?? 0; ?> reviews)
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($room['description'])): ?>
                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="room-features">
                <h3>Room Features</h3>
                <ul>
                    <li><i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['beds'] ?? 'Not specified'); ?></li>
                    <li><i class="fas fa-ruler-combined"></i> <?php echo $room['size'] ?? 'N/A'; ?> sqm</li>
                    <li><i class="fas fa-wifi"></i> Free WiFi</li>
                    <li><i class="fas fa-snowflake"></i> Air Conditioning</li>
                    <li><i class="fas fa-tv"></i> Flat-screen TV</li>
                    <li><i class="fas fa-coffee"></i> Coffee Maker</li>
                    <li><i class="fas fa-phone"></i> Telephone</li>
                    <li><i class="fas fa-shower"></i> Private Bathroom</li>
                </ul>
            </div>
            
            <div class="booking-actions">
                <button class="btn-book" 
                        onclick="bookRoom(<?php echo $room['room_type_id']; ?>, '<?php echo addslashes($room['room_type']); ?>')">
                    Book Now
                </button>
            </div>
        </div>
    </div>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fa;
    }
    
    .room-details {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .room-gallery {
        margin-bottom: 0;
    }
    
    .carousel-inner {
        border-radius: 10px 10px 0 0;
    }
    
    .carousel-item img {
        height: 400px;
        object-fit: cover;
    }
    
    .carousel-indicators {
        margin-bottom: 10px;
    }
    
    .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin: 0 5px;
        background-color: rgba(255, 255, 255, 0.5);
        border: none;
    }
    
    .carousel-indicators .active {
        background-color: #fff;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 8%;
        background: rgba(0, 0, 0, 0.2);
        opacity: 1;
    }
    
    .carousel-control-prev {
        border-radius: 10px 0 0 0;
    }
    
    .carousel-control-next {
        border-radius: 0 10px 0 0;
    }
    
    .room-info {
        padding: 30px;
    }
    
    .room-info h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-weight: 700;
    }
    
    .price-section {
        margin-bottom: 20px;
    }
    
    .price {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .per-night {
        font-size: 1rem;
        color: #7f8c8d;
    }
    
    .original-price {
        text-decoration: line-through;
        color: #95a5a6;
        margin: 0 10px;
    }
    
    .discount-badge {
        background-color: #e74c3c;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .room-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        color: #7f8c8d;
        flex-wrap: wrap;
    }
    
    .room-meta i {
        margin-right: 5px;
        color: #3498db;
    }
    
    .room-features ul {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    
    .room-features li {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4a5568;
    }
    
    .room-features i {
        color: #3498db;
        width: 20px;
        text-align: center;
    }
    
    .booking-actions {
        margin-top: 30px;
        text-align: center;
    }
    
    .btn-book {
        background: linear-gradient(45deg, #3498db, #2ecc71);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 100%;
        max-width: 300px;
    }
    
    .btn-book:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
    
    @media (max-width: 992px) {
        .room-details {
            flex-direction: column;
        }
        
        .main-image {
            height: 350px;
        }
    }
    
    @media (max-width: 576px) {
        .room-meta {
            flex-direction: column;
            gap: 10px;
        }
        
        .main-image {
            height: 250px;
        }
        
        .room-features ul {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    // Initialize thumbnail click handlers
    document.addEventListener('DOMContentLoaded', function() {
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.getElementById('main-room-image');
        
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const newSrc = this.getAttribute('data-image');
                if (newSrc && mainImage) {
                    // Update main image with fade effect
                    mainImage.style.opacity = '0';
                    setTimeout(() => {
                        mainImage.src = newSrc;
                        mainImage.style.opacity = '1';
                    }, 200);
                    
                    // Update active thumbnail
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
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