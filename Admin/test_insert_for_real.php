<?php
// DIRECT DATABASE CONNECTION - NO DEPENDENCIES
$host = 'localhost';
$username = 'root';
$password = '';  // Usually empty for XAMPP
$database = 'u429956055_Hotelms';  // From your screenshot

// Create direct connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>DIRECT ORDER INSERTION</h1>";

// Order #1 - First manual order
$sql1 = "INSERT INTO orders (
    customer_name, contact_number, user_id, table_id, order_date, 
    status, payment_method, total_amount, amount_paid, change_amount, order_type
) VALUES (
    'Manual Order 1', '12345678', 1, 21, NOW(), 
    'pending', 'cash', 281.00, 281.00, 0, 'walk-in'
)";

if ($conn->query($sql1)) {
    $orderId1 = $conn->insert_id;
    echo "<p style='color:green'>Successfully inserted Order #1 with ID: $orderId1</p>";
    
    // Insert order item
    $itemSql1 = "INSERT INTO order_items (order_id, menu_item_id, quantity, price, item_name) 
                VALUES ($orderId1, 1, 1, 281.00, 'TRUFFLE PARMESAN POTATO CHIPS')";
    
    if ($conn->query($itemSql1)) {
        echo "<p style='color:green'>Added item to Order #1</p>";
    } else {
        echo "<p style='color:red'>Failed to add item to Order #1: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color:red'>Failed to insert Order #1: " . $conn->error . "</p>";
}

// Order #2 - Second manual order
$sql2 = "INSERT INTO orders (
    customer_name, contact_number, user_id, table_id, order_date, 
    status, payment_method, total_amount, amount_paid, change_amount, order_type
) VALUES (
    'Manual Order 2', '12345678', 1, 22, NOW(), 
    'pending', 'cash', 350.00, 350.00, 0, 'walk-in'
)";

if ($conn->query($sql2)) {
    $orderId2 = $conn->insert_id;
    echo "<p style='color:green'>Successfully inserted Order #2 with ID: $orderId2</p>";
    
    // Insert order item
    $itemSql2 = "INSERT INTO order_items (order_id, menu_item_id, quantity, price, item_name) 
                VALUES ($orderId2, 2, 1, 350.00, 'SEAFOOD PASTA')";
    
    if ($conn->query($itemSql2)) {
        echo "<p style='color:green'>Added item to Order #2</p>";
    } else {
        echo "<p style='color:red'>Failed to add item to Order #2: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color:red'>Failed to insert Order #2: " . $conn->error . "</p>";
}

// Order #3 - Third manual order
$sql3 = "INSERT INTO orders (
    customer_name, contact_number, user_id, table_id, order_date, 
    status, payment_method, total_amount, amount_paid, change_amount, order_type
) VALUES (
    'Manual Order 3', '12345678', 1, 23, NOW(), 
    'pending', 'cash', 420.00, 420.00, 0, 'walk-in'
)";

if ($conn->query($sql3)) {
    $orderId3 = $conn->insert_id;
    echo "<p style='color:green'>Successfully inserted Order #3 with ID: $orderId3</p>";
    
    // Insert order item
    $itemSql3 = "INSERT INTO order_items (order_id, menu_item_id, quantity, price, item_name) 
                VALUES ($orderId3, 3, 1, 420.00, 'BEEF STEAK')";
    
    if ($conn->query($itemSql3)) {
        echo "<p style='color:green'>Added item to Order #3</p>";
    } else {
        echo "<p style='color:red'>Failed to add item to Order #3: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color:red'>Failed to insert Order #3: " . $conn->error . "</p>";
}

// Check if orders are actually in database
echo "<h2>Verifying Orders in Database:</h2>";
$checkSql = "SELECT id, customer_name, table_id, total_amount FROM orders ORDER BY id DESC LIMIT 10";
$result = $conn->query($checkSql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Customer</th><th>Table ID</th><th>Amount</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . $row['table_id'] . "</td>";
        echo "<td>" . $row['total_amount'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No orders found or query error: " . $conn->error . "</p>";
}

// Close connection
$conn->close();
?> 