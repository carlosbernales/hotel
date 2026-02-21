<?php
session_start();
require 'db_con.php';
require 'vendor/autoload.php'; // Make sure you have TCPDF installed

if (!isset($_SESSION['userid'])) {
    header('location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('location:reservations.php');
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['userid'];

try {
    // Get order details with items
    $stmt = $pdo->prepare("
        SELECT o.*, u.firstname, u.lastname, u.email, u.phone,
        GROUP_CONCAT(CONCAT(oi.quantity, 'x ', m.name, ' (₱', m.price, ')') SEPARATOR '\n') as items_detail
        FROM cafe_orders o
        LEFT JOIN cafe_order_items oi ON o.order_id = oi.order_id
        LEFT JOIN menu m ON oi.menu_id = m.id
        LEFT JOIN users u ON o.user_id = u.userid
        WHERE o.order_id = ? AND o.user_id = ?
        GROUP BY o.order_id
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['error'] = "Invalid order or unauthorized access.";
        header('location:reservations.php');
        exit();
    }

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Casa Estela');
    $pdf->SetAuthor('Casa Estela');
    $pdf->SetTitle('Order Receipt #' . $order_id);

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add content
    $html = '
    <h1>Casa Estela Order Receipt</h1>
    <h2>Order #' . $order_id . '</h2>
    
    <h3>Customer Information</h3>
    <p>
    Name: ' . htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) . '<br>
    Email: ' . htmlspecialchars($order['email']) . '<br>
    Phone: ' . htmlspecialchars($order['phone']) . '
    </p>

    <h3>Order Details</h3>
    <p>
    Status: ' . ucfirst($order['status']) . '<br>
    Order Date: ' . date('F j, Y g:i A', strtotime($order['created_at'])) . '<br>';

    if ($order['order_date']) {
        $html .= 'For: ' . date('F j, Y g:i A', strtotime($order['order_date'])) . '<br>';
    }

    $html .= '</p>

    <h3>Items Ordered</h3>
    <pre>' . htmlspecialchars($order['items_detail']) . '</pre>
    
    <p><strong>Total Amount: ₱' . number_format($order['total_amount'], 2) . '</strong></p>

    <h3>Terms and Conditions</h3>
    <p>
    1. All prices are inclusive of VAT.<br>
    2. Orders cannot be cancelled once preparation has begun.<br>
    3. For advance orders, please arrive on time.<br>
    4. Payment must be settled before order preparation.<br>
    5. Please check your order upon receipt.
    </p>

    <p>Thank you for dining at Casa Estela!</p>
    ';

    // Write HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('Casa_Estela_Order_' . $order_id . '.pdf', 'D');

} catch (PDOException $e) {
    $_SESSION['error'] = "Error generating PDF: " . $e->getMessage();
    header('location:reservations.php');
    exit();
}
