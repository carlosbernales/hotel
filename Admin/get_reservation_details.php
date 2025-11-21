<?php
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Not authorized</div>';
    exit;
}

// Get reservation ID
$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$reservation_id) {
    echo '<div class="alert alert-danger">Invalid reservation ID</div>';
    exit;
}

try {
    // Get reservation details
    $query = "SELECT * FROM table_reservations WHERE reservation_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $reservation_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error fetching reservation: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    
    if (!$reservation) {
        throw new Exception("Reservation not found");
    }
    
    $stmt->close();

    // Get order details with debugging
    $query = "SELECT ro.*, mi.name, mi.price, 
              (ro.quantity * mi.price) as subtotal 
              FROM reservation_orders ro 
              JOIN menu_items mi ON ro.menu_item_id = mi.id 
              WHERE ro.reservation_id = ?";
              
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $reservation_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error fetching orders: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $orders = [];
    
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    // Enhanced debug information
    $debug = [
        'reservation_id' => $reservation_id,
        'order_count' => count($orders),
        'query' => "SELECT ro.*, mi.name, mi.price, (ro.quantity * mi.price) as subtotal FROM reservation_orders ro JOIN menu_items mi ON ro.menu_item_id = mi.id WHERE ro.reservation_id = $reservation_id",
        'has_orders' => !empty($orders),
        'raw_orders' => $orders
    ];

    // Check reservation_orders table
    $check_query = "SELECT * FROM reservation_orders WHERE reservation_id = ?";
    $check_stmt = $con->prepare($check_query);
    $check_stmt->bind_param("i", $reservation_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $orders_in_table = [];
    while ($row = $check_result->fetch_assoc()) {
        $orders_in_table[] = $row;
    }
    $debug['orders_in_table'] = $orders_in_table;
    $check_stmt->close();

    // Check menu_items table for the menu items
    $menu_check_query = "SELECT * FROM menu_items WHERE id IN (SELECT menu_item_id FROM reservation_orders WHERE reservation_id = ?)";
    $menu_check_stmt = $con->prepare($menu_check_query);
    $menu_check_stmt->bind_param("i", $reservation_id);
    $menu_check_stmt->execute();
    $menu_result = $menu_check_stmt->get_result();
    $menu_items = [];
    while ($row = $menu_result->fetch_assoc()) {
        $menu_items[] = $row;
    }
    $debug['menu_items'] = $menu_items;
    $menu_check_stmt->close();
    
    $stmt->close();
    
    // Parse advance order JSON
    $advance_order = json_decode($reservation['advance_order'], true);
    ?>
    
    <div class="reservation-details p-3">
        <!-- Customer Information -->
        <div class="section mb-4">
            <h5 class="border-bottom pb-2 mb-3">Customer Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($reservation['customer_name']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($reservation['contact_number']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Package Type:</strong> <?php echo htmlspecialchars($reservation['table_type']); ?></p>
                    <p><strong>Guest Count:</strong> <?php echo $reservation['guest_count']; ?></p>
                </div>
            </div>
        </div>

        <!-- Reservation Details -->
        <div class="section mb-4">
            <h5 class="border-bottom pb-2 mb-3">Reservation Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($reservation['reservation_datetime'])); ?></p>
                    <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($reservation['reservation_datetime'])); ?></p>
                </div>
                <div class="col-md-6">
                    <p>
                        <strong>Status:</strong> 
                        <span class="badge <?php 
                            echo $reservation['status'] === 'confirmed' ? 'badge-success' : 
                                ($reservation['status'] === 'cancelled' ? 'badge-danger' : 'badge-warning'); 
                        ?>">
                            <?php echo ucfirst($reservation['status']); ?>
                        </span>
                    </p>
                    <p><strong>Created:</strong> <?php echo date('M j, Y g:i A', strtotime($reservation['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Advance Order Details -->
        <?php if ($advance_order && isset($advance_order['items']) && !empty($advance_order['items'])): ?>
        <div class="section mb-4">
            <h5 class="border-bottom pb-2 mb-3">Advance Order Details</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($advance_order['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="text-right">₱<?php echo number_format($item['price'], 2); ?></td>
                            <td class="text-right">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Amount:</th>
                            <th class="text-right">₱<?php echo number_format($reservation['total_amount'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="section">
            <h5 class="border-bottom pb-2 mb-3">Payment Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Payment Type:</strong> <?php echo ucfirst($reservation['payment_type']); ?> Payment</p>
                    <p><strong>Payment Method:</strong> <?php echo ucfirst($reservation['payment_method']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Amount to Pay:</strong> ₱<?php echo number_format($reservation['amount_to_pay'], 2); ?></p>
                    <p>
                        <strong>Payment Status:</strong>
                        <span class="badge <?php echo $reservation['payment_status'] === 'paid' ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo ucfirst($reservation['payment_status']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php

} catch (Exception $e) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
}
?> 