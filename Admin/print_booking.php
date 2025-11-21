<?php
require_once 'includes/init.php';
require_once 'db.php';

// Check if booking ID is provided
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    echo "Error: No booking ID provided.";
    exit;
}

$booking_id = $_GET['booking_id'];

// Get booking details
$sql = "SELECT 
    b.booking_id,
    b.first_name,
    b.last_name,
    b.email,
    b.contact,
    b.check_in,
    b.check_out,
    b.number_of_guests,
    b.payment_method,
    b.payment_option,
    b.total_amount,
    b.downpayment_amount,
    CASE 
        WHEN b.payment_option = 'full' THEN b.total_amount
        WHEN b.payment_option = 'downpayment' THEN b.downpayment_amount
        ELSE b.total_amount -- Default to total amount if payment_option is not specified
    END as amount_paid,
    rt.room_type,
    rt.price as room_price,
    DATEDIFF(b.check_out, b.check_in) as nights_stayed
FROM bookings b
LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
WHERE b.booking_id = $booking_id";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Error: Booking not found.";
    exit;
}

$booking = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - Casa Estela Boutique Hotel & Cafe</title>
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
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/images/IMG_8064.jpeg'; ?>" alt="Casa Estela Boutique Hotel & Cafe" class="logo-img" id="logo-img" onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';">
                <div id="text-logo" style="display:none; color: #c8a955; font-size: 24px; font-weight: bold; margin-bottom: 5px;">
                    CASA ESTELA<br>
                    <span style="font-size: 16px;">BOUTIQUE HOTEL & CAFÉ</span>
                </div>
            </div>
            <div><b>BOOKING RECEIPT</b></div>
        </div>
        
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 48%;">
                <div class="info-section">
                    <h3>Guest Information</h3>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($booking['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact:</span>
                        <span class="info-value"><?php echo htmlspecialchars($booking['contact']); ?></span>
                    </div>
                </div>
            </div>
            
            <div style="width: 48%;">
                <div class="info-section">
                    <h3>Booking Details</h3>
                    <div class="info-row">
                        <span class="info-label">Booking ID:</span>
                        <span class="info-value"><?php echo $booking['booking_id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Room Type:</span>
                        <span class="info-value"><?php echo htmlspecialchars($booking['room_type']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Check In:</span>
                        <span class="info-value"><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Check Out:</span>
                        <span class="info-value"><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nights:</span>
                        <span class="info-value"><?php echo $booking['nights_stayed']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Guests:</span>
                        <span class="info-value"><?php echo $booking['number_of_guests']; ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Nights</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($booking['room_type']); ?> Room</td>
                    <td><?php echo $booking['nights_stayed']; ?></td>
                    <td>₱<?php echo number_format($booking['room_price'], 2); ?></td>
                    <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="info-section">
            <h3>Payment Information</h3>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 48%;">
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value"><?php echo htmlspecialchars($booking['payment_method']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Option:</span>
                        <span class="info-value"><?php echo ucfirst(htmlspecialchars($booking['payment_option'])); ?></span>
                    </div>
                    <?php if ($booking['payment_option'] == 'downpayment'): ?>
                    <div class="info-row">
                        <span class="info-label">Downpayment:</span>
                        <span class="info-value">₱<?php echo number_format($booking['downpayment_amount'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="width: 48%;">
                    <div class="info-row">
                        <span class="info-label">Total Amount:</span>
                        <span class="info-value">₱<?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>
                    <div class="info-row" style="font-size: 14px; font-weight: bold; margin-top: 5px; padding: 6px; background-color: #f9f9f9; border: 2px solid #28a745; border-radius: 5px;">
                        <span class="info-label">Amount Paid:</span>
                        <span class="info-value">₱<?php echo number_format($booking['amount_paid'], 2); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if($booking['total_amount'] > $booking['amount_paid']): ?>
            <div class="info-row" style="font-size: 14px; font-weight: bold; margin-top: 5px; padding: 6px; background-color: #fff8f8; border: 2px solid #dc3545; border-radius: 5px;">
                <span class="info-label">Balance Due:</span>
                <span class="info-value">₱<?php echo number_format($booking['total_amount'] - $booking['amount_paid'], 2); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="total">
            TOTAL PAID: ₱<?php echo number_format($booking['amount_paid'], 2); ?>
        </div>
        
        <div style="margin-top: 10px; border-top: 1px dotted #ccc; padding-top: 8px;">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <strong>Payment Status:</strong> 
                    <?php if($booking['total_amount'] == $booking['amount_paid']): ?>
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
            <p>Thank you for choosing Casa Estela Boutique Hotel & Cafe! This is an official receipt of your booking.</p>
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
                '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/images/IMG_8064.jpeg'; ?>',
                'images/IMG_8064.jpeg',
                'IMG_8064.jpeg',
                '../images/IMG_8064.jpeg',
                './images/IMG_8064.jpeg'
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