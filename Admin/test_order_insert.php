<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Set values for a test order
$customerName = "Test Customer";
$contactNumber = "12345678";
$userId = 1; // Admin user ID
$tableId = 21; // The booking ID from your screenshot
$orderDate = date('Y-m-d H:i:s');
$orderStatus = "pending";
$paymentMethod = "cash";
$totalAmount = 281.00; // From your screenshot
$amountPaid = 281.00;
$changeAmount = 0;
$orderType = "walk-in";
$referenceNumber = 'TB21-' . date('Ymd');

// Execute a direct SQL query to insert the order
$directSql = "INSERT INTO orders (
    customer_name, contact_number, user_id, table_id,
    order_date, status, payment_method, total_amount,
    amount_paid, change_amount, order_type, reference_number
) VALUES (
    '$customerName', '$contactNumber', $userId, $tableId,
    '$orderDate', '$orderStatus', '$paymentMethod', $totalAmount,
    $amountPaid, $changeAmount, '$orderType', '$referenceNumber'
)";

echo "<h2>Attempting to insert a test order</h2>";
echo "<p>SQL Query:</p>";
echo "<pre>" . htmlspecialchars($directSql) . "</pre>";

$result = $con->query($directSql);

if ($result) {
    $orderId = $con->insert_id;
    echo "<p style='color: green;'>SUCCESS! Order inserted with ID: $orderId</p>";
    
    // Insert a test order item
    $itemName = "TRUFFLE PARMESAN POTATO CHIPS";
    $menuItemId = 1; // Assuming a menu item ID
    $quantity = 1;
    $price = 281.00;
    
    $itemSql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price, item_name)
                VALUES ($orderId, $menuItemId, $quantity, $price, '$itemName')";
    
    $itemResult = $con->query($itemSql);
    
    if ($itemResult) {
        echo "<p style='color: green;'>Order item also inserted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Failed to insert order item: " . $con->error . "</p>";
    }
    
    // Verify the order exists
    $checkSql = "SELECT * FROM orders WHERE id = $orderId";
    $checkResult = $con->query($checkSql);
    
    if ($checkResult && $checkResult->num_rows > 0) {
        $order = $checkResult->fetch_assoc();
        echo "<h3>Order verification successful:</h3>";
        echo "<table border='1'>";
        foreach ($order as $key => $value) {
            echo "<tr><td>$key</td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Order verification failed - could not find the order after insertion!</p>";
    }
    
} else {
    echo "<p style='color: red;'>ERROR! Failed to insert order: " . $con->error . "</p>";
    
    // Check if the orders table exists and show its structure
    echo "<h3>Checking database structure:</h3>";
    
    $tablesCheck = $con->query("SHOW TABLES LIKE 'orders'");
    if ($tablesCheck->num_rows > 0) {
        echo "<p>The 'orders' table exists.</p>";
        
        // Check table structure
        $columnsCheck = $con->query("DESCRIBE orders");
        echo "<p>Table structure:</p>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($column = $columnsCheck->fetch_assoc()) {
            echo "<tr>";
            foreach ($column as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>The 'orders' table does not exist!</p>";
    }
} 