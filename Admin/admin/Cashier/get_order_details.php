<?php
require_once 'db.php';

// Ensure clean output buffer
ob_clean();
header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$order_id = intval($_GET['order_id']);

try {
    // Fetch order details including payment information
    $sql = "SELECT o.*, 
            CONCAT(u.firstname, ' ', u.lastname) as customer_name,
            oi.item_name, oi.quantity, oi.unit_price,
            oia.addon_name, oia.addon_price,
            o.payment_proof,
            o.payment_reference
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
            WHERE o.id = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    $order = [
        'items' => [],
        'addons' => []
    ];

    while ($row = $result->fetch_assoc()) {
        if (empty($order['id'])) {
            $order['id'] = $row['id'];
            $order['customer_name'] = $row['customer_name'] ?? 'N/A';
            $order['order_type'] = $row['order_type'] ?? 'N/A';
            $order['payment_method'] = $row['payment_method'] ?? 'N/A';
            $order['payment_status'] = $row['payment_status'] ?? 'N/A';
            
            // Get payment reference
            $order['payment_reference'] = $row['payment_reference'] ?? 'N/A';
            
            // Handle payment proof path
            $payment_proof = $row['payment_proof'] ?? '';
            if (!empty($payment_proof)) {
                // Construct the correct path
                $payment_proof = basename($payment_proof); // Get just the filename
                $order['payment_proof'] = '../../aa/uploads/payment_proofs/' . $payment_proof;
            } else {
                $order['payment_proof'] = '';
            }
            
            $order['remaining_balance'] = $row['remaining_balance'] ?? 0;
            $order['total_amount'] = $row['total_amount'] ?? 0;
            $order['order_date'] = $row['order_date'] ?? 'N/A';
            $order['status'] = $row['status'] ?? 'N/A';
        }

        // Group items and their addons
        if ($row['item_name']) {
            $item_key = $row['item_name'] . '_' . $row['quantity'];
            if (!isset($order['items'][$item_key])) {
                $order['items'][$item_key] = [
                    'name' => $row['item_name'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'addons' => []
                ];
            }
            
            if ($row['addon_name'] && $row['addon_price']) {
                $order['items'][$item_key]['addons'][] = [
                    'name' => $row['addon_name'],
                    'price' => $row['addon_price']
                ];
            }
        }
    }

    // Convert items array to indexed array
    $order['items'] = array_values($order['items']);

    // Close database resources
    $stmt->close();
    $connection->close();

    // Return JSON response
    echo json_encode(['success' => true, 'order' => $order], JSON_UNESCAPED_SLASHES);
    exit;

} catch (Exception $e) {
    error_log('Error in get_order_details.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Helper function to generate HTML for order details
function generateOrderDetailsHtml($order) {
    $html = '<div class="order-details">';
    
    // Customer Information Section
    $html .= '<div class="customer-info">';
    $html .= '<h5>Customer Information</h5>';
    $html .= '<p><strong>Name:</strong> ' . htmlspecialchars($order['customer_name']) . '</p>';
    
    // Add email and contact for online orders
    if ($order['order_type'] === 'Online Order') {
        $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($order['email']) . '</p>';
        $html .= '<p><strong>Contact:</strong> ' . htmlspecialchars($order['user_contact']) . '</p>';
    }
    
    $html .= '</div>';

    // Order Information Section
    $html .= '<div class="order-info">';
    $html .= '<h5>Order Information</h5>';
    $html .= '<p><strong>Order Type:</strong> ' . htmlspecialchars(ucfirst($order['order_type'])) . ' Order</p>';
    $html .= '<p><strong>Payment Method:</strong> ' . htmlspecialchars(ucfirst($order['payment_method'])) . '</p>';
    $html .= '<p><strong>Payment Status:</strong> ' . htmlspecialchars(ucfirst($order['payment_status'])) . '</p>';
    $html .= '<p><strong>Order Date:</strong> ' . htmlspecialchars($order['order_date']) . '</p>';
    $html .= '<p><strong>Status:</strong> <span class="label label-' . getStatusClass($order['status']) . '">' . 
             htmlspecialchars(ucfirst($order['status'])) . '</span></p>';
    $html .= '</div>';

    // Items Section
    $html .= '<div class="items-info">';
    $html .= '<h5>Order Items</h5>';
    $html .= '<div class="items-list">';
    
    foreach ($order['items'] as $item) {
        $html .= '<div class="item">';
        $html .= '<p class="item-name"><strong>' . htmlspecialchars($item['name']) . '</strong></p>';
        $html .= '<p>Quantity: ' . htmlspecialchars($item['quantity']) . ' x ₱' . 
                number_format($item['unit_price'], 2) . '</p>';
        
        if (!empty($item['addons'])) {
            $html .= '<div class="addons">';
            $html .= '<p><em>Add-ons:</em></p>';
            $html .= '<ul>';
            foreach ($item['addons'] as $addon) {
                $html .= '<li>' . htmlspecialchars($addon['name']) . ' - ₱' . 
                        number_format($addon['price'], 2) . '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        
        $html .= '<p class="text-right"><strong>Subtotal: ₱' . 
                number_format($item['subtotal'], 2) . '</strong></p>';
        $html .= '</div>';
    }
    $html .= '</div>';

    // Payment Summary Section
    $html .= '<div class="total-amount">';
    $html .= '<h4>Payment Summary</h4>';
    $html .= '<p><strong>Total Amount:</strong> ₱' . number_format($order['total_amount'], 2) . '</p>';
    if ($order['discount_amount'] > 0) {
        $html .= '<p><strong>Discount:</strong> ₱' . number_format($order['discount_amount'], 2) . '</p>';
        $html .= '<p><strong>Final Amount:</strong> ₱' . number_format($order['final_amount'], 2) . '</p>';
    }
    if ($order['amount_paid']) {
        $html .= '<p><strong>Amount Paid:</strong> ₱' . number_format($order['amount_paid'], 2) . '</p>';
        
        // Display remaining balance for partially paid online orders
        if ($order['order_type'] === 'Online Order' && $order['payment_status'] === 'Partially Paid') {
            $html .= '<p><strong>Remaining Balance:</strong> <span class="text-danger">₱' . 
                    number_format($order['remaining_balance'], 2) . '</span></p>';
        }
        
        if ($order['change_amount'] > 0) {
            $html .= '<p><strong>Change:</strong> ₱' . number_format($order['change_amount'], 2) . '</p>';
        }
    }
    $html .= '</div>';

    $html .= '</div>';
    return $html;
}

function getStatusClass($status) {
    switch(strtolower($status)) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'finished':
            return 'success';
        case 'rejected':
            return 'danger';
        default:
            return 'default';
    }
}
?> 