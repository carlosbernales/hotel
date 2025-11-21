<?php
require_once 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if required parameters are present
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    die("Missing required parameters. Please provide both 'id' and 'type'.");
}

$id = mysqli_real_escape_string($con, $_GET['id']);
$type = mysqli_real_escape_string($con, $_GET['type']);

// Initialize variables
$booking_data = null;
$error_message = '';

try {
    // Different queries based on booking type
    if ($type === 'room') {
        $sql = "SELECT 
            b.*,
            rt.room_type,
            rt.price as room_base_price,
            rb.room_name,
            rb.room_price,
            rb.number_of_nights,
            DATEDIFF(b.check_out, b.check_in) as total_nights
        FROM bookings b
        LEFT JOIN room_bookings rb ON b.booking_id = rb.booking_id
        LEFT JOIN room_types rt ON rb.room_type_id = rt.room_type_id
        WHERE b.booking_id = ?";
    } elseif ($type === 'event') {
        $sql = "SELECT * FROM event_bookings WHERE id = ?";
    } else {
        throw new Exception("Invalid booking type specified");
    }

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        throw new Exception("Error executing query: " . mysqli_error($con));
    }
    
    $booking_data = mysqli_fetch_assoc($result);
    
    if (!$booking_data) {
        throw new Exception("No booking found with the provided ID");
    }

} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Casa Estela</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .receipt-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .receipt {
                border: none;
            }
        }
    </style>
</head>
<body>
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php else: ?>
        <div class="receipt">
            <div class="receipt-header">
                <img src="assets/img/logo.png" alt="Casa Estela Logo">
                <h2>Casa Estela</h2>
                <p>Official Receipt</p>
            </div>

            <div class="receipt-details">
                <div class="row">
                    <div class="col-6">
                        <p><strong>Receipt No:</strong> <?php echo $type === 'room' ? $booking_data['booking_id'] : $booking_data['id']; ?></p>
                        <p><strong>Date:</strong> <?php echo date('F d, Y'); ?></p>
                    </div>
                    <div class="col-6 text-right">
                        <p><strong>Guest Name:</strong> 
                            <?php 
                            if ($type === 'room') {
                                echo htmlspecialchars($booking_data['first_name'] . ' ' . $booking_data['last_name']);
                            } else {
                                echo htmlspecialchars($booking_data['customer_name']);
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <table class="table receipt-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($type === 'room'): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($booking_data['room_type']); ?><br>
                                Check-in: <?php echo date('M d, Y', strtotime($booking_data['check_in'])); ?><br>
                                Check-out: <?php echo date('M d, Y', strtotime($booking_data['check_out'])); ?><br>
                                Number of nights: <?php echo $booking_data['total_nights']; ?>
                            </td>
                            <td class="text-right">₱<?php echo number_format($booking_data['total_amount'], 2); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td>
                                Event Booking<br>
                                Date: <?php echo date('M d, Y', strtotime($booking_data['booking_date'])); ?><br>
                                Package: <?php echo htmlspecialchars($booking_data['package_name']); ?><br>
                                Number of guests: <?php echo $booking_data['num_guests']; ?>
                            </td>
                            <td class="text-right">₱<?php echo number_format($booking_data['total_amount'], 2); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Amount</th>
                        <th class="text-right">₱<?php echo number_format($booking_data['total_amount'], 2); ?></th>
                    </tr>
                    <?php if ($type === 'room' && $booking_data['payment_option'] === 'downpayment'): ?>
                        <tr>
                            <td>Downpayment Paid</td>
                            <td class="text-right">₱<?php echo number_format($booking_data['downpayment_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Remaining Balance</td>
                            <td class="text-right">₱<?php echo number_format($booking_data['total_amount'] - $booking_data['downpayment_amount'], 2); ?></td>
                        </tr>
                    <?php endif; ?>
                </tfoot>
            </table>

            <div class="receipt-footer">
                <p>Thank you for choosing Casa Estela!</p>
                <p>For inquiries, please contact us at: info@casaestela.com</p>
            </div>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>
    <?php endif; ?>

    <script>
        // Automatically open print dialog when the page loads
        window.onload = function() {
            // Small delay to ensure everything is loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
