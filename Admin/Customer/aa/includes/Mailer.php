<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';
require_once 'config.php'; // Include the config file with SMTP constants

class Mailer {
    private $mailer;

    public function __construct() {
        try {
            $this->mailer = new PHPMailer(true);
            
            // Debug output
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mailer->Debugoutput = function($str, $level) {
                error_log("PHPMailer Debug [$level]: $str");
            };

            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = SMTP_SECURE;
            $this->mailer->Port = SMTP_PORT;
            
            // Additional settings
            $this->mailer->Timeout = 60;
            $this->mailer->CharSet = 'UTF-8';
            
            // SSL verification settings - Only use in development
            $this->mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->isHTML(true);
            
        } catch (Exception $e) {
            error_log("Mailer Constructor Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Failed to initialize mailer: " . $e->getMessage());
        }
    }

    public function sendVerificationCode($email, $code) {
        try {
            // Clear any previous recipients/attachments
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            error_log("Attempting to send verification code to: " . $email);
            
            // Set sender
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->addAddress($email);
            
            // Email content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Email Verification Code - Casa Estela Hotel Botique Hotel and Cafe';
            $this->mailer->Body = $this->getVerificationEmailTemplate($code);
            $this->mailer->AltBody = 'Your verification code is: ' . $code;
            
            // Send email
            $result = $this->mailer->send();
            if (!$result) {
                error_log("Mailer Error Details: " . $this->mailer->ErrorInfo);
                throw new Exception("Mail send failed: " . $this->mailer->ErrorInfo);
            }
            
            error_log("Verification email sent successfully to: " . $email);
            return true;
            
        } catch (Exception $e) {
            error_log("Send Verification Detailed Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Failed to send verification email: " . $e->getMessage());
        }
    }

    private function getVerificationEmailTemplate($code) {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #d4af37; padding: 20px; text-align: center; color: white;">
                <h1> E Akomoda</h1>
            </div>
            <div style="padding: 20px; border: 1px solid #ddd; border-top: none;">
                <h2>Verify Your Email Address</h2>
                <p>Thank you for registering with Casa Estela Hotel Boutique Hotel and Cafe. Please use the following verification code to complete your registration:</p>
                <div style="background-color: #f5f5f5; padding: 15px; text-align: center; margin: 20px 0;">
                    <h1 style="color: #d4af37; margin: 0; letter-spacing: 5px;">' . $code . '</h1>
                </div>
                <p>This code will expire in 2 minutes.</p>
                <p>If you did not request this verification code, please ignore this email.</p>
            </div>
            <div style="text-align: center; padding: 20px; color: #666;">
                <p>&copy; ' . date('Y') . ' Casa Estela Hotel Boutique Hotel and Cafe. All rights reserved.</p>
            </div>
        </div>';
    }

