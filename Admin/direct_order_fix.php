<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include existing database connection
require_once 'db.php';

// Set UTF-8
header('Content-Type: text/html; charset=utf-8');

echo "<h1>HOSTED SITE ORDER FIX - MINIMAL VERSION</h1>";

// First, check the order_items table structure
echo "<h2>Order Items Table Structure:</h2>";
$itemsStructureQuery = "DESCRIBE order_items";
$itemsStructureResult = $con->query($itemsStructureQuery);

$itemColumns = [];

if ($itemsStructureResult) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($col = $itemsStructureResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $col['Extra'] . "</td>";
        echo "</tr>";
        $itemColumns[] = $col['Field'];
    }
    
    echo "</table>";
    echo "<p>Available columns: " . implode(", ", $itemColumns) . "</p>";
} else {
    echo "<p style='color:red'>Cannot get order_items structure: " . $con->error . "</p>";
}

// Function to create a test order
function createOrder($con, $name, $tableId, $totalAmount, $itemName, $availableColumns) {
    // Insert order using the existing database connection
    $sql = "INSERT INTO orders SET 
            customer_name = '$name',
            contact_number = '9123456789',
            user_id = 1,
            table_id = $tableId,
            order_date = NOW(),
            status = 'pending',
            payment_method = 'cash',
            total_amount = $totalAmount,
            amount_paid = $totalAmount,
            change_amount = 0,
            order_type = 'walk-in'";
    
    echo "<p>Executing query: " . htmlspecialchars($sql) . "</p>";
    
    // Execute query
    if ($con->query($sql)) {
        $orderId = $con->insert_id;
        echo "<p style='color:green'>Successfully created Order #$orderId for Table $tableId</p>";
        
        // Insert order item - ULTRA MINIMAL VERSION - only order_id and item_name
        $itemSql = "INSERT INTO order_items (order_id, item_name) 
                   VALUES ($orderId, '$itemName')";
        
        if ($con->query($itemSql)) {
            echo "<p style='color:green'>Added item '$itemName' to Order #$orderId (minimal fields only)</p>";
            return $orderId;
        } else {
            echo "<p style='color:red'>Failed to add item: " . $con->error . "</p>";
            echo "<p>Query was: " . htmlspecialchars($itemSql) . "</p>";
            
            // If that fails, let's try constructing a query with ONLY the columns we know exist
            $hasCols = [];
            if (in_array('order_id', $availableColumns)) $hasCols[] = 'order_id';
            if (in_array('item_name', $availableColumns)) $hasCols[] = 'item_name';
            
            if (!empty($hasCols)) {
                $colsStr = implode(", ", $hasCols);
                $valsArr = [];
                if (in_array('order_id', $hasCols)) $valsArr[] = $orderId;
                if (in_array('item_name', $hasCols)) $valsArr[] = "'$itemName'";
                $valsStr = implode(", ", $valsArr);
                
                $finalSql = "INSERT INTO order_items ($colsStr) VALUES ($valsStr)";
                echo "<p>Trying with available columns only: " . htmlspecialchars($finalSql) . "</p>";
                
                if ($con->query($finalSql)) {
                    echo "<p style='color:green'>Added item using only available columns!</p>";
                    return $orderId;
                } else {
                    echo "<p style='color:red'>All attempts failed: " . $con->error . "</p>";
                }
            }
            
            return $orderId; // Return the order ID anyway
        }
    } else {
        echo "<p style='color:red'>Failed to create order: " . $con->error . "</p>";
        return false;
    }
}

// Create 3 test orders
$order1 = createOrder($con, "Test Order 1", 21, 281.00, "TRUFFLE PARMESAN POTATO CHIPS", $itemColumns);
$order2 = createOrder($con, "Test Order 2", 22, 350.00, "SEAFOOD PASTA", $itemColumns);
$order3 = createOrder($con, "Test Order 3", 23, 420.00, "BEEF STEAK", $itemColumns);

// Check if orders exist in the database
echo "<h2>Checking Orders in Database:</h2>";

$checkQuery = "SELECT * FROM orders ORDER BY id DESC LIMIT 10";
$result = $con->query($checkQuery);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Customer</th><th>Table ID</th><th>Amount</th><th>Status</th><th>Order Type</th><th>Date</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . ($row['id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['customer_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['table_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['total_amount'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['status'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['order_type'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['order_date'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:red'>Could not retrieve orders: " . $con->error . "</p>";
}

// Check order items too
echo "<h2>Order Items:</h2>";

if (isset($order1) && $order1 && isset($order2) && $order2 && isset($order3) && $order3) {
    $itemsQuery = "SELECT * FROM order_items WHERE order_id IN ($order1, $order2, $order3)";
    $itemsResult = $con->query($itemsQuery);

    if ($itemsResult && $itemsResult->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        
        // Get column names
        $firstRow = $itemsResult->fetch_assoc();
        echo "<tr>";
        foreach ($firstRow as $columnName => $value) {
            echo "<th>" . $columnName . "</th>";
        }
        echo "</tr>";
        
        // Output first row
        echo "<tr>";
        foreach ($firstRow as $value) {
            echo "<td>" . ($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
        
        // Output remaining rows
        while ($item = $itemsResult->fetch_assoc()) {
            echo "<tr>";
            foreach ($item as $value) {
                echo "<td>" . ($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p style='color:red'>No order items found or error: " . $con->error . "</p>";
    }
} else {
    echo "<p style='color:red'>No orders were created successfully.</p>";
}

// Display table structure for debugging
echo "<h2>Orders Table Structure:</h2>";
$structureQuery = "DESCRIBE orders";
$structureResult = $con->query($structureQuery);

if ($structureResult) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($col = $structureResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $col['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}
?> 