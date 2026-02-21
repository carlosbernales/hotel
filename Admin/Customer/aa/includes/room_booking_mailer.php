<?php
require_once 'Mailer.php';

class RoomBookingMailer {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new Mailer();
    }
    
    public function sendBookingConfirmation($bookingData) {
        try {
            $to = $bookingData['email'];
            $subject = 'Booking Confirmation - ' . $bookingData['booking_reference'];
            
            // Create HTML email template
            $htmlContent = $this->createBookingConfirmationTemplate($bookingData);
            
            // Create plain text version
            $textContent = $this->createBookingConfirmationText($bookingData);
            
            // Send the email
            $result = $this->mailer->sendEmail($to, $subject, $htmlContent, $textContent);
            
            error_log("Booking confirmation email sent to: " . $to . " - Result: " . ($result ? 'Success' : 'Failed'));
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error sending booking confirmation email: " . $e->getMessage());
            return false;
        }
    }
    
    private function createBookingConfirmationTemplate($bookingData) {
        $currency = '₱';
        $bookingRef = $bookingData['booking_reference'];
        $checkIn = $bookingData['check_in'];
        $checkOut = $bookingData['check_out'];
        $nights = $bookingData['nights'];
        $totalAmount = $bookingData['total_amount'];
        $amountPaid = $bookingData['amount_paid'] ?? 0;
        $remainingBalance = $bookingData['remaining_balance'] ?? 0;
        $paymentOption = $bookingData['payment_option'] ?? '';
        $paymentMethod = $bookingData['payment_method'] ?? '';
        
        // Payment option display text
        $paymentOptions = [
            'down_payment' => 'Down Payment',
            'full_payment' => 'Full Payment',
            'custom_payment' => 'Custom Payment'
        ];
        
        $paymentMethodText = [
            'credit_card' => 'Credit/Debit Card',
            'gcash' => 'GCash',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer'
        ];
        
        $roomDetails = '';
        if (isset($bookingData['rooms']) && is_array($bookingData['rooms'])) {
            foreach ($bookingData['rooms'] as $room) {
                $roomName = $room['room_type_name'] ?? $room['name'] ?? 'Room';
                $roomPrice = $room['price'] ?? 0;
                $roomQty = $room['quantity'] ?? 1;
                $roomSubtotal = $roomPrice * $roomQty * $nights;
                
                $roomDetails .= "
                <tr>
                    <td style='padding: 12px; border-bottom: 1px solid #e9ecef; color: #495057;'>{$roomName}</td>
                    <td style='padding: 12px; border-bottom: 1px solid #e9ecef; text-align: center; color: #495057;'>{$roomQty}</td>
                    <td style='padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right; color: #495057;'>{$currency}" . number_format($roomPrice, 2) . "</td>
                    <td style='padding: 12px; border-bottom: 1px solid #e9ecef; text-align: center; color: #495057;'>{$nights}</td>
                    <td style='padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right; font-weight: 600; color: #b07d06;'>{$currency}" . number_format($roomSubtotal, 2) . "</td>
                </tr>";
            }
        }
        
        $guestInfo = '';
        if (isset($bookingData['adults']) && is_array($bookingData['adults'])) {
            foreach ($bookingData['adults'] as $index => $adult) {
                $guestInfo .= "<div style='margin-bottom: 8px;'><span style='color: #6c757d;'>Adult " . ($index + 1) . ":</span> <strong style='color: #495057;'>" . 
                             htmlspecialchars($adult['firstName'] . ' ' . $adult['lastName']) . 
                             "</strong> <span style='color: #6c757d;'>(Age: " . $adult['age'] . ")</span></div>";
            }
        }
        
        if (isset($bookingData['children']) && is_array($bookingData['children'])) {
            foreach ($bookingData['children'] as $index => $child) {
                $guestInfo .= "<div style='margin-bottom: 8px;'><span style='color: #6c757d;'>Child " . ($index + 1) . ":</span> <strong style='color: #495057;'>" . 
                             htmlspecialchars($child['firstName'] . ' ' . $child['lastName']) . 
                             "</strong> <span style='color: #6c757d;'>(Age: " . $child['age'] . ")</span></div>";
            }
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Booking Confirmation</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f5f7fa;
                    color: #2c3e50;
                    line-height: 1.6;
                    padding: 20px;
                }
                .email-container {
                    max-width: 700px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 16px;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
                    overflow: hidden;
                    border: 1px solid #e1e8ed;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                    position: relative;
                }
                .logo {
                    font-size: 28px;
                    font-weight: 700;
                    margin-bottom: 8px;
                    letter-spacing: -0.5px;
                }
                .header h2 {
                    font-size: 18px;
                    font-weight: 400;
                    margin: 0;
                    opacity: 0.9;
                }
                .content {
                    padding: 40px 30px;
                }
                .booking-ref {
                    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
                    border: 1px solid #d4e4ff;
                    border-left: 4px solid #667eea;
                    padding: 24px 28px;
                    margin-bottom: 32px;
                    border-radius: 12px;
                }
                .booking-ref h3 {
                    color: #667eea;
                    font-size: 14px;
                    font-weight: 600;
                    margin-bottom: 6px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .booking-ref p {
                    font-size: 24px;
                    font-weight: 700;
                    color: #2c3e50;
                    margin: 0;
                }
                .section {
                    background-color: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 28px;
                    margin-bottom: 24px;
                }
                .section-title {
                    color: #2c3e50;
                    font-size: 18px;
                    font-weight: 700;
                    margin-bottom: 20px;
                    padding-bottom: 12px;
                    border-bottom: 2px solid #e2e8f0;
                    display: flex;
                    align-items: center;
                }
                .section-title i {
                    margin-right: 12px;
                    font-size: 18px;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                }
                .info-item {
                    margin-bottom: 8px;
                }
                .info-label {
                    color: #64748b;
                    font-size: 13px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 4px;
                    font-weight: 600;
                }
                .info-value {
                    color: #2c3e50;
                    font-size: 15px;
                    font-weight: 500;
                }
                .room-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 16px;
                    background: white;
                    border-radius: 8px;
                    overflow: hidden;
                    border: 1px solid #e2e8f0;
                }
                .room-table th {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 16px 12px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 13px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .room-table td {
                    padding: 16px 12px;
                    border-bottom: 1px solid #e2e8f0;
                    color: #2c3e50;
                }
                .room-table tr:last-child td {
                    border-bottom: none;
                }
                .payment-summary {
                    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 28px;
                    margin-top: 24px;
                }
                .payment-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 12px;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #e2e8f0;
                }
                .payment-row:last-of-type {
                    border-bottom: none;
                    margin-bottom: 0;
                    padding-bottom: 0;
                }
                .payment-label {
                    color: #64748b;
                    font-weight: 500;
                }
                .payment-amount {
                    font-weight: 600;
                    color: #2c3e50;
                }
                .amount-paid {
                    color: #10b981 !important;
                    font-size: 18px;
                }
                .remaining-balance {
                    color: #ef4444 !important;
                }
                .total-amount {
                    color: #667eea !important;
                    font-size: 20px;
                    font-weight: 700;
                }
                .divider {
                    height: 1px;
                    background: linear-gradient(90deg, #e2e8f0 0%, #667eea 50%, #e2e8f0 100%);
                    margin: 24px 0;
                    border-radius: 1px;
                }
                .footer {
                    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .footer p {
                    margin-bottom: 10px;
                    opacity: 0.9;
                }
                .footer .important {
                    background: rgba(102, 126, 234, 0.1);
                    padding: 16px;
                    border-radius: 8px;
                    margin-top: 16px;
                    border-left: 3px solid #667eea;
                }
                @media only screen and (max-width: 600px) {
                    body {
                        padding: 10px;
                    }
                    .email-container {
                        margin: 0;
                        border-radius: 12px;
                    }
                    .content {
                        padding: 24px 20px;
                    }
                    .section {
                        padding: 20px;
                    }
                    .info-grid {
                        grid-template-columns: 1fr;
                    }
                    .booking-ref {
                        padding: 20px;
                    }
                    .booking-ref p {
                        font-size: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <div class='logo'>CASA ESTELA</div>
                    <h2>Booking Confirmation</h2>
                </div>
                
                <div class='content'>
                    <div class='booking-ref'>
                        <h3>Booking Reference</h3>
                        <p>{$bookingRef}</p>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>
                            <i>📅</i> Booking Details
                        </div>
                        <div class='info-grid'>
                            <div class='info-item'>
                                <div class='info-label'>Check-in Date</div>
                                <div class='info-value'>" . date('F j, Y', strtotime($checkIn)) . "</div>
                            </div>
                            <div class='info-item'>
                                <div class='info-label'>Check-out Date</div>
                                <div class='info-value'>" . date('F j, Y', strtotime($checkOut)) . "</div>
                            </div>
                            <div class='info-item'>
                                <div class='info-label'>Number of Nights</div>
                                <div class='info-value'>{$nights} " . ($nights == 1 ? 'Night' : 'Nights') . "</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>
                            <i>👥</i> Guest Information
                        </div>
                        <div style='margin-bottom: 16px;'>{$guestInfo}</div>
                        <div class='info-grid'>
                            <div class='info-item'>
                                <div class='info-label'>Contact Number</div>
                                <div class='info-value'>" . htmlspecialchars($bookingData['phone'] ?? '') . "</div>
                            </div>
                            <div class='info-item'>
                                <div class='info-label'>Email Address</div>
                                <div class='info-value'>" . htmlspecialchars($bookingData['email'] ?? '') . "</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>
                            <i>💳</i> Payment Information
                        </div>
                        <div class='info-grid'>
                            <div class='info-item'>
                                <div class='info-label'>Payment Option</div>
                                <div class='info-value'>" . ($paymentOptions[$paymentOption] ?? $paymentOption) . "</div>
                            </div>
                            <div class='info-item'>
                                <div class='info-label'>Payment Method</div>
                                <div class='info-value'>" . ($paymentMethodText[$paymentMethod] ?? $paymentMethod) . "</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>
                            <i>🏨</i> Room Details
                        </div>
                        <table class='room-table'>
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th style='text-align: center;'>Quantity</th>
                                    <th style='text-align: right;'>Price/Night</th>
                                    <th style='text-align: center;'>Nights</th>
                                    <th style='text-align: right;'>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$roomDetails}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class='payment-summary'>
                        <div class='section-title' style='border: none; padding: 0; margin: 0 0 20px 0;'>
                            <i>💰</i> Payment Summary
                        </div>
                        <div class='payment-row'>
                            <span class='payment-label'>Total Room Charges</span>
                            <span class='payment-amount'>{$currency}" . number_format($totalAmount, 2) . "</span>
                        </div>
                        <div class='payment-row'>
                            <span class='payment-label'>Amount Paid</span>
                            <span class='payment-amount amount-paid'>{$currency}" . number_format($amountPaid, 2) . "</span>
                        </div>
                        <div class='payment-row'>
                            <span class='payment-label'>Remaining Balance</span>
                            <span class='payment-amount remaining-balance'>{$currency}" . number_format($remainingBalance, 2) . "</span>
                        </div>";
                        
                        $htmlContent .= "
                        <div class='divider'></div>
                        <div class='payment-row'>
                            <span class='payment-label' style='font-size: 16px; font-weight: 600;'>Total Amount</span>
                            <span class='payment-amount total-amount'>{$currency}" . number_format($totalAmount, 2) . "</span>
                        </div>
                    </div>
                </div>
                
                <div class='footer'>
                    <p><strong>Thank you for choosing Casa Estela!</strong></p>
                    <p>Please keep this confirmation for your records. We look forward to welcoming you.</p>
                    <div class='important'>
                        <strong>Important:</strong> Please arrive at the hotel during check-in hours with a valid ID and this booking confirmation.
                    </div>
                </div>
            </div>
        </body>
        </html>";
        
        return $htmlContent;
    }
    
    private function createBookingConfirmationText($bookingData) {
        $currency = '₱';
        $bookingRef = $bookingData['booking_reference'];
        $checkIn = $bookingData['check_in'];
        $checkOut = $bookingData['check_out'];
        $nights = $bookingData['nights'];
        $totalAmount = $bookingData['total_amount'];
        $amountPaid = $bookingData['amount_paid'] ?? 0;
        $remainingBalance = $bookingData['remaining_balance'] ?? 0;
        $paymentOption = $bookingData['payment_option'] ?? '';
        $paymentMethod = $bookingData['payment_method'] ?? '';
        
        // Payment option display text
        $paymentOptions = [
            'down_payment' => 'Down Payment',
            'full_payment' => 'Full Payment',
            'custom_payment' => 'Custom Payment'
        ];
        
        $paymentMethodText = [
            'credit_card' => 'Credit/Debit Card',
            'gcash' => 'GCash',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer'
        ];
        
        $text = "BOOKING CONFIRMATION\n";
        $text .= "====================\n\n";
        $text .= "Booking Reference: {$bookingRef}\n\n";
        $text .= "Booking Details:\n";
        $text .= "Check-in: " . date('F j, Y', strtotime($checkIn)) . "\n";
        $text .= "Check-out: " . date('F j, Y', strtotime($checkOut)) . "\n";
        $text .= "Number of Nights: {$nights}\n\n";
        
        $text .= "Guest Information:\n";
        if (isset($bookingData['adults']) && is_array($bookingData['adults'])) {
            foreach ($bookingData['adults'] as $index => $adult) {
                $text .= "Adult " . ($index + 1) . ": " . $adult['firstName'] . ' ' . $adult['lastName'] . " (Age: " . $adult['age'] . ")\n";
            }
        }
        
        if (isset($bookingData['children']) && is_array($bookingData['children'])) {
            foreach ($bookingData['children'] as $index => $child) {
                $text .= "Child " . ($index + 1) . ": " . $child['firstName'] . ' ' . $child['lastName'] . " (Age: " . $child['age'] . ")\n";
            }
        }
        
        $text .= "Contact: " . ($bookingData['phone'] ?? '') . "\n";
        $text .= "Email: " . ($bookingData['email'] ?? '') . "\n\n";
        
        $text .= "Payment Information:\n";
        $text .= "Payment Option: " . ($paymentOptions[$paymentOption] ?? $paymentOption) . "\n";
        $text .= "Payment Method: " . ($paymentMethodText[$paymentMethod] ?? $paymentMethod) . "\n\n";
        
        $text .= "Payment Summary:\n";
        $text .= "Total Room Charges: {$currency}" . number_format($totalAmount, 2) . "\n";
        $text .= "Amount Paid: {$currency}" . number_format($amountPaid, 2) . "\n";
        
        if ($remainingBalance > 0) {
            $text .= "Remaining Balance: {$currency}" . number_format($remainingBalance, 2) . "\n";
        }
        
        $text .= "Total Amount: {$currency}" . number_format($totalAmount, 2) . "\n\n";
        $text .= "Thank you for your booking!\n";
        $text .= "Please arrive at the hotel during check-in hours with a valid ID.\n";
        
        return $text;
    }
}
?>
