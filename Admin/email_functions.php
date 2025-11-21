<?php
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendBookingConfirmationEmail($recipientEmail, $recipientName, $bookingDetails) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com'; // Replace with your Gmail
        $mail->Password = 'fcbsurxkzcwougyb'; // Replace with your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela');
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - Casa Estela';

        // Email body
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { padding: 20px; }
                .header { background-color: #B8860B; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; }
                .details { margin: 20px 0; }
                .details table { width: 100%; border-collapse: collapse; }
                .details th, .details td { padding: 10px; border: 1px solid #ddd; }
                .details th { background-color: #f8f8f8; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Booking Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear {$recipientName},</p>
                    <p>Thank you for choosing Casa Estela. Your booking has been confirmed successfully!</p>
                    
                    <div class='details'>
                        <h3>Booking Details:</h3>
                        <table>
                            <tr>
                                <th>Booking ID</th>
                                <td>{$bookingDetails['booking_id']}</td>
                            </tr>
                            <tr>
                                <th>Check-in Date</th>
                                <td>{$bookingDetails['check_in']}</td>
                            </tr>
                            <tr>
                                <th>Check-out Date</th>
                                <td>{$bookingDetails['check_out']}</td>
                            </tr>
                            <tr>
                                <th>Room Type</th>
                                <td>{$bookingDetails['room_type']}</td>
                            </tr>
                            <tr>
                                <th>Number of Guests</th>
                                <td>{$bookingDetails['number_of_guests']}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>₱{$bookingDetails['total_amount']}</td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td>{$bookingDetails['payment_status']}</td>
                            </tr>
                        </table>
                    </div>

                    <p>If you have any questions or need to modify your booking, please don't hesitate to contact us.</p>
                </div>
                <div class='footer'>
                    <p>Casa Estela Boutique Hotel & Cafe</p>
                    <p>Contact: 09087474892/043-4416924| Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                    <p>Address: Gov B Marasigan St, Libis, Calapan City, Oriental Mindoro</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

function sendBookingRejectionEmail($recipientEmail, $recipientName, $bookingDetails) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
        $mail->Password = 'fcbsurxkzcwougyb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela');
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Status Update - Casa Estela';

        // Email body
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { padding: 20px; }
                .header { background-color: #8B4513; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; }
                .details { margin: 20px 0; }
                .details table { width: 100%; border-collapse: collapse; }
                .details th, .details td { padding: 10px; border: 1px solid #ddd; }
                .details th { background-color: #f8f8f8; }
                .status-badge { 
                    background-color: #DC3545; 
                    color: white; 
                    padding: 5px 10px; 
                    border-radius: 5px; 
                }
                .reason { 
                    background-color: #FFF3CD; 
                    border: 1px solid #FFEEBA; 
                    padding: 15px; 
                    margin: 20px 0; 
                    border-radius: 5px; 
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Booking Status Update</h2>
                </div>
                <div class='content'>
                    <p>Dear {$recipientName},</p>
                    <p>We regret to inform you that your booking request at Casa Estela has been rejected.</p>
                    
                    <div class='reason'>
                        <strong>Reason for Rejection:</strong> {$bookingDetails['reason']}
                    </div>
                    
                    <div class='details'>
                        <h3>Booking Details:</h3>
                        <table>
                            <tr>
                                <th>Booking ID</th>
                                <td>{$bookingDetails['booking_id']}</td>
                            </tr>
                            <tr>
                                <th>Check-in Date</th>
                                <td>{$bookingDetails['check_in']}</td>
                            </tr>
                            <tr>
                                <th>Check-out Date</th>
                                <td>{$bookingDetails['check_out']}</td>
                            </tr>
                            <tr>
                                <th>Room Type</th>
                                <td>{$bookingDetails['room_type']}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>₱{$bookingDetails['total_amount']}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span class='status-badge'>Rejected</span></td>
                            </tr>
                        </table>
                    </div>

                    <p>If you have any questions or would like to make a new booking, please don't hesitate to contact us.</p>
                    <p>We apologize for any inconvenience caused and hope to serve you in the future.</p>
                </div>
                <div class='footer'>
                    <p>Casa Estela Boutique Hotel & Cafe</p>
                    <p>Contact: 09087474892/043-4416924 | Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                    <p>Address: Gov B Marasigan St, Libis, Calapan City, Oriental Mindoro</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        throw new Exception("Failed to send email: " . $mail->ErrorInfo);
    }
}

function sendBookingAcceptanceEmail($recipientEmail, $recipientName, $bookingDetails) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
        $mail->Password = 'fcbsurxkzcwougyb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela');
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Accepted - Casa Estela';

        // Email body
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { padding: 20px; }
                .header { background-color: #B8860B; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f5f5f5; padding: 20px; text-align: center; }
                .details { margin: 20px 0; }
                .details table { width: 100%; border-collapse: collapse; }
                .details th, .details td { padding: 10px; border: 1px solid #ddd; }
                .details th { background-color: #f8f8f8; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Booking Acceptance Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear {$recipientName},</p>
                    <p>Good news! Your booking at Casa Estela has been accepted and confirmed.</p>
                    
                    <div class='details'>
                        <h3>Booking Details:</h3>
                        <table>
                            <tr>
                                <th>Booking ID</th>
                                <td>{$bookingDetails['booking_id']}</td>
                            </tr>
                            <tr>
                                <th>Check-in Date</th>
                                <td>{$bookingDetails['check_in']}</td>
                            </tr>
                            <tr>
                                <th>Check-out Date</th>
                                <td>{$bookingDetails['check_out']}</td>
                            </tr>
                            <tr>
                                <th>Room Type</th>
                                <td>{$bookingDetails['room_type']}</td>
                            </tr>
                            <tr>
                                <th>Number of Nights</th>
                                <td>{$bookingDetails['nights']}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>₱" . number_format($bookingDetails['total_amount'], 2) . "</td>
                            </tr>
                            <tr>
                                <th>Amount Paid</th>
                                <td>₱" . number_format($bookingDetails['amount_paid'], 2) . "</td>
                            </tr>
                        </table>
                    </div>

                    <p>We look forward to welcoming you. If you have any questions, please contact us.</p>
                </div>
                <div class='footer'>
                    <p>Casa Estela Boutique Hotel & Cafe</p>
                    <p>Contact: 09087474892/043-4416924 | Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                    <p>Address: Gov B Marasigan St, Libis, Calapan City, Oriental Mindoro</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Acceptance email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

?>