<?php
session_start();
require_once 'db.php';

function generateReceipt($orderId) {
    global $conn;
    
    // Get current logged in cashier's ID from session
    $cashierId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Debug session
    error_log(print_r($_SESSION, true));
    
    // Fetch order details with cashier name
    $orderQuery = "SELECT o.*, u.first_name, u.last_name 
                  FROM orders o 
                  LEFT JOIN userss u ON u.id = ? 
                  WHERE o.id = ?";
    
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("ii", $cashierId, $orderId);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    $order = $orderResult->fetch_assoc();

    // Debug order data
    error_log(print_r($order, true));

    if (!$order) {
        return "Order not found";
    }

    // Get cashier name from session if available
    $cashierName = '';
    if (isset($_SESSION['first_name'])) {
        $cashierName = $_SESSION['first_name'];
        if (isset($_SESSION['last_name'])) {
            $cashierName .= ' ' . $_SESSION['last_name'];
        }
    } elseif (!empty($order['first_name'])) {
        $cashierName = $order['first_name'];
        if (!empty($order['last_name'])) {
            $cashierName .= ' ' . $order['last_name'];
        }
    } else {
        $cashierName = 'Unknown';
    }
    error_log("Cashier name: " . $cashierName);

    // Fetch order items
    $itemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($itemsQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $itemsResult = $stmt->get_result();
    $items = [];
    while ($row = $itemsResult->fetch_assoc()) {
        $items[] = $row;
    }

    // Generate unique invoice number
    $invoiceNo = date('dmY') . '-' . $orderId . '-' . rand(1000, 9999);
    
    // Calculate VAT (12% of subtotal after discount)
    $vatRate = 0.12;
    $subtotal = $order['subtotal_amount'] ?? $order['total_amount'] + ($order['discount_amount'] ?? 0);
    $discount = $order['discount_amount'] ?? 0;
    $vatAmount = ($subtotal - $discount) * $vatRate;
    $totalAmount = $subtotal - $discount + $vatAmount;
    
    // Generate receipt HTML
    $receipt = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Order Receipt #' . $orderId . '</title>
        <style>
            @media print {
                body {
                    margin: 0;
                    padding: 10px;
                    font-family: "Courier New", monospace;
                    width: 80mm;
                    font-size: 12px;
                    line-height: 1.4;
                }
                .receipt {
                    width: 100%;
                }
                .header {
                    text-align: center;
                    margin-bottom: 15px;
                }
                .header h1 {
                    font-size: 16px;
                    margin: 0;
                    padding: 0;
                }
                .header h2 {
                    font-size: 14px;
                    margin: 5px 0;
                    font-weight: normal;
                }
                .order-info {
                    margin: 10px 0;
                }
                .divider {
                    border-top: 1px dashed #000;
                    margin: 10px 0;
                }
                .items-header {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 5px;
                    font-weight: bold;
                }
                .item-row {
                    display: flex;
                    justify-content: space-between;
                    margin: 3px 0;
                }
                .item-name {
                    flex: 2;
                }
                .item-amount {
                    text-align: right;
                }
                .item-details {
                    font-size: 10px;
                    color: #555;
                    margin-left: 10px;
                    margin-bottom: 5px;
                }
                .total-section {
                    margin: 10px 0;
                    text-align: right;
                }
                .total-section p {
                    margin: 3px 0;
                }
                .total-amount {
                    font-weight: bold;
                    font-size: 14px;
                    margin-top: 5px;
                    padding-top: 5px;
                    border-top: 1px dashed #000;
                }
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    font-size: 11px;
                }
                .center {
                    text-align: center;
                    font-weight: bold;
                    margin: 10px 0;
                }
                @page {
                    margin: 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="receipt">
            <div class="header">
                <h1>CASA ESTELA</h1>
                <h2>Boutique Hotel & Cafe</h2>
            </div>
            
            <div class="center">SALES INVOICE</div>
            
            <div class="order-info">
                <p>Order #: ' . str_pad($orderId, 6, "0", STR_PAD_LEFT) . '</p>
                <p>Date: ' . date('Y-m-d H:i:s') . '</p>
                <p>Cashier: ' . htmlspecialchars($cashierName) . '</p>
                <p>Payment: ' . htmlspecialchars($order['payment_method'] ?? 'Cash') . '</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="items-header">
                <span>ITEM</span>
                <span>AMOUNT</span>
            </div>';

    // Add items
    foreach ($items as $item) {
        $receipt .= '
            <div class="item-row">
                <div class="item-name">' . htmlspecialchars($item['item_name']) . '</div>
                <div class="item-amount">₱' . number_format($item['unit_price'] * $item['quantity'], 2) . '</div>
            </div>
            <div class="item-details">
                ' . $item['quantity'] . ' x ₱' . number_format($item['unit_price'], 2) . '
            </div>';
    }

    $receipt .= '
            <div class="divider"></div>
            
            <div class="total-section">
                <p>Subtotal: ₱' . number_format($subtotal, 2) . '</p>';

    if ($discount > 0) {
        $discountType = $order['discount_type'] ?? 'PWD';
        $receipt .= '
                <p>Discount (' . htmlspecialchars(strtoupper($discountType)) . '): -₱' . number_format($discount, 2) . '</p>';
    }
    
    $receipt .= '
                <p>VAT (12%): ₱' . number_format($vatAmount, 2) . '</p>
                <p class="total-amount">TOTAL: ₱' . number_format($totalAmount, 2) . '</p>
                <p>Amount Paid: ₱' . number_format($order['amount_paid'], 2) . '</p>
                <p>Change: ₱' . number_format($order['change_amount'], 2) . '</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="footer">
                <p>VAT REG TIN: 123-456-789-000</p>
                <p>This serves as your official receipt</p>
                <p>Thank you for dining with us!</p>
                <p>Please come again</p>
            </div>
        </div>
        
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>
    </html>';

    return $receipt;
}

// If this file is called directly with an order ID
if (isset($_GET['order_id'])) {
    echo generateReceipt($_GET['order_id']);
}
?> 