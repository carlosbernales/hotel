<?php
require_once 'db.php';
require_once 'config.php';  // Add config file
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Get JSON data from the request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

try {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    
    // Debug settings - More verbose
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $debugOutput = '';
    $mail->Debugoutput = function($str, $level) use (&$debugOutput) {
        $debugOutput .= "[$level] $str\n";
        error_log("PHPMailer Debug [$level]: $str");
    };

    // Basic server settings
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Using explicit SSL
    $mail->Port = SMTP_PORT;
    
    // Additional settings
    $mail->CharSet = 'UTF-8';
    $mail->Timeout = 30; // Reduced timeout
    $mail->SMTPKeepAlive = false; // Disable keep-alive
    
    // SSL settings for development
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Log configuration
    error_log("Starting email send attempt with configuration:");
    error_log("Host: " . SMTP_HOST);
    error_log("Port: " . SMTP_PORT);
    error_log("Username: " . SMTP_USERNAME);
    error_log("Encryption: SMTPS (SSL)");

    // Test connection before proceeding
    try {
        if (!$mail->smtpConnect()) {
            throw new Exception("SMTP connection failed");
        }
        error_log("SMTP connection test successful");
        $mail->smtpClose(); // Close test connection
    } catch (Exception $e) {
        error_log("SMTP connection test failed: " . $e->getMessage());
        throw new Exception("Failed to connect to mail server: " . $e->getMessage());
    }

    // Set up email
    if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid recipient email address: ' . $data['to']);
    }
    
    $mail->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
    $mail->addAddress($data['to']);
    
    // Content setup
    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmation - Casa Estela Boutique Hotel';

    // Create email body
    $emailBody = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background-color: #DAA520; color: white; padding: 20px; text-align: center;'>
            <h1>Booking Confirmation</h1>
        </div>
        
        <div style='padding: 20px; background-color: #f8f9fa;'>
            <p>Dear {$data['firstName']} {$data['lastName']},</p>
            
            <p>Thank you for choosing Casa Estela Boutique Hotel. Your booking has been confirmed!</p>
            
            <div style='background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='color: #DAA520;'>Booking Details</h3>
                <p><strong>Booking ID:</strong> {$data['bookingId']}</p>
                <p><strong>Check-in Date:</strong> {$data['checkIn']}</p>
                <p><strong>Check-out Date:</strong> {$data['checkOut']}</p>
                
                <h4 style='color: #DAA520; margin-top: 20px;'>Room Details:</h4>";

    // Add room details
    foreach ($data['rooms'] as $room) {
        $emailBody .= "
        <div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px;'>
            <p><strong>Room Type:</strong> {$room['type']}</p>
            <p><strong>Number of Nights:</strong> {$room['nights']}</p>
            <p><strong>Number of Guests:</strong> {$room['guestCount']}</p>
            <p><strong>Price:</strong> ₱" . number_format($room['totalPrice'], 2) . "</p>
        </div>";
    }

    $emailBody .= "
                <p style='font-size: 18px; margin-top: 20px;'><strong>Total Amount:</strong> ₱" . number_format($data['totalAmount'], 2) . "</p>
            </div>
            
            <div style='background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='color: #DAA520;'>Important Information</h3>
                <ul style='list-style-type: none; padding-left: 0;'>
                    <li>✓ Check-in time: 2:00 PM</li>
                    <li>✓ Check-out time: 12:00 PM</li>
                    <li>✓ Please present a valid ID upon check-in</li>
                    <li>✓ For any queries, please contact us at +63 917 709 1888</li>
                </ul>
            </div>
            
            <p>We look forward to welcoming you at Casa Estela Boutique Hotel!</p>
            
            <p style='margin-top: 20px;'>Best regards,<br>Casa Estela Boutique Hotel Team</p>
        </div>
        
        <div style='background-color: #333; color: white; padding: 20px; text-align: center; font-size: 12px;'>
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>© " . date('Y') . " Casa Estela Boutique Hotel. All rights reserved.</p>
        </div>
    </div>";

    $mail->Body = $emailBody;
    $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailBody));

    if (!$mail->send()) {
        throw new Exception('Mailer Error: ' . $mail->ErrorInfo . "\nDebug Output: " . $debugOutput);
    }

    error_log("Email sent successfully to: " . $data['to']);
    echo json_encode([
        'success' => true, 
        'message' => 'Email sent successfully',
        'debug' => $debugOutput
    ]);

} catch (Exception $e) {
    error_log("Email sending failed: " . $e->getMessage());
    error_log("Debug output: " . $debugOutput);
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Email could not be sent',
        'error' => $e->getMessage(),
        'debug' => $debugOutput
    ]);
}
?> 