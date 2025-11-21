<?php
// Start output buffering
ob_start();

// Include database connection
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the content type to JSON
header('Content-Type: application/json');

// Function to validate date format (YYYY-MM-DD)
function isValidDate($date) {
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Check if booking_id and new dates are provided
if (!isset($_POST['booking_id']) || !isset($_POST['new_check_in']) || !isset($_POST['new_check_out'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Get and sanitize input
$bookingId = mysqli_real_escape_string($con, $_POST['booking_id']);
$newCheckIn = mysqli_real_escape_string($con, $_POST['new_check_in']);
$newCheckOut = mysqli_real_escape_string($con, $_POST['new_check_out']);

// Validate dates
if (!isValidDate($newCheckIn) || !isValidDate($newCheckOut)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid date format'
    ]);
    exit;
}

// Check if the check-out date is after the check-in date
$checkInDate = new DateTime($newCheckIn);
$checkOutDate = new DateTime($newCheckOut);

if ($checkInDate >= $checkOutDate) {
    echo json_encode([
        'success' => false,
        'message' => 'Check-out date must be after check-in date'
    ]);
    exit;
}

// Get the original booking to send email notification
$getBookingSql = "SELECT * FROM bookings WHERE booking_id = '$bookingId'";
$bookingResult = mysqli_query($con, $getBookingSql);

if (!$bookingResult || mysqli_num_rows($bookingResult) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking not found'
    ]);
    exit;
}

$bookingData = mysqli_fetch_assoc($bookingResult);
$customerEmail = $bookingData['email'];
$customerName = $bookingData['first_name'] . ' ' . $bookingData['last_name'];
$originalCheckIn = $bookingData['check_in'];
$originalCheckOut = $bookingData['check_out'];

// Start transaction
mysqli_begin_transaction($con);

try {
    // Update the booking dates
    $updateSql = "UPDATE bookings SET 
                    check_in = '$newCheckIn', 
                    check_out = '$newCheckOut'
                  WHERE booking_id = '$bookingId'";

    $updateResult = mysqli_query($con, $updateSql);

    if (!$updateResult) {
        throw new Exception("Failed to update booking dates: " . mysqli_error($con));
    }

    // Check if booking_history table exists, if not, create it
    $checkTableSql = "SHOW TABLES LIKE 'booking_history'";
    $tableExists = mysqli_query($con, $checkTableSql);
    
    if (!$tableExists || mysqli_num_rows($tableExists) == 0) {
        // Create booking_history table if it doesn't exist
        $createTableSql = "CREATE TABLE booking_history (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            booking_id VARCHAR(50) NOT NULL,
            action VARCHAR(50) NOT NULL,
            details TEXT,
            performed_by VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $createResult = mysqli_query($con, $createTableSql);
        if (!$createResult) {
            throw new Exception("Failed to create booking_history table: " . mysqli_error($con));
        }
    }

    // Add entry to booking_history table
    $details = mysqli_real_escape_string($con, "Booking rescheduled from $originalCheckIn - $originalCheckOut to $newCheckIn - $newCheckOut");
    $historySql = "INSERT INTO booking_history (booking_id, action, details, performed_by, created_at) 
                  VALUES ('$bookingId', 'reschedule', '$details', 'Admin', NOW())";
    
    $historyResult = mysqli_query($con, $historySql);
    if (!$historyResult) {
        throw new Exception("Failed to add booking history: " . mysqli_error($con));
    }

    // Commit the transaction
    mysqli_commit($con);

    // Send email notification to customer
    $to = $customerEmail;
    $subject = "Booking Rescheduled - Casa Estela Boutique Hotel & Cafe";
    
    $message = "
    <html>
    <head>
        <title>Booking Rescheduled</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #FFC107; color: #333; padding: 10px; text-align: center; }
            .content { padding: 20px; border: 1px solid #ddd; }
            .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #666; }
            .details { margin: 20px 0; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Casa Estela Boutique Hotel & Cafe</h2>
            </div>
            <div class='content'>
                <h3>Dear $customerName,</h3>
                <p>We would like to inform you that your booking at Casa Estela Boutique Hotel & Cafe has been rescheduled.</p>
                
                <div class='details'>
                    <table>
                        <tr>
                            <th colspan='2'>Booking Details</th>
                        </tr>
                        <tr>
                            <td>Booking ID:</td>
                            <td>#$bookingId</td>
                        </tr>
                        <tr>
                            <td>Previous Check-in Date:</td>
                            <td>" . date('F j, Y', strtotime($originalCheckIn)) . "</td>
                        </tr>
                        <tr>
                            <td>Previous Check-out Date:</td>
                            <td>" . date('F j, Y', strtotime($originalCheckOut)) . "</td>
                        </tr>
                        <tr>
                            <td>New Check-in Date:</td>
                            <td>" . date('F j, Y', strtotime($newCheckIn)) . "</td>
                        </tr>
                        <tr>
                            <td>New Check-out Date:</td>
                            <td>" . date('F j, Y', strtotime($newCheckOut)) . "</td>
                        </tr>
                    </table>
                </div>
                
                <p>If you have any questions or need further assistance, please contact us at <a href='mailto:info@casaestelaboutique.com'>info@casaestelaboutique.com</a> or call us at +63 917 123 4567.</p>
                
                <p>Thank you for choosing Casa Estela Boutique Hotel & Cafe. We look forward to welcoming you!</p>
                
                <p>Best Regards,<br>Casa Estela Boutique Hotel & Cafe Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated email. Please do not reply to this message.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Set email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Casa Estela Boutique Hotel & Cafe <noreply@casaestelaboutique.com>" . "\r\n";
    
    // Send email (don't block the response if email fails)
    @mail($to, $subject, $message, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking dates updated successfully',
        'new_check_in' => $newCheckIn,
        'new_check_out' => $newCheckOut
    ]);
    
} catch (Exception $e) {
    // Rollback the transaction in case of error
    mysqli_rollback($con);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering and flush output
ob_end_flush();
?> 