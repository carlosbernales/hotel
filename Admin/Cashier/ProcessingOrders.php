<?php
require "db.php"; // Include the database connection file

// Query to get only processing orders
$sql = "SELECT orders.*, order_items.item_name, order_items.quantity, order_items.unit_price, 
        order_item_addons.addon_name, order_item_addons.addon_price,
        CONCAT(userss.first_name, ' ', userss.last_name) as customer_name,
        GROUP_CONCAT(DISTINCT CONCAT(order_items.item_name, ' (', order_items.quantity, ')') SEPARATOR ', ') as items,
        GROUP_CONCAT(DISTINCT CONCAT(order_item_addons.addon_name, ' - ₱', order_item_addons.addon_price) SEPARATOR ', ') as addons
        FROM orders 
        LEFT JOIN userss ON orders.user_id = userss.id
        LEFT JOIN order_items ON orders.id = order_items.order_id 
        LEFT JOIN order_item_addons ON order_items.id = order_item_addons.order_item_id
        WHERE orders.status = 'processing'
        GROUP BY orders.id, orders.user_id, orders.payment_method, orders.total_amount, orders.order_date, orders.status,
                 orders.order_type, order_items.item_name, order_items.quantity, order_items.unit_price,
                 userss.first_name, userss.last_name
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
                                    <span class="label label-info">Processing</span>
                                </td>
                                <td class="actions-column">
                                    <button class="btn btn-success btn-sm finish-order" 
                                            data-toggle="tooltip" 
                                            data-placement="top" 
                                            data-order-id="<?php echo htmlspecialchars($orderId); ?>"
                                            title="Mark as Finished">
                                        <i class="fa fa-check"></i> Finish
                                    </button>
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for button functionality -->
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Hide tooltip when clicking the button
    $('.actions-column button').click(function() {
        $(this).tooltip('hide');
    });

    // Finish Order
    $(document).on('click', '.finish-order', function(e) {
        e.preventDefault();
        const button = $(this);
        const orderId = button.data('order-id');
        
        if (confirm('Are you sure you want to mark this order as finished?')) {
            button.prop('disabled', true).tooltip('hide');
            
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
                        button.closest('tr').fadeOut(400, function() {
                            $(this).remove();
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
                if (response.status === 'success') {
                    $('#orderDetailsContent').html(response.response.details_html);
                    $('#viewOrderModal').modal('show');
                } else {
                    alert(response.message || 'Error loading order details');
                }
            },
            error: function() {
                alert('Error loading order details');
            }
        });
    });
});
</script>

<!-- Add necessary styles -->
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
.label-info {
    background-color: #5bc0de;
    color: white;
}
</style> 