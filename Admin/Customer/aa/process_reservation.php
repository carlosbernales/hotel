<?php
require 'db_con.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance()->connect();
        
        // Generate booking reference number
        $reference = 'BK' . date('YmdHis') . rand(100, 999);
        
        // Calculate payment details
        $total_amount = $_POST['packagePrice'];
        $payment_type = $_POST['paymentType'];
        $amount_to_pay = ($payment_type === 'downpayment') ? $total_amount * 0.5 : $total_amount;
        
        // Prepare the SQL statement
        $sql = "INSERT INTO table_bookings (
            booking_id,
            customer_id,
            package_type,
            booking_date,
            booking_time,
            num_guests,
            payment_method,
            total_amount,
            amount_paid,
            payment_status,
            status,
            created_at
        ) VALUES (
            :booking_id,
            :customer_id,
            :package_type,
            :booking_date,
            :booking_time,
            :num_guests,
            :payment_method,
            :total_amount,
            :amount_paid,
            :payment_status,
            :status,
            NOW()
        )";
        
        $stmt = $db->prepare($sql);
        
        // Execute the statement with the form data
        $result = $stmt->execute([
            'booking_id' => $reference,
            'customer_id' => $_SESSION['user_id'],
            'package_type' => $_POST['packageName'],
            'booking_date' => $_POST['reservationDate'],
            'booking_time' => $_POST['reservationStartTime'],
            'num_guests' => $_POST['reservationCapacity'],
            'payment_method' => $_POST['paymentMethod'],
            'total_amount' => $total_amount,
            'amount_paid' => 0, // Initially 0 until payment is confirmed
            'payment_status' => 'Pending',
            'status' => 'Pending'
        ]);
        
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Booking successful!',
                'reference' => $reference
            ]);
        } else {
            throw new Exception("Failed to save booking");
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>