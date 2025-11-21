<?php
require "db.php"; // Include the database connection file

// Modified query to show only pending orders
$sql = "SELECT orders.*, order_items.item_name, order_items.quantity, order_items.unit_price, 
        order_item_addons.addon_name, order_item_addons.addon_price,
        CONCAT(users.firstname, ' ', users.lastname) as customer_name,
        GROUP_CONCAT(DISTINCT CONCAT(order_items.item_name, ' (', order_items.quantity, ')') SEPARATOR ', ') as items,
        GROUP_CONCAT(DISTINCT CONCAT(order_item_addons.addon_name, ' - ₱', order_item_addons.addon_price) SEPARATOR ', ') as addons
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id
        LEFT JOIN order_items ON orders.id = order_items.order_id 
        LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
        WHERE orders.status = 'pending'
        GROUP BY orders.id, orders.user_id, orders.payment_method, orders.total_amount, orders.order_date, orders.status,
                 orders.order_type, order_items.item_name, order_items.quantity, order_items.unit_price,
                 users.firstname, users.lastname, orders.payment_proof, orders.payment_reference, orders.remaining_balance
        ORDER BY orders.order_date DESC";
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
            'payment_proof' => $row['payment_proof'] ?? '',
            'payment_reference' => $row['payment_reference'] ?? '',
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
</style>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
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
                                        
                                        echo htmlspecialchars($displayType);
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td>₱<?php echo htmlspecialchars($order['total_amount']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
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
                                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
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
                                                data-order-id="<?php echo htmlspecialchars($orderId); ?>"
                                                title="Accept <?php echo ucfirst($orderType); ?> Order">
                                            <i class="fa fa-check"></i> Accept
                                        </button>
                                        <button class="btn btn-danger btn-sm reject-order" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                data-order-id="<?php echo htmlspecialchars($orderId); ?>"
                                                title="Reject <?php echo ucfirst($orderType); ?> Order">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($status === 'processing'): ?>
                                        <button class="btn btn-success btn-sm finish-order" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                data-order-id="<?php echo htmlspecialchars($orderId); ?>"
                                                title="Mark as Finished">
                                            <i class="fa fa-check"></i> Finish
                                        </button>
                                    <?php endif; ?>

                                    <button class="btn btn-info btn-sm view-details" 
                                            data-toggle="tooltip" 
                                            data-placement="top" 
                                            data-order-id="<?php echo htmlspecialchars($orderId); ?>"
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
                    <img id="paymentProofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto;" onerror="this.onerror=null; this.src='img/image-not-found.png';">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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

        // Accept Order - Works for all order types
        $(document).on('click', '.accept-order', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            
            if (confirm('Are you sure you want to accept this order?')) {
                button.prop('disabled', true).tooltip('hide');
                
                $.ajax({
                    url: 'update_order_status.php',
                    method: 'POST',
                    data: { 
                        order_id: orderId,
                        status: 'processing'  // This will update the status to 'processing'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Reload the page to show updated status
                            location.reload();
                        } else {
                            alert(response.message || 'Error accepting order');
                            button.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Error accepting order');
                        button.prop('disabled', false);
                    }
                });
            }
        });

        // Reject Order - Works for all order types
        $(document).on('click', '.reject-order', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            
            if (confirm('Are you sure you want to reject this order?')) {
                button.prop('disabled', true).tooltip('hide');
                
                $.ajax({
                    url: 'update_order_status.php',
                    method: 'POST',
                    data: { 
                        order_id: orderId,
                        status: 'rejected'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message || 'Error rejecting order');
                            button.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Error rejecting order');
                        button.prop('disabled', false);
                    }
                });
            }
        });

        // Finish Order
        $(document).on('click', '.finish-order', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            
            if (confirm('Are you sure you want to mark this order as finished?')) {
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
                            // Simply remove the row and reload if needed
                            button.closest('tr').fadeOut(400, function() {
                                $(this).remove();
                                // Optionally reload the page to refresh the data
                                // location.reload();
                            });
                        } else {
                            alert(response.message || 'Error finishing order');
                            button.prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Error finishing order');
                        button.prop('disabled', false);
                    }
                });
            }
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

                        // Add payment proof if available
                        if (response.order.payment_proof) {
                            detailsHtml += `
                                    <p><strong>Payment Proof:</strong> 
                                        <button type="button" class="btn btn-sm btn-info view-proof" data-proof="${response.order.payment_proof}">
                                            <i class="fa fa-eye"></i> View Payment Proof
                                        </button>
                                    </p>`;
                        }

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
            // Clear previous image
            $('#paymentProofImage').attr('src', '');
            // Set new image with absolute path
            $('#paymentProofImage').attr('src', proofUrl);
            $('#paymentProofModal').modal('show');
            
            // Debug log
            console.log('Loading image from:', proofUrl);
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
    });
    </script>



