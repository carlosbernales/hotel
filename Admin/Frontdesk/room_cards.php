<?php
// Start the session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header.php first
require_once 'header.php';

// Start output buffering after header.php
ob_start();

// Add cache control meta tags
echo '<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">';
echo '<meta http-equiv="Pragma" content="no-cache">';
echo '<meta http-equiv="Expires" content="0">';

// Display success/error messages
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
        ' . $_SESSION['message'] . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Include database connection
require_once 'db.php';
require_once 'includes/discount_helper.php';  

// Update query to correctly display room availability
$query = "SELECT 
    rt.room_type_id, rt.room_type, rt.price, rt.description, rt.beds, rt.rating, rt.image,
    r.id, r.total_rooms, r.available_rooms,
    CASE 
        WHEN r.available_rooms = 0 THEN 'Occupied' 
        WHEN r.available_rooms > 0 THEN 'Available'
        ELSE r.status 
    END as status
    FROM room_types rt
    INNER JOIN rooms r ON rt.room_type_id = r.room_type_id
    WHERE rt.status = 'active' AND r.status != 'deleted'
    ORDER BY rt.room_type";

// Debug query output
echo "<!-- SQL Query: " . htmlspecialchars($query) . " -->";

$result = mysqli_query($con, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Debug all room data
echo "<!-- Room Data: -->";
$debug_query = mysqli_query($con, "SELECT * FROM rooms ORDER BY room_type_id");
while ($debug_row = mysqli_fetch_assoc($debug_query)) {
    echo "<!-- Room ID: " . $debug_row['id'] . 
         ", Room Type: " . $debug_row['room_type_id'] . 
         ", Status: " . $debug_row['status'] . 
         ", Total: " . $debug_row['total_rooms'] . 
         ", Available: " . $debug_row['available_rooms'] . " -->";
}

$roomDetails = array();

while ($row = mysqli_fetch_assoc($result)) {
    // Debug row data
    echo "<!-- Room Type ID: " . $row['room_type_id'] . 
         ", Room Type: " . $row['room_type'] . 
         ", Total: " . $row['total_rooms'] . 
         ", Available: " . $row['available_rooms'] . " -->";
    
    // Get amenities for this room type
    $amenitiesQuery = "SELECT a.name, a.icon 
                      FROM amenities a 
                      INNER JOIN room_type_amenities rta ON a.amenity_id = rta.amenity_id 
                      WHERE rta.room_type_id = " . $row['room_type_id'];
    $amenitiesResult = mysqli_query($con, $amenitiesQuery);
    $amenities = array();
    
    while ($amenity = mysqli_fetch_assoc($amenitiesResult)) {
        $amenities[] = $amenity;
    }
    
    // Get active discounts for this room type
    $discounts = getActiveDiscounts($con, $row['room_type_id']);
    
    // Calculate final price with discounts
    $priceInfo = calculateDiscountedPrice($row['price'], $discounts);
    
    // Clean and prepare image path
    $image = $row['image'] ?? '';
    
    // Remove '../' from the start of the path if it exists
    $image = preg_replace('/^\.\.\//', '', $image);
    
    // Remove 'aa/' from the start of the path if it exists
    $image = preg_replace('/^aa\//', '', $image);
    
    // If path doesn't start with a slash, add one
    if (!empty($image) && $image[0] !== '/') {
        $image = '/' . $image;
    }
    
    // Set default image if none exists
    if (empty($image)) {
        $image = 'Admin/assets/img/rooms/default.jpg';
    }
    
    // Store cleaned image path in room details
    $roomDetails[] = array(
        'id' => $row['room_type_id'],
        'type' => $row['room_type'],
        'price' => $row['price'],
        'discounted_price' => $priceInfo['final_price'],
        'discount_percentage' => $priceInfo['discount_percentage'],
        'discount_name' => $priceInfo['discount_name'],
        'available_rooms' => (int)$row['available_rooms'],
        'total_rooms' => (int)$row['total_rooms'],
        'status' => $row['status'],
        'capacity' => $row['beds'],
        'description' => $row['description'],
        'rating' => $row['rating'],
        'image' => $image,
        'amenities' => $amenities
    );
}

// If no rooms were found at all, show message
if (count($roomDetails) == 0) {
    echo '<div class="alert alert-info m-4">
        <h4 class="alert-heading">No Rooms Found</h4>
        <p>We\'re sorry, but there are no rooms in our system. Please check back later.</p>
    </div>';
}
?>

<?php if (empty($roomDetails)): ?>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 50vh;">
            <div class="col-md-8 text-center">
                <i class="fas fa-hotel fa-5x text-muted mb-4"></i>
                <h3 class="mb-3">We're sorry, but there are no rooms in our system.</h3>
                <p class="text-muted lead">Please check back later.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="room-container">
        <?php foreach ($roomDetails as $room): ?>
            <div class="room-card <?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? 'room-unavailable' : ''; ?>">
            <div class="room-image">
                <?php
                // Add detailed debugging at the top of the file
                error_log("SERVER INFORMATION:");
                error_log("Document Root: " . $_SERVER['DOCUMENT_ROOT']);
                error_log("Script Filename: " . $_SERVER['SCRIPT_FILENAME']);
                error_log("HTTP Host: " . $_SERVER['HTTP_HOST']);
                error_log("Request URI: " . $_SERVER['REQUEST_URI']);
                
                // Set the base path for room images with more flexibility
                $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $upload_path = 'Admin/uploads/rooms/';  // Changed from 'aa/uploads/rooms/'
                $default_image = 'Admin/assets/img/rooms/default.jpg';
                
                // Get the filename from the stored path and clean it
                $filename = '';
                if (!empty($room['image'])) {
                    // Remove any full paths that might be stored in the database
                    $filename = basename($room['image']);
                    
                    // If the image path starts with /uploads/rooms/, extract just the filename
                    if (strpos($room['image'], 'Admin/uploads/rooms/') !== false) {
                        $filename = basename($room['image']);
                    }
                    
                    // Clean the filename
                    $filename = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $filename);
                }
                
                // Construct the image paths
                $image_path = !empty($filename) ? $upload_path . $filename : $default_image;
                
                // For debugging
                error_log("Image Debug - Room ID: " . $room['id']);
                error_log("Original image value: " . ($room['image'] ?? 'null'));
                error_log("Cleaned filename: " . $filename);
                error_log("Final relative path: " . $image_path);
                ?>
                <!-- Debug info in HTML source -->
                <!-- DEBUG: Image Path = <?php echo htmlspecialchars($image_path); ?> -->
                <img src="<?php echo htmlspecialchars($image_path); ?>?t=<?php echo time(); ?>" 
                     alt="<?php echo htmlspecialchars($room['type']); ?>" 
                     onerror="this.onerror=null; this.src='<?php echo $default_image; ?>?t=<?php echo time(); ?>'; console.log('Falling back to default image for:', this.alt);"
                     class="room-image"
                     loading="lazy">
                
                <?php if ($room['discount_percentage'] > 0 && $room['status'] === 'Available' && $room['available_rooms'] > 0): ?>
                    <div class="discount-badge">
                        <?php echo number_format($room['discount_percentage'], 0); ?>% OFF
                    </div>
                <?php endif; ?>
                
                <?php if ($room['status'] !== 'Available' || $room['available_rooms'] <= 0): ?>
                    <div class="unavailable-badge">
                        Not Available
                    </div>
                <?php endif; ?>
            </div>
            <div class="room-info">
                <h3><?php echo $room['type']; ?></h3>
                
                <div class="rating">
                    <?php
                    $rating = $room['rating'];
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($rating)) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif ($i - $rating > 0 && $i - $rating < 1) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    echo " <span>($rating)</span>";
                    ?>
                </div>

                <div class="price-container">
                    <?php if ($room['discount_percentage'] > 0): ?>
                        <div class="original-price">₱<?php echo number_format($room['price'], 2); ?></div>
                        <div class="discounted-price">₱<?php echo number_format($room['discounted_price'], 2); ?></div>
                        <div class="discount-name"><?php echo $room['discount_name']; ?></div>
                    <?php else: ?>
                        <div class="price">₱<?php echo number_format($room['price'], 2); ?> per night</div>
                    <?php endif; ?>
                </div>
                
                <div class="availability">
                    <?php if ($room['status'] === 'Available' && $room['available_rooms'] > 0): ?>
                        <i class="fas fa-check-circle text-success"></i>
                        <?php if ($room['available_rooms'] == 1): ?>
                            Only 1 room left
                        <?php else: ?>
                            Only <?php echo $room['available_rooms']; ?> rooms left
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-danger"></i>
                        Currently not available
                    <?php endif; ?>
                </div>

                <div class="capacity">
                    <i class="fas fa-users"></i>
                    Max capacity: <?php echo $room['capacity']; ?> persons
                </div>

                <div class="amenities">
                    <?php foreach ($room['amenities'] as $amenity): ?>
                        <span class="amenity">
                            <i class="<?php echo $amenity['icon']; ?>"></i>
                            <?php echo $amenity['name']; ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <div class="room-actions">
                    <button class="btn btn-view-details" onclick="viewDetails(<?php echo $room['id']; ?>)">
                        <i class="fas fa-info-circle"></i> VIEW DETAILS
                    </button>
                    <button class="btn btn-add-to-list<?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? ' disabled' : ''; ?>" 
                            <?php if ($room['status'] === 'Available' && $room['available_rooms'] > 0): ?>
                            onclick="addToList(
                                <?php echo (int)$room['id']; ?>,
                                '<?php echo addslashes($room['type']); ?>',
                                <?php echo $room['discounted_price'] ?: $room['price']; ?>,
                                '<?php echo addslashes($room['capacity']); ?>',
                                '<?php echo addslashes($room['image']); ?>'
                            )"
                            <?php else: ?>
                            disabled
                            <?php endif; ?>>
                        <i class="fas fa-cart-plus"></i> Add to List
                    </button>
                    <button class="btn btn-advance-check-in<?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? ' disabled' : ''; ?>" 
                            <?php if ($room['status'] === 'Available' && $room['available_rooms'] > 0): ?>
                            onclick="advanceCheckIn(
                                <?php echo $room['id']; ?>,
                                '<?php echo addslashes($room['type']); ?>',
                                <?php echo $room['discounted_price'] ?: $room['price']; ?>,
                                '<?php echo addslashes($room['capacity']); ?>',
                                '<?php echo addslashes($room['image']); ?>'
                            )"
                            <?php else: ?>
                            disabled
                            <?php endif; ?>>
                        <i class="fas fa-sign-in-alt"></i> Check In
                    </button>
                </div>
            </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="qrCodeContainer" style="display:none;">
    <img id="qrCodeImage" src="" alt="QR Code" style="width:200px;height:200px;">
    <div id="paymentAmount"></div>
