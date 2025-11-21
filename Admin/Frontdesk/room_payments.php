<?php
require_once 'db.php';

require_once 'header.php';
require_once 'sidebar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create payments table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    booking_type ENUM('room', 'table', 'event') NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('gcash', 'maya', 'cash') NOT NULL,
    reference_number VARCHAR(100) NOT NULL,
    receipt_image VARCHAR(255),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    notes TEXT,
    booking_date DATE,
    booking_type_details VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

// Fetch room payments
$query = "SELECT * FROM payments WHERE booking_type = 'room' ORDER BY payment_date DESC";
$payments = $con->query($query);

// Check if query was successful
if (!$payments) {
    $_SESSION['error_message'] = "Error fetching payments: " . $con->error;
    $payments = []; // Set empty array as fallback
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i></a></li>
            <li class="active">Room Payments</li>
        </ol>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="payments-section">
                <div class="section-header">
                    <h2>Room Booking Payments</h2>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Reference Number</th>
                                <th>Receipt</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($payments && $payments->num_rows > 0): ?>
                                <?php while ($payment = $payments->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payment['customer_name']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                Check-in: <?php echo $payment['booking_date'] ? date('F j, Y', strtotime($payment['booking_date'])) : 'N/A'; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="payment-method method-<?php echo strtolower($payment['payment_method']); ?>">
                                                <?php echo strtoupper($payment['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($payment['reference_number']); ?></td>
                                        <td>
                                            <?php if ($payment['receipt_image']): ?>
                                                <img src="uploads/receipts/<?php echo htmlspecialchars($payment['receipt_image']); ?>" 
                                                     class="receipt-image" 
                                                     onclick="showReceiptModal(this.src)"
                                                     alt="Payment Receipt">
                                            <?php else: ?>
                                                <span class="text-muted">No receipt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $payment['status']; ?>">
                                                <?php echo ucfirst($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($payment['status'] === 'pending'): ?>
                                                <button class="btn-verify" onclick="verifyPayment(<?php echo $payment['id']; ?>)">
                                                    <i class="fa fa-check"></i> Accept
                                                </button>
                                                <button class="btn-reject" onclick="rejectPayment(<?php echo $payment['id']; ?>)">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No payments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="receipt-modal">
    <span class="receipt-modal-close" onclick="closeReceiptModal()">&times;</span>
    <img class="receipt-modal-content" id="receiptModalImg">
</div>

<style>
    /* Main container adjustments */
    .col-sm-9.col-sm-offset-3.col-lg-10.col-lg-offset-2.main {
        padding-right: 0;
        padding-left: 0;
        width: calc(100% - 240px);
        margin-left: 240px;
    }

    .row {
        margin-right: 0;
        margin-left: 0;
    }

    .col-md-12 {
        padding-right: 15px;
        padding-left: 15px;
    }

    .payments-section {
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        margin-right: 15px;
    }

    .table-responsive {
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    /* Adjust column widths */
    .table th:nth-child(1), .table td:nth-child(1) { width: 20%; } /* Customer Name */
    .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Payment Method */
    .table th:nth-child(3), .table td:nth-child(3) { width: 12%; } /* Amount */
    .table th:nth-child(4), .table td:nth-child(4) { width: 15%; } /* Reference Number */
    .table th:nth-child(5), .table td:nth-child(5) { width: 15%; } /* Receipt */
    .table th:nth-child(6), .table td:nth-child(6) { width: 12%; } /* Status */
    .table th:nth-child(7), .table td:nth-child(7) { width: 14%; } /* Actions */

    .table th {
        background-color: #DAA520;
        color: white;
        font-weight: 500;
        padding: 12px 15px;
        white-space: nowrap;
    }

    .table td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .receipt-image {
        max-width: 100px;
        cursor: pointer;
    }

    .receipt-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
    }

    .receipt-modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    .receipt-modal-close {
        position: absolute;
        right: 35px;
        top: 15px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    .payment-method {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: 600;
    }
    
    .method-gcash {
        background: #0074E4;
        color: white;
    }
    
    .method-maya {
        background: #00B7C2;
        color: white;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9em;
    }
    
    .status-pending {
        background: #ffc107;
        color: #000;
    }
    
    .status-verified {
        background: #28a745;
        color: white;
    }
    
    .status-rejected {
        background: #dc3545;
        color: white;
    }

    .btn-verify, .btn-reject {
        padding: 5px 15px;
        border: none;
        border-radius: 4px;
        color: white;
        cursor: pointer;
        margin: 0 5px;
    }
    
    .btn-verify {
        background: #28a745;
    }
    
    .btn-reject {
        background: #dc3545;
    }
</style>

<script>
function showReceiptModal(src) {
    var modal = document.getElementById('receiptModal');
    var modalImg = document.getElementById('receiptModalImg');
    modal.style.display = "block";
    modalImg.src = src;
}

function closeReceiptModal() {
    var modal = document.getElementById('receiptModal');
    modal.style.display = "none";
}

function verifyPayment(paymentId) {
    if (confirm('Are you sure you want to verify this payment?')) {
        // Add your verification logic here
        console.log('Verifying payment:', paymentId);
    }
}

function rejectPayment(paymentId) {
    if (confirm('Are you sure you want to reject this payment?')) {
        // Add your rejection logic here
        console.log('Rejecting payment:', paymentId);
    }
}
</script>

<?php include_once 'footer.php'; ?> 