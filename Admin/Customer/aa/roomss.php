<?php 
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/roomss_errors.log');

// Start session
session_start();

// Include database connection
require 'db_con.php';

// Debug: Log session and POST data
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

/**
 * Get all room types with their availability status
 */
function getRoomTypesWithNumbers($pdo) {
    try {
        // Debug: Log the start of the function
        error_log("Starting getRoomTypesWithNumbers function");
        
        // First, check if the table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'room_types'");
        if ($tableCheck->rowCount() === 0) {
            error_log("ERROR: room_types table does not exist");
            return [];
        }
        
        // Check if room_numbers table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'room_numbers'");
        if ($tableCheck->rowCount() === 0) {
            error_log("ERROR: room_numbers table does not exist");
            return [];
        }
        
        // Get check-in/check-out dates from session or use defaults
        $check_in = isset($_SESSION['booking_form_data']['check_in']) ? 
                   $_SESSION['booking_form_data']['check_in'] : date('Y-m-d');
        $check_out = isset($_SESSION['booking_form_data']['check_out']) ? 
                    $_SESSION['booking_form_data']['check_out'] : date('Y-m-d', strtotime('+1 day'));
        
        error_log("Using check-in: $check_in, check-out: $check_out");
        
        // Simplified query for debugging
        // First, let's check if there are any active room types at all
        $checkActive = $pdo->query("SELECT COUNT(*) as count FROM room_types WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC);
        error_log("Active room types count: " . $checkActive['count']);
        
        // If no active rooms, check for any room types regardless of status
        if ($checkActive['count'] == 0) {
            $allRooms = $pdo->query("SELECT * FROM room_types LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            error_log("No active room types found. Sample of all room types: " . print_r($allRooms, true));
        }
        
        // Simplified query to get room types with basic info
        $sql = "SELECT 
                    rt.room_type_id, 
                    rt.room_type as room_type, 
                    rt.price, 
                    rt.capacity,
                    rt.description, 
                    rt.beds, 
                    rt.rating, 
                    rt.image,
                    rt.image2,
                    rt.image3,
                    rt.status as room_status,
                    CASE 
                        WHEN rt.discount_valid_until >= CURDATE() THEN rt.discount_percent 
                        ELSE NULL 
                    END as discount_percent,
                    rt.discount_valid_until,
                    COALESCE((
                        SELECT COUNT(*)
                        FROM room_numbers rn
                        WHERE rn.room_type_id = rt.room_type_id 
                        AND rn.status = 'active'
                    ), 0) as total_rooms,
                    CASE 
                        WHEN rt.status = 'active' THEN 'Available' 
                        ELSE 'Not Available' 
                    END as status
                FROM room_types rt
                WHERE rt.status = 'active'
                ORDER BY rt.room_type";
                
        error_log("Executing SQL: " . $sql);
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($rooms) . " active room types");
        
        if (empty($rooms)) {
            // Try to get any room types, even if not active
            $sql = "SELECT * FROM room_types LIMIT 5";
            $all_rooms = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            error_log("Sample room_types: " . print_r($all_rooms, true));
            
            // Check room_numbers for each room type
            foreach ($all_rooms as $room) {
                $room_type_id = $room['room_type_id'];
                $sql = "SELECT COUNT(*) as count, status 
                        FROM room_numbers 
                        WHERE room_type_id = :room_type_id 
                        GROUP BY status";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':room_type_id' => $room_type_id]);
                $room_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                error_log("Room type ID {$room_type_id} ({$room['room_type']}) has these room numbers:");
                error_log(print_r($room_counts, true));
                
                // Get sample room numbers
                $sql = "SELECT * FROM room_numbers 
                        WHERE room_type_id = :room_type_id 
                        LIMIT 5";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':room_type_id' => $room_type_id]);
                $sample_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Sample room numbers for room_type_id {$room_type_id}:");
                error_log(print_r($sample_rooms, true));
            }
        }
        
        return $rooms;
        
                
        // Get check-in/check-out dates from session or use defaults
        $check_in = isset($_SESSION['booking_form_data']['check_in']) ? 
                   $_SESSION['booking_form_data']['check_in'] : date('Y-m-d');
        $check_out = isset($_SESSION['booking_form_data']['check_out']) ? 
                    $_SESSION['booking_form_data']['check_out'] : date('Y-m-d', strtotime('+1 day'));
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':check_in', $check_in);
        $stmt->bindParam(':check_out', $check_out);
        $stmt->execute();
        
        $rooms = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $roomTypeId = $row['room_type_id'];
            
            // Initialize room type
            $rooms[$roomTypeId] = [
                'room_type_id' => $row['room_type_id'],
                'room_type' => $row['room_type'],
                'price' => $row['price'],
                'capacity' => $row['capacity'],
                'image' => $row['image'],
                'image2' => $row['image2'],
                'image3' => $row['image3'],
                'rating' => $row['rating'],
                'description' => $row['description'],
                'beds' => $row['beds'],
                'discount_percent' => $row['discount_percent'],
                'discount_valid_until' => $row['discount_valid_until'],
                'available_rooms' => (int)$row['available_rooms'],
                'total_rooms' => (int)$row['total_rooms'],
                'status' => $row['status']
            ];
        }
        
        // Fix image paths and convert to indexed array
        $result = [];
        foreach ($rooms as $room) {
            // Fix image paths
            $room['image'] = !empty($room['image']) ? 
                (strpos($room['image'], 'uploads/') === 0 ? 
                    '../../../Admin/' . $room['image'] : 
                    '../../../Admin/uploads/rooms/' . basename($room['image'])) : 
                '../../../Admin/uploads/rooms/default.jpg';
                
            $room['image2'] = !empty($room['image2']) ? 
                (strpos($room['image2'], 'uploads/') === 0 ? 
                    '../../../Admin/' . $room['image2'] : 
                    '../../../Admin/uploads/rooms/' . basename($room['image2'])) : 
                '../../../Admin/uploads/rooms/default2.jpg';
                
            $room['image3'] = !empty($room['image3']) ? 
                (strpos($room['image3'], 'uploads/') === 0 ? 
                    '../../../Admin/' . $room['image3'] : 
                    '../../../Admin/uploads/rooms/' . basename($room['image3'])) : 
                '../../../Admin/uploads/rooms/default3.jpg';
            
            $result[] = $room;
        }
        
        return $result;
        
    } catch(PDOException $e) {
        error_log("Error fetching room types with numbers: " . $e->getMessage());
        return [];
    }
}

// Get all room types with their numbers
$rooms = getRoomTypesWithNumbers($pdo);

// Debug: Check if rooms were found
if (empty($rooms)) {
    error_log("No rooms found in database or query returned no results");
    // Check for any PDO errors
    $errorInfo = $pdo->errorInfo();
    if ($errorInfo[0] !== '00000') {
        error_log("PDO Error: " . print_r($errorInfo, true));
    }
}

error_log("Rooms data: " . print_r($rooms, true));

// Check if form was submitted and save data to session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['booking_form_data'] = $_POST;
}

// Clear form data from session after successful booking
if (isset($_GET['clear_form']) && $_GET['clear_form'] === 'true') {
    unset($_SESSION['booking_form_data']);
}



// Add this function near the top of the file with other functions
function getExtraBeds($pdo) {
    try {
        $sql = "SELECT id, item_type, price FROM beds WHERE available_quantity > 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching extra beds: " . $e->getMessage());
        return [];
    }
}

// Add this line after $rooms = getRooms($pdo);
$extraBeds = getExtraBeds($pdo);

// Function to fetch discount types from database
function getDiscountTypes($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM discount_types WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching discount types: " . $e->getMessage());
        return [];
    }
}

// Function to fetch active payment methods
function getPaymentMethods($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM payment_methods WHERE is_active = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching payment methods: " . $e->getMessage());
        return [];
    }
}

// Add this line to fetch discount types
$discountTypes = getDiscountTypes($pdo);
// Add this line to fetch payment methods
$paymentMethods = getPaymentMethods($pdo);

function getRoomReviews($pdo, $roomTypeId) {
    try {
        $sql = "SELECT 
                    rr.review_id,
                    rr.user_id,
                    rr.rating,
                    rr.review,
                    rr.created_at,
                    u.username  -- Add username from users table
                FROM room_reviews rr
                LEFT JOIN users u ON rr.user_id = u.user_id
                WHERE rr.room_type_id = :room_type_id
                ORDER BY rr.created_at DESC";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['room_type_id' => $roomTypeId]);
        
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the reviews data
        foreach ($reviews as &$review) {
            // Format the date
            $review['formatted_date'] = date('M d, Y', strtotime($review['created_at']));
            
            // Set default username if null
            if (empty($review['username'])) {
                $review['username'] = 'Anonymous User';
            }
            
            // Ensure rating is numeric
            $review['rating'] = floatval($review['rating']);
        }
        
        return $reviews;
        
    } catch(PDOException $e) {
        error_log("Error fetching room reviews: " . $e->getMessage());
        return [];
    }
}

/**
 * Get available room numbers for a specific room type
 */
