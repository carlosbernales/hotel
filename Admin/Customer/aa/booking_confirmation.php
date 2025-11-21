<?php
// Start session with secure settings
session_start([
    'use_strict_mode' => true,
    'use_cookies' => 1,
    'cookie_httponly' => 1,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Lax',
    'read_and_close' => false
]);

require 'db_con.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug function
function debug_log($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message);
}

debug_log('Booking confirmation page loaded');
debug_log('Session data: ' . print_r($_SESSION, true));
debug_log('GET data: ' . print_r($_GET, true));

// Debug: Log session data
if (session_status() !== PHP_SESSION_ACTIVE) {
    error_log('Session is not active');
} else {
    error_log('Session ID: ' . session_id());
    error_log('Session data: ' . print_r($_SESSION, true));
}

// Initialize variables
$booking = [];
$error = '';
$fromSession = false;

// Check if we have booking details in session
if (isset($_SESSION['booking_details'])) {
    $bookingData = $_SESSION['booking_details'];
    
    $booking = [
        'booking_id' => $bookingData['bookRef'],
        'package_name' => $bookingData['packageName'],
        'event_date' => $bookingData['eventDate'],
        'start_time' => $bookingData['eventTime'],
        'number_of_guests' => $bookingData['numberOfGuests'],
        'total_amount' => $bookingData['packagePrice'],
        'payment_method' => $bookingData['paymentMethod'],
        'payment_type' => $bookingData['paymentType'],
        'reference_number' => $bookingData['bookRef'],
        'timestamp' => $bookingData['paymentDate'] ?? date('Y-m-d H:i:s'),
        'paid_amount' => $bookingData['amountPaid'] ?? $bookingData['packagePrice'],
        'remaining_balance' => $bookingData['remainingBalance'] ?? 0,
        'payment_status' => $bookingData['paymentStatus'] ?? 'success'
    ];
    
    $fromSession = true;
    
    // Clear the session data after using it
    unset($_SESSION['booking_details']);
    
    debug_log('Loaded booking data from session: ' . print_r($booking, true));
} 
// Check if we have booking data in URL parameters
else if (isset($_GET['booking_id'])) {
    // Get booking data from URL parameters
    $booking = [
        'booking_id' => $_GET['booking_id'] ?? '',
        'package_name' => $_GET['package_name'] ?? '',
        'event_date' => $_GET['event_date'] ?? '',
        'start_time' => $_GET['start_time'] ?? '',
        'end_time' => $_GET['end_time'] ?? '',
        'duration' => $_GET['duration'] ?? '',
        'number_of_guests' => $_GET['number_of_guests'] ?? '',
        'event_type' => $_GET['event_type'] ?? '',
        'total_amount' => $_GET['total_amount'] ?? '',
        'payment_method' => $_GET['payment_method'] ?? '',
        'payment_type' => $_GET['payment_type'] ?? '',
        'reference_number' => $_GET['reference_number'] ?? '',
        'timestamp' => $_GET['timestamp'] ?? date('Y-m-d H:i:s')
    ];
    $fromSession = false;
} 
// Check if we have booking data in session
elseif (isset($_SESSION['booking_details'])) {
    // Convert from our new session format to expected format
    $sessionData = $_SESSION['booking_details'];
    $booking = [
        'booking_id' => $sessionData['bookRef'],
        'package_name' => $sessionData['packageName'],
        'event_date' => $sessionData['eventDate'],
        'start_time' => $sessionData['eventTime'],
        'number_of_guests' => $sessionData['numberOfGuests'],
        'total_amount' => $sessionData['packagePrice'],
        'payment_method' => $sessionData['paymentMethod'],
        'payment_type' => $sessionData['paymentType'],
        'reference_number' => $sessionData['bookRef'],
        'timestamp' => $sessionData['paymentDate'],
        'paid_amount' => $sessionData['amountPaid'],
        'remaining_balance' => $sessionData['remainingBalance']
    ];
    $fromSession = true;
    
    // Debug: Log the session data
    error_log('Retrieved from session: ' . print_r($booking, true));
    
    // Clear the session data after retrieving it
    unset($_SESSION['booking_details']);
    
} elseif (isset($_SESSION['booking_summary'])) {
    // Legacy support for old session format
    $booking = $_SESSION['booking_summary'];
    $fromSession = true;
    
    // Clear the session data after retrieving it
    unset($_SESSION['booking_summary']);
}
// Fallback to database if we have a booking ID
else {
    $bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $fromSession = false;
}

// Initialize additional variables
$roomItems = [];
$guestInfo = [];

