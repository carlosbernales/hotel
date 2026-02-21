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
                    background-color: #f8f9fa;
                    color: #495057;
                    line-height: 1.6;
                }
                .email-container {
                    max-width: 650px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #b07d06 0%, #d59a07 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                    position: relative;
                }
                .header::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"1\" fill=\"rgba(255,255,255,0.1)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');
                    opacity: 0.3;
                }
                .logo {
                    font-size: 32px;
                    font-weight: 700;
                    margin-bottom: 10px;
                    position: relative;
                    z-index: 1;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                }
                .header h2 {
                    font-size: 24px;
                    font-weight: 400;
                    margin: 0;
                    position: relative;
                    z-index: 1;
                    opacity: 0.95;
                }
                .content {
                    padding: 40px 30px;
                }
                .booking-ref {
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    border-left: 4px solid #b07d06;
                    padding: 20px 25px;
                    margin-bottom: 30px;
                    border-radius: 0 8px 8px 0;
                }
                .booking-ref h3 {
                    color: #b07d06;
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 5px;
                }
                .booking-ref p {
                    font-size: 24px;
                    font-weight: 700;
                    color: #495057;
                    margin: 0;
                }
                .section {
                    background-color: #f8f9fa;
                    border-radius: 12px;
                    padding: 25px;
                    margin-bottom: 25px;
                    border: 1px solid #e9ecef;
                }
                .section-title {
                    color: #b07d06;
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #e9ecef;
                    display: flex;
                    align-items: center;
                }
                .section-title i {
                    margin-right: 10px;
                    font-size: 16px;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 15px;
                }
                .info-item {
                    margin-bottom: 12px;
                }
                .info-label {
                    color: #6c757d;
                    font-size: 13px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 4px;
                    font-weight: 600;
                }
                .info-value {
                    color: #495057;
                    font-size: 15px;
                    font-weight: 500;
                }
                .room-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                    background: white;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                }
                .room-table th {
                    background: linear-gradient(135deg, #b07d06 0%, #d59a07 100%);
                    color: white;
                    padding: 15px 12px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .room-table td {
                    padding: 12px;
                    border-bottom: 1px solid #e9ecef;
                    color: #495057;
                }
                .room-table tr:last-child td {
                    border-bottom: none;
                }
                .payment-summary {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    border: 2px solid #e9ecef;
                    border-radius: 12px;
                    padding: 25px;
                    margin-top: 25px;
                }
                .payment-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 12px;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #e9ecef;
                }
                .payment-row:last-of-type {
                    border-bottom: none;
                    margin-bottom: 0;
                    padding-bottom: 0;
                }
                .payment-label {
                    color: #6c757d;
                    font-weight: 500;
                }
                .payment-amount {
                    font-weight: 600;
                    color: #495057;
                }
                .amount-paid {
                    color: #28a745 !important;
                    font-size: 18px;
                }
                .remaining-balance {
                    color: #dc3545 !important;
                }
                .total-amount {
                    color: #b07d06 !important;
                    font-size: 20px;
                    font-weight: 700;
                }
                .divider {
                    height: 2px;
                    background: linear-gradient(90deg, #e9ecef 0%, #b07d06 50%, #e9ecef 100%);
                    margin: 20px 0;
                    border-radius: 1px;
                }
                .footer {
                    background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .footer p {
                    margin-bottom: 10px;
                    opacity: 0.9;
                }
                .footer .important {
                    background: rgba(255,255,255,0.1);
                    padding: 15px;
                    border-radius: 8px;
                    margin-top: 15px;
                    border-left: 3px solid #b07d06;
                }
                @media only screen and (max-width: 600px) {
                    .email-container {
                        margin: 0;
                        border-radius: 0;
                    }
                    .content {
                        padding: 25px 20px;
                    }
                    .section {
                        padding: 20px;
                    }
                    .info-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <div class='logo'>CASA ESTELA BOTIQUE HOTEL & CAFE </div>
                    <h2>Booking Information</h2>
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
                        <div style='margin-bottom: 15px;'>{$guestInfo}</div>
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
                    <p><strong>Thank you for choosing our hotel!</strong></p>
                    <p>Please keep this confirmation for your records. We look forward to welcoming you.</p>
                    <div class='important'>
                        <strong>Important:</strong> Please arrive at the hotel during check-in hours with a valid ID and this booking confirmation.
                    </div>
                </div>
            </div>
        </body>
        </html>";
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
