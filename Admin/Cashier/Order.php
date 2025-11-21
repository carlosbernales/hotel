<?php
require "db.php"; // Include the database connection file

// Add this helper function near the top of the file, after the database connection
function safeEscape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Modified query to show only pending orders
$sql = "SELECT orders.*, order_items.item_name, order_items.quantity, order_items.unit_price, 
        order_item_addons.addon_name, order_item_addons.addon_price,
        CONCAT(userss.first_name, ' ', userss.last_name) as customer_name,
        GROUP_CONCAT(DISTINCT CONCAT(order_items.item_name, ' (', order_items.quantity, ')') SEPARATOR ', ') as items,
        GROUP_CONCAT(DISTINCT CONCAT(order_item_addons.addon_name, ' - ₱', order_item_addons.addon_price) SEPARATOR ', ') as addons
        FROM orders 
        LEFT JOIN userss ON orders.user_id = userss.id
        LEFT JOIN order_items ON orders.id = order_items.order_id 
        LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
        WHERE orders.status = 'pending'
        GROUP BY orders.id, orders.user_id, orders.payment_method, orders.total_amount, orders.order_date, orders.status,
                 orders.order_type, order_items.item_name, order_items.quantity, order_items.unit_price,
                 userss.first_name, userss.last_name, orders.remaining_balance
        ORDER BY orders.id DESC, orders.order_date DESC";
$result = $connection->query($sql);

// Group the results by order
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'user_id' => $row['user_id'],
            'customer_name' => $row['customer_name'],
            'payment_method' => $row['payment_method'],
            'total_amount' => $row['total_amount'],
            'order_date' => $row['order_date'],
            'status' => $row['status'] ?? 'pending',
            'order_type' => $row['order_type'],
            'remaining_balance' => $row['remaining_balance'] ?? 0,
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
    
    // Only add addon if it exists
    if (!empty($row['addon_name']) && !empty($row['addon_price'])) {
        $orders[$orderId]['items'][$itemKey]['addons'][] = [
            'name' => $row['addon_name'],
            'price' => $row['addon_price']
        ];
    }
}
?>

<style>
.label {
    display: inline-block;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: bold;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 3px;
}
.label-warning {
    background-color: #f0ad4e;
    color: white;
}
.label-success {
    background-color: #5cb85c;
    color: white;
}
.label-info {
    background-color: #5bc0de;
    color: white;
}
.label-danger {
    background-color: #d9534f;
    color: white;
}
.label-default {
    background-color: #777;
    color: white;
}

/* Custom tooltip styling */
.tooltip {
    font-family: Arial, sans-serif;
}

.tooltip-inner {
    background-color: #333;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
}

/* Button spacing */
.actions-column button {
    margin-right: 5px;
}

.actions-column button:last-child {
    margin-right: 0;
}

/* Button hover effects */
.btn-warning:hover {
    background-color: #ec971f;
    border-color: #d58512;
}

.btn-danger:hover {
    background-color: #c9302c;
    border-color: #ac2925;
}

.btn-success:hover {
    background-color: #449d44;
    border-color: #398439;
}

.btn-info:hover {
    background-color: #31b0d5;
    border-color: #269abc;
}

/* Order Details Modal Styling */
.order-details {
    padding: 15px;
}

.order-details h5 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 2px solid #eee;
}

.customer-info,
.order-info,
.discount-info,
.items-info {
    margin-bottom: 20px;
}

.items-list {
    margin-top: 10px;
}

.item {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.item:last-child {
    border-bottom: none;
}

.addons {
    padding-left: 20px;
    margin-top: 5px;
    font-size: 0.9em;
    color: #666;
}

.addons ul {
    list-style: none;
    padding-left: 15px;
    margin: 5px 0;
}

.total-amount {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 2px solid #eee;
    text-align: right;
}

.total-amount h4 {
    color: #28a745;
    font-weight: bold;
}

.text-danger {
    color: #dc3545;
    font-weight: bold;
}

.total-amount p {
    margin-bottom: 10px;image.png
    font-size: 1.1em;
}

.total-amount .text-danger {
    font-size: 1.2em;
}

/* Add this CSS to adjust the main content area margin */
.main {
    margin-left: -10px; /* Adjust this value based on your sidebar width */
    padding: 20px;
    margin-top: 20px;
    transition: margin-left 0.3s ease;
}

/* Adjust for smaller screens if needed */
@media screen and (max-width: 768px) {
    .main {
        margin-left: 0;
    }
}

#paymentProofModal .modal-body {
    padding: 20px;
    text-align: center;
    background-color: #f8f9fa;
}

