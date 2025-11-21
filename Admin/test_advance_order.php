<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'test_advance_order.log');

// Include database connection
require_once 'db.php';

echo "<h1>Test Advance Order Creation</h1>";

// Check if the table exists
$tableCheck = $con->query("SHOW TABLES LIKE 'advance_table_orders'");
if ($tableCheck->num_rows == 0) {
    echo "<p style='color:red'>Table 'advance_table_orders' does not exist!</p>";
    echo "<p>Trying to create the table now...</p>";
    
    // Try to create the table
    $createTableSql = file_get_contents('advance_table_orders.sql');
    if ($con->multi_query($createTableSql)) {
        echo "<p style='color:green'>Successfully created the advance_table_orders table!</p>";
        // Clear results so we can run more queries
        while ($con->more_results() && $con->next_result()) {
            // consume all results
        }
    } else {
        echo "<p style='color:red'>Failed to create table: " . $con->error . "</p>";
        echo "<p>SQL used:</p>";
        echo "<pre>" . htmlspecialchars($createTableSql) . "</pre>";
        exit;
    }
}

// Create a test booking if we need to
$testBookingId = null;
if (isset($_POST['create_booking'])) {
    try {
        // Insert a test booking
        $sql = "INSERT INTO table_bookings (
            user_id, package_name, name, contact_number, email_address,
            booking_date, booking_time, num_guests, total_amount, 
            payment_status, status, package_type, created_at
        ) VALUES (
            1, 'Test Package', 'Test Customer', '123456789', 'test@example.com',
            DATE_ADD(CURDATE(), INTERVAL 1 DAY), '12:00:00', 2, 100.00,
            'Pending', 'Pending', 'Test', NOW()
        )";
        
        if ($con->query($sql)) {
            $testBookingId = $con->insert_id;
            echo "<p style='color:green'>Created test booking with ID: $testBookingId</p>";
        } else {
            echo "<p style='color:red'>Failed to create test booking: " . $con->error . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Exception creating test booking: " . $e->getMessage() . "</p>";
    }
}

// Test advance order creation
if (isset($_POST['create_advance_order'])) {
    $bookingId = $_POST['booking_id'];
    
    try {
        // Create sample order items JSON
        $orderItems = [
            [
                'id' => 1,
                'name' => 'Test Food Item',
                'price' => 50.00,
                'quantity' => 2
            ],
            [
                'id' => 2,
                'name' => 'Test Drink Item',
                'price' => 25.00,
                'quantity' => 2
            ]
        ];
        
        $orderItemsJson = json_encode($orderItems);
        $customerName = 'Test Customer';
        $totalAmount = 150.00;
        $paymentOption = 'full';
        $paymentMethod = 'cash';
        $amountToPay = 150.00;
        
        // Insert the advance order
        $sql = "INSERT INTO advance_table_orders (
            table_booking_id, 
            customer_name, 
            order_items, 
            total_amount, 
            payment_option, 
            payment_method, 
            amount_to_pay
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }
        
        $stmt->bind_param(
            "issssss",
            $bookingId,
            $customerName,
            $orderItemsJson,
            $totalAmount,
            $paymentOption,
            $paymentMethod,
            $amountToPay
        );
        
        if ($stmt->execute()) {
            $advanceOrderId = $con->insert_id;
            echo "<p style='color:green'>Successfully created advance order with ID: $advanceOrderId</p>";
            
            // Now try to fetch it back to validate
            $fetchSql = "SELECT * FROM advance_table_orders WHERE id = ?";
            $fetchStmt = $con->prepare($fetchSql);
            $fetchStmt->bind_param("i", $advanceOrderId);
            $fetchStmt->execute();
            $result = $fetchStmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $advanceOrder = $result->fetch_assoc();
                echo "<h3>Created Advance Order Record:</h3>";
                echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
                foreach ($advanceOrder as $field => $value) {
                    echo "<tr>";
                    echo "<th>" . htmlspecialchars($field) . "</th>";
                    
                    if ($field === 'order_items') {
                        // Pretty print the JSON
                        $decodedItems = json_decode($value, true);
                        echo "<td><pre>" . htmlspecialchars(json_encode($decodedItems, JSON_PRETTY_PRINT)) . "</pre></td>";
                    } else {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    
                    echo "</tr>";
                }
                echo "</table>";
                
                // Test get_advance_order.php
                echo "<h3>Testing get_advance_order.php API:</h3>";
                echo "<p>API URL: <a href='get_advance_order.php?advance_order_id=$advanceOrderId' target='_blank'>get_advance_order.php?advance_order_id=$advanceOrderId</a></p>";
            }
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}

// Get existing table bookings for testing
$bookingsQuery = "SELECT id, name, booking_date, booking_time FROM table_bookings ORDER BY id DESC LIMIT 10";
$bookingsResult = $con->query($bookingsQuery);
?>

<form method="post" style="margin-top:20px; padding:15px; background:#f8f9fa; border-radius:5px;">
    <?php if (isset($testBookingId)): ?>
        <input type="hidden" name="booking_id" value="<?php echo $testBookingId; ?>">
        <p>Created booking ID: <?php echo $testBookingId; ?></p>
        <button type="submit" name="create_advance_order" style="padding:10px 15px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">
            Create Advance Order for New Booking
        </button>
    <?php else: ?>
        <button type="submit" name="create_booking" style="padding:10px 15px; background:#FF9800; color:white; border:none; border-radius:4px; cursor:pointer; margin-bottom:20px;">
            Create Test Booking
        </button>
        
        <?php if ($bookingsResult && $bookingsResult->num_rows > 0): ?>
            <h3>Or Use Existing Booking:</h3>
            <select name="booking_id" style="padding:8px; border-radius:4px; border:1px solid #ddd; margin-right:10px;">
                <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                    <option value="<?php echo $booking['id']; ?>">
                        ID: <?php echo $booking['id']; ?> - 
                        <?php echo $booking['name']; ?> - 
                        <?php echo $booking['booking_date']; ?> 
                        <?php echo $booking['booking_time']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="create_advance_order" style="padding:10px 15px; background:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">
                Create Advance Order for Selected Booking
            </button>
        <?php endif; ?>
    <?php endif; ?>
</form>

<h2>Existing Advance Orders</h2>
<?php
// Show existing records
$existingQuery = "SELECT * FROM advance_table_orders ORDER BY id DESC LIMIT 10";
$existingResult = $con->query($existingQuery);

if ($existingResult && $existingResult->num_rows > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse; width:100%;'>";
    echo "<tr><th>ID</th><th>Booking ID</th><th>Customer Name</th><th>Items</th><th>Total</th><th>Created</th><th>Actions</th></tr>";
    
    while ($order = $existingResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $order['id'] . "</td>";
        echo "<td>" . $order['table_booking_id'] . "</td>";
        echo "<td>" . htmlspecialchars($order['customer_name']) . "</td>";
        
        $itemsDecoded = json_decode($order['order_items'], true);
        $itemsCount = is_array($itemsDecoded) ? count($itemsDecoded) : 0;
        
        echo "<td>" . $itemsCount . " items</td>";
        echo "<td>₱" . $order['total_amount'] . "</td>";
        echo "<td>" . $order['created_at'] . "</td>";
        echo "<td><a href='get_advance_order.php?advance_order_id=" . $order['id'] . "' target='_blank'>View</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No advance orders found in the database.</p>";
}
?>

<div style="margin-top:20px;">
    <a href="table_packages.php" style="display:inline-block; padding:10px 15px; background:#007bff; color:white; text-decoration:none; border-radius:4px;">
        Back to Table Packages
    </a>
</div> 