// Only fetch from database if we don't have session data
if (!$fromSession && !empty($bookingId)) {
    // Existing database fetch logic here
    try {

try {
    if ($bookingId > 0) {
        // Get booking details with payment and guest information
        $stmt = $pdo->prepare("
            SELECT b.*, 
                   p.amount as paid_amount, 
                   p.payment_method, 
                   p.payment_date,
                   p.reference_number as payment_reference,
                   g.guest_name,
                   g.email as guest_email
            FROM bookings b
            LEFT JOIN payments p ON b.id = p.booking_id
            LEFT JOIN booking_guests g ON b.id = g.booking_id AND g.guest_type = 'primary'
            WHERE b.id = ?
            ORDER BY p.payment_date DESC
            LIMIT 1
        ");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            // Get room items for this booking
            $stmt = $pdo->prepare("
                SELECT r.*, rt.name as room_type_name, rt.image_url 
                FROM booking_rooms r
                LEFT JOIN room_types rt ON r.room_type_id = rt.id
                WHERE r.booking_id = ?");
            $stmt->execute([$bookingId]);
            $roomItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get all guests for this booking
            $stmt = $pdo->prepare("SELECT * FROM booking_guests WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            $guestInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = 'Booking not found. Please check your booking reference or contact support.';
        }
    } else {
        $error = 'Invalid booking reference. Please check the URL or contact support.';
    // Set a flag to show the error in a SweetAlert2 modal
    $showErrorModal = true;
    }
    } catch (Exception $e) {
        $error = 'An error occurred while fetching your booking details. Please try again later.';
        error_log('Error fetching booking details: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
    }
} else if (empty($booking) && empty($error)) {
    $error = 'No booking data found. Please complete the booking process again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Casa Estela Boutique Hotel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .confirmation-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .confirmation-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }
        .confirmation-body {
            padding: 2rem;
            background: white;
            border-radius: 0 0 15px 15px;
        }
        .hotel-logo {
            max-width: 180px;
            margin-bottom: 1.5rem;
        }
        .divider {
            height: 2px;
            background-color: #e9ecef;
            margin: 1.5rem 0;
        }
        .btn-download {
            background: #4e73df;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn-download:hover {
            background: #2e59d9;
            color: white;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="container my-5">
        <?php if (!empty($error)): ?>
            <?php if (isset($showErrorModal)): ?>
                <!-- Error alert placeholder (hidden) -->
                <div id="errorAlert" style="display: none;" data-error='<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>'></div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const errorMessage = document.getElementById('errorAlert').dataset.error;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Error',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                                <p class="mb-3">${errorMessage}</p>
                                <p class="text-muted small mt-3">Please check the URL or contact our support team for assistance.</p>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4f46e5',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        buttonsStyling: true,
                        customClass: {
                            confirmButton: 'btn btn-primary px-4 py-2',
                            popup: 'border-0 shadow-lg',
                            title: 'h4 mb-3',
                            htmlContainer: 'text-left'
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeIn animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster'
                        },
                        showConfirmButton: true,
                        showCloseButton: false,
                        showLoaderOnConfirm: false,
                        showCancelButton: false,
                        focusConfirm: true,
                        focusCancel: false,
                        returnFocus: true,
                        reverseButtons: false,
                        timer: null,
                        timerProgressBar: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to home page or any other page after clicking OK
                            // window.location.href = 'index.php';
                        }
                    });
                });
                </script>
            <?php else: ?>
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Error</h4>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <hr>
                    <p class="mb-0">Please contact our support team if you need assistance.</p>
                </div>
            <?php endif; ?>
        <?php elseif (!empty($booking)): ?>
            <div class="confirmation-card mb-5">
                <div class="confirmation-header text-center">
                    <h1><i class="fas fa-check-circle me-2"></i>Booking Confirmed!</h1>
                    <p class="lead mb-0">Your reservation is now confirmed</p>
                    <p class="mt-2">Booking Reference: <strong>#<?php echo htmlspecialchars($bookingId); ?></strong></p>
                </div>
                
                <div class="confirmation-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4>Booking Details</h4>
                                <button onclick="window.print()" class="btn btn-outline-primary">
                                    <i class="fas fa-print me-1"></i> Print
                                </button>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                A confirmation has been sent to <?php echo !empty($booking['guest_email']) ? htmlspecialchars($booking['guest_email']) : 'your email'; ?>. 
                                Please check your spam folder if you don't see it in your inbox.
                            </div>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Guest Information</h5>
                                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($booking['guest_name'] ?? 'Guest'); ?></p>
                                    <?php if (!empty($booking['guest_email'])): ?>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($booking['guest_email']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($booking['phone'])): ?>
                                        <p class="mb-0"><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <h5 class="mb-3">Booking Summary</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6>Booking Reference</h6>
                                    <p class="text-muted">#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Booking Date</h6>
                                    <p class="text-muted"><?php echo date('F j, Y', strtotime($booking['created_at'])); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Check-in</h6>
                                    <p class="text-muted">
                                        <?php echo date('l, F j, Y', strtotime($booking['check_in'])); ?>
                                        <br>
                                        <small>From 2:00 PM</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Check-out</h6>
                                    <p class="text-muted">
                                        <?php echo date('l, F j, Y', strtotime($booking['check_out'])); ?>
                                        <br>
                                        <small>Until 12:00 PM</small>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Guests</h6>
                                    <p class="text-muted">
                                        <?php echo $booking['num_adults'] . ' Adult' . ($booking['num_adults'] > 1 ? 's' : ''); ?>
                                        <?php if ($booking['num_children'] > 0): ?>
                                            <br>
                                            <?php echo $booking['num_children'] . ' Child' . ($booking['num_children'] > 1 ? 'ren' : ''); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <?php if (!empty($roomItems)): ?>
                                <h5 class="mt-4 mb-3">Room Details</h5>
                                <?php foreach ($roomItems as $item): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['room_type']); ?></h6>
                                                    <p class="text-muted mb-1">
                                                        <?php echo $item['quantity']; ?> x <?php echo '₱' . number_format($item['price'], 2); ?> per night
                                                    </p>
                                                    <p class="text-muted mb-0">
                                                        <?php echo $booking['num_nights']; ?> night(s) • Total: ₱<?php echo number_format($item['total'], 2); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="sticky-top" style="top: 20px;">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Payment Summary</h5>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                                    </div>
                                    
                                    <?php if ($booking['payment_option'] === 'Partial Payment'): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Partial Payment (<?php echo number_format(($booking['paid_amount'] / $booking['total_amount']) * 100, 0); ?>%):</span>
                                            <span>-₱<?php echo number_format($booking['paid_amount'], 2); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 fw-bold">
                                            <span>Remaining Balance:</span>
                                            <span>₱<?php echo number_format($booking['remaining_balance'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="divider my-3"></div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Payment Method:</span>
                                        <span><?php echo htmlspecialchars($booking['payment_method']); ?></span>
                                    </div>
                                    
                                    <?php if (!empty($booking['payment_reference'])): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Payment Reference:</span>
                                            <span><?php echo htmlspecialchars($booking['payment_reference']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Payment Status:</span>
                                        <span class="badge bg-success">Paid</span>
                                    </div>
                                    
                                    <div class="divider my-3"></div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>Print Confirmation
                                        </button>
                                        <a href="index.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-home me-2"></i> Back to Home
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Need Help?</h5>
                                    <p class="mb-2"><i class="fas fa-phone-alt me-2"></i> +1 234 567 8900</p>
                                    <p class="mb-0"><i class="fas fa-envelope me-2"></i> support@casaestela.com</p>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-1"></i> Back to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (empty($error)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-exclamation-circle fa-4x text-warning mb-3"></i>
                    <h2>Booking Not Found</h2>
                    <p class="lead">We couldn't find your booking details.</p>
                    <p>Please check your booking reference or contact our support team for assistance.</p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-2">
                            <i class="fas fa-home me-1"></i> Return to Home
                        </a>
                        <a href="contact.php" class="btn btn-outline-secondary">
                            <i class="fas fa-headset me-1"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* SweetAlert2 Custom Styles */
        .swal2-popup {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .swal2-title {
            font-size: 1.375rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        
        .swal2-html-container {
            font-size: 1rem;
            color: #4a5568;
            line-height: 1.5;
            margin: 1rem 0;
            text-align: center;
        }
        
        .swal2-icon {
            margin: 0 auto 1rem;
            width: 4rem;
            height: 4rem;
        }
        
        .swal2-icon.swal2-error {
            border-color: #f56565;
            color: #f56565;
        }
        
        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #f56565;
        }
        
        .swal2-actions {
            margin: 1.5rem 0 0;
        }
        
        .swal2-styled.swal2-confirm {
            background-color: #4f46e5;
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .swal2-styled.swal2-confirm:hover {
            background-color: #4338ca;
        }
        
        .swal2-styled:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .swal2-show {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    
    <script>
        // Ensure SweetAlert2 is properly initialized
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 not loaded!');
        } else {
            // Set default SweetAlert2 options
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary me-2'
                },
                buttonsStyling: false
            });
            
            // Make the swalWithBootstrapButtons available globally
            window.swalWithBootstrapButtons = swalWithBootstrapButtons;
        }
        
        // Print the confirmation when the page loads (for demo purposes)
        // window.addEventListener('load', function() {
        //     window.print();
        // });
    </script>
</body>
</html>
