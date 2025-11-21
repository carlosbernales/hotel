<?php
require_once 'db.php';

// Prevent any unwanted output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Ensure clean output buffer
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Debug: Log raw POST data
error_log("Raw POST data: " . print_r($_POST, true));

// Create table_bookings table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS table_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) UNIQUE,
    customer_name VARCHAR(100),
    booking_date DATE,
    booking_time TIME,
    num_guests INT,
    payment_method VARCHAR(50),
    total_amount DECIMAL(10,2),
    amount_paid DECIMAL(10,2),
    change_amount DECIMAL(10,2),
    special_requests TEXT,
    package_type VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $create_table_sql)) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Database setup error: ' . mysqli_error($con)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get and sanitize form data
        $customer_name = mysqli_real_escape_string($con, $_POST['customerName']);
        $contact_number = mysqli_real_escape_string($con, $_POST['contactNumber']);
        $email = !empty($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : null;
        $package_type = mysqli_real_escape_string($con, $_POST['packageType']);
        $reservation_date = mysqli_real_escape_string($con, $_POST['bookingDate']);
        $arrival_time = mysqli_real_escape_string($con, $_POST['bookingTime']);
        $guest_count = (int)$_POST['numGuests'];
        $special_requests = !empty($_POST['specialRequests']) ? mysqli_real_escape_string($con, $_POST['specialRequests']) : null;
        $payment_method = mysqli_real_escape_string($con, $_POST['paymentMethod']);
        $total_amount = (float)$_POST['totalAmount'];
        $amount_paid = (float)$_POST['amountPaid'];
        $change_amount = max(0, $amount_paid - $total_amount);

        // Validate required fields
        if (empty($customer_name) || empty($contact_number) || empty($reservation_date) || 
            empty($arrival_time) || empty($package_type) || empty($payment_method)) {
            throw new Exception("Please fill in all required fields");
        }

        // Insert into table_bookings
        $sql = "INSERT INTO table_bookings (
            customer_name, contact_number, email, package_type,
            reservation_date, arrival_time, guest_count,
            special_requests, payment_method, total_amount,
            amount_paid, change_amount, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

        $stmt = mysqli_prepare($con, $sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, 'ssssssissddd',
            $customer_name,
            $contact_number,
            $email,
            $package_type,
            $reservation_date,
            $arrival_time,
            $guest_count,
            $special_requests,
            $payment_method,
            $total_amount,
            $amount_paid,
            $change_amount
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing statement: " . mysqli_error($con));
        }

        $booking_id = mysqli_insert_id($con);

        // Success response
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Booking successful',
            'booking_id' => $booking_id
        ]);

        mysqli_stmt_close($stmt);

    } catch (Exception $e) {
        error_log("Error in table booking: " . $e->getMessage());
        ob_clean();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

// Close database connection
mysqli_close($con);
?>