</div>

<!-- Payment QR Code Section -->
<div class="payment-section" style="display:none;">
    <div class="qr-container text-center">
        <h4 class="payment-method-title mb-3"></h4>
        <div class="qr-code-wrapper">
            <img id="qrCodeImage" src="" alt="QR Code" style="max-width:250px; height:auto;">
        </div>
        <div class="payment-details mt-3">
            <h5>Payment Details</h5>
            <div class="total-amount mb-2">Total Amount: <span id="totalPaymentAmount">₱0.00</span></div>
            <div id="discountInfo" style="display: none;" class="discount-amount">
                <span>Discount:</span>
                <span id="discountAmountDisplay">₱0.00</span>
            </div>
            <div class="payment-type mb-2">Payment Type: <span id="paymentTypeDisplay">Full Payment</span></div>
            <div class="amount-to-pay">Amount to Pay: <span id="amountToPay">₱0.00</span></div>
        </div>
    </div>
</div>

<!-- Room Details Modal -->
<div class="modal fade" id="roomDetailsModal" tabindex="-1" role="dialog" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="modalRoomImage" src="" alt="Room Image" class="img-fluid rounded">
                    </div>
                    <div class="col-md-6">
                        <h3 id="modalRoomType"></h3>
                        <div id="modalRoomRating" class="rating mb-3"></div>
                        <div id="modalRoomPrice" class="price-container mb-3"></div>
                        <div id="modalRoomCapacity" class="capacity mb-3"></div>
                        <div id="modalRoomDescription" class="description mb-3"></div>
                        <div id="modalRoomAmenities" class="amenities"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modalAddToList">Add to List</button>
            </div>
        </div>
    </div>
</div>

