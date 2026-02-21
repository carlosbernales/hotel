<?php
session_start();
require 'db_con.php';

header('Content-Type: application/json');


try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $requiredFields = ['tables', 'reservationDate', 'reservationTime'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate tables data
    if (!is_array($data['tables']) || empty($data['tables'])) {
        throw new Exception('No tables selected for booking');
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // 1. Insert into orders_table
        $orderNumber = 'TB-' . time() . '-' . rand(1000, 9999);
        // Combine reservation date and time from form data
        $reservationDateTime = $data['reservationDate'] . ' ' . $data['reservationTime'] . ':00';
        $userId = $_SESSION['user_id'];
        
        // Get user details
        $userStmt = $pdo->prepare("SELECT first_name, last_name, contact_number, email FROM userss WHERE id = ?");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('User not found');
        }

        // Insert the booking
        $orderInsert = $pdo->prepare("
            INSERT INTO orders_table (
                user_id, order_id, order_type, firstname, lastname, 
                contact, email, date_time, status
            ) VALUES (
                :user_id, :order_id, :order_type, :firstname, :lastname, 
                :contact, :email, :date_time, 'confirmed'
            )
        ");

        $orderInsert->execute([
            ':user_id' => $userId,
            ':order_id' => $orderNumber,
            ':order_type' => 'Table Booking',
            ':firstname' => $user['first_name'],
            ':lastname' => $user['last_name'],
            ':contact' => $user['contact_number'],
            ':email' => $user['email'],
            ':date_time' => $reservationDateTime
        ]);

        $orderId = $pdo->lastInsertId();

        // 2. Process each table type in the cart
        $tableTypeInsert = $pdo->prepare("
            INSERT INTO orders_table_type (
                table_booking_fk_id, 
                table_number_fk_id, 
                table_type_fk_id,
                table_name,
                table_number
            ) VALUES (
                :order_id,
                :table_id,
                :table_type_id,
                :table_name,
                :table_number
            )
        ");

        // Array to store all table IDs that need to be updated
        $tableIdsToUpdate = [];
        $reservationDate = $data['reservationDate'];
        
        // Validate reservation date is not in the past
        $currentDateTime = new DateTime();
        $reservationDateTimeObj = new DateTime($reservationDateTime);
        if ($reservationDateTimeObj < $currentDateTime) {
            throw new Exception('Reservation date and time cannot be in the past');
        }
        
        // Process each table type in the cart
        foreach ($data['tables'] as $tableItem) {
            $tableTypeId = $tableItem['packageId'];
            $quantity = $tableItem['quantity'];
            
                // Get available tables of the selected type
                $tableStmt = $pdo->prepare("
                    SELECT tn.id, tt.table_name, tn.table_number 
                    FROM table_number tn
                    JOIN table_types tt ON tn.table_type_fk_id = tt.id
                    WHERE tn.table_type_fk_id = :type_id 
                    AND tn.status = 'available'
                    AND tn.id NOT IN (
                        SELECT ott.table_number_fk_id 
                        FROM orders_table ot
                    JOIN orders_table_type ott ON ot.id = ott.table_booking_fk_id
                    WHERE DATE(ot.date_time) = :date_time
                    AND ot.status = 'confirmed'
                )
                LIMIT :quantity
                FOR UPDATE
            ");
            
            $tableStmt->bindValue(':type_id', $tableTypeId, PDO::PARAM_INT);
            $tableStmt->bindValue(':date_time', $reservationDate, PDO::PARAM_STR);
            $tableStmt->bindValue(':quantity', (int)$quantity, PDO::PARAM_INT);
            $tableStmt->execute();
            
            $tables = $tableStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check if we have enough tables available
            if (count($tables) < $quantity) {
                throw new Exception('Not enough available tables of the selected type for the selected date');
            }
            
            // Insert each table into orders_table_type
            foreach ($tables as $table) {
                $tableTypeInsert->execute([
                    ':order_id' => $orderId,
                    ':table_id' => $table['id'],
                    ':table_type_id' => $tableTypeId, 
                    ':table_name' => $table['table_name'],
                    ':table_number' => $table['table_number']
                ]);
                
                // Add to list of tables to update
                $tableIdsToUpdate[] = $table['id'];
            }
        }
        
        // Update status of all reserved tables to 'occupied'
        if (!empty($tableIdsToUpdate)) {
            $placeholders = rtrim(str_repeat('?,', count($tableIdsToUpdate)), ',');
            $updateTables = $pdo->prepare("
                UPDATE table_number 
                SET status = 'available' 
                WHERE id IN ($placeholders)
            ");
            $updateTables->execute($tableIdsToUpdate);
        }

        // Commit transaction
        $pdo->commit();

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Table booked successfully!',
            'booking_reference' => $orderNumber,
            'booking_id' => $orderId
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
