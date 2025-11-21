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

// Function to sanitize output
function h($str) {
    if ($str === null) {
        return '';
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Get all table bookings with their associated orders
$sql = "SELECT 
            tb.id as booking_id,
            tb.package_name,
            tb.name as customer_name,
            tb.contact_number,
            tb.booking_date,
            tb.booking_time,
            tb.num_guests,
            tb.total_amount,
            tb.payment_method,
            tb.status as booking_status,
            tb.created_at as booking_created,
            o.id as order_id,
            o.customer_name as order_customer,
            o.total_amount as order_amount,
            o.order_date,
            o.status as order_status,
            o.order_type,
            COUNT(oi.id) as item_count
        FROM 
            table_bookings tb
        LEFT JOIN 
            orders o ON tb.id = o.table_id
        LEFT JOIN
            order_items oi ON o.id = oi.order_id
        GROUP BY 
            tb.id, o.id
        ORDER BY 
            tb.created_at DESC
        LIMIT 50";

$result = $con->query($sql);

// Check if there was an error with the query
if (!$result) {
    $error = "Error executing query: " . $con->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Reservation Orders Check</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        .status-pending {
            background-color: #fff3cd;
        }
        .status-confirmed {
            background-color: #d1e7dd;
        }
        .status-cancelled {
            background-color: #f8d7da;
        }
        .missing-order {
            background-color: #f8d7da;
        }
        .has-order {
            background-color: #d1e7dd;
        }
        .table th {
            white-space: nowrap;
        }
        h1 {
            margin-bottom: 20px;
        }
        .fixed-action-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        .diagnostic-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1>Table Reservation Orders Check</h1>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Diagnostic Information -->
                <div class="card diagnostic-card">
                    <div class="card-header bg-primary text-white">
                        Diagnostic Information
                    </div>
                    <div class="card-body">
                        <p><strong>Total Table Bookings:</strong> 
                            <?php 
                            $countBookings = $con->query("SELECT COUNT(*) as count FROM table_bookings");
                            echo $countBookings->fetch_assoc()['count'];
                            ?>
                        </p>
                        <p><strong>Total Orders:</strong> 
                            <?php 
                            $countOrders = $con->query("SELECT COUNT(*) as count FROM orders");
                            echo $countOrders->fetch_assoc()['count'];
                            ?>
                        </p>
                        <p><strong>Orders with Table ID:</strong> 
                            <?php 
                            $countTableOrders = $con->query("SELECT COUNT(*) as count FROM orders WHERE table_id IS NOT NULL AND table_id > 0");
                            echo $countTableOrders->fetch_assoc()['count'];
                            ?>
                        </p>
                        <p><strong>Orders with Order Type 'advance':</strong> 
                            <?php 
                            $countAdvanceOrders = $con->query("SELECT COUNT(*) as count FROM orders WHERE order_type = 'advance'");
                            echo $countAdvanceOrders->fetch_assoc()['count'];
                            ?>
                        </p>
                        <p><strong>Orders with Order Type 'walk-in':</strong> 
                            <?php 
                            $countWalkInOrders = $con->query("SELECT COUNT(*) as count FROM orders WHERE order_type = 'walk-in'");
                            echo $countWalkInOrders->fetch_assoc()['count'];
                            ?>
                        </p>
                    </div>
                </div>
                
                <!-- Database Structure Check -->
                <div class="card diagnostic-card">
                    <div class="card-header bg-info text-white">
                        Orders Table Structure
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Column</th>
                                    <th>Type</th>
                                    <th>Null</th>
                                    <th>Key</th>
                                    <th>Default</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $columns = $con->query("DESCRIBE orders");
                                while ($column = $columns->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . h($column['Field']) . "</td>";
                                    echo "<td>" . h($column['Type']) . "</td>";
                                    echo "<td>" . h($column['Null']) . "</td>";
                                    echo "<td>" . h($column['Key']) . "</td>";
                                    echo "<td>" . h($column['Default']) . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Quick Fix Actions -->
                <div class="card diagnostic-card">
                    <div class="card-header bg-warning text-dark">
                        Quick Fix Actions
                    </div>
                    <div class="card-body">
                        <form id="fixForm" method="post">
                            <div class="mb-3">
                                <button type="submit" name="action" value="update_order_type" class="btn btn-warning btn-block">
                                    Update All Table Orders to 'walk-in'
                                </button>
                                <small class="form-text text-muted">This will update all orders with a table_id to have order_type='walk-in'</small>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" name="action" value="recreate_orders" class="btn btn-danger btn-block">
                                    Recreate Missing Orders
                                </button>
                                <small class="form-text text-muted">Attempt to create orders for bookings that don't have associated orders</small>
                            </div>
                        </form>
                        
                        <?php
                        // Handle form submissions
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            if (isset($_POST['action'])) {
                                if ($_POST['action'] === 'update_order_type') {
                                    // Update all orders with table_id to have order_type='walk-in'
                                    $updateSql = "UPDATE orders SET order_type = 'walk-in' WHERE table_id IS NOT NULL AND table_id > 0";
                                    if ($con->query($updateSql)) {
                                        echo '<div class="alert alert-success mt-3">Successfully updated orders to type "walk-in".</div>';
                                    } else {
                                        echo '<div class="alert alert-danger mt-3">Error updating orders: ' . $con->error . '</div>';
                                    }
                                }
                                else if ($_POST['action'] === 'recreate_orders') {
                                    // Get bookings without orders
                                    $noOrdersSql = "SELECT * FROM table_bookings 
                                                  WHERE id NOT IN (SELECT table_id FROM orders WHERE table_id IS NOT NULL)
                                                  AND total_amount > 0";
                                    $noOrdersResult = $con->query($noOrdersSql);
                                    
                                    $successCount = 0;
                                    $errorCount = 0;
                                    
                                    while ($booking = $noOrdersResult->fetch_assoc()) {
                                        // Create an order for this booking
                                        $createOrderSql = "INSERT INTO orders (
                                            customer_name, contact_number, user_id, table_id, order_date, 
                                            status, payment_method, total_amount, amount_paid, change_amount, order_type
                                        ) VALUES (
                                            '" . $con->real_escape_string($booking['name']) . "',
                                            '" . $con->real_escape_string($booking['contact_number']) . "',
                                            " . intval($booking['user_id']) . ",
                                            " . intval($booking['id']) . ",
                                            '" . $con->real_escape_string($booking['created_at']) . "',
                                            'pending',
                                            '" . $con->real_escape_string($booking['payment_method']) . "',
                                            " . floatval($booking['total_amount']) . ",
                                            " . floatval($booking['amount_to_pay']) . ",
                                            0,
                                            'walk-in'
                                        )";
                                        
                                        if ($con->query($createOrderSql)) {
                                            $successCount++;
                                        } else {
                                            $errorCount++;
                                        }
                                    }
                                    
                                    if ($successCount > 0) {
                                        echo '<div class="alert alert-success mt-3">Successfully created ' . $successCount . ' orders.</div>';
                                    } 
                                    if ($errorCount > 0) {
                                        echo '<div class="alert alert-danger mt-3">Failed to create ' . $errorCount . ' orders.</div>';
                                    }
                                    if ($successCount === 0 && $errorCount === 0) {
                                        echo '<div class="alert alert-info mt-3">No bookings without orders found.</div>';
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Most Recent Orders -->
                <div class="card diagnostic-card">
                    <div class="card-header bg-success text-white">
                        Most Recent Orders
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Table ID</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recentOrders = $con->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
                                while ($order = $recentOrders->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . h($order['id']) . "</td>";
                                    echo "<td>" . h($order['order_type']) . "</td>";
                                    echo "<td>" . (empty($order['table_id']) ? 'N/A' : h($order['table_id'])) . "</td>";
                                    echo "<td>₱" . h($order['total_amount']) . "</td>";
                                    echo "<td>" . h(date('m/d/Y', strtotime($order['order_date']))) . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Table Bookings and Orders -->
        <div class="table-container">
            <h3>Table Bookings and Associated Orders (Last 50)</h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php else: ?>
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Package</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Booking Status</th>
                            <th>Order Status</th>
                            <th>Order ID</th>
                            <th>Order Type</th>
                            <th>Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="<?php echo empty($row['order_id']) ? 'missing-order' : 'has-order'; ?> 
                                           <?php echo 'status-' . strtolower($row['booking_status']); ?>">
                                    <td><?php echo h($row['booking_id']); ?></td>
                                    <td><?php echo h($row['customer_name']); ?><br>
                                        <small><?php echo h($row['contact_number']); ?></small></td>
                                    <td><?php echo h($row['package_name']); ?><br>
                                        <small><?php echo h($row['num_guests']); ?> guests</small></td>
                                    <td><?php echo h(date('m/d/Y', strtotime($row['booking_date']))); ?><br>
                                        <small><?php echo h($row['booking_time']); ?></small></td>
                                    <td>₱<?php echo h($row['total_amount']); ?><br>
                                        <small><?php echo h($row['payment_method']); ?></small></td>
                                    <td><span class="badge bg-<?php 
                                        echo $row['booking_status'] === 'Pending' ? 'warning' : 
                                            ($row['booking_status'] === 'Confirmed' ? 'success' : 
                                            ($row['booking_status'] === 'Cancelled' ? 'danger' : 'info')); 
                                        ?>"><?php echo h($row['booking_status']); ?></span><br>
                                        <small><?php echo h(date('m/d/Y', strtotime($row['booking_created']))); ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['order_status'])): ?>
                                            <span class="badge bg-<?php 
                                                echo $row['order_status'] === 'pending' ? 'warning' : 
                                                    ($row['order_status'] === 'accepted' ? 'primary' : 
                                                    ($row['order_status'] === 'finished' ? 'success' : 'info')); 
                                                ?>"><?php echo h($row['order_status']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No Order</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo empty($row['order_id']) ? 'N/A' : h($row['order_id']); ?></td>
                                    <td><?php echo empty($row['order_type']) ? 'N/A' : h($row['order_type']); ?></td>
                                    <td><?php echo empty($row['item_count']) || $row['item_count'] == 0 ? 'No items' : h($row['item_count']) . ' items'; ?></td>
                                    <td>
                                        <?php if (empty($row['order_id']) && floatval($row['total_amount']) > 0): ?>
                                            <form method="post">
                                                <input type="hidden" name="booking_id" value="<?php echo h($row['booking_id']); ?>">
                                                <button type="submit" name="action" value="create_order" class="btn btn-sm btn-primary">
                                                    Create Order
                                                </button>
                                            </form>
                                        <?php elseif (!empty($row['order_id'])): ?>
                                            <a href="Cashier/view_order.php?id=<?php echo h($row['order_id']); ?>" class="btn btn-sm btn-info" target="_blank">
                                                View Order
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No action</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">No table bookings found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Fixed Action Button to Return to Admin -->
    <div class="fixed-action-btn">
        <a href="index.php" class="btn btn-lg btn-primary">
            <i class="fa fa-home"></i> Back to Dashboard
        </a>
    </div>
    
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html> 