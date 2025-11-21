<?php
session_start();
require 'db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get user ID from session
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not logged in');
        }
        $userId = $_SESSION['user_id'];
        
        // Validate required fields
        $requiredFields = ['package_id', 'package_name', 'date', 'guest_count', 'arrival_time'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Get form data
        $packageId = $_POST['package_id'];
        $packageName = $_POST['package_name'];
        $date = $_POST['date'];
        $guestCount = $_POST['guest_count'];
        $arrivalTime = $_POST['arrival_time'];
        $duration = $_POST['duration'] ?? '4';
        $isUltimate = $_POST['is_ultimate'] ?? '0';

        // Validate package exists
        $packageQuery = "SELECT * FROM table_packages WHERE id = ?";
        $packageStmt = $pdo->prepare($packageQuery);
        $packageStmt->execute([$packageId]);
        $package = $packageStmt->fetch();
        
        if (!$package) {
            throw new Exception('Invalid package selected');
        }

        // Start transaction
        $pdo->beginTransaction();

        // Get user contact info
        $userQuery = "SELECT contact_number, email FROM userss WHERE id = ?";
        $userStmt = $pdo->prepare($userQuery);
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();
        
        if (!$user) {
            throw new Exception('User information not found');
        }

        // Calculate total amount
        $basePrice = $_POST['base_price'] ?? $package['price'];
        $totalAmount = $basePrice;

        // Add extra charges for ultimate packages
        if ($isUltimate == "1") {
            // Extra hours cost
            $extraHours = max(0, intval($duration) - 4);
            $extraHoursCost = $extraHours * 2000;

            // Extra guests cost
            $capacity = $package['capacity'];
            $extraGuests = max(0, intval($guestCount) - $capacity);
            $extraGuestsCost = $extraGuests * 1000;

            $totalAmount += $extraHoursCost + $extraGuestsCost;
        }

        // Set amount paid based on payment option
        $amountPaid = $totalAmount;
        if ($isUltimate == "1" && isset($_POST['payment_option']) && $_POST['payment_option'] == 'partial') {
            $amountPaid = $totalAmount * 0.5;
        }

        // Handle payment proof upload
        $paymentProofPath = null;
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
            $uploadDir = 'uploads/payment_proofs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('payment_') . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetPath)) {
                $paymentProofPath = $targetPath;
            } else {
                throw new Exception('Failed to upload payment proof');
            }
        }

        // Insert into table_bookings
        $bookingQuery = "INSERT INTO table_bookings (
            user_id,
            package_name,
            contact_number,
            email_address,
            booking_date,
            booking_time,
            num_guests,
            special_requests,
            payment_method,
            total_amount,
            amount_paid,
            change_amount,
            payment_status,
            status,
            package_type,
            payment_reference,
            payment_proof,
            created_at
        ) VALUES (
            :user_id,
            :package_name,
            :contact_number,
            :email_address,
            :booking_date,
            :booking_time,
            :num_guests,
            :special_requests,
            :payment_method,
            :total_amount,
            :amount_paid,
            :change_amount,
            :payment_status,
            :status,
            :package_type,
            :payment_reference,
            :payment_proof,
            CURRENT_TIMESTAMP
        )";

        $stmt = $pdo->prepare($bookingQuery);
        $success = $stmt->execute([
            ':user_id' => $userId,
            ':package_name' => $packageName,
            ':contact_number' => $user['contact_number'],
            ':email_address' => $user['email'],
            ':booking_date' => $date,
            ':booking_time' => $arrivalTime,
            ':num_guests' => $guestCount,
            ':special_requests' => $_POST['special_requests'] ?? null,
            ':payment_method' => $_POST['payment_method'] ?? null,
            ':total_amount' => $totalAmount,
            ':amount_paid' => $amountPaid,
            ':change_amount' => 0,
            ':payment_status' => $isUltimate == "1" ? ($_POST['payment_option'] == 'full' ? 'Paid' : 'Partially Paid') : 'Pending',
            ':status' => 'Pending',
            ':package_type' => $isUltimate == "1" ? 'Ultimate' : 'Regular',
            ':payment_reference' => $_POST['reference_number'] ?? null,
            ':payment_proof' => $paymentProofPath
        ]);

        if (!$success) {
            throw new Exception('Failed to create booking');
        }

        // Commit transaction
        $pdo->commit();

        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Booking created successfully']);

    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Log the error
        error_log("Booking Error: " . $e->getMessage());
        
        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}

function getAdvanceOrders($bookingId) {
    $db = Database::getInstance()->connect();
    
    $query = "SELECT 
        ao.*, 
        mi.name as item_name,
        GROUP_CONCAT(
            CONCAT(ma.name, ':', aoa.price)
            SEPARATOR '|'
        ) as addons
    FROM advance_orders ao
    JOIN menu_items mi ON ao.menu_item_id = mi.id
    LEFT JOIN advance_order_addons aoa ON ao.id = aoa.advance_order_id
    LEFT JOIN menu_item_addons ma ON aoa.addon_id = ma.id
    WHERE ao.booking_id = ?
    GROUP BY ao.id";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$bookingId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?> 