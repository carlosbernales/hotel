<?php
// Include database connection
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check if request has table_id and status
if (isset($_POST['table_id']) && isset($_POST['status'])) {
    $table_id = $_POST['table_id'];
    $status = $_POST['status'];
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
    
    // Validate status
    if ($status !== 'available' && $status !== 'occupied') {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    
    try {
        // Start transaction
        $con->begin_transaction();
        
        if ($status === 'occupied') {
            // If marking as occupied with an order ID
            if ($order_id) {
                // Update table status with order ID
                $sql = "UPDATE table_number SET status = ?, occupied_at = ?, order_id = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                $occupied_at = date('Y-m-d H:i:s');
                $stmt->bind_param("ssii", $status, $occupied_at, $order_id, $table_id);
            } else {
                // Just mark as occupied without order ID
                $sql = "UPDATE table_number SET status = ?, occupied_at = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                $occupied_at = date('Y-m-d H:i:s');
                $stmt->bind_param("ssi", $status, $occupied_at, $table_id);
            }
        } else {
            // If marking as available, clear the order_id as well
            $sql = "UPDATE table_number SET status = ?, occupied_at = NULL, order_id = NULL WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("si", $status, $table_id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating table: " . $stmt->error);
        }
        
        $stmt->close();
        
        // Commit transaction
        $con->commit();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
}

$con->close();
?>
