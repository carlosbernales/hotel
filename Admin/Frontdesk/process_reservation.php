<?php
// Prevent any unwanted output
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Ensure clean output buffer
ob_start();

require_once "db.php";

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$table_type = mysqli_real_escape_string($con, $_POST['table_type']);
$customer_name = mysqli_real_escape_string($con, $_POST['customer_name']);
$contact_number = mysqli_real_escape_string($con, $_POST['contact_number']);
$email = isset($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : null;
$reservation_date = mysqli_real_escape_string($con, $_POST['reservation_date']);
$arrival_time = mysqli_real_escape_string($con, $_POST['arrival_time']);
$special_requests = isset($_POST['special_requests']) ? mysqli_real_escape_string($con, $_POST['special_requests']) : null;
$advance_order = isset($_POST['advance_order']) ? $_POST['advance_order'] : null;

try {
    // Start transaction
    mysqli_begin_transaction($con);

    // Generate booking ID (format: CEYYYYMMDDXXXX)
    $date = date('Ymd');
    $query = "SELECT MAX(CAST(SUBSTRING(booking_id, 11) AS UNSIGNED)) as max_id 
              FROM table_reservations 
              WHERE booking_id LIKE 'CE{$date}%'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $next_id = str_pad(($row['max_id'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    $booking_id = "CE{$date}{$next_id}";

    // Insert reservation
    $sql = "INSERT INTO table_reservations (
                booking_id, table_type, customer_name, contact_number, 
                email, reservation_date, arrival_time, special_requests, 
                status, created_at
            ) VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, ?,
                'Pending', NOW()
            )";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssss',
        $booking_id, $table_type, $customer_name, $contact_number,
        $email, $reservation_date, $arrival_time, $special_requests
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to save reservation: " . mysqli_error($con));
    }

    $reservation_id = mysqli_insert_id($con);

    // Process advance order if exists
    if ($advance_order) {
        $order_data = json_decode($advance_order, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid advance order data: " . json_last_error_msg());
        }
        
        // Insert order details
        $sql = "INSERT INTO advance_orders (
                    reservation_id, payment_type, payment_method, 
                    total_amount, amount_to_pay, status, created_at
                ) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";

        $stmt = mysqli_prepare($con, $sql);
        $total_amount = str_replace(['₱', ','], '', $order_data['totalAmount']);
        $amount_to_pay = str_replace(['₱', ','], '', $order_data['amountToPay']);
        
        mysqli_stmt_bind_param($stmt, 'issdd',
            $reservation_id,
            $order_data['paymentType'],
            $order_data['paymentMethod'],
            $total_amount,
            $amount_to_pay
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to save advance order: " . mysqli_error($con));
        }

        $order_id = mysqli_insert_id($con);

        // Insert order items
        $sql = "INSERT INTO order_items (
                    order_id, item_id, item_name, 
                    quantity, price, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $sql);

        foreach ($order_data['items'] as $item) {
            $subtotal = $item['price'] * ($item['quantity'] ?? 1);
            mysqli_stmt_bind_param($stmt, 'iisidi',
                $order_id,
                $item['id'],
                $item['name'],
                $item['quantity'] ?? 1,
                $item['price'],
                $subtotal
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to save order items: " . mysqli_error($con));
            }
        }
    }

    // Commit transaction
    mysqli_commit($con);

    // Clean output buffer and send JSON response
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'booking_id' => $booking_id,
        'message' => 'Reservation successfully processed'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    
    // Clean output buffer and send JSON response
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close database connection
mysqli_close($con); 