    public function sendEmail($to, $subject, $body) {
        try {
            // Recipients
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            
            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mailer->ErrorInfo);
            throw new Exception("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }

    public function sendBookingConfirmation($bookingData) {
        try {
            // Recipients
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->addAddress($bookingData['email'], $bookingData['first_name'] . ' ' . $bookingData['last_name']);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Booking Information - E Akomoda';

            // Calculate amounts for display
            $totalAmount = number_format($bookingData['total_amount'], 2);
            $downpayment = number_format($bookingData['downpayment_amount'], 2);
            $remaining = number_format($bookingData['remaining_balance'], 2);
            $extraCharges = number_format($bookingData['extra_charges'], 2);
            $discountAmount = number_format($bookingData['discount_amount'], 2);

            // Create email body
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #ffc107;'>Booking Information</h2>
                <p>Dear {$bookingData['first_name']} {$bookingData['last_name']},</p>
                <p>Thank you for choosing E Akomoda. Your booking has been confirmed.</p>

                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='color: #333;'>Booking Details</h3>
                    <p><strong>Check-in:</strong> " . date('F j, Y', strtotime($bookingData['check_in'])) . "</p>
                    <p><strong>Check-out:</strong> " . date('F j, Y', strtotime($bookingData['check_out'])) . "</p>
                    <p><strong>Number of Nights:</strong> {$bookingData['nights']}</p>
                    <p><strong>Arrival Time:</strong> {$bookingData['arrival_time']}</p>
                    <p><strong>Number of Guests:</strong> {$bookingData['number_of_guests']}</p>
                </div>

                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='color: #333;'>Payment Information</h3>
                    <p><strong>Payment Method:</strong> " . ucfirst($bookingData['payment_method']) . "</p>
                    <p><strong>Payment Option:</strong> " . ucfirst($bookingData['payment_option']) . "</p>
                    <p><strong>Original Amount:</strong> ₱{$totalAmount}</p>";

            if ($bookingData['extra_charges'] > 0) {
                $body .= "<p><strong>Extra Guest Charges:</strong> ₱{$extraCharges}</p>";
            }

            if ($bookingData['discount_amount'] > 0) {
                $body .= "
                    <p><strong>Discount ({$bookingData['discount_percentage']}%):</strong> -₱{$discountAmount}</p>
                    <p><strong>Total Amount:</strong> ₱{$totalAmount}</p>";
            }

            $paymentOption = strtolower(trim($bookingData['payment_option']));
            if ($paymentOption === 'partial payment' || $paymentOption === 'downpayment 1500' || $paymentOption === 'custom payment') {
                $paymentLabel = ucfirst($bookingData['payment_option']);
                $body .= "
                    <p><strong>{$paymentLabel} Amount:</strong> ₱{$downpayment}</p>
                    <p><strong>Remaining Balance:</strong> ₱{$remaining}</p>";
            }

            $body .= "
                    <p><strong>Payment Reference:</strong> {$bookingData['payment_reference']}</p>
                </div>

                <div style='margin-top: 20px;'>
                    <p><strong>Important Notes:</strong></p>
                    <ul>
                        <li>Please present this confirmation email upon check-in</li>
                        <li>Check-in time starts at your arrival time</li>
                        <li>Check-out time is until 12:00 PM</li>
                        <li>Please bring valid IDs for all guests</li>
                    </ul>
                </div>

                <div style='margin-top: 20px; color: #666;'>
                    <p>If you have any questions, please don't hesitate to contact us:</p>
                    <p>Phone: +63 912 345 6789</p>
                    <p>Email: support@eakomoda.com</p>
                </div>
            </div>";

            $this->mailer->Body = $body;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
            return false;
        }
    }

    public function sendOrderConfirmation($orderData, $userEmail, $userName) {
        try {
            // Reset recipients
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->addAddress($userEmail, $userName);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Order Confirmation - Casa Estela Hotel Boutique Hotel and Cafe';

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #d4af37; padding: 20px; text-align: center; color: white;'>
                    <h1>Order Confirmation</h1>
                </div>
                
                <div style='padding: 20px; border: 1px solid #ddd;'>
                    <h2>Thank you for your order!</h2>
                    <p>Dear {$userName},</p>
                    <p>Your order has been successfully placed.</p>

                    <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0;'>
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> #{$orderData['order_id']}</p>
                        <p><strong>Order Type:</strong> " . ucfirst($orderData['order_type']) . "</p>
                        <p><strong>Payment Method:</strong> " . ucfirst($orderData['payment_method']) . "</p>
                        <p><strong>Payment Status:</strong> {$orderData['payment_status']}</p>
                        <p><strong>Total Amount:</strong> ₱" . number_format($orderData['final_total'], 2) . "</p>
                        <p><strong>Amount Paid:</strong> ₱" . number_format($orderData['amount_paid'], 2) . "</p>";

            if ($orderData['remaining_balance'] > 0) {
                $body .= "<p><strong>Remaining Balance:</strong> ₱" . number_format($orderData['remaining_balance'], 2) . "</p>";
            }

            if ($orderData['order_type'] === 'advance') {
                $body .= "
                        <h3>Booking Details</h3>
                        <p><strong>Booking Date:</strong> {$orderData['table_details']['date']}</p>
                        <p><strong>Booking Time:</strong> {$orderData['table_details']['time']}</p>
                        <p><strong>Number of Guests:</strong> {$orderData['table_details']['guest_count']}</p>";
            }

            $body .= "
                    </div>

                    <div style='margin-top: 20px;'>
                        <p>If you have any questions about your order, please contact us:</p>
                        <p>Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                        <p>Phone: [Your Phone Number]</p>
                    </div>
                </div>

                <div style='text-align: center; padding: 20px; color: #666;'>
                    <p>&copy; " . date('Y') . " Casa Estela Hotel Boutique Hotel and Cafe. All rights reserved.</p>
                </div>
            </div>";

            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n"], $body));

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Order confirmation email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send event booking confirmation email
     * 
     * @param array $bookingData Array containing booking details
     * @return bool True on success, false on failure
     */
    public function sendEventBookingConfirmation($bookingData) {
        try {
            // Reset recipients
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->addAddress($bookingData['email'], $bookingData['first_name'] . ' ' . $bookingData['last_name']);
            
            // Set reply-to address if needed
            // $this->mailer->addReplyTo('noreply@example.com', 'No Reply');
            
            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Event Booking Information - Casa Estela Hotel Boutique Hotel and Cafe';
            
            // Format amounts
            $totalAmount = number_format($bookingData['total_amount'], 2);
            $paidAmount = number_format($bookingData['paid_amount'], 2);
            $remainingBalance = number_format($bookingData['remaining_balance'], 2);
            
            // Create email body
            $body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="background-color: #d4af37; padding: 20px; text-align: center; color: white;">
                    <h1>Event Booking Information!</h1>
                </div>
                
                <div style="padding: 20px; border: 1px solid #ddd;">
                    <h2>Thank you for your booking!</h2>
                    <p>Dear ' . htmlspecialchars($bookingData['first_name']) . ',</p>
                    <p>Your event booking has been successfully confirmed. Below are the details of your reservation:</p>
                    
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
                        <h3 style="color: #333; margin-top: 0;">Event Details</h3>
                        <p><strong>Event Date:</strong> ' . htmlspecialchars($bookingData['event_date']) . '</p>
                        <p><strong>Time:</strong> ' . htmlspecialchars($bookingData['start_time']) . ' - ' . htmlspecialchars($bookingData['end_time']) . '</p>
                        <p><strong>Package:</strong> ' . htmlspecialchars($bookingData['package_name']) . '</p>
                        <p><strong>Number of Guests:</strong> ' . htmlspecialchars($bookingData['number_of_guests']) . '</p>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
                        <h3 style="color: #333; margin-top: 0;">Payment Information</h3>
                        <p><strong>Total Amount:</strong> ₱' . $totalAmount . '</p>
                        <p><strong>Amount Paid:</strong> ₱' . $paidAmount . '</p>';
            
            // Only show remaining balance if there is one
            if ($bookingData['remaining_balance'] > 0) {
                $body .= '
                        <p><strong>Remaining Balance:</strong> ₱' . $remainingBalance . '</p>
                        <p><strong>Payment Method:</strong> ' . htmlspecialchars(ucfirst($bookingData['payment_method'])) . ' (' . htmlspecialchars(ucfirst($bookingData['payment_type'])) . ')</p>';
            }
            
            $body .= '
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <p><strong>Important Notes:</strong></p>
                        <ul>
                            <li>Please present this confirmation email upon arrival</li>
                            <li>For any changes or cancellations, please contact us at least 48 hours in advance</li>
                            <li>Additional charges may apply for extra guests beyond the package limit</li>
                        </ul>
                    </div>
                    
                    <div style="margin-top: 20px; color: #666;">
                        <p>If you have any questions about your booking, please don\'t hesitate to contact us:</p>
                        <p>Phone: +63 912 345 6789</p>
                        <p>Email: events@casaestela.com</p>
                    </div>
                </div>
                
                <div style="text-align: center; padding: 20px; color: #666; font-size: 0.9em;">
                    <p>&copy; ' . date('Y') . ' Casa Estela Hotel Boutique Hotel and Cafe. All rights reserved.</p>
                </div>
            </div>';
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n"], $body));
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Event booking confirmation email error: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendMail($to, $subject, $body) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }

    /**
     * Send an HTML email with a plain text alternative
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlBody HTML version of the email body
     * @param string $textBody Plain text version of the email body
     * @return bool Whether the email was sent successfully
     */
    public function sendHtmlMail($to, $subject, $htmlBody, $textBody = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            
            // Set email to HTML format
            $this->mailer->isHTML(true);
            $this->mailer->Body = $htmlBody;
            
            // Set plain text alternative
            if (!empty($textBody)) {
                $this->mailer->AltBody = $textBody;
            } else {
                // If no text body provided, create one by stripping HTML
                $this->mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n"], $htmlBody));
            }
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("HTML email sending failed: " . $this->mailer->ErrorInfo);
            throw new Exception("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }
} 