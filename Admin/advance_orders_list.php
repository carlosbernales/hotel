<?php
require_once "db.php";
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Set page title
$pageTitle = "Table Reservation Orders";

// Get status filter if any
$statusFilter = '';
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($con, $_GET['status']);
    $statusFilter = "AND o.status = '$status'";
}

// Get all orders with a table_id (connected to table bookings)
$query = "SELECT o.*, tb.booking_date, tb.booking_time, tb.name as customer_name 
          FROM orders o 
          LEFT JOIN table_bookings tb ON o.table_id = tb.id 
          WHERE o.table_id IS NOT NULL AND o.table_id > 0 
          $statusFilter
          ORDER BY o.order_date DESC";

$result = mysqli_query($con, $query);

// Check if query was successful
if (!$result) {
    $error = "Error retrieving table reservation orders: " . mysqli_error($con);
}

// Count rows
$orderCount = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle; ?> - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .order-body {
            padding: 15px;
        }
        .order-footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #ddd;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-completed {
            background-color: #28a745;
            color: #fff;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        .modal-title {
            font-weight: bold;
            color: #333;
        }
        .item-row {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
        .empty-state {
            text-align: center;
            padding: 50px 0;
        }
        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .empty-state h4 {
            color: #666;
            margin-bottom: 10px;
        }
        .empty-state p {
            color: #999;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Table Reservation Orders</li>
            </ol>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Table Reservation Orders</h1>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>All Table Reservation Orders (<?php echo $orderCount; ?>)</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Filter by Status <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="advance_orders_list.php">All Orders</a></li>
                                        <li><a href="advance_orders_list.php?status=pending">Pending</a></li>
                                        <li><a href="advance_orders_list.php?status=finished">Completed</a></li>
                                        <li><a href="advance_orders_list.php?status=cancelled">Cancelled</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if ($orderCount > 0): ?>
                            <div class="row">
                                <?php while($order = mysqli_fetch_assoc($result)): ?>
                                <div class="col-md-6">
                                    <div class="order-card">
                                        <div class="order-header">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h4>Order #<?php echo $order['id']; ?></h4>
                                                    <small>
                                                        <?php 
                                                        $orderDate = new DateTime($order['order_date']);
                                                        echo $orderDate->format('F j, Y - g:i A'); 
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Customer:</strong> <?php echo isset($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'N/A'; ?></p>
                                                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Table:</strong> #<?php echo $order['table_id']; ?></p>
                                                    <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <p><strong>Reservation Date:</strong> 
                                                        <?php 
                                                        if (isset($order['booking_date']) && isset($order['booking_time'])) {
                                                            $bookingDate = new DateTime($order['booking_date'] . ' ' . $order['booking_time']);
                                                            echo $bookingDate->format('F j, Y - g:i A');
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order-footer">
                                            <button type="button" class="btn btn-primary btn-sm view-details" data-order-id="<?php echo $order['id']; ?>">
                                                <i class="fa fa-eye"></i> View Details
                                            </button>
                                            
                                            <?php if ($order['status'] == 'pending'): ?>
                                            <button type="button" class="btn btn-success btn-sm mark-completed" data-order-id="<?php echo $order['id']; ?>">
                                                <i class="fa fa-check"></i> Mark as Completed
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm cancel-order" data-order-id="<?php echo $order['id']; ?>">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fa fa-shopping-cart"></i>
                                <h4>No Table Reservation Orders Found</h4>
                                <p>There are currently no orders associated with table reservations in the system.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading order details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add required scripts -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        // View Order Details
        $('.view-details').click(function() {
            const orderId = $(this).data('order-id');
            
            // Show the modal with loading state
            $('#orderDetailsModal').modal('show');
            
            // Fetch order details from the server
            $.ajax({
                url: 'get_order_details.php',
                type: 'GET',
                data: { order_id: orderId },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (data.success) {
                            const order = data.order;
                            const orderItems = data.order_items;
                            
                            // Format the order date
                            const orderDate = new Date(order.order_date);
                            const formattedDate = orderDate.toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            // Build the order details HTML
                            let html = `
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6>Order Information</h6>
                                        <p><strong>Order ID:</strong> #${order.id}</p>
                                        <p><strong>Date:</strong> ${formattedDate}</p>
                                        <p><strong>Status:</strong> <span class="badge status-badge status-${order.status.toLowerCase()}">${order.status}</span></p>
                                        <p><strong>Payment Method:</strong> ${order.payment_method}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Customer Information</h6>
                                        <p><strong>Name:</strong> ${order.customer_name || 'N/A'}</p>
                                        <p><strong>Table ID:</strong> #${order.table_id || 'N/A'}</p>
                                    </div>
                                </div>
                                
                                <h6>Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-right">Price</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            if (orderItems && orderItems.length > 0) {
                                orderItems.forEach(item => {
                                    const totalPrice = parseFloat(item.price) * parseInt(item.quantity);
                                    html += `
                                        <tr>
                                            <td>${item.item_name}</td>
                                            <td class="text-center">${item.quantity}</td>
                                            <td class="text-right">₱${parseFloat(item.price).toFixed(2)}</td>
                                            <td class="text-right">₱${totalPrice.toFixed(2)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                html += `
                                    <tr>
                                        <td colspan="4" class="text-center">No items found for this order</td>
                                    </tr>
                                `;
                            }
                            
                            html += `
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="order-summary mt-4">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Subtotal:</strong></td>
                                                    <td class="text-right">₱${parseFloat(order.total_amount).toFixed(2)}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Amount Paid:</strong></td>
                                                    <td class="text-right">₱${parseFloat(order.amount_paid || 0).toFixed(2)}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total:</strong></td>
                                                    <td class="text-right"><strong>₱${parseFloat(order.total_amount).toFixed(2)}</strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Update the modal body
                            $('#orderDetailsModal .modal-body').html(html);
                            
                        } else {
                            $('#orderDetailsModal .modal-body').html(`
                                <div class="alert alert-danger">
                                    <i class="fa fa-exclamation-circle"></i> ${data.message || 'Error retrieving order details'}
                                </div>
                            `);
                        }
                    } catch (error) {
                        console.error('Error processing response:', error);
                        $('#orderDetailsModal .modal-body').html(`
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-circle"></i> Error processing response
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#orderDetailsModal .modal-body').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> Error retrieving order details from server
                        </div>
                    `);
                }
            });
        });
        
        // Mark Order as Completed
        $('.mark-completed').click(function() {
            const orderId = $(this).data('order-id');
            
            Swal.fire({
                title: 'Mark as Completed?',
                text: 'Are you sure you want to mark this order as completed?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Complete It'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send request to update order status
                    $.ajax({
                        url: 'update_order_status.php',
                        type: 'POST',
                        data: { 
                            order_id: orderId,
                            status: 'finished'
                        },
                        success: function(response) {
                            try {
                                const data = typeof response === 'string' ? JSON.parse(response) : response;
                                
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Order has been marked as completed',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message || 'Failed to update order status',
                                        icon: 'error'
                                    });
                                }
                            } catch (error) {
                                console.error('Error processing response:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while processing the response',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while communicating with the server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
        
        // Cancel Order
        $('.cancel-order').click(function() {
            const orderId = $(this).data('order-id');
            
            Swal.fire({
                title: 'Cancel Order?',
                text: 'Are you sure you want to cancel this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Cancel It'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send request to update order status
                    $.ajax({
                        url: 'update_order_status.php',
                        type: 'POST',
                        data: { 
                            order_id: orderId,
                            status: 'cancelled'
                        },
                        success: function(response) {
                            try {
                                const data = typeof response === 'string' ? JSON.parse(response) : response;
                                
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Order has been cancelled',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message || 'Failed to cancel order',
                                        icon: 'error'
                                    });
                                }
                            } catch (error) {
                                console.error('Error processing response:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while processing the response',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while communicating with the server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
    </script>
</body>
</html> 