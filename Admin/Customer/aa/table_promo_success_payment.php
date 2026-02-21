<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Show session ID and all session variables
error_log('Session ID: ' . session_id());
error_log('Session variables: ' . print_r($_SESSION, true));

// Get invoice ID from URL
$invoiceId = $_GET['invoice_id'] ?? '';

// Database connection
$host = 'localhost';
$dbname = 'casaa';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create promo_bookings table if it doesn't exist
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS promo_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_ref VARCHAR(50) UNIQUE NOT NULL,
        invoice_id VARCHAR(100),
        guest_firstname VARCHAR(100) NOT NULL,
        guest_lastname VARCHAR(100) NOT NULL,
        guest_email VARCHAR(100) NOT NULL,
        guest_phone VARCHAR(20),
        number_of_guests INT NOT NULL,
        special_requests TEXT,
        payment_option VARCHAR(50),
        payment_method VARCHAR(50),
        amount_paid DECIMAL(10,2) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        offer_title VARCHAR(200),
        offer_price DECIMAL(10,2),
        payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'paid',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_invoice_id (invoice_id),
        INDEX idx_booking_ref (booking_ref),
        INDEX idx_payment_status (payment_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createTableSQL);
    
    if ($invoiceId) {
        // Try to get payment details from Xendit API
        $xenditSecretKey = 'xnd_development_uT7ZucMP1K72tnaW8HhC7gFungXo0cAjDRcxRyHLoBCAnu0fsLPDqRe6IcB6Bw9n';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.xendit.co/v2/invoices/" . $invoiceId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($xenditSecretKey . ':')
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $paymentVerified = false;
        $bookingDetails = [];
        
        if ($httpCode === 200 && !$error) {
            $responseData = json_decode($response, true);
            
            // Check if payment is successful
            if (isset($responseData['status']) && $responseData['status'] === 'PAID') {
                $paymentVerified = true;
                
                // Extract booking details from metadata
                if (isset($responseData['metadata']['booking_details'])) {
                    $bookingDetails = json_decode($responseData['metadata']['booking_details'], true);
                }
            }
        }
        
        // Generate booking reference
        $bookingRef = 'TABLE-' . strtoupper(uniqid());
        
        // Get guest information from session (use table promo data if available)
        $userId = $_SESSION['user_id'] ?? null;
        $guestFirstname = $_SESSION['table_promo_guest_firstname'] ?? $_SESSION['first_name'] ?? 'Guest';
        $guestLastname = $_SESSION['table_promo_guest_lastname'] ?? $_SESSION['last_name'] ?? 'User';
        $guestEmail = $_SESSION['table_promo_guest_email'] ?? $_SESSION['email'] ?? 'guest@example.com';
        $guestPhone = $_SESSION['table_promo_guest_phone'] ?? $_SESSION['phone'] ?? '';
        
        // Use table promo data from session if available
        $numberOfGuests = $_SESSION['table_promo_guests'] ?? 1;
        $specialRequests = $_SESSION['table_promo_special_requests'] ?? '';
        $paymentMethod = 'Xendit';
        $paymentOption = $_SESSION['table_promo_payment_option'] ?? 'Full Payment';
        $amountPaid = $_SESSION['table_promo_amount'] ?? 6000.00;
        $totalAmount = $_SESSION['table_promo_total_amount'] ?? 6000.00;
        $promoTitle = $_SESSION['table_promo_offer_title'] ?? 'Table Promo';
        $promoPrice = $_SESSION['table_promo_offer_price'] ?? 6000.00;
        
        // Override with Xendit metadata if available and payment verified
        if ($paymentVerified && !empty($bookingDetails)) {
            $guestFirstname = $bookingDetails['guest_firstname'] ?? $guestFirstname;
            $guestLastname = $bookingDetails['guest_lastname'] ?? $guestLastname;
            $guestEmail = $bookingDetails['guest_email'] ?? $guestEmail;
            $guestPhone = $bookingDetails['guest_phone'] ?? $guestPhone;
            $numberOfGuests = $bookingDetails['number_of_guests'] ?? $numberOfGuests;
            $specialRequests = $bookingDetails['special_requests'] ?? $specialRequests;
            $paymentOption = $bookingDetails['payment_option'] ?? $paymentOption;
            $totalAmount = $bookingDetails['full_booking_amount'] ?? $totalAmount;
            $promoPrice = $bookingDetails['offer_price'] ?? $promoPrice;
        }
        
        // Insert booking into database
        $insertSQL = "
        INSERT INTO promo_bookings (
            booking_ref, 
            invoice_id, 
            guest_firstname, 
            guest_lastname, 
            guest_email, 
            guest_phone, 
            number_of_guests, 
            special_requests, 
            payment_option, 
            payment_method, 
            amount_paid, 
            total_amount, 
            offer_title, 
            offer_price, 
            payment_status
        ) VALUES (
            :booking_ref,
            :invoice_id,
            :guest_firstname,
            :guest_lastname,
            :guest_email,
            :guest_phone,
            :number_of_guests,
            :special_requests,
            :payment_option,
            :payment_method,
            :amount_paid,
            :total_amount,
            :offer_title,
            :offer_price,
            :payment_status
        )
        ";
        
        $stmt = $pdo->prepare($insertSQL);
        
        $paymentStatus = $paymentVerified ? 'paid' : 'pending';
        
        // Debug: Show data being inserted
        error_log('About to insert booking data: ' . print_r([
            'booking_ref' => $bookingRef,
            'invoice_id' => $invoiceId,
            'guest_firstname' => $guestFirstname,
            'guest_lastname' => $guestLastname,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'number_of_guests' => $numberOfGuests,
            'special_requests' => $specialRequests,
            'payment_option' => $paymentOption,
            'payment_method' => $paymentMethod,
            'amount_paid' => $amountPaid,
            'total_amount' => $totalAmount,
            'offer_title' => $promoTitle,
            'offer_price' => $promoPrice,
            'payment_status' => $paymentStatus
        ], true));
        
        try {
            $result = $stmt->execute([
                ':booking_ref' => $bookingRef,
                ':invoice_id' => $invoiceId,
                ':guest_firstname' => $guestFirstname,
                ':guest_lastname' => $guestLastname,
                ':guest_email' => $guestEmail,
                ':guest_phone' => $guestPhone,
                ':number_of_guests' => $numberOfGuests,
                ':special_requests' => $specialRequests,
                ':payment_option' => $paymentOption,
                ':payment_method' => $paymentMethod,
                ':amount_paid' => $amountPaid,
                ':total_amount' => $totalAmount,
                ':offer_title' => $promoTitle,
                ':offer_price' => $promoPrice,
                ':payment_status' => $paymentStatus
            ]);
            
            if ($result) {
                error_log('Booking successfully inserted into promo_bookings table. Booking ref: ' . $bookingRef);
            } else {
                error_log('Failed to insert booking into promo_bookings table.');
            }
        } catch (PDOException $e) {
            error_log('Database insertion error: ' . $e->getMessage());
        }
        
        // Clear table promo session variables
        unset($_SESSION['table_promo_guest_firstname']);
        unset($_SESSION['table_promo_guest_lastname']);
        unset($_SESSION['table_promo_guest_email']);
        unset($_SESSION['table_promo_guest_phone']);
        unset($_SESSION['table_promo_guests']);
        unset($_SESSION['table_promo_special_requests']);
        unset($_SESSION['table_promo_payment_option']);
        unset($_SESSION['table_promo_amount']);
        unset($_SESSION['table_promo_total_amount']);
        unset($_SESSION['table_promo_offer_title']);
        unset($_SESSION['table_promo_offer_price']);
        
    }
    
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $bookingRef = 'ERROR-' . uniqid();
    $paymentStatus = 'failed';
    $guestFirstname = 'Error';
    $guestLastname = 'User';
    $guestEmail = 'error@example.com';
    $numberOfGuests = 1;
    $specialRequests = '';
    $paymentOption = 'Full Payment';
    $amountPaid = 0;
    $totalAmount = 0;
    $promoTitle = 'Error';
    $promoPrice = 0;
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    $bookingRef = 'ERROR-' . uniqid();
    $paymentStatus = 'failed';
    $guestFirstname = 'Error';
    $guestLastname = 'User';
    $guestEmail = 'error@example.com';
    $numberOfGuests = 1;
    $specialRequests = '';
    $paymentOption = 'Full Payment';
    $amountPaid = 0;
    $totalAmount = 0;
    $promoTitle = 'Error';
    $promoPrice = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .success-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 40px;
        }
        .booking-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h1 class="mb-3">Payment Successful!</h1>
            <p class="text-muted mb-4">Your table promo booking has been confirmed.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                A confirmation email has been sent to you.
            </div>
            
            <div class="d-flex gap-3 justify-content-center">
                <a href="home.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
