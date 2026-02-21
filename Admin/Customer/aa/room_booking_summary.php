<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection first
require_once 'db_con.php';

// Function to fetch terms and conditions from database
function getTermsAndConditions($pdo) {
    try {
        $stmt = $pdo->query("SELECT title, rule_text FROM terms_and_conditions WHERE is_active = 1 ORDER BY display_order ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching terms and conditions: " . $e->getMessage());
        return [];
    }
}

// Get terms and conditions
$termsAndConditions = getTermsAndConditions($pdo);

// Check for successful payment return
$paymentStatus = $_GET['payment'] ?? '';
$sessionId = $_GET['session_id'] ?? '';
$bookingRef = $_GET['ref'] ?? '';

// If this is a successful payment return, we'll show a success message
$showSuccessAlert = ($paymentStatus === 'success' && !empty($sessionId) && !empty($bookingRef));

// Get data from URL parameters
$checkInDate = $_GET['check_in'] ?? '';
$checkOutDate = $_GET['check_out'] ?? '';
$nights = intval($_GET['nights'] ?? 1);
$numAdults = intval($_GET['adults'] ?? 0);
$numChildren = intval($_GET['children'] ?? 0);
$paymentOption = $_GET['payment_option'] ?? '';
$paymentMethod = $_GET['payment_method'] ?? '';
$totalAmount = floatval($_GET['total_amount'] ?? 0);
$amountToPay = floatval($_GET['amount_due'] ?? 0);
$remainingBalance = floatval($_GET['balance'] ?? 0);

// Initialize guest information array
$guestInfo = [
    'first_name' => $_GET['first_name'] ?? '',
    'last_name' => $_GET['last_name'] ?? '',
    'email' => $_GET['email'] ?? '',
    'phone' => $_GET['phone'] ?? ''
];

// Check if user is logged in and fetch their details
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT firstname, lastname, email, contact FROM userss WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // If user exists, update guestInfo with their details
            $guestInfo = [
                'first_name' => $user['firstname'],
                'last_name' => $user['lastname'],
                'email' => $user['email'],
                'phone' => $user['contact']
            ];
            
            // Also update the first adult's details if available
            if (!empty($adults)) {
                $adults[0]['firstName'] = $user['firstname'];
                $adults[0]['lastName'] = $user['lastname'];
            }
        }
    } catch (PDOException $e) {
        // Log error but don't show to user
        error_log("Error fetching user details: " . $e->getMessage());
    }
}

// Get room information
$rooms = [];
$roomIndex = 0;
while (isset($_GET["room_{$roomIndex}_type"])) {
    $rooms[] = [
        'name' => urldecode($_GET["room_{$roomIndex}_type"]),
        'quantity' => intval($_GET["room_{$roomIndex}_qty"] ?? 1),
        'price' => floatval($_GET["room_{$roomIndex}_price"] ?? 0)
    ];
    $roomIndex++;
}

// Get adult details
$adults = [];
$adultIndex = 1;
while (isset($_GET["adult_{$adultIndex}_firstname"])) {
    $adults[] = [
        'firstName' => urldecode($_GET["adult_{$adultIndex}_firstname"] ?? ''),
        'lastName' => urldecode($_GET["adult_{$adultIndex}_lastname"] ?? ''),
        'age' => intval($_GET["adult_{$adultIndex}_age"] ?? 0),
        'userType' => $_GET["adult_{$adultIndex}_usertype"] ?? 'regular'
    ];
    $adultIndex++;
}

// Get children details
$children = [];
$childIndex = 1;
while (isset($_GET["child_{$childIndex}_firstname"])) {
    $children[] = [
        'firstName' => urldecode($_GET["child_{$childIndex}_firstname"] ?? ''),
        'lastName' => urldecode($_GET["child_{$childIndex}_lastname"] ?? ''),
        'age' => intval($_GET["child_{$childIndex}_age"] ?? 0)
    ];
    $childIndex++;
}

