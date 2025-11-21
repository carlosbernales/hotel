<?php
require "db.php"; // Include the database connection file

// Add these CSS and JS includes at the very top, before any HTML output
?>
<!-- Add required CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// SQL query to fetch order details with processed_by field and user details
$sql = "SELECT 
    orders.*,
    CONCAT(userss.first_name, ' ', userss.last_name) as customer_name,
    order_items.id as item_id,
    order_items.item_name,
    order_items.quantity,
    order_items.unit_price,
    GROUP_CONCAT(DISTINCT order_item_addons.addon_name SEPARATOR '||') as addon_names,
    GROUP_CONCAT(DISTINCT order_item_addons.addon_price SEPARATOR '||') as addon_prices,
    COALESCE(orders.processed_by, 'Not processed yet') as processed_by,
    orders.discount_type,
    orders.discount_amount,
    orders.amount_paid,
    orders.change_amount as change_amount,
    orders.payment_status,
    orders.table_name,
    (
        SELECT SUM(oi.quantity * oi.unit_price + COALESCE((
            SELECT SUM(oia.addon_price)
            FROM order_item_addons oia
            WHERE oia.order_item_id = oi.id
        ), 0))
        FROM order_items oi
        WHERE oi.order_id = orders.id
    ) as calculated_total
FROM orders 
LEFT JOIN userss ON orders.user_id = userss.id
LEFT JOIN order_items ON orders.id = order_items.order_id 
LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
WHERE orders.status = 'processing'
GROUP BY 
    orders.id,
    orders.customer_name,
    orders.processed_by,
    orders.discount_type,
    orders.discount_amount,
    orders.amount_paid,
    orders.change_amount,
    orders.payment_status,
    orders.table_name,
    orders.order_date,
    order_items.id,
    order_items.item_name,
    order_items.quantity,
    order_items.unit_price,
    order_items.order_id
ORDER BY orders.order_date DESC";

$result = $connection->query($sql);

// Initialize orders array with improved structure
$orders = [];

// Process the results with better error handling
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['id'];
        
        // Debug logging
        error_log("Order ID: " . $orderId . " Change Amount: " . $row['change_amount']);
        
        // Initialize order if not exists
        if (!isset($orders[$orderId])) {
            // Calculate discount and final amount
            $originalAmount = floatval($row['calculated_total']);
            $discountType = $row['discount_type'];
            $discountAmount = floatval($row['discount_amount'] ?? 0);
            $amountPaid = floatval($row['amount_paid'] ?? 0);
            $changeAmount = floatval($row['change_amount'] ?? 0);
            
            // Debug logging
            error_log("Processing order $orderId - Change Amount: $changeAmount");
            
            // Calculate discounted amount
            $discountedAmount = $originalAmount;
            if ($discountType === 'senior_citizen' || $discountType === 'pwd') {
                if ($discountAmount === 0) {
                    $discountAmount = $originalAmount * 0.20; // 20% discount
                }
                $discountedAmount = $originalAmount - $discountAmount;
            } else if ($discountAmount > 0) {
                $discountedAmount = $originalAmount - $discountAmount;
            }

            // Calculate remaining balance
            $remainingBalance = $discountedAmount - $amountPaid;
            
            $orders[$orderId] = [
                'id' => $orderId,
                'user_id' => $row['user_id'],
                'customer_name' => $row['customer_name'],
                'payment_method' => $row['payment_method'],
                'table_name' => $row['table_name'] ?? 'N/A',
                'total_amount' => $originalAmount,
                'discount_type' => $discountType,
                'discount_amount' => $discountAmount,
                'final_amount' => $discountedAmount,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'remaining_balance' => max(0, $remainingBalance),
                'payment_status' => $row['payment_status'],
                'order_date' => $row['order_date'],
                'status' => $row['status'],
                'order_type' => $row['order_type'],
                'type_of_order' => $row['type_of_order'] ?? 'dine_in',
                'processed_by' => $row['processed_by'] ?? 'Not processed yet',
                'items' => [],
            ];
        }

        // Process item and its addons
        if (!empty($row['item_name'])) {
            $itemId = $row['item_id'];
            if (!isset($orders[$orderId]['items'][$itemId])) {
                $addonNames = !empty($row['addon_names']) ? explode('||', $row['addon_names']) : [];
                $addonPrices = !empty($row['addon_prices']) ? explode('||', $row['addon_prices']) : [];
                
                $addons = [];
                foreach ($addonNames as $index => $addonName) {
                    if (isset($addonPrices[$index])) {
                        $addons[] = [
                            'name' => trim($addonName),
                            'price' => floatval(trim($addonPrices[$index]))
                        ];
                    }
                }

                // Ensure proper numeric values
                $quantity = intval($row['quantity']);
                $unitPrice = is_numeric($row['unit_price']) ? floatval($row['unit_price']) : 0;
                $itemSubtotal = $quantity * $unitPrice;
                
                // Add addon prices to subtotal
                foreach ($addons as $addon) {
                    $itemSubtotal += floatval($addon['price']);
                }

                $orders[$orderId]['items'][$itemId] = [
                    'id' => $itemId,
                    'name' => $row['item_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'addons' => !empty($row['addon_names']) ? implode(', ', explode('||', $row['addon_names'])) : 'None',
                    'subtotal' => $itemSubtotal
                ];
            }
        }
    }
} else {
    // Handle query error
    error_log("Query error: " . $connection->error);
}

// Convert items from associative to indexed array for easier handling in the view
foreach ($orders as &$order) {
    $order['items'] = array_values($order['items']);
}
unset($order); // Break the reference
?>

