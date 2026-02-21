<?php
// Include necessary files
require_once 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get processing orders from database with item count and addons
$stmt = $pdo->prepare("
    SELECT 
        o.order_id,
        o.user_id,
        o.order_type,
        o.firstname,
        o.lastname,
        o.contact,
        o.email,
        o.date_time,
        o.total,
        o.balance,
        o.downpayment,
        o.amount_paid,
        o.discount_type,
        o.discount_percentage,
        o.discount_amount,
        o.change_amount,
        o.payment_method,
        o.status,
        (
            SELECT GROUP_CONCAT(
                DISTINCT CONCAT(ott2.table_name, ' (', ott2.table_number, ')')
                SEPARATOR ', '
            )
            FROM orders_table_type ott2
            WHERE ott2.table_booking_fk_id = o.id
        ) as table_info,
        COUNT(DISTINCT oi.id) as item_count,
        (
            SELECT GROUP_CONCAT(
                DISTINCT CONCAT(oi2.item_name, 
                    IF(
                        EXISTS (
                            SELECT 1 
                            FROM order_item_addons oia 
                            WHERE oia.order_item_fk_id = oi2.id
                        ),
                        CONCAT(' (', 
                            COALESCE(
                                (SELECT GROUP_CONCAT(DISTINCT oia2.addon_name SEPARATOR ', ')
                                 FROM order_item_addons oia2 
                                 WHERE oia2.order_item_fk_id = oi2.id),
                                ''
                            ),
                            ')'
                        ),
                        ''
                    )
                )
                SEPARATOR ', '
            )
            FROM order_items oi2
            WHERE oi2.order_fk_id = o.id
        ) as item_names
    FROM orders_table o
    LEFT JOIN order_items oi ON o.id = oi.order_fk_id
    LEFT JOIN orders_table_type ott ON o.id = ott.table_booking_fk_id
    WHERE o.status = 'processing'
    GROUP BY o.id, o.order_id, o.user_id, o.order_type, o.firstname, o.lastname, 
             o.contact, o.email, o.date_time, o.total, o.balance, o.downpayment, 
             o.amount_paid, o.discount_type, o.discount_percentage, o.discount_amount, 
             o.change_amount, o.payment_method, o.status
    ORDER BY o.date_time DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Orders - Casa Estela Boutique Hotel & Cafe</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <style>
        /* Primary color variables */
    :root {
        --primary-color: #b8860b;
        --primary-hover: #9a7209;
        --primary-light: rgba(184, 134, 11, 0.1);
        --primary-light-hover: rgba(184, 134, 11, 0.2);
    }
    
    body {
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 20px;
            padding: 20px;
            margin-top: 60px;
            transition: all 0.3s ease;
            width: calc(100% - 40px);
            box-sizing: border-box;
            max-width: 100%;
        }
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px 15px;
            }
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .card-header {
            background-color: var(--primary-color) !important;
            color: white;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        .table {
            width: 100%;
            table-layout: auto;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: var(--primary-color) !important;
            color: white;
            font-weight: 600;
            white-space: nowrap;
            padding: 15px 12px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
        }
        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            background-color: white;
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .table tbody tr:hover td {
            border-bottom-color: #e0e0ff;
        }
        .order-id-cell {
            font-weight: 700;
            color: #667eea;
            font-size: 0.9rem;
        }
        .customer-cell {
            font-weight: 500;
            color: #333;
        }
        .items-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .amount-cell {
            font-weight: 600;
            color: #2d3748;
            font-family: 'Courier New', monospace;
        }
        .discount-amount {
            color: var(--primary-color);
            font-weight: 600;
        }
        .table-info-cell {
            background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%);
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            color: #495057;
            text-align: center;
        }
        .order-type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .order-type-dinein {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }
        .order-type-takeout {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }
        .order-type-delivery {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }
        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #4a5568;
        }
        .payment-method-badge i {
            font-size: 0.9rem;
        }
        .date-time-cell {
            font-size: 0.85rem;
            color: #718096;
            white-space: nowrap;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-processing {
            background: linear-gradient(135deg, #ffd93d 0%, #ffb347 100%);
            color: #744210;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .action-buttons .btn {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-edit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
        }
        .btn-complete {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .btn-complete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.4);
        }
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border: none;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px 25px;
            border: none;
        }
        .no-orders-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        .no-orders-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }
        .no-orders-state h5 {
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-processing {
            background-color: #fff3cd;
            color: #856404;
        }
        .menu-item-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .menu-item-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }
        .menu-item-card:active {
            transform: translateY(-1px);
        }
        .menu-item-card .card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }
        .menu-item-card .card-text {
            font-size: 0.8rem;
            line-height: 1.3;
        }
        .menu-item-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .menu-item-card .input-group-sm .form-control {
            font-size: 0.875rem;
        }
        .order-summary-item {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .order-summary-item:last-child {
            border-bottom: none;
        }
        .nav-tabs {
            flex-wrap: wrap;
            border-bottom: 2px solid var(--primary-color);
        }
        .nav-tabs .nav-link {
            white-space: normal;
            text-align: center;
            min-width: 120px;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 0.375rem 0.375rem 0 0;
            margin-bottom: -1px;
            color: var(--primary-color);
            font-weight: 500;
        }
        .nav-tabs .nav-link:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
            isolation: isolate;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: #fff;
            border-color: var(--primary-color) var(--primary-color) #fff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h2><i class="fas fa-cogs me-2"></i>Processing Orders</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Processing Orders</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-ul me-2"></i>Processing Order List</span>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" id="orderSearchInput" placeholder="Search orders...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <colgroup>
                                <col style="width: 100px;">
                                <col style="min-width: 150px;">
                                <col style="width: 70px;">
                                <col style="min-width: 200px;">
                                <col style="width: 120px;">
                                <col style="width: 120px;">
                                <col style="width: 140px;">
                                <col style="width: 130px;">
                                <col style="width: 160px;">
                                <col style="width: 100px;">
                                <col style="width: 130px;">
                                <col style="width: 150px;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i> Order ID</th>
                                    <th><i class="fas fa-user me-1"></i> Customer</th>
                                    <th><i class="fas fa-box me-1"></i> Items</th>
                                    <th><i class="fas fa-utensils me-1"></i> Item Names</th>
                                    <th><i class="fas fa-money-bill me-1"></i> Total</th>
                                    <th><i class="fas fa-percentage me-1"></i> Discount</th>
                                    <th><i class="fas fa-table me-1"></i> Table Info</th>
                                    <th><i class="fas fa-concierge-bell me-1"></i> Order Type</th>
                                    <th><i class="fas fa-credit-card me-1"></i> Payment</th>
                                    <th><i class="fas fa-calendar me-1"></i> Date</th>
                                    <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                    <th><i class="fas fa-cogs me-1"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($orders) > 0): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="order-id-cell">#<?= htmlspecialchars($order['order_id']) ?></td>
                                            <td class="customer-cell"><?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname'] ?? 'Walk-in Customer') ?></td>
                                            <td class="text-center"><?= $order['item_count'] ?? 0 ?> items</td>
                                            <td class="items-cell" title="<?= htmlspecialchars($order['item_names'] ?? 'N/A') ?>"><?= htmlspecialchars($order['item_names'] ?? 'N/A') ?></td>
                                            <td class="amount-cell">₱<?= number_format($order['total'] ?? 0, 2) ?></td>
                                            <td class="discount-cell">
                                                <?php 
                                                $discountInfo = [];
                                                if (!empty($order['discount_type'])) {
                                                    $discountInfo[] = htmlspecialchars($order['discount_type']);
                                                }
                                                if (!empty($order['discount_percentage']) && $order['discount_percentage'] > 0) {
                                                    $discountInfo[] = $order['discount_percentage'] . '%';
                                                }
                                                if (!empty($order['discount_amount']) && $order['discount_amount'] > 0) {
                                                    $discountInfo[] = '₱' . number_format($order['discount_amount'], 2);
                                                }
                                                echo !empty($discountInfo) ? implode(' - ', $discountInfo) : '₱0.00';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($order['table_info'])): ?>
                                                    <div class="table-info-cell">
                                                        <i class="fas fa-chair me-1"></i>
                                                        <?= htmlspecialchars($order['table_info']) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">No Table</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $orderType = $order['order_type'] ?? 'N/A';
                                                $badgeClass = 'order-type-' . strtolower(str_replace(['-', ' '], '', $orderType));
                                                ?>
                                                <span class="order-type-badge <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($orderType) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $paymentMethod = $order['payment_method'] ?? 'Cash';
                                                $iconClass = match($paymentMethod) {
                                                    'Cash' => 'fa-money-bill-wave',
                                                    'Card' => 'fa-credit-card',
                                                    'GCash' => 'fa-mobile-alt',
                                                    default => 'fa-money-bill'
                                                };
                                                ?>
                                                <div class="payment-method-badge">
                                                    <i class="fas <?= $iconClass ?>"></i>
                                                    <?= htmlspecialchars($paymentMethod) ?>
                                                </div>
                                            </td>
                                            <td class="date-time-cell">
                                                <div><?= date('M d, Y', strtotime($order['date_time'])) ?></div>
                                                <small><?= date('h:i A', strtotime($order['date_time'])) ?></small>
                                            </td>
                                            <td>
                                                <span class="status-badge status-processing">
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                    Processing
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-view view-order" data-id="<?= htmlspecialchars($order['order_id']) ?>" title="View Order">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-edit edit-order" data-id="<?= htmlspecialchars($order['order_id']) ?>" title="Edit Order">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-complete complete-order" data-id="<?= htmlspecialchars($order['order_id']) ?>" title="Complete Order">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="13">
                                            <div class="no-orders-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5>No Processing Orders</h5>
                                                <p>There are currently no orders being processed. New orders will appear here automatically.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (count($orders) > 0): ?>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing <strong><?= count($orders) ?></strong> processing orders
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="viewOrderModalLabel">Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border" role="status" style="color: var(--primary-color);">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading order details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editOrderBtn">
                        <i class="fas fa-edit me-1"></i> Edit Order
                    </button>
                    <button type="button" class="btn btn-success" id="completeOrderBtn">
                        <i class="fas fa-check me-1"></i> Complete Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="editOrderModalLabel">Edit Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editOrderContent">
                    <!-- Edit form will be loaded here via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border" role="status" style="color: var(--primary-color);">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading order data for editing...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveOrderBtn">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items Modal -->
    <div class="modal fade" id="menuItemsModal" tabindex="-1" aria-labelledby="menuItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="menuItemsModalLabel"> Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="menuCategoriesTab" role="tablist">
                        <!-- Category tabs will be injected here by JavaScript -->
                    </ul>
                    <div class="tab-content" id="menuCategoriesTabContent">
                        <!-- Menu items grouped by category will be injected here by JavaScript -->
                    </div>
                    
                    <!-- Order Summary Section -->
                    <div class="card mt-4">
                        <div class="card-header bg-light fw-bold">Order Summary</div>
                        <div class="card-body">
                            <div id="orderSummaryItems">
                                <!-- Selected items will be listed here -->
                                <p class="text-muted">No items added yet.</p>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Total:</h5>
                                <h5 class="mb-0 text-success" id="orderTotal">₱0.00</h5>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="clearOrderBtn"><i class="fas fa-trash"></i> Clear Order</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="advanceOrderBtn">Add to Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="paymentModalLabel">Process Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="paymentOrderId">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount Due:</label>
                            <div class="form-control-plaintext fw-bold text-danger" id="paymentBalance">₱0.00</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount Paid:</label>
                            <input type="number" class="form-control" id="paymentAmountPaid" step="0.01" min="0" placeholder="Enter amount">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method:</label>
                            <select class="form-select" id="paymentMethod">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="GCash">GCash</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Change:</label>
                            <div class="form-control-plaintext fw-bold text-success" id="paymentChange">₱0.00</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="processPaymentBtn">
                        <i class="fas fa-credit-card me-1"></i> Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Global variable to store selected items
