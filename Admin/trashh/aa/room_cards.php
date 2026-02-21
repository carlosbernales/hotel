<?php
require_once 'db_con.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get room ratings and review count
function getRoomRatings($pdo, $roomTypeId) {
    $stmt = $pdo->prepare("
        SELECT 
            AVG(rating) as average_rating,
            COUNT(*) as review_count
        FROM room_reviews 
        WHERE room_type_id = ?
    ");
    $stmt->execute([$roomTypeId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Check if we have availability data from the search
$hasSearched = !empty($_SESSION['room_availability']);
$rooms = $hasSearched ? $_SESSION['room_availability']['rooms'] : [];
$checkin = $hasSearched ? $_SESSION['room_availability']['checkin'] : '';
$checkout = $hasSearched ? $_SESSION['room_availability']['checkout'] : '';

// If no search was performed, get all active rooms
if (!$hasSearched) {
    $sql = "SELECT 
                rt.room_type_id,
                rt.room_type,
                rt.price,
                rt.capacity,
                rt.beds,
                rt.description,
                rt.image,
                rt.image2,
                rt.image3,
                rt.discount_percent,
                rt.discount_valid_until,
                COUNT(DISTINCT rn.room_number_id) as total_rooms,
                0 as available_rooms
            FROM room_types rt
            LEFT JOIN room_numbers rn ON rt.room_type_id = rn.room_type_id AND rn.status = 'active'
            WHERE rt.status = 'active'
            GROUP BY rt.room_type_id";
    
    $stmt = $pdo->query($sql);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set default availability text
    foreach ($rooms as &$room) {
        $room['is_available'] = false;
        $room['availability_text'] = 'Check availability';
    }
    unset($room);
}
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
   <style>
.room-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    padding: 20px 0;
}

.room-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.room-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.room-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.room-content {
    padding: 20px;
}

.room-content h3 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 1.4rem;
}

.room-description {
    color: #7f8c8d;
    font-size: 0.95rem;
    margin: 10px 0;
    min-height: 60px;
}

.room-features {
    display: flex;
    gap: 15px;
    margin: 15px 0;
    color: #7f8c8d;
    font-size: 0.9rem;
}

.room-features i {
    margin-right: 5px;
    color: #3498db;
}

.room-price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin: 15px 0;
}

.room-price small {
    font-size: 0.8rem;
    color: #7f8c8d;
}

.room-availability {
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.badge {
    font-size: 0.85em;
    padding: 5px 10px;
    border-radius: 4px;
}

.badge-success {
    background-color: #d4edda;
    color: #155724;
}

.badge-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.badge-warning {
    background-color: #fff3cd;
    color: #856404;
}

.btn {
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #3498db;
    border-color: #3498db;
}

.btn-primary:hover {
    background-color: #2980b9;
    border-color: #2980b9;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.room-unavailable {
    opacity: 0.7;
}

.room-unavailable .room-image {
    filter: grayscale(70%);
}

.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}

.search-summary {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    grid-column: 1 / -1;
}

.search-summary h4 {
    margin-top: 0;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .room-container {
        grid-template-columns: 1fr;
    }
}
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            padding: 40px 0;
            color: #2d3436;
        }
        .room-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .room-card {
            background: #fff;
            width: 350px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 35px;
            position: relative;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        .room-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: transform 0.5s ease;
            position: relative;
            border-radius: 16px 16px 0 0;
        }
        .room-card:hover .room-image {
            transform: scale(1.03);
        }
        .room-content {
            padding: 25px;
            position: relative;
        }
        .room-content h3 {
            margin: 0 0 12px;
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .price span:first-child {
            background: linear-gradient(45deg, #3498db, #2ecc71);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .discount-badge {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(255, 107, 107, 0.3);
            position: absolute;
            top: -15px;
            right: 25px;
            z-index: 2;
        }
        .original-price {
            text-decoration: line-through;
            color: #95a5a6;
            font-size: 1rem;
            font-weight: 500;
        }
        .room-features {
            list-style: none;
            padding: 0;
            margin: 20px 0 25px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .room-features li {
            padding: 8px 0;
            color: #4a5568;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border-radius: 6px;
            padding-left: 8px;
        }
        .room-features li:hover {
            background: #f8f9fa;
            transform: translateX(3px);
        }
        .room-features li i {
            color: #3498db;
            margin-right: 8px;
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-view-details, .btn-book {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-view-details {
            background: #f8f0e0;
            color: #d59a07;
            border: 1px solid #d59a07;
            transition: all 0.3s ease;
        }
        
        .btn-view-details:hover {
            background: #d59a07;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-book {
            background: #d59a07;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 4px 15px rgba(213, 154, 7, 0.3);
            transition: all 0.3s ease;
        }
        .btn-book:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #d59a07, #f0b733);
            transition: all 0.4s ease;
            z-index: -1;
        }
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(213, 154, 7, 0.4);
        }
        .btn-book:hover:before {
            left: 0;
        }
        .rating {
            color: #f39c12;
            margin-bottom: 10px;
        }
        .capacity {
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            font-size: 0.85rem;
            color: #4a5568;
            margin: 5px 0 15px;
        }
        .capacity i {
            color: #3498db;
            margin-right: 5px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .room-card {
                width: 100%;
                max-width: 400px;
                margin-left: auto;
                margin-right: auto;
            }
            .room-features {
                grid-template-columns: 1fr;
            }
        }
</style>
</head>
<?php

$roomAvailability = $_SESSION['room_availability']['rooms'] ?? [];
$hasSearched = !empty($_SESSION['room_availability']);
?>

<div class="container">
    <h1 class="text-center mb-5">Our Rooms & Suites</h1>
    
    <?php if ($hasSearched): ?>
        <div class="alert alert-info mb-4">
            Showing availability for <strong><?= date('M d, Y', strtotime($_SESSION['room_availability']['checkin'])) ?></strong> to 
            <strong><?= date('M d, Y', strtotime($_SESSION['room_availability']['checkout'])) ?></strong>
            <a href="?reset=1" class="btn btn-sm btn-outline-secondary ms-3">Clear Search</a>
        </div>
    <?php endif; ?>
    
    <div class="room-container" id="roomContainer">
        <?php if (!$hasSearched): ?>
            <div class="text-center py-5">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Please select check-in and check-out dates to see room availability.
                </div>
            </div>
        <?php endif; ?>
            <?php 
            // Only show rooms if search has been performed
            if ($hasSearched) {
                // Debug logging
                error_log("=== ROOM CARDS DEBUG ===");
                error_log("Total rooms before filtering: " . count($rooms));
                
                // Filter out rooms with no availability
                $availableRooms = array_filter($rooms, function($room) {
                    $hasAvailability = ($room['available_rooms'] ?? 0) > 0;
                    error_log(sprintf(
                        "Room %s - Available: %d, Has Availability: %s",
                        $room['room_type'] ?? 'Unknown',
                        $room['available_rooms'] ?? 0,
                        $hasAvailability ? 'Yes' : 'No'
                    ));
                    return $hasAvailability;
                });
                error_log("Available rooms after filtering: " . count($availableRooms));

                if (empty($availableRooms)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No rooms available for the selected dates. Please try different dates.
                        </div>
                    </div>
                <?php 
                else: 
                    foreach ($availableRooms as $room): 
                        $isAvailable = true; // All rooms in this list are available
                        $availableCount = $room['available_rooms'] ?? 0;
                    
                    // Get raw price for JavaScript (without formatting)
                    $rawPrice = $room['price'];
                    $hasDiscount = !empty($room['discount_percent']) && 
                                  strtotime($room['discount_valid_until']) >= time();
                
                if ($hasDiscount) {
                    $rawDiscountedPrice = $room['price'] * (1 - ($room['discount_percent'] / 100));
                } else {
                    $rawDiscountedPrice = $rawPrice;
                }
                
                // Format price for display
                $price = number_format($room['price'], 2);
                $discountedPrice = number_format($rawDiscountedPrice, 2);
                
                // Handle image path
                $imagePath = !empty($room['image']) ? 
                    (strpos($room['image'], 'http') === 0 ? $room['image'] : "../../../Admin/uploads/rooms/" . basename($room['image'])) : 
                    'https://via.placeholder.com/300x200?text=Room+Image';
                
                // Get total rooms and available rooms for the selected dates
                $checkin = $_SESSION['room_availability']['checkin'] ?? '';
                $checkout = $_SESSION['room_availability']['checkout'] ?? '';
                
                // Get total rooms of this type
                $totalStmt = $pdo->prepare("
                    SELECT COUNT(*) as total_count 
                    FROM room_numbers 
                    WHERE room_type_id = ? 
                    AND status = 'active'
                ");
                $totalStmt->execute([$room['room_type_id']]);
                $totalRooms = $totalStmt->fetch(PDO::FETCH_ASSOC)['total_count'];
                
                // Get booked rooms for the selected dates
                $bookedStmt = $pdo->prepare("
                    SELECT COUNT(DISTINCT 
                        CASE 
                            WHEN br.room_number_fk_id IS NOT NULL THEN br.room_number_fk_id 
                            ELSE rn.room_number_id 
                        END
                    ) as booked_count
                    FROM room_numbers rn
                    LEFT JOIN booked_rooms br ON (rn.room_number_id = br.room_number_fk_id OR rn.room_type_id = br.room_type_id)
                    JOIN bookings b ON br.booking_id = b.booking_id
                    WHERE rn.room_type_id = ?
                    AND b.status IN ('confirmed', 'checked_in')
                    AND (
                        (b.check_in < ? AND b.check_out > ?) OR  -- Overlaps start of desired period
                        (b.check_in < ? AND b.check_out > ?) OR  -- Overlaps end of desired period
                        (b.check_in >= ? AND b.check_out <= ?)   -- Completely within desired period
                    )
                ");
                
                $params = [
                    $room['room_type_id'],
                    $checkout, $checkin,   // For first condition
                    $checkin, $checkout,   // For second condition
                    $checkin, $checkout    // For third condition
                ];
                
                $bookedStmt->execute($params);
                $bookedCount = $bookedStmt->fetch(PDO::FETCH_ASSOC)['booked_count'] ?? 0;
                
                // Calculate available rooms
                $availableCount = max(0, $totalRooms - $bookedCount);
            ?>
                <div class="room-card">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                         class="room-image" 
                         alt="<?php echo htmlspecialchars($room['room_type']); ?>"
                         onerror="this.src='https://via.placeholder.com/300x200?text=Room+Image'">
                    
                    <div class="room-content">
                        <h3><?php echo htmlspecialchars($room['room_type']); ?></h3>
                        
                        <?php 
                        // Get room ratings
                        $ratings = getRoomRatings($pdo, $room['room_type_id']);
                        if ($ratings && $ratings['average_rating'] > 0): 
                        ?>
                        <div class="room-rating mb-2">
                            <?php
                            $fullStars = floor($ratings['average_rating']);
                            $hasHalfStar = $ratings['average_rating'] - $fullStars >= 0.5;
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            
                            // Full stars
                            for ($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="fas fa-star text-warning"></i>';
                            }
                            
                            // Half star if needed
                            if ($hasHalfStar) {
                                echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                $emptyStars--; // Decrease empty stars count
                            }
                            
                            // Empty stars
                            for ($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="far fa-star text-warning"></i>';
                            }
                            ?>
                            <span class="ms-1 text-muted small">(<?php echo number_format($ratings['average_rating'], 1); ?>)</span>
                            <span class="ms-1 text-muted small"><?php echo $ratings['review_count']; ?> review<?php echo $ratings['review_count'] != 1 ? 's' : ''; ?></span>
                        </div>
                        <?php else: ?>
                        <div class="room-rating mb-2">
                            <span class="text-muted small">No reviews yet</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="price">
                            ₱<?php echo $hasDiscount ? $discountedPrice : $price; ?>
                            <?php if ($hasDiscount): ?>
                                <span class="original-price">₱<?php echo $price; ?></span>
                                <span class="discount-badge">Save <?php echo $room['discount_percent']; ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="capacity">
                            <i class="fas fa-users"></i> 
                            Capacity: <?php echo $room['capacity']; ?> persons
                        </div>
                        
                        <div class="available-rooms">
                            <i class="fas fa-door-open"></i> 
                            Available: <?= $availableCount; ?> of <?= $totalRooms; ?> room<?= $totalRooms != 1 ? 's' : ''; ?>
                        </div>
                        
                        <ul class="room-features">
                            <li><i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['beds']); ?></li>
                            <li><i class="fas fa-wifi"></i> Free WiFi</li>
                            <li><i class="fas fa-snowflake"></i> Air Conditioning</li>
                            <li><i class="fas fa-tv"></i> Flat-screen TV</li>
                        </ul>
                        
                        <div class="button-group">
                            <?php if ($isAvailable): ?>
                                <div class="d-flex flex-column gap-2 align-items-center w-100">
                                    <button class="btn-book w-100 text-center" 
                                            onclick="addToList(<?php echo $room['room_type_id']; ?>, '<?php echo addslashes($room['room_type']); ?>', <?php echo $rawDiscountedPrice; ?>, <?php echo $room['capacity']; ?>)">
                                        <i class="fas fa-plus"></i> ADD TO LIST
                                    </button>
                                    <button class="btn btn-outline-primary view-details-btn w-100 text-center" 
                                            data-room-id="<?php echo $room['room_type_id']; ?>">
                                        <i class="fas fa-info-circle"></i> VIEW DETAILS
                                    </button>
                                    <button class="btn btn-outline-success write-review-btn w-100 text-center" 
                                            onclick="writeReview(<?php echo $room['room_type_id']; ?>, '<?php echo addslashes($room['room_type']); ?>')">
                                        <i class="fas fa-pen"></i> VIEW REVIEW
                                    </button>
                                </div>
                            <?php else: ?>
                                <button class="btn-book btn-secondary" disabled>
                                    <i class="fas fa-times"></i> FULLY BOOKED
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                    endforeach; 
                endif; // End of available rooms check
            } 
            ?>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Room Details Modal -->
<div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="roomDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading room details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reviewModalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading review form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to load room details into modal
function loadRoomDetails(roomId) {
    const modal = new bootstrap.Modal(document.getElementById('roomDetailsModal'));
    const modalContent = document.getElementById('roomDetailsContent');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading room details...</p>
        </div>`;
    
    // Show the modal
    modal.show();
    
    // Fetch room details
    fetch(`room_details.php?id=${roomId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            modalContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading room details:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Could not load room details. Please try again later.
                </div>`;
        });
}

// Add event listeners for view details buttons
document.addEventListener('DOMContentLoaded', function() {
    const viewDetailButtons = document.querySelectorAll('.view-details-btn');
    viewDetailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            if (roomId) {
                loadRoomDetails(roomId);
            }
        });
    });
});

function addToList(roomTypeId, roomType, price, capacity) {
    // Get current booking list from localStorage
    let bookingList = JSON.parse(localStorage.getItem('bookingList') || '[]');
    
    // Check if room is already in the list
    const existingRoom = bookingList.find(room => room.roomTypeId === roomTypeId);
    
    if (existingRoom) {
        // Show warning if room already exists
        Swal.fire({
            title: 'Already Added!',
            html: `<strong>${roomType}</strong> is already in your booking list.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107',
            timer: 2500,
            timerProgressBar: true
        });
        return;
    }
    
    // Add room to list with price and capacity
    bookingList.push({
        roomTypeId: roomTypeId,
        roomType: roomType,
        price: price,
        capacity: capacity,
        quantity: 1,
        addedAt: new Date().toISOString()
    });
    
    // Save to localStorage
    localStorage.setItem('bookingList', JSON.stringify(bookingList));
    
    // Show success notification
    Swal.fire({
        title: 'Added to List!',
        html: `<strong>${roomType}</strong> has been successfully added to your list.`,
        icon: 'success',
        showConfirmButton: true,
        confirmButtonText: 'Great!',
        confirmButtonColor: '#667eea',
        timer: 3000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
            // Update badge
            updateNavBadge();
            console.log('Room added to list:', roomTypeId, roomType);
        }
    });
}

function writeReview(roomTypeId, roomType) {
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    const modalContent = document.getElementById('reviewModalContent');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading review form...</p>
        </div>`;
    
    // Show the modal
    modal.show();
    
    // Fetch review form content
    fetch(`room_review_details.php?room_id=${roomTypeId}&room_type=${encodeURIComponent(roomType)}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            modalContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading review form:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Could not load review form. Please try again later.
                </div>`;
        });
}

</script>
