<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_con.php';

// Check if room_id is provided
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    die('<div class="alert alert-danger">Invalid room ID provided.</div>');
}

$roomId = (int)$_GET['room_id'];
$roomType = isset($_GET['room_type']) ? htmlspecialchars($_GET['room_type']) : 'this room';

// Function to get room reviews
function getRoomReviews($pdo, $roomTypeId) {
    $stmt = $pdo->prepare("
        SELECT 
            rr.*,
            u.first_name as user_name,
            u.profile_photo
        FROM room_reviews rr
        LEFT JOIN `userss` u ON rr.user_id = u.id
        WHERE rr.room_type_id = ?
        ORDER BY rr.created_at DESC
    ");
    $stmt->execute([$roomTypeId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get room reviews
$reviews = getRoomReviews($pdo, $roomId);

// Get room details for the header
$stmt = $pdo->prepare("
    SELECT 
        room_type,
        (SELECT AVG(rating) FROM room_reviews WHERE room_type_id = ?) as avg_rating,
        (SELECT COUNT(*) FROM room_reviews WHERE room_type_id = ?) as review_count
    FROM room_types 
    WHERE room_type_id = ?
");
$stmt->execute([$roomId, $roomId, $roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die('<div class="alert alert-danger">Room not found.</div>');
}

// Calculate star ratings
$avgRating = $room['avg_rating'] ? round($room['avg_rating'], 1) : 0;
$fullStars = floor($avgRating);
$hasHalfStar = $avgRating - $fullStars >= 0.5;
$emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);

// Function to get time difference in human-readable format
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    // Calculate weeks from days (fix for deprecated DateInterval::$w)
    $weeks = floor($diff->d / 7);
    $days = $diff->d - ($weeks * 7);
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    // Create a custom diff object with calculated weeks and days
    $customDiff = (object)[
        'y' => $diff->y,
        'm' => $diff->m,
        'w' => $weeks,
        'd' => $days,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s
    ];
    
    foreach ($string as $k => &$v) {
        if ($customDiff->$k) {
            $v = $customDiff->$k . ' ' . $v . ($customDiff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
// Get room reviews
$reviews = getRoomReviews($pdo, $roomId);
?>

<div class="container py-4">
    <!-- Room Header -->
    <div class="text-center mb-4">
        <h2>Reviews for <?php echo htmlspecialchars($roomType); ?></h2>
        <div class="d-flex justify-content-center align-items-center mb-3">
            <div class="me-3">
                <?php
                // Full stars
                for ($i = 0; $i < $fullStars; $i++) {
                    echo '<i class="fas fa-star text-warning" style="font-size: 1.5rem;"></i> ';
                }
                
                // Half star if needed
                if ($hasHalfStar) {
                    echo '<i class="fas fa-star-half-alt text-warning" style="font-size: 1.5rem;"></i> ';
                }
                
                // Empty stars
                for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<i class="far fa-star text-warning" style="font-size: 1.5rem;"></i> ';
                }
                ?>
            </div>
            <div>
                <span class="h4"><?php echo $avgRating; ?></span>
                <span class="text-muted">(<?php echo $room['review_count']; ?> reviews)</span>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="reviews-container">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <p class="h5">No reviews yet for this room.</p>
                <p class="text-muted">Be the first to write a review!</p>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($review['profile_photo'])): ?>
                                <img src="../../../Admin/adminBackend/user_photo/<?php echo htmlspecialchars($review['profile_photo']); ?>" 
                                     class="rounded-circle me-3" 
                                     width="50" 
                                     height="50" 
                                     alt="User" 
                                     onerror="this.src='https://via.placeholder.com/50'">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($review['user_name'] ?? 'Anonymous'); ?></h6>
                                <div class="text-warning">
                                    <?php
                                    $reviewRating = $review['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $reviewRating) {
                                            echo '<i class="fas fa-star"></i> ';
                                        } elseif (($i - 0.5) <= $reviewRating) {
                                            echo '<i class="fas fa-star-half-alt"></i> ';
                                        } else {
                                            echo '<i class="far fa-star"></i> ';
                                        }
                                    }
                                    ?>
                                </div>
                                <small class="text-muted" title="<?php echo date('F j, Y, g:i a', strtotime($review['created_at'])); ?>">
    <?php echo time_elapsed_string($review['created_at']); ?>
</small>
                            </div>
                        </div>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .reviews-container {
        max-height: 60vh;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .reviews-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .reviews-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .reviews-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .reviews-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-3px);
    }
</style>