// Format dates
$formattedCheckIn = !empty($checkInDate) ? date('F j, Y', strtotime($checkInDate)) : '';
$formattedCheckOut = !empty($checkOutDate) ? date('F j, Y', strtotime($checkOutDate)) : '';
$currency = '₱';
    
    // Format currency values
    $formatCurrency = function($amount) use ($currency) {
        return $currency . number_format($amount, 2);
    };
    
    // Get payment option display text
    $paymentOptions = [
        'down_payment' => 'Down Payment (₱1,500)',
        'full_payment' => 'Full Payment',
        'custom_payment' => 'Custom Payment'
    ];
    
    $paymentMethodText = [
        'credit_card' => 'Credit/Debit Card',
        'gcash' => 'GCash',
        'paypal' => 'PayPal',
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash on Arrival'
    ];
    
    $userTypeLabels = [
        'regular' => 'Regular',
        'senior' => 'Senior Citizen',
        'pwd' => 'PWD',
        'student' => 'Student',
        'foreigner' => 'Foreigner'
    ];
    
    // Calculate subtotals for each room
    $roomSubtotals = [];
    foreach ($rooms as $room) {
        $roomSubtotals[] = [
            'room_type_name' => $room['name'],
            'quantity' => $room['quantity'],
            'price_per_night' => $room['price'],
            'subtotal' => $room['price'] * $room['quantity'] * $nights
        ];
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Summary - Confirmation</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <style>
            body {
                background-color: #f8f9fa;
                color: #333;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .header-bg {
                background: linear-gradient(135deg, #d59a07 0%, #b07d06 100%);
                color: white;
                padding: 3rem 0;
                margin-bottom: 2rem;
                border-radius: 0 0 30px 30px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .btn-primary {
                background-color: #d59a07;
                border: none;
            }
            
            .btn-primary:hover {
                background-color: #b07d06;
            }
            
            .btn-outline-primary {
                color: #d59a07;
                border-color: #d59a07;
            }
            
            .btn-outline-primary:hover {
                background-color: #d59a07;
                border-color: #d59a07;
            }
            .booking-card {
                background: white;
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                margin-bottom: 2rem;
                overflow: hidden;
            }
            .section-title {
                color: #2c3e50;
                font-weight: 600;
                border-bottom: 2px solid #f1f3f5;
                padding-bottom: 0.8rem;
                margin-bottom: 1.5rem;
                position: relative;
            }
            .section-title:after {
                content: '';
                position: absolute;
                left: 0;
                bottom: -2px;
                width: 60px;
                height: 3px;
                background: #d59a07;
            }
            .info-label {
                font-weight: 500;
                color: #6c757d;
                margin-bottom: 0.25rem;
            }
            .info-value {
                font-weight: 500;
                color: #2c3e50;
                margin-bottom: 1rem;
                font-size: 1.05rem;
            }
            .guest-badge {
                background-color: #fef9e7;
                color: #d59a07;
                font-weight: 500;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                margin-bottom: 1.5rem;
                border: 1px solid #f5d98e;
            }
            .guest-badge i {
                margin-right: 0.5rem;
            }
            .price-highlight {
                font-size: 1.5rem;
                font-weight: 700;
                color: #d59a07;
            }
            .divider {
                border-top: 1px dashed #dee2e6;
                margin: 1.5rem 0;
            }
            .print-btn {
                background: linear-gradient(135deg, #d59a07 0%, #b07d06 100%);
                border: none;
                padding: 0.75rem 2rem;
                font-weight: 500;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
                color: white;
            }
            .print-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(75, 108, 183, 0.3);
            }
            .room-item {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 1.25rem;
                margin-bottom: 1rem;
            }
            .room-name {
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 0.5rem;
            }
            .room-detail {
                font-size: 0.9rem;
                color: #6c757d;
            }
            .room-price {
                font-weight: 600;
                color: #2c3e50;
                text-align: right;
            }
            .alert-booking {
                background: #fffbf0;
                border-left: 4px solid #d59a07;
                border-radius: 0 8px 8px 0;
                border-right: 1px solid #f5d98e;
                border-top: 1px solid #f5d98e;
                border-bottom: 1px solid #f5d98e;
            }
        </style>
    </head>
    <body>
        <div class="header-bg">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1 class="display-5 fw-bold mb-3"><i class="fas fa-hourglass-half me-2"></i> Booking Pending!</h1>
                        <p class="lead mb-0">Booking summary information. Process your payment.</p>
                        <?php 
// Generate a booking reference with only alphanumeric characters, underscores, and dashes
$booking_ref = 'BOOK-' . strtoupper(preg_replace('/[^a-zA-Z0-9_-]/', '', base64_encode(random_bytes(8))));
?>
                       
                    </div>
                </div>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="booking-card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="section-title mb-0">Booking Summary</h3>
                            <span class="badge <?php echo $paymentStatus === 'success' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning'; ?> px-3 py-2">
                                <i class="fas <?php echo $paymentStatus === 'success' ? 'fa-check-circle' : 'fa-hourglass-half'; ?> me-1"></i>
                                <?php echo $paymentStatus === 'success' ? 'Paid' : 'Pending'; ?>
                            </span>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-calendar-check fa-2x" style="color: #d59a07;"></i>
                                    </div>
                                    <div>
                                        <div class="info-label">Check-in Date</div>
                                        <div class="info-value"><?php echo $formattedCheckIn; ?> at 2:00 PM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-calendar-times fa-2x" style="color: #d59a07;"></i>
                                    </div>
                                    <div>
                                        <div class="info-label">Check-out Date</div>
                                        <div class="info-value"><?php echo $formattedCheckOut; ?> at 11:00 AM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-moon fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="info-label">Duration</div>
                                        <div class="info-value"><?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?> stay</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="section-title">Guest Information</h4>
                                <div class="guest-badge mb-3">
                                    <i class="fas fa-users"></i>
                                    <?php echo $numAdults; ?> Adult<?php echo $numAdults != 1 ? 's' : ''; ?>
                                    <?php if ($numChildren > 0): ?>
                                        , <?php echo $numChildren; ?> Child<?php echo $numChildren != 1 ? 'ren' : ''; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($guestInfo['first_name']) || !empty($guestInfo['last_name'])): ?>
                                <div class="mb-3">
                                    <h5 class="fw-semibold mb-3">Primary Guest</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 bg-light rounded-3">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($guestInfo['first_name'] . ' ' . $guestInfo['last_name']); ?></h6>
                                                <?php if (!empty($guestInfo['email'])): ?>
                                                <div class="text-muted small">
                                                    <i class="fas fa-envelope me-1" style="color: #d59a07;"></i> <?php echo htmlspecialchars($guestInfo['email']); ?>
                                                </div>
                                                <?php endif; ?>
                                                <?php if (!empty($guestInfo['phone'])): ?>
                                                <div class="text-muted small">
                                                    <i class="fas fa-phone me-1" style="color: #d59a07;"></i> <?php echo htmlspecialchars($guestInfo['phone']); ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row">
                                    <?php if (!empty($adults)): ?>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <h5 class="fw-semibold mb-3">Adult Details</h5>
                                            <div class="row">
                                                <?php foreach ($adults as $index => $adult): ?>
                                                <div class="col-12 mb-3">
                                                    <div class="p-3 bg-light rounded-3 h-100">
                                                        <h6 class="mb-2"><?php echo htmlspecialchars(($adult['firstName'] ?? '') . ' ' . ($adult['lastName'] ?? '')); ?></h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <?php if (!empty($adult['age'])): ?>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                                <i class="fas fa-user me-1"></i> <?php echo $adult['age']; ?> years
                                                            </span>
                                                            <?php endif; ?>
                                                            <?php if (!empty($adult['userType']) && $adult['userType'] !== 'regular'): ?>
                                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                                <?php 
                                                                $userTypeLabels = [
                                                                    'senior' => 'Senior',
                                                                    'pwd' => 'PWD',
                                                                    'student' => 'Student',
                                                                    'foreigner' => 'Foreigner'
                                                                ];
                                                                echo $userTypeLabels[$adult['userType']] ?? ucfirst($adult['userType']);
                                                                ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($children)): ?>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <h5 class="fw-semibold mb-3">Children Details</h5>
                                            <div class="row">
                                                <?php foreach ($children as $index => $child): ?>
                                                <div class="col-12 mb-3">
                                                    <div class="p-3 bg-light rounded-3 h-100">
                                                        <h6 class="mb-2"><?php echo htmlspecialchars(($child['firstName'] ?? '') . ' ' . ($child['lastName'] ?? '')); ?></h6>
                                                        <?php if (!empty($child['age'])): ?>
                                                        <div class="text-muted small">
                                                            <i class="fas fa-child me-1" style="color: #d59a07;"></i> Age: <?php echo $child['age']; ?> years old
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div class="mb-4">
                            <h4 class="section-title">Room Details</h4>
                            <?php 
                            // Debug output - remove this after fixing
                            // echo '<pre>'; 
                            // var_dump($rooms); 
                            // echo '</pre>'; 
                            
                            foreach ($rooms as $index => $room): 
                                // Debug output for each room - remove after fixing
                                // echo '<pre>Room ' . ($index + 1) . ' data: '; 
                                // print_r($room); 
                                // echo '</pre>';
                            ?>
                                <div class="room-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="room-name"><?php echo htmlspecialchars($room['room_type_name'] ?? $room['name'] ?? 'Room ' . ($index + 1)); ?></h5>
                                            <div class="room-detail">
                                                <span class="me-3">
                                                    <i class="fas fa-bed me-1"></i> <?php echo $room['capacity']; ?> person capacity
                                                </span>
                                                <span class="me-3">
                                                    <i class="fas fa-hourglass-half me-2" style="color: #d59a07;"></i> x<?php echo $room['quantity']; ?>
                                                </span>
                                                <span>
                                                    <i class="far fa-moon me-1"></i> <?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="room-price">
                                                <?php echo $currency . number_format($room['price'], 2); ?> <small class="text-muted">/ night</small>
                                                <div class="text-end">
                                                    <small class="text-muted">Subtotal: </small>
                                                    <?php echo $currency . number_format($room['price'] * $room['quantity'] * $nights, 2); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div class="mb-4">
                            <h4 class="section-title">Payment Information</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Payment Option</div>
                                    <div class="info-value">
                                        <?php echo $paymentOptions[$paymentOption] ?? $paymentOption; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Payment Method</div>
                                    <div class="info-value">
                                        <?php echo $paymentMethodText[$paymentMethod] ?? $paymentMethod; ?>
                                    </div>
                                </div>
                                <?php if (!empty($paymentDetails)): ?>
                                    <div class="col-12 mt-3">
                                        <div class="info-label">Payment Reference</div>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($paymentDetails); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div>
                            <h4 class="section-title">Payment Summary</h4>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="info-label">Total Room Charges</div>
                                    <div class="info-label">Amount Paid</div>
                                    <?php if ($remainingBalance > 0): ?>
                                        <div class="info-label">Remaining Balance</div>
                                    <?php endif; ?>
                                    <div class="info-label mt-3 fw-bold">Total Amount</div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="info-value"><?php echo $currency . number_format($totalAmount, 2); ?></div>
                                    <div class="info-value text-success fw-bold"><?php echo $currency . number_format($amountToPay, 2); ?></div>
                                    <?php if ($remainingBalance > 0): ?>
                                        <div class="info-value text-danger"><?php echo $currency . number_format($remainingBalance, 2); ?></div>
                                    <?php endif; ?>
                                    <div class="info-value mt-3 fw-bold price-highlight"><?php echo $currency . number_format($totalAmount, 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-booking d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle fa-2x" style="color: #d59a07;"></i>
                        <div>
                            <h5 class="alert-heading mb-1">Important Information</h5>
                            <p class="mb-0">
                                Please present this confirmation at the reception upon arrival. 
                                Check-in time is 2:00 PM and check-out time is 11:00 AM. 
                                Early check-in and late check-out are subject to availability.
                            </p>
                        </div>
                    </div>
                    
                    <?php
                    // Check if payment was successful
                    $isPaymentSuccess = isset($_GET['payment']) && $_GET['payment'] === 'success';
                    ?>
                    
                    <!-- Terms and Conditions -->
                    <div class="card border-light mb-4">
                        <div class="card-body p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termsCheckbox" <?php echo $paymentStatus === 'success' ? 'checked' : 'disabled'; ?>>
                                <label class="form-check-label" for="termsCheckbox">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row g-3 justify-content-between align-items-center mt-2">
                        <div class="col-md-6 col-lg-4">
                            <a href="roomss.php" class="btn btn-outline-primary w-100 py-2">
                                <i class="fas fa-arrow-left me-2"></i> Back to Rooms
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4 mt-md-0 mt-2">
                            <button id="proceedToPayment" class="btn btn-<?php echo $isPaymentSuccess ? 'success' : 'primary'; ?> w-100 py-2" <?php echo $isPaymentSuccess ? '' : 'disabled'; ?>>
                                <i class="fas <?php echo $isPaymentSuccess ? 'fa-check-circle' : 'fa-credit-card'; ?> me-2"></i> 
                                <?php echo $isPaymentSuccess ? 'Finish Booking' : 'Proceed to Payment' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Terms and Conditions Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($termsAndConditions)): ?>
                            <?php foreach ($termsAndConditions as $index => $term): ?>
                                <h6><?php echo ($index + 1) . '. ' . htmlspecialchars($term['title']); ?></h6>
                                <p><?php echo nl2br(htmlspecialchars($term['rule_text'])); ?></p>
                                <?php if ($index < count($termsAndConditions) - 1): ?>
                                    <hr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Terms and conditions are currently unavailable. Please contact support for assistance.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="understandBtn" data-bs-dismiss="modal">I Understand</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Enable terms checkbox and payment button when user clicks "I Understand"
            document.getElementById('understandBtn').addEventListener('click', function() {
                const termsCheckbox = document.getElementById('termsCheckbox');
                const proceedBtn = document.getElementById('proceedToPayment');
                termsCheckbox.disabled = false;
                termsCheckbox.checked = true;
                proceedBtn.disabled = false;
            });

            // Initially set the proceed to payment button state based on payment status
            document.addEventListener('DOMContentLoaded', function() {
                const paymentStatus = '<?php echo $paymentStatus; ?>';
                const proceedButton = document.getElementById('proceedToPayment');
                const termsCheckbox = document.getElementById('termsCheckbox');
                
                if (paymentStatus === 'success') {
                    // If payment is successful, enable the button and check the terms
                    termsCheckbox.checked = true;
                    termsCheckbox.disabled = false;
                    proceedButton.disabled = false;
                } else {
                    // Otherwise, keep the button disabled until terms are accepted
                    proceedButton.disabled = true;
                }
            });

            // Validate terms and conditions before proceeding
            document.getElementById('proceedToPayment').addEventListener('click', function(e) {
                const termsCheckbox = document.getElementById('termsCheckbox');
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terms and Conditions',
                        text: 'Please accept the terms and conditions to proceed with your booking.',
                        confirmButtonText: 'I Understand',
                        confirmButtonColor: '#4b6cb7'
                    });
                    return false;
                }
                
                // Continue with payment processing if terms are accepted
                const button = this;
                const buttonText = button.textContent.trim();
                const isFinishBooking = buttonText === 'Finish Booking';
                
                // Show loading state
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ' + 
                                 (isFinishBooking ? 'Finishing...' : 'Processing...');
                
                // If it's the Finish Booking button, use AJAX to complete booking
                if (isFinishBooking) {
                    // Show processing state
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Finalizing...';
                    
                    // Prepare booking data for AJAX
                    const bookingData = {
                        booking_reference: '<?php echo $booking_ref; ?>',
                        check_in: '<?php echo $checkInDate; ?>',
                        check_out: '<?php echo $checkOutDate; ?>',
                        nights: '<?php echo $nights; ?>',
                        adults: '<?php echo $numAdults; ?>',
                        children: '<?php echo $numChildren; ?>',
                        payment_option: '<?php echo $paymentOption; ?>',
                        payment_method: '<?php echo $paymentMethod; ?>',
                        total_amount: '<?php echo $totalAmount; ?>',
                        amount_paid: '<?php echo $amountToPay; ?>',
                        balance: '<?php echo $remainingBalance; ?>',
                        first_name: '<?php echo $guestInfo['first_name']; ?>',
                        last_name: '<?php echo $guestInfo['last_name']; ?>',
                        email: '<?php echo $guestInfo['email']; ?>',
                        phone: '<?php echo $guestInfo['phone']; ?>',
                        rooms: [
                            <?php 
                            foreach ($rooms as $i => $room) {
                                echo "{name: '" . addslashes($room['name']) . "', quantity: " . $room['quantity'] . ", price: " . $room['price'] . "}";
                                if ($i < count($rooms) - 1) echo ",";
                            }
                            ?>
                        ],
                        adults_details: [
                            <?php 
                            foreach ($adults as $i => $adult) {
                                echo "{firstName: '" . addslashes($adult['firstName']) . "', lastName: '" . addslashes($adult['lastName']) . "', age: " . ($adult['age'] ?? 'null') . ", userType: '" . ($adult['userType'] ?? 'regular') . "'}";
                                if ($i < count($adults) - 1) echo ",";
                            }
                            ?>
                        ],
                        children_details: [
                            <?php 
                            foreach ($children as $i => $child) {
                                echo "{firstName: '" . addslashes($child['firstName']) . "', lastName: '" . addslashes($child['lastName']) . "', age: " . ($child['age'] ?? 'null') . "}";
                                if ($i < count($children) - 1) echo ",";
                            }
                            ?>
                        ]
                    };
                    
                    // Send AJAX request to complete booking
                    // Add 3-second delay before sending request
                    setTimeout(() => {
                        fetch('room_finish_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(bookingData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Booking Completed!',
                                    html: 'Your booking has been successfully completed.<br><strong>Booking Reference:</strong> ' + data.booking_reference,
                                    confirmButtonText: 'Great!',
                                    confirmButtonColor: '#28a745',
                                    allowOutsideClick: false
                                }).then((result) => {
                                    // Show review prompt when user clicks 'Great!'
                                    if (result.isConfirmed) {
                                        Swal.fire({
                                            title: 'How was your experience?',
                                            text: 'We would love to hear your feedback about your booking experience!',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Leave a Review',
                                            cancelButtonText: 'Maybe Later',
                                            confirmButtonColor: '#4b6cb7',
                                            cancelButtonColor: '#6c757d'
                                        }).then(async (reviewResult) => {
                                            // Clear booking session and redirect
                                            try {
                                                // First clear the session on the server
                                                const response = await fetch('room_clear_booking_session.php', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/x-www-form-urlencoded',
                                                    },
                                                    body: 'action=clear_booking'
                                                });
                                                const result = await response.json();
                                                
                                                // Clear localStorage
                                                if (window.localStorage) {
                                                    localStorage.removeItem('bookingList');
                                                }
                                                
                                                // Show star rating if user wants to leave a review
                                                if (reviewResult.isConfirmed) {
                                                    // First show star rating
                                                    Swal.fire({
                                                        title: 'Rate Your Experience',
                                                        html: `
                                                            <div class="text-center my-3">
                                                                <div class="star-rating mb-3">
                                                                    <i class="far fa-star" data-rating="1" style="font-size: 2.5rem; margin: 0 5px; color: #ffd700; cursor: pointer;"></i>
                                                                    <i class="far fa-star" data-rating="2" style="font-size: 2.5rem; margin: 0 5px; color: #ffd700; cursor: pointer;"></i>
                                                                    <i class="far fa-star" data-rating="3" style="font-size: 2.5rem; margin: 0 5px; color: #ffd700; cursor: pointer;"></i>
                                                                    <i class="far fa-star" data-rating="4" style="font-size: 2.5rem; margin: 0 5px; color: #ffd700; cursor: pointer;"></i>
                                                                    <i class="far fa-star" data-rating="5" style="font-size: 2.5rem; margin: 0 5px; color: #ffd700; cursor: pointer;"></i>
                                                                </div>
                                                                <input type="hidden" id="rating-value" value="0">
                                                                <p class="text-muted">Click on a star to rate</p>
                                                                
                                                                <div class="mt-4">
                                                                    <label for="review-message" class="form-label">Share your experience (optional)</label>
                                                                    <textarea class="form-control" id="review-message" rows="3" placeholder="Tell us more about your experience..." style="resize: none;"></textarea>
                                                                    <div class="form-text">Your feedback helps us improve our service</div>
                                                                </div>
                                                            </div>
                                                        `,
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Submit Rating',
                                                        cancelButtonText: 'Skip',
                                                        confirmButtonColor: '#4b6cb7',
                                                        cancelButtonColor: '#6c757d',
                                                        showLoaderOnConfirm: true,
                                                        preConfirm: () => {
                                                            const rating = document.getElementById('rating-value').value;
                                                            const reviewMessage = document.getElementById('review-message').value;
                                                            
                                                            if (rating === '0') {
                                                                Swal.showValidationMessage('Please select a rating');
                                                                return false;
                                                            }
                                                            
                                                            return { 
                                                                rating: rating,
                                                                message: reviewMessage
                                                            };
                                                        },
                                                        didOpen: () => {
                                                            // Add star hover and click functionality
                                                            const stars = document.querySelectorAll('.star-rating i');
                                                            stars.forEach(star => {
                                                                star.addEventListener('mouseover', function() {
                                                                    const rating = this.getAttribute('data-rating');
                                                                    highlightStars(rating);
                                                                });
                                                                
                                                                star.addEventListener('click', function() {
                                                                    const rating = this.getAttribute('data-rating');
                                                                    document.getElementById('rating-value').value = rating;
                                                                    highlightStars(rating);
                                                                });
                                                                
                                                                star.addEventListener('mouseout', function() {
                                                                    const currentRating = document.getElementById('rating-value').value;
                                                                    if (currentRating === '0') {
                                                                        resetStars();
                                                                    } else {
                                                                        highlightStars(currentRating);
                                                                    }
                                                                });
                                                            });
                                                            
                                                            function highlightStars(rating) {
                                                                const stars = document.querySelectorAll('.star-rating i');
                                                                stars.forEach(star => {
                                                                    if (star.getAttribute('data-rating') <= rating) {
                                                                        star.classList.remove('far');
                                                                        star.classList.add('fas');
                                                                    } else {
                                                                        star.classList.remove('fas');
                                                                        star.classList.add('far');
                                                                    }
                                                                });
                                                            }
                                                            
                                                            function resetStars() {
                                                                const stars = document.querySelectorAll('.star-rating i');
                                                                stars.forEach(star => {
                                                                    star.classList.remove('fas');
                                                                    star.classList.add('far');
                                                                });
                                                            }
                                                        }
                                                    }).then(async (ratingResult) => {
                                                        // If user confirmed their rating, submit it via AJAX
                                                        if (ratingResult.isConfirmed) {
                                                            try {
                                                                const submitButton = Swal.getConfirmButton();
                                                                const originalText = submitButton.innerHTML;
                                                                
                                                                // Show loading state
                                                                submitButton.disabled = true;
                                                                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                                                                
                                                                // Show processing modal
                                                                const processingModal = Swal.fire({
                                                                    title: 'Submitting Your Rating',
                                                                    html: '<div class="text-center"><div class="spinner-border text-primary mb-3" role="status"></div><p>Please wait while we process your rating...</p></div>',
                                                                    showConfirmButton: false,
                                                                    allowOutsideClick: false,
                                                                    allowEscapeKey: false,
                                                                    allowEnterKey: false,
                                                                    willOpen: () => {
                                                                        Swal.showLoading();
                                                                    }
                                                                });
                                                                
                                                                // Get the booking reference from the data object or fall back to the one from PHP
                                                                const bookingReference = data.booking_reference || '<?php echo $booking_ref; ?>';
                                                                
                                                                if (!bookingReference) {
                                                                    throw new Error('Booking reference is missing');
                                                                }
                                                                
                                                                // Create form data
                                                                const formData = new FormData();
                                                                formData.append('booking_ref', bookingReference);
                                                                formData.append('rating', ratingResult.value.rating);
                                                                formData.append('message', ratingResult.value.message || '');
                                                                
                                                                const response = await fetch('room_create_review.php', {
                                                                    method: 'POST',
                                                                    body: formData
                                                                });
                                                                
                                                                const result = await response.json();
                                                                
                                                                // Close processing modal
                                                                await Swal.close();
                                                                
                                                                if (result.success) {
                                                                    // Close the current dialog
                                                                    Swal.close();
                                                                    
                                                                    // Show success alert
                                                                    await Swal.fire({
                                                                        title: 'Thank You!',
                                                                        text: 'Your feedback has been submitted successfully!',
                                                                        icon: 'success',
                                                                        confirmButtonText: 'Continue',
                                                                        allowOutsideClick: false
                                                                    }).then(() => {
                                                                        // Redirect to roomss.php after successful submission
                                                                        window.location.href = 'roomss.php';
                                                                    });
                                                                    
                                                                    // Exit the function
                                                                    return false;
                                                                } else {
                                                                    throw new Error(result.message || 'Failed to submit review');
                                                                }
                                                            } catch (error) {
                                                                console.error('Error submitting review:', error);
                                                                
                                                                // Close any open modals
                                                                await Swal.close();
                                                                
                                                                // Show error message
                                                                await Swal.fire({
                                                                    title: 'Submission Failed',
                                                                    text: 'Failed to submit review. ' + (error.message || 'Please try again.'),
                                                                    icon: 'error',
                                                                    confirmButtonText: 'OK'
                                                                });
                                                                
                                                                // Reset button state
                                                                const submitButton = Swal.getConfirmButton();
                                                                if (submitButton) {
                                                                    submitButton.disabled = false;
                                                                    submitButton.innerHTML = originalText || 'Submit';
                                                                }
                                                                
                                                                // Allow user to try again
                                                                return false;
                                                            }
                                                        } else {
                                                            // If user clicked Skip, redirect to home
                                                            window.location.href = 'roomss.php';
                                                        }
                                                    });
                                                } else {
                                                    window.location.href = 'roomss.php';
                                                }
                                            } catch (error) {
                                                console.error('Error during cleanup:', error);
                                                // Still redirect even if there was an error
                                                window.location.href = 'roomss.php';
                                            }
                                        });
                                    }
                                    // Update button to show completion
                                    button.innerHTML = '<i class="fas fa-check-circle me-2"></i> Booking Completed';
                                    button.classList.remove('btn-primary');
                                    button.classList.add('btn-success');
                                    button.disabled = true;
                                    
                                    // Optionally update the page status
                                    const statusBadge = document.querySelector('.badge');
                                    if (statusBadge) {
                                        statusBadge.classList.remove('bg-warning', 'bg-opacity-10', 'text-warning');
                                        statusBadge.classList.add('bg-success', 'bg-opacity-10', 'text-success');
                                        statusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Completed';
                                    }
                                });
                            } else {
                                throw new Error(data.message || 'Failed to complete booking');
                            }
                        })
                        .catch(error => {
                            console.error('Booking completion failed:', error);
                            
                            // Reset button state
                            button.disabled = false;
                            button.innerHTML = originalText;
                            
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Booking Error',
                                text: error.message || 'Failed to complete booking. Please try again.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                    }, 3000); // 3-second delay
                    
                    return;
                }
                
                // Get booking reference from PHP variable
                const bookingRef = '<?php echo $booking_ref; ?>';
                
                // Build the success URL with all parameters
                let successUrl = window.location.href.split('?')[0] + '?payment=success&ref=' + encodeURIComponent(bookingRef) + 
                    '&check_in=' + encodeURIComponent('<?php echo $checkInDate; ?>') +
                    '&check_out=' + encodeURIComponent('<?php echo $checkOutDate; ?>') +
                    '&nights=' + encodeURIComponent('<?php echo $nights; ?>') +
                    '&adults=' + encodeURIComponent('<?php echo $numAdults; ?>') +
                    '&children=' + encodeURIComponent('<?php echo $numChildren; ?>') +
                    '&first_name=' + encodeURIComponent('<?php echo $guestInfo['first_name']; ?>') +
                    '&last_name=' + encodeURIComponent('<?php echo $guestInfo['last_name']; ?>') +
                    '&email=' + encodeURIComponent('<?php echo $guestInfo['email']; ?>') +
                    '&phone=' + encodeURIComponent('<?php echo $guestInfo['phone']; ?>') +
                    '&payment_option=' + encodeURIComponent('<?php echo $paymentOption; ?>') +
                    '&payment_method=' + encodeURIComponent('<?php echo $paymentMethod; ?>') +
                    '&total_amount=' + encodeURIComponent('<?php echo $totalAmount; ?>') +
                    '&amount_due=' + encodeURIComponent('<?php echo $amountToPay; ?>') +
                    '&balance=' + encodeURIComponent('<?php echo $remainingBalance; ?>');

                // Add adult details to URL
                <?php 
                foreach ($adults as $i => $adult) {
                    echo "successUrl += '&adult_" . ($i + 1) . "_firstname=' + encodeURIComponent('" . addslashes($adult['firstName'] ?? '') . "');\n";
                    echo "successUrl += '&adult_" . ($i + 1) . "_lastname=' + encodeURIComponent('" . addslashes($adult['lastName'] ?? '') . "');\n";
                    echo "successUrl += '&adult_" . ($i + 1) . "_age=' + encodeURIComponent('" . addslashes($adult['age'] ?? '') . "');\n";
                    echo "successUrl += '&adult_" . ($i + 1) . "_usertype=' + encodeURIComponent('" . addslashes($adult['userType'] ?? '') . "');\n";
                }
                ?>

                // Add room details to URL
                <?php 
                foreach ($rooms as $i => $room) {
                    echo "successUrl += '&room_" . $i . "_type=' + encodeURIComponent('" . urlencode($room['name'] ?? '') . "');\n";
                    echo "successUrl += '&room_" . $i . "_qty=' + encodeURIComponent('" . $room['quantity'] . "');\n";
                    echo "successUrl += '&room_" . $i . "_price=' + encodeURIComponent('" . $room['price'] . "');\n";
                }
                ?>

                // Add children details to URL
                <?php 
                foreach ($children as $i => $child) {
                    echo "successUrl += '&child_" . ($i + 1) . "_firstname=' + encodeURIComponent('" . addslashes($child['firstName'] ?? '') . "');\n";
                    echo "successUrl += '&child_" . ($i + 1) . "_lastname=' + encodeURIComponent('" . addslashes($child['lastName'] ?? '') . "');\n";
                    echo "successUrl += '&child_" . ($i + 1) . "_age=' + encodeURIComponent('" . addslashes($child['age'] ?? '') . "');\n";
                }
                ?>

                // Prepare payment data
                const paymentData = {
                    booking_reference: bookingRef,
                    amount: parseFloat('<?php echo $amountToPay; ?>'),
                    currency: '<?php echo $currency; ?>',
                    description: 'Room Booking Payment - ' + bookingRef,
                    success_url: successUrl,
                    cancel_url: window.location.href.split('?')[0] + '?payment=cancelled&ref=' + encodeURIComponent(bookingRef)
                };
                
                // Prepare customer email and name
                const customerEmail = '<?php echo $adults[0]['email'] ?? ''; ?>';
                const customerName = '<?php echo ($adults[0]['firstName'] ?? '') . ' ' . ($adults[0]['lastName'] ?? ''); ?>'.trim();
                
                console.log('Initiating payment with data:', {
                    amount: paymentData.amount * 100,
                    description: paymentData.description,
                    customer_email: customerEmail,
                    customer_name: customerName
                });

                // Make AJAX call to PayMongo checkout
                // Collect all URL parameters to include in metadata
                const urlParams = new URLSearchParams(window.location.search);
                const metadata = {
                    ref: bookingRef,
                    check_in: '<?php echo $checkInDate; ?>',
                    check_out: '<?php echo $checkOutDate; ?>',
                    nights: '<?php echo $nights; ?>',
                    adults: '<?php echo $numAdults; ?>',
                    children: '<?php echo $numChildren; ?>',
                    first_name: '<?php echo $guestInfo['first_name']; ?>',
                    last_name: '<?php echo $guestInfo['last_name']; ?>',
                    email: '<?php echo $guestInfo['email']; ?>',
                    phone: '<?php echo $guestInfo['phone']; ?>',
                    payment_option: '<?php echo $paymentOption; ?>',
                    payment_method: '<?php echo $paymentMethod; ?>',
                    total_amount: '<?php echo $totalAmount; ?>',
                    amount_due: '<?php echo $amountToPay; ?>',
                    balance: '<?php echo $remainingBalance; ?>',
                    booking_reference: bookingRef,
                    customer_email: customerEmail,
                    customer_name: customerName
                };

                // Add adult details
                <?php 
                foreach ($adults as $i => $adult) {
                    echo "metadata['adult_" . ($i + 1) . "_firstname'] = '" . addslashes($adult['firstName']) . "';\n";
                    echo "metadata['adult_" . ($i + 1) . "_lastname'] = '" . addslashes($adult['lastName']) . "';\n";
                    echo "metadata['adult_" . ($i + 1) . "_age'] = '" . addslashes($adult['age'] ?? '') . "';\n";
                    echo "metadata['adult_" . ($i + 1) . "_usertype'] = '" . addslashes($adult['userType'] ?? '') . "';\n";
                }
                ?>

                // Add room details
                <?php 
                foreach ($rooms as $i => $room) {
                    echo "metadata['room_" . $i . "_type'] = '" . urlencode($room['name']) . "';\n";
                    echo "metadata['room_" . $i . "_qty'] = '" . $room['quantity'] . "';\n";
                    echo "metadata['room_" . $i . "_price'] = '" . $room['price'] . "';\n";
                }
                ?>

                fetch('room_paymongo_checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        amount: paymentData.amount * 100, // Convert to centavos
                        description: paymentData.description,
                        success_url: paymentData.success_url,
                        cancel_url: paymentData.cancel_url,
                        reference_number: bookingRef,
                        metadata: metadata
                    })
                })
                .then(async response => {
                    const responseData = await response.json();
                    
                    // Log the response for debugging
                    console.log('Payment API Response:', {
                        status: response.status,
                        statusText: response.statusText,
                        data: responseData
                    });

                    if (!response.ok) {
                        const errorMsg = responseData.error || 'Failed to process payment';
                        console.error('Payment API Error:', errorMsg);
                        throw new Error(errorMsg);
                    }

                    // Handle successful response
                    if (responseData.data?.attributes?.checkout_url) {
                        console.log('Redirecting to checkout:', responseData.data.attributes.checkout_url);
                        window.location.href = responseData.data.attributes.checkout_url;
                    } else if (responseData.checkout_url) {
                        // Fallback for different response format
                        console.log('Redirecting to checkout (fallback):', responseData.checkout_url);
                        window.location.href = responseData.checkout_url;
                    } else {
                        console.error('No checkout URL in response:', responseData);
                        throw new Error('No checkout URL received from payment processor');
                    }
                })
                .catch(error => {
                    console.error('Payment processing failed:', error);
                    
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = originalText;
                    
                    // Show detailed error message
                    const errorMessage = error.message || 'Failed to process payment. Please try again.';
                    
                    // Use SweetAlert2 for better error display if available, otherwise use alert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Error',
                            text: errorMessage,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        alert('Error: ' + errorMessage);
                    }
                    
                    // Log the error for debugging
                    if (typeof gtag === 'function') {
                        gtag('event', 'exception', {
                            'description': errorMessage,
                            'fatal': false
                        });
                    }
                });
            });
            
            // Show success message if returning from successful payment
            document.addEventListener('DOMContentLoaded', function() {
                // Check if we should show the success alert
                const urlParams = new URLSearchParams(window.location.search);
                const paymentStatus = urlParams.get('payment');
                const sessionId = urlParams.get('session_id');
                
                if (paymentStatus === 'success') {
                    // Show success message with SweetAlert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Payment Successful!',
                            text: 'Your payment has been processed successfully. Please click the button below to complete your booking.',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4b6cb7',
                            allowOutsideClick: false
                        }).then((result) => {
                            // Do nothing when OK is clicked - just close the alert
                        });
                    } else {
                        // Fallback to regular alert if SweetAlert is not available
                        alert('Payment successful! Please click the "Proceed to Payment" button to complete your booking.');
                    }
                }
            });

            // Print only the booking details when printing
            document.addEventListener('DOMContentLoaded', function() {
                // Add print-specific styles
                const style = document.createElement('style');
                style.media = 'print';
                style.innerHTML = `
                    body * {
                        visibility: hidden;
                    }
                    .header-bg, .booking-card, .alert-booking, .print-actions {
                        visibility: visible;
                    }
                    .booking-card, .alert-booking {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        box-shadow: none;
                        border: none;
                    }
                    .no-print {
                        display: none !important;
                    }
                    @page {
                        size: auto;
                        margin: 10mm;
                    }
                `;
                document.head.appendChild(style);
                
                // Add print button functionality
                document.querySelector('.print-btn').addEventListener('click', function() {
                    window.print();
                });
            });
        </script>
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
