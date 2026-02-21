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
            
            // Disable debug output to prevent breaking JSON response
            $this->mailer->SMTPDebug = 0; // Disable debug output
            // Only log errors to error log, not to output
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

    /**
     * Send booking confirmation email
     * 
     * @param array $bookingData Array containing booking details including 'email' and other booking information
     * @return bool True on success, throws Exception on failure
     * @throws Exception If email sending fails
     */
    public function sendBookingConfirmation($bookingData) {
        // Extract email from booking data
        $email = $bookingData['email'] ?? '';
        if (empty($email)) {
            throw new Exception("No email address provided in booking data");
        }
        try {
            // Clear any previous recipients/attachments
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            error_log("Attempting to send booking confirmation to: " . $email);
            
            // Set sender and recipient
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->addAddress($email);
            
            // Email content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Booking Confirmation - ' . ($bookingData['booking_reference'] ?? 'N/A');
            
            // Check if this is a room booking or event booking
            if (isset($bookingData['booking_type']) && $bookingData['booking_type'] === 'room') {
                $this->mailer->Body = $this->getRoomBookingConfirmationTemplate($bookingData);
                $this->mailer->AltBody = $this->getPlainTextRoomBookingConfirmation($bookingData);
            } else {
                // Default to event booking template
                $this->mailer->Body = $this->getBookingConfirmationTemplate($bookingData);
                $this->mailer->AltBody = $this->getPlainTextBookingConfirmation($bookingData);
            }
            
            // Send email
            error_log('Attempting to send email...');
            $result = $this->mailer->send();
            if (!$result) {
                $error = "Mailer Error Details: " . $this->mailer->ErrorInfo;
                error_log($error);
                throw new Exception($error);
            }
            
            error_log('Email sent successfully to ' . $bookingData['email']);
            
            error_log("Booking confirmation email sent successfully to: " . $email);
            return true;
            
        } catch (Exception $e) {
            error_log("Send Booking Confirmation Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Failed to send booking confirmation: " . $e->getMessage());
        }
    }

    private function getBookingConfirmationTemplate($booking) {
        $paymentStatus = ($booking['payment_type'] === 'full_payment' ? 'Paid in Full' : 'Down Payment');
        $balanceDue = ($booking['payment_type'] === 'full_payment' ? '₱0.00' : '₱' . number_format($booking['total_amount'] / 2, 2));
        
        // Format event date and time
        $eventDateTime = new DateTime($booking['date_time_start']);
        $eventDate = $eventDateTime->format('F j, Y');
        $eventTime = $eventDateTime->format('h:i A');
        
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #d4af37; padding: 20px; text-align: center; color: white;">
                <h1>Casa Estela Hotel Boutique</h1>
                <p>Event Booking Confirmation</p>
            </div>
            <div style="padding: 20px; border: 1px solid #ddd; border-top: none;">
                <h2>Thank you for your booking!</h2>
                <p>Dear ' . htmlspecialchars($booking['customer_name']) . ',</p>
                <p>Your event booking has been confirmed. Below are your booking details:</p>
                
                <div style="background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #d4af37;">
                    <h3 style="margin-top: 0; color: #333;">Booking Reference: ' . htmlspecialchars($booking['booking_refId']) . '</h3>
                    <p><strong>Event:</strong> ' . htmlspecialchars($booking['package_name']) . '</p>
                    <p><strong>Date:</strong> ' . $eventDate . '</p>
                    <p><strong>Time:</strong> ' . $eventTime . '</p>
                    <p><strong>Venue:</strong> ' . htmlspecialchars($booking['place']) . '</p>
                    <p><strong>Number of Guests:</strong> ' . $booking['number_of_guests'] . '</p>
                </div>
                
                <div style="margin: 20px 0;">
                    <h3>Payment Information</h3>
                    <p><strong>Total Amount:</strong> ₱' . number_format($booking['total_amount'], 2) . '</p>
                    <p><strong>Payment Status:</strong> ' . $paymentStatus . '</p>
                    <p><strong>Amount Paid:</strong> ₱' . number_format($booking['paid_amount'], 2) . '</p>
                    <p><strong>Balance Due:</strong> ' . $balanceDue . '</p>
                </div>
                
                <p>If you have any questions about your booking, please contact us at ' . SMTP_USERNAME . ' or call us at [Your Contact Number].</p>
                
                <div style="margin-top: 30px; padding: 15px; background-color: #f5f5f5; border-left: 4px solid #d4af37;">
                    <p style="margin: 0;"><strong>Need to make changes to your booking?</strong><br>
                    Please contact our customer service team with your booking reference number.</p>
                </div>
                
                <p style="margin-top: 30px;">We look forward to hosting your event!</p>
                
                <p>Best regards,<br>The Casa Estela Team</p>
            </div>
            <div style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #ddd;">
                <p>&copy; ' . date('Y') . ' Casa Estela Hotel Boutique. All rights reserved.</p>
                <p>This is an automated email, please do not reply directly to this message.</p>
            </div>
        </div>';
    }
    
    private function getPlainTextBookingConfirmation($booking) {
        $paymentStatus = ($booking['payment_type'] === 'full_payment' ? 'Paid in Full' : 'Down Payment');
        $balanceDue = ($booking['payment_type'] === 'full_payment' ? '₱0.00' : '₱' . number_format($booking['total_amount'] / 2, 2));
        
        // Format event date and time
        $eventDateTime = new DateTime($booking['date_time_start']);
        $eventDate = $eventDateTime->format('F j, Y');
        $eventTime = $eventDateTime->format('h:i A');
        
        return "BOOKING CONFIRMATION\n\n" .
               "Dear " . $booking['customer_name'] . ",\n\n" .
               "Thank you for your booking at Casa Estela Hotel Boutique. Below are your booking details:\n\n" .
               "Booking Reference: " . $booking['booking_refId'] . "\n" .
               "Event: " . $booking['package_name'] . "\n" .
               "Date: " . $eventDate . "\n" .
               "Time: " . $eventTime . "\n" .
               "Venue: " . $booking['place'] . "\n" .
               "Number of Guests: " . $booking['number_of_guests'] . "\n\n" .
               "PAYMENT INFORMATION\n" .
               "Total Amount: ₱" . number_format($booking['total_amount'], 2) . "\n" .
               "Payment Status: " . $paymentStatus . "\n" .
               "Amount Paid: ₱" . number_format($booking['paid_amount'], 2) . "\n" .
               "Balance Due: " . $balanceDue . "\n\n" .
               "If you have any questions about your booking, please contact us at " . SMTP_USERNAME . " or call us at [Your Contact Number].\n\n" .
               "We look forward to hosting your event!\n\n" .
               "Best regards,\nThe Casa Estela Team";
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

    public function sendOrderConfirmation($orderData, $userEmail, $userName) {
        try {
            error_log("sendOrderConfirmation: Starting email sending process");
            error_log("sendOrderConfirmation: userEmail = " . $userEmail);
            error_log("sendOrderConfirmation: userName = " . $userName);
            
            // Reset recipients
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
            $this->mailer->addAddress($userEmail, $userName);
            
            error_log("sendOrderConfirmation: Recipients set successfully");

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Order Confirmation - Casa Estela Hotel Boutique Hotel and Cafe';
            
            error_log("sendOrderConfirmation: Email subject set");

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #d4af37; padding: 20px; text-align: center; color: white;'>
                    <h1>Order Confirmation</h1>
                </div>
                
                <div style='padding: 20px; border: 1px solid #ddd;'>
                    <h2>Thank you for your order!</h2>
                    <p>Dear {$userName},</p>
                    <p>Your order has been successfully placed and confirmed.</p>

                    <div style='background-color: #f5f5f5; padding: 15px; margin: 20px 0; border-left: 4px solid #d4af37;'>
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

            // Add order items if available
            if (!empty($orderData['items']) && is_array($orderData['items'])) {
                $body .= "
                        <h4>Order Items:</h4>
                        <table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>
                            <thead>
                                <tr style='background-color: #f8f9fa;'>
                                    <th style='padding: 8px; text-align: left; border-bottom: 1px solid #ddd;'>Item</th>
                                    <th style='padding: 8px; text-align: center; border-bottom: 1px solid #ddd;'>Quantity</th>
                                    <th style='padding: 8px; text-align: right; border-bottom: 1px solid #ddd;'>Price</th>
                                </tr>
                            </thead>
                            <tbody>";
                
                foreach ($orderData['items'] as $item) {
                    $itemName = htmlspecialchars($item['name'] ?? 'Unknown Item');
                    $quantity = intval($item['quantity'] ?? 1);
                    $price = floatval($item['price'] ?? 0);
                    $itemTotal = $price * $quantity;
                    
                    $body .= "
                                <tr>
                                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$itemName}";
                    
                    // Add addons if any
                    if (!empty($item['addons']) && is_array($item['addons'])) {
                        $body .= "<br><small style='color: #666;'>";
                        $addons = [];
                        foreach ($item['addons'] as $addon) {
                            $addonName = htmlspecialchars($addon['name'] ?? 'Addon');
                            $addonPrice = floatval($addon['price'] ?? 0);
                            $addons[] = $addonName . ' (₱' . number_format($addonPrice, 2) . ')';
                        }
                        $body .= 'Add-ons: ' . implode(', ', $addons);
                        $body .= "</small>";
                    }
                    
                    $body .= "</td>
                                    <td style='padding: 8px; text-align: center; border-bottom: 1px solid #ddd;'>{$quantity}</td>
                                    <td style='padding: 8px; text-align: right; border-bottom: 1px solid #ddd;'>₱" . number_format($itemTotal, 2) . "</td>
                                </tr>";
                }
                
                $body .= "
                            </tbody>
                        </table>";
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
            
            error_log("sendOrderConfirmation: Email body prepared, attempting to send...");

            $result = $this->mailer->send();
            
            if ($result) {
                error_log("sendOrderConfirmation: Email sent successfully");
                return true;
            } else {
                error_log("sendOrderConfirmation: Email sending failed. Error: " . $this->mailer->ErrorInfo);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("sendOrderConfirmation: Exception occurred: " . $e->getMessage());
            error_log("sendOrderConfirmation: Exception trace: " . $e->getTraceAsString());
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
            error_log('Starting to send event booking confirmation email');
            error_log('Booking data: ' . print_r($bookingData, true));
            
            // Check if email is provided
            if (empty($bookingData['email'])) {
                error_log('Error: No email address provided in booking data');
                throw new Exception('No email address provided');
            }
            
            // Reset recipients
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Set sender and recipient
            $fromEmail = defined('SMTP_USERNAME') ? SMTP_USERNAME : 'noreply@casaestela.com';
            $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Casa Estela';
            
            error_log('Setting sender to: ' . $fromEmail . ' (' . $fromName . ')');
            error_log('Sending to: ' . $bookingData['email']);
            
            $this->mailer->setFrom($fromEmail, $fromName);
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
                            <li>Additional charges may apply for extra guests and extra menu items</li>
                        </ul>
                    </div>
                    
                    <div style="margin-top: 20px; color: #666;">
                        <p>If you have any questions about your booking, please don\'t hesitate to contact us:</p>
                        <p>Phone: +63 912 345 6789</p>
                        <p>Email: casaestelabotiquehotelandcafe@gmail.com</p>
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