<div class="main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
                <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Processing Orders</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div id="flashMessage" class="alert" style="display: none;"></div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Processing Orders</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%"
                           id="rooms">
                        <thead>
                        <tr>
                            <th>Order Details</th>
                            <th>Customer Name</th>
                            <th>Table Number</th>
                            <th>Order Type</th>
                            <th>Mode of Order</th>
                            <th>Payment Method</th>
                            <th>Total Amount</th>
                            <th>Ordered At</th>
                            <th>Status</th>
                            <th>Processed By</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $orderId => $order): ?>
                            <tr data-order-id="<?php echo $orderId; ?>">
                                <td class="order-details">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?php 
                                                // Ensure price is properly formatted
                                                $unitPrice = is_numeric($item['unit_price']) ? number_format($item['unit_price'], 2) : '0.00';
                                                echo htmlspecialchars($item['name']); ?> 
                                                (<?php echo htmlspecialchars($item['quantity']); ?> x ₱<?php echo $unitPrice; ?>)
                                            <?php if (!empty($item['addons'])): ?>
                                                <br>Add-ons: <?php echo htmlspecialchars($item['addons']); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php 
                                    // Show the actual customer name from the database instead of hardcoded "Walkin Customers"
                                    echo htmlspecialchars($order['customer_name'] ?? 'N/A'); 
                                ?></td>
                                <td>
                                    <?php if ($order['table_name'] === 'N/A' || empty($order['table_name'])): ?>
                                        <span class="text-muted">N/A</span>
                                        <button class="btn btn-primary btn-sm ml-2 select-table" 
                                                data-order-id="<?php echo $orderId; ?>"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title="Select Table">
                                            <i class="fas fa-chair"></i> Select Table
                                        </button>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($order['table_name']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $orderType = strtolower($order['order_type'] ?? '');
                                        $displayType = '';
                                        
                                        // Properly handle different order types
                                        switch($orderType) {
                                            case 'online':
                                                $displayType = 'Online Order';
                                                break;
                                            case 'advance':
                                                $displayType = 'Advance Order';
                                                break;
                                            case 'walk-in':
                                                $displayType = 'Walk-in Order';
                                                break;
                                            default:
                                                $displayType = ucfirst($orderType) . ' Order';
                                        }
                                        echo htmlspecialchars($displayType);
                                    ?>
                                </td>
                                <td><?php echo !empty($order['type_of_order']) ? htmlspecialchars(ucfirst($order['type_of_order'])) : 'Dine-in'; ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td class="total-amount">
                                    <?php if (!empty($order['discount_type'])): ?>
                                        ₱<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?><br>
                                        <span class="text-success">
                                            Discount (20%): ₱<?php echo htmlspecialchars(number_format($order['discount_amount'], 2)); ?>
                                        </span><br>
                                        <strong class="text-primary">
                                            Final Amount: ₱<?php echo htmlspecialchars(number_format($order['final_amount'], 2)); ?>
                                        </strong>
                                    <?php else: ?>
                                        ₱<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>
                                    <span class="label label-info">Processing</span>
                                </td>
                                <td>
    <?php 
    if (!empty($order['processed_by']) && $order['processed_by'] !== 'Not processed yet') {
        echo htmlspecialchars($order['processed_by']);
        // Add a small user icon before the name
        echo ' <i class="fas fa-user-check text-success" data-toggle="tooltip" title="Processed by staff"></i>';
    } else {
        echo '<span class="text-muted">Not processed yet <i class="fas fa-user-clock" data-toggle="tooltip" title="Awaiting processing"></i></span>';
    }
    ?>
</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-info btn-sm view-order" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="View Order Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm finish-order" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Mark Order as Finished">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-sm print-order" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Print Order">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal HTML at the bottom of your file, before the scripts -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="orderDetailsModalLabel">Order Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <p><strong>Name:</strong> <span id="customerName"></span></p>
                        <p><strong>Table Number:</strong> <span id="tableName"></span></p>
                        <p><strong>Order Type:</strong> <span id="orderType"></span></p>
                        <p><strong>Payment Method:</strong> <span id="paymentMethod"></span></p>
                        <p><strong>Order Date:</strong> <span id="orderDate"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Summary</h5>
                        <p><strong>Status:</strong> <span id="orderStatus" class="label label-info"></span></p>
                        <p><strong>Total Amount:</strong> ₱<span id="totalAmount">0.00</span></p>
                        <p class="discount-info" style="display: none;">
                            <strong>Discount:</strong> ₱<span id="discountAmount">0.00</span>
                            (<span id="discountType"></span>)
                        </p>
                        <p><strong>Final Amount:</strong> ₱<span id="finalAmount">0.00</span></p>
                        <p><strong>Amount Paid:</strong> ₱<span id="amountPaid">0.00</span></p>
                        <p><strong>Change:</strong> ₱<span id="changeAmount">0.00</span></p>
                        <p class="remaining-balance" style="display: none;">
                            <strong>Remaining Balance:</strong> ₱<span id="remainingBalance">0.00</span>
                        </p>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Order Items</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Add-ons</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal HTML before the existing modals -->
<div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Additional Orders</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addOrderForm">
                    <input type="hidden" name="order_id" id="additionalOrderId">
                    
                    <!-- Menu Categories -->
                    <div class="form-group">
                        <label>Select Category:</label>
                        <select class="form-control" id="menuCategory">
                            <option value="">Select Category</option>
                            <!-- Categories will be loaded dynamically -->
                        </select>
                    </div>

                    <!-- Menu Items -->
                    <div class="form-group">
                        <label>Select Item:</label>
                        <select class="form-control" id="menuItem" disabled>
                            <option value="">Select Item First</option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label>Quantity:</label>
                        <input type="number" class="form-control" id="itemQuantity" min="1" value="1">
                    </div>

                    <!-- Add-ons Section -->
                    <div class="form-group" id="addonsSection" style="display: none;">
                        <label>Available Add-ons:</label>
                        <div id="addonsList">
                            <!-- Add-ons will be loaded dynamically -->
                        </div>
                    </div>

                    <!-- Selected Items Table -->
                    <div class="form-group">
                        <label>Selected Items:</label>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Add-ons</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="selectedItemsList">
                                <!-- Selected items will appear here -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addSelectedItem">Add Item</button>
                <button type="button" class="btn btn-success" id="saveAdditionalOrders">Save Orders</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Order</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editOrderForm">
                    <input type="hidden" id="editOrderId" name="order_id">
                    
                    <!-- Menu Categories Dropdown -->
                    <div class="form-group">
                        <label>Select Category:</label>
                        <select class="form-control" id="editMenuCategory">
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <!-- Menu Items Dropdown -->
                    <div class="form-group">
                        <label>Select Item:</label>
                        <select class="form-control" id="editMenuItem">
                            <option value="">Select Category First</option>
                        </select>
                    </div>

                    <!-- Quantity Input -->
                    <div class="form-group">
                        <label>Quantity:</label>
                        <input type="number" class="form-control" id="editItemQuantity" min="1" value="1">
                    </div>

                    <!-- Add-ons Section -->
                    <div class="form-group" id="editAddonsSection" style="display: none;">
                        <label>Available Add-ons:</label>
                        <div id="editAddonsList">
                            <!-- Add-ons will be loaded dynamically -->
                        </div>
                    </div>

                    <!-- Current Order Items Table -->
                    <div class="form-group">
                        <label>Current Order Items:</label>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Add-ons</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="editOrderItems">
                                <!-- Order items will be loaded here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                                    <td colspan="2"><strong id="editTotalAmount">₱0.00</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addEditItem">Add Item</button>
                <button type="button" class="btn btn-success" id="saveOrderChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal HTML before the existing modals -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Process Payment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="payment_order_id" name="order_id">
                    
                    <div class="form-group">
                        <label>Total Amount:</label>
                        <input type="text" class="form-control" id="total_amount" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Amount Paid:</label>
                        <input type="text" class="form-control" id="amount_paid" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Remaining Balance:</label>
                        <input type="text" class="form-control" id="remaining_balance" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Amount:</label>
                        <input type="number" class="form-control" id="payment_amount" name="payment_amount" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method:</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="processPayment">Process Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- First, add this new payment modal HTML after your existing modals -->
<div class="modal fade" id="additionalOrderPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Process Payment for Additional Orders</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="additionalOrderPaymentForm">
                    <input type="hidden" id="additional_order_id" name="order_id">
                    
                    <div class="form-group">
                        <label>Total Amount:</label>
                        <input type="text" class="form-control" id="additional_total_amount" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Amount:</label>
                        <input type="number" class="form-control" id="additional_payment_amount" name="payment_amount" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method:</label>
                        <select class="form-control" id="additional_payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="processAdditionalPayment">Process Payment</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Update the .main class margin */
.main {
    margin-left: 0 !important; /* Remove left margin */
    padding-left: 15px; /* Add some padding instead */
    width: 100%; /* Ensure full width */
    padding-top: 50px;
}

/* Adjust the existing col-sm-offset and col-lg-offset classes */
.col-sm-offset-3,
.col-lg-offset-2 {
    margin-left: 0 !important; /* Remove the bootstrap offset */
}

del {
    color: #6c757d;
    text-decoration: line-through;
}

.text-success {
    color: #28a745 !important;
}

.text-primary {
    color: #007bff !important;
}

.label-info {
    background-color: #5bc0de;
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 0.8em;
}

.btn-sm {
    margin: 0 2px;
}

.btn i {
    margin-right: 0;
}

.btn {
    padding: 5px 10px;
}

/* Tooltip styling */
.tooltip {
    font-size: 12px;
}

.tooltip-inner {
    background-color: #333;
    padding: 5px 10px;
}

.btn-group {
    display: flex;
    gap: 5px;
}

.btn-group .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 4px;
}

