<?php
// Include database connection
require_once 'db.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Enable error reporting for diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'order_items_fix.log');

// Function to sanitize output
function h($str) {
    if ($str === null) {
        return '';
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

echo "<h1>Order Items Table Structure Check</h1>";

// Check order_items table structure
$structureResult = $con->query("DESCRIBE `order_items`");
if ($structureResult) {
    echo "<h2>Current Order Items Table Structure:</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $structureResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . h($row['Field']) . "</td>";
        echo "<td>" . h($row['Type']) . "</td>";
        echo "<td>" . h($row['Null']) . "</td>";
        echo "<td>" . h($row['Key']) . "</td>";
        echo "<td>" . h($row['Default']) . "</td>";
        echo "<td>" . h($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>Error checking order_items table structure: " . $con->error . "</p>";
}

// Check if price and quantity columns exist
$hasPriceColumn = false;
$hasQuantityColumn = false;
$structureResult = $con->query("DESCRIBE `order_items`");
if ($structureResult) {
    while ($row = $structureResult->fetch_assoc()) {
        if ($row['Field'] == 'price') {
            $hasPriceColumn = true;
        }
        if ($row['Field'] == 'quantity') {
            $hasQuantityColumn = true;
        }
    }
}

echo "<h2>Missing Columns Check:</h2>";
echo "<ul>";
echo "<li>Price column: " . ($hasPriceColumn ? "Exists" : "Missing") . "</li>";
echo "<li>Quantity column: " . ($hasQuantityColumn ? "Exists" : "Missing") . "</li>";
echo "</ul>";

// Add missing columns if necessary
if (!$hasPriceColumn || !$hasQuantityColumn) {
    echo "<h2>Adding Missing Columns:</h2>";
    
    if (!$hasPriceColumn) {
        $sql = "ALTER TABLE `order_items` ADD COLUMN `price` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `item_name`";
        if ($con->query($sql)) {
            echo "<p class='success'>Successfully added price column</p>";
        } else {
            echo "<p class='error'>Error adding price column: " . $con->error . "</p>";
        }
    }
    
    if (!$hasQuantityColumn) {
        $sql = "ALTER TABLE `order_items` ADD COLUMN `quantity` INT NOT NULL DEFAULT 1 AFTER `price`";
        if ($con->query($sql)) {
            echo "<p class='success'>Successfully added quantity column</p>";
        } else {
            echo "<p class='error'>Error adding quantity column: " . $con->error . "</p>";
        }
    }
}

// Check for recent orders without price or quantity
$orderItemsQuery = "SELECT oi.*, o.table_id FROM order_items oi 
                   LEFT JOIN orders o ON oi.order_id = o.id 
                   WHERE (oi.price = 0 OR oi.quantity = 0)
                   AND o.table_id IS NOT NULL
                   ORDER BY oi.order_id DESC
                   LIMIT 20";

$orderItemsResult = $con->query($orderItemsQuery);

if ($orderItemsResult && $orderItemsResult->num_rows > 0) {
    echo "<h2>Recent Order Items Missing Price/Quantity:</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Order ID</th><th>Table ID</th><th>Item Name</th><th>Price</th><th>Quantity</th><th>Action</th></tr>";
    
    while ($item = $orderItemsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . h($item['id']) . "</td>";
        echo "<td>" . h($item['order_id']) . "</td>";
        echo "<td>" . h($item['table_id']) . "</td>";
        echo "<td>" . h($item['item_name']) . "</td>";
        echo "<td>" . h($item['price']) . "</td>";
        echo "<td>" . h($item['quantity']) . "</td>";
        echo "<td>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='item_id' value='" . h($item['id']) . "'>";
        echo "<input type='text' name='price' placeholder='Price' size='5' value='" . h($item['price']) . "'>";
        echo "<input type='text' name='quantity' placeholder='Qty' size='3' value='" . h($item['quantity']) . "'>";
        echo "<input type='submit' name='update_item' value='Update'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No order items found with missing price or quantity.</p>";
}

// Handle updates if submitted
if (isset($_POST['update_item'])) {
    $itemId = intval($_POST['item_id']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    
    $updateSql = "UPDATE order_items SET price = ?, quantity = ? WHERE id = ?";
    $stmt = $con->prepare($updateSql);
    $stmt->bind_param("dii", $price, $quantity, $itemId);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Successfully updated item #" . $itemId . "</p>";
        echo "<script>window.location.reload();</script>";
    } else {
        echo "<p class='error'>Error updating item: " . $stmt->error . "</p>";
    }
}

echo "<h2>Test Order Item Insertion</h2>";
echo "<form method='post'>";
echo "<input type='hidden' name='create_test_order' value='1'>";
echo "<p>This will create a test order with sample items to verify the order items functionality.</p>";
echo "<input type='submit' value='Create Test Order' class='button'>";
echo "</form>";

// Handle test order creation
if (isset($_POST['create_test_order'])) {
    // Start transaction
    $con->begin_transaction();
    
    try {
        // Create a test order
        $customerName = "Test Customer";
        $contactNumber = "123456789";
        $userId = $_SESSION['user_id'];
        $currentDate = date('Y-m-d H:i:s');
        
        $orderSql = "INSERT INTO orders (customer_name, contact_number, user_id, order_date, status, payment_method, total_amount, amount_paid, change_amount, order_type) 
                    VALUES (?, ?, ?, ?, 'pending', 'cash', 500, 500, 0, 'advance')";
        
        $stmt = $con->prepare($orderSql);
        $stmt->bind_param("ssis", $customerName, $contactNumber, $userId, $currentDate);
        
        if ($stmt->execute()) {
            $orderId = $con->insert_id;
            echo "<p class='success'>Created test order #" . $orderId . "</p>";
            
            // Add test items
            $testItems = [
                ['name' => 'Test Item 1', 'price' => 100.00, 'quantity' => 1],
                ['name' => 'Test Item 2', 'price' => 200.00, 'quantity' => 2]
            ];
            
            $itemSql = "INSERT INTO order_items (order_id, item_name, price, quantity) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($itemSql);
            
            foreach ($testItems as $item) {
                $stmt->bind_param("isdi", $orderId, $item['name'], $item['price'], $item['quantity']);
                
                if ($stmt->execute()) {
                    echo "<p class='success'>Added test item: " . $item['name'] . "</p>";
                } else {
                    throw new Exception("Error adding test item: " . $stmt->error);
                }
            }
            
            // Commit transaction
            $con->commit();
            echo "<p class='success'>Test order created successfully!</p>";
            
            // Verify the items were added
            $verifySql = "SELECT * FROM order_items WHERE order_id = ?";
            $stmt = $con->prepare($verifySql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            echo "<h3>Verification of Test Order Items:</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Order ID</th><th>Item Name</th><th>Price</th><th>Quantity</th></tr>";
            
            while ($item = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . h($item['id']) . "</td>";
                echo "<td>" . h($item['order_id']) . "</td>";
                echo "<td>" . h($item['item_name']) . "</td>";
                echo "<td>" . h($item['price']) . "</td>";
                echo "<td>" . h($item['quantity']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            throw new Exception("Error creating test order: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Rollback on error
        $con->rollback();
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='check_table_orders.php' class='button'>Return to Table Orders Check</a></p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    line-height: 1.6;
}
h1, h2, h3 {
    color: #333;
}
table {
    border-collapse: collapse;
    width: 100%;
    margin: 20px 0;
}
th {
    background-color: #f2f2f2;
}
.success {
    color: green;
    font-weight: bold;
}
.error {
    color: red;
    font-weight: bold;
}
.button {
    display: inline-block;
    background: #0275d8;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 4px;
}
form {
    margin: 20px 0;
}
</style> 