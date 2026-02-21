<?php
session_start();
require_once 'db_con.php';

// Check if order_id is provided
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('Location: table.php');
    exit();
}

// Get order details from database
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Casa de Alfonso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #b6860a;
            border-color: #b6860a;
        }
        .btn-primary:hover {
            background-color: #9a7209;
            border-color: #9a7209;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="mb-4">Booking Confirmed!</h1>
            <p class="lead mb-4">Thank you for your booking at Casa de Alfonso.</p>
            
            <div class="booking-details text-start mb-4">
                <h4>Booking #<?php echo htmlspecialchars($order['id']); ?></h4>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['booking_date'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($order['booking_time'])); ?></p>
                <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Status:</strong> <span class="badge bg-success">Confirmed</span></p>
            </div>

            <div class="mt-4">
                <a href="table.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
                <button class="btn btn-outline-secondary ms-2" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
