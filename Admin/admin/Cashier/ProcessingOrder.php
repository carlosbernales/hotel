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
// Modified query to show only processing orders
$sql = "SELECT orders.*, order_items.item_name, order_items.quantity, order_items.unit_price, 
        order_item_addons.addon_name, order_item_addons.addon_price,
        CONCAT(users.firstname, ' ', users.lastname) as customer_name,
        GROUP_CONCAT(DISTINCT CONCAT(order_items.item_name, ' (', order_items.quantity, ')') SEPARATOR ', ') as items,
        GROUP_CONCAT(DISTINCT CONCAT(order_item_addons.addon_name, ' - ₱', order_item_addons.addon_price) SEPARATOR ', ') as addons,
        orders.discount_type, orders.discount_amount as stored_discount_amount
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id
        LEFT JOIN order_items ON orders.id = order_items.order_id 
        LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
        WHERE orders.status = 'processing'
        GROUP BY orders.id, orders.user_id, orders.payment_method, orders.total_amount, orders.order_date, orders.status,
                 orders.order_type, order_items.item_name, order_items.quantity, order_items.unit_price,
                 users.firstname, users.lastname, orders.discount_type, orders.discount_amount
        ORDER BY orders.order_date DESC";

$result = $connection->query($sql);

// Initialize orders array
$orders = [];

// Group the results by order
while ($row = $result->fetch_assoc()) {
    $orderId = $row['id'];
    if (!isset($orders[$orderId])) {
        // Calculate discount if applicable
        $originalAmount = floatval($row['total_amount']);
        $discountType = $row['discount_type'];
        $discountedAmount = $originalAmount;
        $discountAmount = floatval($row['stored_discount_amount'] ?? 0);
        
        if (($discountType === 'senior_citizen' || $discountType === 'pwd') && $discountAmount === 0) {
            $discountAmount = $originalAmount * 0.20; // 20% discount
            $discountedAmount = $originalAmount - $discountAmount;
        } else if ($discountAmount > 0) {
            $discountedAmount = $originalAmount - $discountAmount;
        }

        $orders[$orderId] = [
            'user_id' => $row['user_id'],
            'customer_name' => $row['customer_name'],
            'payment_method' => $row['payment_method'],
            'total_amount' => $originalAmount,
            'discount_type' => $discountType,
            'discount_amount' => $discountAmount,
            'final_amount' => $discountedAmount,
            'order_date' => $row['order_date'],
            'status' => $row['status'],
            'order_type' => $row['order_type'],
            'items' => []
        ];
    }
    
    // Add items and their addons
    $itemKey = $row['item_name'] . '_' . $row['quantity'];
    if (!isset($orders[$orderId]['items'][$itemKey])) {
        $orders[$orderId]['items'][$itemKey] = [
            'name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'unit_price' => $row['unit_price'],
            'addons' => []
        ];
    }
    
    if (!empty($row['addon_name']) && !empty($row['addon_price'])) {
        $orders[$orderId]['items'][$itemKey]['addons'][] = [
            'name' => $row['addon_name'],
            'price' => $row['addon_price']
        ];
    }
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
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
                            <th>Order Type</th>
                            <th>Payment Method</th>
                            <th>Total Amount</th>
                            <th>Ordered At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $orderId => $order): ?>
                            <tr data-order-id="<?php echo $orderId; ?>">
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        (<?php echo htmlspecialchars($item['quantity']); ?> x ₱<?php echo htmlspecialchars($item['unit_price']); ?>)<br>
                                        <?php if (!empty($item['addons'])): ?>
                                            <em>Add-ons:</em>
                                            <?php foreach ($item['addons'] as $addon): ?>
                                                <?php echo htmlspecialchars($addon['name']); ?> (₱<?php echo htmlspecialchars($addon['price']); ?>)<br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                        $orderType = strtolower($order['order_type'] ?? '');
                                        $displayType = $orderType === 'advance' ? 'Advance Order' : 'Walk-in Order';
                                        echo htmlspecialchars($displayType);
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td>
                                    <?php if (!empty($order['discount_type'])): ?>
                                        <del>₱<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></del><br>
                                        <span class="text-success">
                                            <strong>Discount (20%): </strong>
                                            ₱<?php echo htmlspecialchars(number_format($order['discount_amount'], 2)); ?>
                                        </span><br>
                                        <span class="text-primary">
                                            <strong>Final Amount: </strong>
                                            ₱<?php echo htmlspecialchars(number_format($order['final_amount'], 2)); ?>
                                        </span>
                                        <br>
                                        <span class="label label-info">
                                            <?php echo ucwords(str_replace('_', ' ', $order['discount_type'])); ?>
                                        </span>
                                    <?php else: ?>
                                        ₱<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>
                                    <span class="label label-info">Processing</span>
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
                                        <button class="btn btn-warning btn-sm edit-order" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Edit Order">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm finish-order" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Mark Order as Finished">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm add-orders" 
                                                data-order-id="<?php echo $orderId; ?>" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Add Additional Orders">
                                            <i class="fas fa-plus"></i>
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
                        <p><strong>Order Type:</strong> <span id="orderType"></span></p>
                        <p><strong>Payment Method:</strong> <span id="paymentMethod"></span></p>
                        <p><strong>Order Date:</strong> <span id="orderDate"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Summary</h5>
                        <p><strong>Status:</strong> <span id="orderStatus" class="label label-info"></span></p>
                        <p><strong>Total Amount:</strong> <span id="totalAmount"></span></p>
                        <p><strong>Discount:</strong> <span id="discount"></span></p>
                        <p><strong>Final Amount:</strong> <span id="finalAmount"></span></p>
                        <p><strong>Amount Paid:</strong> <span id="amountPaid"></span></p>
                        <p><strong>Change:</strong> <span id="changeAmount"></span></p>
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
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                                    <td colspan="2"><strong id="totalAmount">₱0.00</strong></td>
                                </tr>
                            </tfoot>
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
                    <input type="hidden" name="order_id" id="editOrderId">
                    
                    <!-- Order Items Table -->
                    <div class="form-group">
                        <label>Order Items:</label>
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
                <button type="button" class="btn btn-primary" id="saveOrderChanges">Save Changes</button>
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