let selectedItems = {};

function displayMenuItems() {
    console.log('Loading menu items...');
    
    $.get('get_menu_items.php', function(response) {
        console.log('Menu items response:', response);
        
        if (response.success) {
            const menuItems = response.menu_items;
            const categories = Object.keys(menuItems);
            
            // Generate category tabs
            let tabsHtml = '';
            let contentHtml = '';
            
            categories.forEach((category, index) => {
                const categoryId = category.replace(/\s+/g, '-').toLowerCase();
                const isActive = index === 0 ? 'active' : '';
                
                // Create tab
                tabsHtml += `
                    <li class="nav-item" role="presentation">
                        <button class="nav-link ${isActive}" id="${categoryId}-tab" data-bs-toggle="tab" 
                                data-bs-target="#${categoryId}" type="button" role="tab">
                            ${category}
                        </button>
                    </li>
                `;
                
                // Create tab content
                contentHtml += `
                    <div class="tab-pane fade ${isActive}" id="${categoryId}" role="tabpanel">
                        <div class="row g-3">
                `;
                
                // Add menu items for this category
                menuItems[category].forEach(item => {
                    contentHtml += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card menu-item-card h-100" data-item-id="${item.id}" data-item-name="${item.name}" data-item-price="${item.price}">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="menu-item-icon me-3">
                                            <i class="fas fa-utensils fa-2x text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1">${item.name}</h6>
                                            ${item.description ? `<p class="card-text small text-muted mb-2">${item.description}</p>` : ''}
                                            <div class="text-success fw-bold mb-2">₱${parseFloat(item.price).toFixed(2)}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <button class="btn btn-outline-secondary decrement-qty" type="button">-</button>
                                            <input type="number" class="form-control text-center item-quantity" value="0" min="0" max="99" readonly>
                                            <button class="btn btn-outline-secondary increment-qty" type="button">+</button>
                                        </div>
                                        <button class="btn btn-primary btn-sm add-to-order">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                contentHtml += `
                        </div>
                    </div>
                `;
            });
            
            // Inject tabs and content
            $('#menuCategoriesTab').html(tabsHtml);
            $('#menuCategoriesTabContent').html(contentHtml);
            
            // Add event handlers
            initializeMenuItemsHandlers();
            
        } else {
            $('#menuCategoriesTabContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${response.message || 'Failed to load menu items'}
                </div>
            `);
        }
    }, 'json').fail(function(xhr, status, error) {
        console.error('Failed to load menu items:', error);
        $('#menuCategoriesTabContent').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading menu items. Please try again.
            </div>
        `);
    });
}

function initializeMenuItemsHandlers() {
    // Quantity increment/decrement
    $('.increment-qty').click(function() {
        const input = $(this).siblings('.item-quantity');
        const currentVal = parseInt(input.val()) || 0;
        if (currentVal < 99) {
            input.val(currentVal + 1);
        }
    });
    
    $('.decrement-qty').click(function() {
        const input = $(this).siblings('.item-quantity');
        const currentVal = parseInt(input.val()) || 0;
        if (currentVal > 0) {
            input.val(currentVal - 1);
        }
    });
    
    // Add to order button
    $('.add-to-order').click(function() {
        const card = $(this).closest('.menu-item-card');
        const itemId = card.data('item-id');
        const itemName = card.data('item-name');
        const itemPrice = parseFloat(card.data('item-price'));
        const quantity = parseInt(card.find('.item-quantity').val()) || 0;
        
        if (quantity > 0) {
            // Add to selected items
            if (selectedItems[itemId]) {
                selectedItems[itemId].quantity += quantity;
            } else {
                selectedItems[itemId] = {
                    name: itemName,
                    price: itemPrice,
                    quantity: quantity
                };
            }
            
            // Reset quantity input
            card.find('.item-quantity').val(0);
            
            // Update order summary
            updateOrderSummary();
        }
    });
}

function updateOrderSummary() {
    const summaryContainer = $('#orderSummaryItems');
    const totalElement = $('#orderTotal');
    
    if (Object.keys(selectedItems).length === 0) {
        summaryContainer.html('<p class="text-muted">No items added yet.</p>');
        totalElement.text('₱0.00');
        return;
    }
    
    let summaryHtml = '';
    let total = 0;
    
    Object.keys(selectedItems).forEach(itemId => {
        const item = selectedItems[itemId];
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        summaryHtml += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="fw-bold">${item.name}</div>
                    <small class="text-muted">₱${item.price.toFixed(2)} x ${item.quantity}</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold">₱${itemTotal.toFixed(2)}</div>
                    <button class="btn btn-sm btn-outline-danger remove-item" data-item-id="${itemId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    summaryContainer.html(summaryHtml);
    totalElement.text('₱' + total.toFixed(2));
    
    // Add remove item handlers
    $('.remove-item').click(function() {
        const itemId = $(this).data('item-id');
        delete selectedItems[itemId];
        updateOrderSummary();
    });
}
        
        function addNewItemToOrder(itemId, itemName, itemPrice) {
            console.log('Adding new item:', itemId, itemName, itemPrice);
            
            const itemsTable = $('#editItemsTable tbody');
            const itemCount = itemsTable.find('tr').length;
            
            const newRow = `
                <tr class="order-item-row" data-item-id="${itemId}">
                    <td>${itemCount + 1}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm item-name" value="${itemName}" readonly>
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm item-quantity" value="1" min="1" style="width: 80px;">
                    </td>
                    <td class="text-end">
                        <input type="number" class="form-control form-control-sm item-price" value="${parseFloat(itemPrice).toFixed(2)}" step="0.01" min="0" style="width: 100px;">
                    </td>
                    <td class="text-end item-total">₱${parseFloat(itemPrice).toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            itemsTable.append(newRow);
            
            // Recalculate totals
            calculateItemTotals();
            calculateOrderTotals();
        }
        
        function populateTableSelection(order) {
            // Fetch all tables
            $.get('get_all_tables.php', function(response) {
                if (response.status === 'success') {
                    const tables = response.tables;
                    const tableBody = $('#availableTablesTable tbody');
                    
                    // Clear existing content
                    tableBody.empty();
                    
                    // Get currently assigned tables for this order
                    const assignedTables = order.tables || [];
                    const assignedTableIds = assignedTables.map(t => t.table_number_id);
                    
                    // Generate table rows
                    Object.keys(tables).forEach(tableName => {
                        const tableType = tables[tableName];
                        
                        tableType.table_numbers.forEach(tableInfo => {
                            const isAssignedToCurrentOrder = assignedTableIds.includes(tableInfo.table_id);
                            const isAssignedToOtherOrder = tableInfo.assignment_status === 'Assigned' && !isAssignedToCurrentOrder;
                            const isOccupied = tableInfo.assignment_status === 'Occupied';
                            
                            // Determine status display and checkbox state
                            let statusClass, statusText, disabledAttr, checkedAttr;
                            
                            if (isAssignedToCurrentOrder) {
                                // Table assigned to current order being edited
                                statusClass = 'table-primary';
                                statusText = 'Assigned to this order';
                                disabledAttr = '';
                                checkedAttr = 'checked';
                            } else if (isAssignedToOtherOrder) {
                                // Table assigned to another order with non-completed status
                                statusClass = 'table-danger';
                                statusText = `Assigned to Order #${tableInfo.assigned_order_number}`;
                                disabledAttr = 'disabled';
                                checkedAttr = '';
                            } else if (isOccupied) {
                                // Table is occupied (status = unavailable)
                                statusClass = 'table-warning';
                                statusText = 'Occupied';
                                disabledAttr = 'disabled';
                                checkedAttr = '';
                            } else {
                                // Table is available
                                statusClass = 'table-success';
                                statusText = 'Available';
                                disabledAttr = '';
                                checkedAttr = '';
                            }
                            
                            const row = `
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" 
                                               class="form-check-input table-checkbox" 
                                               value="${tableInfo.table_id}"
                                               data-table-name="${tableName}"
                                               data-table-number="${tableInfo.table_number}"
                                               data-assignment-status="${tableInfo.assignment_status}"
                                               ${checkedAttr}
                                               ${disabledAttr}>
                                    </td>
                                    <td>${tableName}</td>
                                    <td>${tableInfo.table_number}</td>
                                    <td>${tableType.capacity} seats</td>
                                    <td>
                                        <span class="badge ${statusClass}">${statusText}</span>
                                        ${isAssignedToOtherOrder ? `<br><small class="text-muted">Status: ${tableInfo.assigned_order_status}</small>` : ''}
                                        ${isAssignedToCurrentOrder ? '<span class="badge bg-primary ms-1">Current Order</span>' : ''}
                                    </td>
                                </tr>
                            `;
                            tableBody.append(row);
                        });
                    });
                    
                    // Add click handlers for checkboxes
                    $('.table-checkbox').on('change', function() {
                        updateTableSelectionSummary();
                    });
                    
                    // Initialize summary
                    updateTableSelectionSummary();
                    
                } else {
                    console.error('Error loading tables:', response.message);
                    $('#availableTablesTable tbody').html(`
                        <tr>
                            <td colspan="5" class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error loading tables: ${response.message}
                            </td>
                        </tr>
                    `);
                }
            }).fail(function() {
                console.error('Failed to load tables');
                $('#availableTablesTable tbody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Failed to load tables. Please try again.
                        </td>
                    </tr>
                `);
            });
        }
        
        function updateTableSelectionSummary() {
            const selectedTables = $('.table-checkbox:checked');
            const selectedCount = selectedTables.length;
            
            // Update summary text
            let summaryText = '';
            if (selectedCount === 0) {
                summaryText = 'No tables selected';
            } else {
                const tableNames = [];
                selectedTables.each(function() {
                    const tableName = $(this).data('table-name');
                    const tableNumber = $(this).data('table-number');
                    tableNames.push(`${tableName} ${tableNumber}`);
                });
                summaryText = `${selectedCount} table(s) selected: ${tableNames.join(', ')}`;
            }
            
            // Update or create summary element
            let summaryDiv = $('#tableSelectionSummary');
            if (summaryDiv.length === 0) {
                $('#tableSelectionArea').append(`
                    <div id="tableSelectionSummary" class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-check-circle me-1"></i>
                            <span id="summaryText">${summaryText}</span>
                        </small>
                    </div>
                `);
            } else {
                $('#summaryText').text(summaryText);
            }
        }
        
        function getSelectedTables() {
            const selectedTables = [];
            $('.table-checkbox:checked').each(function() {
                selectedTables.push({
                    table_id: $(this).val(),
                    table_name: $(this).data('table-name'),
                    table_number: $(this).data('table-number')
                });
            });
            return selectedTables;
        }
        
        function displayEditForm(order) {
            console.log('displayEditForm called with order:', order);
            
            let itemsHtml = '';
            let subtotal = 0;
            
            // Generate editable items HTML
            order.items.forEach((item, index) => {
                const itemTotal = parseFloat(item.unit_price) * parseInt(item.quantity);
                subtotal += itemTotal;
                
                itemsHtml += `
                    <tr class="order-item-row" data-item-id="${item.id}">
                        <td>${index + 1}</td>
                        <td>
                            <input type="text" class="form-control form-control-sm item-name" value="${item.item_name}" readonly>
                            ${item.addons && item.addons.length > 0 ? `
                                <div class="mt-1">
                                    <small class="text-muted">Addons:</small>
                                    ${item.addons.map(addon => `
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <input type="text" class="form-control form-control-sm addon-name" value="${addon.addon_name}" readonly>
                                            <input type="number" class="form-control form-control-sm addon-price" value="${parseFloat(addon.price).toFixed(2)}" step="0.01" min="0" style="width: 100px;">
                                            <button type="button" class="btn btn-sm btn-danger remove-addon">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </td>
                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm item-quantity" value="${item.quantity}" min="1" style="width: 80px;">
                        </td>
                        <td class="text-end">
                            <input type="number" class="form-control form-control-sm item-price" value="${parseFloat(item.unit_price).toFixed(2)}" step="0.01" min="0" style="width: 100px;">
                        </td>
                        <td class="text-end item-total">₱${itemTotal.toFixed(2)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                
                // Add addon costs to subtotal
                if (item.addons) {
                    item.addons.forEach(addon => {
                        subtotal += parseFloat(addon.price);
                    });
                }
            });
            
            const editFormHtml = `
                <form id="editOrderForm">
                    <input type="hidden" id="editOrderId" value="${order.order_id}">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Customer Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="editFirstname" value="${order.firstname || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="editLastname" value="${order.lastname || ''}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact</label>
                                    <input type="text" class="form-control" id="editContact" value="${order.contact || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" value="${order.email || ''}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Order Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Order Type</label>
                                    <select class="form-select" id="editOrderType">
                                        <option value="Dine-in" ${order.type_of_order === 'Dine-in' ? 'selected' : ''}>Dine-in</option>
                                        <option value="Take-out" ${order.type_of_order === 'Take-out' ? 'selected' : ''}>Take-out</option>
                                        <option value="Delivery" ${order.type_of_order === 'Delivery' ? 'selected' : ''}>Delivery</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" id="editPaymentMethod">
                                        <option value="Cash" ${order.payment_method === 'Cash' ? 'selected' : ''}>Cash</option>
                                        <option value="Card" ${order.payment_method === 'Card' ? 'selected' : ''}>Card</option>
                                        <option value="GCash" ${order.payment_method === 'GCash' ? 'selected' : ''}>GCash</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Table Assignment <span class="text-danger">*</span></label>
                                    <div id="tableSelectionArea">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered" id="availableTablesTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="50">Select</th>
                                                        <th>Table Name</th>
                                                        <th>Table Number</th>
                                                        <th>Capacity</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="5" class="text-center">
                                                            <div class="spinner-border spinner-border-sm" role="status">
                                                                <span class="visually-hidden">Loading tables...</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Select multiple tables for this order. Currently assigned tables are pre-selected.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Type</label>
                                    <select class="form-select" id="editDiscountType">
                                        <option value="">None</option>
                                        <option value="Senior Citizen" ${order.discount_type === 'Senior Citizen' ? 'selected' : ''}>Senior Citizen</option>
                                        <option value="PWD" ${order.discount_type === 'PWD' ? 'selected' : ''}>PWD</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount %</label>
                                    <input type="number" class="form-control" id="editDiscountPercentage" value="${order.discount_percentage || 0}" min="0" max="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="text-muted mb-3">Order Items</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm" id="editItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Item Name</th>
                                    <th width="80" class="text-center">Qty</th>
                                    <th width="100" class="text-end">Unit Price</th>
                                    <th width="100" class="text-end">Total</th>
                                    <th width="80" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                                <i class="fas fa-plus me-1"></i> Add New Item
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="120"><strong>Subtotal:</strong></td>
                                            <td class="text-end" id="editSubtotal">₱${subtotal.toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td class="text-end text-danger" id="editDiscount">-₱${parseFloat(order.discount_amount || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total Amount:</strong></td>
                                            <td class="text-end"><strong id="editTotal">₱${parseFloat(order.total).toFixed(2)}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Downpayment:</strong></td>
                                            <td class="text-end text-info" id="editDownpayment">₱${parseFloat(order.downpayment || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amount Paid:</strong></td>
                                            <td class="text-end">
                                                <input type="number" class="form-control form-control-sm text-end" id="editAmountPaid" 
                                                       value="${parseFloat(order.amount_paid || 0).toFixed(2)}" 
                                                       step="0.01" min="0" placeholder="Enter amount paid">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Balance:</strong></td>
                                            <td class="text-end text-warning" id="editBalance">₱${parseFloat(order.balance || 0).toFixed(2)}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            `;
            
            console.log('Setting edit form HTML...');
            $('#editOrderContent').html(editFormHtml);
            console.log('Edit form HTML set successfully');
            
            // Initialize event listeners for auto-calculation
            initializeCalculationListeners();
            
            // Populate table selection interface
            populateTableSelection(order);
            
            // Handle order type change to show/hide table requirement
            $('#editOrderType').off('change').on('change', function() {
                const orderType = $(this).val();
                const tableSelectionArea = $('#tableSelectionArea');
                
                if (orderType === 'Dine-in') {
                    tableSelectionArea.show();
                    tableSelectionArea.find('label span.text-danger').show();
                } else {
                    tableSelectionArea.hide();
                    // Clear table selections for non-dine-in orders
                    $('.table-checkbox').prop('checked', false);
                    updateTableSelectionSummary();
                }
            });
            
            // Trigger the change event on page load
            $('#editOrderType').trigger('change');
        }
        
        function initializeCalculationListeners() {
            // Calculate totals when quantity or price changes
            $(document).on('input', '.item-quantity, .item-price, .addon-price', function() {
                calculateItemTotals();
                calculateOrderTotals();
            });
            
            // Calculate totals when discount type or percentage changes
            $(document).on('change', '#editDiscountType, #editDiscountPercentage', function() {
                calculateOrderTotals();
            });
            
            // Recalculate balance when amount paid changes
            $(document).on('input', '#editAmountPaid', function() {
                calculateOrderTotals();
            });
            
            // Remove item functionality
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                calculateItemTotals();
                calculateOrderTotals();
            });
            
            // Remove addon functionality
            $(document).on('click', '.remove-addon', function() {
                $(this).closest('div').remove();
                calculateItemTotals();
                calculateOrderTotals();
            });
        }
        
        function calculateItemTotals() {
            $('.order-item-row').each(function() {
                const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val()) || 0;
                let addonTotal = 0;
                
                // Calculate addon totals
                $(this).find('.addon-price').each(function() {
                    addonTotal += parseFloat($(this).val()) || 0;
                });
                
                const itemTotal = (quantity * price) + addonTotal;
                $(this).find('.item-total').text('₱' + itemTotal.toFixed(2));
            });
        }
        
        function calculateOrderTotals() {
            let subtotal = 0;
            
            // Calculate subtotal from all items
            $('.order-item-row').each(function() {
                const itemTotalText = $(this).find('.item-total').text();
                const itemTotal = parseFloat(itemTotalText.replace('₱', '')) || 0;
                subtotal += itemTotal;
            });
            
            // Get discount values
            const discountType = $('#editDiscountType').val();
            const discountPercentage = parseFloat($('#editDiscountPercentage').val()) || 0;
            
            // Calculate discount amount
            let discountAmount = 0;
            if (discountType && discountPercentage > 0) {
                discountAmount = (subtotal * discountPercentage) / 100;
            }
            
            // Calculate total
            const total = subtotal - discountAmount;
            
            // Get downpayment and amount paid
            const downpayment = parseFloat($('#editDownpayment').text().replace('₱', '')) || 0;
            const amountPaid = parseFloat($('#editAmountPaid')?.val() || 0);
            
            // Calculate balance: Total - Downpayment - Amount Paid
            const balance = Math.max(0, total - downpayment - amountPaid);
            
            // Update display
            $('#editSubtotal').text('₱' + subtotal.toFixed(2));
            $('#editDiscount').text('-₱' + discountAmount.toFixed(2));
            $('#editTotal').text('₱' + total.toFixed(2));
            $('#editBalance').text('₱' + balance.toFixed(2));
        }
        
        function displayOrderDetails(order) {
            let itemsHtml = '';
            let subtotal = 0;
            
            // Generate items HTML
            order.items.forEach((item, index) => {
                const itemTotal = parseFloat(item.unit_price) * parseInt(item.quantity);
                subtotal += itemTotal;
                
                itemsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <strong>${item.item_name}</strong>
                            ${item.addons && item.addons.length > 0 ? `
                                <br><small class="text-muted">
                                    Addons: ${item.addons.map(addon => `${addon.addon_name} (₱${parseFloat(addon.price).toFixed(2)})`).join(', ')}
                                </small>
                            ` : ''}
                        </td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-end">₱${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td class="text-end">₱${itemTotal.toFixed(2)}</td>
                    </tr>
                `;
                
                // Add addon costs to subtotal
                if (item.addons) {
                    item.addons.forEach(addon => {
                        subtotal += parseFloat(addon.price);
                    });
                }
            });
            
            const detailsHtml = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Order Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>Order ID:</strong></td>
                                <td>#${String(order.order_id).padStart(6, '0')}</td>
                            </tr>
                            <tr>
                                <td><strong>Date & Time:</strong></td>
                                <td>${new Date(order.date_time).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td><strong>Cashier:</strong></td>
                                <td>${order.cashier_name || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Order Type:</strong></td>
                                <td>${order.type_of_order || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method:</strong></td>
                                <td>${order.payment_method || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Option:</strong></td>
                                <td>${order.payment_option || 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Table Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>Table Info:</strong></td>
                                <td>${order.tables_info || 'No tables assigned'}</td>
                            </tr>
                            <tr>
                                <td><strong>Discount Type:</strong></td>
                                <td>${order.discount_type || 'None'}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-warning text-dark">${order.status}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h6 class="text-muted mb-3">Order Items</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Item Name</th>
                                <th width="80" class="text-center">Qty</th>
                                <th width="100" class="text-end">Unit Price</th>
                                <th width="100" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                </div>
                
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="120"><strong>Subtotal:</strong></td>
                                        <td class="text-end">₱${subtotal.toFixed(2)}</td>
                                    </tr>
                                    ${order.discount_amount && parseFloat(order.discount_amount) > 0 ? `
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td class="text-end text-danger">-₱${parseFloat(order.discount_amount).toFixed(2)}</td>
                                        </tr>
                                    ` : ''}
                                    <tr class="border-top">
                                        <td><strong>Total Amount:</strong></td>
                                        <td class="text-end"><strong>₱${parseFloat(order.total).toFixed(2)}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount Paid:</strong></td>
                                        <td class="text-end">₱${parseFloat(order.amount_paid || 0).toFixed(2)}</td>
                                    </tr>
                                    ${order.downpayment && parseFloat(order.downpayment) > 0 ? `
                                        <tr>
                                            <td><strong>Downpayment:</strong></td>
                                            <td class="text-end text-info">₱${parseFloat(order.downpayment).toFixed(2)}</td>
                                        </tr>
                                    ` : ''}
                                    ${order.balance && parseFloat(order.balance) > 0 ? `
                                        <tr>
                                            <td><strong>Balance:</strong></td>
                                            <td class="text-end text-warning">₱${parseFloat(order.balance).toFixed(2)}</td>
                                        </tr>
                                    ` : ''}
                                    <tr>
                                        <td><strong>Change:</strong></td>
                                        <td class="text-end">₱${parseFloat(order.change_amount || 0).toFixed(2)}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#orderDetailsContent').html(detailsHtml);
        }
        
        $(document).ready(function() {
            // Search functionality for orders
            $('#orderSearchInput').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                $('.table tbody tr').each(function() {
                    const row = $(this);
                    const rowText = row.text().toLowerCase();
                    
                    if (rowText.includes(searchTerm)) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
                
                // Show message if no results found
                const visibleRows = $('.table tbody tr:visible').length;
                if (visibleRows === 0 && searchTerm !== '') {
                    if (!$('.no-results-message').length) {
                        $('.table tbody').append(`
                            <tr class="no-results-message">
                                <td colspan="13" class="text-center py-4">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">No orders found matching "${searchTerm}"</p>
                                </td>
                            </tr>
                        `);
                    }
                } else {
                    $('.no-results-message').remove();
                }
            });

            // Edit Order from table
            $('.edit-order').click(function() {
                const orderId = $(this).data('id');
                console.log('Edit button clicked, Order ID:', orderId);
                
                if (orderId) {
                    const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
                    
                    // Show loading spinner
                    $('#editOrderContent').html(`
                        <div class="text-center my-5">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading order data for editing...</p>
                        </div>
                    `);
                    
                    modal.show();
                    
                    // Load order details for editing via AJAX
                    $.get('get_order_details.php', { id: orderId }, function(response) {
                        console.log('Response received:', response);
                        
                        if (response.success) {
                            displayEditForm(response.order);
                        } else {
                            $('#editOrderContent').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${response.message || 'Failed to load order data for editing'}
                                </div>
                            `);
                        }
                    }, 'json').fail(function(xhr, status, error) {
                        console.error('AJAX failed:', status, error);
                        console.log('Response text:', xhr.responseText);
                        
                        $('#editOrderContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error loading order data. Please try again.
                                <br><small>Details: ${error}</small>
                            </div>
                        `);
                    });
                } else {
                    console.error('No order ID found');
                    Swal.fire({
                        title: 'Error!',
                        text: 'No order ID found',
                        icon: 'error',
                        confirmButtonColor: '#b8860b'
                    });
                }
            });

            // View Order Details
            $('.view-order').click(function() {
                const orderId = $(this).data('id');
                const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                
                // Show loading spinner
                $('#orderDetailsContent').html(`
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading order details...</p>
                    </div>
                `);
                
                modal.show();
                
                // Load order details via AJAX
                $.get('get_order_details.php', { id: orderId }, function(response) {
                    if (response.success) {
                        displayOrderDetails(response.order);
                    } else {
                        $('#orderDetailsContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${response.message || 'Failed to load order details'}
                            </div>
                        `);
                    }
                }, 'json').fail(function() {
                    $('#orderDetailsContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading order details. Please try again.
                        </div>
                    `);
                });
                
                // Set the order ID in the complete button
                $('#completeOrderBtn').data('order-id', orderId);
                // Set the order ID in the edit button
                $('#editOrderBtn').data('order-id', orderId);
            });

            // Edit Order
            $('#editOrderBtn').click(function() {
                const orderId = $(this).data('order-id');
                console.log('Modal Edit button clicked, Order ID:', orderId);
                
                if (orderId) {
                    const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
                    
                    // Show loading spinner
                    $('#editOrderContent').html(`
                        <div class="text-center my-5">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading order data for editing...</p>
                        </div>
                    `);
                    
                    modal.show();
                    
                    // Load order details for editing via AJAX
                    $.get('get_order_details.php', { id: orderId }, function(response) {
                        console.log('Modal response received:', response);
                        
                        if (response.success) {
                            displayEditForm(response.order);
                        } else {
                            $('#editOrderContent').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${response.message || 'Failed to load order data for editing'}
                                </div>
                            `);
                        }
                    }, 'json').fail(function(xhr, status, error) {
                        console.error('Modal AJAX failed:', status, error);
                        console.log('Response text:', xhr.responseText);
                        
                        $('#editOrderContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error loading order data. Please try again.
                                <br><small>Details: ${error}</small>
                            </div>
                        `);
                    });
                } else {
                    console.error('No order ID found in modal edit button');
                    Swal.fire({
                        title: 'Error!',
                        text: 'No order ID found',
                        icon: 'error',
                        confirmButtonColor: '#b8860b'
                    });
                }
            });

            // Complete Order button
            $('.complete-order, #completeOrderBtn').click(function() {
                const orderId = $(this).data('order-id') || $(this).closest('.complete-order').data('id');
                
                if (!orderId) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Order ID not found',
                        icon: 'error',
                        confirmButtonColor: '#b8860b'
                    });
                    return;
                }
                
                // Check order balance first
                $.ajax({
                    url: 'check_order_balance.php',
                    method: 'GET',
                    data: { order_id: orderId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const order = response.order;
                            const balance = parseFloat(order.balance);
                            
                            if (balance > 0) {
                                // Show payment modal
                                showPaymentModal(orderId, balance);
                            } else {
                                // No balance, mark as finished directly
                                Swal.fire({
                                    title: 'Complete Order?',
                                    text: 'Are you sure you want to mark this order as finished?',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#b8860b',
                                    cancelButtonColor: '#6c757d',
                                    confirmButtonText: 'Yes, Complete!',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        markOrderAsFinished(orderId);
                                    }
                                });
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error checking order balance: ' + (response.message || 'Unknown error'),
                                icon: 'error',
                                confirmButtonColor: '#b8860b'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking order balance:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error checking order balance. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#b8860b'
                        });
                    }
                });
            });
            
            // Function to show payment modal
            function showPaymentModal(orderId, balance) {
                $('#paymentOrderId').val(orderId);
                $('#paymentBalance').text('₱' + balance.toFixed(2));
                $('#paymentAmountPaid').val('');
                $('#paymentChange').text('₱0.00');
                $('#paymentMethod').val('Cash');
                
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            }
            
            // Function to mark order as finished
            function markOrderAsFinished(orderId) {
                $.ajax({
                    url: 'mark_order_finished.php',
                    method: 'POST',
                    data: { order_id: orderId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order marked as finished successfully!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error marking order as finished: ' + (response.message || 'Unknown error'),
                                icon: 'error',
                                confirmButtonColor: '#b8860b'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking order as finished:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error marking order as finished. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#b8860b'
                        });
                    }
                });
            }
            
            // Payment amount paid input handler
            $(document).on('input', '#paymentAmountPaid', function() {
                const balance = parseFloat($('#paymentBalance').text().replace('₱', '').replace(',', ''));
                const amountPaid = parseFloat($(this).val()) || 0;
                const change = amountPaid - balance;
                $('#paymentChange').text('₱' + change.toFixed(2));
            });
            
            // Process Payment button
            $(document).on('click', '#processPaymentBtn', function() {
                const orderId = $('#paymentOrderId').val();
                const balance = parseFloat($('#paymentBalance').text().replace('₱', '').replace(',', ''));
                const amountPaid = parseFloat($('#paymentAmountPaid').val()) || 0;
                const paymentMethod = $('#paymentMethod').val();
                
                if (amountPaid <= 0) {
                    Swal.fire({
                        title: 'Invalid Amount!',
                        text: 'Please enter a valid payment amount',
                        icon: 'warning',
                        confirmButtonColor: '#b8860b'
                    });
                    return;
                }
                
                if (amountPaid < balance) {
                    Swal.fire({
                        title: 'Partial Payment?',
                        text: 'Partial payment of ₱' + amountPaid.toFixed(2) + ' will be processed. Remaining balance will be ₱' + (balance - amountPaid).toFixed(2) + '. Continue?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#b8860b',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Process',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processPayment(orderId, amountPaid, balance);
                        }
                    });
                    return;
                }
                
                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');
                
                // Process payment
                $.ajax({
                    url: 'process_payment.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        amount_paid: amountPaid,
                        payment_method: paymentMethod
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let message = 'Payment processed successfully!';
                            
                            if (response.new_balance && response.new_balance > 0) {
                                message += ' Remaining balance: ₱' + response.new_balance.toFixed(2);
                            }
                            
                            Swal.fire({
                                title: 'Success!',
                                text: message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error processing payment: ' + (response.message || 'Unknown error'),
                                icon: 'error',
                                confirmButtonColor: '#b8860b'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error processing payment:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error processing payment. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#b8860b'
                        });
                    },
                    complete: function() {
                        // Restore button state
                        $('#processPaymentBtn').prop('disabled', false).html('<i class="fas fa-credit-card me-1"></i> Process Payment');
                    }
                });
            });

            // Add New Item button
            $(document).on('click', '#addItemBtn', function() {
                console.log('Add Item button clicked');
                
                // Reset selected items
                selectedItems = {};
                
                // Show menu items modal
                const modal = new bootstrap.Modal(document.getElementById('menuItemsModal'));
                modal.show();
                
                // Load menu items
                displayMenuItems();
            });

            // Save Order button
            $(document).on('click', '#saveOrderBtn', function() {
                console.log('Save Order button clicked');
                
                // Validate form
                const orderId = $('#editOrderId').val();
                if (!orderId) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Order ID not found',
                        icon: 'error',
                        confirmButtonColor: '#b8860b'
                    });
                    return;
                }
                
                // Validate table selection for dine-in orders
                const orderType = $('#editOrderType').val();
                const selectedTables = getSelectedTables();
                
                if (orderType === 'Dine-in' && selectedTables.length === 0) {
                    Swal.fire({
                        title: 'Table Required!',
                        text: 'Table selection is required for dine-in orders. Please select at least one table.',
                        icon: 'warning',
                        confirmButtonColor: '#b8860b'
                    });
                    return;
                }
                
                // Collect order data
                const orderData = {
                    order_id: orderId,
                    firstname: $('#editFirstname').val(),
                    lastname: $('#editLastname').val(),
                    contact: $('#editContact').val(),
                    email: $('#editEmail').val(),
                    order_type: $('#editOrderType').val(),
                    payment_method: $('#editPaymentMethod').val(),
                    selected_tables: selectedTables,
                    discount_type: $('#editDiscountType').val(),
                    discount_percentage: $('#editDiscountPercentage').val(),
                    subtotal: 0,
                    items: []
                };
                
                // Collect items data
                $('.order-item-row').each(function() {
                    const item = {
                        item_name: $(this).find('.item-name').val(),
                        quantity: parseInt($(this).find('.item-quantity').val()) || 0,
                        unit_price: parseFloat($(this).find('.item-price').val()) || 0,
                        addons: []
                    };
                    
                    // Collect addons for this item
                    $(this).find('.addon-name').each(function(index) {
                        const addonName = $(this).val();
                        const addonPrice = parseFloat($(this).siblings('.addon-price').eq(index).val()) || 0;
                        if (addonName && addonPrice > 0) {
                            item.addons.push({
                                addon_name: addonName,
                                price: addonPrice
                            });
                        }
                    });
                    
                    if (item.quantity > 0) {
                        orderData.items.push(item);
                    }
                });
                
                if (orderData.items.length === 0) {
                    Swal.fire({
                        title: 'No Items!',
                        text: 'Order must have at least one item',
                        icon: 'warning',
                        confirmButtonColor: '#b8860b'
                    });
                    return;
                }
                
                // Calculate subtotal
                orderData.subtotal = orderData.items.reduce((sum, item) => {
                    const itemTotal = (item.unit_price * item.quantity) + 
                        item.addons.reduce((addonSum, addon) => addonSum + addon.price, 0);
                    return sum + itemTotal;
                }, 0);
                
                // Calculate discount amount
                let discountAmount = 0;
                if (orderData.discount_type && orderData.discount_percentage > 0) {
                    discountAmount = (orderData.subtotal * orderData.discount_percentage) / 100;
                }
                
                // Calculate total and balance
                const total = orderData.subtotal - discountAmount;
                const downpayment = parseFloat($('#editDownpayment').text().replace('₱', '')) || 0;
                const amountPaid = parseFloat($('#editAmountPaid')?.val() || 0);
                const balance = Math.max(0, total - downpayment - amountPaid);
                
                // Add calculated values to order data
                orderData.total = total;
                orderData.discount_amount = discountAmount;
                orderData.downpayment = downpayment;
                orderData.amount_paid = amountPaid;
                orderData.balance = balance;
                
                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
                
                // Update order via AJAX
                $.ajax({
                    url: 'update_order.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(orderData),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order updated successfully!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error updating order: ' + (response.message || 'Unknown error'),
                                icon: 'error',
                                confirmButtonColor: '#b8860b'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Update order error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error updating order. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#b8860b'
                        });
                    },
                    complete: function() {
                        // Restore button state
                        $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');
                    }
                });
            });

            // Clear Order button
            $(document).on('click', '#clearOrderBtn', function() {
                Swal.fire({
                    title: 'Clear Order?',
                    text: 'Are you sure you want to clear the order?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#b8860b',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Clear!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        selectedItems = {};
                        updateOrderSummary();
                    }
                });
            });

            // Advance Order button
            $(document).on('click', '#advanceOrderBtn', function() {
                if (Object.keys(selectedItems).length === 0) {
                    Swal.fire({
                        title: 'No Items!',
                        text: 'Please add items to the order first.',
                        icon: 'warning',
                        confirmButtonColor: '#b8860b'
                    });
                    return;
                }
                
                // Add selected items to the edit order form
                Object.keys(selectedItems).forEach(itemId => {
                    const item = selectedItems[itemId];
                    for (let i = 0; i < item.quantity; i++) {
                        addNewItemToOrder(itemId, item.name, item.price);
                    }
                });
                
                // Close menu modal
                bootstrap.Modal.getInstance(document.getElementById('menuItemsModal')).hide();
                
                // Reset selected items
                selectedItems = {};
            });
        });
    </script>
</body>
</html>
