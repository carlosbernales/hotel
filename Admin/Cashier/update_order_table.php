<?php
// Include database connection
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to browser

// Log any errors to PHP error log instead
function logError($message) {
    error_log("[Table Update Error] " . $message);
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Check if required parameters are present
    if (isset($_POST['order_id']) && isset($_POST['table_id']) && isset($_POST['table_number'])) {
        $orderId = intval($_POST['order_id']);
        $tableId = intval($_POST['table_id']);
        $tableNumber = intval($_POST['table_number']);
        
        // Log received data for debugging
        logError("Received data: order_id=$orderId, table_id=$tableId, table_number=$tableNumber");
        
        // Verify connection is active
        if (!$connection || $connection->connect_error) {
            throw new Exception("Database connection failed: " . ($connection ? $connection->connect_error : "No connection"));
        }
        
        // Begin transaction
        $connection->begin_transaction();
        
        // Update order with new table name
        $updateOrderQuery = "UPDATE orders SET table_name = ? WHERE id = ?";
        $stmt = $connection->prepare($updateOrderQuery);
        
        if (!$stmt) {
            throw new Exception("Prepare failed for order update: " . $connection->error);
        }
        
        $tableName = "Table " . $tableNumber;
        $stmt->bind_param("si", $tableName, $orderId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update order: " . $stmt->error);
        }
        
        // Close the statement before preparing a new one
        $stmt->close();
        
        // Update table status to occupied
        $updateTableQuery = "UPDATE table_number SET status = 'occupied', order_id = ?, occupied_at = NOW() WHERE id = ?";
        $stmt = $connection->prepare($updateTableQuery);
        
        if (!$stmt) {
            throw new Exception("Prepare failed for table update: " . $connection->error);
        }
        
        $stmt->bind_param("ii", $orderId, $tableId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update table status: " . $stmt->error);
        }
        
        // Close the statement
        $stmt->close();
        
        // Commit transaction
        $connection->commit();
        
        $response['success'] = true;
        $response['message'] = 'Table assigned successfully';
        $response['table_name'] = $tableName;
    } else {
        throw new Exception('Missing required parameters');
    }
} catch (Exception $e) {
    // Log the error
    logError($e->getMessage());
    
    // Rollback transaction on error if connection exists and is active
    if (isset($connection) && $connection && !$connection->connect_error) {
        try {
            $connection->rollback();
        } catch (Exception $rollbackEx) {
            logError("Rollback failed: " . $rollbackEx->getMessage());
        }
    }
    
    $response['success'] = false;
    $response['message'] = $e->getMessage();
} finally {
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    
    // Close connection if it exists and is active
    if (isset($connection) && $connection && !$connection->connect_error) {
        $connection->close();
    }
}
?>