<!-- Advance Check In Modal -->
<div class="modal fade" id="advanceCheckInModal" tabindex="-1" role="dialog" aria-labelledby="advanceCheckInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advanceCheckInModalLabel">Check In</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="advanceCheckInForm" action="process_advance_checkin.php" method="POST">
                <div class="modal-body">
                    <div class="room-details mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="" alt="Room Image" class="room-image img-fluid rounded">
                            </div>
                            <div class="col-md-7">
                                <h4 class="room-type"></h4>
                                <p class="room-price"></p>
                                <p class="capacity"><i class="fas fa-users"></i> Max capacity: <span id="roomCapacityDisplay"></span> persons</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for room details -->
                    <input type="hidden" id="roomIdInput" name="room_type_id">
                    <input type="hidden" id="roomTypeInput" name="room_type">
                    <input type="hidden" id="roomPriceInput" name="price">
                    <input type="hidden" id="roomCapacity" name="capacity">
                    <input type="hidden" id="discountApplied" name="discount_applied" value="0">
                    <input type="hidden" id="discountPercentage" name="discount_percentage" value="0">
                    <input type="hidden" id="guestCount" name="guestCount" value="1">
                    
                    <!-- Guest Information -->
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="text" class="form-control" id="contactNumber" name="contactNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="checkOutDate">Check Out Date</label>
                        <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required>
                    </div>
                    <div class="form-group">
                        <label for="specialRequests">Special Requests (Optional)</label>
                        <textarea class="form-control" id="specialRequests" name="specialRequests" rows="3"></textarea>
                    </div>
                    
                    <!-- Discount Selection Section -->
                    <div class="form-group">
                        <label for="discountType">Apply Discount (if applicable)</label>
                        <select class="form-control" id="discountType" name="discountType">
                            <option value="">No Discount</option>
                            <?php
                            // Check if discount_types table exists and fetch active discount types
                            $table_check = mysqli_query($con, "SHOW TABLES LIKE 'discount_types'");
                            if ($table_check && mysqli_num_rows($table_check) > 0) {
                                $discount_query = "SELECT * FROM discount_types WHERE is_active = 1 ORDER BY name ASC";
                                $discount_result = mysqli_query($con, $discount_query);
                                
                                if ($discount_result && mysqli_num_rows($discount_result) > 0) {
                                    while ($discount = mysqli_fetch_assoc($discount_result)) {
                                        echo '<option value="' . htmlspecialchars($discount['name']) . '" 
                                            data-percentage="' . htmlspecialchars($discount['percentage']) . '">' 
                                            . htmlspecialchars(ucfirst($discount['name'])) . ' (' 
                                            . htmlspecialchars($discount['percentage']) . '%)</option>';
                                    }
                                }
                            } else {
                                // Fallback to default options if table doesn't exist
                                echo '<option value="senior">Senior Citizen</option>';
                                echo '<option value="pwd">PWD</option>';
                                echo '<option value="student">Student</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Maya">Maya</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="paymentOption">Payment Option</label>
                        <select class="form-control" id="paymentOption" name="paymentOption" required>
                            <option value="full">Full Payment</option>
                            <option value="downpayment">Downpayment (50%)</option>
                        </select>
                    </div>
                    
                    <!-- Payment QR Code Section (Hidden by default) -->
                    <div class="payment-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="payment-method-title">Payment QR Code</h5>
                                <div class="qr-container">
                                    <div class="qr-code-wrapper text-center">
                                        <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 250px;">
                                    </div>
                                    <div class="payment-details">
                                        <h5>Payment Details</h5>
                                        <div class="total-amount">
                                            <span>Total Amount:</span>
                                            <span id="totalPaymentAmount">₱0.00</span>
                                        </div>
                                        <div id="discountInfo" style="display: none;" class="discount-amount">
                                            <span>Discount:</span>
                                            <span id="discountAmountDisplay">₱0.00</span>
                                        </div>
                                        <div class="payment-type">
                                            <span>Payment Type:</span>
                                            <span id="paymentTypeDisplay">Full Payment</span>
                                        </div>
                                        <div class="amount-to-pay">
                                            <span><strong>Amount to Pay:</strong></span>
                                            <span id="amountToPay"><strong>₱0.00</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Number of Adults and Children -->
                    <div class="form-group">
                        <label for="numAdults">Number of Adults</label>
                        <input type="number" class="form-control" id="numAdults" name="numAdults" min="1" value="1" required>
                    </div>
                    <div id="adultNamesContainer"></div>
                    <div class="form-group">
                        <label for="numChildren">Number of Children</label>
                        <input type="number" class="form-control" id="numChildren" name="numChildren" min="0" value="0" required>
                    </div>
                    <div id="childrenNamesContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="completeCheckInBtn">Complete Check In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.room-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 2rem;
    margin-left: 250px; /* Match sidebar width */
    width: calc(100% - 280px); /* Account for sidebar width + some padding */
    box-sizing: border-box;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .room-container {
        margin-left: 0;
        width: 100%;
        padding: 1rem;
    }
}

.room-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.room-image {
    width: 100%;
    height: 250px;
    position: relative;
    overflow: hidden;
}

.room-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.3s ease;
}

.room-image img:hover {
    transform: scale(1.05);
}

.discount-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #e74c3c;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
    font-size: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.room-info {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.price-container {
    margin: 1rem 0;
}

.original-price {
    color: #999;
    text-decoration: line-through;
    font-size: 0.9rem;
}

.discounted-price {
    font-size: 1.4rem;
    font-weight: bold;
    color: #e74c3c;
    margin-top: 0.25rem;
}

.discount-name {
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.25rem;
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #2c3e50;
}

.availability, .capacity {
    margin: 0.5rem 0;
    color: #666;
}

.amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin: 1rem 0;
}

.amenity {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #666;
}

.room-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 20px;
    padding: 10px 0;
    width: 100%;
}

