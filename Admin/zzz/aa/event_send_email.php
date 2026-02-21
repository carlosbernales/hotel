<?php
// This file will handle the design and process of sending booking confirmation emails.
// It will be called after a successful booking payment.

// Include necessary files
require_once '../includes/init.php';
require_once '../includes/Mailer.php';

function sendBookingConfirmationEmail($bookingDetails, $recipientEmail) {
    // Sanitize and validate input data
    $bookingReference = htmlspecialchars($bookingDetails['booking_reference']);
    $packageName = htmlspecialchars($bookingDetails['package_name']);
    $packagePrice = htmlspecialchars($bookingDetails['package_price']);
    $downPayment = htmlspecialchars($bookingDetails['down_payment']);
    $balance = htmlspecialchars($bookingDetails['balance']);
    $eventDate = htmlspecialchars($bookingDetails['event_date']);
    $eventTime = htmlspecialchars($bookingDetails['event_time']);
    $venue = htmlspecialchars($bookingDetails['venue']);
    $customerName = htmlspecialchars($bookingDetails['customer_name']);

    // Email subject
    $subject = "Booking Confirmation - Reference: " . $bookingReference;

    // Email body (HTML format for better design)
    $message = "
    <html>
    <head>
        <title>Booking Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
            .header { background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { padding: 20px; }
            .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .highlight { background-color: #e8f5e8; padding: 10px; border-left: 4px solid #4CAF50; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Your Booking is Confirmed!</h2>
            </div>
            <div class='content'>
                <p>Dear " . $customerName . ",</p>
                <p>Thank you for your booking with us. Your event booking has been successfully completed and confirmed.</p>
                
                <div class='highlight'>
                    <strong>Booking Reference: " . $bookingReference . "</strong><br>
                    Please save this reference for future inquiries.
                </div>
                
                <h3>Booking Details:</h3>
                <table>
                    <tr><th>Package Name:</th><td>" . $packageName . "</td></tr>
                    <tr><th>Package Price:</th><td>P" . number_format($packagePrice, 2) . "</td></tr>
                    <tr><th>Down Payment:</th><td>P" . number_format($downPayment, 2) . "</td></tr>
                    <tr><th>Balance Due:</th><td>P" . number_format($balance, 2) . "</td></tr>
                    <tr><th>Event Date:</th><td>" . $eventDate . "</td></tr>
                    <tr><th>Event Time:</th><td>" . $eventTime . "</td></tr>
                    <tr><th>Venue:</th><td>" . $venue . "</td></tr>
                </table>
                
                <h3>Payment Status:</h3>
                <table>
                    <tr><th>Down Payment Status:</th><td style='color: #4CAF50; font-weight: bold;'>Paid</td></tr>
                    <tr><th>Remaining Balance:</th><td>P" . number_format($balance, 2) . " (Due on event day)</td></tr>
                </table>
                
                <p><strong>Important Notes:</strong></p>
                <ul>
                    <li>Please arrive at least 30 minutes before your scheduled event time</li>
                    <li>Bring a valid ID for verification</li>
                    <li>Remaining balance must be paid on the event day</li>
                    <li>For any changes or inquiries, please contact us with your booking reference</li>
                </ul>
                
                <p>We look forward to hosting your event!</p>
                <p>Best regards,<br>Event Management Team</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Your Company Name. All rights reserved.</p>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>";

    // Set email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@yourcompany.com" . "\r\n";
    $headers .= "Reply-To: info@yourcompany.com" . "\r\n";

    // Send email using PHPMailer
    try {
        $mailer = new Mailer();
        $mailer->addAddress($recipientEmail, $customerName);
        $mailer->Subject = $subject;
        $mailer->Body = $message;
        $mailer->isHTML(true);
        
        if ($mailer->send()) {
            return true;
        } else {
            error_log("Email sending failed: " . $mailer->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log("Email exception: " . $e->getMessage());
        return false;
    }
}

// Function to send notification email to admin
function sendAdminNotificationEmail($bookingDetails) {
    $bookingReference = htmlspecialchars($bookingDetails['booking_reference']);
    $packageName = htmlspecialchars($bookingDetails['package_name']);
    $customerName = htmlspecialchars($bookingDetails['customer_name']);
    $customerEmail = htmlspecialchars($bookingDetails['customer_email']);
    $eventDate = htmlspecialchars($bookingDetails['event_date']);
    $packagePrice = htmlspecialchars($bookingDetails['package_price']);

    $subject = "New Event Booking - " . $bookingReference;
    
    $message = "
    <html>
    <head>
        <title>New Booking Notification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #2196F3; color: white; padding: 10px 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { padding: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Event Booking Received</h2>
            </div>
            <div class='content'>
                <p>A new event booking has been successfully completed:</p>
                <table>
                    <tr><th>Booking Reference:</th><td>" . $bookingReference . "</td></tr>
                    <tr><th>Customer Name:</th><td>" . $customerName . "</td></tr>
                    <tr><th>Customer Email:</th><td>" . $customerEmail . "</td></tr>
                    <tr><th>Package Name:</th><td>" . $packageName . "</td></tr>
                    <tr><th>Package Price:</th><td>P" . number_format($packagePrice, 2) . "</td></tr>
                    <tr><th>Event Date:</th><td>" . $eventDate . "</td></tr>
                </table>
                <p>Please check the admin panel for complete booking details.</p>
            </div>
        </div>
    </body>
    </html>";

    try {
        $mailer = new Mailer();
        $mailer->addAddress('admin@yourcompany.com', 'Admin');
        $mailer->Subject = $subject;
        $mailer->Body = $message;
        $mailer->isHTML(true);
        
        return $mailer->send();
    } catch (Exception $e) {
        error_log("Admin notification email exception: " . $e->getMessage());
        return false;
    }
}
?>
