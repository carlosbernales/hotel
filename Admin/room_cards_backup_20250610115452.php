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

        // Query to get all active room types with their availability from room_numbers
        $query = "SELECT 
            rt.room_type_id, 
            rt.room_type as type, 
            rt.price, 
            rt.description, 
            rt.beds, 
            rt.rating, 
            rt.image,
            (
                SELECT COUNT(*)
                FROM room_numbers rn
                WHERE rn.room_type_id = rt.room_type_id 
                AND LOWER(rn.status) IN ('active', 'available')
                AND NOT EXISTS (
                    SELECT 1 
                    FROM bookings b 
                    WHERE b.room_number COLLATE utf8mb4_unicode_ci = rn.room_number COLLATE utf8mb4_unicode_ci
                    AND b.status IN ('confirmed', 'checked_in')
                )
            ) as available_rooms,
            (
                SELECT COUNT(*)
                FROM room_numbers rn
                WHERE rn.room_type_id = rt.room_type_id 
                AND LOWER(rn.status) IN ('active', 'available')
            ) as total_rooms,
            CASE 
                WHEN (
                    SELECT COUNT(*)
                    FROM room_numbers rn
                    WHERE rn.room_type_id = rt.room_type_id 
                    AND LOWER(rn.status) IN ('active', 'available')
                    AND NOT EXISTS (
                        SELECT 1 
                        FROM bookings b 
                        WHERE b.room_number COLLATE utf8mb4_unicode_ci = rn.room_number COLLATE utf8mb4_unicode_ci
                        AND b.status IN ('confirmed', 'checked_in')
                    )
                ) > 0 THEN 'Available'
                ELSE 'Not Available'
            END as status
        FROM room_types rt
        WHERE rt.status = 'active'
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
                ", Room Type: " . $row['type'] . 
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
                $image = '/assets/img/rooms/default.jpg';
            }
            
            // Store cleaned image path in room details
            $roomDetails[] = array(
                'id' => $row['room_type_id'],
                'room_type_id' => $row['room_type_id'], // Add explicit room_type_id field
                'type' => $row['type'],
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
                        $upload_path = 'uploads/rooms/';  // Changed from 'aa/uploads/rooms/'
                        $default_image = 'assets/img/rooms/default.jpg';
                        
                        // Get the filename from the stored path and clean it
                        $filename = '';
                        if (!empty($room['image'])) {
                            // Remove any full paths that might be stored in the database
                            $filename = basename($room['image']);
                            
                            // If the image path starts with /uploads/rooms/, extract just the filename
                            if (strpos($room['image'], '/uploads/rooms/') !== false) {
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
                        
                        <?php if ($room['status'] !== 'Available' || $room['available_rooms'] <= 0): ?>
                            <div class="unavailable-badge">
                                <?php echo $room['status']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="room-info">
                        <h3><?php echo $room['type']; ?></h3>
                        <p class="room-description"><?php echo $room['description']; ?></p>
                        <?php if (!empty($room['beds'])): ?>
                            <p class="room-beds">Beds: <?php echo $room['beds']; ?></p>
                        <?php endif; ?>
                        <p class="room-price">₱<?php echo number_format($room['price'], 2); ?>/night</p>
                        <p class="room-availability">
                            <?php if ($room['total_rooms'] > 0): ?>
                                Available: <?php echo $room['available_rooms']; ?>/<?php echo $room['total_rooms']; ?> rooms
                            <?php else: ?>
                                No rooms currently available
                            <?php endif; ?>
                        </p>
                        
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
                            <button class="btn btn-advance-check-in<?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? ' disabled' : ''; ?>" 
                                    <?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? 'disabled' : ''; ?>
                                    onclick="advanceCheckIn(
                                        <?php echo $room['id']; ?>,
                                        '<?php echo addslashes((string)($room['type'] ?? '')); ?>',
                                        <?php echo $room['discounted_price'] ?: $room['price']; ?>,
                                        '<?php echo addslashes((string)($room['capacity'] ?? '1')); ?>',
                                        '<?php echo htmlspecialchars((string)($room['image'] ?? 'uploads/rooms/default.jpg'), ENT_QUOTES); ?>'
                                    )">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </button>
                            <button class="btn btn-reserve<?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? ' disabled' : ''; ?>" 
                                    <?php echo ($room['status'] !== 'Available' || $room['available_rooms'] <= 0) ? 'disabled' : ''; ?>
                                    onclick="reserveBooking(<?php echo $room['id']; ?>)">
                                <i class="fas fa-calendar-check"></i> Reserve Room
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

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
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h4 class="modal-title fw-bold" id="roomDetailsModalLabel">Room Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <img id="modalRoomImage" src="" alt="Room Image" class="img-fluid rounded shadow-sm" style="width: 100%; max-height: 400px; object-fit: cover;">
                            </div>
                            <div class="col-md-6">
                                <h2 id="modalRoomType" class="fw-bold mb-4" style="color: #333; font-size: 2.2rem;"></h2>
                                <div id="modalRoomRating" class="rating mb-4" style="font-size: 1.2rem;"></div>
                                <div id="modalRoomPrice" class="price-container mb-4" style="font-size: 1.5rem; font-weight: 600;"></div>
                                <div id="modalRoomCapacity" class="capacity mb-4" style="font-size: 1.2rem;"></div>
                                <div id="modalRoomDescription" class="description mb-4 p-3 bg-light rounded" style="font-size: 1.1rem; line-height: 1.6;"></div>
                                <div class="amenities-section">
                                    <h5 class="fw-bold mb-3">Room Amenities</h5>
                                    <div id="modalRoomAmenities" class="amenities d-flex flex-wrap gap-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                            
                            <!-- Room Number Selection -->
                            <div class="form-group">
                                <label for="roomNumber">Room Number</label>
                                <select class="form-control" id="roomNumber" name="roomNumber" required>
                                    <option value="">Loading rooms...</option>
                                </select>
                            </div>
                            
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

        <!-- Cart Modal -->
        <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cartModalLabel">Your Selected Rooms</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="cartItems">
                            <!-- Cart items will be dynamically inserted here by showCart() -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Continue Browsing</button>
                        <button type="button" class="btn btn-danger" onclick="clearCart()">Clear Cart</button>
                        <button type="button" class="btn btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information Modal -->
<div class="modal fade" id="customerInfoModal" tabindex="-1" aria-labelledby="customerInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerInfoModalLabel">Booking Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customerInfoForm" onsubmit="return false;">
                    <!-- Personal Information -->
                    <div class="section-title mb-3">
                        <h6 class="fw-bold">Personal Information</h6>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail" name="customerEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customerPhone" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="customerPhone" name="customerPhone" required>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="section-title mb-3 mt-4">
                        <h6 class="fw-bold">Booking Dates</h6>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="checkInDate" class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" id="checkInDate" name="checkInDate" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="checkOutDate" class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" id="checkOutDate" name="checkOutDate" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>

                    <!-- Payment Options -->
                    <div class="section-title mb-3 mt-4">
                        <h6 class="fw-bold">Payment Options</h6>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentOption" id="fullPayment" value="full" checked>
                            <label class="form-check-label" for="fullPayment">
                                Full Payment
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentOption" id="downPayment" value="down">
                            <label class="form-check-label" for="downPayment">
                                Down Payment (₱1,500)
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method Dropdown -->
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label fw-500">Payment Method</label>
                                                    <select class="form-select shadow-sm" id="paymentMethod" name="paymentMethod" required>
                            <option value="" selected disabled>Select payment method</option>
                            <option value="cash">💵 Cash</option>
                            <option value="gcash">💳 GCash</option>
                            <option value="maya">💳 Maya</option>
                        </select>
                    </div>

                    <!-- Guest Information -->
                    <div class="section-title mb-3 mt-4">
                        <h6 class="fw-bold">Guest Information</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addGuestField()">
                            <i class="fas fa-plus"></i> Add Guest
                        </button>
                    </div>
                    <div id="guestsContainer">
                        <!-- Guest fields will be added here dynamically -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="showBookingSummary()">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Summary Modal -->
<div class="modal fade" id="bookingSummaryModal" tabindex="-1" aria-labelledby="bookingSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingSummaryModalLabel">Booking Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookingSummaryContent">
                <!-- Summary content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="$('#bookingSummaryModal').modal('hide'); $('#customerInfoModal').modal('show');">Back</button>
                <button type="button" class="btn btn-primary" onclick="processBooking()">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

        <style>
        /* Form Styles */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        
        .form-control:focus {
            color: #212529;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        /* Cart Item Styles */
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
            margin-right: 15px;
            border-radius: 8px;
            overflow: hidden;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item-details h5 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            color: #333;
        }

        .cart-item-details p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .cart-item-actions {
            margin-left: 15px;
        }

        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: right;
        }

        .total-section h4 {
            margin: 0;
            color: #28a745;
            font-weight: bold;
        }

        /* Responsive adjustments for cart items */
        @media (max-width: 576px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .cart-item-image {
                margin-bottom: 10px;
            }
            
            .cart-item-actions {
                margin: 10px 0 0 0;
                width: 100%;
            }
        }

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

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
        }

        .quantity-controls button {
            width: 28px;
            height: 28px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .quantity-controls button:hover:not(:disabled) {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .quantity-controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-display {
            min-width: 30px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            padding: 4px 8px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
            margin-right: 15px;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item-details h5 {
            margin-bottom: 8px;
            color: #212529;
        }

        .cart-item-details p {
            margin-bottom: 4px;
            color: #6c757d;
        }

        .room-total {
            font-weight: 600;
            color: #212529;
            margin-top: 8px;
        }

        .cart-item-actions {
            margin-left: 15px;
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title {
            color: #212529;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .section-title {
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
        }

        .guest-entry {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .guest-entry .remove-guest {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #dc3545;
        }

        .guest-entry .remove-guest:hover {
            color: #c82333;
        }

        .modal-lg {
    max-width: 800px;
}

.form-select {
    padding: 0.5rem 1rem;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    background-color: #fff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.15);
    outline: 0;
}

.form-select:hover {
    border-color: #b3d7ff;
}

.form-select option {
    padding: 10px;
}

.form-select option:first-child {
    color: #6c757d;
}

.form-select:required:invalid {
    color: #6c757d;
}

.form-select option[value=""][disabled] {
    display: none;
}

.form-select option:not([value=""][disabled]) {
    color: #212529;
}

/* Booking Summary Styles */
.booking-summary-popup {
    font-family: 'Arial', sans-serif;
}

.booking-summary-content {
    padding: 20px;
}

.summary-section {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.summary-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.summary-heading {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #3498db;
    display: inline-block;
}

.summary-section p {
    margin-bottom: 8px;
    color: #444;
}

.summary-section strong {
    color: #2c3e50;
}

.total-amount {
    font-size: 1.1em;
    color: #2c3e50;
    padding: 10px 0;
    margin-top: 10px;
    border-top: 1px dashed #ddd;
}

.amount-due {
    font-size: 1.1em;
    color: #2c3e50;
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
}

/* Booking Summary Modal Styles */
.booking-summary-modal .swal2-popup {
    padding: 2rem;
    max-width: 800px;
    width: 90%;
}

.booking-summary-modal .text-left {
    text-align: left;
}

.booking-summary-modal .summary-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.booking-summary-modal .summary-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.booking-summary-modal h6 {
    color: #333;
    margin-bottom: 1rem;
}

.booking-summary-modal p {
    margin-bottom: 0.5rem;
    color: #666;
}

.booking-summary-modal strong {
    color: #333;
}

.booking-summary-modal .ms-3 {
    margin-left: 1rem;
}

.booking-summary-modal .mt-2 {
    margin-top: 0.75rem;
}

.booking-summary-modal .mt-3 {
    margin-top: 1rem;
}

.booking-summary-modal .mt-4 {
    margin-top: 1.5rem;
}

.booking-summary-modal .pt-2 {
    padding-top: 0.75rem;
}

.booking-summary-modal .border-top {
    border-top: 1px solid #eee;
}

.booking-summary-modal .bg-light {
    background-color: #f8f9fa;
}

.booking-summary-modal .rounded {
    border-radius: 0.25rem;
}

.booking-summary-modal .fw-bold {
    font-weight: bold;
}

.booking-summary-modal .mb-3 {
    margin-bottom: 1rem;
}

.booking-summary-modal .p-2 {
    padding: 0.75rem;
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

        // Add to List functionality
        function addToList(roomId, roomType, price, capacity, image) {
            try {
                if (!roomId) {
                    throw new Error('Room type ID is missing from room data');
                }

                // Debug log before creating room object
                console.log('Adding room to list with parameters:', {
                    roomId: roomId,
                    roomType: roomType,
                    price: price,
                    capacity: capacity,
                    image: image
                });

                // Create room object with proper room_type_id
                const room = {
                    id: parseInt(roomId),
                    room_type_id: parseInt(roomId), // Ensure both id and room_type_id are set
                    type: roomType,
                    price: parseFloat(price),
                    capacity: parseInt(capacity),
                    image: image,
                    addedAt: new Date().toISOString()
                };

                // Debug log the room object
                console.log('Room object created:', room);

                // Get existing list
                let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
                
                // Check if room is already in list
                const existingRoom = roomList.find(r => r.room_type_id === room.room_type_id);
                if (existingRoom) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Room Already Added',
                        text: 'This room is already in your list.'
                    });
                    return;
                }

                // Add room to list
                roomList.push(room);
                
                // Debug log the updated room list
                console.log('Updated room list:', roomList);
                
                // Save to localStorage
                localStorage.setItem('roomList', JSON.stringify(roomList));
                
                // Also ensure the cartItems has the same content (for compatibility)
                localStorage.setItem('cartItems', JSON.stringify(roomList));
                
                updateCartCount();
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Room added to list successfully!'
                });
            } catch (error) {
                console.error('Error adding room to list:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        }

        // Update cart count
        function updateCartCount() {
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            $('.cart-count').text(roomList.length);
        }

        // Initialize cart if it doesn't exist
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize if roomList doesn't exist
            if (!localStorage.getItem('roomList')) {
                localStorage.setItem('roomList', JSON.stringify([]));
            }
            updateCartCount();
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
        <span class="amenity p-2 bg-white rounded shadow-sm" style="font-size: 1.1rem;">
            <i class="${amenity.icon} text-primary me-2"></i>
            ${amenity.name}
        </span>
    `).join('');

            // Show the modal using jQuery
            $('#roomDetailsModal').modal('show');
        }

        // Function to fetch available room numbers
        function fetchRoomNumbers(roomTypeId) {
            // Clear existing options
            const roomNumberSelect = document.getElementById('roomNumber');
            roomNumberSelect.innerHTML = '<option value="">Select a room...</option>';
            
            // Show loading state
            const loadingOption = document.createElement('option');
            loadingOption.value = '';
            loadingOption.textContent = 'Loading rooms...';
            roomNumberSelect.appendChild(loadingOption);
            
            // Fetch available rooms via AJAX
            $.ajax({
                url: 'get_available_rooms.php',
                type: 'GET',
                data: {
                    room_type_id: roomTypeId,
                    check_in: $('#checkInDate').val(),
                    check_out: $('#checkOutDate').val()
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Room data received:', response); // Debug log
                    
                    roomNumberSelect.innerHTML = ''; // Clear loading message
                    
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Select Room Number --';
                    roomNumberSelect.appendChild(defaultOption);
                    
                    if (response.success && response.rooms && response.rooms.length > 0) {
                        // Add room numbers to the dropdown
                        response.rooms.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.room_number_id;
                            option.textContent = room.room_number + (room.floor ? ` (Floor ${room.floor})` : '');
                            roomNumberSelect.appendChild(option);
                        });
                    } else {
                        // No rooms available or error
                        const errorMessage = response.message || 'No rooms available';
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = errorMessage;
                        roomNumberSelect.appendChild(option);
                        console.error('No rooms available or error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching room numbers:', error);
                    roomNumberSelect.innerHTML = '<option value="">Error loading rooms. Please try again.</option>';
                }
            });
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
                // Use the direct path to the image
                let imagePath = 'uploads/rooms/room_type_681ed761b40cb.jpg';
                
                // Log the path for debugging
                console.log('Using direct image path:', imagePath);
                
                // Fetch available room numbers
                fetchRoomNumbers(roomId);
                
                // Set room details in the modal
                $('#advanceCheckInModal .room-type').text(roomDetails.type);
                $('#roomTypeInput').val(roomDetails.type);
                $('#roomIdInput').val(roomId);
                $('#roomPriceInput').val(roomDetails.price);
                $('#roomCapacity').val(roomDetails.capacity);
                $('#advanceCheckInModal .room-image').attr('src', imagePath).on('error', function() {
                    console.error('Failed to load image:', imagePath);
                    $(this).attr('src', '/Admin/assets/img/rooms/default.jpg');
                });
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

                // Add event listeners for date changes
                $('#checkInDate, #checkOutDate').on('change', function() {
                    // Re-fetch room numbers when dates change
                    fetchRoomNumbers(roomId);
                });
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

        async function addToList(roomId, roomType, price, capacity, image) {
            try {
                // First, update the room availability in the database
                const response = await fetch('update_room_availability.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=decrement&room_type_id=${roomId}`
                });

                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Failed to update room availability');
                }
                
                // Process the image path to ensure it's correct
                let processedImage = image;
                
                // If the image path is empty or doesn't start with 'uploads/rooms/', try to fix it
                if (processedImage && !processedImage.startsWith('uploads/rooms/') && !processedImage.startsWith('/uploads/rooms/')) {
                    // Check if it's a full URL
                    if (!processedImage.startsWith('http') && !processedImage.startsWith('data:')) {
                        // Remove any leading slashes to avoid double slashes
                        processedImage = processedImage.replace(/^\/+/g, '');
                        // Add the correct path
                        processedImage = 'uploads/rooms/' + processedImage;
                        // Ensure we don't have double slashes
                        processedImage = processedImage.replace(/([^:]\/)\/+/g, '$1');
                    }
                } else if (processedImage && processedImage.startsWith('/uploads/rooms/')) {
                    // Remove leading slash if present
                    processedImage = processedImage.substring(1);
                }
                
                // Create room object with processed image path
                const room = {
                    id: roomId,
                    room_type_id: roomId, // Explicitly set room_type_id
                    type: roomType,
                    price: parseFloat(price),
                    capacity: capacity,
                    image: processedImage || 'assets/img/rooms/default.jpg', // Fallback to default image if empty
                    addedAt: new Date().toISOString()
                };
                
                console.log('Adding room to list:', room);
                
                // Get existing list
                let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
                
                // Check if room is already in list
                if (roomList.some(item => item.id === roomId)) {
                    // If room is already in the list, increment the availability back
                    await fetch('update_room_availability.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=increment&room_type_id=${roomId}`
                    });
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Already in List',
                        text: 'This room is already in your list!',
                        timer: 1500,
                        showConfirmButton: false
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
            } catch (error) {
                console.error('Error adding to list:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to add room to list. Please try again.'
                });
            }
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
                    const quantity = room.quantity || 1;
                    const roomTotal = parseFloat(room.price) * quantity;
                    total += roomTotal;
                    
                    html += `
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="${room.image}" alt="${room.type}" onerror="this.src='assets/img/rooms/default.jpg'">
                            </div>
                            <div class="cart-item-details">
                                <h5>${room.type}</h5>
                                <p>Capacity: ${room.capacity}</p>
                                <p>Price: ₱${parseFloat(room.price).toLocaleString()}/night</p>
                                <p>Available: ${room.available_rooms} rooms</p>
                                <div class="quantity-controls">
                                    <button class="btn" onclick="updateQuantity(${index}, -1)">−</button>
                                    <span class="quantity-display">${quantity}</span>
                                    <button class="btn" onclick="updateQuantity(${index}, 1)" ${quantity >= room.available_rooms ? 'disabled' : ''}>+</button>
                                </div>
                                <p class="room-total">Total: ₱${roomTotal.toLocaleString()}</p>
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
            
            // Update the modal footer to use the new function
            const modalFooter = document.querySelector('#cartModal .modal-footer');
            if (modalFooter) {
                modalFooter.innerHTML = `
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="clearCart()">Clear Cart</button>
                    <button type="button" class="btn btn-warning" onclick="showCustomerInfoModal()">Book Selected Rooms</button>
                `;
            }
            
            $('#cartModal').modal('show');
        }

        function updateQuantity(index, change) {
            let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            const room = roomList[index];
            
            if (!room.quantity) {
                room.quantity = 1;
            }
            
            const newQuantity = room.quantity + change;
            
            // Check if the new quantity is valid (between 1 and available rooms)
            if (newQuantity < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Quantity',
                    text: 'Quantity cannot be less than 1'
                });
                return;
            }

            // Check if the new quantity exceeds available rooms
            if (newQuantity > room.available_rooms) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum Limit Reached',
                    text: `Only ${room.available_rooms} rooms available for this type`
                });
                return;
            }
            
            room.quantity = newQuantity;
            roomList[index] = room;
            
            localStorage.setItem('roomList', JSON.stringify(roomList));
            localStorage.setItem('cartItems', JSON.stringify(roomList)); // For compatibility
            
            // Update the display
            showCart();
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
            
            // Get room list from localStorage
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            if (roomList.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Rooms Selected',
                    text: 'Please select at least one room before booking.'
                });
                return;
            }
            
            // Debug log
            console.log("Room list from localStorage:", roomList);
            
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
            
            // Add each room to the booking data
            document.querySelectorAll('.selected-room-item').forEach((roomItem, index) => {
                const room = roomList[index];
                if (!room) {
                    console.error('Room not found in localStorage for index:', index);
                    return;
                }
                
                // Debug log
                console.log("Processing room:", room);
                
                // Get the actual room_type_id from the original room data
                const roomData = {
                    id: room.id,
                    room_type_id: room.id, // This is the room_type_id from when we added it to the list
                    type: room.type,
                    price: parseFloat(room.price),
                    capacity: parseInt(room.capacity),
                    totalPrice: parseFloat(room.price) * parseInt(roomItem.querySelector('.nights-count').textContent),
                    nights: parseInt(roomItem.querySelector('.nights-count').textContent),
                    guestCount: 1
                };
                
                // Debug log
                console.log("Prepared room data:", roomData);
                
                bookingData.rooms.push(roomData);
            });
            
            // Debug log
            console.log("Final booking data:", bookingData);
            
            // Send booking data to server
            const ajaxData = new FormData();
            for (let key in bookingData) {
                if (key === 'rooms') {
                    ajaxData.append(key, JSON.stringify(bookingData[key]));
                } else {
                    ajaxData.append(key, bookingData[key]);
                }
            }
            
            // Show loading state
            Swal.fire({
                title: 'Processing Booking',
                text: 'Please wait while we process your reservation...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            $.ajax({
                url: 'process_booking.php',
                type: 'POST',
                data: ajaxData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response.success) {
                        // Clear the room list from localStorage
                        localStorage.removeItem('roomList');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Successful!',
                            text: 'Your booking has been confirmed.'
                        }).then(() => {
                            window.location.href = 'index.php?reservation';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Booking Failed',
                            text: response.message || 'An error occurred while processing your booking.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        text: 'An error occurred while processing your booking. Please try again.'
                    });
                }
            });
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
            const form = document.getElementById('customerInfoForm');
            if (!form.checkValidity() || !validateBookingDates()) {
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
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            
            console.log('Processing room items. Total rooms in list:', roomList.length);
            
            document.querySelectorAll('.selected-room-item').forEach((roomItem, index) => {
                const guestCount = roomItem.querySelector('.guest-count').value;
                const roomPrice = roomItem.querySelector('.room-price').textContent.replace(/[^0-9.]/g, '');
                const roomIndex = roomItem.querySelector('.guest-count').dataset.roomIndex;
                const nights = roomItem.querySelector('.nights-count').textContent;
                const room = roomList[roomIndex];
                
                if (!room) {
                    console.error('Room not found in roomList at index:', roomIndex);
                    console.log('Available roomList:', roomList);
                    throw new Error('Room data is missing. Please try selecting the room again.');
                }
                
                console.log(`Processing room ${index + 1}:`, {
                    roomId: room.id,
                    roomType: room.type,
                    roomPrice: room.price,
                    room_type_id: room.room_type_id || room.id,
                    roomData: room
                });
                
                // Collect guest names
                const guestNames = [];
                roomItem.querySelectorAll('.guest-name').forEach(input => {
                    if (input.value.trim()) {
                        guestNames.push(input.value.trim());
                    }
                });
                
                // Debug log the original room data
                console.log('Original room data from localStorage:', room);
                
                // Create a clean room data object with all required fields
                const roomData = {
                    id: room.id,
                    room_type_id: room.room_type_id || room.id, // Always set room_type_id, fallback to id if not set
                    type: room.type,
                    price: parseFloat(room.price),
                    capacity: room.capacity,
                    image: room.image,
                    guestCount: parseInt(guestCount),
                    guestNames: guestNames,
                    nights: parseInt(nights),
                    totalPrice: parseFloat(roomPrice)
                };
                
                console.log('Prepared room data for server:', roomData);
                
                console.log('Room data prepared for server:', roomData);
                
                console.log('Room data being sent to server:', roomData);
                roomsData.push(roomData);
            });
            
            const roomsJson = JSON.stringify(roomsData);
            console.log('=== ROOM DATA BEING SENT TO SERVER ===');
            console.log('Final rooms JSON:', roomsJson);
            console.log('Room data structure:', roomsData);
            
            // Log each room's data in detail
            roomsData.forEach((room, index) => {
                console.log(`Room ${index + 1}:`, {
                    id: room.id,
                    room_type_id: room.room_type_id,
                    type: room.type,
                    price: room.price,
                    _debug: room._debug
                });
            });
            
            // Add all required fields to formData
            formData.append('rooms', roomsJson);
            formData.append('email', formData.get('customerEmail'));
            formData.append('contact', formData.get('customerPhone'));
            
            // Get dates directly from the form
            formData.append('check_in', document.getElementById('checkInDate').value);
            formData.append('check_out', document.getElementById('checkOutDate').value);
            
            formData.append('first_name', formData.get('firstName'));
            formData.append('last_name', formData.get('lastName'));
            formData.append('payment_option', formData.get('paymentOption'));
            formData.append('payment_method', formData.get('paymentMethod'));
            formData.append('total_amount', totalAmount);
            
            // Add room_type_id from the first room in roomsData
            if (roomsData.length > 0) {
                const firstRoom = roomsData[0];
                formData.append('room_type_id', firstRoom.room_type_id || firstRoom.id); // Use room_type_id or fallback to id
            }
            
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

        function reserveBooking(roomId) {
            try {
                // Get room details
                const roomCard = event.target.closest('.room-card');
                const roomType = roomCard.querySelector('h3').textContent;
                const priceText = roomCard.querySelector('.room-price').textContent;
                const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
                const capacityText = roomCard.querySelector('.capacity').textContent;
                const capacity = capacityText.match(/\d+/)[0];
                const imagePath = roomCard.querySelector('img').getAttribute('src');
                
                // Get available rooms count
                const availabilityText = roomCard.querySelector('.room-availability').textContent;
                const availableRooms = parseInt(availabilityText.match(/\d+/)[0]) || 0;

                // Create room object
                const room = {
                    id: roomId,
                    room_type_id: roomId, // Add this line to explicitly set room_type_id
                    type: roomType,
                    price: price,
                    capacity: capacity,
                    image: imagePath,
                    quantity: 1,
                    available_rooms: availableRooms,
                    timestamp: new Date().getTime()
                };

                // Get existing cart
                let roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
                
                // Check if room is already in cart
                const existingRoom = roomList.find(item => item.id === roomId);
                if (existingRoom) {
                    // Remove the room if it exists (this helps refresh the data)
                    roomList = roomList.filter(item => item.id !== roomId);
                }

                // Add room to cart
                roomList.push(room);
                localStorage.setItem('roomList', JSON.stringify(roomList));
                localStorage.setItem('cartItems', JSON.stringify(roomList));

                // Update cart count
                updateCartCount();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Room Reserved',
                    text: 'Room has been added to your selected rooms successfully!',
                    showConfirmButton: true,
                    confirmButtonText: 'View Cart',
                    showCancelButton: true,
                    cancelButtonText: 'Continue Browsing'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showCart();
                    }
                });

            } catch (error) {
                console.error('Error reserving room:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to reserve room. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        }

        function updateCartDisplay() {
            const selectedRooms = JSON.parse(localStorage.getItem('selectedRooms') || '[]');
            const cartContainer = document.querySelector('.cart-items') || document.querySelector('.selected-rooms');
            
            if (!cartContainer) {
                console.error('Cart container not found');
                return;
            }

            if (selectedRooms.length === 0) {
                cartContainer.innerHTML = '<p>Your cart is empty</p>';
                return;
            }

            let html = '';
            selectedRooms.forEach((room, index) => {
                html += `
                    <div class="cart-item">
                        <img src="${room.image}" alt="${room.type}" style="width: 100px; height: 75px; object-fit: cover;">
                        <div class="cart-item-details">
                            <h5>${room.type}</h5>
                            <p>₱${room.price.toLocaleString()}/night</p>
                            <p>Max Capacity: ${room.capacity} persons</p>
                        </div>
                        <button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            });
            cartContainer.innerHTML = html;

            // Update cart count if it exists
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = selectedRooms.length;
            }
        }

        function removeFromCart(index) {
            let selectedRooms = JSON.parse(localStorage.getItem('selectedRooms') || '[]');
            selectedRooms.splice(index, 1);
            localStorage.setItem('selectedRooms', JSON.stringify(selectedRooms));
            updateCartDisplay();
        }

        // Initialize cart display when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCartDisplay();
        });

        function calculateTotalAmount(roomList) {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            const nights = calculateNights(checkInDate, checkOutDate);
            
            return roomList.reduce((total, room) => {
                return total + (parseFloat(room.price) * nights * (room.quantity || 1));
            }, 0);
        }

        function showCustomerInfoModal() {
            // Set minimum date for check-in and check-out
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('checkInDate').min = today;
            document.getElementById('checkOutDate').min = today;
            
            // Add check-in date change listener
            document.getElementById('checkInDate').addEventListener('change', function() {
                document.getElementById('checkOutDate').min = this.value;
            });

            // Initialize form fields and restore any saved values
            const form = document.getElementById('customerInfoForm');
            if (form) {
                const firstName = document.getElementById('firstName');
                const lastName = document.getElementById('lastName');
                const email = document.getElementById('customerEmail');
                const phone = document.getElementById('customerPhone');
                
                // Try to restore saved form data
                try {
                    const savedData = JSON.parse(localStorage.getItem('tempFormData') || '{}');
                    if (savedData.firstName) firstName.value = savedData.firstName;
                    if (savedData.lastName) lastName.value = savedData.lastName;
                    if (savedData.email) email.value = savedData.email;
                    if (savedData.phone) phone.value = savedData.phone;
                } catch (e) {
                    console.error('Error restoring form data:', e);
                }
                
                // Add input validation listeners
                firstName?.addEventListener('input', function() {
                    this.value = this.value.trim();
                });
                lastName?.addEventListener('input', function() {
                    this.value = this.value.trim();
                });
            }

            $('#cartModal').modal('hide');
            $('#customerInfoModal').modal('show');
        }

        function showBookingSummary() {
            const form = document.getElementById('customerInfoForm');
            const formData = new FormData(form);
            
            // Validate dates first
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            
            if (!checkInDate || !checkOutDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Dates',
                    text: 'Please select both check-in and check-out dates.'
                });
                return;
            }

            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);
            
            if (checkOut <= checkIn) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Dates',
                    text: 'Check-out date must be after check-in date.'
                });
                return;
            }

            // Rest of the function...
        }

        let guestCount = 0;
        let extraBedNeeded = false;

        function addGuestField() {
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            const totalCapacity = roomList.reduce((total, room) => {
                return total + (parseInt(room.capacity) * (room.quantity || 1));
            }, 0);
            
            const currentGuests = document.querySelectorAll('.guest-entry').length;
            
            // Check if adding another guest would exceed capacity
            if (currentGuests >= totalCapacity && !extraBedNeeded) {
                Swal.fire({
                    title: 'Room Capacity Reached',
                    text: 'Would you like to add an extra bed for ₱1,000 per night?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, add extra bed',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        extraBedNeeded = true;
                        addExtraBedToTotal();
                        addGuestFieldHTML();
                    }
                });
                return;
            } else if (currentGuests >= totalCapacity + 1 && extraBedNeeded) {
                Swal.fire({
                    title: 'Maximum Capacity Reached',
                    text: 'You have reached the maximum capacity including extra bed.',
                    icon: 'warning'
                });
                return;
            }
            
            addGuestFieldHTML();
        }

        function addGuestFieldHTML() {
            const guestsContainer = document.getElementById('guestsContainer');
            const guestId = guestCount++;
            
            const guestHtml = `
                <div class="guest-entry" id="guest-${guestId}">
                    <i class="fas fa-times remove-guest" onclick="removeGuest(${guestId})"></i>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Guest Name</label>
                            <input type="text" class="form-control" name="guestName_${guestId}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guest Type</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="guestType_${guestId}" value="adult" checked>
                                    <label class="form-check-label">Adult</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="guestType_${guestId}" value="child">
                                    <label class="form-check-label">Child</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            guestsContainer.insertAdjacentHTML('beforeend', guestHtml);
            updateGuestCounter();
        }

        function removeGuest(guestId) {
            const guestElement = document.getElementById(`guest-${guestId}`);
            if (guestElement) {
                const currentGuests = document.querySelectorAll('.guest-entry').length;
                if (currentGuests <= 2) {
                    extraBedNeeded = false;
                    removeExtraBedFromTotal();
                }
                guestElement.remove();
                updateGuestCounter();
            }
        }

        function updateGuestCounter() {
            const currentGuests = document.querySelectorAll('.guest-entry').length;
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            const totalCapacity = roomList.reduce((total, room) => {
                return total + (parseInt(room.capacity) * (room.quantity || 1));
            }, 0);
            
            const guestCounterDiv = document.getElementById('guestCounter') || createGuestCounter();
            guestCounterDiv.innerHTML = `
                <div class="alert ${currentGuests > totalCapacity ? 'alert-warning' : 'alert-info'} mb-3">
                    <strong>Guests: ${currentGuests}</strong> / Maximum Capacity: ${totalCapacity}
                    ${extraBedNeeded ? '<br><small>(Includes extra bed)</small>' : ''}
                </div>
            `;
        }

        function createGuestCounter() {
            const container = document.querySelector('.section-title');
            const counterDiv = document.createElement('div');
            counterDiv.id = 'guestCounter';
            container.after(counterDiv);
            return counterDiv;
        }

        function addExtraBedToTotal() {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            
            if (checkInDate && checkOutDate) {
                const nights = calculateNights(checkInDate, checkOutDate);
                updateTotalWithExtraBed(nights);
            }
        }

        function removeExtraBedFromTotal() {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            
            if (checkInDate && checkOutDate) {
                const nights = calculateNights(checkInDate, checkOutDate);
                updateTotalWithExtraBed(nights, true);
            }
        }

        function calculateNights(checkIn, checkOut) {
            const start = new Date(checkIn);
            const end = new Date(checkOut);
            const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            return nights;
        }

        function updateTotalWithExtraBed(nights, remove = false) {
            const extraBedCost = 1000 * nights * (remove ? -1 : 1);
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            
            // Update the total in the confirmation
            const totalAmount = calculateTotalAmount(roomList);
            document.querySelector('.total-section h4').innerHTML = `Total Amount: ₱${(totalAmount + (extraBedCost)).toLocaleString()}`;
        }

        // Calculate the number of nights between two dates
        function calculateNights(checkIn, checkOut) {
            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
            return Math.ceil(timeDiff / (1000 * 3600 * 24));
        }

        // Update showCustomerInfoModal to initialize the guest counter
        function showBookingSummary() {
            const form = document.getElementById('customerInfoForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Collect all guest information
            const guests = [];
            document.querySelectorAll('.guest-entry').forEach(entry => {
                const guestId = entry.id.split('-')[1];
                const guestName = entry.querySelector(`input[name="guestName_${guestId}"]`).value;
                const guestType = entry.querySelector(`input[name="guestType_${guestId}"]:checked`).value;
                guests.push({ name: guestName, type: guestType });
            });

            const formData = new FormData(form);
            const nights = calculateNights(formData.get('checkInDate'), formData.get('checkOutDate'));
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            const roomsTotal = roomList.reduce((total, room) => {
                return total + (parseFloat(room.price) * nights * (room.quantity || 1));
            }, 0);
            const extraBedTotal = extraBedNeeded ? (1000 * nights) : 0;
            const totalAmount = roomsTotal + extraBedTotal;
            const paymentOption = document.querySelector('input[name="paymentOption"]:checked').value;
            const paymentDue = paymentOption === 'full' ? totalAmount : 1500;
            
            Swal.fire({
                title: 'Booking Summary',
                html: `
                    <div class="text-left">
                        <div class="summary-section">
                            <h6 class="fw-bold mb-3">Personal Information</h6>
                            <p><strong>First Name:</strong> ${formData.get('firstName')}</p>
                            <p><strong>Last Name:</strong> ${formData.get('lastName')}</p>
                            <p><strong>Email:</strong> ${formData.get('customerEmail')}</p>
                            <p><strong>Contact Number:</strong> ${formData.get('customerPhone')}</p>
                        </div>

                        <div class="summary-section mt-4">
                            <h6 class="fw-bold mb-3">Booking Details</h6>
                            <p><strong>Check-in Date:</strong> ${new Date(formData.get('checkInDate')).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })}</p>
                            <p><strong>Check-out Date:</strong> ${new Date(formData.get('checkOutDate')).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })}</p>
                            <p><strong>Number of Nights:</strong> ${calculateNights(formData.get('checkInDate'), formData.get('checkOutDate'))}</p>
                        </div>

                        <div class="summary-section mt-4">
                            <h6 class="fw-bold mb-3">Room & Pricing Details</h6>
                            ${roomList.map(room => {
                                const roomPrice = parseFloat(room.price);
                                const quantity = room.quantity || 1;
                                const roomTotal = roomPrice * nights * quantity;
                                return `
                                    <div class="room-pricing mb-3">
                                        <p><strong>${room.type}:</strong></p>
                                        <p class="ms-3">Room Rate: ₱${roomPrice.toLocaleString()} per night</p>
                                        <p class="ms-3">Duration: ${nights} night${nights > 1 ? 's' : ''}</p>
                                        <p class="ms-3">Quantity: ${quantity} room${quantity > 1 ? 's' : ''}</p>
                                        <p class="ms-3 fw-bold">Room Total = ₱${roomTotal.toLocaleString()}</p>
                                    </div>
                                `;
                            }).join('')}
                            <p class="mt-2"><strong>Room Subtotal:</strong> ₱${roomsTotal.toLocaleString()}</p>
                            ${extraBedNeeded ? 
                                `<p><strong>Extra Bed:</strong> ₱1,000 × ${nights} night${nights > 1 ? 's' : ''} = ₱${(1000 * nights).toLocaleString()}</p>` 
                                : ''
                            }
                            <p class="mt-3 pt-2 border-top"><strong>Total Amount:</strong> ₱${totalAmount.toLocaleString()}</p>
                        </div>

                        <div class="summary-section mt-4">
                            <h6 class="fw-bold mb-3">Payment Information</h6>
                            <p><strong>Payment Option:</strong> ${paymentOption === 'full' ? 'Full Payment' : 'Down Payment (₱1,500)'}</p>
                            <p><strong>Payment Method:</strong> ${formData.get('paymentMethod').charAt(0).toUpperCase() + formData.get('paymentMethod').slice(1)}</p>
                            <p class="mt-2 p-2 bg-light rounded"><strong>Amount Due:</strong> ₱${paymentDue.toLocaleString()}</p>
                        </div>

                        <div class="summary-section mt-4">
                            <h6 class="fw-bold mb-3">Guest Information</h6>
                            ${guests.map((guest, index) => `
                                <p><strong>Guest ${index + 1}:</strong> ${guest.name} (${guest.type})</p>
                            `).join('')}
                            <p class="mt-2"><strong>Total Guests:</strong> ${guests.length}</p>
                        </div>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: 'Confirm Booking',
                cancelButtonText: 'Back',
                customClass: {
                    container: 'booking-summary-modal',
                    popup: 'booking-summary-popup',
                    content: 'booking-summary-content'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processBooking();
                } else {
                    // If user clicks Back, show the customer info modal again
                    $('#customerInfoModal').modal('show');
                }
            });
        }

        function processBooking() {
            const form = document.getElementById('customerInfoForm');
            if (!form.checkValidity() || !validateBookingDates()) {
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
            const roomList = JSON.parse(localStorage.getItem('roomList') || '[]');
            
            console.log('Processing room items. Total rooms in list:', roomList.length);
            
            document.querySelectorAll('.selected-room-item').forEach((roomItem, index) => {
                const guestCount = roomItem.querySelector('.guest-count').value;
                const roomPrice = roomItem.querySelector('.room-price').textContent.replace(/[^0-9.]/g, '');
                const roomIndex = roomItem.querySelector('.guest-count').dataset.roomIndex;
                const nights = roomItem.querySelector('.nights-count').textContent;
                const room = roomList[roomIndex];
                
                if (!room) {
                    console.error('Room not found in roomList at index:', roomIndex);
                    console.log('Available roomList:', roomList);
                    throw new Error('Room data is missing. Please try selecting the room again.');
                }
                
                console.log(`Processing room ${index + 1}:`, {
                    roomId: room.id,
                    roomType: room.type,
                    roomPrice: room.price,
                    room_type_id: room.room_type_id || room.id,
                    roomData: room
                });
                
                // Collect guest names
                const guestNames = [];
                roomItem.querySelectorAll('.guest-name').forEach(input => {
                    if (input.value.trim()) {
                        guestNames.push(input.value.trim());
                    }
                });
                
                // Debug log the original room data
                console.log('Original room data from localStorage:', room);
                
                // Create a clean room data object with all required fields
                const roomData = {
                    id: room.id,
                    room_type_id: room.room_type_id || room.id, // Always set room_type_id, fallback to id if not set
                    type: room.type,
                    price: parseFloat(room.price),
                    capacity: room.capacity,
                    image: room.image,
                    guestCount: parseInt(guestCount),
                    guestNames: guestNames,
                    nights: parseInt(nights),
                    totalPrice: parseFloat(roomPrice)
                };
                
                console.log('Prepared room data for server:', roomData);
                
                console.log('Room data prepared for server:', roomData);
                
                console.log('Room data being sent to server:', roomData);
                roomsData.push(roomData);
            });
            
            const roomsJson = JSON.stringify(roomsData);
            console.log('=== ROOM DATA BEING SENT TO SERVER ===');
            console.log('Final rooms JSON:', roomsJson);
            console.log('Room data structure:', roomsData);
            
            // Log each room's data in detail
            roomsData.forEach((room, index) => {
                console.log(`Room ${index + 1}:`, {
                    id: room.id,
                    room_type_id: room.room_type_id,
                    type: room.type,
                    price: room.price,
                    _debug: room._debug
                });
            });
            
            // Add all required fields to formData
            formData.append('rooms', roomsJson);
            formData.append('email', formData.get('customerEmail'));
            formData.append('contact', formData.get('customerPhone'));
            
            // Get dates directly from the form
            formData.append('check_in', document.getElementById('checkInDate').value);
            formData.append('check_out', document.getElementById('checkOutDate').value);
            
            formData.append('first_name', formData.get('firstName'));
            formData.append('last_name', formData.get('lastName'));
            formData.append('payment_option', formData.get('paymentOption'));
            formData.append('payment_method', formData.get('paymentMethod'));
            formData.append('total_amount', totalAmount);
            
            // Add room_type_id from the first room in roomsData
            if (roomsData.length > 0) {
                const firstRoom = roomsData[0];
                formData.append('room_type_id', firstRoom.room_type_id || firstRoom.id); // Use room_type_id or fallback to id
            }
            
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

        function calculateTotalAmount(roomList) {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            const nights = calculateNights(checkInDate, checkOutDate);
            
            return roomList.reduce((total, room) => {
                return total + (parseFloat(room.price) * nights * (room.quantity || 1));
            }, 0);
        }

        // Add date validation function
        function validateBookingDates() {
            const checkInDate = document.getElementById('checkInDate').value;
            const checkOutDate = document.getElementById('checkOutDate').value;
            
            if (!checkInDate || !checkOutDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Dates',
                    text: 'Please select both check-in and check-out dates.'
                });
                return false;
            }

            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);

            if (checkOut <= checkIn) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Dates',
                    text: 'Check-out date must be after check-in date.'
                });
                return false;
            }

            return true;
        }

        // Add event listeners for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const checkInDate = document.getElementById('checkInDate');
            const checkOutDate = document.getElementById('checkOutDate');

            if (checkInDate && checkOutDate) {
                checkInDate.addEventListener('change', function() {
                    if (this.value) {
                        const nextDay = new Date(this.value);
                        nextDay.setDate(nextDay.getDate() + 1);
                        checkOutDate.min = nextDay.toISOString().split('T')[0];
                        
                        if (checkOutDate.value && new Date(checkOutDate.value) <= new Date(this.value)) {
                            checkOutDate.value = nextDay.toISOString().split('T')[0];
                        }
                    }
                });

                checkOutDate.addEventListener('change', function() {
                    if (checkInDate.value && this.value) {
                        validateBookingDates();
                    }
                });
            }
        });
        </script>