<style>
del {
    color: #999;
    font-size: 0.9em;
}

.text-success {
    color: #5cb85c;
}

.text-primary {
    color: #337ab7;
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
.main {
    margin-top: 0;
    padding-top: 0;
}

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
                    // Existing modal population code
                    $('#customerName').text(response.order.customer_name === 'N/A' ? 'Walk-in Customer' : response.order.customer_name);
                    const orderType = response.order.order_type === 'advance' ? 'Advance Order' : 'Walk-in Order';
                    $('#orderType').text(orderType);
                    $('#paymentMethod').text(response.order.payment_method);
                    $('#orderDate').text(response.order.order_date);
                    $('#orderStatus').text(response.order.status);
                    $('#totalAmount').text('₱' + parseFloat(response.order.total_amount).toFixed(2));
                    
                    // Handle remaining balance display
                    if(response.order.remaining_balance > 0) {
                        $('#amountPaid').html(`
                            <span class="text-warning">
                                Partial Payment: ₱${parseFloat(response.order.amount_paid).toFixed(2)}
                            </span>
                            <button class="btn btn-primary btn-sm ml-2" onclick="showPaymentModal(
                                ${response.order.id}, 
                                ${response.order.total_amount}, 
                                ${response.order.amount_paid}, 
                                ${response.order.remaining_balance}
                            )">
                                Process Payment
                            </button>
                        `);
                        $('#changeAmount').html(`
                            <span class="text-danger">
                                Remaining: ₱${parseFloat(response.order.remaining_balance).toFixed(2)}
                            </span>
                        `);
                    } else {
                        $('#amountPaid').html(`
                            <span class="text-success">
                                ₱${parseFloat(response.order.amount_paid).toFixed(2)}
                            </span>
                        `);
                        $('#changeAmount').html(`
                            <span class="text-primary">
                                ₱${parseFloat(response.order.change_amount || 0).toFixed(2)}
                            </span>
                        `);
                    }

                    // Show the modal
                    $('#orderDetailsModal').modal('show');
                } else {
                    showAlert('Error', response.message || 'Failed to load order details', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
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
                                data: { order_id: orderId },
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
                        menuItemSelect.empty();
                        menuItemSelect.append('<option value="">Select Item</option>');
                        
                        response.items.forEach(function(item) {
                            menuItemSelect.append(`<option value="${item.id}" data-price="${item.price}">${item.name} - ₱${item.price}</option>`);
                        });
                        menuItemSelect.prop('disabled', false);
                    }
                }
            });
        } else {
            menuItemSelect.prop('disabled', true);
            menuItemSelect.html('<option value="">Select Item First</option>');
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
        const quantity = parseInt($('#itemQuantity').val());
        
        if(!item.val()) {
            alert('Please select an item');
            return;
        }

        const selectedAddons = [];
        let addonsTotalPrice = 0;
        $('.addon-checkbox:checked').each(function() {
            const addonPrice = parseFloat($(this).data('price'));
            addonsTotalPrice += addonPrice;
            selectedAddons.push({
                id: $(this).val(),
                name: $(this).parent().text().trim(),
                price: addonPrice
            });
        });

        const itemPrice = parseFloat(item.data('price'));
        const totalItemPrice = (itemPrice + addonsTotalPrice) * quantity;

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
        
        $('#selectedItemsList tr').each(function() {
            const $row = $(this);
            items.push({
                item_name: $row.find('td:first').text(),
                quantity: $row.find('td:eq(1)').text(),
                addons: $row.find('td:eq(2)').text(),
                price: parseFloat($row.find('td:eq(3)').text().replace('₱', ''))
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
                $.ajax({
                    url: 'save_additional_orders.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        items: JSON.stringify(items)
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Additional orders saved successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#addOrderModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            showAlert('Error', response.message || 'Error saving additional orders', 'error');
                        }
                    },
                    error: function() {
                        showAlert('Error', 'Error saving additional orders', 'error');
                    }
                });
            }
        });
    });

    // Helper Functions
    function updateTotalAmount() {
        let total = 0;
        $('#selectedItemsList tr').each(function() {
            const priceText = $(this).find('td:eq(3)').text();
            const price = parseFloat(priceText.replace('₱', '').replace(',', '')) || 0;
            total += price;
        });
        $('#totalAmount').text(`₱${total.toFixed(2)}`);
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
        
        // Fetch order details
        $.ajax({
            url: 'get_order_details.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                console.log('Order details response:', response); // Debug log
                
                if(response.success && response.order && response.order.items) {
                    const items = response.order.items;
                    const tbody = $('#editOrderItems');
                    tbody.empty();
                    
                    items.forEach(function(item) {
                        if (!item.id) {
                            console.error('Item missing ID:', item);
                            return;
                        }
                        
                        const addons = item.addons && item.addons.length > 0 
                            ? item.addons.map(addon => addon.name + ' - ₱' + addon.price).join(', ')
                            : 'None';
                        
                        tbody.append(`
                            <tr data-item-id="${item.id}">
                                <td>${item.name}</td>
                                <td>
                                    <input type="number" class="form-control quantity-input" 
                                           value="${item.quantity}" min="1" 
                                           data-original-quantity="${item.quantity}"
                                           data-price="${item.unit_price}">
                                </td>
                                <td>${addons}</td>
                                <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-order-item"
                                            data-item-id="${item.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    updateEditTotalAmount();
                    $('#editOrderModal').modal('show');
                } else {
                    showAlert('Error', response.message || 'Unknown error', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
                showAlert('Error', 'Failed to load order details: ' + error, 'error');
            }
        });
    });

    // Update quantity handler
    $(document).on('change', '.quantity-input', function() {
        const row = $(this).closest('tr');
        const quantity = parseInt($(this).val());
        const price = parseFloat($(this).data('price'));
        const subtotal = quantity * price;
        
        row.find('td:eq(3)').text(`₱${subtotal.toFixed(2)}`);
        updateEditTotalAmount();
    });

    // Remove order item handler
    $(document).on('click', '.remove-order-item', function() {
        const row = $(this).closest('tr');
        const itemId = $(this).data('item-id') || row.data('item-id');
        const orderId = $('#editOrderId').val();

        Swal.fire({
            title: 'Remove Item',
            text: 'Are you sure you want to remove this item?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'update_order_items.php',
                    method: 'POST',
                    data: {
                        action: 'delete',
                        delete_item_id: itemId,
                        order_id: orderId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            row.remove();
                            updateEditTotalAmount();
                            Swal.fire({
                                title: 'Success!',
                                text: 'Item removed successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            showAlert('Error', response.message || 'Error removing item', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', error);
                        showAlert('Error', 'Error removing item: ' + error, 'error');
                    }
                });
            }
        });
    });

    // Save order changes
    $('#saveOrderChanges').click(function() {
        const orderId = $('#editOrderId').val();
        const items = [];
        
        $('#editOrderItems tr').each(function() {
            const $row = $(this);
            items.push({
                item_id: $row.data('item-id'),
                quantity: $row.find('.quantity-input').val()
            });
        });
        
        $.ajax({
            url: 'update_order_items.php',
            method: 'POST',
            data: {
                order_id: orderId,
                items: JSON.stringify(items)
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Order updated successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editOrderModal').modal('hide');
                        location.reload();
                    });
                } else {
                    showAlert('Error', response.message || 'Error updating order', 'error');
                }
            },
            error: function() {
                showAlert('Error', 'Error updating order', 'error');
            }
        });
    });

    // Helper function to update total amount in edit modal
    function updateEditTotalAmount() {
        let total = 0;
        $('#editOrderItems tr').each(function() {
            const priceText = $(this).find('td:eq(3)').text();
            const price = parseFloat(priceText.replace('₱', '').replace(',', '')) || 0;
            total += price;
        });
        $('#editTotalAmount').text(`₱${total.toFixed(2)}`);
    }

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

    // Add data-order-id to each order row for status updates
    $('.finish-order').each(function() {
        const orderId = $(this).data('order-id');
        $(this).closest('tr').attr('data-order-id', orderId);
    });
    
    // Print Order Handler
    $('.print-order').on('click', function(e) {
        e.preventDefault();
        const orderId = $(this).data('order-id');
        // Open print page in a new window
        window.open('print_order.php?order_id=' + orderId, '_blank');
    });
});
</script> 