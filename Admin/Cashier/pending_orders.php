<?php
// Include necessary files
require_once 'db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle AJAX request for processing order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'process_order') {
    header('Content-Type: application/json');
    
    try {
        $orderId = $_POST['id'] ?? null;
        
        if (!$orderId) {
            throw new Exception('Order ID is required');
        }
        
        // Start transaction for atomic updates
        $pdo->beginTransaction();
        
        // Update order status to 'processing' - use order_id field instead of id
        $stmt = $pdo->prepare("UPDATE orders_table SET status = 'processing' WHERE order_id = ?");
        $stmt->execute([$orderId]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Order not found or already processed');
        }
        
        // Get the database ID for this order to update the correct notification
        $orderStmt = $pdo->prepare("SELECT id FROM orders_table WHERE order_id = ?");
        $orderStmt->execute([$orderId]);
        $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($orderData && $orderData['id']) {
            // Update the notification: set is_processing=1, update message, set is_read=0
            $notificationStmt = $pdo->prepare("
                UPDATE notifications 
                SET is_processing = 1, 
                    title = 'Order Processing',
                    message = 'Your order #{$orderId} is now being processed', 
                    is_read = 0,
                    is_completed = 0,
                    is_rejected = 0
                WHERE order_id = ? 
                AND type = 'Order'
                AND title = 'Order Pending'
            ");
            $notificationStmt->execute([$orderData['id']]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Order processed successfully'
        ]);
        
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

// Handle AJAX request for updating sidebar counts
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['update_counts'])) {
    header('Content-Type: application/json');
    
    try {
        // Get processing orders count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE status = 'processing'");
        $stmt->execute();
        $processingResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $processingCount = $processingResult['count'];
        
        // Get pending orders count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE status = 'pending'");
        $stmt->execute();
        $pendingResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $pendingCount = $pendingResult['count'];
        
        echo json_encode([
            'processingCount' => $processingCount,
            'pendingCount' => $pendingCount
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'processingCount' => 0,
            'pendingCount' => 0
        ]);
    }
    exit();
}

// Get pending orders from database with item count and addons
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
        CONCAT(ott.table_name, ' (', ott.table_number, ')') as table_info,
        COUNT(DISTINCT oi.id) as item_count,
        GROUP_CONCAT(
            CONCAT(oi.item_name, 
                IF(
                    (SELECT GROUP_CONCAT(oia.addon_name SEPARATOR ', ') 
                     FROM order_item_addons oia 
                     WHERE oia.order_item_fk_id = oi.id) IS NOT NULL,
                    CONCAT(' (', 
                        (SELECT GROUP_CONCAT(oia.addon_name SEPARATOR ', ') 
                         FROM order_item_addons oia 
                         WHERE oia.order_item_fk_id = oi.id), 
                    ')'),
                    ''
                )
            ) 
            SEPARATOR ', '
        ) as item_names
    FROM orders_table o
    LEFT JOIN order_items oi ON o.id = oi.order_fk_id
    LEFT JOIN orders_table_type ott ON o.id = ott.table_booking_fk_id
    WHERE o.status = 'pending'
    GROUP BY o.id
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
    <title>Pending Orders - Casa Estela Boutique Hotel & Cafe</title>
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
            text-align: left;
        }
        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            background-color: white;
            transition: all 0.3s ease;
            text-align: left;
        }
        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .table tbody tr:hover td {
            border-bottom-color: #e0e0ff;
        }
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
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
        .status-pending {
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
        .btn-process {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .btn-process:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.4);
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
                    <h2><i class="fas fa-clock me-2"></i>Pending Orders</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pending Orders</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-ul me-2"></i>Pending Order List</span>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" id="orderSearchInput" placeholder="Search orders...">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="pendingOrdersTable">
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
                                                <span class="status-badge status-pending">
                                                    <i class="fas fa-clock"></i>
                                                    Pending
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-view view-order" data-id="<?= htmlspecialchars($order['order_id']) ?>" title="View Order">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-process process-order" data-id="<?= htmlspecialchars($order['order_id']) ?>" title="Process Order">
                                                        <i class="fas fa-cogs"></i>
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
                                                <h5>No Pending Orders</h5>
                                                <p>There are currently no pending orders. New orders will appear here automatically.</p>
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
                                Showing <strong><?= count($orders) ?></strong> pending orders
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
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewOrderModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading order details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="processOrderBtn">
                        <i class="fas fa-check me-1"></i> Process Order
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
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Table Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>Table Name:</strong></td>
                                <td>${order.table_name || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Table Number:</strong></td>
                                <td>${order.table_number || 'N/A'}</td>
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
                                    <tr class="border-top">
                                        <td><strong>Balance:</strong></td>
                                        <td class="text-end ${parseFloat(order.balance || 0) > 0 ? 'text-warning' : 'text-success'}">
                                            ₱${parseFloat(order.balance || 0).toFixed(2)}
                                        </td>
                                    </tr>
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
            // Edit Order from table
            $('.edit-order').click(function() {
                const orderId = $(this).data('id');
                if (orderId) {
                    window.location.href = `edit_order.php?id=${orderId}`;
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
                
                // Set the order ID in the process button
                $('#processOrderBtn').data('order-id', orderId);
            });

            // Process Order
            $('.process-order, #processOrderBtn').click(function() {
                const orderId = $(this).data('order-id') || $(this).closest('.process-order').data('id');
                const button = $(this);
                
                Swal.fire({
                    title: 'Process Order?',
                    text: 'Are you sure you want to mark this order as processed?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#b8860b',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Process!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button and show loading
                        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                        
                        // Create a simple AJAX endpoint inline
                        $.ajax({
                            url: 'pending_orders.php',
                            method: 'POST',
                            data: { 
                                action: 'process_order',
                                id: orderId 
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Show success message
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Order processed successfully!',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        position: 'top-end',
                                        toast: true
                                    });
                                    
                                    // Remove the row with animation
                                    const row = button.closest('tr');
                                    row.fadeOut(500, function() {
                                        $(this).remove();
                                        
                                        // Check if there are any remaining orders
                                        if ($('#pendingOrdersTable tbody tr').length === 0) {
                                            location.reload(); // Reload to show empty state
                                        }
                                    });
                                    
                                    // Update sidebar badge count
                                    updateSidebarCounts();
                                } else {
                                    button.prop('disabled', false).html('<i class="fas fa-cogs"></i>');
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Error processing order: ' + (response.message || 'Unknown error'),
                                        icon: 'error',
                                        confirmButtonColor: '#b8860b'
                                    });
                                }
                            },
                            error: function() {
                                button.prop('disabled', false).html('<i class="fas fa-cogs"></i>');
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Error processing order. Please try again.',
                                    icon: 'error',
                                    confirmButtonColor: '#b8860b'
                                });
                            }
                        });
                    }
                });
            });
            
            // Function to update sidebar counts
            function updateSidebarCounts() {
                $.get('sidebar.php', { update_counts: true }, function(response) {
                    // Update the processing count badge if it exists
                    $('#processing-count').text(response.processingCount || 0);
                    $('#pending-count').text(response.pendingCount || 0);
                }, 'json');
            }
        });
    </script>
</body>
</html>