.btn-view-details, .btn-add-to-list, .btn-advance-check-in {
    width: 100%;
    padding: 10px;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 6px;
    transition: all 0.3s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-view-details {
    background-color: #ffffff;
    color: #007bff;
    border: 2px solid #007bff;
}

.btn-view-details:hover {
    background-color: #007bff;
    color: #ffffff;
}

.btn-add-to-list {
    background-color: #17a2b8;
    color: #ffffff;
    border: none;
}

.btn-add-to-list:hover {
    background-color: #138496;
}

.btn-advance-check-in {
    background-color: #28a745;
    color: #ffffff;
    border: none;
}

.btn-advance-check-in:hover {
    background-color: #218838;
}

.room-actions i {
    margin-right: 8px;
    font-size: 14px;
}

.payment-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.discount-amount {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    color: #28a745;
    font-weight: 500;
    margin-bottom: 8px;
    border-bottom: 1px dashed #ddd;
}

.qr-container {
    max-width: 400px;
    margin: 0 auto;
}

.qr-code-wrapper {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.payment-details {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.payment-method-title {
    color: #2c3e50;
    font-weight: bold;
}

.payment-details h5 {
    color: #2c3e50;
    margin-bottom: 15px;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.total-amount, .payment-type, .amount-to-pay {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
}

/* Modal Styles */
.modal-dialog.modal-lg {
    max-width: 900px;
    margin: 1.75rem auto;
}

.modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.25rem 1.5rem;
}

.modal-header .modal-title {
    font-weight: 600;
    font-size: 1.4rem;
    color: #333;
}

.modal-header .close {
    padding: 1.25rem;
    margin: -1.25rem -1.25rem -1.25rem auto;
}

.modal-body {
    padding: 1.5rem;
}

.modal-body .row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.modal-body .col-md-6 {
    position: relative;
    width: 50%;
    padding-right: 15px;
    padding-left: 15px;
}

.modal-body img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.modal-body h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.75rem;
}

.modal-body .rating {
    margin-bottom: 1rem;
}

.modal-body .rating i.fas,
.modal-body .rating i.far,
.modal-body .rating i.fa-star-half-alt {
    color: #ffc107;
    font-size: 1.1rem;
    margin-right: 2px;
}

.modal-body .rating span {
    font-size: 0.9rem;
    color: #6c757d;
    margin-left: 5px;
}

.modal-body .price-container {
    margin-bottom: 1.25rem;
}

.modal-body .price {
    font-size: 1.3rem;
    font-weight: 600;
    color: #28a745;
}

.modal-body .original-price {
    color: #6c757d;
    text-decoration: line-through;
    font-size: 1rem;
    font-weight: normal;
    margin-bottom: 0.25rem;
}

.modal-body .discounted-price {
    font-size: 1.3rem;
    font-weight: 600;
    color: #dc3545;
    margin-bottom: 0.25rem;
}

.modal-body .discount-name {
    font-size: 0.9rem;
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
}

.modal-body .capacity {
    margin-bottom: 1.25rem;
    font-size: 1rem;
    color: #495057;
    display: flex;
    align-items: center;
}

.modal-body .capacity i {
    margin-right: 0.5rem;
    color: #6c757d;
}

.modal-body .description {
    margin-bottom: 1.5rem;
    color: #6c757d;
    line-height: 1.6;
    font-size: 0.95rem;
}

.modal-body .amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.modal-body .amenity {
    display: inline-flex;
    align-items: center;
    background-color: #f8f9fa;
    padding: 0.5rem 0.75rem;
    border-radius: 5px;
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

.modal-body .amenity i {
    margin-right: 0.5rem;
    color: #17a2b8;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 1.25rem;
}

.modal-footer .btn {
    font-weight: 500;
    padding: 0.6rem 1.25rem;
    border-radius: 5px;
}

.modal-footer .btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.modal-footer .btn-primary {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.modal-footer .btn-primary:hover {
    background-color: #138496;
    border-color: #117a8b;
}

/* Add styles for unavailable rooms */
.room-unavailable {
    opacity: 0.8;
    background-color: #f8f9fa;
    position: relative;
}

.unavailable-badge {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    background-color: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 10px 20px;
    font-size: 1.5rem;
    font-weight: bold;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 5;
    white-space: nowrap;
}

button.disabled {
    opacity: 0.65;
    cursor: not-allowed;
    pointer-events: none;
    background-color: #6c757d;
    border-color: #6c757d;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}
</style>

<script>
// Make sure we have jQuery and Bootstrap loaded at the beginning
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery and Bootstrap are loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. The VIEW DETAILS functionality requires jQuery.');
        return;
    }
    
    if (typeof bootstrap === 'undefined' && typeof $.fn.modal === 'undefined') {
        console.error('Bootstrap JS is not loaded. The VIEW DETAILS functionality requires Bootstrap.');
        return;
    }
    
    // Make sure the modal exists in the DOM
    if (!document.getElementById('roomDetailsModal')) {
        console.error('Room details modal not found in the DOM');
        return;
    }
    
    console.log('VIEW DETAILS functionality initialized');
});

// Global variables for room details
const roomDetailsArray = <?php echo json_encode($roomDetails); ?>;

// Fix the viewDetails function to properly display the modal
function viewDetails(roomId) {
    console.log('View details clicked for room ID:', roomId);
    
    // Find the room details from the PHP-generated array
    const rooms = <?php echo json_encode($roomDetails); ?>;
    const room = rooms.find(r => parseInt(r.id) === parseInt(roomId));
    
    if (!room) {
        console.error('Room not found with ID:', roomId);
        return;
    }

    console.log('Room details found:', room);

    // Update modal content
    document.getElementById('modalRoomType').textContent = room.type;
    document.getElementById('modalRoomImage').src = room.image;
    
    // Update rating
    const ratingDiv = document.getElementById('modalRoomRating');
    let ratingHtml = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(room.rating)) {
            ratingHtml += '<i class="fas fa-star"></i>';
        } else if (i - room.rating > 0 && i - room.rating < 1) {
            ratingHtml += '<i class="fas fa-star-half-alt"></i>';
        } else {
            ratingHtml += '<i class="far fa-star"></i>';
        }
    }
    ratingHtml += ` <span>(${room.rating})</span>`;
    ratingDiv.innerHTML = ratingHtml;
    
    // Update price
    const priceDiv = document.getElementById('modalRoomPrice');
    if (room.discount_percentage > 0) {
        priceDiv.innerHTML = `
            <div class="original-price">₱${parseFloat(room.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            <div class="discounted-price">₱${parseFloat(room.discounted_price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            <div class="discount-name">${room.discount_name}</div>
        `;
    } else {
        priceDiv.innerHTML = `<div class="price">₱${parseFloat(room.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} per night</div>`;
    }
    
    // Update capacity
    document.getElementById('modalRoomCapacity').innerHTML = `
        <i class="fas fa-users"></i> Max capacity: ${room.capacity} persons
    `;
    
    // Update description
    document.getElementById('modalRoomDescription').textContent = room.description;
    
    // Update amenities
    const amenitiesDiv = document.getElementById('modalRoomAmenities');
    amenitiesDiv.innerHTML = room.amenities.map(amenity => `
        <span class="amenity">
            <i class="${amenity.icon}"></i>
            ${amenity.name}
        </span>
    `).join('');
    
    // Update Add to List button
    const addToListBtn = document.getElementById('modalAddToList');
    addToListBtn.onclick = () => addToList(
        room.id,
        room.type,
        room.discounted_price || room.price,
        room.capacity,
        room.image
    );
    
    // Ensure that the modal is initialized properly
    try {
        // Get the existing Bootstrap modal instance
        const modalElement = document.getElementById('roomDetailsModal');
        const modalInstance = $('#roomDetailsModal');
        
        // Show the modal using jQuery
        modalInstance.modal('show');
        
        console.log('Modal displayed successfully');
    } catch (error) {
        console.error('Error showing modal:', error);
    }
}

// Function to handle advance check in
function advanceCheckIn(roomId, roomType, roomPrice, roomCapacity, roomImage) {
    // Find room details from PHP-generated array
    let roomDetails = null;
    try {
        roomDetails = roomDetailsArray.find(room => room.id === roomId);
    } catch (e) {
        console.error("Error finding room details:", e);
    }

    if (!roomDetails && roomType && roomPrice) {
        roomDetails = {
            type: roomType,
            price: parseFloat(roomPrice),
            capacity: roomCapacity,
            image: roomImage
        };
    }

    if (roomDetails) {
        // Set room details in the modal
        $('#advanceCheckInModal .room-type').text(roomDetails.type);
        $('#roomTypeInput').val(roomDetails.type);
        $('#roomIdInput').val(roomId);
        $('#roomPriceInput').val(roomDetails.price);
        $('#roomCapacity').val(roomDetails.capacity);
        $('#advanceCheckInModal .room-image').attr('src', roomDetails.image);
        $('#roomCapacityDisplay').text(roomDetails.capacity);
        
        // Format and set the room price
        const formattedPrice = parseFloat(roomDetails.price).toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).replace('PHP', '₱');
        
        $('#advanceCheckInModal .room-price').text(formattedPrice);
        
        // Update the total amount in the price summary
        updatePriceSummary(roomDetails.price);
        
        // Set default dates
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        
        $('#checkOutDate').val(tomorrow.toISOString().split('T')[0]);
        
        // Set default guest count and max capacity
        $('#guestCount').val(1);
        $('#guestCount').attr('max', roomDetails.capacity);
        
        // Reset form fields
        $('#firstName, #lastName, #contactNumber, #email, #specialRequests').val('');
        $('#paymentMethod').val('');
        $('.payment-section').hide();
        
        // Show the modal
        $('#advanceCheckInModal').modal('show');
    } else {
        console.error("Room details not found");
    }
}

// Clear any existing cart data on page load
document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('roomList'); // Clear existing data
    localStorage.setItem('roomList', JSON.stringify([])); // Initialize empty cart
    updateCartCount();
});

function addToList(roomId, roomType, price, capacity, image) {
    // Create room object
    const room = {
        id: roomId,
        type: roomType,
        price: parseFloat(price),
        capacity: capacity,
        image: image,
        addedAt: new Date().toISOString()
    };
    
    console.log('Adding room to list:', room);
    
    // Get existing list
    let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
    
    // Check if room is already in list
    if (roomList.some(item => item.id === roomId)) {
        Swal.fire({
            icon: 'warning',
            title: 'Already in List',
            text: 'This room is already in your list!'
        });
        return;
    }
    
    // Add room to list
    roomList.push(room);
    localStorage.setItem('roomList', JSON.stringify(roomList));
    
    // Also ensure the cartItems has the same content (for compatibility)
    localStorage.setItem('cartItems', JSON.stringify(roomList));
    
    // Update cart count
    updateCartCount();
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Added to List',
        text: 'Room has been added to your list!',
        timer: 1500,
        showConfirmButton: false
    });
}