function getAvailableRoomNumbers($pdo, $room_type_id) {
    try {
        $sql = "SELECT room_number 
                FROM room_numbers 
                WHERE room_type_id = :room_type_id 
                AND status = 'active'
                ORDER BY room_number";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':room_type_id', $room_type_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $room_numbers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $room_numbers[] = $row['room_number'];
        }
        
        return $room_numbers;
        
    } catch(PDOException $e) {
        error_log("Error fetching room numbers: " . $e->getMessage());
        return [];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E Akomoda - Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="roomss.css">
</head>
<body>

<?php include('nav.php'); ?>
 <?php include 'message_box.php'; ?>
    
    <!-- Room Search/Filter Section -->
    <div class="container my-5 pt-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4"><i class="fas fa-search"></i> Check Room Availability</h4>
                <form id="roomSearchForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="search_check_in" class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" id="search_check_in" name="search_check_in" required>
                        </div>
                        <div class="col-md-2">
                            <label for="search_check_out" class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" id="search_check_out" name="search_check_out" required>
                        </div>
                        <div class="col-md-2">
                            <label for="search_rooms" class="form-label">Number of Rooms</label>
                            <input type="number" class="form-control" id="search_rooms" name="search_rooms" value="1" min="1" max="10">
                        </div>
                        <div class="col-md-2">
                            <label for="search_adults" class="form-label">Adults</label>
                            <input type="number" class="form-control" id="search_adults" name="search_adults" value="1" min="1" max="10">
                        </div>
                        <div class="col-md-2">
                            <label for="search_children" class="form-label">Children</label>
                            <input type="number" class="form-control" id="search_children" name="search_children" value="0" min="0" max="10">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-warning w-100" id="checkAvailabilityBtn">
                                <i class="fas fa-search"></i> Check Availability
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Search Results Status -->
                <div id="searchAvailabilityStatus" class="alert d-none mt-3" role="alert">
                    <div class="d-flex align-items-center">
                        <div id="searchAvailabilityIcon" class="me-2"></div>
                        <div>
                            <div id="searchAvailabilityTitle" class="fw-bold"></div>
                            <div id="searchAvailabilityMessage" class="small"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <?php foreach($rooms as $room): ?>
                <div class="col-md-4">
                    <div class="room-card" id="room_<?php echo $room['room_type_id']; ?>">
                        <div class="position-relative">
                            <?php
                            $imagePath = '';
                            if (!empty($room['image'])) {
                                // If image path already contains 'uploads/', use it directly
                                if (strpos($room['image'], 'uploads/') !== false) {
                                    $imagePath = '../../../Admin/' . $room['image'];
                                } else {
                                    // Otherwise, construct the path using basename
                                    $imagePath = '../../../Admin/uploads/rooms/' . basename($room['image']);
                                }
                            } else {
                                // Default image path
                                $imagePath = '../../../Admin/uploads/rooms/default.jpg';
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                 class="room-image" 
                                 alt="<?php echo htmlspecialchars($room['room_type']); ?>"
                                 onerror="this.src='../../../Admin/uploads/rooms/default.jpg'">
                            

                            <?php 
                            // Get available room numbers for this room type
                            $available_rooms = getAvailableRoomNumbers($pdo, $room['room_type_id']);
                            $available_count = count($available_rooms);
                            $has_available_rooms = ($available_count > 0);
                            
                            if (!$has_available_rooms): ?>
                            <div class="fully-booked-overlay">
                                <span class="fully-booked-text">FULLY BOOKED</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="room-details">
                            <h5 class="room-title"><?php echo htmlspecialchars($room['room_type']); ?></h5>
                            
                            <div class="rating">
                                <?php
                                $rating = $room['rating'] ?? 0;
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif($i - 0.5 <= $rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                                <span class="text-muted">(<?php echo number_format($rating, 1); ?>)</span>
                            </div>

                            <div class="price">₱<?php echo number_format($room['price'], 2); ?> per night</div>
                            
                            <!-- Available rooms display -->
                            <div class="available-rooms <?php echo $has_available_rooms ? 'text-success' : 'text-danger'; ?>">
                                <i class="fas fa-door-open"></i> 
                                <?php if ($has_available_rooms): ?>
                                    <?php echo $available_count; ?> room<?php echo $available_count != 1 ? 's' : ''; ?> available
                                <?php else: ?>
                                    No rooms available
                                <?php endif; ?>
                            </div>

                            <div class="capacity">
                                <i class="fas fa-user-friends"></i> 
                                Max capacity: <?php echo $room['capacity']; ?> guests
                            </div>

                            <div class="amenities">
                                <i class="fas fa-snowflake" title="Air Conditioning"></i>
                                <i class="fas fa-bath" title="Private Bathroom"></i>
                                <i class="fas fa-tv" title="Flat-screen TV"></i>
                                <i class="fas fa-wifi" title="Free WiFi"></i>
                                <i class="fas fa-shower" title="Hot Shower"></i>
                            </div>

                            <div class="mt-3">
                                <button class="btn btn-warning w-100 mb-2" onclick="viewDetails(<?php echo $room['room_type_id']; ?>)">
                                    <i class="fas fa-eye"></i> VIEW DETAILS
                                </button>
                                <?php if ($has_available_rooms): ?>
                                    <!-- Add hidden input for room quantity -->
                                    <input type="hidden" name="room_quantity" id="room_quantity_<?php echo $room['room_type_id']; ?>" 
                                           value="1" data-available="<?php echo $available_count; ?>">
                                    <button class="btn btn-warning w-100 add-to-list-btn" data-room-id="<?php echo $room['room_type_id']; ?>">
                                        <i class="fas fa-plus"></i> ADD TO LIST
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-ban"></i> NOT AVAILABLE
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add this modal HTML before the closing body tag -->
    <div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="roomImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#roomImageCarousel" data-bs-slide-to="0" class="active"></button>
                                    <button type="button" data-bs-target="#roomImageCarousel" data-bs-slide-to="1"></button>
                                    <button type="button" data-bs-target="#roomImageCarousel" data-bs-slide-to="2"></button>
                                </div>
                                <div class="carousel-inner rounded">
                                    <div class="carousel-item active">
                                        <img id="modalRoomImage1" src="" class="d-block w-100" alt="Room Image 1">
                                    </div>
                                    <div class="carousel-item">
                                        <img id="modalRoomImage2" src="" class="d-block w-100" alt="Room Image 2">
                                    </div>
                                    <div class="carousel-item">
                                        <img id="modalRoomImage3" src="" class="d-block w-100" alt="Room Image 3">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#roomImageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#roomImageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 id="modalRoomType"></h4>
                            <div class="rating mb-2">
                                <div id="modalRating"></div>
                            </div>
                            <p class="price-text mb-3" id="modalPrice"></p>
                            <div class="details-section mb-3">
                                <h6 class="fw-bold">Room Details</h6>
                                <p id="modalDescription"></p>
                                <p><i class="fas fa-user-friends"></i> <span id="modalCapacity"></span></p>
                                <p><i class="fas fa-bed"></i> <span id="modalBeds"></span></p>
                            </div>
                            <div class="amenities-section">
                                <h6 class="fw-bold">Amenities</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <span><i class="fas fa-snowflake"></i> Air Conditioning</span>
                                    <span><i class="fas fa-bath"></i> Private Bathroom</span>
                                    <span><i class="fas fa-tv"></i> Flat-screen TV</span>
                                    <span><i class="fas fa-wifi"></i> Free WiFi</span>
                                    <span><i class="fas fa-shower"></i> Hot Shower</span>
                                </div>
                            </div>
                            <!-- Reviews Section -->
                            <div class="reviews-section mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0">Reviews</h6>
                                    <button class="btn btn-warning btn-sm" id="writeReviewBtn">
                                        <i class="fas fa-star"></i> Write a Review
                                    </button>
                                </div>
                                <div id="reviewsList" class="review-list-container">
                                    <!-- Reviews will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Rating & Review Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">Rate & Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ratingForm">
                        <input type="hidden" id="room_type_id" name="room_type_id">
                        <div class="mb-3">
                            <label class="form-label">Your Rating</label>
                            <div class="star-rating">
                                <i class="far fa-star" data-rating="1"></i>
                                <i class="far fa-star" data-rating="2"></i>
                                <i class="far fa-star" data-rating="3"></i>
                                <i class="far fa-star" data-rating="4"></i>
                                <i class="far fa-star" data-rating="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="selected_rating" required>
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Your Review</label>
                            <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before </body> -->
    <!-- Booking List Modal -->
    <div class="modal fade" id="bookingListModal" tabindex="-1" aria-labelledby="bookingListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingListModalLabel">Your Booking List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="bookingListContent">
                        <!-- Booking list items will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning proceed-to-booking-btn">Proceed to Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this before </body> -->
    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Complete Your Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" action="room_payment.php" method="POST" enctype="multipart/form-data">
                            <!-- Hidden fields for room_payment.php -->
                            <input type="hidden" id="room_type" name="room_type">
                            <input type="hidden" id="room_type_id" name="room_type_id">
                            <input type="hidden" id="room_price" name="room_price">
                            <input type="hidden" id="num_nights" name="num_nights">
                            <input type="hidden" id="extra_bed_cost" name="extra_bed_cost" value="0">
                            <input type="hidden" id="payment_amount" name="payment_amount">
                            <input type="hidden" id="remaining_balance" name="remaining_balance">
                            
                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <!-- Contact Information Section for Non-logged Users -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Contact Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="form-text">Booking confirmation will be sent here</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               pattern="[0-9]{11}" placeholder="09XXXXXXXXX" required>
                                        <div class="form-text">Enter 11-digit phone number</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" required>
                            </div>
                            <div class="col-md-6">
                                <label for="check_out" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" required>
                            </div>
                        </div>

                        <!-- Availability Status Indicator -->
                        <div id="availabilityStatus" class="alert d-none" role="alert">
                            <div class="d-flex align-items-center">
                                <div id="availabilityIcon" class="me-2"></div>
                                <div>
                                    <div id="availabilityTitle"></div>
                                    <div id="availabilityMessage" class="small"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Number of Adults</label>
                                <input type="number" class="form-control" id="num_adults" name="num_adults" 
                                       value="1" min="1" required onchange="validateGuestCount()">
                                <input type="hidden" id="room_capacity" name="room_capacity" value="<?php echo $room['capacity'] ?? 3; ?>">
                                <div id="capacityWarning" class="text-danger d-none">
                                    <i class="fas fa-exclamation-triangle"></i> Total guests exceed room capacity
                                </div>
                            </div>
                            
                            <!-- Add Extra Bed Dropdown -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Extra Bed (Optional)</label>
                                <select class="form-select" id="extra_bed" name="extra_bed" onchange="updateBookingSummary()">
                                    <option value="">No Extra Bed</option>
                                    <?php foreach($extraBeds as $bed): ?>
                                        <option value="<?php echo $bed['id']; ?>">
                                            <?php echo htmlspecialchars($bed['item_type']) . ' (+₱' . number_format($bed['price'], 2) . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Number of Children</label>
                                <input type="number" class="form-control" id="num_children" name="num_children" 
                                       value="0" min="0" onchange="validateGuestCount()" oninput="validateGuestCount()">
                                <div class="form-text">Guests under 7 years old is free of charge</div>
                        </div>

                        <h5 class="mt-4 mb-3">Guest Name</h5>
                        <div id="guestFieldsContainer">
                            <!-- Guest fields will be dynamically added here -->
                        </div>

                        <h5 class="mt-4 mb-3">Booking Summary</h5>
                        <div class="card mb-4">
                            <div class="card-body" id="bookingSummaryContent">
                                <!-- Booking summary will be dynamically added here -->
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Booking Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> Check-in is at 1:00 PM, and check-out is at 11:00 AM
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_option" class="form-label">Payment Option</label>
                                <select class="form-select" id="payment_option" name="payment_option" required onchange="updatePaymentFields()">
                                    <option value="">Select Payment Option</option>
                                    <option value="Partial Payment">Downpayment ₱1,500</option>
                                    <option value="Custom Payment">Custom Payment (Min ₱1,500)</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="customPaymentField" style="display: none;">
                                <label for="custom_payment" class="form-label">Custom Payment Amount (Min ₱1,500)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="custom_payment" name="custom_payment" min="1500" step="100">
                                </div>
                                <div class="form-text">Minimum payment: ₱1,500</div>
                            </div>
                            <div class="col-md-4">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <?php foreach($paymentMethods as $method): ?>
                                        <option value="<?php echo htmlspecialchars($method['name']); ?>">
                                            <?php echo htmlspecialchars($method['display_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Form Buttons -->
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="proceedToSummaryBtn" onclick="showBookingSummary()">Proceed to Summary</button>
                        </div>
                        
                        <!-- Hidden form fields to store calculated values -->
                        <input type="hidden" id="total_amount" name="total_amount">
                        <input type="hidden" id="discount_amount" name="discount_amount">
                        <input type="hidden" id="tax_amount" name="tax_amount">
                        <input type="hidden" id="grand_total" name="grand_total">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
    .flatpickr-day.booked {
        background-color: #ff6b6b !important;
        color: white !important;
        border-color: #ff6b6b !important;
        text-decoration: line-through;
        position: relative;
        cursor: not-allowed;
    }
    .flatpickr-day.booked:hover {
        background-color: #ff5252 !important;
    }
    .flatpickr-day.booked::after {
        content: 'Fully Booked';
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        z-index: 1000;
    }
    .flatpickr-day.booked:hover::after {
        opacity: 1;
    }
    .flatpickr-day.booked.nextMonthDay,
    .flatpickr-day.booked.prevMonthDay {
        opacity: 0.5;
    }
    </style>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update availability status indicator
        function updateAvailabilityStatus(data) {
            const statusDiv = document.getElementById('availabilityStatus');
            const iconDiv = document.getElementById('availabilityIcon');
            const titleDiv = document.getElementById('availabilityTitle');
            const messageDiv = document.getElementById('availabilityMessage');

            if (!data || !data.availability_info) {
                statusDiv.classList.add('d-none');
                return;
            }

            const hasIssues = data.availability_info.some(room => room.available_rooms === 0);
            const hasLimited = data.availability_info.some(room => room.available_rooms < room.total_rooms && room.available_rooms > 0);
            const isAdvanceBooking = data.is_advance_booking === true;

            if (hasIssues) {
                // Show warning for unavailable dates
                statusDiv.classList.remove('d-none', 'alert-success', 'alert-warning', 'alert-info');
                statusDiv.classList.add('alert-danger');
                iconDiv.innerHTML = '<i class="fas fa-times-circle fa-lg text-white"></i>';
                titleDiv.textContent = 'Dates Not Available';
                
                // Check if this is an advance booking scenario
                if (isAdvanceBooking) {
                    messageDiv.innerHTML = '<strong>Advance Booking:</strong> All room numbers are currently occupied for these dates. Your booking will be scheduled for a future date when rooms become available.';
                } else {
                    messageDiv.textContent = 'Some selected dates are fully booked. Please choose different dates.';
                }
            } else if (hasLimited) {
                // Show warning for limited availability
                statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-info');
                statusDiv.classList.add('alert-warning');
                iconDiv.innerHTML = '<i class="fas fa-exclamation-triangle fa-lg"></i>';
                titleDiv.textContent = 'Limited Availability';
                messageDiv.textContent = 'Some room types have limited availability for your selected dates.';
            } else {
                // Show success for full availability
                statusDiv.classList.remove('d-none', 'alert-warning', 'alert-danger', 'alert-info');
                statusDiv.classList.add('alert-success');
                iconDiv.innerHTML = '<i class="fas fa-check-circle fa-lg"></i>';
                titleDiv.textContent = 'Dates Available';
                messageDiv.textContent = 'All room types are available for your selected dates.';
            }
        }

        // Function to clear availability status
        function clearAvailabilityStatus() {
            const statusDiv = document.getElementById('availabilityStatus');
            statusDiv.classList.add('d-none');
        }

        // Function to check room availability via AJAX
        async function checkRoomAvailability(checkIn, checkOut) {
            if (!checkIn || !checkOut) {
                clearAvailabilityStatus();
                return true; // Don't block if dates aren't complete
            }

            try {
                // Get booking list to check only those room types
                let url = `check_room_availability.php?check_in=${checkIn}&check_out=${checkOut}`;
                
                // Fetch booking list to get room type IDs
                try {
                    const bookingListResponse = await fetch('get_booking_list.php');
                    const bookingListData = await bookingListResponse.json();
                    
                    if (bookingListData.success && bookingListData.items && bookingListData.items.length > 0) {
                        // Extract room_type_ids from booking list
                        const roomTypeIds = bookingListData.items.map(item => item.room_type_id).join(',');
                        url += `&room_type_ids=${roomTypeIds}`;
                    }
                } catch (error) {
                    console.log('Could not fetch booking list, checking all room types:', error);
                }
                
                const response = await fetch(url);
                const data = await response.json();

                if (!data.success) {
                    // Enhanced alert with detailed availability information
                    const isAdvanceBooking = data.is_advance_booking === true;
                    let alertHtml = `<div class="text-center">
                        <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                        <h5 class="mb-3">Date${data.availability_info && data.availability_info.length > 1 ? 's' : ''} Not Available</h5>
                        <p class="mb-3">${data.message}</p>`;
                    
                    // Show advance booking alert if all rooms are occupied
                    if (isAdvanceBooking) {
                        alertHtml += `<div class="alert alert-warning mt-3">
                            <i class="fas fa-calendar-plus me-2"></i>
                            <strong>Advance Booking Notice:</strong> All room numbers are currently occupied for these dates. 
                            You can still proceed with an advance booking, and a room will be assigned when available.
                        </div>`;
                    }

                    if (data.availability_info && data.availability_info.length > 0) {
                        alertHtml += `<div class="availability-details">
                            <h6>Room Availability Details:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Room Type</th>
                                            <th>Total Rooms</th>
                                            <th>Available</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        data.availability_info.forEach(room => {
                            const statusClass = room.status === 'fully_booked' ? 'text-danger' :
                                              room.available_rooms < room.total_rooms ? 'text-warning' : 'text-success';
                            const statusIcon = room.status === 'fully_booked' ? 'fas fa-times-circle' :
                                             room.available_rooms < room.total_rooms ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';

                            alertHtml += `
                                <tr>
                                    <td>${room.room_type}</td>
                                    <td>${room.total_rooms}</td>
                                    <td>${room.available_rooms}</td>
                                    <td class="${statusClass}">
                                        <i class="${statusIcon}"></i>
                                        ${room.status === 'fully_booked' ? 'Fully Booked' :
                                          room.available_rooms < room.total_rooms ? 'Limited Availability' : 'Available'}
                                    </td>
                                </tr>`;
                        });

                        alertHtml += `</tbody></table></div>`;

                        if (data.summary && data.summary.total_available_rooms > 0) {
                            alertHtml += `<div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Alternative:</strong> ${data.summary.total_available_rooms} rooms are available across different types.
                                Consider selecting different dates or try other room types.
                            </div>`;
                        }
                    }

                    alertHtml += `</div>`;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Room Availability Check',
                        html: alertHtml,
                        confirmButtonText: 'Choose Different Dates',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        customClass: {
                            popup: 'availability-alert-popup'
                        }
                    });

                    // Update status indicator for unavailable dates
                    updateAvailabilityStatus(data);
                    return false;
                } else {
                    // Show success message only if there are some constraints or limited availability
                    if (data.availability_info && data.availability_info.some(room => room.booked_rooms > 0)) {
                        let successHtml = `<div class="text-center">
                            <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                            <h5 class="mb-3">Dates Available!</h5>
                            <p class="mb-3">${data.message}</p>`;

                        if (data.availability_info && data.availability_info.length > 0) {
                            successHtml += `<div class="availability-details">
                                <h6>Room Availability Details:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Room Type</th>
                                                <th>Total Rooms</th>
                                                <th>Available</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                            data.availability_info.forEach(room => {
                                const statusClass = room.status === 'fully_booked' ? 'text-danger' :
                                                  room.available_rooms < room.total_rooms ? 'text-warning' : 'text-success';
                                const statusIcon = room.status === 'fully_booked' ? 'fas fa-times-circle' :
                                                 room.available_rooms < room.total_rooms ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';

                                successHtml += `
                                    <tr>
                                        <td>${room.room_type}</td>
                                        <td>${room.total_rooms}</td>
                                        <td>${room.available_rooms}</td>
                                        <td class="${statusClass}">
                                            <i class="${statusIcon}"></i>
                                            ${room.status === 'fully_booked' ? 'Fully Booked' :
                                              room.available_rooms < room.total_rooms ? 'Limited Availability' : 'Available'}
                                        </td>
                                    </tr>`;
                            });

                            successHtml += `</tbody></table></div>`;
                        }

                        successHtml += `</div>`;

                        Swal.fire({
                            icon: 'success',
                            title: 'Room Availability Confirmed',
                            html: successHtml,
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'availability-success-popup'
                            }
                        });
                    }
                    // If all rooms are fully available, don't show any alert - just proceed silently

                    // Update status indicator for available dates
                    updateAvailabilityStatus(data);
                    return true;
                }
            } catch (error) {
                console.error('Error checking room availability:', error);
                clearAvailabilityStatus(); // Clear status on error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to check room availability. Please try again later.',
                    confirmButtonText: 'OK'
                });
                return true; // Assume available if there's an error
            }
        }

        // Function to disable booked dates
        function getDisabledDates() {
            // This will be populated via AJAX
            return [];
        }

        // Function to style booked dates
        function styleBookedDates(instance, bookedDates) {
            if (!bookedDates || !instance.calendarContainer) return;
            
            // Add 'booked' class to all booked dates
            instance.daysContainer.querySelectorAll('.flatpickr-day').forEach(day => {
                if (!day.classList.contains('flatpickr-day')) return;
                
                const date = day.getAttribute('aria-label');
                if (date) {
                    const dateObj = new Date(date);
                    const dateStr = instance.formatDate(dateObj, 'Y-m-d');
                    
                    if (bookedDates.includes(dateStr)) {
                        day.classList.add('booked');
                        day.classList.add('flatpickr-disabled');
                        day.classList.remove('flatpickr-enabled');
                        day.title = 'Fully Booked';
                        day.setAttribute('aria-disabled', 'true');
                        return;
                    }
                }
                
                // Only remove the booked class if it's not a disabled date for other reasons
                if (!day.classList.contains('flatpickr-disabled')) {
                    day.classList.remove('booked');
                }
            });
        }

        // Function to fetch and apply booked dates
        function fetchBookedDates(instance) {
            fetch('get_booked_rooms.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.booked_dates) {
                        window.bookedDates = data.booked_dates;
                        // Disable fully booked dates
                        instance.set('disable', data.booked_dates);
                        // Style the booked dates after a small delay to ensure calendar is rendered
                        setTimeout(() => {
                            styleBookedDates(instance, data.booked_dates);
                        }, 10);
                    }
                })
                .catch(error => console.error('Error fetching booked dates:', error));
        }

        // Initialize flatpickr for check-in
        const checkInPicker = flatpickr("#check_in", {
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: true, // Better UX on mobile
            disable: [], // Will be populated by fetchBookedDates
            onChange: async function(selectedDates, dateStr, instance) {
                // When check-in date changes, update check-out min date
                if (selectedDates.length > 0) {
                    const nextDay = new Date(selectedDates[0]);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkOutPicker.set('minDate', nextDay);

                    // If check-out date is before or same as new check-in date, reset it
                    if (checkOutPicker.selectedDates[0] &&
                        checkOutPicker.selectedDates[0] <= selectedDates[0]) {
                        checkOutPicker.clear();
                        clearAvailabilityStatus();
                    }

                    // Check room availability when check-in date changes
                    const isAvailable = await checkRoomAvailability(
                        instance.formatDate(selectedDates[0], 'Y-m-d'),
                        checkOutPicker.selectedDates[0] ?
                            checkOutPicker.formatDate(checkOutPicker.selectedDates[0], 'Y-m-d') :
                            instance.formatDate(nextDay, 'Y-m-d')
                    );

                    if (!isAvailable) {
                        instance.clear();
                    }
                } else {
                    // Clear status when check-in is cleared
                    clearAvailabilityStatus();
                }
                updateBookingSummary();
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                // Re-fetch and style dates when month changes
                fetchBookedDates(instance);
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                // Re-fetch and style dates when year changes
                fetchBookedDates(instance);
            },
            onOpen: function(selectedDates, dateStr, instance) {
                fetchBookedDates(instance);
            },
            onReady: function(selectedDates, dateStr, instance) {
                fetchBookedDates(instance);
            }
        });

        // Initialize flatpickr for check-out
        const checkOutPicker = flatpickr("#check_out", {
            minDate: new Date().fp_incr(1), // Tomorrow
            dateFormat: "Y-m-d",
            disableMobile: true, // Better UX on mobile
            disable: [], // Will be populated by fetchBookedDates
            onChange: async function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0 && checkInPicker.selectedDates[0]) {
                    // Check room availability when check-out date changes
                    const isAvailable = await checkRoomAvailability(
                        checkInPicker.formatDate(checkInPicker.selectedDates[0], 'Y-m-d'),
                        instance.formatDate(selectedDates[0], 'Y-m-d')
                    );

                    if (!isAvailable) {
                        instance.clear();
                    }
                } else {
                    // Clear status when check-out is cleared
                    clearAvailabilityStatus();
                }
                updateBookingSummary();
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                // Re-fetch and style dates when month changes
                fetchBookedDates(instance);
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                // Re-fetch and style dates when year changes
                fetchBookedDates(instance);
            },
            onOpen: function(selectedDates, dateStr, instance) {
                // When opening check-out, ensure we disable dates before check-in
                fetch('get_booked_rooms.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.booked_dates) {
                            // Include dates before check-in as disabled
                            const disabledDates = [...data.booked_dates];
                            if (checkInPicker.selectedDates[0]) {
                                const checkInDate = new Date(checkInPicker.selectedDates[0]);
                                checkInDate.setDate(checkInDate.getDate() - 1);
                                
                                // Add all dates before check-in as disabled
                                let currentDate = new Date();
                                while (currentDate <= checkInDate) {
                                    disabledDates.push(instance.formatDate(currentDate, 'Y-m-d'));
                                    currentDate.setDate(currentDate.getDate() + 1);
                                }
                            }
                            instance.set('disable', disabledDates);
                            // Style the booked dates
                            setTimeout(() => styleBookedDates(instance, data.booked_dates), 10);
                        }
                    });
            },
            onReady: function(selectedDates, dateStr, instance) {
                // Style dates when calendar is ready
                if (window.bookedDates) {
                    setTimeout(() => styleBookedDates(instance, window.bookedDates), 10);
                }
            }
        });

        // Initialize date pickers for search form
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        
        document.getElementById('search_check_in').setAttribute('min', today);
        document.getElementById('search_check_out').setAttribute('min', tomorrowStr);
        
        // Update check-out min date when check-in changes
        document.getElementById('search_check_in').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckOut = checkInDate.toISOString().split('T')[0];
            document.getElementById('search_check_out').setAttribute('min', minCheckOut);
            
            // Clear check-out if it's before the new min date
            const checkOutInput = document.getElementById('search_check_out');
            if (checkOutInput.value && checkOutInput.value < minCheckOut) {
                checkOutInput.value = '';
            }
        });

        // Handle Check Availability button click
        document.getElementById('checkAvailabilityBtn').addEventListener('click', async function() {
            const checkIn = document.getElementById('search_check_in').value;
            const checkOut = document.getElementById('search_check_out').value;
            const adults = document.getElementById('search_adults').value;
            const children = document.getElementById('search_children').value;

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
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

            try {
                const response = await fetch(`check_room_availability.php?check_in=${checkIn}&check_out=${checkOut}`);
                const data = await response.json();

                // Update search status indicator
                const statusDiv = document.getElementById('searchAvailabilityStatus');
                const iconDiv = document.getElementById('searchAvailabilityIcon');
                const titleDiv = document.getElementById('searchAvailabilityTitle');
                const messageDiv = document.getElementById('searchAvailabilityMessage');

                statusDiv.classList.remove('d-none', 'alert-success', 'alert-warning', 'alert-danger');

                if (!data.success) {
                    // Show unavailable status
                    statusDiv.classList.add('alert-danger');
                    iconDiv.innerHTML = '<i class="fas fa-times-circle fa-2x text-danger"></i>';
                    titleDiv.textContent = 'No Rooms Available';
                    messageDiv.textContent = `Unfortunately, no rooms are available for ${checkIn} to ${checkOut}. Please try different dates.`;

                    // Show detailed alert
                    let alertHtml = `<div class="text-center">
                        <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                        <h5 class="mb-3">No Rooms Available</h5>
                        <p class="mb-3">${data.message}</p>`;

                    if (data.availability_info && data.availability_info.length > 0) {
                        alertHtml += `<div class="availability-details">
                            <h6>Room Availability Details:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Room Type</th>
                                            <th>Total Rooms</th>
                                            <th>Available</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        data.availability_info.forEach(room => {
                            const statusClass = room.status === 'fully_booked' ? 'text-danger' : 'text-warning';
                            const statusIcon = room.status === 'fully_booked' ? 'fas fa-times-circle' : 'fas fa-exclamation-triangle';

                            alertHtml += `
                                <tr>
                                    <td>${room.room_type}</td>
                                    <td>${room.total_rooms}</td>
                                    <td>${room.available_rooms}</td>
                                    <td class="${statusClass}">
                                        <i class="${statusIcon}"></i>
                                        ${room.status === 'fully_booked' ? 'Fully Booked' : 'Limited'}
                                    </td>
                                </tr>`;
                        });

                        alertHtml += `</tbody></table></div></div>`;
                    }

                    alertHtml += `</div>`;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Room Availability Check',
                        html: alertHtml,
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Show available status
                    const hasLimited = data.availability_info.some(room => room.available_rooms < room.total_rooms && room.available_rooms > 0);
                    
                    if (hasLimited) {
                        statusDiv.classList.add('alert-warning');
                        iconDiv.innerHTML = '<i class="fas fa-exclamation-triangle fa-2x text-warning"></i>';
                        titleDiv.textContent = 'Limited Availability';
                        messageDiv.textContent = `Some rooms are available for ${checkIn} to ${checkOut}. Book soon!`;
                    } else {
                        statusDiv.classList.add('alert-success');
                        iconDiv.innerHTML = '<i class="fas fa-check-circle fa-2x text-success"></i>';
                        titleDiv.textContent = 'Rooms Available!';
                        messageDiv.textContent = `Great! Rooms are available for ${checkIn} to ${checkOut} with ${adults} adult(s) and ${children} child(ren).`;
                    }

                    // Show success alert with details
                    let successHtml = `<div class="text-center">
                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                        <h5 class="mb-3">Rooms Available!</h5>
                        <p class="mb-3">Check-in: ${checkIn} | Check-out: ${checkOut}</p>
                        <p class="mb-3">Guests: ${adults} Adult(s), ${children} Child(ren)</p>`;

                    if (data.availability_info && data.availability_info.length > 0) {
                        successHtml += `<div class="availability-details">
                            <h6>Available Rooms:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Room Type</th>
                                            <th>Total Rooms</th>
                                            <th>Available</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        data.availability_info.forEach(room => {
                            const statusClass = room.available_rooms === room.total_rooms ? 'text-success' : 'text-warning';
                            const statusIcon = room.available_rooms === room.total_rooms ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';

                            successHtml += `
                                <tr>
                                    <td>${room.room_type}</td>
                                    <td>${room.total_rooms}</td>
                                    <td>${room.available_rooms}</td>
                                    <td class="${statusClass}">
                                        <i class="${statusIcon}"></i>
                                        ${room.available_rooms === room.total_rooms ? 'Fully Available' : 'Limited'}
                                    </td>
                                </tr>`;
                        });

                        successHtml += `</tbody></table></div></div>`;
                    }

                    successHtml += `<p class="mt-3 text-muted">Scroll down to view and book available rooms.</p></div>`;

                    Swal.fire({
                        icon: 'success',
                        title: 'Availability Confirmed',
                        html: successHtml,
                        confirmButtonText: 'View Rooms',
                        timer: 5000
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
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });
    </script>

    <!-- Booking Summary Modal -->
    <div class="modal fade" id="bookingSummaryModal" tabindex="-1" aria-labelledby="bookingSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingSummaryModalLabel">Booking Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="summaryContent">
                        <!-- Summary content will be dynamically inserted here -->
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex flex-column align-items-center">
                            <button type="button" id="understandBtn" class="btn btn-primary mb-2">
                                I Understand the Terms and Conditions
                            </button>
                            <small class="text-muted">
                                <a href="#" id="showTermsLink">View Terms and Conditions</a>
                            </small>
                            <div id="termsError" class="invalid-feedback d-none">Please read and understand the terms and conditions to proceed.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="backToFormBtn">Back to Form</button>
                    <button type="button" class="btn btn-primary" id="confirmBookingBtn">Confirm & Pay</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms And Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <h5>Casa Estela Boutique Hotel & Café</h5>
                <p>Welcome to Casa Estela Boutique Hotel & Café's website. By using our website, you agree to these Terms and Conditions. Please review them carefully:</p>
                <ul>
                    <li>You agree to use the website for personal, non-commercial purposes only.</li>
                    <li>All information and content on this website are provided for general informational purposes only.</li>
                    <li>Reservations for rooms and café services must be made through our booking system.</li>
                    <li>Guests are required to provide accurate information during the booking process.</li>
                    <li>Cancellations can be made at any time; however, no refunds will be issued for canceled bookings.</li>
                    <li>Refunds are not applicable under any circumstances for cancellations.</li>
                    <li>Guests are expected to follow Casa Estela's house rules during their stay, which will be provided upon check-in.</li>
                    <li>We respect your privacy. Please review our <a href="#" class="text-primary text-decoration-underline">Privacy Policy</a> to understand how we collect, use, and protect your information.</li>
                    <li>Casa Estela reserves the right to modify these Terms and Conditions at any time. Changes will be posted on this page.</li>
                </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="acceptTermsBtn">
                        <i class="fas fa-check-circle"></i> I Accept the Terms
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Bootstrap modals
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the booking summary modal
            window.bookingSummaryModal = new bootstrap.Modal(document.getElementById('bookingSummaryModal'));
            
            // Add event listener for Proceed to Summary button
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (validateBookingForm()) {
                        showBookingSummary();
                    }
                });
            }
            
            // Add event listener for Confirm Booking button
            const confirmBookingBtn = document.getElementById('confirmBookingBtn');
            if (confirmBookingBtn) {
                confirmBookingBtn.addEventListener('click', function() {
                    const understandBtn = document.getElementById('understandBtn');
                    const termsError = document.getElementById('termsError');
                    
                    if (understandBtn.classList.contains('btn-disabled')) {
                        // Show error and prevent form submission
                        termsError.classList.remove('d-none');
                        return false;
                    }
                    
                    // Show loading indicator
                    Swal.fire({
                        title: 'Processing your booking',
                        text: 'Please wait while we prepare your payment details...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Get the booking form
                    const bookingForm = document.getElementById('bookingForm');
                    
                    // Get all form data
                    const formData = new FormData(bookingForm);
                    const formDataObj = {};
                    
                    // Convert FormData to object
                    formData.forEach((value, key) => {
                        formDataObj[key] = value;
                    });
                    
                    // Add booking items to the form data
                    fetch('get_booking_list.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.items && data.items.length > 0) {
                                // Add room items to form data
                                formDataObj.room_items = data.items;
                                
                                // Calculate total amount from room items
                                let totalAmount = 0;
                                data.items.forEach(item => {
                                    totalAmount += (item.price * item.quantity);
                                });
                                
                                // Add extra bed cost if any
                                const extraBedCost = parseFloat(formDataObj.extra_bed_cost || 0);
                                totalAmount += extraBedCost;
                                
                                formDataObj.total_amount = totalAmount.toFixed(2);
                                
                                // Calculate number of nights
                                const checkIn = new Date(formDataObj.check_in);
                                const checkOut = new Date(formDataObj.check_out);
                                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                                formDataObj.num_nights = nights;
                                
                                // Calculate payment amount based on payment option
                                const paymentOption = formDataObj.payment_option;
                                let paymentAmount = 0;
                                let remainingBalance = 0;
                                
                                if (paymentOption === 'Full Payment') {
                                    paymentAmount = totalAmount;
                                    remainingBalance = 0;
                                } else if (paymentOption === 'Partial Payment') {
                                    paymentAmount = 1500; // Fixed downpayment
                                    remainingBalance = totalAmount - paymentAmount;
                                } else if (paymentOption === 'Custom Payment') {
                                    paymentAmount = parseFloat(formDataObj.custom_payment) || 0;
                                    // Ensure payment is at least 1500
                                    if (paymentAmount < 1500) paymentAmount = 1500;
                                    remainingBalance = Math.max(0, totalAmount - paymentAmount);
                                }
                                
                                // Update form data with calculated values
                                formDataObj.payment_amount = paymentAmount.toFixed(2);
                                formDataObj.remaining_balance = remainingBalance.toFixed(2);
                                
                                // Prepare URL parameters
                                const params = new URLSearchParams();
                                
                                // Add guest information - ensure we have at least a default guest name
                                const firstName = formDataObj.first_name || '';
                                const lastName = formDataObj.last_name || '';
                                const guestName = `${firstName} ${lastName}`.trim() || 'Guest';
                                params.append('guest', guestName);
                                
                                // Add check-in/check-out dates
                                params.append('checkin', formDataObj.check_in || new Date().toISOString().split('T')[0]);
                                params.append('checkout', formDataObj.check_out || new Date(Date.now() + 86400000).toISOString().split('T')[0]);
                                
                                // Format guests string (e.g., "2 Adults, 1 Child")
                                const adults = parseInt(formDataObj.num_adults) || 1;
                                const children = parseInt(formDataObj.num_children) || 0;
                                let guestsStr = `${adults} ${adults === 1 ? 'Adult' : 'Adults'}`;
                                if (children > 0) {
                                    guestsStr += `, ${children} ${children === 1 ? 'Child' : 'Children'}`;
                                }
                                params.append('guests', guestsStr);
                                
                                // Add room information with fallback
                                if (data.items && data.items.length > 0) {
                                    const roomNames = data.items.map(item => 
                                        `${item.name || 'Room'} (${item.quantity || 1} ${(item.quantity || 1) === 1 ? 'room' : 'rooms'})`
                                    ).join(', ');
                                    params.append('room', roomNames);
                                } else {
                                    // Provide a default room name if no items
                                    params.append('room', 'Room (1)');
                                }
                                
                                // Add extra bed information
                                if (formDataObj.extra_bed && formDataObj.extra_bed !== '0') {
                                    params.append('extra_bed', 'yes');
                                }
                                
                                // Add pricing information
                                params.append('subtotal', totalAmount.toFixed(2));
                                params.append('total', totalAmount.toFixed(2));
                                params.append('downpayment', paymentAmount.toFixed(2));
                                params.append('balance', remainingBalance.toFixed(2));
                                
                                // Close the loading indicator and modal
                                Swal.close();
                                const bookingSummaryModal = bootstrap.Modal.getInstance(document.getElementById('bookingSummaryModal'));
                                if (bookingSummaryModal) {
                                    bookingSummaryModal.hide();
                                }

                            } else {
                                throw new Error('No rooms in booking list');
                            }
                        })
                        .catch(error => {
                            console.error('Error getting booking list:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to load booking details. Please try again.',
                                confirmButtonColor: '#3085d6'
                            });
                        });
                });
            }
            
            // Add event listener for terms modal
            const showTermsLink = document.getElementById('showTermsLink');
            const termsModalEl = document.getElementById('termsModal');
            const understandBtn = document.getElementById('understandBtn');
            
            if (termsModalEl) {
                const acceptTermsBtn = document.getElementById('acceptTermsBtn');
                const termsContainer = document.querySelector('.form-group.mt-3');
                
                // Handle accept terms button click
                if (acceptTermsBtn) {
                    acceptTermsBtn.addEventListener('click', function() {
                        // Enable and hide the understand button
                        if (understandBtn) {
                            understandBtn.style.display = 'none';
                        }
                        
                        // Hide the terms container
                        if (termsContainer) {
                            termsContainer.innerHTML = `
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle"></i> 
                                    You have accepted the Terms and Conditions
                                </div>
                            `;
                        }
                        
                        // Enable the confirm button
                        const confirmBtn = document.getElementById('confirmBookingBtn');
                        if (confirmBtn) {
                            confirmBtn.disabled = false;
                        }
                        
                        // Close the modal
                        const modal = bootstrap.Modal.getInstance(termsModalEl);
                        if (modal) {
                            modal.hide();
                        }
                    });
                }
                
                // When modal is shown, reset the state
                termsModalEl.addEventListener('show.bs.modal', function() {
                    if (understandBtn) {
                        understandBtn.style.display = 'block';
                        understandBtn.disabled = true;
                        understandBtn.classList.add('btn-outline-secondary');
                        understandBtn.innerHTML = 'I Understand the Terms and Conditions';
                    }
                });
            }
            
            if (showTermsLink) {
                showTermsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const termsModal = new bootstrap.Modal(termsModalEl);
                    termsModal.show();
                });
            }
            
            // Add click event for Proceed to Summary button
            document.getElementById('proceedToSummaryBtn').addEventListener('click', showBookingSummary);
            

            // Helper function to format date
            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            }
            // Add click event for Confirm Booking button
            document.getElementById('confirmBookingBtn').addEventListener('click', function() {
                // Submit the form when confirm is clicked
                document.getElementById('bookingForm').submit();
            });
        });
        // Helper function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        function openRatingModal(roomId) {
            // Check if user is logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login to submit a review');
                return;
            <?php endif; ?>
            
            document.getElementById('room_type_id').value = roomId;
            const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
            ratingModal.show();
        }

        // Star Rating Functionality
        document.querySelectorAll('.star-rating i').forEach(star => {
            star.addEventListener('mouseover', function() {
                const rating = this.dataset.rating;
                updateStars(rating);
            });
            
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.getElementById('selected_rating').value = rating;
                updateStars(rating, true);
            });
        });
        
        document.querySelector('.star-rating').addEventListener('mouseleave', function() {
            const selectedRating = document.getElementById('selected_rating').value;
            updateStars(selectedRating || 0);
        });
        
        function updateStars(rating, clicked = false) {
            document.querySelectorAll('.star-rating i').forEach(star => {
                const starRating = star.dataset.rating;
                if (starRating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                    if (clicked) star.classList.add('active');
                } else {
                    star.classList.remove('fas', 'active');
                    star.classList.add('far');
                }
            });
        }
        
        // Update the review form submission handler
        document.getElementById('ratingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Always use the current room ID
            formData.set('room_type_id', window.currentRoomId);
            
            // Validate rating
            if (!formData.get('rating')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rating Required',
                    text: 'Please select a rating before submitting',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
            
            // Show loading state
            Swal.fire({
                title: 'Submitting Review',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Debug log
            console.log('Submitting review for room:', window.currentRoomId);
            
            fetch('submit_rating.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close rating modal
                    const ratingModal = bootstrap.Modal.getInstance(document.getElementById('ratingModal'));
                    ratingModal.hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Thank You!',
                        text: 'Your review has been submitted successfully',
                        confirmButtonColor: '#ffc107'
                    }).then(() => {
                        // Refresh reviews
                        loadReviews(window.currentRoomId);
                        
                        // Reset form
                        this.reset();
                        updateStars(0);
                        
                        // Refresh the page to update room ratings
                        setTimeout(() => location.reload(), 1000);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to submit review. Please try again.',
                        confirmButtonColor: '#ffc107'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to submit review. Please try again later.',
                    confirmButtonColor: '#ffc107'
                });
            });
        });
        
        // Function to update payment fields visibility
        function updatePaymentFields() {
            const paymentOption = document.getElementById('payment_option').value;
            const customPaymentField = document.getElementById('customPaymentField');
            const customPaymentInput = document.getElementById('custom_payment');
            
            if (paymentOption === 'Custom Payment') {
                customPaymentField.style.display = 'block';
                customPaymentInput.required = true;
            } else {
                customPaymentField.style.display = 'none';
                customPaymentInput.required = false;
            }
        }
        
        // Track if terms have been viewed
        let termsViewed = false;

        // Function to enable confirm button when user clicks 'I Understand'
        function enableConfirmButton() {
            const confirmBtn = document.getElementById('confirmBookingBtn');
            const termsError = document.getElementById('termsError');
            const understandBtn = document.getElementById('understandBtn');
            
            if (!termsViewed) {
                termsError.textContent = 'Please view the Terms and Conditions first';
                termsError.classList.remove('d-none');
                return false;
            }
            
            if (confirmBtn) {
                confirmBtn.disabled = false;
                termsError.classList.add('d-none');
                understandBtn.classList.remove('btn-outline-primary');
                understandBtn.classList.add('btn-success');
                understandBtn.innerHTML = '<i class="fas fa-check-circle"></i> Terms Accepted';
            }
            
            return true;
        }

        // Initialize payment fields and event listeners on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePaymentFields();
            
            // Add input event listeners for guest count validation
            const numAdults = document.getElementById('num_adults');
            const numChildren = document.getElementById('num_children');
            const understandBtn = document.getElementById('understandBtn');
            const confirmBtn = document.getElementById('confirmBookingBtn');
            
            // Initialize confirm button as disabled
            if (confirmBtn) {
                confirmBtn.disabled = true;
            }
            
            // Add event listener for understand button
            if (understandBtn) {
                // Initially disable the understand button
                understandBtn.disabled = true;
                understandBtn.classList.add('btn-outline-secondary');
                
                // When clicking understand button, show terms modal
                understandBtn.addEventListener('click', function() {
                    const termsModal = new bootstrap.Modal(document.getElementById('termsModal'));
                    termsModal.show();
                });
            }
            
            // Validate on input with debounce
            let timeoutId;
            const validateInput = () => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(validateGuestCount, 300);
            };
            
            if (numAdults) numAdults.addEventListener('input', validateInput);
            if (numChildren) numChildren.addEventListener('input', validateInput);
        });
        
        // Function to validate guest count against room capacity
        function validateGuestCount() {
            const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
            const numChildren = parseInt(document.getElementById('num_children').value) || 0;
            const capacity = parseInt(document.getElementById('room_capacity').value) || 0;
            const warningElement = document.getElementById('capacityWarning');
            const totalGuests = numAdults + numChildren;
            
            if (totalGuests > capacity) {
                warningElement.classList.remove('d-none');
                // Show SweetAlert when capacity is exceeded
                Swal.fire({
                    icon: 'warning',
                    title: 'Capacity Exceeded',
                    html: `Total guests (${totalGuests}) exceeds the room's maximum capacity of ${capacity}.<br>Please adjust the number of guests.`,
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                });
                return false;
            } else {
                warningElement.classList.add('d-none');
                return true;
            }
        }

        // Function to validate booking form
        function validateBookingForm() {
            const form = document.getElementById('bookingForm');
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            // Check all required fields
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('invalid');
                }
            });
            
            // Validate dates
            const checkIn = new Date(document.getElementById('check_in').value);
            const checkOut = new Date(document.getElementById('check_out').value);
            
            if (checkIn >= checkOut) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Dates',
                    text: 'Check-out date must be after check-in date',
                    confirmButtonColor: '#ffc107'
                });
                return false;
            }
            
            // Get number of guests and validate
            const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
            const numChildren = parseInt(document.getElementById('num_children').value) || 0;
            const totalGuests = numAdults + numChildren;
            const roomCapacity = parseInt(document.getElementById('room_capacity').value) || 0;
            
            if (numAdults < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Adult Required',
                    text: 'At least one adult is required',
                    confirmButtonColor: '#ffc107'
                });
                return false;
            }
            
            // Check guest count against capacity
            if (!validateGuestCount()) {
                return false;
            }
            
            // Check if total guests exceed room capacity
            if (roomCapacity > 0 && totalGuests > roomCapacity) {
                alert(`Total number of guests (${totalGuests}) exceeds the room's maximum capacity of ${roomCapacity}.`);
                return false;
            }
            
            // Validate guest count
            if (!validateGuestCount()) {
                alert('Please adjust the number of guests to match the room capacity.');
                return false;
            }
            
            return isValid;
        }
        
        // Function to show booking summary
        function showBookingSummary() {
            if (!validateBookingForm()) {
                return;
            }
            
            const form = document.getElementById('bookingForm');
            const formData = new FormData(form);
            const summaryContent = document.getElementById('summaryContent');
            
            // Close the booking form modal
            const bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
            if (bookingModal) {
                bookingModal.hide();
            }
            
            // Calculate number of nights
            const checkIn = new Date(formData.get('check_in'));
            const checkOut = new Date(formData.get('check_out'));
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            
            // Get room details from the booking list
            fetch('get_booking_list.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.success || !data.items || data.items.length === 0) {
                        throw new Error('No rooms in booking list');
                    }
                    
                    // Collect guest types
                    let guestTypes = [];
                    const numGuests = parseInt(formData.get('num_adults') || '1');
                    console.log('Total guests to process:', numGuests);
                    
                    // First, try to get all guest types from the form data
                    for (let i = 1; i <= numGuests; i++) {
                        const guestType = formData.get(`guest_type_${i}`);
                        console.log(`Guest ${i} type:`, guestType);
                        
                        if (guestType && guestType !== 'regular') {
                            // Map database values to display names
                            const displayType = {
                                'senior': 'Senior Citizen',
                                'pwd': 'PWD',
                                'senior_citizen': 'Senior Citizen',
                                'Senior Citizen': 'Senior Citizen',
                                'PWD': 'PWD'
                            }[guestType] || guestType;
                            
                            if (!guestTypes.includes(displayType)) {
                                guestTypes.push(displayType);
                            }
                        }
                    }
                    
                    // If no guest types found, try alternative method to collect them
                    if (guestTypes.length === 0) {
                        console.log('No guest types found in form data, trying alternative method');
                        const guestTypeInputs = document.querySelectorAll('select[name^="guest_type_"]');
                        guestTypeInputs.forEach(input => {
                            if (input.value && input.value !== 'regular') {
                                const displayType = {
                                    'senior': 'Senior Citizen',
                                    'pwd': 'PWD',
                                    'senior_citizen': 'Senior Citizen',
                                    'Senior Citizen': 'Senior Citizen',
                                    'PWD': 'PWD'
                                }[input.value] || input.value;
                                
                                if (!guestTypes.includes(displayType)) {
                                    guestTypes.push(displayType);
                                }
                            }
                        });
                    }

                    // Calculate totals
                    let subtotal = 0;
                    let roomDetails = '';
                    
                    data.items.forEach(item => {
                        const roomTotal = item.price * item.quantity * nights;
                        subtotal += roomTotal;
                        
                        roomDetails += `
                            <div class="d-flex justify-content-between mb-2">
                                <span>${item.room_type} (${item.quantity} ${item.quantity > 1 ? 'rooms' : 'room'})</span>
                                <span>₱${(item.price * item.quantity).toLocaleString()} × ${nights} night${nights > 1 ? 's' : ''}</span>
                                <span>₱${roomTotal.toLocaleString()}</span>
                            </div>`;
                    });
                    
                    // Add extra bed if selected
                    const extraBedSelect = document.getElementById('extra_bed');
                    const extraBedOption = extraBedSelect.options[extraBedSelect.selectedIndex];
                    let extraBedCost = 0;
                    let extraBedHtml = '';
                    
                    if (extraBedOption.value) {
                        // Extract price from the option text (format: "Extra Bed (+₱X,XXX.XX)")
                        const priceMatch = extraBedOption.text.match(/\+₱([\d,]+(\.\d{2})?)/);
                        if (priceMatch) {
                            extraBedCost = parseFloat(priceMatch[1].replace(/,/g, '')) || 0;
                            extraBedHtml = `
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Extra Bed</span>
                                    <span>₱${extraBedCost.toLocaleString()}</span>
                                </div>`;
                            subtotal += extraBedCost;
                        }
                    }
                    
                    // Calculate discount if there are senior citizens or PWD guests
                    let discountAmount = 0;
                    let discountHtml = '';
                    // Check if there are any senior citizens or PWDs
                    const hasSeniorOrPwd = guestTypes.some(type => 
                        type === 'Senior Citizen' || type === 'PWD' || 
                        type === 'senior' || type === 'pwd' ||
                        type === 'senior_citizen'
                    );
                    
                    console.log('Guest types:', guestTypes);
                    console.log('Has senior/PWD:', hasSeniorOrPwd);
                    
                    if (hasSeniorOrPwd) {
                        // Apply 20% discount on the total amount (including extra bed charges)
                        discountAmount = subtotal * 0.2;
                        subtotal -= discountAmount;
                        
                        // Create discount text based on guest types
                        const discountTypes = [];
                        if (guestTypes.includes('Senior Citizen')) discountTypes.push('Senior Citizen');
                        if (guestTypes.includes('PWD')) discountTypes.push('PWD');
                        
                        discountHtml = `
                            <div class="d-flex justify-content-between mb-2 text-success discount-animation">
                                <span>Discount (20% for ${discountTypes.join('/')}):</span>
                                <span>-₱${discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                            </div>`;
                    }
                    
                    // Total after discount
                    const total = subtotal;
                    
                    // Get payment option
                    const paymentOption = formData.get('payment_option');
                    let paymentHtml = '';
                    let remainingBalance = 0;
                    let paymentAmount = 0;
                    
                    if (paymentOption === 'Partial Payment') {
                        paymentAmount = 1500;
                        remainingBalance = total - paymentAmount;
                        paymentHtml = `
                            <div class="d-flex justify-content-between mb-2">
                                <span>Required Down Payment:</span>
                                <span class="text-primary fw-bold">₱${paymentAmount.toLocaleString()}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Remaining Balance:</span>
                                <span class="text-danger fw-bold">₱${remainingBalance.toLocaleString()}</span>
                            </div>`;
                    } else if (paymentOption === 'Custom Payment') {
                        paymentAmount = parseFloat(document.getElementById('custom_payment').value) || 0;
                        
                        // Validate minimum payment amount
                        if (paymentAmount < 1500) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Amount',
                                text: 'Custom payment must be at least ₱1,500',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        
                        // Validate maximum payment amount
                        if (paymentAmount > total) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Amount Exceeds Total',
                                html: `The payment amount (₱${paymentAmount.toLocaleString()}) exceeds the total amount (₱${total.toLocaleString()}).<br><br>Would you like to pay the full amount?`,
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, pay full amount',
                                cancelButtonText: 'No, change amount'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('custom_payment').value = total.toFixed(2);
                                    showBookingSummary();
                                }
                            });
                            return false;
                        }
                        
                        if (paymentAmount >= total) {
                            paymentAmount = total;
                            paymentHtml = `
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Full Payment:</span>
                                    <span class="text-success fw-bold">₱${paymentAmount.toLocaleString()}</span>
                                </div>`;
                        } else {
                            remainingBalance = total - paymentAmount;
                            paymentHtml = `
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Custom Payment:</span>
                                    <span class="text-primary fw-bold">₱${paymentAmount.toLocaleString()}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Remaining Balance:</span>
                                    <span class="text-danger fw-bold">₱${remainingBalance.toLocaleString()}</span>
                                </div>`;
                        }
                    }
                    
                    // Check if user is logged in (by checking if contact fields exist in formData)
                    const hasContactInfo = formData.has('first_name');
                    let contactInfoHtml = '';
                    
                    // Only include contact info section for non-logged-in users
                    if (hasContactInfo) {
                        const firstName = formData.get('first_name') || '';
                        const lastName = formData.get('last_name') || '';
                        const email = formData.get('email') || '';
                        const phone = formData.get('phone') || '';
                        
                        contactInfoHtml = `
                            <h6 class="mb-3">Contact Information</h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong>Name:</strong> ${firstName} ${lastName}</p>
                                <p class="mb-1"><strong>Email:</strong> ${email}</p>
                                <p class="mb-1"><strong>Phone:</strong> ${phone}</p>
                            </div>`;
                    }
                    

                    
                    // Get guest information from form or session
                    let firstName = formData.get('first_name') || '';
                    let lastName = formData.get('last_name') || '';
                    
                    // If user is logged in, fetch their details from the database
                    <?php 
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        $userStmt = $pdo->prepare("SELECT first_name, last_name FROM userss WHERE id = ?");
                        $userStmt->execute([$user_id]);
                        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                        if ($user) {
                            echo 'if (!firstName) firstName = ' . json_encode($user['first_name']) . ' || \'\';' . "\n";
                            echo 'if (!lastName) lastName = ' . json_encode($user['last_name']) . ' || \'\';' . "\n";
                        }
                    }
                    ?>
                    
                    // Get payment method
                    const paymentMethod = formData.get('payment_method') || 'Not selected';
                    
                    // Format dates for display
                    const checkInStr = checkIn.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    const checkOutStr = checkOut.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    // Generate summary HTML
                    let summaryHtml = `
                        <div class="booking-summary">
                            <h5 class="mb-3">Booking Summary</h5>
                            <div class="mb-3">
                                <h6>Guest Information</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Name:</span>
                                    <span>${firstName} ${lastName}</span>
                                </div>
                                ${guestTypes.length > 0 ? `
                                <div class="d-flex justify-content-between">
                                    <span>Guest Type:</span>
                                    <span>${guestTypes.join(', ')}</span>
                                </div>` : ''}
                            </div>
                            <div class="mb-3">
                                <h6>Stay Details</h6>
                                <div class="d-flex justify-content-between">
                                    <span>Check-in:</span>
                                    <span>${checkInStr}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Check-out:</span>
                                    <span>${checkOutStr} (${nights} ${nights > 1 ? 'nights' : 'night'})</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Guests:</span>
                                    <span>${formData.get('num_adults')} Adult${formData.get('num_adults') > 1 ? 's' : ''} ${formData.get('num_children') > 0 ? `, ${formData.get('num_children')} Child${formData.get('num_children') > 1 ? 'ren' : ''}` : ''}</span>
                                </div>
                            </div>
                            <h6 class="mb-3">Booking Summary</h6>
                            ${roomDetails}
                            ${extraBedHtml}
                            
                            <div class="border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>₱${(subtotal + (discountAmount || 0)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                </div>
                                ${discountHtml}

                                <div class="d-flex justify-content-between mb-2 fw-bold">
                                    <span>Total Amount:</span>
                                    <span>₱${total.toLocaleString()}</span>
                                </div>
                                ${paymentHtml}
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> Please review your booking details before confirming.
                            </div>
                        </div>`;
                    
                    // Update summary content and show modal
                    summaryContent.innerHTML = summaryHtml;
                    
                    // Store totals in hidden fields
                    document.getElementById('total_amount').value = subtotal.toFixed(2);
                    document.getElementById('tax_amount').value = '0.00';
                    document.getElementById('grand_total').value = total.toFixed(2);
                    
                    // Store booking details in hidden fields for room_payment.php
                    const roomType = data.items[0]?.room_type || '';
                    const roomTypeId = data.items[0]?.room_type_id || '';
                    const roomPrice = parseFloat(data.items[0]?.price) || 0;
                    
                    document.getElementById('room_type').value = roomType;
                    document.getElementById('room_type_id').value = roomTypeId;
                    document.getElementById('room_price').value = roomPrice.toFixed(2);
                    document.getElementById('num_nights').value = nights;
                    document.getElementById('extra_bed_cost').value = extraBedCost.toFixed(2);
                    document.getElementById('payment_amount').value = paymentAmount.toFixed(2);
                    document.getElementById('remaining_balance').value = remainingBalance.toFixed(2);
                    
                    // Show the summary modal
                    const bookingSummaryModal = new bootstrap.Modal(document.getElementById('bookingSummaryModal'));
                    
                    // Handle back to form button
                    document.getElementById('backToFormBtn').onclick = function() {
                        bookingSummaryModal.hide();
                        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                        bookingModal.show();
                    };
                    
                    // Handle confirm & pay button
                    document.getElementById('confirmBookingBtn').onclick = function() {
                        // Check if terms are accepted
                        const confirmBtn = document.getElementById('confirmBookingBtn');
                        const termsError = document.getElementById('termsError');
                        
                        if (confirmBtn.disabled) {
                            termsError.classList.remove('d-none');
                            return;
                        }
                        
                        // Submit the form
                        const form = document.getElementById('bookingForm');
                        
                        // Add a hidden field for the booking data
                        let bookingData = {
                            check_in: formData.get('check_in'),
                            check_out: formData.get('check_out'),
                            num_nights: document.getElementById('num_nights').value,
                            num_adults: formData.get('num_adults'),
                            num_children: formData.get('num_children') || 0,
                            room_type: document.getElementById('room_type').value,
                            room_price: document.getElementById('room_price').value,
                            extra_bed: document.getElementById('extra_bed').value,
                            extra_bed_cost: document.getElementById('extra_bed_cost').value,
                            payment_method: formData.get('payment_method'),
                            payment_option: formData.get('payment_option'),
                            total_amount: document.getElementById('grand_total').value,
                            payment_amount: document.getElementById('payment_amount').value || document.getElementById('grand_total').value,
                            remaining_balance: document.getElementById('remaining_balance').value || 0
                        };
                        
                        // Add guest information
                        const guestInfo = [];
                        const numGuests = parseInt(formData.get('num_adults') || '1');
                        
                        for (let i = 0; i < numGuests; i++) {
                            const guest = {
                                first_name: formData.get(`guest_first_name_${i}`) || '',
                                last_name: formData.get(`guest_last_name_${i}`) || '',
                                contact: formData.get(`guest_contact_${i}`) || '',
                                email: formData.get(`guest_email_${i}`) || '',
                                guest_type: formData.get(`guest_type_${i}`) || 'regular',
                                id_number: formData.get(`guest_id_number_${i}`) || '',
                                id_type: formData.get(`guest_id_type_${i}`) || ''
                            };
                            guestInfo.push(guest);
                        }
                        
                        bookingData.guest_info = guestInfo;
                        
                        // Add booking data as a hidden field
                        let bookingDataInput = document.createElement('input');
                        bookingDataInput.type = 'hidden';
                        bookingDataInput.name = 'booking_data';
                        bookingDataInput.value = JSON.stringify(bookingData);
                        form.appendChild(bookingDataInput);
                        
                        // Get room_type_id from the form or data
                        const roomTypeId = document.getElementById('room_type_id').value || 
                                         (data.items && data.items[0] && data.items[0].room_type_id) || '';
                        
                        // Update form action to include room_type_id as URL parameter
                        const url = new URL(form.action);
                        url.searchParams.set('room_type_id', roomTypeId);
                        form.action = url.toString();
                        
                        console.log('Submitting form to:', form.action);
                        
                        // Submit the form
                        form.submit();
                    };
                    
                    bookingSummaryModal.show();
                    
                })
                .catch(error => {
                    console.error('Error loading booking list:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load booking summary. Please try again.',
                        confirmButtonColor: '#ffc107'
                    });
                });
        }
        
        // Load Reviews
        function loadReviews(roomId) {
            console.log('Loading reviews for room:', roomId);
            
            // Show loading state
            const reviewsList = document.getElementById('reviewsList');
            reviewsList.innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Loading reviews...</p>
                </div>
            `;

            fetch(`get_reviews.php?room_type_id=${roomId}`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(reviews => {
                    console.log('Received reviews:', reviews);
                    
                    if (!Array.isArray(reviews)) {
                        throw new Error('Invalid response format');
                    }

                    if (reviews.length === 0) {
                        reviewsList.innerHTML = `
                            <div class="text-center p-4">
                                <i class="far fa-comment-alt mb-2" style="font-size: 24px;"></i>
                                <p class="text-muted mb-0">No reviews yet. Be the first to review this room!</p>
                            </div>
                        `;
                        return;
                    }
                    
                    reviewsList.innerHTML = reviews.map(review => `
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold">${escapeHtml(review.username || 'Anonymous User')}</div>
                                    <div class="rating">
                                        ${generateStars(review.rating)}
                                    </div>
                                </div>
                                <small class="text-muted">${review.formatted_date}</small>
                            </div>
                            <p class="mt-2 mb-0">${escapeHtml(review.review || '')}</p>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading reviews:', error);
                    reviewsList.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            Error loading reviews: ${error.error || error.message || 'Unknown error'}
                            ${error.details ? `<br><small>${error.details}</small>` : ''}
                        </div>
                    `;
                });
        }
        
        function generateStars(rating) {
            let stars = '';
            for(let i = 1; i <= 5; i++) {
                if(i <= rating) {
                    stars += '<i class="fas fa-star text-warning"></i>';
                } else if(i - 0.5 <= rating) {
                    stars += '<i class="fas fa-star-half-alt text-warning"></i>';
                } else {
                    stars += '<i class="far fa-star text-warning"></i>';
                }
            }
            return stars;
        }

        function viewDetails(roomId) {
            console.log('Opening details for room:', roomId); // Debug log
            const room = <?php echo json_encode($rooms); ?>.find(r => r.room_type_id == roomId);
            
            if (room) {
                // Store current room ID for review functionality
                window.currentRoomId = roomId;
                
                // Update modal content
                document.getElementById('modalRoomType').textContent = room.room_type;
                document.getElementById('modalPrice').textContent = `₱${parseFloat(room.price).toLocaleString()} per night`;
                document.getElementById('modalDescription').textContent = room.description;
                document.getElementById('modalCapacity').textContent = `Max capacity: ${room.capacity} guests`;
                document.getElementById('modalBeds').textContent = room.beds;
                
                // Update carousel images
                document.getElementById('modalRoomImage1').src = room.image || 'https://srv1760-files.hstgr.io/112f0151d91cad47/files/public_html/Admin/uploads/rooms/default.jpg';
                document.getElementById('modalRoomImage2').src = room.image2 || 'https://srv1760-files.hstgr.io/112f0151d91cad47/files/public_html/Admin/uploads/rooms/default2.jpg';
                document.getElementById('modalRoomImage3').src = room.image3 || 'https://srv1760-files.hstgr.io/112f0151d91cad47/files/public_html/Admin/uploads/rooms/default3.jpg';
                
                // Update rating stars
                const rating = parseFloat(room.rating) || 0;
                let starsHtml = '';
                for(let i = 1; i <= 5; i++) {
                    if(i <= rating) {
                        starsHtml += '<i class="fas fa-star text-warning"></i>';
                    } else if(i - 0.5 <= rating) {
                        starsHtml += '<i class="fas fa-star-half-alt text-warning"></i>';
                    } else {
                        starsHtml += '<i class="far fa-star text-warning"></i>';
                    }
                }
                starsHtml += `<span class="text-muted ms-2">(${rating})</span>`;
                document.getElementById('modalRating').innerHTML = starsHtml;
                
                // Load reviews for this room
                loadReviews(roomId);
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('roomDetailsModal'));
                modal.show();
            }
        }

        // Add event listener for Write Review button
        document.getElementById('writeReviewBtn').addEventListener('click', function() {
            if (!window.currentRoomId) {
                alert('Error: Room not found');
                return;
            }
            openRatingModal(window.currentRoomId);
        });

        // Make sure the updateBadgeCount function is properly defined
        function updateBadgeCount(count) {
            // Update all booking badges (mobile and desktop)
            const badges = document.querySelectorAll('.booking-badge');
            badges.forEach(badge => {
                if (count > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = count;
                    // Add animation
                    badge.style.animation = 'none';
                    badge.offsetHeight; // Trigger reflow
                    badge.style.animation = 'badgePulse 0.3s ease-in-out';
                } else {
                    badge.style.display = 'none';
                }
            });
            
            // Also update any booking list icons
            const bookingListIcons = document.querySelectorAll('.booking-list-icon');
            bookingListIcons.forEach(icon => {
                if (count > 0) {
                    icon.classList.add('has-items');
                } else {
                    icon.classList.remove('has-items');
                }
            });
            
            console.log('Badge count updated:', count); // Add this for debugging
        }

        // Make sure to call updateBadgeCount when adding to list
        function addToList(room) {
            // Create a simple form submission instead of fetch API
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'add_to_list.php';
            form.style.display = 'none';
            
            const roomIdInput = document.createElement('input');
            roomIdInput.type = 'hidden';
            roomIdInput.name = 'room_type_id';
            roomIdInput.value = room.room_type_id;
            
            form.appendChild(roomIdInput);
            document.body.appendChild(form);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Add to List',
                text: `Add ${room.room_type} to your booking list?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator
                    Swal.fire({
                        title: 'Adding to list...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit the form
                    form.submit();
                } else {
                    // Remove the form if cancelled
                    document.body.removeChild(form);
                }
            });
        }

        // Add this to handle the redirect after adding to list
        document.addEventListener('DOMContentLoaded', function() {
            // Check for success message in URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('add_success');
            const message = urlParams.get('message');
            const count = urlParams.get('count');
            
            if (success === 'true') {
                                // Update badge count
                if (count) {
                    updateBadgeCount(parseInt(count));
                }

                // Show success message
                                Swal.fire({
                    title: 'Success',
                    text: message || 'Room added to booking list',
                                    icon: 'success',
                                    confirmButtonColor: '#ffc107',
                                    showConfirmButton: true,
                                    confirmButtonText: 'View Booking List',
                                    showCancelButton: true,
                    cancelButtonText: 'Continue Shopping',
                    allowOutsideClick: true,
                    didClose: () => {
                        // Remove the parameters from URL and refresh the page
                        window.location.href = window.location.pathname;
                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Show the booking list if user clicks "View Booking List"
                                        showBookingList();
                            } else {
                        // Refresh the page for any other result
                        window.location.reload();
                    }
                });
            } else if (success === 'false') {
                // Show error as a notice instead of an alert
                Swal.fire({
                    title: 'Notice',
                    text: message || 'Could not add room to your list.',
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didClose: () => {
                        // Remove the parameters from URL and refresh the page
                        window.location.href = window.location.pathname;
                }
            });
        }
        });

        // Fix the showBookingList function to handle undefined values
        function showBookingList() {
            const bookingListModal = document.getElementById('bookingListModal');
            const bookingListContent = document.getElementById('bookingListContent');
            const proceedToBookingBtn = document.querySelector('.proceed-to-booking-btn');
            
            // Show loading indicator
            bookingListContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Loading your booking list...</p>
                </div>
            `;

            // Initialize and show the modal
            const modal = new bootstrap.Modal(bookingListModal);
            modal.show();

            // Fetch the booking list data
            fetch('get_booking_list.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.items && data.items.length > 0) {
                            // Generate HTML for items
                            let html = '';
                            data.items.forEach(item => {
                                html += `
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <img src="${getImagePath(item.image)}" 
                                                         alt="${item.room_type}" 
                                                         class="img-thumbnail" 
                                                         style="width: 100px; height: 80px; object-fit: cover;"
                                                         onerror="this.src='../../../Admin/uploads/rooms/default.jpg'">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <h5 class="card-title">${item.room_type}</h5>
                                                        <button type="button" class="btn-close" onclick="removeFromList(${item.room_type_id})"></button>
                                                    </div>
                                                     <p class="card-text mb-0">Added on: ${item.added_date}</p>
                                                     <p class="card-text mb-0">₱${parseFloat(item.price).toLocaleString()} per night</p>
                                                     <p class="card-text mb-0"><i class="fas fa-user-friends"></i> Max capacity: ${item.capacity} guests</p>

                                                     
                                                     <!-- Room Quantity Controls -->
                                                     <div class="mt-2 d-flex align-items-center">
                                                         <label class="me-2"><strong>Quantity:</strong></label>
                                                         <div class="input-group" style="width: 130px;">
                                                             <button type="button" class="btn btn-outline-secondary btn-sm decrement-btn" 
                                                                 data-room-id="${item.room_type_id}" 
                                                                 ${parseInt(item.quantity || 1) <= 1 ? 'disabled' : ''}>
                                                                 <i class="fas fa-minus"></i>
                                                             </button>
                                                             <span class="form-control form-control-sm text-center quantity-value" 
                                                                 data-room-id="${item.room_type_id}">${item.quantity || 1}</span>
                                                             <button type="button" class="btn btn-outline-secondary btn-sm increment-btn" 
                                                                 data-room-id="${item.room_type_id}" 
                                                                 ${parseInt(item.quantity || 1) >= item.room_number ? 'disabled' : ''}>
                                                                 <i class="fas fa-plus"></i>
                                                             </button>
                                                         </div>
                                                     </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            // Add total amount section
                            html += `
                                <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Total Amount:</h5>
                                    <h5 class="card-title mb-0">₱${parseFloat(data.totalAmount).toLocaleString()} per night</h5>
                                </div>
                            </div>
                        </div>
                    `;

                            bookingListContent.innerHTML = html;
                            if (proceedToBookingBtn) proceedToBookingBtn.disabled = false;
                            
                            // Add event listeners to quantity buttons after rendering the HTML
                            document.querySelectorAll('.increment-btn').forEach(button => {
                                button.addEventListener('click', function() {
                                    const roomId = this.getAttribute('data-room-id');
                                    const quantitySpan = document.querySelector(`.quantity-value[data-room-id="${roomId}"]`);
                                    const currentQuantity = parseInt(quantitySpan.textContent);
                                    updateQuantity(roomId, currentQuantity + 1);
                                });
                            });
                            
                            document.querySelectorAll('.decrement-btn').forEach(button => {
                                button.addEventListener('click', function() {
                                    const roomId = this.getAttribute('data-room-id');
                                    const quantitySpan = document.querySelector(`.quantity-value[data-room-id="${roomId}"]`);
                                    const currentQuantity = parseInt(quantitySpan.textContent);
                                    if (currentQuantity > 1) {
                                        updateQuantity(roomId, currentQuantity - 1);
                                    }
                                });
                            });
                        } else {
                            // Show empty list message
                            bookingListContent.innerHTML = `
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-shopping-cart text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted">Your booking list is empty</h5>
                                    <p class="text-muted">Add rooms to your list to start booking</p>
                                </div>
                            `;
                            if (proceedToBookingBtn) proceedToBookingBtn.disabled = true;
                        }
                    } else {
                throw new Error(data.message || 'Error loading booking list');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            bookingListContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message || 'Error loading booking list. Please try again.'}
                </div>
            `;
            if (proceedToBookingBtn) proceedToBookingBtn.disabled = true;
        });
        }

        // Update the updateQuantity function to handle server-side availability errors
        function updateQuantity(roomTypeId, newQuantity) {
            // Show loading indicator
            const quantitySpan = document.querySelector(`.quantity-value[data-room-id="${roomTypeId}"]`);
            const originalText = quantitySpan ? quantitySpan.textContent : '';
            
            if (quantitySpan) {
                quantitySpan.innerHTML = '<small><i class="fas fa-spinner fa-spin"></i></small>';
            }
            
            // Ensure roomTypeId is a valid number
            if (!roomTypeId || isNaN(parseInt(roomTypeId))) {
                console.error('Invalid room ID:', roomTypeId);
                if (quantitySpan) {
                    quantitySpan.textContent = originalText;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: 'Invalid room ID. Please refresh the page and try again.',
                    icon: 'error',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }
            
            // Ensure newQuantity is a valid positive number
            if (isNaN(newQuantity) || newQuantity < 1) {
                console.error('Invalid quantity:', newQuantity);
                if (quantitySpan) {
                    quantitySpan.textContent = originalText;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: 'Invalid quantity. Please try again.',
                    icon: 'error',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }
            
            // Send request to update quantity
            const formData = new FormData();
            formData.append('room_type_id', roomTypeId);
            formData.append('quantity', newQuantity);
            formData.append('action', 'update_quantity'); // Add action parameter to clarify the request
            
            // Add debug logging
            console.log('Sending update request:', {
                room_type_id: roomTypeId,
                quantity: newQuantity,
                action: 'update_quantity'
            });
            
            fetch('update_list_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Server error: ' + text);
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Update quantity response:', data);
                
                if (data.success) {
                    // Update the quantity display
                    if (quantitySpan) {
                        quantitySpan.textContent = data.quantity;
                    }
                    
                    // Simplest solution: just refresh the booking list to update everything
                    console.log('Successfully updated quantity. Total amount:', data.totalAmount);
                    
                    // Full refresh of booking list to update all data properly
                    showBookingList();
                    
                    // Update button states
                    const decrementBtn = document.querySelector(`.decrement-btn[data-room-id="${roomTypeId}"]`);
                    
                    if (decrementBtn) {
                        decrementBtn.disabled = data.quantity <= 1;
                    }
                    
                    // If the server indicates we've reached the maximum available rooms
                    if (data.message && data.message.includes('maximum')) {
                            Swal.fire({
                                title: 'Maximum Reached',
                            text: data.message || 'You have selected all available rooms of this type.',
                                icon: 'info',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                    }
                } else {
                    // Restore original quantity on error
                    if (quantitySpan) {
                        quantitySpan.textContent = originalText;
                    }
                    
                    // Check for specific error messages about availability
                    if (data.message && (data.message.includes('available') || data.message.includes('maximum'))) {
                        Swal.fire({
                            title: 'Maximum Reached',
                            text: data.message || 'No more rooms available of this type.',
                            icon: 'warning',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else if (data.message && data.message.includes('not in booking list')) {
                        // Handle the specific "Room not in booking list" error
                        Swal.fire({
                            title: 'Room Not Found',
                            text: 'This room is no longer in your booking list. Refreshing your list...',
                            icon: 'warning',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        
                        // Refresh the booking list after a short delay
                        setTimeout(() => {
                            showBookingList();
                        }, 2000);
                    } else {
                        // Show generic error message
                    Swal.fire({
                        title: 'Update Failed',
                        text: data.message || 'Could not update room quantity.',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    }
                }
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
                
                // Restore original quantity on error
                if (quantitySpan) {
                    quantitySpan.textContent = originalText;
                }
                
                // Show a more user-friendly error message
                Swal.fire({
                    title: 'Update Failed',
                    text: 'Could not update room quantity. Please try again later.',
                    icon: 'warning',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        }


        // Function to remove a room from the booking list
        function removeFromList(roomTypeId) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Remove Room',
                text: 'Are you sure you want to remove this room from your booking list?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form data
                    const formData = new FormData();
                    formData.append('room_type_id', roomTypeId);

                    // Show loading indicator
                    Swal.fire({
                        title: 'Removing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send request to remove room
                    fetch('remove_from_list.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update badge count
                            if (typeof data.count !== 'undefined') {
                                updateBadgeCount(data.count);
                            }

                            // Show success message
                            Swal.fire({
                                title: 'Removed!',
                                text: 'Room has been removed from your booking list',
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });

                            // If the booking list modal is open, refresh it
                            const bookingListModal = document.getElementById('bookingListModal');
                            if (bookingListModal && bookingListModal.classList.contains('show')) {
                                showBookingList();
                            }

                            // If count is 0, close the modal after a short delay
                            if (data.count === 0) {
                                setTimeout(() => {
                                    const modal = bootstrap.Modal.getInstance(bookingListModal);
                                    if (modal) {
                                        modal.hide();
                                    }
                                }, 1000);
                            }
                        } else {
                            throw new Error(data.message || 'Failed to remove room');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Could not remove room. Please try again.',
                            icon: 'error'
                        });
                    });
                }
            });
        }

        // Function to update the booking summary
        function updateBookingSummary(bookingItems) {
            const summaryElement = document.getElementById('bookingSummaryContent');
            if (!summaryElement) {
                console.error('Booking summary element not found');
                return;
            }
            
            let totalAmount = 0;
            const summaryHtml = bookingItems.map(item => {
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity || 1);
                totalAmount += itemTotal;
                return `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>${item.room_type} (${item.quantity || 1})</span>
                        <span>₱${itemTotal.toLocaleString()} per night</span>
                    </div>
                `;
            }).join('');
            
            summaryElement.innerHTML = `
                ${summaryHtml}
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <strong>Total Amount per Night:</strong>
                    <strong>₱${totalAmount.toLocaleString()}</strong>
                </div>
            `;
            
            return totalAmount;
        }

        // Function to handle proceeding to booking
        function proceedToBooking() {
            // Show loading indicator
            Swal.fire({
                title: 'Checking your booking list',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // First check if booking list is empty
            fetch('get_booking_list.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Booking list check response:', data); // Debug log
                    
                    // Close loading indicator
                    Swal.close();
                    
                    // Check if booking list is empty
                    if (!data.success || !data.items || data.items.length === 0) {
                        Swal.fire({
                            title: 'Empty Booking List',
                            text: 'You cannot proceed to booking with an empty list. Please add rooms to your booking list first.',
                            icon: 'warning',
                            confirmButtonColor: '#ffc107'
                        });
                        return;
                    }
                    
                    // Continue with booking process if list is not empty
            // Close the booking list modal
                    const bookingListModal = document.getElementById('bookingListModal');
                    if (bookingListModal) {
                        const bsModal = bootstrap.Modal.getInstance(bookingListModal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }

            // Set minimum dates for check-in and check-out
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const checkIn = document.getElementById('check_in');
            const checkOut = document.getElementById('check_out');
            
                    if (checkIn && checkOut) {
            checkIn.min = today.toISOString().split('T')[0];
            checkOut.min = tomorrow.toISOString().split('T')[0];
            
            // Clear any previous values
            checkIn.value = '';
            checkOut.value = '';
                    }

                    // Update booking summary
                    updateBookingSummary(data.items);

                    // Show the booking form modal
                    const bookingModal = document.getElementById('bookingModal');
                    if (bookingModal) {
                        const bsModal = new bootstrap.Modal(bookingModal);
                        bsModal.show();
                        
                        // Initialize the booking form after the modal is shown
                        setTimeout(() => {
                        updateGuestFields();

                            // Set up event listener for number of guests change
                            const numGuestsInput = document.getElementById('num_adults');
                            if (numGuestsInput) {
                                numGuestsInput.addEventListener('change', updateGuestFields);
                                numGuestsInput.addEventListener('input', updateGuestFields);
                            }
                        }, 500);
                    } else {
                        console.error('Booking modal not found');
                        Swal.fire({
                            title: 'Error',
                            text: 'Could not find booking form. Please refresh the page and try again.',
                            icon: 'error',
                            confirmButtonColor: '#ffc107'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking booking list:', error);
                    
                    // Close loading indicator
                    Swal.close();
                    
                    Swal.fire({
                        title: 'Error',
                        text: 'Could not check booking list. Please try again later.',
                        icon: 'error',
                        confirmButtonColor: '#ffc107'
                    });
                });
        }

        // Make sure the event listener is properly attached
        document.addEventListener('DOMContentLoaded', function() {
            // Attach event listener to proceed to booking button
            const proceedToBookingBtn = document.querySelector('.proceed-to-booking-btn');
            if (proceedToBookingBtn) {
                proceedToBookingBtn.addEventListener('click', proceedToBooking);
                console.log('Proceed to booking button listener attached');
            }
            
            // Also attach the event to the button directly in case the class selector doesn't work
            const allButtons = document.querySelectorAll('button');
            allButtons.forEach(button => {
                if (button.textContent.trim() === 'Proceed to Booking') {
                    button.addEventListener('click', proceedToBooking);
                    console.log('Proceed to booking button found by text and listener attached');
                }
            });
            
            // Initialize booking form when modal is shown
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('shown.bs.modal', function() {
                    console.log('Booking modal shown, initializing form');
                    updateGuestFields();
                    
                    // Set up event listener for number of guests change
                    const numGuestsInput = document.getElementById('num_adults');
                    if (numGuestsInput) {
                        numGuestsInput.addEventListener('change', updateGuestFields);
                        numGuestsInput.addEventListener('input', updateGuestFields);
                    }
                });
            }
            
            // Set up event listener for confirm booking button
            const confirmBookingBtn = document.getElementById('confirmBookingBtn');
            if (confirmBookingBtn) {
                confirmBookingBtn.addEventListener('click', function() {
                    // Validate the form
                    const bookingForm = document.getElementById('bookingForm');
                    if (bookingForm.checkValidity()) {
                        // Show booking summary confirmation first
                        showBookingSummaryConfirmation();
                    } else {
                        // Trigger form validation
                        bookingForm.reportValidity();
                    }
                });
            }
        });

        // Function to generate a unique reference number
        function generateReferenceNumber() {
            const prefix = 'CAS';
            const timestamp = new Date().getTime().toString().slice(-6);
            const random = Math.floor(1000 + Math.random() * 9000);
            return `${prefix}${timestamp}${random}`;
        }



        // Function to handle the confirm booking button click
        function handleConfirmBookingClick() {
            // Validate the form
            const bookingForm = document.getElementById('bookingForm');
            
            // Basic validations
            if (!validateBookingForm()) {
                return;
            }
            
            if (bookingForm.checkValidity()) {
                // Show the booking summary
                showBookingSummary();
            } else {
                bookingForm.reportValidity();
            }
        }


        // Helper function to show error messages
        function showError(message) {
                Swal.fire({
                title: 'Validation Error',
                text: message,
                    icon: 'warning',
                    confirmButtonColor: '#ffc107'
                });
        }

        // Helper function to format date
        function formatDate(dateString) {
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Add event listener for confirm booking button
        document.addEventListener('DOMContentLoaded', function() {
            const confirmBookingBtn = document.getElementById('confirmBookingBtn');
            if (confirmBookingBtn) {
                confirmBookingBtn.addEventListener('click', handleConfirmBookingClick);
            }
        });

        // Add this helper function for copying text
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Payment number copied to clipboard',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // Add event listeners for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const checkIn = document.getElementById('check_in');
            const checkOut = document.getElementById('check_out');
            const paymentMethod = document.getElementById('payment_method');



            if (checkIn && checkOut) {
                checkIn.addEventListener('change', function() {
                    // Set minimum check-out date to day after check-in
                    const minCheckOut = new Date(this.value);
                    minCheckOut.setDate(minCheckOut.getDate() + 1);
                    checkOut.min = minCheckOut.toISOString().split('T')[0];
                    
                    // If check-out date is before new minimum, update it
                    if (new Date(checkOut.value) <= new Date(this.value)) {
                        checkOut.value = minCheckOut.toISOString().split('T')[0];
                    }
                    updateTotalAmount();
                });

                checkOut.addEventListener('change', updateTotalAmount);
            }
        });



        // Update the updateGuestFields function to include user information fields when not logged in

        function updateGuestFields() {
            const numGuests = parseInt(document.getElementById('adults').value) || 0;
            const guestFieldsContainer = document.getElementById('guestFieldsContainer');
            
            let html = '';
            
            // Add user information fields if not logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                html += `
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title">Booking Contact Information</h6>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> Please provide your contact information for this booking
                            </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_firstname" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_lastname" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="user_email" required
                                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                <div class="form-text">We'll send your booking confirmation here</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="user_contact" required
                                       pattern="[0-9]{11}"
                                       placeholder="e.g. 09123456789">
                                <div class="form-text">Please enter a valid 11-digit phone number</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            <?php endif; ?>

            html += `<h6 class="mb-3">Guest Information</h6>`;
            
            // Rest of your existing guest fields code...
            for (let i = 0; i < numGuests; i++) {
                html += `
                    <div class="guest-info-card card mb-3">
                            <div class="card-body">
                            <h6 class="card-title">Guest ${i + 1}</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="guest_firstname_${i}" required>
                                    </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="guest_lastname_${i}" required>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Guest Type</label>
                                    <select class="form-select" name="guest_type_${i}" onchange="handleGuestTypeChange(this, ${i})" required>
                                        <option value="regular">Regular</option>
                                        <option value="pwd">PWD</option>
                                        <option value="senior">Senior Citizen</option>
                                        </select>
                                    </div>
                                <div class="col-md-6 mb-2 id-number-container-${i}" style="display: none;">
                                    <label class="form-label">ID Number</label>
                                    <input type="text" class="form-control" name="guest_id_number_${i}">
                                    <div class="invalid-feedback">
                                        Please enter a valid ID number in the correct format.
                                </div>
                                    </div>
                            </div>
                            <div class="row id-upload-container-${i}" style="display: none;">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Upload ID</label>
                                    <input type="file" class="form-control" name="guest_id_upload_${i}" accept="image/*">
                                    <div class="form-text">Please upload a clear photo of the ID</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
    
            
            guestFieldsContainer.innerHTML = html;

            // Add validation for contact number
            const contactInput = document.querySelector('input[name="user_contact"]');
            if (contactInput) {
                contactInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
                    if (this.value.length === 11) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
            }

            // Add validation for email
            const emailInput = document.querySelector('input[name="user_email"]');
            if (emailInput) {
                emailInput.addEventListener('input', function(e) {
                    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (emailPattern.test(this.value)) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
            }
        }


        // Function to calculate the number of nights between two dates
        function calculateNights(checkInDate, checkOutDate) {
            if (!checkInDate || !checkOutDate) return 0;
            
            const startDate = new Date(checkInDate);
            const endDate = new Date(checkOutDate);
            
            // Calculate the time difference in milliseconds
            const timeDiff = endDate.getTime() - startDate.getTime();
            
            // Convert time difference to days
            const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            return nights > 0 ? nights : 0;
        }



        // Add function to update total when dates change
        function updateTotalAmount() {
            const checkIn = new Date(document.getElementById('check_in').value);
            const checkOut = new Date(document.getElementById('check_out').value);
            
            if (checkIn && checkOut && checkOut > checkIn) {
                updatePaymentSummary();
            }
        }

        // Add these functions back
        function handlePaymentMethodChange(select) {
            const container = document.getElementById('paymentDetailsContainer');
            const accountNumber = document.getElementById('selectedAccountNumber');
            const accountName = document.getElementById('selectedAccountName');
            const qrCode = document.getElementById('qrCodeImage');
            const paymentReference = document.querySelector('input[name="payment_reference"]');
            const paymentProof = document.querySelector('input[name="payment_proof"]');
            
            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                accountNumber.textContent = selectedOption.dataset.number;
                accountName.textContent = selectedOption.dataset.name;
                
                if (selectedOption.dataset.qr) {
                    qrCode.src = selectedOption.dataset.qr;
                    qrCode.style.display = 'block';
                } else {
                    qrCode.style.display = 'none';
                }
                
                // Add real-time validation for payment reference
                paymentReference.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        Swal.fire({
                            title: 'Required Field',
                            text: 'Please enter the payment reference number',
                            icon: 'warning',
                            confirmButtonColor: '#ffc107',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });

                // Add real-time validation for payment proof
                paymentProof.addEventListener('change', function() {
                    if (!this.files || this.files.length === 0) {
                        Swal.fire({
                            title: 'Required Field',
                            text: 'Please upload your payment proof',
                            icon: 'warning',
                            confirmButtonColor: '#ffc107',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        handlePaymentProofUpload(this);
                    }
                });
                
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        function copyAccountNumber() {
            const accountNumber = document.getElementById('selectedAccountNumber').textContent;
            navigator.clipboard.writeText(accountNumber).then(() => {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Account number copied to clipboard',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }




        // Call setup function when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            setupPaymentValidation();
        });

        // Add this function after your existing JavaScript code
        function showPaymentProof(imageUrl) {
            Swal.fire({
                title: 'Payment Proof',
                html: `
                    <div class="payment-proof-modal">
                        <img src="${imageUrl}" alt="Payment Proof" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
                    </div>
                `,
                width: '800px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    container: 'payment-proof-preview-modal'
                }
            });
        }

        // Add this JavaScript function to handle form data
        function saveFormData() {
            const form = document.getElementById('bookingForm');
            const formData = new FormData(form);
            const data = {};
            
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Save form data to sessionStorage
            sessionStorage.setItem('bookingFormData', JSON.stringify(data));
        }

        // Add function to restore form data
        function restoreFormData() {
            // Check for data in sessionStorage first
            const storedData = sessionStorage.getItem('bookingFormData');
            if (storedData) {
                const data = JSON.parse(storedData);
                const form = document.getElementById('bookingForm');
                
                // Restore form fields
                Object.keys(data).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = data[key];
                    }
                });
                
                // Update any dependent fields or calculations
                updateTotalAmount();
                return;
            }
            
            // If no sessionStorage data, check PHP session data
            <?php if (isset($_SESSION['booking_form_data'])): ?>
            const sessionData = <?php echo json_encode($_SESSION['booking_form_data']); ?>;
            const form = document.getElementById('bookingForm');
            
            Object.keys(sessionData).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = sessionData[key];
                }
            });
            
            // Update calculations
            updateTotalAmount();
            <?php endif; ?>
        }

        // Add event listeners for form fields
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bookingForm');
            
            // Restore form data when page loads
            restoreFormData();
            
            // Save form data when fields change
            form.addEventListener('change', saveFormData);
            
            // Save form data before page unload
            window.addEventListener('beforeunload', saveFormData);
            
            // Add offline/online handlers
            window.addEventListener('offline', function() {
                Swal.fire({
                    title: 'You are offline!',
                    text: 'Don\'t worry, your booking information has been saved.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107'
                });
            });
            
            window.addEventListener('online', function() {
                Swal.fire({
                    title: 'You\'re back online!',
                    text: 'You can continue with your booking.',
                    icon: 'success',
                    confirmButtonColor: '#ffc107'
                });
            });
        });

        // Modify your success handler to clear saved data
        function handleBookingSuccess() {
            // Clear stored form data
            sessionStorage.removeItem('bookingFormData');
            
            // Make an AJAX call to clear PHP session data
            fetch('roomss.php?clear_form=true')
                .then(() => {
                    // Redirect or show success message
                    window.location.href = 'mybookings.php';
                });
        }

        // Function to handle "Add to List" button clicks
        function setupAddToListButtons() {
            const addToListButtons = document.querySelectorAll('.add-to-list-btn');
            
            addToListButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const roomTypeId = this.getAttribute('data-room-id');
                    if (!roomTypeId) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Room ID not found',
                            icon: 'error',
                            confirmButtonColor: '#ffc107'
                        });
                        return;
                    }
                    
                    // Create form data
                    const formData = new FormData();
                    formData.append('room_type_id', roomTypeId);
                    
                    // Send AJAX request
                    fetch('add_to_list.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#ffc107',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Clear form fields
                                const form = document.getElementById('bookingForm');
                                if (form) {
                                    form.reset();
                                }
                                
                                // Clear session storage
                                sessionStorage.removeItem('bookingFormData');
                                
                                // Redirect to roomss.php
                                window.location.href = 'roomss.php';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Failed to add room to list',
                                icon: 'error',
                                confirmButtonColor: '#ffc107'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'An unexpected error occurred',
                            icon: 'error',
                            confirmButtonColor: '#ffc107'
                        });
                    });
                });
            });
        }
        
        // Initialize when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            setupAddToListButtons();
        });

        // Add this to your document ready function
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize payment methods from database
            fetchPaymentMethods();
            
            // Initialize booking list badge
            fetch('get_booking_list.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count) {
                        updateBadgeCount(data.count);
                    }
                })
                .catch(error => {
                    console.error('Error initializing booking badge:', error);
                });
            
            // Set up event listeners for Add to List buttons
            document.querySelectorAll('.add-to-list-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    const room = <?php echo json_encode($rooms); ?>.find(r => r.room_type_id == roomId);
                    if (room) {
                        addToList(room);
                    } else {
                        console.error('Room not found:', roomId);
                    }
                });
            });
        });

        // Add these functions to handle quantity updates
        function incrementQuantity(roomTypeId) {
            const quantitySpan = document.querySelector(`.quantity-value[data-room-id="${roomTypeId}"]`);
            
            if (!quantitySpan) {
                console.error('Required elements not found');
                return;
            }
            
            const currentQuantity = parseInt(quantitySpan.textContent) || 1;
            
            // Find the available rooms element for this room
            // Look for the small text element within the same card as the quantity span
            const card = quantitySpan.closest('.card');
            const availabilityElement = card ? card.querySelector('p.small[class*="text-"]') : null;
            
            if (availabilityElement) {
                // Extract the number of available rooms from the text
                const availableText = availabilityElement.textContent.trim();
                const match = availableText.match(/(\d+)\s+room/);
                const availableRooms = match ? parseInt(match[1]) : 0;
                
                console.log('Available rooms check:', {
                    roomTypeId,
                    currentQuantity,
                    availableText,
                    availableRooms
                });
            
            // Only increment if there are available rooms
            if (availableRooms > 0) {
                updateQuantity(roomTypeId, currentQuantity + 1);
            } else {
                Swal.fire({
                        title: 'Maximum Reached',
                        text: 'No more rooms available of this type.',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                });
                }
            } else {
                // If we can't find the availability element, just try to increment
                // and let the server validate
                updateQuantity(roomTypeId, currentQuantity + 1);
            }
        }

        function decrementQuantity(roomTypeId) {
            const quantitySpan = document.querySelector(`.quantity-value[data-room-id="${roomTypeId}"]`);
            
            if (!quantitySpan) {
                console.error('Quantity element not found');
                return;
            }
            
            const currentQuantity = parseInt(quantitySpan.textContent);
            
            // Only decrement if quantity is greater than 1
            if (currentQuantity > 1) {
                updateQuantity(roomTypeId, currentQuantity - 1);
            } else {
                // Ask if they want to remove the room entirely
                Swal.fire({
                    title: 'Remove Room?',
                    text: 'Do you want to remove this room from your list?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeFromList(roomTypeId);
                    }
                });
            }
        }

        // Function to handle guest fields visibility and discount
        function toggleUploadProof(guestType, guestIndex) {
            const uploadProofDiv = document.getElementById(`upload_proof_container_${guestIndex}`);
            const guestTypeInput = document.getElementById(`guest_type_${guestIndex}`);
            
            if (uploadProofDiv && guestTypeInput) {
                if (guestType === 'Regular') {
                    // Hide upload proof
                    uploadProofDiv.style.display = 'none';
                    uploadProofDiv.querySelector('input[type="file"]').required = false;
                    
                    // Clear the file input
                    const fileInput = uploadProofDiv.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                    
                    // Reset any discount-related fields if they exist
                    const discountInput = document.querySelector(`input[name="discount_amount_${guestIndex}"]`);
                    if (discountInput) {
                        discountInput.value = '0';
                    }
                } else if (guestType === 'Senior Citizen' || guestType === 'PWD') {
                    // Show upload proof for Senior Citizen and PWD
                    uploadProofDiv.style.display = 'block';
                    uploadProofDiv.querySelector('input[type="file"]').required = true;
                } else {
                    // For other guest types, hide upload proof but keep any discount
                    uploadProofDiv.style.display = 'none';
                    uploadProofDiv.querySelector('input[type="file"]').required = false;
                    
                    // Clear the file input
                    const fileInput = uploadProofDiv.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                }
            }
            
            // Update booking summary when guest type changes
            updateBookingSummary();
        }

        // Add event delegation for dynamically created guest type dropdowns
        document.addEventListener('change', function(e) {
            if (e.target && e.target.matches('select[name^="guest_type_"]')) {
                const guestIndex = e.target.id.split('_')[2];
                toggleUploadProof(e.target.value, guestIndex);
            }
        });

        function updateGuestFields() {
            const numGuests = parseInt(document.getElementById('num_adults').value) || 1;
            const guestFieldsContainer = document.getElementById('guestFieldsContainer');
            
            if (!guestFieldsContainer) {
                console.error('Guest fields container not found');
                return;
            }
            
            console.log('Updating guest fields for', numGuests, 'guests');
            
            // Always show at least one guest field
            let html = '';
            
            for (let i = 1; i <= numGuests; i++) {
                html += `
                    <div class="guest-info-card mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Guest ${i}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="guest_firstname_${i}" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="guest_firstname_${i}" name="guest_firstname_${i}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="guest_lastname_${i}" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="guest_lastname_${i}" name="guest_lastname_${i}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="guest_type_${i}" class="form-label">Guest Type</label>
                                        <?php
                                        // Fetch discount types from the database
                                        $guest_types = [];
                                        try {
                                            $stmt = $pdo->query("SELECT name FROM discount_types WHERE status = 'active'");
                                            $guest_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                            
                                            // Always include 'regular' as an option
                                            if (!in_array('regular', $guest_types)) {
                                                array_unshift($guest_types, 'regular');
                                            }
                                        } catch (PDOException $e) {
                                            error_log("Error fetching discount types: " . $e->getMessage());
                                            // Fallback to default values if there's an error
                                            $guest_types = ['regular', 'pwd', 'senior'];
                                        }
                                        
                                        // Map database values to display names
                                        $display_names = [
                                            'regular' => 'Regular',
                                            'pwd' => 'PWD',
                                            'senior' => 'Senior Citizen',
                                            'senior_citizen' => 'Senior Citizen',
                                            'child' => 'Child',
                                            'student' => 'Student',
                                            'promo' => 'Promotional',
                                            'vip' => 'VIP',
                                            'government' => 'Government',
                                            'corporate' => 'Corporate',
                                            'military' => 'Military',
                                            'aaa' => 'AAA',
                                            'aarp' => 'AARP'
                                        ];
                                        ?>
                                        <select class="form-select" id="guest_type_${i}" name="guest_type_${i}" onchange="toggleUploadProof(this.value, ${i})" required>
                                            <?php foreach ($guest_types as $type): 
                                                $display_name = $display_names[$type] ?? ucfirst($type);
                                            ?>
                                                <option value="<?php echo $display_name; ?>"><?php echo $display_name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3" id="upload_proof_container_${i}" style="display: none;">
                                    <div class="col-md-12">
                                        <label for="guest_proof_${i}" class="form-label">Upload Proof (Senior Citizen ID or PWD ID)</label>
                                        <input type="file" class="form-control" id="guest_proof_${i}" name="guest_proof_${i}" accept="image/*,.pdf" data-guest-index="${i}">
                                        <small class="text-muted">Please upload a clear photo or scan of your ID (JPG, PNG, or PDF, max 5MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            guestFieldsContainer.innerHTML = html;
            console.log('Guest fields updated:', guestFieldsContainer.innerHTML.length > 0);
        }

        // Make sure to call this function when the modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize booking form when modal is shown
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('shown.bs.modal', function() {
                    console.log('Booking modal shown, initializing form');
                    updateGuestFields();
                    
                    // Set up event listener for number of guests change
                    const numGuestsInput = document.getElementById('num_adults');
                    if (numGuestsInput) {
                        numGuestsInput.addEventListener('change', updateGuestFields);
                        numGuestsInput.addEventListener('input', updateGuestFields);
                    }
                });
            }
        });


        // Make sure to update the booking summary when the number of guests changes
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listener for number of guests change
            const numGuestsInput = document.getElementById('num_adults');
            if (numGuestsInput) {
                numGuestsInput.addEventListener('change', updateBookingSummary);
                numGuestsInput.addEventListener('input', updateBookingSummary);
            }
            
            // Other existing event listeners...
        });

        // Add a hidden field to store the total amount
        document.addEventListener('DOMContentLoaded', function() {
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                // Add hidden field for total amount if it doesn't exist
                if (!document.getElementById('total_amount')) {
                    const totalAmountField = document.createElement('input');
                    totalAmountField.type = 'hidden';
                    totalAmountField.id = 'total_amount';
                    totalAmountField.name = 'total_amount';
                    bookingForm.appendChild(totalAmountField);
                }
            }
            
            // Set up event listeners for date changes
            const checkIn = document.getElementById('check_in');
            const checkOut = document.getElementById('check_out');
            
            if (checkIn && checkOut) {
                checkIn.addEventListener('change', function() {
                    // Set minimum check-out date to day after check-in
                    if (this.value) {
                        const minCheckOut = new Date(this.value);
                        minCheckOut.setDate(minCheckOut.getDate() + 1);
                        checkOut.min = minCheckOut.toISOString().split('T')[0];
                        
                        // Clear check-out if it's before new minimum
                        if (checkOut.value && new Date(checkOut.value) <= new Date(this.value)) {
                            checkOut.value = '';
                        }
                    }
                    
                    // Update booking summary if both dates are set
                    if (this.value && checkOut.value) {
                        updateBookingSummary();
                    }
                });
                
                checkOut.addEventListener('change', function() {
                    // Update booking summary if both dates are set
                    if (this.value && checkIn.value) {
                        updateBookingSummary();
                    }
                });
            }
            
            // Initialize booking form when modal is shown
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('shown.bs.modal', function() {
                    console.log('Booking modal shown, initializing form');
                    
                    // Initialize guest fields
                    updateGuestFields();
                    
                    // Set up event listener for number of guests change
                    const numGuestsInput = document.getElementById('num_adults');
                    if (numGuestsInput) {
                        numGuestsInput.addEventListener('change', updateGuestFields);
                        numGuestsInput.addEventListener('input', updateGuestFields);
                    }
                    
                    // Initialize booking summary
                    updateBookingSummary();
                });
            }
        });

        // Update the proceedToBooking function to initialize the booking summary
        function proceedToBooking() {
            // Show loading indicator
            Swal.fire({
                title: 'Checking your booking list',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // First check if booking list is empty
            fetch('get_booking_list.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Booking list check response:', data); // Debug log
                    
                    // Close loading indicator
                    Swal.close();
                    
                    // Check if booking list is empty
                    if (!data.success || !data.items || data.items.length === 0) {
                        Swal.fire({
                            title: 'Empty Booking List',
                            text: 'You cannot proceed to booking with an empty list. Please add rooms to your booking list first.',
                            icon: 'warning',
                            confirmButtonColor: '#ffc107'
                        });
                        return;
                    }
                    
                    // Continue with booking process if list is not empty
                    // Close the booking list modal
                    const bookingListModal = document.getElementById('bookingListModal');
                    if (bookingListModal) {
                        const bsModal = bootstrap.Modal.getInstance(bookingListModal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }

                    // Set minimum dates for check-in and check-out
                    const today = new Date();
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    
                    const checkIn = document.getElementById('check_in');
                    const checkOut = document.getElementById('check_out');
                    
                    if (checkIn && checkOut) {
                        checkIn.min = today.toISOString().split('T')[0];
                        checkOut.min = tomorrow.toISOString().split('T')[0];
                        
                        // Clear any previous values
                        checkIn.value = '';
                        checkOut.value = '';
                    }

                    // Show the booking form modal
                    const bookingModal = document.getElementById('bookingModal');
                    if (bookingModal) {
                        const bsModal = new bootstrap.Modal(bookingModal);
                        bsModal.show();
                        
                        // Initialize the booking form after the modal is shown
                        setTimeout(() => {
                            updateGuestFields();
                            updateBookingSummary();
                        }, 500);
                    } else {
                        console.error('Booking modal not found');
                        Swal.fire({
                            title: 'Error',
                            text: 'Could not find booking form. Please refresh the page and try again.',
                            icon: 'error',
                            confirmButtonColor: '#ffc107'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking booking list:', error);
                    
                    // Close loading indicator
                    Swal.close();
                    
                    Swal.fire({
                        title: 'Error',
                        text: 'Could not check booking list. Please try again later.',
                        icon: 'error',
                        confirmButtonColor: '#ffc107'
                    });
                });
        }

        // Function to calculate discounts based on guest types
        function calculateDiscounts() {
            const numAdults = parseInt(document.getElementById('num_adults').value) || 1;
            let hasDiscount = false;
            let seniorOrPwdCount = 0;
            const guestIdNumbers = {};

            // Check each adult guest for Senior Citizen or PWD status
            for (let i = 1; i <= numAdults; i++) {
                const guestType = document.getElementById(`guest_type_${i}`);
                const idNumber = document.getElementById(`id_number_${i}`);
                
                if (guestType && (guestType.value === 'Senior Citizen' || guestType.value === 'PWD')) {
                    hasDiscount = true;
                        seniorOrPwdCount++;
                    if (idNumber && idNumber.value) {
                        guestIdNumbers[`guest_${i}`] = {
                            type: guestType.value,
                            idNumber: idNumber.value
                        };
                    }
                }
            }
            
            return {
                hasDiscount,
                seniorOrPwdCount,
                guestIdNumbers
            };
        }

        // Make PHP discount types available to JavaScript
        const discountTypes = <?php echo json_encode($discountTypes); ?>;
        console.log('Loaded discount types:', discountTypes);

        // Function to toggle ID number field visibility
        function toggleIdNumberField(guestIndex) {
            const guestType = document.getElementById(`guest_type_${guestIndex}`).value;
            const idNumberRow = document.querySelector(`.id-number-row-${guestIndex}`);
            
            if (guestType === 'Senior Citizen' || guestType === 'PWD') {
                idNumberRow.style.display = 'block';
            } else {
                idNumberRow.style.display = 'none';
            }
        }



        // Function to handle the confirm booking button click
        function handleConfirmBookingClick(e) {
            console.log('Confirm booking button clicked');
            e.preventDefault();
            
            // Validate the form
            const bookingForm = document.getElementById('bookingForm');
            if (!bookingForm) {
                console.error('Booking form not found');
                return false;
            }
            
            if (!bookingForm.checkValidity()) {
                console.log('Form validation failed');
                bookingForm.reportValidity();
                return false;
            }
            
            console.log('Form is valid, showing booking summary');
            // Show the booking summary
            showBookingSummary();
            return false;
        }

        // Initialize booking form when modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            // Add event listener for confirm booking button
            const confirmBookingBtn = document.getElementById('confirmBookingBtn');
            console.log('Confirm booking button:', confirmBookingBtn);
            
            if (confirmBookingBtn) {
                console.log('Adding click event listener to confirm booking button');
                confirmBookingBtn.addEventListener('click', handleConfirmBookingClick);
            } else {
                console.error('Confirm booking button not found');
            }
            
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('shown.bs.modal', function() {
                    console.log('Booking modal shown, initializing form');
                    
                    // Initialize guest fields
                    updateGuestFields();
                    
                    // Fetch payment methods
                    fetchPaymentMethods();
                    
                    // Set up event listener for number of guests change
                    const numGuestsInput = document.getElementById('num_adults');
                    if (numGuestsInput) {
                        numGuestsInput.addEventListener('change', updateGuestFields);
                        numGuestsInput.addEventListener('input', updateGuestFields);
                    }
                    
                    // Initialize booking summary
                    updateBookingSummary();
                });
            }
            
            // Other existing event listeners...
        });


        // Function to update the booking summary with night calculation, extra guest charges, and payment options
        function updateBookingSummary() {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            const numGuests = parseInt(document.getElementById('num_adults').value) || 1;
            const nights = calculateNights(checkIn, checkOut);
            const paymentOption = document.getElementById('payment_option').value;
            const extraBedSelect = document.getElementById('extra_bed');
            
            // Get discount information
            const discountInfo = calculateDiscounts();
            
            // Get booking items from session
            fetch('get_booking_list.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.success || !data.items || data.items.length === 0) {
                        console.error('No booking items found');
                        return;
                    }
                    
                    const summaryElement = document.getElementById('bookingSummaryContent');
                    if (!summaryElement) {
                        console.error('Booking summary element not found');
                        return;
                    }
                    
                    let totalPerNight = 0;
                    let totalAmount = 0;
                    let totalCapacity = 0;
                    let extraBedCharge = 0;
                    let discountAmount = 0;
                    
                    // Calculate total capacity and base price
                    data.items.forEach(item => {
                        const capacity = parseInt(item.capacity) || 1;
                        const quantity = parseInt(item.quantity) || 1;
                        totalCapacity += capacity * quantity;
                    });
                    
                    // Calculate extra bed charge
                    if (extraBedSelect && extraBedSelect.value) {
                        const selectedOption = extraBedSelect.options[extraBedSelect.selectedIndex];
                        const bedPrice = parseFloat(selectedOption.textContent.match(/\+₱([\d,]+)/)[1].replace(/,/g, ''));
                        extraBedCharge = bedPrice;
                    }
                    
                    // Calculate room total
                    const summaryHtml = data.items.map(item => {
                        const pricePerNight = parseFloat(item.price) * parseInt(item.quantity || 1);
                        totalPerNight += pricePerNight;
                        const roomTotal = nights > 0 ? pricePerNight * nights : pricePerNight;
                        totalAmount += roomTotal;
                        
                        return `
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>${item.room_type} (${item.quantity || 1})</span>
                                <span>₱${pricePerNight.toLocaleString()} per night</span>
                            </div>
                        `;
                    }).join('');
                    
                    // Build summary HTML
                    let html = summaryHtml;
                    
                    // Add nights calculation
                    if (nights > 0) {
                        html += `
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total per night:</span>
                                <span>₱${totalPerNight.toLocaleString()}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Number of nights:</span>
                                <span>${nights}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Room Total (${nights} nights):</span>
                                <span>₱${totalAmount.toLocaleString()}</span>
                            </div>
                        `;
                    }
                    
                    // Add extra charges
                    if (extraBedCharge > 0) {
                        html += `
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Extra Bed Charge:</span>
                                <span>₱${extraBedCharge.toLocaleString()}</span>
                            </div>
                        `;
                        totalAmount += extraBedCharge;
                    }
                    
                    // Apply discount if applicable
                    if (discountInfo.hasDiscount && discountInfo.seniorOrPwdCount > 0) {
                        discountAmount = totalAmount * 0.2; // 20% discount
                        html += `
                            <div class="d-flex justify-content-between align-items-center text-success discount-animation">
                                <span>Discount (20% for Senior Citizen/PWD):</span>                                <span>-₱${discountAmount.toLocaleString()}</span>
                            </div>
                        `;
                        totalAmount -= discountAmount;
                    }
                    
                    // Add total amount and payment details
                    html += `<hr>`;
                    
                    // Handle payment option
                    let finalPaymentAmount = totalAmount;
                    if (paymentOption === 'Partial Payment') {
                        finalPaymentAmount = 1500; // Fixed downpayment
                        const remainingBalance = totalAmount - finalPaymentAmount;
                        html += `
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Amount:</strong>
                                <strong>₱${totalAmount.toLocaleString()}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-primary">
                                <strong>Required Down Payment:</strong>
                                <strong>₱${finalPaymentAmount.toLocaleString()}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted">
                                <span>Remaining Balance:</span>
                                <span>₱${remainingBalance.toLocaleString()}</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Amount (Full Payment):</strong>
                                <strong>₱${totalAmount.toLocaleString()}</strong>
                            </div>
                        `;
                    }
                    
                    // Update summary display
                    summaryElement.innerHTML = html;
                    
                    // Store values in hidden fields
                    document.getElementById('total_amount').value = totalAmount;
                    document.getElementById('payment_amount').value = finalPaymentAmount;
                    
                    // Store discount amount
                    if (!document.getElementById('discount_amount')) {
                        const discountField = document.createElement('input');
                        discountField.type = 'hidden';
                        discountField.id = 'discount_amount';
                        discountField.name = 'discount_amount';
                        document.getElementById('bookingForm').appendChild(discountField);
                    }
                    document.getElementById('discount_amount').value = discountAmount;
                    
                    // Store guest ID numbers
                    if (!document.getElementById('guest_id_numbers')) {
                        const guestIdNumbersField = document.createElement('input');
                        guestIdNumbersField.type = 'hidden';
                        guestIdNumbersField.id = 'guest_id_numbers';
                        guestIdNumbersField.name = 'guest_id_numbers';
                        document.getElementById('bookingForm').appendChild(guestIdNumbersField);
                    }
                    document.getElementById('guest_id_numbers').value = JSON.stringify(discountInfo.guestIdNumbers);
                })
                .catch(error => {
                    console.error('Error updating booking summary:', error);
                });
        }

        // Add event listener for payment option change
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listener for payment option change
            const paymentOptionSelect = document.getElementById('payment_option');
            if (paymentOptionSelect) {
                paymentOptionSelect.addEventListener('change', updateBookingSummary);
            }
            
            // Other existing event listeners...
        });

        // Add this to your existing JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Phone number validation
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    // Remove any non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Limit to 11 digits
                    if (this.value.length > 11) {
                        this.value = this.value.slice(0, 11);
                    }
                    
                    // Add validation classes
                    if (this.value.length === 11) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
            }
        });

        // Update the form submission handler
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Show booking confirmation summary
            Swal.fire({
                title: 'Booking Summary',
                html: generateBookingSummaryHTML(formData),
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Confirm Booking',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#dc3545',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Processing Booking',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit booking to server
                    fetch('process_booking.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Booking Successful!',
                                text: `Your booking reference is: ${data.booking_reference}`,
                                icon: 'success',
                                confirmButtonColor: '#ffc107'
                            }).then(() => {
                                // Clear form and redirect
                                window.location.href = 'roomss.php';
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Could not process booking. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#ffc107'
                        });
                    });
                }
            });
        });


        // Add helper function to generate payment details HTML
        function generatePaymentDetailsHTML(formData) {
            const paymentOption = formData.get('payment_option');
            const totalAmount = parseFloat(formData.get('total_amount'));
            let paymentHTML = '';
            
            if (paymentOption === 'Partial Payment') {
                const downPayment = 1500;
                const remainingBalance = totalAmount - downPayment;
                
                paymentHTML += `
                    <p class="mb-1 text-primary">Down Payment: ₱${downPayment.toLocaleString()}</p>
                    <p class="mb-1">Remaining Balance: ₱${remainingBalance.toLocaleString()}</p>
                    <small class="text-muted">To be paid at check-in</small>
                `;
            } else {
                paymentHTML += `
                    <p class="mb-1">Full Payment Amount: ₱${totalAmount.toLocaleString()}</p>
                `;
            }
            
            return paymentHTML;
        }

        // Helper function to format date
        function formatDate(dateString) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        // Helper function to calculate nights
        function calculateNights(checkIn, checkOut) {
            const start = new Date(checkIn);
            const end = new Date(checkOut);
            const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            return nights;
        }


        // Booking list management
        function addToList(roomId, roomName, roomPrice) {
            const roomData = {
                id: roomId,
                name: roomName,
                price: roomPrice,
                dateAdded: new Date().toISOString()
            };
            
            let bookingList = JSON.parse(localStorage.getItem('bookingList')) || [];
            
            // Check if room is already in list
            if (!bookingList.some(item => item.id === roomId)) {
                bookingList.push(roomData);
                localStorage.setItem('bookingList', JSON.stringify(bookingList));
                
                // Update badge
                const badge = document.getElementById('mobileBedBadge');
                if (badge) {
                    badge.textContent = bookingList.length;
                    badge.style.display = 'flex';
                }
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Added to list',
                    text: 'Room has been added to your booking list',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                // Show already in list message
                Swal.fire({
                    icon: 'info',
                    title: 'Already in list',
                    text: 'This room is already in your booking list',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }

        // Initialize badge count when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const bookingList = JSON.parse(localStorage.getItem('bookingList')) || [];
            const badge = document.getElementById('mobileBedBadge');
            if (badge) {
                if (bookingList.length > 0) {
                    badge.textContent = bookingList.length;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        });

        // Add this helper function for safe HTML escaping
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Add this helper function to handle image paths
        function getImagePath(imagePath) {
            if (!imagePath) {
                return '../../../Admin/uploads/rooms/default.jpg';
            }
            
            if (imagePath.includes('uploads/')) {
                return '../../../Admin/' + imagePath;
            }
            
            return '../../../Admin/uploads/rooms/' + imagePath.split('/').pop();
        }

        // Update the updateTotalGuests function
        function updateTotalGuests() {
            const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
            const numChildren = parseInt(document.getElementById('num_children').value) || 0;
            const totalGuests = numAdults + numChildren;
            
            // Create hidden input for total guests if it doesn't exist
            if (!document.getElementById('num_guests')) {
                const totalGuestsInput = document.createElement('input');
                totalGuestsInput.type = 'hidden';
                totalGuestsInput.id = 'num_guests';
                totalGuestsInput.name = 'num_guests';
                document.getElementById('bookingForm').appendChild(totalGuestsInput);
            }
            document.getElementById('num_guests').value = totalGuests;

            // Update guest information fields
            const guestFieldsContainer = document.getElementById('guestFieldsContainer');
            if (!guestFieldsContainer) {
                console.error('Guest fields container not found');
                return;
            }

            let html = '';

            // Add fields for adults
            for (let i = 1; i <= numAdults; i++) {
                html += `
                    <div class="guest-info-card mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Adult Guest ${i}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="guest_firstname_${i}" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="guest_firstname_${i}" name="guest_firstname_${i}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="guest_lastname_${i}" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="guest_lastname_${i}" name="guest_lastname_${i}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="guest_type_${i}" class="form-label">Guest Type</label>
                                        <select class="form-select" id="guest_type_${i}" name="guest_type_${i}" onchange="toggleIdNumberField(${i})" required>
                                            <option value="Regular">Regular</option>
                                            <option value="Senior Citizen">Senior Citizen</option>
                                            <option value="PWD">PWD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row id-number-row-${i}" style="display: none;">
                                    <div class="col-md-12 mt-3">
                                        <label for="id_number_${i}" class="form-label">ID Number</label>
                                        <input type="text" class="form-control" id="id_number_${i}" name="id_number_${i}" placeholder="Enter ID number">
                                        <small class="text-muted">Required for Senior Citizen/PWD discount</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Add fields for children
            for (let i = 1; i <= numChildren; i++) {
                const guestIndex = numAdults + i;
                html += `
                    <div class="guest-info-card mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Child Guest ${i}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="guest_firstname_${guestIndex}" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="guest_firstname_${guestIndex}" name="guest_firstname_${guestIndex}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="guest_lastname_${guestIndex}" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="guest_lastname_${guestIndex}" name="guest_lastname_${guestIndex}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="child_age_${guestIndex}" class="form-label">Age</label>
                                        <input type="number" class="form-control" id="child_age_${guestIndex}" name="child_age_${guestIndex}" 
                                               min="0" max="11" required>
                                        <small class="text-muted">Must be under 7 years old to be free of charge</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            guestFieldsContainer.innerHTML = html;

            // Set up event listeners for guest type changes
            for (let i = 1; i <= numAdults; i++) {
                const guestTypeSelect = document.getElementById(`guest_type_${i}`);
                if (guestTypeSelect) {
                    guestTypeSelect.addEventListener('change', function() {
                        toggleIdNumberField(i);
                        updateBookingSummary();
                    });
                }
            }

            // Update booking summary
            updateBookingSummary();
        }

        // Add event listeners for both adults and children inputs
        document.addEventListener('DOMContentLoaded', function() {
            const numAdultsInput = document.getElementById('num_adults');
            const numChildrenInput = document.getElementById('num_children');
            
            if (numAdultsInput) {
                numAdultsInput.addEventListener('change', updateTotalGuests);
                numAdultsInput.addEventListener('input', updateTotalGuests);
            }
            
            if (numChildrenInput) {
                numChildrenInput.addEventListener('change', updateTotalGuests);
                numChildrenInput.addEventListener('input', updateTotalGuests);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Existing event listeners...
            
            // Add event listener for extra bed changes
            const extraBedSelect = document.getElementById('extra_bed');
            if (extraBedSelect) {
                extraBedSelect.addEventListener('change', updateBookingSummary);
            }
        });

        // Add this HTML for the policy modal near the end of the body tag
        function addPolicyModal() {
            const modalHTML = `
                <div class="modal fade" id="policyModal" tabindex="-1" aria-labelledby="policyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="policyModalLabel">Hotel Policies and Regulations</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="policy-content">
                                    <h6>CASA ESTELA BOUTIQUE HOTEL & CAFÉ</h6>
                                    <h6>HOUSE RULES AND REGULATION</h6>
                                    
                                    <p>Our warmest welcome to Casa Estela Boutique Hotel & Café!</p>
                                    
                                    <ul>
                                        <li>A safe is available at the front desk where you can deposit your valuables. However, the management has the right to refuse to store money and other belongings if they pose a threat to safety, their value exceeds the standard of the hotel, or if they take up too much space.</li>
                                        <li>Damage to any hotel's equipment, fixtures or property shall result to corresponding financial charges.</li>
                                        <li>Left and unclaimed items will be kept for a period of 1 month from your departure date, unless otherwise instructed.</li>
                                        <li>PWD and Senior Citizen discounts are applicable to cash payments only.</li>
                                        <li>Check-in time is at 2:00 PM and check-out time is at 12:00 NN. Early check-in and late check-out are subject to room availability and additional charges may apply.</li>
                                        <li>Pets are not allowed in the hotel premises.</li>
                                        <li>Smoking is strictly prohibited in all rooms and indoor areas. Designated smoking areas are available outside the building.</li>
                                        <li>Quiet hours are from 10:00 PM to 7:00 AM. Please be considerate of other guests.</li>
                                        <li>The hotel reserves the right to refuse service to anyone who violates these policies.</li>
                                    </ul>
                                    
                                    <p>Thank you for choosing Casa Estela Boutique Hotel & Café. We hope you enjoy your stay!</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="agreeButton" data-bs-dismiss="modal">I Agree</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add the modal to the body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        // Add this function to handle the policy checkbox and link
        function setupPolicyCheckbox() {
            // Create the checkbox and link HTML for the booking list modal
            const bookingListModal = document.querySelector('.modal-content');
            if (bookingListModal && bookingListModal.querySelector('.modal-footer')) {
                const policyCheckboxHTML = `
                    <div class="form-check mb-3 mx-3">
                        <input class="form-check-input" type="checkbox" id="policyCheckbox" name="policy_agreement">
                        <label class="form-check-label" for="policyCheckbox">
                            I have read and agree to the <a href="#" id="policyLink">house rules and regulations</a>
                        </label>
                        <div class="invalid-feedback">
                            You must agree to the house rules and regulations before proceeding.
                        </div>
                    </div>
                `;
                
                // Insert the checkbox before the modal footer
                const modalFooter = bookingListModal.querySelector('.modal-footer');
                modalFooter.insertAdjacentHTML('beforebegin', policyCheckboxHTML);
                
                // Add event listener to the policy link
                document.getElementById('policyLink').addEventListener('click', function(e) {
                    e.preventDefault();
                    const policyModal = new bootstrap.Modal(document.getElementById('policyModal'));
                    policyModal.show();
                });
                
                // Add event listener to the agree button in the policy modal
                document.getElementById('agreeButton').addEventListener('click', function() {
                    document.getElementById('policyCheckbox').checked = true;
                    document.getElementById('policyCheckbox').classList.remove('is-invalid');
                });
                
                // Add event listener to the "Proceed to Booking" button
                const proceedButton = document.getElementById('proceedToBookingBtn') || 
                                     document.querySelector('.modal-footer button.btn-primary');
                
                if (proceedButton) {
                    proceedButton.addEventListener('click', function(e) {
                        const policyCheckbox = document.getElementById('policyCheckbox');
                        if (!policyCheckbox.checked) {
                            e.preventDefault();
                            e.stopPropagation();
                            policyCheckbox.classList.add('is-invalid');
                            
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Agreement Required',
                                text: 'You must agree to the house rules and regulations before proceeding.',
                                confirmButtonColor: '#3085d6'
                            });
                            
                            return false;
                        }
                        // If checked, allow proceeding
                        return true;
                    });
                }
            }
        }

        // Update the document ready function to handle both modals
        document.addEventListener('DOMContentLoaded', function() {
            // Add the policy modal to the page
            addPolicyModal();
            
            // Set up the checkbox in the booking list modal
            // We need to wait a bit for the booking list modal to be fully loaded
            setTimeout(setupPolicyCheckbox, 500);
            
            // For the booking confirmation modal (if it exists)
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    const policyCheckbox = document.getElementById('policyCheckbox');
                    if (policyCheckbox && !policyCheckbox.checked) {
                        e.preventDefault();
                        e.stopPropagation();
                        policyCheckbox.classList.add('is-invalid');
                        
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Agreement Required',
                            text: 'You must agree to the house rules and regulations before confirming your booking.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
            
            // Add event listener for the "Proceed to Booking" button in the booking list modal
            const proceedButton = document.querySelector('button[id="proceedToBookingBtn"]');
            if (!proceedButton) {
                // If the button doesn't have an ID, try to find it by class and text
                const buttons = document.querySelectorAll('.modal-footer .btn-primary');
                buttons.forEach(button => {
                    if (button.textContent.includes('Proceed to Booking')) {
                        button.id = 'proceedToBookingBtn';
                    }
                });
            }
        });

        // Add this JavaScript for form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            // Basic form validation
            var paymentProof = document.getElementById('payment_proof');
            if (paymentProof.files.length > 0) {
                var file = paymentProof.files[0];
                var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Please upload only JPG, JPEG or PNG files');
                    return false;
                }
                
                // Check file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('File size must be less than 5MB');
                    return false;
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('shown.bs.modal', function() {
                    fetchPaymentMethods();
                });
            }
        });


    </script>
</body>
</html>
     <script>                                                                                                                                       


        document.addEventListener('DOMContentLoaded', function() {
            const bookingModal = document.getElementById('bookingModal');
            if (bookingModal) {
                bookingModal.addEventListener('shown.bs.modal', function() {
                    fetchPaymentMethods();
                });
            }
        });


    </script>
</body>
</html>
                                                                                                                                                