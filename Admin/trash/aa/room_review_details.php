<?php
require_once 'db_con.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get room ID from URL parameter
$roomId = (int)($_GET['room_id'] ?? 0);
$roomType = $_GET['room_type'] ?? '';

// Make sure roomId is properly set for JavaScript
$jsRoomId = json_encode($roomId);

// Get room details
$room = null;
$averageRating = 0;
$totalReviews = 0;

if ($roomId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM room_types WHERE room_type_id = ?");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get average rating and review count
    $ratingStmt = $pdo->prepare("
        SELECT 
            AVG(rating) as average_rating,
            COUNT(review_id) as total_reviews
        FROM room_reviews 
        WHERE room_type_id = ?
    ");
    $ratingStmt->execute([$roomId]);
    $ratings = $ratingStmt->fetch(PDO::FETCH_ASSOC);
    
    $averageRating = $ratings['average_rating'] ? round($ratings['average_rating'], 1) : 0;
    $totalReviews = $ratings['total_reviews'];
}

// Check if user is logged in
$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? '';
?>

<div class="review-form-container">
    <!-- Reviews Section -->
    <div class="reviews-section mb-4">
        <h5 class="mb-3">
            <i class="fas fa-star text-warning"></i> 
            <?php echo $averageRating; ?> · <?php echo $totalReviews; ?> reviews
        </h5>
        
        <div id="reviewsContainer" class="reviews-container">
            <div id="reviewsList" class="reviews-list">
                <!-- Reviews will be loaded here via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading reviews...</p>
                </div>
            </div>
            <div id="noReviews" class="text-center py-4" style="display: none;">
                <i class="fas fa-comment-slash fa-2x text-muted mb-2"></i>
                <p class="text-muted">No reviews yet. Be the first to review!</p>
            </div>
        </div>
    </div>
    
    <div class="divider my-4"><span>Write a Review</span></div>
    <?php if ($room): ?>
        <div class="room-info mb-4">
            <h5 class="mb-3">Write a Review for: <?php echo htmlspecialchars($room['room_type']); ?></h5>
            <div class="room-details">
                <p class="mb-2"><strong>Room Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
                <p class="mb-2"><strong>Capacity:</strong> <?php echo $room['capacity']; ?> persons</p>
                <p class="mb-2"><strong>Price:</strong> ₱<?php echo number_format($room['price'], 2); ?></p>
            </div>
        </div>

        <?php if ($userId): ?>
            <form id="reviewForm" method="POST" action="submit_review.php">
                <input type="hidden" name="room_type_id" value="<?php echo $roomId; ?>">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                
                <div class="mb-4">
                    <label for="rating" class="form-label d-block mb-2">Rating <span class="text-danger">*</span></label>
                    <div class="rating-input">
                        <div class="star-rating" id="starRating" style="font-size: 2rem;">
                            <i class="far fa-star" data-rating="1" title="1 Star"></i>
                            <i class="far fa-star" data-rating="2" title="2 Stars"></i>
                            <i class="far fa-star" data-rating="3" title="3 Stars"></i>
                            <i class="far fa-star" data-rating="4" title="4 Stars"></i>
                            <i class="far fa-star" data-rating="5" title="5 Stars"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" value="0" required>
                        <small class="text-muted">Click to rate</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="review_text" class="form-label">Your Review <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="review_text" name="review_text" rows="5" 
                              placeholder="Share your experience with this room..." required></textarea>
                    <small class="text-muted">Minimum 20 characters</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Submit Review
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Login Required</strong><br>
                Please <a href="login.php" class="alert-link">login</a> to write a review.
            </div>
            <div class="d-flex gap-2">
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            Room not found.
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Close
        </button>
    <?php endif; ?>
</div>

<style>
.review-form-container {
    padding: 20px;
}

.room-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.star-rating {
    display: flex;
    gap: 5px;
    font-size: 24px;
    cursor: pointer;
}

/* Reviews Section */
.reviews-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1.25rem;
    border: 1px solid #e9ecef;
}

.reviews-container {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
    margin-top: 15px;
}

