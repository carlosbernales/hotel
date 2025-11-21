<?php
require_once 'db.php';

// Check if order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "Error: No order ID provided.";
    exit;
}

$order_id = $_GET['order_id'];

// Get order details - simplified query
$sql = "SELECT orders.*, 
        CONCAT(users.firstname, ' ', users.lastname) as customer_name,
        users.email, users.phone as contact
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id
        WHERE orders.id = $order_id";

$result = mysqli_query($connection, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Error: Order not found.";
    exit;
}

$order = mysqli_fetch_assoc($result);

// Get items summary from joined items field
$items = explode(', ', $order['items'] ?? '');

// Format the order date
$orderDate = date('M d, Y h:i A', strtotime($order['order_date']));

// Calculate amounts
$totalAmount = floatval($order['total_amount']);
$amountPaid = floatval($order['amount_paid'] ?? 0);
$remainingBalance = max(0, $totalAmount - $amountPaid);
$isFullyPaid = $amountPaid >= $totalAmount;

// Get discount information
$discountAmount = 0;
$discountType = $order['discount_type'] ?? '';

if (!empty($discountType)) {
    if ($discountType === 'senior_citizen' || $discountType === 'pwd') {
        $discountAmount = $totalAmount * 0.20; // 20% discount
    } else if (!empty($order['discount_amount'])) {
        $discountAmount = floatval($order['discount_amount']);
    }
}

$finalAmount = $totalAmount - $discountAmount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - Casa Estela Boutique Hotel & Cafe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            font-size: 12px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .logo-img {
            max-width: 250px;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .info-section {
            margin-bottom: 12px;
        }
        .info-section h3 {
            margin: 8px 0;
            font-size: 14px;
        }
        .info-row {
            display: flex;
            margin-bottom: 3px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .info-value {
            flex: 1;
        }
        .total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-size: 12px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .footer p {
            margin: 3px 0;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            @page {
                size: auto;
                margin: a4;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="../images/IMG_8064.jpeg" alt="Casa Estela Boutique Hotel & Cafe" class="logo-img" id="logo-img" onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';">
                <div id="text-logo" style="display:none; color: #c8a955; font-size: 24px; font-weight: bold; margin-bottom: 5px;">
                    CASA ESTELA<br>
                    <span style="font-size: 16px;">BOUTIQUE HOTEL & CAFÉ</span>
                </div>
            </div>
            <div><b>ORDER RECEIPT</b></div>
        </div>
        
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 48%;">
                <div class="info-section">
                    <h3>Customer Information</h3>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo !empty($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'Walk-in Customer'; ?></span>
                    </div>
                    <?php if (!empty($order['email'])): ?>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($order['contact'])): ?>
                    <div class="info-row">
                        <span class="info-label">Contact:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['contact']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="width: 48%;">
                <div class="info-section">
                    <h3>Order Details</h3>
                    <div class="info-row">
                        <span class="info-label">Order #:</span>
                        <span class="info-value"><?php echo $order['id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Type:</span>
                        <span class="info-value"><?php echo ($order['order_type'] === 'advance') ? 'Advance Order' : 'Walk-in Order'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value"><?php echo ucfirst(htmlspecialchars($order['status'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value"><?php echo $orderDate; ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Order Items</h3>
            <ul>
                <?php 
                if (!empty($items)) {
                    foreach ($items as $item) {
                        echo '<li>' . htmlspecialchars($item) . '</li>';
                    }
                } else {
                    echo '<li>No items found</li>';
                }
                ?>
            </ul>
            
            <?php if (!empty($order['addons'])): ?>
            <h3>Add-ons</h3>
            <ul>
                <?php 
                $addons = explode(', ', $order['addons']);
                foreach ($addons as $addon) {
                    echo '<li>' . htmlspecialchars($addon) . '</li>';
                }
                ?>
            </ul>
            <?php endif; ?>
        </div>
        
        <div class="info-section">
            <h3>Payment Information</h3>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 48%;">
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value"><?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></span>
                    </div>
                </div>
                <div style="width: 48%;">
                    <div class="info-row">
                        <span class="info-label">Subtotal:</span>
                        <span class="info-value">₱<?php echo number_format($totalAmount, 2); ?></span>
                    </div>
                    
                    <?php if ($discountAmount > 0): ?>
                    <div class="info-row">
                        <span class="info-label">Discount:</span>
                        <span class="info-value">
                            -₱<?php echo number_format($discountAmount, 2); ?>
                            <?php if ($discountType === 'senior_citizen' || $discountType === 'pwd'): ?>
                            (20% <?php echo ucwords(str_replace('_', ' ', $discountType)); ?>)
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Final Amount:</span>
                        <span class="info-value">₱<?php echo number_format($finalAmount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-row" style="font-size: 14px; font-weight: bold; margin-top: 5px; padding: 6px; background-color: #f9f9f9; border: 2px solid #28a745; border-radius: 5px;">
                        <span class="info-label">Amount Paid:</span>
                        <span class="info-value">₱<?php echo number_format($amountPaid, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($remainingBalance > 0): ?>
            <div class="info-row" style="font-size: 14px; font-weight: bold; margin-top: 5px; padding: 6px; background-color: #fff8f8; border: 2px solid #dc3545; border-radius: 5px;">
                <span class="info-label">Balance Due:</span>
                <span class="info-value">₱<?php echo number_format($remainingBalance, 2); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="total">
            TOTAL PAID: ₱<?php echo number_format($amountPaid, 2); ?>
        </div>
        
        <div style="margin-top: 10px; border-top: 1px dotted #ccc; padding-top: 8px;">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <strong>Payment Status:</strong> 
                    <?php if ($isFullyPaid): ?>
                        <span style="color: #28a745; font-weight: bold;">FULLY PAID</span>
                    <?php else: ?>
                        <span style="color: #dc3545; font-weight: bold;">PARTIALLY PAID</span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong>Receipt Date:</strong> <?php echo date('M d, Y h:i A'); ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing Casa Estela Boutique Hotel & Cafe! This is an official receipt of your order.</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 10px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        // Automatically open print dialog when page loads
        window.onload = function() {
            // Try to load the logo image from various possible paths
            var logoImg = document.getElementById('logo-img');
            var possiblePaths = [
                '../images/IMG_8064.jpeg',
                '../../images/IMG_8064.jpeg',
                '/images/IMG_8064.jpeg',
                'images/IMG_8064.jpeg',
                'IMG_8064.jpeg'
            ];
            
            function tryNextPath(index) {
                if (index >= possiblePaths.length) {
                    console.log('Could not load logo from any path');
                    return;
                }
                
                logoImg.src = possiblePaths[index];
                logoImg.onerror = function() {
                    console.log('Failed to load logo from: ' + possiblePaths[index]);
                    tryNextPath(index + 1);
                };
                logoImg.onload = function() {
                    console.log('Successfully loaded logo from: ' + possiblePaths[index]);
                };
            }
            
            // Check if image failed to load
            if (!logoImg.complete || logoImg.naturalWidth === 0) {
                tryNextPath(0);
            }
            
            setTimeout(function() {
                window.print();
            }, 1000); // Increased delay to allow image to load
        };
    </script>
</body>
</html> 