.btn-group .btn i {
    font-size: 14px;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    transition: all 0.2s;
}

.btn:active {
    transform: translateY(0);
}

/* Make sure icons are visible */
.fas {
    line-height: 1;
}

/* Additional styles to ensure proper spacing */
.breadcrumb {
    margin-top: 10px;
}

.modal-lg {
    max-width: 80%;
}

.modal-body {
    padding: 20px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.table th {
    background-color: #f8f9fa;
}

.mt-4 {
    margin-top: 1.5rem;
}

/* Add these to your existing styles */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeeba;
}

.text-success {
    color: #28a745;
    font-weight: 500;
}

.text-primary {
    color: #007bff;
    font-weight: 500;
}

#orderDetailsModal table td {
    vertical-align: middle;
}

#orderDetailsModal .row {
    margin-bottom: 20px;
}

#orderDetailsModal h5 {
    color: #333;
    margin-bottom: 15px;
    border-bottom: 2px solid #eee;
    padding-bottom: 5px;
}

/* Add to your existing styles */
#orderDetailsModal p {
    margin-bottom: 10px;
    line-height: 1.5;
}

#orderDetailsModal .text-success,
#orderDetailsModal .text-primary {
    font-weight: 600;
}

#amountPaid, #changeAmount {
    font-size: 1.1em;
}

#orderDetailsModal .col-md-6 {
    margin-bottom: 20px;
}

/* Add to your existing styles */
.text-warning {
    color: #ffc107;
    font-weight: 500;
}

.text-muted {
    color: #6c757d;
    font-weight: 500;
}

/* Payment Status Labels */
.label {
    display: inline-block;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 3px;
}

.label-success {
    background-color: #28a745;
    color: white;
}

.label-warning {
    background-color: #ffc107;
    color: #000;
}

.label-info {
    background-color: #17a2b8;
    color: white;
}

/* Add data-order-id attribute to the order row */
.modal-body p {
    margin-bottom: 10px;
    line-height: 1.5;
}

.label {
    display: inline-block;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 3px;
}

