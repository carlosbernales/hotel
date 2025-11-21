<?php
session_start();
require_once 'db_con.php';

if (!isset($_SESSION['order_success']) || !isset($_SESSION['order_id'])) {
    header('Location: cafes.php');
    exit;
}

$orderId = $_SESSION['order_id'];

// Clear the session variables
unset($_SESSION['order_success']);
unset($_SESSION['order_id']);

// Fetch order details
$query = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $orderId);
mysqli_stmt_execute($stmt);
$order = mysqli_stmt_get_result($stmt)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <!-- Include your CSS files -->
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                <h2 class="mt-3">Order Placed Successfully!</h2>
                <p class="lead">Your order number is: #<?php echo $orderId; ?></p>
                <p>Please pick up your order at <?php echo $order['pickup_time']; ?></p>
                <p>Payment Method: <?php echo ucfirst($order['payment_method']); ?></p>
                <a href="cafes.php" class="btn btn-primary mt-3">Back to Menu</a>
            </div>
        </div>
    </div>
</body>
</html> 