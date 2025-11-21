<?php
require_once 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['booking_id'])) {
        throw new Exception('Booking ID is required');
    }

    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);

    // Start transaction
    mysqli_begin_transaction($con);

    // Get booking details with proper JOIN and type checking
    $sql = "SELECT b.*, rb.room_name, rt.room_type 
            FROM bookings b 
            LEFT JOIN room_bookings rb ON b.booking_id = rb.booking_id 
            LEFT JOIN room_types rt ON rb.room_type_id = rt.room_type_id 
            WHERE b.booking_id = ? FOR UPDATE";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $booking_id); // Use integer binding
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Booking not found");
    }

    $booking = $result->fetch_assoc();

    // Debug logging
    error_log("Current booking status: " . $booking['status']);

    // Validate current status
    if ($booking['status'] !== 'Pending') {
        throw new Exception("Cannot check in booking. Current status is '{$booking['status']}'. Must be 'Pending'.");
    }

    // Update booking status using prepared statement
    $new_status = 'Checked in';
    $update_sql = "UPDATE bookings SET status = ? WHERE booking_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $booking_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update booking status: " . $update_stmt->error);
    }

    if ($update_stmt->affected_rows === 0) {
        throw new Exception("No booking was updated");
    }

    // Send confirmation email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'alfredaceveda.3@gmail.com';
    $mail->Password = 'dqdf eqmn trjf vwnh';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->SMTPDebug = 0;
    
    $mail->Timeout = 60;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPKeepAlive = true;
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->setFrom('alfredaceveda.3@gmail.com', 'Casa Estela Hotel Boutique and Cafe');
    $mail->addAddress($booking['email'], $booking['first_name'] . ' ' . $booking['last_name']);
    $mail->isHTML(true);
    $mail->Subject = 'Check-in Confirmation - Casa Estela Hotel Boutique and Cafe';

    // Format dates
    $check_in = date('F d, Y', strtotime($booking['check_in']));
    $check_out = date('F d, Y', strtotime($booking['check_out']));

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #333;'>Check-in Confirmation</h2>
            <p>Dear {$booking['first_name']} {$booking['last_name']},</p>
            <p>Welcome to Casa Estela Hotel Boutique and Cafe! This email confirms that you have successfully checked in.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                <h3 style='margin-top: 0;'>Booking Details:</h3>
                <p><strong>Booking ID:</strong> {$booking_id}</p>
                <p><strong>Room Type:</strong> {$booking['room_type']}</p>
                <p><strong>Room:</strong> {$booking['room_name']}</p>
                <p><strong>Check-in Date:</strong> {$check_in}</p>
                <p><strong>Check-out Date:</strong> {$check_out}</p>
                <p><strong>Number of Guests:</strong> {$booking['number_of_guests']}</p>
            </div>

            <p>If you need any assistance during your stay, please don't hesitate to contact our front desk.</p>
            <p>We hope you enjoy your stay with us!</p>
            
            <p>Best regards,<br>Casa Estela Hotel Boutique and Cafe Team</p>
        </div>
    ";

    try {
        $mail->send();
        $mail_sent = true;
        $message = "Check-in successful and confirmation email sent to " . $booking['email'];
    } catch (Exception $e) {
        error_log("Mail Error: " . $e->getMessage());
        $mail_sent = false;
        $message = "Check-in successful but failed to send confirmation email. Error: " . $e->getMessage();
    }

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'emailSent' => $mail_sent,
        'message' => $message
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con)) {
        mysqli_rollback($con);
    }
    
    error_log("Check-in Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'emailSent' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($con)) {
    mysqli_close($con);
}
?>