.label-info {
    background-color: #17a2b8;
    color: white;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.ml-2 {
    margin-left: 0.5rem;
}

.discount-info, .remaining-balance {
    color: #dc3545;
    font-weight: 500;
}

.table tfoot tr:last-child {
    font-size: 1.1em;
}

.table tfoot td {
    border-top: 2px solid #dee2e6;
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#rooms').DataTable();

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Helper function for SweetAlert2 messages
    function showAlert(title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }

    // Modify the view-order click handler to check for remaining balance
    $('.view-order').on('click', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        
        $.ajax({
            url: 'get_order_details.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                if(response.success) {
                    // Add debug logging
                    console.log('Order response:', response);
                    
                    // Populate customer information
                    $('#customerName').text(response.order.customer_name || 'Walk-in Customer');
                    $('#tableName').text(response.order.table_name || 'N/A');
                    $('#orderType').text(response.order.order_type === 'advance' ? 'Advance Order' : 'Walk-in Order');
                    $('#paymentMethod').text(response.order.payment_method);
                    $('#orderDate').text(response.order.order_date);
                    $('#orderStatus').text(response.order.status);
                    
                    // Format and display amounts
                    $('#totalAmount').text(parseFloat(response.order.total_amount).toFixed(2));
                    
                    // Handle discount display
                    if (response.order.discount_amount > 0) {
                        $('.discount-info').show();
                        $('#discountAmount').text(parseFloat(response.order.discount_amount).toFixed(2));
                        $('#discountType').text(response.order.discount_type ? 
                            response.order.discount_type.replace('_', ' ').toUpperCase() : '');
                    } else {
                        $('.discount-info').hide();
                    }
                    
                    // Display final amount
                    $('#finalAmount').text(parseFloat(response.order.final_amount).toFixed(2));
                    
                    // Handle payment information
                    const amountPaid = parseFloat(response.order.amount_paid || 0);
                    const changeAmount = parseFloat(response.order.change_amount || 0);
                    const remainingBalance = parseFloat(response.order.remaining_balance || 0);
                    
                    // Debug logging
                    console.log('Amount Paid:', amountPaid);
                    console.log('Change Amount:', changeAmount);
                    
                    $('#amountPaid').text(amountPaid.toFixed(2));
                    $('#changeAmount').text(changeAmount.toFixed(2));
                    
                    if (remainingBalance > 0) {
                        $('.remaining-balance').show();
                        $('#remainingBalance').text(remainingBalance.toFixed(2));
                        
                        // Add payment button if there's remaining balance
                        $('#amountPaid').after(`
                            <button class="btn btn-primary btn-sm ml-2" onclick="showPaymentModal(
                                ${response.order.id}, 
                                ${response.order.final_amount}, 
                                ${amountPaid}, 
                                ${remainingBalance}
                            )">
                                Process Payment
                            </button>
                        `);
                    } else {
                        $('.remaining-balance').hide();
                    }
                    
                    // Populate order items table
                    let itemsHtml = '';
                    response.order.items.forEach(function(item) {
                        let addonsText = item.addons.map(addon => 
                            `${addon.name} (₱${parseFloat(addon.price).toFixed(2)})`
                        ).join(', ') || 'None';
                        
                        itemsHtml += `
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.quantity}</td>
                                <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td>${addonsText}</td>
                                <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    $('#orderItemsBody').html(itemsHtml);
                    
                    // Show the modal
                    $('#orderDetailsModal').modal('show');
                } else {
                    showAlert('Error', response.message || 'Failed to load order details', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
                console.log('Response:', xhr.responseText);
                showAlert('Error', 'Failed to load order details: ' + error, 'error');
            }
        });
    });

    // Finish Order Handler
    $('.finish-order').on('click', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        
        // First check if there's any remaining balance
        $.ajax({
            url: 'get_order_details.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                if(response.success) {
                    if(response.order.remaining_balance > 0) {
                        Swal.fire({
                            title: 'Cannot Complete Order',
                            text: 'This order has a remaining balance of ₱' + parseFloat(response.order.remaining_balance).toFixed(2) + '. Please process the full payment before marking the order as finished.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Process Payment',
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show payment modal with order details
                                $('#payment_order_id').val(response.order.id);
                                $('#total_amount').val('₱' + parseFloat(response.order.total_amount).toFixed(2));
                                $('#amount_paid').val('₱' + parseFloat(response.order.amount_paid).toFixed(2));
                                $('#remaining_balance').val('₱' + parseFloat(response.order.remaining_balance).toFixed(2));
                                $('#payment_amount').attr('max', response.order.remaining_balance);
                                $('#payment_amount').val('');
                                $('#payment_method').val('');
                                
                                // Show the payment modal
                                $('#paymentModal').modal('show');
                            }
                        });
                        return;
                    }

                    // Rest of the existing finish order code...
                    Swal.fire({
                        title: 'Confirm Order Completion',
                        text: 'Are you sure you want to mark this order as finished?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#dc3545',
                        confirmButtonText: 'Yes, finish it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'finish_order.php',
                                method: 'POST',
                                data: { 
                                    order_id: orderId,
                                    cashier_id: <?php echo $_SESSION['user_id']; ?> // Add cashier ID
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response.success) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: 'Order marked as finished successfully!',
                                            icon: 'success',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        showAlert('Error', response.message || 'Failed to finish order', 'error');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                    showAlert('Error', 'An error occurred while processing the request', 'error');
                                }
                            });
                        }
                    });
                } else {
                    showAlert('Error', response.message || 'Failed to check order details', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                showAlert('Error', 'An error occurred while checking order details', 'error');
            }
        });
    });

    // Process payment button handler
    $('#processPayment').click(function() {
        const form = $('#paymentForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const paymentAmount = parseFloat($('#payment_amount').val());
        const remainingBalance = parseFloat($('#remaining_balance').val().replace('₱', ''));
        
        if (isNaN(paymentAmount) || paymentAmount <= 0) {
            showAlert('Error', 'Please enter a valid payment amount', 'error');
            return;
        }
        
        if (paymentAmount > remainingBalance) {
            showAlert('Error', `Payment amount (₱${paymentAmount.toFixed(2)}) cannot exceed remaining balance (₱${remainingBalance.toFixed(2)})`, 'error');
            return;
        }
        
        // Disable the process payment button and show loading state
        const $processBtn = $('#processPayment');
        const originalText = $processBtn.text();
        $processBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: 'process_payment.php',
            method: 'POST',
            data: {
                order_id: $('#payment_order_id').val(),
                payment_amount: paymentAmount,
                payment_method: $('#payment_method').val()
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Update the status label in the table
                    const orderId = $('#payment_order_id').val();
                    const statusCell = $(`tr[data-order-id="${orderId}"] td:nth-last-child(2)`);
                    
                    if (response.payment_status === 'Paid') {
                        statusCell.html('<span class="label label-success">Paid</span>');
                    } else {
                        statusCell.html('<span class="label label-warning">Partial</span>');
                    }
                    
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#paymentModal').modal('hide');
                        location.reload();
                    });
                } else {
                    showAlert('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to process payment';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                showAlert('Error', errorMessage, 'error');
            },
            complete: function() {
                // Re-enable the process payment button
                $processBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Add input validation for payment amount
    $('#payment_amount').on('input', function() {
        const input = $(this);
        const value = parseFloat(input.val());
        const remainingBalance = parseFloat($('#remaining_balance').val().replace('₱', ''));
        
        if (value > remainingBalance) {
            input.val(remainingBalance.toFixed(2));
            showAlert('Warning', 'Payment amount cannot exceed remaining balance', 'warning');
        }
        
        // Ensure positive numbers only
        if (value < 0) {
            input.val('0.00');
        }
    });

    // Format payment amount on blur
    $('#payment_amount').on('blur', function() {
        const input = $(this);
        const value = parseFloat(input.val());
        if (!isNaN(value)) {
            input.val(value.toFixed(2));
        }
    });

    // Validate payment method selection
    $('#payment_method').on('change', function() {
        if (!$(this).val()) {
            showAlert('Warning', 'Please select a payment method', 'warning');
        }
    });

    // Handle payment modal close
    $('#paymentModal').on('hidden.bs.modal', function () {
        $('#orderDetailsModal').modal('show');
    });

    // Function to show payment modal
    function showPaymentModal(orderId, totalAmount, amountPaid, remainingBalance) {
        $('#payment_order_id').val(orderId);
        $('#total_amount').val('₱' + parseFloat(totalAmount).toFixed(2));
        $('#amount_paid').val('₱' + parseFloat(amountPaid).toFixed(2));
        $('#remaining_balance').val('₱' + parseFloat(remainingBalance).toFixed(2));
        $('#payment_amount').attr('max', remainingBalance);
        $('#payment_amount').val('');
        $('#payment_method').val('');
        
        $('#orderDetailsModal').modal('hide');
        $('#paymentModal').modal('show');
    }

    // Additional Orders Handler
    $('.add-orders').on('click', function(e) {
        e.preventDefault();
        const orderId = $(this).data('order-id');
        $('#additionalOrderId').val(orderId);
        
        // Load menu categories
        $.ajax({
            url: 'get_menu_categories.php',
            method: 'GET',
            success: function(response) {
                if(response.success) {
                    const categorySelect = $('#menuCategory');
                    categorySelect.empty();
                    categorySelect.append('<option value="">Select Category</option>');
                    
                    response.categories.forEach(function(category) {
                        categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }
            }
        });

        $('#addOrderModal').modal('show');
    });

    // Category Change Handler
    $('#menuCategory').change(function() {
        const categoryId = $(this).val();
        const menuItemSelect = $('#menuItem');
        
        if(categoryId) {
            $.ajax({
                url: 'get_menu_items.php',
                method: 'GET',
                data: { category_id: categoryId },
                success: function(response) {
                    if(response.success) {
                        menuItemSelect.empty().prop('disabled', false); // Enable the dropdown
                        menuItemSelect.append('<option value="">Select Item</option>');
                        
                        response.items.forEach(function(item) {
                            menuItemSelect.append(`
                                <option value="${item.id}" data-price="${item.price}">
                                    ${item.name} - ₱${parseFloat(item.price).toFixed(2)}
                                </option>
                            `);
                        });
                    } else {
                        menuItemSelect.prop('disabled', true);
                        menuItemSelect.html('<option value="">Error loading items</option>');
                        showAlert('Error', response.message || 'Failed to load menu items', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    menuItemSelect.prop('disabled', true);
                    menuItemSelect.html('<option value="">Error loading items</option>');
                    showAlert('Error', 'Failed to load menu items: ' + error, 'error');
                }
            });
        } else {
            menuItemSelect.prop('disabled', true);
            menuItemSelect.html('<option value="">Select Category First</option>');
        }
    });

    // Menu Item Change Handler
    $('#menuItem').change(function() {
        const itemId = $(this).val();
        
        if(itemId) {
            $.ajax({
                url: 'get_item_addons.php',
                method: 'GET',
                data: { item_id: itemId },
                success: function(response) {
                    if(response.success && response.addons.length > 0) {
                        const addonsList = $('#addonsList');
                        addonsList.empty();
                        
                        response.addons.forEach(function(addon) {
                            addonsList.append(`
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="addon-checkbox" 
                                               value="${addon.id}" 
                                               data-price="${addon.price}">
                                        ${addon.name} - ₱${addon.price}
                                    </label>
                                </div>
                            `);
                        });
                        $('#addonsSection').show();
                    } else {
                        $('#addonsSection').hide();
                    }
                }
            });
        } else {
            $('#addonsSection').hide();
        }
    });

    // Add Selected Item Button Handler
    $('#addSelectedItem').click(function() {
        const itemSelect = $('#menuItem');
        const item = itemSelect.find('option:selected');
        const quantity = parseInt($('#itemQuantity').val()) || 1;
        
        if(!item.val()) {
            showAlert('Warning', 'Please select an item', 'warning');
            return;
        }

        const selectedAddons = [];
        let addonsTotalPrice = 0;
        $('.addon-checkbox:checked').each(function() {
            const addonPrice = parseFloat($(this).data('price')) || 0;
            addonsTotalPrice += addonPrice;
            selectedAddons.push({
                id: $(this).val(),
                name: $(this).parent().text().trim(),
                price: addonPrice
            });
        });

        const itemPrice = parseFloat(item.data('price')) || 0;
        const totalItemPrice = (itemPrice + addonsTotalPrice) * quantity;

        // Add the item to the selected items list with proper price formatting
        $('#selectedItemsList').append(`
            <tr>
                <td>${item.text()}</td>
                <td>${quantity}</td>
                <td>${selectedAddons.map(addon => addon.name).join(', ') || 'None'}</td>
                <td>₱${totalItemPrice.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);

        // Update the total amount immediately after adding the item
        updateTotalAmount();
        resetItemForm();
    });

    // Remove Item Handler with total update
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateTotalAmount();
    });

    // Save Additional Orders Handler
    $('#saveAdditionalOrders').click(function() {
        const orderId = $('#additionalOrderId').val();
        const items = [];
        let totalAmount = 0;
        
        $('#selectedItemsList tr').each(function() {
            const $row = $(this);
            const price = parseFloat($row.find('td:eq(3)').text().replace('₱', '').replace(/,/g, '')) || 0;
            totalAmount += price;
            
            items.push({
                item_name: $row.find('td:first').text(),
                quantity: $row.find('td:eq(1)').text(),
                addons: $row.find('td:eq(2)').text(),
                price: price
            });
        });

        if(items.length === 0) {
            showAlert('Warning', 'Please add at least one item', 'warning');
            return;
        }

        Swal.fire({
            title: 'Save Additional Orders',
            text: 'Are you sure you want to save these additional orders?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show payment modal instead of direct save
                $('#additional_order_id').val(orderId);
                $('#additional_total_amount').val(`₱${totalAmount.toFixed(2)}`);
                $('#additional_payment_amount').val(totalAmount.toFixed(2));
                $('#additional_payment_method').val('');
                
                $('#addOrderModal').modal('hide');
                $('#additionalOrderPaymentModal').modal('show');
            }
        });
    });

    // Add Process Additional Payment Handler
    $('#processAdditionalPayment').click(function() {
        const form = $('#additionalOrderPaymentForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const orderId = $('#additional_order_id').val();
        const paymentAmount = parseFloat($('#additional_payment_amount').val());
        const paymentMethod = $('#additional_payment_method').val();
        const items = [];
        
        $('#selectedItemsList tr').each(function() {
            const $row = $(this);
            items.push({
                item_name: $row.find('td:first').text(),
                quantity: $row.find('td:eq(1)').text(),
                addons: $row.find('td:eq(2)').text(),
                price: parseFloat($row.find('td:eq(3)').text().replace('₱', '').replace(/,/g, ''))
            });
        });

        // Disable the button and show loading state
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Send both order and payment data
        $.ajax({
            url: 'save_additional_orders.php',
            method: 'POST',
            data: {
                order_id: orderId,
                items: JSON.stringify(items),
                payment_amount: paymentAmount,
                payment_method: paymentMethod
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Additional orders and payment processed successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#additionalOrderPaymentModal').modal('hide');
                        location.reload();
                    });
                } else {
                    showAlert('Error', response.message || 'Error processing order and payment', 'error');
                }
            },
            error: function() {
                showAlert('Error', 'Error processing order and payment', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Add payment validation
    $('#additional_payment_amount').on('input', function() {
        const input = $(this);
        const value = parseFloat(input.val());
        const totalAmount = parseFloat($('#additional_total_amount').val().replace('₱', '').replace(/,/g, ''));
        
        if (value < totalAmount) {
            input.addClass('is-invalid');
            showAlert('Warning', 'Payment amount must be at least equal to the total amount', 'warning');
        } else {
            input.removeClass('is-invalid');
        }
    });

    // Handle modal chain
    $('#additionalOrderPaymentModal').on('hidden.bs.modal', function() {
        $('#addOrderModal').modal('show');
    });

    // Helper Functions
    function updateTotalAmount() {
        let total = 0;
        $('#selectedItemsList tr').each(function() {
            const $row = $(this);
            const priceText = $row.find('td:eq(3)').text();
            // Remove ₱ symbol and any commas, then parse as float
            const price = parseFloat(priceText.replace(/[₱,]/g, '')) || 0;
            total += price;
        });

        // Update both the total cell and the total amount span
        $('#selectedItemsList').closest('table').find('tfoot').remove(); // Remove existing footer if any
        $('#selectedItemsList').closest('table').append(`
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                    <td colspan="2"><strong>₱${total.toFixed(2)}</strong></td>
                </tr>
            </tfoot>
        `);
    }

    function resetItemForm() {
        $('#menuItem').val('');
        $('#itemQuantity').val(1);
        $('.addon-checkbox').prop('checked', false);
        $('#addonsSection').hide();
    }

    // Edit Order Handler
    $('.edit-order').on('click', function(e) {
        e.preventDefault();
        const orderId = $(this).data('order-id');
        $('#editOrderId').val(orderId);
        
        // Reset and disable menu item select initially
        $('#editMenuItem').prop('disabled', true).html('<option value="">Select Category First</option>');
        
        // Load categories
        $.ajax({
            url: 'get_menu_categories.php',
            method: 'GET',
            success: function(response) {
                if(response.success) {
                    const categorySelect = $('#editMenuCategory');
                    categorySelect.empty();
                    categorySelect.append('<option value="">Select Category</option>');
                    
                    response.categories.forEach(function(category) {
                        categorySelect.append(`
                            <option value="${category.id}">
                                ${category.display_name || category.name}
                            </option>
                        `);
                    });
                    
                    // Load current order details
                    loadOrderDetails(orderId);
                } else {
                    showAlert('Error', response.message || 'Failed to load categories', 'error');
                }
            },
            error: function(xhr, status, error) {
                showAlert('Error', 'Failed to load categories: ' + error, 'error');
            }
        });
        
        $('#editOrderModal').modal('show');
    });

    // Category Change Handler
    $('#editMenuCategory').change(function() {
        const categoryId = $(this).val();
        const menuItemSelect = $('#editMenuItem');
        
        // Reset and show loading state
        menuItemSelect.prop('disabled', true);
        menuItemSelect.html('<option value="">Loading items...</option>');
        
        if (!categoryId) {
            menuItemSelect.html('<option value="">Select Category First</option>');
            return;
        }
        
        // Fetch menu items
        $.ajax({
            url: 'get_menu_items.php',
            method: 'GET',
            data: { category_id: categoryId },
            dataType: 'json',
            success: function(response) {
                if (response.success && Array.isArray(response.items)) {
                    menuItemSelect.empty();
                    menuItemSelect.append('<option value="">Select Item</option>');
                    
                    response.items.forEach(function(item) {
                        menuItemSelect.append(`
                            <option value="${item.id}" data-price="${item.price}">
                                ${item.name} - ₱${item.price.toFixed(2)}
                            </option>
                        `);
                    });
                    
                    menuItemSelect.prop('disabled', false);
                } else {
                    menuItemSelect.prop('disabled', true);
                    menuItemSelect.html('<option value="">Error loading items</option>');
                    console.error('Invalid response:', response);
                }
            },
            error: function(xhr, status, error) {
                menuItemSelect.prop('disabled', true);
                menuItemSelect.html('<option value="">Error loading items</option>');
                console.error('Ajax error:', error);
                console.log('Response:', xhr.responseText);
            }
        });
    });

    // Menu Item Change Handler
    $('#editMenuItem').change(function() {
        const itemId = $(this).val();
        
        if(itemId) {
            $.ajax({
                url: 'get_item_addons.php',
                method: 'GET',
                data: { item_id: itemId },
                success: function(response) {
                    if(response.success && response.addons.length > 0) {
                        const addonsList = $('#editAddonsList');
                        addonsList.empty();
                        
                        response.addons.forEach(function(addon) {
                            addonsList.append(`
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="addon-checkbox" 
                                               value="${addon.id}" 
                                               data-price="${addon.price}">
                                        ${addon.name} - ₱${addon.price}
                                    </label>
                                </div>
                            `);
                        });
                        $('#editAddonsSection').show();
                    } else {
                        $('#editAddonsSection').hide();
                    }
                }
            });
        } else {
            $('#editAddonsSection').hide();
        }
    });

    // Add Item to Order Handler
    $('#addEditItem').click(function() {
        const itemSelect = $('#editMenuItem option:selected');
        const quantity = parseInt($('#editItemQuantity').val()) || 1;
        
        if(!itemSelect.val()) {
            showAlert('Warning', 'Please select an item', 'warning');
            return;
        }

        const itemPrice = parseFloat(itemSelect.data('price')) || 0;
        let addonsTotalPrice = 0;
        const selectedAddons = [];
        
        $('.addon-checkbox:checked').each(function() {
            const addonPrice = parseFloat($(this).data('price')) || 0;
            addonsTotalPrice += addonPrice;
            selectedAddons.push({
                id: $(this).val(),
                name: $(this).parent().text().trim(),
                price: addonPrice
            });
        });

        const totalItemPrice = (itemPrice * quantity) + addonsTotalPrice;
        const addonsText = selectedAddons.length > 0 
            ? selectedAddons.map(addon => addon.name).join(', ')
            : 'None';

        $('#editOrderItems').append(`
            <tr>
                <td>${itemSelect.text()}</td>
                <td>${quantity}</td>
                <td>${addonsText}</td>
                <td>₱${totalItemPrice.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);

        updateEditTotalAmount();
        resetEditItemForm();
    });

    // Helper Functions
    function loadOrderDetails(orderId) {
        $.ajax({
            url: 'get_order_details.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                if(response.success) {
                    const tbody = $('#editOrderItems');
                    tbody.empty();
                    let totalAmount = 0;
                    
                    response.order.items.forEach(function(item) {
                        // Calculate item total including addons
                        const itemPrice = parseFloat(item.unit_price) || 0;
                        const quantity = parseInt(item.quantity) || 0;
                        let addonTotal = 0;
                        
                        // Calculate addons total if any
                        if (Array.isArray(item.addons)) {
                            item.addons.forEach(addon => {
                                addonTotal += parseFloat(addon.price) || 0;
                            });
                        }
                        
                        const itemTotal = (itemPrice * quantity) + addonTotal;
                        totalAmount += itemTotal;
                        
                        const addonsText = Array.isArray(item.addons) && item.addons.length > 0 
                            ? item.addons.map(addon => `${addon.name} (₱${parseFloat(addon.price).toFixed(2)})`).join(', ')
                            : 'None';
                        
                        tbody.append(`
                            <tr>
                                <td>${item.name}</td>
                                <td>${quantity}</td>
                                <td>${addonsText}</td>
                                <td>₱${itemTotal.toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    // Update total amount
                    $('#editTotalAmount').text(`₱${totalAmount.toFixed(2)}`);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading order details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load order details. Please try again.'
                });
            }
        });
    }

    function updateEditTotalAmount() {
        let total = 0;
        $('#editOrderItems tr').each(function() {
            const priceText = $(this).find('td:eq(3)').text();
            const price = parseFloat(priceText.replace(/[₱,]/g, '')) || 0;
            total += price;
        });
        $('#editTotalAmount').text(`₱${total.toFixed(2)}`);
    }

    function resetEditItemForm() {
        $('#editMenuItem').val('');
        $('#editItemQuantity').val(1);
        $('.addon-checkbox').prop('checked', false);
        $('#editAddonsSection').hide();
    }

    // Remove item handler
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateEditTotalAmount();
    });

    // Add this helper function for flash messages
    function showFlashMessage(message, type) {
        const iconMap = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Swal.fire({
            title: type.charAt(0).toUpperCase() + type.slice(1),
            text: message,
            icon: iconMap[type] || 'info',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }

    // Add data-order-id attribute to each order row for status updates
    $('.finish-order').each(function() {
        const orderId = $(this).data('order-id');
        $(this).closest('tr').attr('data-order-id', orderId);
    });
    
    // Print Order Handler
    $('.print-order').on('click', function(e) {
        e.preventDefault();
        const orderId = $(this).data('order-id');
        
        // Show loading state
        const $printBtn = $(this);
        const originalHtml = $printBtn.html();
        $printBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Call generate_receipt.php
        $.ajax({
            url: 'generate_receipt.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                // Create a new window for the receipt
                const printWindow = window.open('', '_blank', 'width=400,height=600');
                if (printWindow) {
                    printWindow.document.write(response);
                    printWindow.document.close();
                    
                    // Wait for content to load then print
                    printWindow.onload = function() {
                        printWindow.focus();
                        printWindow.print();
                        
                        // Close window after printing (optional - comment out if you want it to stay open)
                        printWindow.onafterprint = function() {
                            printWindow.close();
                        };
                    };
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please allow pop-ups to print the receipt'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Print Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to generate receipt. Please try again.'
                });
            },
            complete: function() {
                // Reset button state
                $printBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // After successful order update, update the order details in the table
    function updateOrderRow(orderId, updatedData) {
        const $orderRow = $(`tr[data-order-id="${orderId}"]`);
        
        // Update the order details cell
        let itemsHtml = '';
        let totalAmount = 0;
        
        updatedData.items.forEach(item => {
            const itemTotal = item.quantity * item.unit_price;
            totalAmount += itemTotal;
            
            itemsHtml += `
                <div>
                    ${item.name} (${item.quantity} x ₱${parseFloat(item.unit_price).toFixed(2)})
                    ${item.addons !== 'None' ? '<br>Add-ons: ' + item.addons : ''}
                </div>
            `;
        });

        $orderRow.find('.order-details').html(itemsHtml);
        
        // Update amounts
        if (updatedData.discount_type) {
            const discountAmount = totalAmount * 0.20;
            const finalAmount = totalAmount - discountAmount;
            
            $orderRow.find('.total-amount').html(`
                ₱${totalAmount.toFixed(2)}<br>
                <span class="text-success">Discount (20%): ₱${discountAmount.toFixed(2)}</span><br>
                <strong class="text-primary">Final Amount: ₱${finalAmount.toFixed(2)}</strong>
            `);
        } else {
            $orderRow.find('.total-amount').text(`₱${totalAmount.toFixed(2)}`);
        }
    }

    // Save Order Changes Handler
    $('#saveOrderChanges').click(function() {
        const orderId = $('#editOrderId').val();
        const items = [];
        
        // Collect all items from the table
        $('#editOrderItems tr').each(function() {
            const $row = $(this);
            const itemName = $row.find('td:eq(0)').text();
            const quantity = parseInt($row.find('td:eq(1)').text());
            const addons = $row.find('td:eq(2)').text();
            const priceText = $row.find('td:eq(3)').text();
            const price = parseFloat(priceText.replace('₱', '').replace(',', ''));
            
            items.push({
                name: itemName,
                quantity: quantity,
                addons: addons === 'None' ? [] : addons.split(', '),
                price: price
            });
        });

        if (items.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Items',
                text: 'Please add at least one item to the order'
            });
            return;
        }

        // Confirm before saving
        Swal.fire({
            title: 'Save Changes?',
            text: 'Are you sure you want to update this order?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save changes'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const $saveBtn = $('#saveOrderChanges');
                const originalText = $saveBtn.text();
                $saveBtn.prop('disabled', true).text('Saving...');

                // Send update request
                $.ajax({
                    url: 'update_order.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        items: JSON.stringify(items),
                        total_amount: parseFloat($('#editTotalAmount').text().replace('₱', ''))
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Order updated successfully',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Close modal and refresh page
                                $('#editOrderModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to update order'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Save Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save changes. Please try again.'
                        });
                    },
                    complete: function() {
                        // Reset button state
                        $saveBtn.prop('disabled', false).text(originalText);
                    }
                });
            }
        });
    });
    // Function to show alerts
    function showAlert(type, message) {
        Swal.fire({
            icon: type === 'success' ? 'success' : 'error',
            title: type === 'success' ? 'Success' : 'Error',
            text: message,
            confirmButtonColor: '#3085d6'
        });
    }
    
    // Table selection functionality
    $('.select-table').on('click', function(e) {
        e.preventDefault();
        const orderId = $(this).data('order-id');
        $('#table_order_id').val(orderId);
        
        // Fetch available tables
        $.ajax({
            url: 'get_tables.php',
            method: 'GET',
            dataType: 'json',  // Expect JSON response
            success: function(data) {
                if (data.success) {
                    const tablesList = $('#available_tables');
                    tablesList.empty();
                    
                    // Add tables to the list
                    data.tables.forEach(function(table) {
                        if (!table.is_occupied) {
                            tablesList.append(
                                `<div class="col-md-3 mb-3">
                                    <button class="btn btn-outline-primary w-100 table-select-btn" 
                                            data-table-id="${table.id}" 
                                            data-table-number="${table.table_number}">
                                        Table ${table.table_number}
                                    </button>
                                 </div>`
                            );
                        }
                    });
                    
                    // Show modal
                    $('#tableSelectionModal').modal('show');
                } else {
                    showAlert('error', 'Failed to fetch tables: ' + (data.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('error', 'Failed to connect to server: ' + error);
            }
        });
    });
    
    // Handle table selection
    $(document).on('click', '.table-select-btn', function() {
        const tableId = $(this).data('table-id');
        const tableNumber = $(this).data('table-number');
        const orderId = $('#table_order_id').val();
        
        // Update order with selected table
        $.ajax({
            url: 'update_order_table.php',
            method: 'POST',
            dataType: 'json',  // Expect JSON response
            data: {
                order_id: orderId,
                table_id: tableId,
                table_number: tableNumber
            },
            success: function(data) {
                if (data.success) {
                    showAlert('success', 'Table assigned successfully');
                    $('#tableSelectionModal').modal('hide');
                    
                    // Refresh the page to show updated table assignment
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert('error', 'Failed to assign table: ' + (data.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('error', 'Failed to connect to server: ' + error);
            }
        });
    });
});
</script> 

<!-- Table Selection Modal -->
<div class="modal fade" id="tableSelectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select a Table</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="table_order_id">
                <div class="row" id="available_tables">
                    <!-- Tables will be loaded here dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>