<?php
require_once "db.php";

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order details including payment information
$sql = "SELECT o.*, 
        CONCAT(u.firstname, ' ', u.lastname) as customer_name,
        oi.item_name, oi.quantity, oi.unit_price,
        oia.addon_name, oia.addon_price,
        op.proof_image, op.reference_number
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN order_item_addons oia ON oi.id = oia.order_item_id
        LEFT JOIN order_payments op ON o.id = op.order_id
        WHERE o.id = ?";

try {
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
            $order['customer_name'] = $row['customer_name'];
            $order['order_type'] = $row['order_type'];
            $order['payment_method'] = $row['payment_method'];
            $order['payment_status'] = $row['payment_status'];
            
            // Check both order_payments and orders table for payment details
            $order['payment_reference'] = $row['reference_number'] ?? $row['payment_reference'] ?? 'N/A';
            $order['payment_proof'] = $row['proof_image'] ?? $row['payment_proof'] ?? '';
            
            $order['remaining_balance'] = $row['remaining_balance'];
            $order['total_amount'] = $row['total_amount'];
            $order['order_date'] = $row['order_date'];
            $order['status'] = $row['status'];

            // Debug information about payment proof
            $order['debug'] = [
                'has_proof' => !empty($order['payment_proof']),
                'proof_path' => $order['payment_proof'],
                'proof_image' => $row['proof_image'] ?? null,
                'payment_proof_field' => $row['payment_proof'] ?? null
            ];
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

    // Convert items associative array to indexed array
    $order['items'] = array_values($order['items']);

    echo json_encode([
        'success' => true,
        'order' => $order
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'debug' => [
            'sql' => $sql,
            'error' => $e->getMessage()
        ]
    ]);
} 