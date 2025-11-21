<?php
require 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug: Log the raw POST data
        error_log("Raw POST data: " . print_r($_POST, true));
        
        // Get form data
        $firstName = isset($_POST['guest_first_name']) ? $_POST['guest_first_name'] : '';
        $lastName = isset($_POST['guest_last_name']) ? $_POST['guest_last_name'] : '';
        $guestName = $firstName . ' ' . $lastName;
        $contact = isset($_POST['guest_contact_number']) ? $_POST['guest_contact_number'] : '';
        $email = isset($_POST['guest_email']) ? $_POST['guest_email'] : '';
        $checkIn = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : '';
        $checkOut = isset($_POST['departure_date']) ? $_POST['departure_date'] : '';
        $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
        $paymentOption = isset($_POST['payment_option']) ? $_POST['payment_option'] : '';
        
        // Get discount information
        $discountType = isset($_POST['discount_type']) ? $_POST['discount_type'] : '';
        $discountAmount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0;
        $originalTotal = isset($_POST['original_total']) ? floatval($_POST['original_total']) : 0;
        $finalTotal = isset($_POST['final_total']) ? floatval($_POST['final_total']) : 0;
        
        // Force calculation of discount if not provided but we have discount type
        if ($discountType && $discountAmount <= 0 && $originalTotal > 0) {
            // Calculate 10% discount
            $discountAmount = $originalTotal * 0.1;
            $finalTotal = $originalTotal - $discountAmount;
            
            error_log("Calculated discount amount: {$discountAmount} from original total: {$originalTotal}");
        }
        
        // Debug discount information
        error_log("Discount Type: " . $discountType);
        error_log("Discount Amount: " . $discountAmount);
        error_log("Original Total: " . $originalTotal);
        error_log("Final Total: " . $finalTotal);
        
        // Debug: Log the rooms data
        error_log("Rooms data: " . (isset($_POST['rooms']) ? $_POST['rooms'] : 'No rooms data'));
        
        $rooms = isset($_POST['rooms']) ? json_decode($_POST['rooms'], true) : null;

        if (!$rooms) {
            throw new Exception("No rooms selected");
        }

        if (empty($checkIn) || empty($checkOut)) {
            throw new Exception("Please select check-in and check-out dates");
        }

        // Calculate nights
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $nights = $checkOutDate->diff($checkInDate)->days;

        if ($nights <= 0) {
            throw new Exception("Invalid date range");
        }

        // Calculate total amount if not provided
        if ($originalTotal <= 0) {
            $originalTotal = 0;
            foreach ($rooms as $room) {
                $price = floatval($room['price']);
                $originalTotal += ($price * $nights);
            }
            $finalTotal = $originalTotal; // Set final total to original if no discount
        }

        // Format dates
        $formattedCheckIn = $checkInDate->format('M d, Y');
        $formattedCheckOut = $checkOutDate->format('M d, Y');

        // Prepare response data
        $data = [
            'guest' => [
                'name' => $guestName,
                'contact' => $contact,
                'email' => $email
            ],
            'booking' => [
                'checkIn' => $formattedCheckIn,
                'checkOut' => $formattedCheckOut,
                'nights' => $nights,
                'paymentMethod' => ucfirst($paymentMethod),
                'paymentOption' => ucfirst($paymentOption)
            ],
            'rooms' => array_map(function($room) {
                return [
                    'type' => $room['type'],
                    'beds' => $room['beds'],
                    'price' => floatval($room['price'])
                ];
            }, $rooms),
            'totalAmount' => $finalTotal,
            'originalAmount' => $originalTotal
        ];
        
        // Add discount information if applicable
        if (!empty($discountType) && $discountAmount > 0) {
            $data['discount'] = [
                'type' => $discountType,
                'amount' => $discountAmount
            ];
            
            // Log that discount was included
            error_log("Discount included in response: Type = {$discountType}, Amount = {$discountAmount}");
        }

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } catch (Exception $e) {
        error_log("Error in process_booking_summary.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
