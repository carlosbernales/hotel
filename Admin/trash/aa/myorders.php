<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'db_con.php';

// Check if database connection is established
if (!isset($con) || !($con instanceof mysqli)) {
    die('Database connection failed. Please check your database configuration.');
}

// Check if connection is valid
if (mysqli_connect_errno()) {
    die('Database connection error: ' . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$reasons = [
    'Change Order',
    'Long Wait Time',
    'Payment Issues',
    'Changed My Mind',
    'Other'
];

try {
    // Main orders query
    $query = "
        SELECT o.*
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ";

    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die('Error in prepare statement: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    if (!mysqli_stmt_execute($stmt)) {
        die('Error executing statement: ' . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die('Error getting result set: ' . mysqli_error($con));
    }
    
    $orders = [];
    while ($order = mysqli_fetch_assoc($result)) {
        // Get items for this order
        $items_query = "
            SELECT oi.*, 
                   oi.unit_price as item_price,
                   (oi.quantity * oi.unit_price) as base_subtotal,
                   GROUP_CONCAT(CONCAT(oia.addon_name, ' (₱', FORMAT(oia.addon_price, 2), ')') SEPARATOR ', ') as addons,
                   SUM(COALESCE(oia.addon_price, 0)) as addon_total
            FROM order_items oi
            LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
            WHERE oi.order_id = ?
            GROUP BY oi.id
        ";
        
        $items_stmt = mysqli_prepare($con, $items_query);
        mysqli_stmt_bind_param($items_stmt, "i", $order['id']);
        mysqli_stmt_execute($items_stmt);
        $items_result = mysqli_stmt_get_result($items_stmt);
        
        $items = [];
        $order_total = 0;
        while ($item = mysqli_fetch_assoc($items_result)) {
            // Calculate total for each item including addons
            $item_total = ($item['quantity'] * $item['item_price']) + ($item['addon_total'] * $item['quantity']);
            $item['subtotal'] = $item_total;
            $order_total += $item_total;
            $items[] = $item;
        }
        
        $order['items'] = $items;
        $order['total_amount'] = $order_total;
        $orders[] = $order;
    }

} catch (Exception $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    die('Error fetching orders. Please try again later.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .page-header {
            background-color: #fff;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .filter-section {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .filter-section input[type="date"] {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.5rem;
        }

        .filter-btn {
            width: 100%;
            padding: 0.5rem 1rem;
            
        }

        .filter-btn:disabled {
            cursor: not-allowed;
        }

        .date-error {
            border-color: #dc3545 !important;
        }

        .date-error:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }

        .order-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .order-header {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .order-status {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-finished {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .order-content {
            padding: 1.5rem;
        }

        .order-info {
            margin-bottom: 1.5rem;
        }

        .order-info-label {
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 0.3rem;
        }

        .order-info-value {
            font-weight: 600;
            color: #2c3e50;
        }

        .order-items {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }

        .order-item {
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .item-price {
            font-weight: 600;
            color: #28a745;
        }

        .addon-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.3rem;
        }

        .total-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: #28a745;
            text-align: right;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }

        .empty-orders {
            text-align: center;
            padding: 3rem;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .empty-orders i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .empty-orders p {
            font-size: 1.1rem;
            color: #6c757d;
            margin: 0;
        }

        .policy-list {
            padding-left: 20px;
        }

        .policy-list li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
        }

        .policy-list li:before {
            content: '•';
            color: #dc3545;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

        .cancel-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .cancel-btn:disabled:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .form-check-input:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .form-check-input:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        .form-check-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .cancel-order-btn {
            margin-top: 10px;
        }

        .cancel-order-btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .order-card {
            position: relative;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        #cancelOrderModal .modal-content {
            border-radius: 10px;
        }

        #cancelOrderModal .modal-header {
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }

        #cancelReason {
            border-radius: 5px;
        }

        #otherReason {
            border-radius: 5px;
            resize: vertical;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }

        .active-order {
            border-left: 4px solid #ffc107;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .current-orders-section {
            margin-bottom: 3rem;
        }

        .past-orders-section .order-card {
            opacity: 0.9;
        }

        .status-pending {
            color: #ffc107;
        }

        .order-card {
            margin-bottom: 1.5rem;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .alert {
            border-radius: 8px;
            padding: 1rem;
        }

        .filter-section {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .filter-btn, .history-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .filter-btn {
            background-color: #007bff;
            border: none;
        }

        .filter-btn:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .history-btn {
            background-color: #6c757d;
            border: none;
        }

        .history-btn:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .filter-btn, .history-btn {
                margin-top: 1rem;
            }
        }

        .policy-list {
            list-style-type: none;
            padding-left: 0;
        }

        .policy-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .policy-list li:before {
            content: "•";
            color: #dc3545;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffecb5;
        }

        #orderHistoryModal .modal-dialog {
            max-width: 90%;
        }

        #orderHistoryModal .modal-body {
            padding: 2rem;
        }

        #orderHistoryModal .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: #fff;
        }

        #orderHistoryModal .order-card:last-child {
            margin-bottom: 0;
        }

        #orderHistoryModal .modal-content {
            border-radius: 12px;
        }

        #orderHistoryModal .modal-header {
            background-color: #f8f9fa;
            border-radius: 12px 12px 0 0;
        }

        #orderHistoryModal .modal-footer {
            background-color: #f8f9fa;
            border-radius: 0 0 12px 12px;
        }

        .payment-proof-img {
            max-height: 70vh;
            object-fit: contain;
        }

        .view-proof {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .view-proof:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Mobile-specific styles */
        @media (max-width: 767.98px) {
            .filter-section {
                padding: 1rem;
            }

            .filter-section .row {
                margin: 0;
            }

            .filter-section label {
                font-size: 0.9rem;
                margin-bottom: 0.3rem;
            }

            .filter-section input[type="date"] {
                font-size: 0.9rem;
                padding: 0.4rem;
            }

            .filter-section .btn {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            .filter-section .btn i {
                font-size: 0.8rem;
            }

            /* Stack date inputs in mobile */
            .filter-section .col-6 {
                padding: 0.25rem;
            }

            /* Add spacing between date inputs and buttons */
            .filter-section .col-12 + .col-12 {
                margin-top: 0.5rem;
            }
        }

        /* Desktop-specific styles */
        @media (min-width: 768px) {
            .filter-section {
                padding: 1.5rem;
            }

            .filter-section label {
                margin-bottom: 0.5rem;
            }

            .filter-section .btn {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        /* Common button styles */
        .filter-section .btn {
            transition: all 0.3s ease;
        }

        .filter-section .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-section .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .filter-section .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>
    <?php include 'message_box.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1 class="page-title">My Orders</h1>
        </div>
    </div>

    <div class="container">
        <div class="filter-section mb-4">
            <form id="filterForm" method="POST">
                <div class="row g-3">
                    <!-- Date Range Selection -->
                    <div class="col-12 col-md-6">
                        <div class="row g-2">
                            <div class="col-6">
                                <label>From:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $startDate ?>">
                            </div>
                            <div class="col-6">
                                <label>To:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $endDate ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="col-12 col-md-6">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter Orders
                                </button>
                            </div>
                            <div class="col-6">
                                <label class="d-none d-md-block">&nbsp;</label>
                                <button type="button" class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#orderHistoryModal">
                                    <i class="fas fa-history me-2"></i>Order History
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Current Orders Section -->
        <div class="current-orders-section mb-4">
            <h4 class="section-title">
                <i class="fas fa-clock me-2"></i>Current Orders
            </h4>
            <?php
            $hasCurrentOrders = false;
            foreach ($orders as $order):
                // Show orders that are either pending or processing in current orders
                if (in_array(strtolower($order['status']), ['pending', 'processing'])):
                    $hasCurrentOrders = true;
            ?>
                <div class="order-card active-order">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <span class="order-status status-<?= strtolower($order['status']) ?>">
                            <i class="fas fa-circle me-2"></i>
                            <?= ucfirst($order['status']) ?>
                        </span>
                        <span class="text-muted">
                            <i class="far fa-clock me-2"></i>
                            Ordered: <?= date('F d, Y h:i A', strtotime($order['order_date'])) ?>
                        </span>
                    </div>
                    
                    <div class="order-content">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="order-info">
                                    <div class="order-info-label">
                                        <i class="far fa-clock me-2"></i>Preparation Time
                                    </div>
                                    <div class="order-info-value">
                                        <span class="text-muted">At least 1 hour preparation</span>
                                    </div>
                                </div>
                                <div class="order-info">
                                    <div class="order-info-label">
                                        <i class="fas fa-wallet me-2"></i>Payment Method
                                    </div>
                                    <div class="order-info-value">
                                        <?= htmlspecialchars($order['payment_method']) ?>
                                    </div>
                                </div>
                                <div class="order-info">
                                    <div class="order-info-label">
                                        <i class="fas fa-sticky-note me-2"></i>Special Instructions
                                    </div>
                                    <div class="order-info-value">
                                        <?= !empty($order['pickup_notes']) ? htmlspecialchars($order['pickup_notes']) : 'No special instructions' ?>
                                    </div>
                                </div>
                                <div class="order-info">
                                    <div class="order-info-label">
                                        <i class="fas fa-receipt me-2"></i>Payment Reference
                                    </div>
                                    <div class="order-info-value">
                                        <?= !empty($order['payment_reference']) ? htmlspecialchars($order['payment_reference']) : 'N/A' ?>
                                    </div>
                                </div>
                                <?php if (!empty($order['payment_proof'])): ?>
                                    <div class="order-info">
                                        <div class="order-info-label">
                                            <i class="fas fa-file-image me-2"></i>Payment Proof
                                        </div>
                                        <div class="order-info-value">
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary view-proof" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#paymentProofModal" 
                                                    data-proof="<?= htmlspecialchars($order['payment_proof']) ?>">
                                                <i class="fas fa-eye me-2"></i>View Payment Proof
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <div class="order-items">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="item-name">
                                                    <?= htmlspecialchars($item['item_name']) ?> 
                                                    <span class="text-muted">×<?= $item['quantity'] ?></span>
                                                </span>
                                                <span class="item-price">₱<?= number_format($item['item_price'], 2) ?></span>
                                            </div>
                                            <?php if (!empty($item['addons'])): ?>
                                                <div class="addon-info">
                                                    <i class="fas fa-plus-circle me-1"></i>
                                                    <?= htmlspecialchars($item['addons']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-end">
                                                <span class="item-subtotal">Subtotal: ₱<?= number_format($item['subtotal'], 2) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="total-amount">
                                        Total Amount: ₱<?= number_format($order['total_amount'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        // Only show cancel button for pending orders within 5 minutes
                        if (strtolower($order['status']) === 'pending'):
                            $orderTime = new DateTime($order['order_date']);
                            $now = new DateTime();
                            $timeDiff = $now->getTimestamp() - $orderTime->getTimestamp();
                            $minutesPassed = $timeDiff / 60;
                            if ($minutesPassed <= 5):
                        ?>
                            <button type="button" 
                                    class="btn btn-danger cancel-btn mt-3" 
                                    onclick="showCancelModal(<?= $order['id'] ?>)">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        <?php 
                            else:
                        ?>
                            <button type="button" 
                                    class="btn btn-danger cancel-btn mt-3"
                                    disabled
                                    title="Orders can only be cancelled within 5 minutes of placing">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        <?php
                            endif;
                        elseif (strtolower($order['status']) === 'processing'):
                        ?>
                            <button type="button" 
                                    class="btn btn-danger cancel-btn mt-3"
                                    disabled
                                    title="Cannot cancel order while it's being processed">
                                <i class="fas fa-times me-2"></i>Cancel Order
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                endif;
            endforeach;
            
            if (!$hasCurrentOrders):
            ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No current orders
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add this modal HTML after your existing content -->
    <div class="modal fade" id="cancellationPolicyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Cancellation Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="policy-content">
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please read our cancellation policy carefully before proceeding.
                        </div>
                        <ul class="policy-list">
                            <li>Orders can only be cancelled within 5 minutes of placing the order</li>
                            <li>Orders that are already being prepared cannot be cancelled</li>
                            <li>Refunds for cancelled orders will be processed within 3-5 business days</li>
                            <li>Cancellation may be subject to a processing fee</li>
                        </ul>
                    </div>
                    <div class="mt-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="policyAcknowledgment">
                            <label class="form-check-label" for="policyAcknowledgment">
                                I have read and understand the cancellation policy
                            </label>
                        </div>
                        <p class="mb-0 text-danger">
                            <i class="fas fa-info-circle me-2"></i>
                            Are you sure you want to cancel this order?
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelButton">Yes, Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Order Cancellation Policy</h6>
                        <ul class="policy-list mb-0">
                            <li>Once cancelled, the order cannot be reinstated</li>
                            <li>No refund will be provided for cancelled orders</li>
                            <li>Cancellation is subject to admin approval</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="policyAgreement" required>
                            <label class="form-check-label" for="policyAgreement">
                                I have read and agree to the cancellation policy
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                        <select class="form-select" id="cancelReason" required>
                            <option value="">Select a reason</option>
                            <option value="Change Order">Change Order</option>
                            <option value="Long Wait Time">Long Wait Time</option>
                            <option value="Payment Issues">Payment Issues</option>
                            <option value="Changed My Mind">Changed My Mind</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="otherReasonDiv" style="display: none;">
                        <label for="otherReason" class="form-label">Please specify</label>
                        <textarea class="form-control" id="otherReason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="submitCancelButton" disabled>Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal structure before the closing </body> tag -->
    <div class="modal fade" id="orderHistoryModal" tabindex="-1" aria-labelledby="orderHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderHistoryModalLabel">
                        <i class="fas fa-history me-2"></i>Order History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $hasPastOrders = false;
                    foreach ($orders as $order):
                        // Show orders that are either finished or cancelled
                        if (in_array(strtolower($order['status']), ['finished', 'cancelled'])):
                            $hasPastOrders = true;
                    ?>
                        <div class="order-card mb-4">
                            <div class="order-header d-flex justify-content-between align-items-center">
                                <span class="order-status status-<?= strtolower($order['status']) ?>">
                                    <i class="fas fa-circle me-2"></i>
                                    <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                </span>
                                <span class="text-muted">
                                    <i class="far fa-clock me-2"></i>
                                    Ordered: <?= date('F d, Y h:i A', strtotime($order['order_date'])) ?>
                                </span>
                            </div>
                            
                            <div class="order-content">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="order-info">
                                            <div class="order-info-label">
                                                <i class="far fa-clock me-2"></i>Preparation Time
                                            </div>
                                            <div class="order-info-value">
                                                <span class="text-muted">At least 1 hour preparation</span>
                                            </div>
                                        </div>
                                        <div class="order-info">
                                            <div class="order-info-label">
                                                <i class="fas fa-wallet me-2"></i>Payment Method
                                            </div>
                                            <div class="order-info-value">
                                                <?= htmlspecialchars($order['payment_method']) ?>
                                            </div>
                                        </div>
                                        <div class="order-info">
                                            <div class="order-info-label">
                                                <i class="fas fa-sticky-note me-2"></i>Special Instructions
                                            </div>
                                            <div class="order-info-value">
                                                <?= !empty($order['pickup_notes']) ? htmlspecialchars($order['pickup_notes']) : 'No special instructions' ?>
                                            </div>
                                        </div>
                                        <div class="order-info">
                                            <div class="order-info-label">
                                                <i class="fas fa-receipt me-2"></i>Payment Reference
                                            </div>
                                            <div class="order-info-value">
                                                <?= !empty($order['payment_reference']) ? htmlspecialchars($order['payment_reference']) : 'N/A' ?>
                                            </div>
                                        </div>
                                        <?php if ($order['status'] === 'Cancelled'): ?>
                                            <div class="order-info">
                                                <div class="order-info-label text-danger">
                                                    <i class="fas fa-ban me-2"></i>Cancellation Reason
                                                </div>
                                                <div class="order-info-value text-danger">
                                                    <?= htmlspecialchars($order['cancellation_reason']) ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        Cancelled at: <?= date('F d, Y h:i A', strtotime($order['cancelled_at'])) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="order-items">
                                            <?php foreach ($order['items'] as $item): ?>
                                                <div class="order-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="item-name">
                                                            <?= htmlspecialchars($item['item_name']) ?> 
                                                            <span class="text-muted">×<?= $item['quantity'] ?></span>
                                                        </span>
                                                        <span class="item-price">₱<?= number_format($item['item_price'], 2) ?></span>
                                                    </div>
                                                    <?php if (!empty($item['addons'])): ?>
                                                        <div class="addon-info">
                                                            <i class="fas fa-plus-circle me-1"></i>
                                                            <?= htmlspecialchars($item['addons']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="text-end">
                                                        <span class="item-subtotal">Subtotal: ₱<?= number_format($item['subtotal'], 2) ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="total-amount">
                                                Total Amount: ₱<?= number_format($order['total_amount'], 2) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach;
                    
                    if (!$hasPastOrders):
                    ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No past orders found
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal before closing body tag -->
    <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentProofModalLabel">
                        <i class="fas fa-file-image me-2"></i>Payment Proof
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="paymentProofImage" src="" alt="Payment Proof" class="img-fluid payment-proof-img">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let currentOrderId = null;

    function showCancelModal(orderId) {
        currentOrderId = orderId;
        
        // Reset form elements
        document.getElementById('policyAgreement').checked = false;
        document.getElementById('cancelReason').value = '';
        document.getElementById('otherReason').value = '';
        document.getElementById('otherReasonDiv').style.display = 'none';
        document.getElementById('submitCancelButton').disabled = true;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const policyAgreement = document.getElementById('policyAgreement');
        const cancelReason = document.getElementById('cancelReason');
        const otherReason = document.getElementById('otherReason');
        const otherReasonDiv = document.getElementById('otherReasonDiv');
        const submitCancelButton = document.getElementById('submitCancelButton');

        // Handle policy agreement change
        policyAgreement.addEventListener('change', function() {
            updateSubmitButton();
        });

        // Handle reason selection change
        cancelReason.addEventListener('change', function() {
            otherReasonDiv.style.display = this.value === 'Other' ? 'block' : 'none';
            updateSubmitButton();
        });

        // Handle other reason input
        otherReason.addEventListener('input', function() {
            updateSubmitButton();
        });

        // Handle submit button click
        submitCancelButton.addEventListener('click', function() {
            if (!currentOrderId) {
                console.error('No order ID set');
                return;
            }

            const reason = cancelReason.value;
            const finalReason = reason === 'Other' ? otherReason.value : reason;

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a reason for cancellation'
                });
                return;
            }

            if (reason === 'Other' && !otherReason.value.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please specify your reason for cancellation'
                });
                return;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Cancel Order',
                text: 'Are you sure you want to cancel this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    submitCancelButton.disabled = true;
                    submitCancelButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cancelling...';

                    // Send cancellation request
                    fetch('cancel_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `orderId=${currentOrderId}&reason=${encodeURIComponent(finalReason)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close the modal first
                            const modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
                            modal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Order Cancelled',
                                text: 'Your order has been cancelled successfully.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to cancel order'
                            });
                            // Reset button state
                            submitCancelButton.disabled = false;
                            submitCancelButton.innerHTML = 'Cancel Order';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'There was an error processing your request'
                        });
                        // Reset button state
                        submitCancelButton.disabled = false;
                        submitCancelButton.innerHTML = 'Cancel Order';
                    });
                }
            });
        });

        function updateSubmitButton() {
            const reasonValue = cancelReason.value;
            const isValid = policyAgreement.checked && 
                           reasonValue && 
                           (reasonValue !== 'Other' || (reasonValue === 'Other' && otherReason.value.trim()));
            submitCancelButton.disabled = !isValid;
        }
    });

    // Add SweetAlert2 library if not already included
    if (typeof Swal === 'undefined') {
        const sweetalertScript = document.createElement('script');
        sweetalertScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(sweetalertScript);
    }

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        // Validate dates
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Dates',
                text: 'Please select both start and end dates'
            });
            return;
        }
        
        if (startDate > endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date Range',
                text: 'Start date cannot be later than end date'
            });
            return;
        }
        
        // Show loading state
        const filterBtn = this.querySelector('.filter-btn');
        const originalBtnText = filterBtn.innerHTML;
        filterBtn.disabled = true;
        filterBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Filtering...';
        
        // Submit form
        const formData = new FormData(this);
        
        fetch('myorders.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Replace the current page content with the filtered results
            document.documentElement.innerHTML = html;
            
            // Reinitialize Bootstrap components
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Orders Filtered',
                text: 'The orders have been filtered according to your selection',
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error filtering the orders. Please try again.'
            });
        })
        .finally(() => {
            // Reset button state
            filterBtn.disabled = false;
            filterBtn.innerHTML = originalBtnText;
        });
    });

    // Add date input validation
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });

    document.getElementById('end_date').addEventListener('change', function() {
        document.getElementById('start_date').max = this.value;
    });

    // Initialize with current date if no dates are selected
    if (!document.getElementById('start_date').value) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').value = today;
        document.getElementById('end_date').value = today;
    }

    // Handle payment proof modal
    document.addEventListener('DOMContentLoaded', function() {
        const paymentProofModal = document.getElementById('paymentProofModal');
        if (paymentProofModal) {
            paymentProofModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const proofPath = button.getAttribute('data-proof');
                const proofImage = document.getElementById('paymentProofImage');
                proofImage.src = '../../uploads/payment_proofs/' + proofPath;
            });
        }
    });
    </script>
</body>
</html>