#paymentProofImage {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.modal-body.text-center {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Add these styles to your existing CSS */
.loading-indicator {
    padding: 20px;
}

.loading-indicator i {
    color: #007bff;
    margin-bottom: 10px;
}

.error-message {
    background-color: #fff3f3;
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}

#paymentProofImage {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 10px 0;
}
</style>

<div class="main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="POS.php">
            <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
                </a></li>
            <li class="active">Orders</li>
        </ol>
    </div><!--/.row-->

    <br>

    <div class="row">
        <div class="col-lg-12">
            <div id="success"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Orders
                    
                </div>
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
                            <tr>
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <strong><?php echo safeEscape($item['name']); ?></strong>
                                        (<?php echo safeEscape($item['quantity']); ?> x ₱<?php echo safeEscape($item['unit_price']); ?>)<br>
                                        <?php if (!empty($item['addons'])): ?>
                                            <em>Add-ons:</em>
                                            <?php foreach ($item['addons'] as $addon): ?>
                                                <?php echo safeEscape($addon['name']); ?> (₱<?php echo safeEscape($addon['price']); ?>)<br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <br>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo safeEscape($order['customer_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                        $orderType = strtolower($order['order_type'] ?? '');
                                        $displayType = '';
                                        
                                        switch($orderType) {
                                            case 'advance':
                                                $displayType = 'Advance Order';
                                                break;
                                            case 'walk-in':
                                                $displayType = 'Walk-in Order';
                                                break;
                                            case 'regular':
                                                $displayType = 'Online Order';
                                                break;
                                            default:
                                                $displayType = ucfirst($orderType) . ' Order';
                                        }
                                        
                                        echo safeEscape($displayType);
                                    ?>
                                </td>
                                <td><?php echo safeEscape($order['payment_method']); ?></td>
                                <td>₱<?php echo safeEscape($order['total_amount']); ?></td>
                                <td><?php echo safeEscape($order['order_date']); ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch($order['status']) {
                                        case 'pending':
                                            $statusClass = 'label-warning';
                                            break;
                                        case 'processing':
                                            $statusClass = 'label-info';
                                            break;
                                        case 'finished':
                                            $statusClass = 'label-success';
                                            break;
                                        case 'rejected':
                                            $statusClass = 'label-danger';
                                            break;
                                        default:
                                            $statusClass = 'label-default';
                                    }
                                    ?>
                                    <span class="label <?php echo $statusClass; ?>">
                                        <?php echo ucfirst(safeEscape($order['status'])); ?>
                                    </span>
                                </td>
                                <td class="actions-column">
                                    <?php 
                                    $orderType = strtolower($order['order_type'] ?? '');
                                    $status = strtolower($order['status'] ?? '');

                                    // For pending orders, show Accept/Reject for all order types
                                    if ($status === 'pending'): ?>
                                        <button class="btn btn-warning btn-sm accept-order" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                data-order-id="<?php echo safeEscape($orderId); ?>"
                                                title="Accept <?php echo ucfirst($orderType); ?> Order">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm reject-order" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                data-order-id="<?php echo safeEscape($orderId); ?>"
                                                title="Reject <?php echo ucfirst($orderType); ?> Order">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($status === 'processing'): ?>
                                        <button class="btn btn-success btn-sm finish-order" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                data-order-id="<?php echo safeEscape($orderId); ?>"
                                                title="Mark as Finished">
                                            <i class="fa fa-check"></i> Finish
                                        </button>
                                    <?php endif; ?>

                                    <button class="btn btn-info btn-sm view-details" 
                                            data-toggle="tooltip" 
                                            data-placement="top" 
                                            data-order-id="<?php echo safeEscape($orderId); ?>"
                                            title="View Order Details">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>    <!--/.main-->

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Order</h4>
                </div>
                <div class="modal-body">
                    <form id="editOrderForm">
                        <input type="hidden" name="editOrderId" id="editOrderId">
                        <div class="form-group">
                            <label>Payment Method:</label>
                            <select class="form-control" name="editPaymentMethod" id="editPaymentMethod">
                                <option value="gcash">GCash</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                        <div id="editOrderItems">
                            <!-- Menu items and add-ons will be loaded here dynamically -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveOrderChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Order Details Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Order Details</h4>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div class="modal fade" id="paymentProofModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Payment Proof</h4>
                </div>
                <div class="modal-body text-center">
                    <div class="loading-indicator" style="display: none;">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Loading image...</p>
                    </div>
                    <img id="paymentProofImage" src="" alt="Payment Proof" 
                         style="max-width: 100%; height: auto;" 
                         onerror="this.onerror=null; this.src='img/image-not-found.png'; $(this).next('.error-message').show();">
                    <div class="error-message" style="display: none; color: red; margin-top: 10px;">
                        Failed to load payment proof image. Please verify the file exists.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add SweetAlert2 CSS and JS in the head section, after Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Make sure jQuery and Bootstrap are loaded first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Add JavaScript code -->
    <script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Hide tooltip when clicking the button
        $('.actions-column button').click(function() {
            $(this).tooltip('hide');
        });

        // Accept Order
        $(document).on('click', '.accept-order', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            
            Swal.fire({
                title: 'Accept Order',
                text: 'Are you sure you want to accept this order?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.prop('disabled', true).tooltip('hide');
                    
                    $.ajax({
                        url: 'update_order_status.php',
                        method: 'POST',
                        data: { 
                            order_id: orderId,
                            status: 'processing'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Order accepted successfully!',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Error accepting order',
                                    icon: 'error'
                                });
                                button.prop('disabled', false);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error accepting order. Please try again.',
                                icon: 'error'
                            });
                            button.prop('disabled', false);
                        }
                    });
                }
            });
        });

        // Reject Order
        $(document).on('click', '.reject-order', function(e) {
            e.preventDefault();
            const orderId = $(this).data('order-id');
            $('#reject_order_id').val(orderId);
            $('#rejectOrderModal').modal('show');
        });

        // Handle Reject Order Confirmation
        $('#confirmReject').on('click', function() {
            const button = $(this);
            const orderId = $('#reject_order_id').val();
            const reason = $('#reject_reason').val().trim();
            
            if (!reason) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please provide a reason for rejection',
                    icon: 'error'
                });
                return;
            }
            
            button.prop('disabled', true);
            
            $.ajax({
                url: 'update_order_status.php',
                method: 'POST',
                data: { 
                    order_id: orderId,
                    status: 'rejected',
                    reject_reason: reason
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#rejectOrderModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Order rejected successfully!',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Error rejecting order',
                            icon: 'error'
                        });
                        button.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error rejecting order. Please try again.',
                        icon: 'error'
                    });
                    button.prop('disabled', false);
                }
            });
        });

        // Reset reject modal when closed
        $('#rejectOrderModal').on('hidden.bs.modal', function() {
            $('#reject_reason').val('');
            $('#reject_order_id').val('');
            $('#confirmReject').prop('disabled', false);
        });

        // Finish Order
        $(document).on('click', '.finish-order', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            
            Swal.fire({
                title: 'Finish Order',
                text: 'Are you sure you want to mark this order as finished?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, finish it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.prop('disabled', true);
                    
                    $.ajax({
                        url: 'update_order_status.php',
                        method: 'POST',
                        data: { 
                            order_id: orderId,
                            status: 'finished'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Order marked as finished!',
                                    icon: 'success'
                                }).then(() => {
                                    button.closest('tr').fadeOut(400, function() {
                                        $(this).remove();
                                    });
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Error finishing order',
                                    icon: 'error'
                                });
                                button.prop('disabled', false);
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error finishing order',
                                icon: 'error'
                            });
                            button.prop('disabled', false);
                        }
                    });
                }
            });
        });

        // Edit Order
        $(document).on('click', '.edit-order', function(e) {
            e.preventDefault();
            const orderId = $(this).data('order-id');
            $('#editOrderId').val(orderId);
            
            $.ajax({
                url: 'get_order_details.php',
                method: 'GET',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editPaymentMethod').val(response.order.payment_method);
                        $('#editOrderItems').html(response.response.items_html);
                        $('#editOrderModal').modal('show');
                    } else {
                        alert(response.message || 'Error loading order details');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error loading order details');
                }
            });
        });

        // Save Order Changes
        $(document).on('click', '#saveOrderChanges', function(e) {
            e.preventDefault();
            const button = $(this);
            button.prop('disabled', true);
            
            const formData = $('#editOrderForm').serialize();
            
            $.ajax({
                url: 'update_order.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editOrderModal').modal('hide');
                        alert('Order updated successfully!');
                        location.reload();
                    } else {
                        alert(response.message || 'Error updating order');
                        button.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error updating order');
                    button.prop('disabled', false);
                }
            });
        });

        // View Order Details
        $(document).on('click', '.view-details', function(e) {
            e.preventDefault();
            const orderId = $(this).data('order-id');
            
            $.ajax({
                url: 'get_order_details.php',
                method: 'GET',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Create HTML for order details
                        let detailsHtml = `
                            <div class="order-details">
                                <div class="customer-info">
                                    <h5>Customer Information</h5>
                                    <p><strong>Name:</strong> ${response.order.customer_name || 'N/A'}</p>
                                </div>

                                <div class="order-info">
                                    <h5>Order Information</h5>
                                    <p><strong>Order Type:</strong> ${response.order.order_type || 'N/A'}</p>
                                    <p><strong>Payment Method:</strong> ${response.order.payment_method || 'N/A'}</p>
                                    <p><strong>Payment Status:</strong> ${response.order.payment_status || 'N/A'}</p>
                                    <p><strong>Payment Reference:</strong> ${response.order.payment_reference || 'N/A'}</p>`;

                        detailsHtml += `
                                    <p><strong>Order Date:</strong> ${response.order.order_date || 'N/A'}</p>
                                    <p><strong>Status:</strong> <span class="label ${getStatusClass(response.order.status)}">${response.order.status || 'N/A'}</span></p>
                                </div>

                                <div class="items-info">
                                    <h5>Order Items</h5>
                                    <div class="items-list">`;

                        // Add order items
                        if (response.order.items && response.order.items.length > 0) {
                            response.order.items.forEach(item => {
                                detailsHtml += `
                                    <div class="item">
                                        <strong>${item.name}</strong>
                                        (${item.quantity} x ₱${item.unit_price})`;
                                
                                if (item.addons && item.addons.length > 0) {
                                    detailsHtml += `
                                        <div class="addons">
                                            <em>Add-ons:</em>
                                            <ul>`;
                                    item.addons.forEach(addon => {
                                        detailsHtml += `
                                            <li>${addon.name} - ₱${addon.price}</li>`;
                                    });
                                    detailsHtml += `
                                            </ul>
                                        </div>`;
                                }
                                detailsHtml += `
                                    </div>`;
                            });
                        }

                        detailsHtml += `
                                </div>
                            </div>

                            <div class="payment-summary">
                                <h5>Payment Summary</h5>
                                <p><strong>Subtotal:</strong> ₱${response.order.total_amount || '0.00'}</p>
                                <p><strong>Total Amount:</strong> ₱${response.order.total_amount || '0.00'}</p>`;

                                if (response.order.remaining_balance && parseFloat(response.order.remaining_balance) > 0) {
                                    detailsHtml += `
                                    <p><strong>Remaining Balance:</strong> <span class="text-danger">₱${response.order.remaining_balance}</span></p>`;
                                }

                                detailsHtml += `
                            </div>
                        </div>`;

                        // Update modal content
                        $('#orderDetailsContent').html(detailsHtml);
                        $('#viewOrderModal').modal('show');
                    } else {
                        alert(response.message || 'Error loading order details');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    alert('Failed to load order details: ' + error);
                }
            });
        });

        // Handle payment proof view button click
        $(document).on('click', '.view-proof', function(e) {
            e.preventDefault();
            const proofUrl = $(this).data('proof');
            const modal = $('#paymentProofModal');
            const imageElement = $('#paymentProofImage');
            
            // Show loading state
            imageElement.attr('src', 'img/loading.gif');
            modal.modal('show');
            
            // Debug log
            console.log('Attempting to load image from:', proofUrl);
            
            // Function to handle image load failure
            function handleImageError() {
                console.error('Failed to load image from:', proofUrl);
                imageElement.attr('src', 'img/image-not-found.png');
                
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load payment proof image. Please check the file path.',
                    icon: 'error',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
            
            // Test if file exists first using fetch
            fetch(proofUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Image not found');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const objectUrl = URL.createObjectURL(blob);
                    imageElement.attr('src', objectUrl);
                    console.log('Image loaded successfully');
                })
                .catch(error => {
                    console.error('Error loading image:', error);
                    handleImageError();
                });
        });

        // Helper function to get status class
        function getStatusClass(status) {
            switch(status.toLowerCase()) {
                case 'pending':
                    return 'label-warning';
                case 'processing':
                    return 'label-info';
                case 'finished':
                    return 'label-success';
                case 'rejected':
                    return 'label-danger';
                default:
                    return 'label-default';
            }
        }

        // Process Walk-in Order
        $(document).on('click', '.process-order', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            
            if (confirm('Are you sure you want to process this walk-in order?')) {
                button.prop('disabled', true).tooltip('hide');
                
                $.ajax({
                    url: 'update_order_status.php',
                    method: 'POST',
                    data: { 
                        order_id: orderId,
                        status: 'processing'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message || 'Error processing order');
                            button.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Error processing order');
                        button.prop('disabled', false);
                    }
                });
            }
        });

        // Handle reason selection change
        $('#reject_reason_select').on('change', function() {
            const selectedValue = $(this).val();
            const customReasonGroup = $('#custom_reason_group');
            
            if (selectedValue === 'custom') {
                customReasonGroup.show();
                $('#reject_reason').prop('required', true);
            } else {
                customReasonGroup.hide();
                $('#reject_reason').prop('required', false);
            }
        });

        // Modify the confirmReject click handler
        $('#confirmReject').on('click', function() {
            const button = $(this);
            const orderId = $('#reject_order_id').val();
            const reasonSelect = $('#reject_reason_select').val();
            let reason;
            
            if (!reasonSelect) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select a reason for rejection',
                    icon: 'error'
                });
                return;
            }
            
            if (reasonSelect === 'custom') {
                reason = $('#reject_reason').val().trim();
                if (!reason) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please provide a custom reason for rejection',
                        icon: 'error'
                    });
                    return;
                }
            } else {
                // Map the reason code to a human-readable message
                const reasonMessages = {
                    'out_of_stock': 'Items are out of stock',
                    'invalid_payment': 'Invalid payment proof provided',
                    'incomplete_payment': 'Incomplete payment',
                    'system_error': 'System error occurred'
                };
                reason = reasonMessages[reasonSelect];
            }
            
            button.prop('disabled', true);
            
            $.ajax({
                url: 'update_order_status.php',
                method: 'POST',
                data: { 
                    order_id: orderId,
                    status: 'rejected',
                    reject_reason: reason
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#rejectOrderModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Order rejected successfully!',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Error rejecting order',
                            icon: 'error'
                        });
                        button.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error rejecting order. Please try again.',
                        icon: 'error'
                    });
                    button.prop('disabled', false);
                }
            });
        });

        // Reset reject modal when closed
        $('#rejectOrderModal').on('hidden.bs.modal', function() {
            $('#reject_reason_select').val('');
            $('#reject_reason').val('');
            $('#custom_reason_group').hide();
            $('#reject_order_id').val('');
            $('#confirmReject').prop('disabled', false);
        });
    });
    </script>

    <!-- Add this modal HTML before the closing </div> of the main container -->
    <div class="modal fade" id="rejectOrderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Reject Order</h4>
                </div>
                <div class="modal-body">
                    <form id="rejectOrderForm">
                        <input type="hidden" name="order_id" id="reject_order_id">
                        <div class="form-group">
                            <label for="reject_reason_select">Select Reason for Rejection:</label>
                            <select class="form-control" id="reject_reason_select" name="reject_reason_select">
                                <option value="">-- Select a reason --</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="invalid_payment">Invalid Payment</option>
                                <option value="incomplete_payment">Incomplete Payment</option>
                                <option value="system_error">System Error</option>
                                <option value="custom">Other (Custom Reason)</option>
                            </select>
                        </div>
                        <div class="form-group" id="custom_reason_group" style="display: none;">
                            <label for="reject_reason">Custom Reason:</label>
                            <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3" placeholder="Enter custom reason for rejection"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject Order</button>
                </div>
            </div>
        </div>
    </div>
</div>



