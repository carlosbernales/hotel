<?php
require_once 'session.php';
require_once 'db_con.php';

// Ensure no output before headers
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display errors, we'll handle them ourselves
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Set proper content type for JSON response
header('Content-Type: application/json');

// Log incoming data for debugging
error_log("Received POST data: " . print_r($_POST, true));
if (isset($_FILES)) {
    error_log("Received FILES data: " . print_r($_FILES, true));
}

function sendError($message, $code = 500) {
    error_log("Error in process_order.php: " . $message);
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

function sendSuccess($message, $data = []) {
    error_log("Success in process_order.php: " . $message);
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendError('Please log in to place an order', 401);
    }

    // Get user details from database
    $userQuery = "SELECT * FROM userss WHERE id = :user_id";
    $stmt = $pdo->prepare($userQuery);
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sendError('User information not found', 400);
    }

    // Validate order data
    if (!isset($_POST['order_data'])) {
        sendError('No order data received', 400);
    }

    // Clean and decode JSON data
    $orderDataString = $_POST['order_data'];
    $orderData = json_decode($orderDataString, true);

    if (!$orderData) {
        sendError('Invalid order data format', 400);
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Calculate payment amount based on payment option
        $totalAmount = floatval($orderData['final_total']);
        $paymentAmount = $orderData['payment_option'] === 'partial' ? ($totalAmount * 0.5) : $totalAmount;
        $changeAmount = $orderData['payment_option'] === 'partial' ? ($totalAmount * 0.5) : 0;
        $remainingBalance = $orderData['payment_option'] === 'partial' ? ($totalAmount * 0.5) : 0;
        
        // Set payment status based on payment method
        $paymentStatus = 'Pending';
        if (in_array($orderData['payment_method'], ['gcash', 'maya'])) {
            $paymentStatus = $orderData['payment_option'] === 'partial' ? 'Partially Paid' : 'Processing';
        }

        // Handle file upload for online payments
        $paymentProofPath = null;
        $referenceNumber = null;
        if (in_array($orderData['payment_method'], ['gcash', 'maya'])) {
            if (!isset($_POST['reference_number']) || !isset($_FILES['payment_proof'])) {
                throw new Exception('Payment proof and reference number required for online payments');
            }

            $referenceNumber = $_POST['reference_number'];
            $uploadDir = '../../uploads/payment_proofs/';
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Invalid file type. Only JPG and PNG files are allowed.');
            }

            $fileName = 'payment_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $paymentProofPath = $fileName;
            
            if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadDir . $fileName)) {
                throw new Exception('Failed to upload payment proof');
            }
        }

        // For advance orders (table reservations)
        if ($orderData['order_type'] === 'advance' && !empty($orderData['table_details'])) {
            // Insert table booking
            $bookingSql = "INSERT INTO table_bookings (
                user_id, package_name, contact_number, email_address,
                booking_date, booking_time, num_guests,
                payment_method, total_amount, amount_paid, downpayment_amount,
                payment_status, status, package_type, payment_reference,
                payment_proof, created_at, payment_option, change_amount
            ) VALUES (
                :user_id, :package_name, :contact_number, :email_address,
                :booking_date, :booking_time, :num_guests,
                :payment_method, :total_amount, :amount_paid, :downpayment_amount,
                :payment_status, :status, :package_type, :payment_reference,
                :payment_proof, NOW(), :payment_option, :change_amount
            )";

            $stmt = $pdo->prepare($bookingSql);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':package_name' => $orderData['table_details']['package_name'],
                ':contact_number' => $user['contact_number'],
                ':email_address' => $user['email'],
                ':booking_date' => $orderData['table_details']['date'],
                ':booking_time' => $orderData['table_details']['time'],
                ':num_guests' => $orderData['table_details']['guests'],
                ':payment_method' => $orderData['payment_method'],
                ':total_amount' => $totalAmount,
                ':amount_paid' => $paymentAmount,
                ':downpayment_amount' => $orderData['payment_option'] === 'partial' ? $paymentAmount : 0,
                ':payment_status' => $paymentStatus,
                ':status' => 'Pending',
                ':package_type' => 'Ultimate',
                ':payment_reference' => $referenceNumber,
                ':payment_proof' => $paymentProofPath,
                ':payment_option' => $orderData['payment_option'],
                ':change_amount' => $changeAmount
            ]);

            $bookingId = $pdo->lastInsertId();
        }

        // Insert order with corrected column names
        $orderSql = "INSERT INTO orders (
            user_id, table_id, customer_name, contact_number, nickname,
            total_amount, amount_paid, change_amount, extra_fee,
            order_type, payment_method, payment_reference,
            payment_status, status, final_total, order_date,
            payment_proof, id_number, pickup_notes, remaining_balance
        ) VALUES (
            :user_id, :table_id, :customer_name, :contact_number, :nickname,
            :total_amount, :amount_paid, :change_amount, :extra_fee,
            :order_type, :payment_method, :payment_reference,
            :payment_status, :status, :final_total, NOW(),
            :payment_proof, :id_number, :pickup_notes, :remaining_balance
        )";

        $customerName = $user['first_name'] . ' ' . $user['last_name'];
        
        $stmt = $pdo->prepare($orderSql);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':table_id' => $bookingId ?? null,
            ':customer_name' => $customerName,
            ':contact_number' => $user['contact_number'],
            ':nickname' => $user['nickname'] ?? $user['first_name'],
            ':total_amount' => $orderData['total_amount'],
            ':amount_paid' => $paymentAmount,
            ':change_amount' => $changeAmount,
            ':extra_fee' => $orderData['extra_fee'] ?? 0,
            ':order_type' => $orderData['order_type'],
            ':payment_method' => $orderData['payment_method'],
            ':payment_reference' => $referenceNumber,
            ':payment_status' => $paymentStatus,
            ':status' => 'Pending',
            ':final_total' => $totalAmount,
            ':payment_proof' => $paymentProofPath,
            ':id_number' => 'ORD' . uniqid(),
            ':pickup_notes' => $orderData['special_requests'] ?? null,
            ':remaining_balance' => $remainingBalance
        ]);

        $orderId = $pdo->lastInsertId();

        // Insert order items
        foreach ($orderData['items'] as $item) {
            $itemSql = "INSERT INTO order_items (
                order_id, item_name, quantity, unit_price
            ) VALUES (
                :order_id, :item_name, :quantity, :unit_price
            )";

            $stmt = $pdo->prepare($itemSql);
            $stmt->execute([
                    ':order_id' => $orderId,
                    ':item_name' => $item['name'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['price']
            ]);

                $orderItemId = $pdo->lastInsertId();

                // Insert addons if any
                if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addon) {
                    $addonSql = "INSERT INTO order_item_addons (
                        order_item_id, addon_name, addon_price
                    ) VALUES (
                        :order_item_id, :addon_name, :addon_price
                    )";

                    $stmt = $pdo->prepare($addonSql);
                    $stmt->execute([
                            ':order_item_id' => $orderItemId,
                            ':addon_name' => $addon['name'],
                        ':addon_price' => $addon['price']
                    ]);
                }
            }
        }

        // Commit transaction
        $pdo->commit();
        
        // Send success response
        sendSuccess('Order placed successfully!', [
            'order_id' => $orderId,
            'booking_id' => $bookingId ?? null
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Delete uploaded file if exists
        if ($paymentProofPath && file_exists('D:/casaaa/htdocs/Admin/uploads/payment_proofs/' . $paymentProofPath)) {
            unlink('D:/casaaa/htdocs/Admin/uploads/payment_proofs/' . $paymentProofPath);
        }
        
        throw $e;
    }

} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    sendError($e->getMessage());
} finally {
    // Clean any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}
?>