function showCart() {
    const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
    const cartContainer = document.getElementById('cartItems');
    
    if (!cartContainer) {
        console.error('Cart container not found');
        return;
    }
    
    if (roomList.length === 0) {
        cartContainer.innerHTML = '<div class="alert alert-info">Your cart is empty</div>';
    } else {
        let html = '';
        let total = 0;
        
        roomList.forEach((room, index) => {
            total += parseFloat(room.price);
            html += `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="${room.image}" alt="${room.type}" onerror="this.src='assets/img/rooms/default.jpg'">
                    </div>
                    <div class="cart-item-details">
                        <h5>${room.type}</h5>
                        <p>Capacity: ${room.capacity}</p>
                        <p>Price: ₱${parseFloat(room.price).toLocaleString()}</p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="btn btn-sm btn-danger" onclick="removeFromList(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += `
            <div class="total-section">
                <h4>Total Amount: ₱${total.toLocaleString()}</h4>
            </div>
        `;
        
        cartContainer.innerHTML = html;
    }
    
    $('#cartModal').modal('show');
}

function removeFromList(index) {
    let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
    roomList.splice(index, 1);
    localStorage.setItem('roomList', JSON.stringify(roomList));
    updateCartCount();
    showCart();
}

function clearCart() {
    Swal.fire({
        title: 'Clear Cart?',
        text: 'Are you sure you want to remove all items from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.setItem('roomList', JSON.stringify([]));
            updateCartCount();
            showCart();
            
            Swal.fire(
                'Cleared!',
                'Your cart has been cleared.',
                'success'
            );
        }
    });
}

function updateCartCount() {
    const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = roomList.length;
        cartCount.style.display = roomList.length > 0 ? 'inline-block' : 'none';
        cartCount.classList.add('pulse');
        setTimeout(() => cartCount.classList.remove('pulse'), 300);
    }
}

// Initialize guest name fields - REMOVE THIS BLOCK
document.querySelectorAll('.guest-count').forEach(input => {
    updateRoomPrice(input);
});

// Update the function to not rely on guest count input
function updateRoomPrice(input) {
    // Since guest count input is removed, we'll use a fixed count of 1
    const guestCount = 1;
    const roomItem = input ? input.closest('.selected-room-item') : null;
    
    if (!roomItem) return;
    
    const basePrice = parseFloat(input ? input.dataset.basePrice : roomItem.querySelector('.price-per-night').dataset.basePrice);
    const capacity = parseInt(input ? input.dataset.capacity : roomItem.querySelector('.price-per-night').dataset.capacity);
    
    // Calculate extra guest fee if any (should be 0 now)
    const extraGuests = Math.max(0, guestCount - capacity);
    const extraFee = extraGuests * 1000; // ₱1,000 per extra guest
    
    // Update price per night with extras
    const pricePerNight = basePrice + extraFee;
    roomItem.querySelector('.price-per-night').textContent = pricePerNight.toLocaleString();
    
    // Get number of nights
    const checkInDate = new Date(document.querySelector('input[name="checkIn"]').value);
    const checkOutDate = new Date(document.querySelector('input[name="checkOut"]').value);
    let nights = 1;
    
    if (checkInDate && checkOutDate && checkInDate < checkOutDate) {
        const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
        nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
    }
    
    // Update nights count
    const nightsElement = roomItem.querySelector('.nights-count');
    if (nightsElement) {
        nightsElement.textContent = nights;
    }
    
    // Calculate and update total price
    const totalPrice = pricePerNight * nights;
    const roomPriceElement = roomItem.querySelector('.room-price');
    if (roomPriceElement) {
        roomPriceElement.textContent = totalPrice.toLocaleString();
    }
    
    // Update total amount for all rooms
    updateTotalAmount();
}

// Add event listeners for date changes
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Update prices for all rooms when dates change
            document.querySelectorAll('.guest-count').forEach(guestInput => {
                updateRoomPrice(guestInput);
            });
        });
    });
});

function proceedToBooking() {
    const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
    if (roomList.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Empty Cart',
            text: 'Please add rooms to your cart before proceeding.'
        });
        return;
    }
    
    $('#cartModal').modal('hide');
    
    // Display selected rooms with guest inputs
    const selectedRoomsList = document.getElementById('selectedRoomsList');
    let html = '';
    
    roomList.forEach((room, index) => {
        html += `
            <div class="selected-room-item">
                <div class="room-info">
                    <h6>${room.type}</h6>
                    <div class="price-info">
                        <p>Base Price: ₱${parseFloat(room.price).toLocaleString()} per night</p>
                        <p>Price per night with extras: ₱<span class="price-per-night">${parseFloat(room.price).toLocaleString()}</span></p>
                    </div>
                    <p>Maximum Capacity: ${room.capacity} persons</p>
                    <div class="room-total">
                        <p>Number of Nights: <span class="nights-count">1</span></p>
                        <p>Total Price: ₱<span class="room-price">${parseFloat(room.price).toLocaleString()}</span></p>
                    </div>
                </div>
            </div>
        `;
    });
    
    selectedRoomsList.innerHTML = html;
    
    // Initialize guest name fields
    document.querySelectorAll('.guest-count').forEach(input => {
        updateRoomPrice(input);
    });
    
    updateTotalAmount();
    $('#bookingFormModal').modal('show');
}

function updateTotalAmount() {
    let total = 0;
    document.querySelectorAll('.room-price').forEach(element => {
        const priceText = element.textContent.replace(/[^\d.]/g, '');
        const price = parseFloat(priceText);
        if (!isNaN(price)) {
            total += price;
        }
    });
    
    document.getElementById('bookingTotalAmount').textContent = total.toLocaleString('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).replace('PHP', '₱');
    
    const paymentOption = document.querySelector('select[name="paymentOption"]')?.value || 'full';
    const downpaymentSection = document.querySelector('.downpayment-section');
    const downpaymentAmount = document.getElementById('downpaymentAmount');
    
    if (paymentOption === 'downpayment' && downpaymentSection && downpaymentAmount) {
        const downpayment = total * 0.5;
        downpaymentSection.style.display = 'block';
        downpaymentAmount.textContent = downpayment.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).replace('PHP', '₱');
    }
}

function confirmBooking() {
    const form = document.getElementById('multipleBookingForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Collect form data
    const formData = new FormData(form);
    const bookingData = {
        firstName: formData.get('firstName'),
        lastName: formData.get('lastName'),
        contact: formData.get('contact'),
        email: formData.get('email'),
        checkIn: formData.get('checkIn'),
        checkOut: formData.get('checkOut'),
        paymentMethod: formData.get('paymentMethod'),
        paymentOption: formData.get('paymentOption'),
        discountType: formData.get('discountType') || '',
        rooms: []
    };
    
    console.log("Collected discount type:", bookingData.discountType);
    
    // Collect room data - modified to handle absence of guest count fields
    document.querySelectorAll('.selected-room-item').forEach(roomItem => {
        // Use fixed guest count since we removed the field
        const guestCount = 1;
        const roomPrice = roomItem.querySelector('.room-price').textContent;
        // Access room index from a data attribute if available, or fallback to position in list
        const roomIndex = roomItem.dataset.roomIndex || 
                          Array.from(document.querySelectorAll('.selected-room-item')).indexOf(roomItem);
        const nights = roomItem.querySelector('.nights-count').textContent;
        const roomList = JSON.parse(localStorage.getItem('roomList'));
        const room = roomList[roomIndex];
        
        // Guest names array will be empty as we removed the fields
        const guestNames = [];
        
        bookingData.rooms.push({
            ...room,
            guestCount: guestCount,
            guestNames: guestNames,
            nights: parseInt(nights),
            totalPrice: parseFloat(roomPrice.replace(/,/g, ''))
        });
    });
    
    // Show booking summary
    displayBookingSummary(bookingData);
}

// Update the Price Summary section in the modal
function updatePriceSummary(roomPrice) {
    const totalAmount = parseFloat(roomPrice);
    if (!isNaN(totalAmount)) {
        const formattedTotal = totalAmount.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).replace('PHP', '₱');
        
        document.querySelector('.modal-body .price-summary').innerHTML = `
            <div class="price-summary-row">
                <span>Total Amount:</span>
                <span>${formattedTotal}</span>
            </div>
        `;
    }
}

function displayBookingSummary(bookingData) {
    // Populate summary modal with booking data
    document.getElementById('summaryName').textContent = `${bookingData.firstName} ${bookingData.lastName}`;
    document.getElementById('summaryContact').textContent = bookingData.contact;
    document.getElementById('summaryEmail').textContent = bookingData.email;
    document.getElementById('summaryPaymentMethod').textContent = bookingData.paymentMethod;
    document.getElementById('summaryCheckIn').textContent = bookingData.checkIn;
    document.getElementById('summaryCheckOut').textContent = bookingData.checkOut;
    
    // Display rooms summary
    let roomsHtml = '';
    let total = 0;
    
    bookingData.rooms.forEach(room => {
        total += room.totalPrice;
        roomsHtml += `
            <div class="summary-room-item">
                <h6>${room.type}</h6>
                <p>Number of Nights: ${room.nights}</p>
                <p>Base Price: ₱${parseFloat(room.price).toLocaleString()} per night</p>
                <p>Total Price: ₱${room.totalPrice.toLocaleString()}</p>
            </div>
        `;
    });
    
    document.getElementById('summaryRoomsList').innerHTML = roomsHtml;
    
    // Get discount information
    const discountSelect = document.getElementById('discountType');
    let finalTotal = total;
    
    if (discountSelect && discountSelect.value) {
        // Apply 10% discount for any discount type
        const discountAmount = total * 0.1;
        finalTotal = total - discountAmount;
        
        // Show discount section
        const discountSection = document.querySelector('.discount-section');
        if (discountSection) {
            discountSection.style.display = 'block';
            
            // Update discount type with proper capitalization
            const discountTypeElement = document.getElementById('summaryDiscountType');
            if (discountTypeElement) {
                const formattedType = discountSelect.value.charAt(0).toUpperCase() + discountSelect.value.slice(1);
                discountTypeElement.textContent = `${formattedType} (10%)`;
            }
            
            // Update discount amount
            const discountAmountElement = document.getElementById('summaryDiscountAmount');
            if (discountAmountElement) {
                discountAmountElement.textContent = `-₱${discountAmount.toLocaleString(2)}`;
            }
        }
    } else {
        // Hide discount section if no discount is applied
        const discountSection = document.querySelector('.discount-section');
        if (discountSection) {
            discountSection.style.display = 'none';
        }
    }
    
    // Update total amount
    document.getElementById('summaryTotalAmount').textContent = `₱${finalTotal.toLocaleString()}`;
    
    if (bookingData.paymentOption === 'downpayment') {
        // Calculate downpayment based on final amount (after discount)
        const downpaymentAmount = finalTotal * 0.5;
        document.getElementById('summaryDownpayment').textContent = `₱${downpaymentAmount.toLocaleString()}`;
        document.querySelector('#bookingSummaryModal .downpayment-section').style.display = 'block';
    } else {
        document.querySelector('#bookingSummaryModal .downpayment-section').style.display = 'none';
    }
    
    // Hide booking form and show summary
    $('#bookingFormModal').modal('hide');
    $('#bookingSummaryModal').modal('show');
}

function editBooking() {
    $('#bookingSummaryModal').modal('hide');
    $('#bookingFormModal').modal('show');
}

function processBooking() {
    const form = document.getElementById('multipleBookingForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Show loading message using SweetAlert2
    Swal.fire({
        title: 'Processing Your Booking',
        html: 'Please wait while we confirm your reservation...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const formData = new FormData(form);
    
    // Collect room data with guest counts and names
    const roomsData = [];
    document.querySelectorAll('.selected-room-item').forEach(roomItem => {
        const guestCount = roomItem.querySelector('.guest-count').value;
        const roomPrice = roomItem.querySelector('.room-price').textContent.replace(/[^0-9.]/g, '');
        const roomIndex = roomItem.querySelector('.guest-count').dataset.roomIndex;
        const nights = roomItem.querySelector('.nights-count').textContent;
        const roomList = JSON.parse(localStorage.getItem('roomList'));
        const room = roomList[roomIndex];
        
        // Collect guest names
        const guestNames = [];
        roomItem.querySelectorAll('.guest-name').forEach(input => {
            if (input.value.trim()) {
                guestNames.push(input.value.trim());
            }
        });
        
        roomsData.push({
            ...room,
            guestCount: parseInt(guestCount),
            guestNames: guestNames,
            nights: parseInt(nights),
            totalPrice: parseFloat(roomPrice),
            room_type_id: room.id
        });
    });
    
    formData.append('rooms', JSON.stringify(roomsData));
    
    // Send booking request
    $.ajax({
        url: 'process_booking.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (result.success) {
                    // Send confirmation email
                    const emailData = {
                        to: formData.get('email'),
                        firstName: formData.get('firstName'),
                        lastName: formData.get('lastName'),
                        checkIn: formData.get('checkIn'),
                        checkOut: formData.get('checkOut'),
                        rooms: roomsData,
                        totalAmount: result.totalAmount,
                        bookingId: result.bookingId
                    };

                    // Send email confirmation
                    $.ajax({
                        url: 'send_booking_email.php',
                        type: 'POST',
                        data: JSON.stringify(emailData),
                        contentType: 'application/json',
                        success: function(emailResponse) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Successful!',
                                html: 'Your rooms have been booked successfully!<br>A confirmation email has been sent to your email address.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                localStorage.removeItem('roomList');
                                window.location.reload();
                            });
                        },
                        error: function() {
                            // Still show success but mention email issue
                            Swal.fire({
                                icon: 'success',
                                title: 'Booking Successful!',
                                html: 'Your rooms have been booked successfully!<br>However, there was an issue sending the confirmation email.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                localStorage.removeItem('roomList');
                                window.location.reload();
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        text: result.message || 'There was an error processing your booking.'
                    });
                }
            } catch (error) {
                console.error('Error processing response:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: 'There was an error processing your booking.'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Booking error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Booking Failed',
                text: 'There was an error processing your booking. Please try again.'
            });
        }
    });
}

$(document).ready(function() {
    // Function to set minimum date for check-out
    function setMinCheckOutDate() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const formattedTomorrow = tomorrow.toISOString().split('T')[0];
        
        // Set min attribute and default value for check-out date
        $('#checkOutDate').attr('min', formattedTomorrow);
        $('#checkOutDate').val(formattedTomorrow);
    }

    // Call the function when document is ready
    setMinCheckOutDate();

    // Update min date when the modal is shown
    $('#advanceCheckInModal').on('show.bs.modal', function() {
        setMinCheckOutDate();
    });

    // Add change event handler for check-out date
    $('#checkOutDate').on('change', function() {
        const selectedDate = new Date(this.value);
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);
        
        if (selectedDate < tomorrow) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Date',
                text: 'Please select a date from tomorrow onwards.'
            });
            this.value = tomorrow.toISOString().split('T')[0];
        }
        updatePaymentDetails();
    });

    // Add click handler for Complete Check In button
    $('#completeCheckInBtn').on('click', function(e) {
        e.preventDefault();
        
        // Get the form
        const form = document.getElementById('advanceCheckInForm');
        
        // Check form validity
        if (!form.reportValidity()) {
            return;
        }
        
        // Show loading state
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        // Create FormData object
        const formData = new FormData(form);
        
        // Add check-in date (current date)
        const today = new Date();
        formData.append('checkInDate', today.toISOString().split('T')[0]);
        
        // Calculate nights and total amount
        const checkOutDate = new Date(formData.get('checkOutDate'));
        const nights = Math.ceil((checkOutDate - today) / (1000 * 60 * 60 * 24));
        const price = parseFloat(formData.get('price'));
        const totalAmount = price * nights;
        
        // Add additional fields
        formData.append('nights', nights);
        formData.append('totalAmount', totalAmount);
        formData.append('confirm', 'true');
        
        // Debug log
        console.log('Form data being sent:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Send AJAX request
        $.ajax({
            url: 'process_advance_checkin.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Server response:', response);
                
                try {
                    // Parse response if it's a string
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        // Hide the modal first
                        $('#advanceCheckInModal').modal('hide');
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Check-in processed successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'checked_in.php';
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'An error occurred during check-in.'
                        });
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Server response:', xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to process check-in. Please try again.'
                });
            },
            complete: function() {
                // Re-enable button
                btn.prop('disabled', false).html('Complete Check In');
            }
        });
    });

    // Handle payment method change
    $('#paymentMethod').change(function() {
        const method = $(this).val();
        const paymentSection = $('.payment-section');
        
        if (method === 'GCash' || method === 'Maya') {
            const qrPath = method === 'GCash' ? 'uploads/gcash_qr.png' : 'uploads/maya_qr.png';
            $('#qrCodeImage').attr('src', qrPath);
            $('.payment-method-title').text(method + ' Payment QR Code');
            paymentSection.show();
            updatePaymentDetails();
        } else {
            paymentSection.hide();
        }
    });

    // Handle payment option change
    $('#paymentOption').change(function() {
        updatePaymentDetails();
    });

    // Add date validation for check-out date
    $('#checkOutDate').on('change', function() {
        validateDates();
        updatePaymentDetails();
    });
    
    function validateDates() {
        const checkOutDate = new Date($('#checkOutDate').val());
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (checkOutDate <= today) {
            alert('Check-out date must be in the future.');
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            $('#checkOutDate').val(tomorrow.toISOString().split('T')[0]);
            return false;
        }
        return true;
    }

    function updatePaymentDetails() {
        const checkOutDate = new Date($('#checkOutDate').val());
        const checkInDate = new Date();
        const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
        const basePrice = parseFloat($('#roomPriceInput').val());
        let totalAmount = basePrice * nights;
        
        // Apply discount based on selection
        const discountType = $('#discountType').val();
        let discountAmount = 0;
        let discountText = '';
        let discountPercentage = 0;
        
        if (discountType) {
            // Get the discount percentage from the selected option's data attribute
            const selectedOption = $('#discountType option:selected');
            discountPercentage = parseFloat(selectedOption.data('percentage')) || 10; // Default to 10% if not found
            discountAmount = totalAmount * (discountPercentage / 100);
            discountText = ` (${discountPercentage}% discount applied)`;
        }
        
        // Update hidden fields for form submission
        $('#discountApplied').val(discountType ? '1' : '0');
        $('#discountPercentage').val(discountPercentage);
        
        // Show or hide discount info
        if (discountAmount > 0) {
            $('#discountInfo').show();
            $('#discountAmountDisplay').text('₱' + discountAmount.toLocaleString());
        } else {
            $('#discountInfo').hide();
        }
        
        // Apply the discount
        totalAmount -= discountAmount;
        
        const paymentOption = $('#paymentOption').val();
        const amountToPay = paymentOption === 'downpayment' ? totalAmount * 0.5 : totalAmount;

        // Update base price without discount text for clarity
        $('#totalPaymentAmount').text('₱' + (basePrice * nights).toLocaleString());
        $('#paymentTypeDisplay').text(paymentOption === 'downpayment' ? 'Down Payment (50%)' : 'Full Payment');
        $('#amountToPay').text('₱' + amountToPay.toLocaleString());
    }

    // Add event listener for discount type change
    $('#discountType').change(function() {
        updatePaymentDetails();
        
        // Update any discount info that might be in a summary modal
        const discountType = $(this).val();
        if (discountType) {
            console.log('Discount selected:', discountType);
            
            // Store the selected discount type in a data attribute on the body
            // This allows other scripts to access it if needed
            $('body').attr('data-selected-discount', discountType);
            
            // Get the discount percentage from the selected option's data attribute
            const selectedOption = $(this).find('option:selected');
            const discountPercentage = parseFloat(selectedOption.data('percentage')) || 10; // Default to 10% if not found
            
            // Calculate and show discount info
            const basePrice = parseFloat($('#roomPriceInput').val()) || 0;
            const discountAmount = basePrice * (discountPercentage / 100);
            
            // If we're showing a booking summary, make sure it includes the discount
            if ($('#bookingSummaryModal').hasClass('show')) {
                // Show the discount info in the summary
                setTimeout(function() {
                    const summaryElements = $('#bookingSummaryModal').find('.discount-section, .discount-row');
                    if (summaryElements.length) {
                        summaryElements.show();
                    } else {
                        // If no discount section exists, we might need to add one
                        const priceSummary = $('#bookingSummaryModal').find('.price-summary');
                        if (priceSummary.length) {
                            // Add discount section if it doesn't exist
                            priceSummary.prepend(`
                                <div class="discount-section" style="display: block; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px; border: 1px dashed #28a745;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="font-weight: bold;">Discount:</td>
                                            <td style="text-align: right; color: #28a745;">
                                                ${discountType.charAt(0).toUpperCase() + discountType.slice(1)} (${discountPercentage}%): -₱${discountAmount.toFixed(2)}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            `);
                        }
                    }
                }, 300);
            }
        } else {
            // No discount selected
            $('body').removeAttr('data-selected-discount');
            
            // Hide discount sections if they exist
            $('.discount-section, .discount-row').hide();
        }
    });

    // Event listener for when the booking summary modal is shown
    $('#bookingSummaryModal').on('shown.bs.modal', function() {
        // Give a short delay to allow other scripts to complete
        setTimeout(function() {
            try {
                console.log('Attempting direct DOM injection for discount in booking summary modal');
                
                // Try to find the discount type
                let discountType = '';
                if ($('#discountTypeSelect').length && $('#discountTypeSelect').val()) {
                    discountType = $('#discountTypeSelect').val();
                } else if ($('#discountType').length && $('#discountType').val()) {
                    discountType = $('#discountType').val();
                } else if ($('[name="discountType"]').length && $('[name="discountType"]').val()) {
                    discountType = $('[name="discountType"]').val();
                }
                
                if (!discountType || discountType === '') {
                    console.log('No discount type found, stopping injection');
                    return;
                }
                
                console.log('Direct injection - discount type:', discountType);
                
                // Find the Price Summary section that matches the screenshot
                const modal = document.getElementById('bookingSummaryModal');
                if (!modal) {
                    console.log('Modal not found');
                    return;
                }
                
                // Look for the Price Summary heading
                const priceSummaryHeadings = modal.querySelectorAll('h6, div.price-summary');
                let priceSummarySection = null;
                
                for (const heading of priceSummaryHeadings) {
                    if (heading.textContent.includes('Price Summary') || heading.classList.contains('price-summary')) {
                        priceSummarySection = heading.parentElement;
                        break;
                    }
                }
                
                // If no specific heading found, look for any element containing total amount
                if (!priceSummarySection) {
                    const totalElements = modal.querySelectorAll('*');
                    for (const el of totalElements) {
                        if (el.textContent.includes('Total Amount')) {
                            priceSummarySection = el.parentElement;
                            break;
                        }
                    }
                }
                
                if (!priceSummarySection) {
                    console.log('Price summary section not found');
                    return;
                }
                
                console.log('Found price summary section:', priceSummarySection);
                
                // Find the total amount element
                const totalElements = priceSummarySection.querySelectorAll('*');
                let totalElement = null;
                
                for (const el of totalElements) {
                    if (el.textContent.includes('Total Amount')) {
                        totalElement = el;
                        break;
                    }
                }
                
                if (!totalElement) {
                    console.log('Total element not found');
                    return;
                }
                
                console.log('Found total element:', totalElement.textContent);
                
                // Extract the total amount
                let originalTotal = 0;
                const match = totalElement.textContent.match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
                if (match && match[1]) {
                    originalTotal = parseFloat(match[1].replace(/,/g, ''));
                } else {
                    console.log('Could not extract total amount');
                    return;
                }
                
                // Get the discount percentage from the selected option
                let discountPercentage = 10; // Default to 10%
                const selectedOption = $('#discountType option:selected');
                if (selectedOption.length && selectedOption.data('percentage')) {
                    discountPercentage = parseFloat(selectedOption.data('percentage'));
                }
                
                const discountAmount = originalTotal * (discountPercentage / 100);
                const finalTotal = originalTotal - discountAmount;
                
                // Check if discount element already exists
                const existingDiscount = priceSummarySection.querySelector('.discount-row, .discount-section');
                
                if (existingDiscount) {
                    console.log('Discount element already exists');
                    return;
                }
                
                // Create discount element that matches the screenshot style
                const discountDiv = document.createElement('div');
                discountDiv.className = 'discount-row';
                discountDiv.style.margin = '10px 0';
                discountDiv.style.padding = '8px';
                discountDiv.style.background = '#f8f9fa';
                discountDiv.style.borderRadius = '4px';
                discountDiv.style.border = '1px dashed #28a745';
                
                discountDiv.innerHTML = `
                    <table style="width: 100%;">
                        <tr>
                            <td style="font-weight: bold;">Discount:</td>
                            <td style="text-align: right; color: #28a745;">
                                ${discountType.charAt(0).toUpperCase() + discountType.slice(1)} (${discountPercentage}%): -₱${discountAmount.toFixed(2)}
                            </td>
                        </tr>
                    </table>
                `;
                
                // Insert the discount element before the total element
                if (totalElement.parentElement) {
                    totalElement.parentElement.insertBefore(discountDiv, totalElement);
                    console.log('Successfully inserted discount element before total');
                    
                    // Update the total amount value
                    const totalText = totalElement.textContent;
                    totalElement.textContent = totalText.replace(
                        /₱\s*([0-9,]+(\.[0-9]+)?)/,
                        `₱${finalTotal.toFixed(2)}`
                    );
                    
                    console.log('Updated total amount to:', finalTotal.toFixed(2));
                }
            } catch (error) {
                console.error('Error in direct DOM injection:', error);
            }
        }, 500);
    });
});

// Add this code after your existing JavaScript
$(document).ready(function() {
    // Handle discount type selection
    $('#discountType').on('change', function() {
        const discountId = $(this).val();
        if (!discountId) {
            // Clear discount info if no discount selected
            $('#discountInfo').remove();
            return;
        }

        // Fetch discount details via AJAX
        $.ajax({
            url: 'get_discount_details.php',
            method: 'POST',
            data: { discount_id: discountId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove existing discount info if any
                    $('#discountInfo').remove();
                    
                    // Create and append new discount info
                    const discountInfo = `
                        <div id="discountInfo" class="mt-3">
                            <h6>Discount Details</h6>
                            <p><strong>Type:</strong> ${response.data.name}</p>
                            <p><strong>Percentage:</strong> ${response.data.percentage}%</p>
                            <p><strong>Description:</strong> ${response.data.description}</p>
                        </div>
                    `;
                    
                    // Insert after the discount type select
                    $(discountInfo).insertAfter('#discountType');
                    
                    // Update hidden fields
                    $('#discountApplied').val(1);
                    $('#discountPercentage').val(response.data.percentage);
                    
                    // Trigger price recalculation if needed
                    if (typeof updateBookingSummary === 'function') {
                        updateBookingSummary();
                    }
                } else {
                    console.error('Failed to fetch discount details:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching discount details:', error);
            }
        });
    });
});

// === [TAG: RemoveGuestFields] REMOVE GUEST FIELDS FROM BOOKING MODAL ===
$(document).ready(function() {
    // Observer to watch for any modal being shown
    $(document).on('shown.bs.modal', function(e) {
        // Immediately hide the Number of Guests and Guest Names sections
        $('.modal-body').find('div, p, label, input').each(function() {
            if (
                $(this).text().includes('Number of Guests') ||
                $(this).text().includes('Additional fee of') ||
                $(this).text().includes('Guest Names') ||
                $(this).text().includes('Guest 1 Name') ||
                ($(this).attr('placeholder') && $(this).attr('placeholder').includes('guest name'))
            ) {
                $(this).closest('.form-group, div').hide();
            }
        });
        // Also try direct removal - remove the elements completely rather than just hiding
        $('.modal-body').find('div:contains("Number of Guests")').remove();
        $('.modal-body').find('div:contains("Guest Names:")').remove();
        $('.modal-body').find('div:contains("Guest 1 Name")').remove();
        $('.modal-body').find('input[placeholder*="guest name"]').closest('.form-group, div').remove();
    });
});

// === [TAG: DynamicAdultChildrenFields] DYNAMIC ADULT/CHILDREN NAME FIELDS ===
function renderNameFields(containerId, count, labelBase, inputNameBase) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    for (let i = 1; i <= count; i++) {
        const div = document.createElement('div');
        div.className = 'form-group';
        const label = document.createElement('label');
        label.textContent = `${labelBase} ${i} Name`;
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control';
        input.name = `${inputNameBase}[${i}]`;
        input.required = true;
        div.appendChild(label);
        div.appendChild(input);
        container.appendChild(div);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const numAdultsInput = document.getElementById('numAdults');
    const numChildrenInput = document.getElementById('numChildren');
    // Initial render
    renderNameFields('adultNamesContainer', numAdultsInput.value, 'Adult', 'adult_names');
    renderNameFields('childrenNamesContainer', numChildrenInput.value, 'Child', 'children_names');
    // On change
    numAdultsInput.addEventListener('input', function() {
        let val = parseInt(this.value) || 1;
        if (val < 1) { this.value = 1; val = 1; }
        renderNameFields('adultNamesContainer', val, 'Adult', 'adult_names');
    });
    numChildrenInput.addEventListener('input', function() {
        let val = parseInt(this.value) || 0;
        if (val < 0) { this.value = 0; val = 0; }
        renderNameFields('childrenNamesContainer', val, 'Child', 'children_names');
    });
});
</script>