.review-item {
    background: white;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.review-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.review-item:not(:last-child) {
    margin-bottom: 1.25rem;
}

.pros-cons {
    font-size: 0.875rem;
    padding: 0.5rem 0;
    border-top: 1px dashed #e9ecef;
    margin-top: 0.75rem;
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 1.5rem 0;
    color: #6c757d;
    font-weight: 500;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #dee2e6;
}

.divider:not(:empty)::before {
    margin-right: 1em;
}

.divider:not(:empty)::after {
    margin-left: 1em;
}

/* Star rating */
.star-rating i {
    color: #e9ecef;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 2rem;
    margin-right: 5px;
}

.star-rating i.text-warning {
    color: #ffc107 !important;
}

.star-rating i.text-muted {
    color: #e9ecef !important;
}

.star-rating i:hover,
.star-rating i:hover ~ i {
    transform: none;
}

.star-rating i.text-muted {
    color: #e9ecef;
}

.star-rating i.hover {
    transform: scale(1.2);
}

/* Custom scrollbar for reviews */
.reviews-container::-webkit-scrollbar {
    width: 6px;
}

.reviews-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.reviews-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.reviews-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.star-rating i:hover {
    transform: scale(1.1);
}

.star-rating i.filled {
    color: #ffc107;
}

.star-rating i:not(.filled) {
    color: #ddd;
}

.rating-input {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.alert {
    border-radius: 8px;
}
</style>

<script>
// Star rating functionality
// Function to load reviews via AJAX
function loadReviews(roomId) {
    fetch(`room_reviews.php?room_type_id=${roomId}`)
        .then(response => response.json())
        .then(data => {
            const reviewsList = document.getElementById('reviewsList');
            const noReviews = document.getElementById('noReviews');
            
            if (data.success && data.reviews.length > 0) {
                reviewsList.innerHTML = ''; // Clear loading spinner
                
                data.reviews.forEach(review => {
                    const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    const stars = Array.from({length: 5}, (_, i) => 
                        `<i class="fas fa-star${i < Math.floor(review.rating) ? ' text-warning' : ' text-muted'}"></i>`
                    ).join('');
                    
                    const reviewHtml = `
                        <div class="review-item mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <strong>${review.firstname} ${review.lastname}</strong>
                                    <div class="text-warning">
                                        ${stars}
                                        <span class="text-muted ms-2">${review.rating}</span>
                                    </div>
                                </div>
                                <small class="text-muted">${reviewDate}</small>
                            </div>
                            ${review.review_title ? `<h6 class="mb-2">${review.review_title}</h6>` : ''}
                            <p class="mb-2">${review.review_text}</p>
                            ${review.pros ? `
                                <div class="pros-cons">
                                    <span class="text-success"><i class="fas fa-thumbs-up"></i> ${review.pros}</span>
                                </div>
                            ` : ''}
                            ${review.cons ? `
                                <div class="pros-cons mt-1">
                                    <span class="text-danger"><i class="fas fa-thumbs-down"></i> ${review.cons}</span>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    reviewsList.insertAdjacentHTML('beforeend', reviewHtml);
                });
                
                noReviews.style.display = 'none';
                document.getElementById('reviewsContainer').style.display = 'block';
            } else {
                reviewsList.style.display = 'none';
                noReviews.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            document.getElementById('reviewsList').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load reviews. Please try again later.
                </div>
            `;
        });
}

// Initialize star rating functionality
function initStarRating() {
    const starRating = document.querySelector('.star-rating');
    if (!starRating) {
        console.error('Star rating container not found');
        return;
    }
    
    const stars = Array.from(starRating.querySelectorAll('i'));
    const ratingInput = document.getElementById('ratingValue');
    
    if (!ratingInput) {
        console.error('Rating input not found');
        return;
    }
    
    // Set initial state
    let currentRating = parseInt(ratingInput.value) || 0;
    
    // Update star display based on current rating
    function updateStars(rating) {
        stars.forEach((star, index) => {
            const starRating = index + 1;
            if (starRating <= rating) {
                star.classList.remove('far');
                star.classList.add('fas', 'text-warning');
            } else {
                star.classList.remove('fas', 'text-warning');
                star.classList.add('far');
            }
        });
    }
    
    // Initialize stars
    updateStars(currentRating);
    
    // Add click event
    starRating.addEventListener('click', function(e) {
        const star = e.target.closest('i');
        if (!star) return;
        
        const newRating = parseInt(star.getAttribute('data-rating'));
        currentRating = newRating === currentRating ? 0 : newRating; // Toggle if clicking the same star
        ratingInput.value = currentRating;
        
        // Update star display
        updateStars(currentRating);
    });
    
    // Hover effect
    starRating.addEventListener('mouseover', function(e) {
        const star = e.target.closest('i');
        if (!star) return;
        
        const hoverRating = parseInt(star.getAttribute('data-rating'));
        
        stars.forEach((s, index) => {
            const starRating = index + 1;
            s.classList.toggle('text-warning', starRating <= hoverRating);
        });
    });
    
    // Reset on mouse leave
    starRating.addEventListener('mouseleave', function() {
        updateStars(currentRating);
    });
    
    // Initialize tooltips if using Bootstrap
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        stars.forEach(star => {
            new bootstrap.Tooltip(star, {
                title: `Rate ${star.getAttribute('data-rating')} star`,
                trigger: 'hover',
                placement: 'top'
            });
        });
    }
}

// Make roomId available globally
const roomId = <?php echo $jsRoomId; ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Load reviews automatically when the page loads
    const reviewsContainer = document.getElementById('reviewsContainer');
    if (reviewsContainer) {
        loadReviews(roomId);
    }
    
    // Initialize star rating
    initStarRating();
    
    // Form validation
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = ratingValue.value;
            const reviewText = document.getElementById('review_text').value.trim();
            
            // Validation
            if (rating === '0') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rating Required',
                    text: 'Please select a rating before submitting.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
            
            if (reviewText.length < 20) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Review Too Short',
                    text: 'Please write at least 20 characters for your review.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
            
            // Submit form
            const formData = new FormData(this);
            
            fetch('submit_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Review Submitted!',
                        text: 'Thank you for your feedback.',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Close modal and refresh if needed
                        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                        // Optionally refresh the room cards to show new rating
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to submit review. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    }
});
</script>
