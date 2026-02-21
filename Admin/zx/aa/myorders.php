<?php 
session_start();
require 'db_con.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Casa Estela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ffc107;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #ffdb4d);
            padding: 2rem 0;
            margin-bottom: 2rem;
            margin-top: 55px;
            text-align: center;
            color: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .order-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            overflow: hidden;
            position: relative;
            border: none;
        }

        .order-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #ffdb4d);
        }

        .card-header-custom {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 219, 77, 0.05));
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 193, 7, 0.2);
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-pending {
            background: linear-gradient(135deg, var(--warning-color), #ffdb4d);
            color: #000;
        }

        .status-confirmed {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: #fff;
        }

        .status-completed {
            background: linear-gradient(135deg, var(--info-color), #00bcd4);
            color: #fff;
        }

        .status-cancelled {
            background: linear-gradient(135deg, var(--danger-color), #c82333);
            color: #fff;
        }

        .order-type-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .food-icon {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        }

        .room-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .service-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .info-item i {
            width: 20px;
            color: var(--primary-color);
            margin-right: 0.75rem;
        }

        .price-tag {
            background: linear-gradient(135deg, var(--primary-color), #ffdb4d);
            color: #000;
            padding: 0.75rem 1.25rem;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .discount-tag {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: #fff;
            padding: 0.25rem 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .section-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.5rem;
        }

        .order-reference {
            font-family: 'Courier New', monospace;
            background: rgba(255, 193, 7, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .payment-info {
            background: rgba(248, 249, 250, 0.8);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .payment-info .info-item {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .order-card {
                margin-bottom: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    <?php include 'message_box.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1 class="mb-0"><i class="fas fa-shopping-bag me-3"></i>My Orders</h1>
            <p class="mb-0">Track and manage all your orders in one place</p>
        </div>
    </div>

    <div class="container mb-4">
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="orderTypeFilter">
                        <option value="">All Types</option>
                        <option value="food">Food Orders</option>
                        <option value="room">Room Orders</option>
                        <option value="service">Service Orders</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-warning w-100" onclick="resetFilters()">
                        <i class="fas fa-undo-alt"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row" id="ordersContainer">
            <?php
            try {
                $stmt = $pdo->prepare("
                    SELECT * FROM orders_table 
                    WHERE user_id = :user_id 
                    ORDER BY order_at DESC
                ");
                
                $stmt->execute([':user_id' => $_SESSION['user_id']]);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($orders)) {
                    echo '<div class="col-12"><div class="empty-state fade-in">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>No Orders Yet</h3>
                        <p class="text-muted">You haven\'t placed any orders yet.</p>
                        <a href="cafes.php" class="btn btn-warning btn-action">
                            <i class="fas fa-utensils"></i> Browse Menu
                        </a>
                    </div></div>';
                } else {
                    foreach($orders as $order) {
                        $statusClass = match(strtolower($order['status'])) {
                            'pending' => 'status-pending',
                            'confirmed' => 'status-confirmed',
                            'completed' => 'status-completed',
                            'cancelled' => 'status-cancelled',
                            default => 'status-pending'
                        };

                        // Determine order type and icon
                        $orderType = 'food';
                        $iconClass = 'food-icon';
                        $typeIcon = 'fa-utensils';
                        $typeName = 'Food Order';
                        
                        if (stripos($order['order_type'] ?? $order['type_of_order'] ?? '', 'room') !== false) {
                            $orderType = 'room';
                            $iconClass = 'room-icon';
                            $typeIcon = 'fa-bed';
                            $typeName = 'Room Order';
                        } elseif (stripos($order['order_type'] ?? $order['type_of_order'] ?? '', 'service') !== false) {
                            $orderType = 'service';
                            $iconClass = 'service-icon';
                            $typeIcon = 'fa-concierge-bell';
                            $typeName = 'Service Order';
                        }
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4 order-item" 
                             data-type="<?php echo $orderType; ?>" 
                             data-status="<?php echo strtolower($order['status']); ?>" 
                             data-date="<?php echo date('Y-m-d', strtotime($order['order_at'] ?? $order['date_time'])); ?>">
                            <div class="order-card fade-in">
                                <div class="card-header-custom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="order-type-icon <?php echo $iconClass; ?>">
                                            <i class="fas <?php echo $typeIcon; ?>"></i>
                                        </div>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                            <?php echo ucfirst($order['status'] ?? 'Pending'); ?>
                                        </span>
                                    </div>
                                    <h6 class="mt-2 mb-1"><?php echo $typeName; ?></h6>
                                    <div class="order-reference">#<?php echo htmlspecialchars($order['order_id']); ?></div>
                                </div>
                                <div class="card-body-custom">
                                    <div class="info-item">
                                        <i class="fas fa-calendar-day"></i>
                                        <span><?php echo date('M d, Y', strtotime($order['order_at'] ?? $order['date_time'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo date('g:i A', strtotime($order['order_at'] ?? $order['date_time'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($order['firstname'] . ' ' . $order['lastname']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($order['contact']); ?></span>
                                    </div>
                                    
                                    <div class="price-tag">
                                        ₱<?php echo number_format($order['total'], 2); ?>
                                    </div>
                                    
                                    <?php if (!empty($order['discount_type']) && $order['discount_amount'] > 0): ?>
                                        <div class="discount-tag">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo htmlspecialchars($order['discount_type']); ?> - ₱<?php echo number_format($order['discount_amount'], 2); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="payment-info">
                                        <div class="info-item">
                                            <i class="fas fa-credit-card"></i>
                                            <span><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <span>Paid: ₱<?php echo number_format($order['amount_paid'], 2); ?></span>
                                        </div>
                                        <?php if ($order['balance'] > 0): ?>
                                        <div class="info-item text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>Balance: ₱<?php echo number_format($order['balance'], 2); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-action flex-fill" onclick="showOrderDetails(<?php echo htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>)">
                                            <i class="fas fa-info-circle me-1"></i> Details
                                        </button>
                                        <?php if (strtolower($order['status']) === 'pending'): ?>
                                            <button class="btn btn-danger btn-action" onclick="cancelOrder('<?php echo $order['order_id']; ?>')">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            } catch(PDOException $e) {
                echo '<div class="col-12"><div class="alert alert-danger">Error loading orders: ' . $e->getMessage() . '</div></div>';
            }
            ?>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="order-details-content">
                        <!-- Content will be populated dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const dateFilter = document.getElementById('dateFilter');
            const orderTypeFilter = document.getElementById('orderTypeFilter');

            function applyFilters() {
                const status = statusFilter.value.toLowerCase();
                const date = dateFilter.value;
                const type = orderTypeFilter.value;

                const allOrders = document.querySelectorAll('.order-item');

                allOrders.forEach(order => {
                    let show = true;

                    // Filter by type
                    if (type && order.dataset.type !== type) {
                        show = false;
                    }

                    // Filter by status
                    if (status && order.dataset.status !== status) {
                        show = false;
                    }

                    // Filter by date
                    if (date && order.dataset.date !== date) {
                        show = false;
                    }

                    order.style.display = show ? 'block' : 'none';
                });

                // Check if any orders are visible
                const visibleOrders = document.querySelectorAll('.order-item[style="display: block"]');
                const container = document.getElementById('ordersContainer');
                
                if (visibleOrders.length === 0 && !container.querySelector('.empty-state')) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'col-12';
                    emptyDiv.innerHTML = '<div class="empty-state"><i class="fas fa-filter"></i><h3>No Results Found</h3><p class="text-muted">No orders match your filter criteria.</p></div>';
                    container.appendChild(emptyDiv);
                } else if (visibleOrders.length > 0) {
                    const emptyState = container.querySelector('.empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }
                }
            }

            statusFilter.addEventListener('change', applyFilters);
            dateFilter.addEventListener('change', applyFilters);
            orderTypeFilter.addEventListener('change', applyFilters);
        });

        function resetFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('orderTypeFilter').value = '';
            
            const allOrders = document.querySelectorAll('.order-item');
            allOrders.forEach(order => {
                order.style.display = 'block';
            });
            
            // Remove empty state if exists
            const emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                emptyState.remove();
            }
        }

        function showOrderDetails(order) {
            const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            const content = document.querySelector('.order-details-content');
            
            let paymentStatus = 'Pending';
            if (parseFloat(order.amount_paid) >= parseFloat(order.total)) {
                paymentStatus = 'Paid';
            } else if (parseFloat(order.amount_paid) > 0) {
                paymentStatus = 'Partial';
            }
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Order Information</h6>
                        <div class="info-item">
                            <i class="fas fa-receipt"></i>
                            <span><strong>Order ID:</strong> #${order.order_id}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span><strong>Type:</strong> ${order.order_type || order.type_of_order || 'General Order'}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Date:</strong> ${new Date(order.order_at || order.date_time).toLocaleDateString()}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Time:</strong> ${new Date(order.order_at || order.date_time).toLocaleTimeString()}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-info-circle"></i>
                            <span><strong>Status:</strong> <span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Customer Information</h6>
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span><strong>Name:</strong> ${order.firstname} ${order.lastname}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><strong>Contact:</strong> ${order.contact}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span><strong>Email:</strong> ${order.email}</span>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="text-primary mb-3">Payment Details</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-calculator"></i>
                            <span><strong>Total Amount:</strong> ₱${parseFloat(order.total).toFixed(2)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-money-bill"></i>
                            <span><strong>Downpayment:</strong> ₱${parseFloat(order.downpayment || 0).toFixed(2)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span><strong>Amount Paid:</strong> ₱${parseFloat(order.amount_paid).toFixed(2)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-balance-scale"></i>
                            <span><strong>Balance:</strong> ₱${parseFloat(order.balance).toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-credit-card"></i>
                            <span><strong>Payment Method:</strong> ${order.payment_method || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-wallet"></i>
                            <span><strong>Payment Option:</strong> ${order.payment_option || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Payment Status:</strong> <span class="badge bg-${paymentStatus === 'Paid' ? 'success' : paymentStatus === 'Partial' ? 'warning' : 'secondary'}">${paymentStatus}</span></span>
                        </div>
                        ${order.change_amount && parseFloat(order.change_amount) > 0 ? `
                        <div class="info-item">
                            <i class="fas fa-exchange-alt"></i>
                            <span><strong>Change:</strong> ₱${parseFloat(order.change_amount).toFixed(2)}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
                
                ${order.discount_type && parseFloat(order.discount_amount) > 0 ? `
                <hr>
                <h6 class="text-primary mb-3">Discount Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span><strong>Discount Type:</strong> ${order.discount_type}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-percentage"></i>
                            <span><strong>Discount Amount:</strong> ₱${parseFloat(order.discount_amount).toFixed(2)}</span>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
            
            modal.show();
        }

        function cancelOrder(orderId) {
            Swal.fire({
                title: 'Cancel Order',
                text: 'Are you sure you want to cancel this order? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would typically make an AJAX call to cancel the order
                    // For now, we'll just show a success message
                    Swal.fire(
                        'Cancelled!',
                        'Your order has been cancelled.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                }
            });
        }
    </script>
</body>
</html>