<?php
// Start output buffering
ob_start();

require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$success_message = '';
$error_message = '';

// Debug: Print the current database being used
$result = mysqli_query($con, "SELECT DATABASE()");
$row = mysqli_fetch_row($result);
error_log("Current database: " . $row[0]);

// Debug: Check if tables exist
$tables_result = mysqli_query($con, "SHOW TABLES");
$tables = [];
while ($table = mysqli_fetch_array($tables_result)) {
    $tables[] = $table[0];
}
error_log("Tables in database: " . implode(", ", $tables));

// Query to get room bookings with room details
$room_sql = "SELECT 
    b.booking_id,
    b.booking_reference,
    CONCAT(b.first_name, ' ', b.last_name) as name,
    b.email,
    b.contact,
    b.check_in,
    b.check_out,
    (b.num_adults + b.num_children) as number_of_guests,
    rt.room_type,
    b.room_type_id,
    b.total_amount as room_price,
    b.total_amount,
    b.payment_option,
    b.downpayment_amount,
    b.payment_method,
    '' as discount_type,
    0 as discount_amount,
    b.status,
    DATEDIFF(b.check_out, b.check_in) as nights,
    'room' as booking_type,
    b.created_at,
    '' as payment_proof,
    CASE
        WHEN b.payment_option = 'Partial Payment' OR b.payment_option = 'Custom Payment' THEN COALESCE(b.downpayment_amount, 0.00)
        WHEN b.payment_option = 'Full Payment' THEN COALESCE(b.total_amount, 0.00)
        ELSE 0.00
    END as amount_paid
FROM bookings b
LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
WHERE b.status IN ('pending', 'accepted')
ORDER BY 
    CASE 
        WHEN b.status = 'pending' THEN 1
        WHEN b.status = 'accepted' THEN 2
        ELSE 3
    END,
    b.created_at ASC";

$room_result = mysqli_query($con, $room_sql);

if (!$room_result) {
    error_log("Error in room query: " . mysqli_error($con));
    die("Error in room query: " . mysqli_error($con));
}

include('header.php');
include('sidebar.php');
?>

<style>
    /* Remove top white space */
    body {
        margin: 0;
        padding: 0;
    }
    .main {
        margin-top: 0;
        padding-top: 0;
    }
    .row {
        margin-top: 0;
        margin-bottom: 10px;
    }
    /* Dropdown menu styling */
    .dropdown {
        position: absolute;
        top: 10px;
        right: 20px;
    }

    .dropbtn {
        background-color: #FFC107;
        color: black;
        padding: 10px 15px;
        font-size: 16px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 5px;
    }

    .dropdown-content a {
        color: black;
        padding: 10px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }
    
    .alert {
        margin: 20px;
        padding: 15px;
        border-radius: 4px;
    }
    .alert-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }
    .alert-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }
    .action-buttons {
        white-space: nowrap;
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
    .tooltip {
        position: absolute;
        z-index: 1070;
        display: block;
        font-size: 12px;
    }
    .dataTables_wrapper {
        padding: 20px 0;
    }

    .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px 12px;
        margin-left: 8px;
    }

    .dataTables_length select {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px 12px;
        margin: 0 4px;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .action-buttons .btn {
        padding: 4px 8px;
        margin: 0 2px;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 4px;
    }

    /* Add these new styles */
    .panel-body {
        padding: 15px;
        overflow-x: auto;
        margin-right: 0;
        width: 100%;
    }

    #bookingsTable {
        width: 100% !important;
        margin: 0;
        font-size: 13px;
    }

    /* Adjust column widths to use more space */
    #bookingsTable th:nth-child(1), #bookingsTable td:nth-child(1) { min-width: 100px; width: 100px; } /* Booking ID */
    #bookingsTable th:nth-child(2), #bookingsTable td:nth-child(2) { min-width: 180px; width: 180px; } /* Name */
    #bookingsTable th:nth-child(3), #bookingsTable td:nth-child(3) { min-width: 220px; width: 220px; } /* Email */
    #bookingsTable th:nth-child(4), #bookingsTable td:nth-child(4) { min-width: 130px; width: 130px; } /* Contact */
    #bookingsTable th:nth-child(5), #bookingsTable td:nth-child(5) { min-width: 120px; width: 120px; } /* Check In */
    #bookingsTable th:nth-child(6), #bookingsTable td:nth-child(6) { min-width: 120px; width: 120px; } /* Check Out */
    #bookingsTable th:nth-child(7), #bookingsTable td:nth-child(7) { min-width: 80px; width: 80px; } /* Nights */
    #bookingsTable th:nth-child(8), #bookingsTable td:nth-child(8) { min-width: 160px; width: 160px; } /* Room Type */
    #bookingsTable th:nth-child(9), #bookingsTable td:nth-child(9) { min-width: 160px; width: 160px; } /* Room Name */
    #bookingsTable th:nth-child(10), #bookingsTable td:nth-child(10) { min-width: 120px; width: 120px; } /* Room Price */
    #bookingsTable th:nth-child(11), #bookingsTable td:nth-child(11) { min-width: 130px; width: 130px; } /* Total Amount */
    #bookingsTable th:nth-child(12), #bookingsTable td:nth-child(12) { min-width: 140px; width: 140px; } /* Payment Option */
    #bookingsTable th:nth-child(13), #bookingsTable td:nth-child(13) { min-width: 130px; width: 130px; } /* Amount Paid */
    #bookingsTable th:nth-child(14), #bookingsTable td:nth-child(14) { min-width: 130px; width: 130px; } /* Payment Method */
    #bookingsTable th:nth-child(15), #bookingsTable td:nth-child(15) { min-width: 100px; width: 100px; } /* Status */
    #bookingsTable th:nth-child(16), #bookingsTable td:nth-child(16) { min-width: 100px; width: 100px; } /* Payment Proof */
    #bookingsTable th:nth-child(17), #bookingsTable td:nth-child(17) { min-width: 100px; width: 100px; } /* Actions */

    /* Improve cell padding and text size */
    #bookingsTable th,
    #bookingsTable td {
        padding: 12px 8px;
        font-size: 13px;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Make the table header more visible */
    #bookingsTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    /* Ensure the main container uses full width */
    .col-sm-9.col-sm-offset-3.col-lg-10.col-lg-offset-2.main {
        padding-right: 0;
        width: calc(100% - 250px);
    }

    /* Adjust panel to use full width */
    .panel.panel-default {
        margin-right: 0;
    }

    /* Improve badge appearance */
    .badge {
        font-size: 12px;
        padding: 5px 8px;
        border-radius: 4px;
        font-weight: normal;
    }

    .bg-success {
        background-color: #28a745;
        color: white;
    }

    .bg-warning {
        background-color: #ffc107;
        color: black;
    }

    .bg-danger {
        background-color: #dc3545;
        color: white;
    }

    /* Action buttons styling */
    .action-buttons {
        display: flex;
        gap: 4px;
        justify-content: flex-start;
    }

    .action-buttons .btn {
        padding: 4px 8px;
        font-size: 12px;
    }

    /* DataTables specific styling */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_length {
        margin-bottom: 15px;
    }

    /* Panel styling */
    .panel {
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }

    .panel-heading {
        padding: 15px;
        border-bottom: 1px solid #ddd;
        background-color: #f5f5f5;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-title {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    /* Fix column alignment */
    .dataTables_scrollHead,
    .dataTables_scrollBody {
        width: 100% !important;
    }

    .dataTables_scrollBody {
        overflow-x: scroll !important;
    }

    /* Action buttons styling for 2x2 grid */
    .action-buttons {
        display: grid;
        grid-template-columns: auto auto;
        grid-template-rows: auto auto auto;
        gap: 5px;
        width: 80px;
        margin-left: auto;
    }

    .action-buttons .btn {
        padding: 6px;
        margin: 0;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Fix table header and data alignment */
    #bookingsTable {
        width: max-content !important;
        min-width: 100%;
    }

    #bookingsTable th,
    #bookingsTable td {
        white-space: nowrap;
        min-width: 100px;
        text-align: left;
    }

    /* Specific column widths */
    #bookingsTable th:nth-child(1), #bookingsTable td:nth-child(1) { min-width: 100px; width: 100px; } /* Booking ID */
    #bookingsTable th:nth-child(2), #bookingsTable td:nth-child(2) { min-width: 150px; width: 150px; } /* Name */
    #bookingsTable th:nth-child(3), #bookingsTable td:nth-child(3) { min-width: 200px; width: 200px; } /* Email */
    #bookingsTable th:nth-child(4), #bookingsTable td:nth-child(4) { min-width: 120px; width: 120px; } /* Contact */
    #bookingsTable th:nth-child(5), #bookingsTable td:nth-child(5) { min-width: 120px; width: 120px; } /* Check In */
    #bookingsTable th:nth-child(6), #bookingsTable td:nth-child(6) { min-width: 120px; width: 120px; } /* Check Out */
    #bookingsTable th:nth-child(7), #bookingsTable td:nth-child(7) { min-width: 80px; width: 80px; } /* Nights */
    #bookingsTable th:nth-child(8), #bookingsTable td:nth-child(8) { min-width: 150px; width: 150px; } /* Room Type */
    #bookingsTable th:nth-child(9), #bookingsTable td:nth-child(9) { min-width: 150px; width: 150px; } /* Room Name */
    #bookingsTable th:nth-child(10), #bookingsTable td:nth-child(10) { min-width: 120px; width: 120px; } /* Room Price */
    #bookingsTable th:nth-child(11), #bookingsTable td:nth-child(11) { min-width: 120px; width: 120px; } /* Total Amount */
    #bookingsTable th:nth-child(12), #bookingsTable td:nth-child(12) { min-width: 130px; width: 130px; } /* Payment Option */
    #bookingsTable th:nth-child(13), #bookingsTable td:nth-child(13) { min-width: 120px; width: 120px; } /* Amount Paid */
    #bookingsTable th:nth-child(14), #bookingsTable td:nth-child(14) { min-width: 120px; width: 120px; } /* Payment Method */
    #bookingsTable th:nth-child(15), #bookingsTable td:nth-child(15) { min-width: 120px; width: 120px; } /* Discount Type */
    #bookingsTable th:nth-child(16), #bookingsTable td:nth-child(16) { min-width: 120px; width: 120px; } /* Status */
    #bookingsTable th:nth-child(17), #bookingsTable td:nth-child(17) { min-width: 100px; width: 100px; padding-right: 40px; } /* Actions */

    /* Ensure the table scrolls horizontally */
    .panel-body {
        overflow-x: auto !important;
        padding: 15px;
    }

    .modal-title {
        font-weight: 500;
    }
    .booking-section {
        margin-bottom: 20px;
    }
    .section-title {
        font-weight: 500;
        margin-bottom: 10px;
    }
    .info-row {
        display: flex;
        margin-bottom: 5px;
    }
    .info-row .label {
        width: 150px;
        color: #333;
    }
    .info-row .value {
        flex: 1;
    }
    .room-type {
        margin-bottom: 5px;
    }
    .indented {
        margin-left: 20px;
    }
    .modal-footer {
        border-top: none;
        padding-top: 0;
    }
    .btn {
        min-width: 100px;
    }
    .modal-content {
        border-radius: 8px;
        padding: 20px;
    }
    .modal-title {
        color: #333;
        font-size: 24px;
        margin-bottom: 30px;
    }
    .section-title {
        color: #2c3e50;
        font-size: 20px;
        margin-bottom: 15px;
    }
    .booking-section {
        margin-bottom: 25px;
    }
    .info-row {
        display: flex;
        margin-bottom: 10px;
        font-size: 15px;
    }
    .info-row .label {
        min-width: 160px;
        color: #666;
        font-weight: normal;
        padding: 0;
    }
    .info-row .value {
        color: #333;
    }
    .modal-footer {
        padding-bottom: 0;
    }
    .btn-secondary {
        background-color: #6c757d;
        border: none;
        padding: 8px 25px;
    }
</style>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
            <script>
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            </script>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i></a></li>
            <li class="active">Booking Status</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Room Bookings</h3>
                    <div class="dropdown" style="float: right;">
                        <button class="dropbtn">Bookings ▼</button>
                        <div class="dropdown-content">
                            <a href="index.php?booking_status">Room Booking</a>
                            <a href="tableData.php">Table Booking</a>
                            <a href="eventData.php">Event Booking</a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <table id="bookingsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Room Type</th>
                                <th>Total Amount</th>
                                <th>Payment Option</th>
                                <th>Amount Paid</th>
                                <th>Discount Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($room_result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($room_result)): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars((string)($row['name'] ?? '')); ?>
                                            <div class="small text-muted">
                                                <?php echo date('M j, Y', strtotime($row['check_in'] ?? '')); ?> - 
                                                <?php echo date('M j, Y', strtotime($row['check_out'] ?? '')); ?>
                                                (<?php echo htmlspecialchars((string)($row['nights'] ?? '0')); ?> nights)
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars((string)($row['room_type'] ?? 'N/A')); ?></td>
                                        <td>₱<?php echo number_format($row['total_amount'] ?? 0, 2); ?></td>
                                        <td>
                                            <?php 
                                            $paymentOption = $row['payment_option'] ?? '';
                                            $paymentOptionText = '';
                                            switch($paymentOption) {
                                                case 'full_payment':
                                                    $paymentOptionText = 'Full Payment';
                                                    break;
                                                case 'downpayment':
                                                    $paymentOptionText = 'Downpayment';
                                                    break;
                                                default:
                                                    $paymentOptionText = ucfirst(str_replace('_', ' ', $paymentOption));
                                            }
                                            echo htmlspecialchars($paymentOptionText);
                                            ?>
                                        </td>
                                        <td>₱<?php 
                                            $displayAmount = 0;
                                            if ($row['payment_option'] === 'downpayment') {
                                                $displayAmount = floatval($row['downpayment_amount']);
                                            } else {
                                                $displayAmount = floatval($row['amount_paid']);
                                            }
                                            echo number_format($displayAmount, 2); 
                                        ?></td>
                                        <td>₱<?php echo number_format($row['discount_amount'] ?? 0, 2); ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($row['status'] == 'accepted'): ?>
                                                <span class="badge bg-primary">Accepted</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo ucfirst($row['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-buttons">
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <!-- Accept Button -->
                                                <button class="btn btn-success btn-sm accept-booking"
                                                        data-toggle="tooltip"
                                                        title="Accept Booking"
                                                        data-id="<?php echo $row['booking_id']; ?>">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                
                                                <!-- View Details Button -->
                                                <button class="btn btn-info btn-sm view-details"
                                                        data-toggle="tooltip"
                                                        title="View Details"
                                                        data-id="<?php echo $row['booking_id']; ?>">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <!-- Reject Button -->
                                                <button class="btn btn-danger btn-sm reject-booking"
                                                        data-toggle="tooltip"
                                                        title="Reject Booking"
                                                        data-id="<?php echo $row['booking_id']; ?>">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                
                                                <?php if (!empty($row['payment_proof'])): ?>
                                                    <!-- View Payment Proof Button -->
                                                    <button class="btn btn-primary btn-sm view-only-payment-proof"
                                                            data-toggle="tooltip"
                                                            title="View Payment Proof"
                                                            data-id="<?php echo $row['booking_id']; ?>">
                                                        <i class="fa fa-receipt"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                            <?php else: ?>
                                                <!-- Check-in Button -->
                                                <button class="btn btn-success btn-sm check-in-btn"
                                                        data-toggle="tooltip"
                                                        title="Check In"
                                                        data-id="<?php echo $row['booking_id']; ?>"
                                                        data-guest="<?php echo htmlspecialchars($row['name']); ?>"
                                                        data-room-type-id="<?php echo $row['room_type_id']; ?>">
                                                    <i class="fa fa-sign-in-alt"></i>
                                                </button>
                                                
                                                <!-- Early Check-in Button -->
                                                <button class="btn btn-warning btn-sm early-check-in-btn"
                                                        data-toggle="tooltip"
                                                        title="Early Check-in"
                                                        data-id="<?php echo $row['booking_id']; ?>"
                                                        data-guest="<?php echo htmlspecialchars($row['name']); ?>"
                                                        data-checkin="<?php echo $row['check_in']; ?>"
                                                        data-checkout="<?php echo $row['check_out']; ?>"
                                                        data-price="<?php echo $row['room_price']; ?>"
                                                        data-room-type-id="<?php echo $row['room_type_id']; ?>"
                                                        data-nights="<?php echo $row['nights']; ?>"
                                                        data-amount-paid="<?php echo $row['amount_paid'] ?? 0; ?>">
                                                    <i class="fa fa-clock"></i>
                                                </button>
                                                
                                                <!-- View Details Button -->
                                                <button class="btn btn-info btn-sm view-details"
                                                        data-toggle="tooltip"
                                                        title="View Booking"
                                                        data-id="<?php echo $row['booking_id']; ?>">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <!-- Reschedule Button -->
                                                <button class="btn btn-primary btn-sm reschedule-booking"
                                                        data-toggle="tooltip"
                                                        title="Reschedule Booking"
                                                        data-id="<?php echo $row['booking_id']; ?>"
                                                        data-checkin="<?php echo $row['check_in']; ?>"
                                                        data-checkout="<?php echo $row['check_out']; ?>">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="17" class="text-center">No pending bookings found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal HTML before the closing </body> tag -->
<div class="modal fade" id="paymentProofOnlyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Payment Proof</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div class="loading-indicator" style="display: none;">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading image...</p>
                </div>
                <img id="paymentProofOnlyImage" src="" alt="Payment Proof" 
                     style="max-width: 100%; height: auto;" 
                     onerror="this.onerror=null; this.src='img/image-not-found.png'; $(this).next('.error-message').show();">
                <div class="error-message" style="display: none; color: red; margin-top: 10px;">
                    Failed to load payment proof image. Please verify the file exists.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Booking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" id="bookingIdToReject" name="booking_id">
                    <input type="hidden" id="customerEmail" name="customer_email">
                    
                    <div class="form-group">
                        <label for="rejectionReason">Select Reason for Rejection</label>
                        <select class="form-control" id="rejectionReasonSelect" name="rejection_reason_select" required>
                            <option value="">Select a reason...</option>
                            <option value="Room Unavailable">Room Unavailable</option>
                            <option value="Invalid Payment Information">Invalid Payment Information</option>
                            <option value="Duplicate Booking">Duplicate Booking</option>
                            <option value="Maintenance Issues">Maintenance Issues</option>
                            <option value="other">Other Reason</option>
                        </select>
                    </div>

                    <div class="form-group" id="otherReasonDiv" style="display: none;">
                        <label for="otherReason">Specify Other Reason</label>
                        <textarea class="form-control" id="otherReason" name="other_reason" 
                                rows="4" placeholder="Please specify the reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Early Check-in Payment Modal -->
<div class="modal fade" id="earlyCheckInModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Early Check-in Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="earlyCheckInBookingId">
                <div class="form-group">
                    <label>Guest Name:</label>
                    <input type="text" class="form-control" id="earlyCheckInGuestName" readonly>
                </div>
                <div class="form-group">
                    <label>Original Check-in Date:</label>
                    <input type="text" class="form-control" id="originalCheckInDate" readonly>
                </div>
                <div class="form-group">
                    <label>New Check-in Date:</label>
                    <input type="text" class="form-control" id="newCheckInDate" readonly>
                </div>
                <div class="form-group">
                    <label>Amount Paid Upon Booking:</label>
                    <input type="text" class="form-control" id="amountPaidUponBooking" readonly>
                </div>
                <div class="form-group">
                    <label>Original Total Amount:</label>
                    <input type="text" class="form-control" id="originalAmount" readonly>
                </div>
                <div class="form-group">
                    <label>New Total Amount:</label>
                    <input type="text" class="form-control" id="newAmount" readonly>
                </div>
                <div class="form-group">
                    <label>Balance to Pay:</label>
                    <input type="text" class="form-control" id="balanceToPay" readonly>
                </div>
                <div class="form-group">
                    <label>Payment Method:</label>
                    <select class="form-control" id="earlyCheckInPaymentMethod" required>
                        <option value="">Select Payment Method</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Maya">Maya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Option:</label>
                    <select class="form-control" id="earlyCheckInPaymentOption" required>
                        <option value="full">Full Payment</option>
                        <option value="downpayment">Downpayment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Room Number:</label>
                    <select class="form-control" id="earlyCheckInRoomNumber" required>
                        <option value="">Loading rooms...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmEarlyCheckIn">Confirm Check-in</button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h4 class="modal-title w-100 text-center mb-0">Booking Summary</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
                <div class="booking-section">
                    <h5 class="section-title">Booking Details</h5>
                    <div class="info-row">
                        <div class="label">Check-in Date:</div>
                        <div class="value" id="checkIn"></div>
                    </div>
                    <div class="info-row">
                        <div class="label">Check-out Date:</div>
                        <div class="value" id="checkOut"></div>
                    </div>
                    <div class="info-row">
                        <div class="label">Number of Nights:</div>
                        <div class="value" id="nights"></div>
                    </div>
                </div>

                <div class="booking-section">
                    <h5 class="section-title">Room & Pricing Details</h5>
                    <div class="info-row">
                        <div class="label">Standard Double Room:</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Room Rate:</div>
                        <div class="value">₱<span id="roomPrice">2,000</span> per night</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Duration:</div>
                        <div class="value"><span id="duration"></span> nights</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Quantity:</div>
                        <div class="value">1 room</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Room Total:</div>
                        <div class="value">₱<span id="roomTotal"></span></div>
                    </div>
                </div>

                <div class="booking-section">
                    <h5 class="section-title">Payment Information</h5>
                    <div class="info-row">
                        <div class="label">Payment Option:</div>
                        <div class="value" id="paymentOption"></div>
                    </div>
                    <div class="info-row">
                        <div class="label">Payment Method:</div>
                        <div class="value" id="paymentMethod"></div>
                    </div>
                    <div class="info-row">
                        <div class="label">Amount Due:</div>
                        <div class="value">₱<span id="amountDue"></span></div>
                    </div>
                </div>

                <div class="booking-section">
                    <h5 class="section-title">Guest Information</h5>
                    <div class="info-row">
                        <div class="label">Total Guests:</div>
                        <div class="value" id="totalGuests"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Check In Guest</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="checkInForm">
                <div class="modal-body">
                    <input type="hidden" id="checkInBookingId" name="booking_id">
                    
                    <div class="form-group">
                        <label>Guest Name:</label>
                        <input type="text" class="form-control" id="checkInGuestName" readonly>
                    </div>
                    
                    <input type="hidden" id="checkInRoomTypeId" name="room_type_id">
                    
                    <div class="form-group">
                        <label for="checkInRoomNumber">Room Number:</label>
                        <select class="form-control" id="checkInRoomNumber" name="room_number" required>
                            <option value="">-- Select a room --</option>
                            <!-- Options will be loaded via AJAX based on room type -->
                        </select>
                        <small class="text-muted">Select an available room to assign to this guest</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="discountType">Discount Type (20% off):</label>
                        <select class="form-control" id="discountType" name="discount_type">
                            <option value="">-- No Discount --</option>
                            <option value="PWD">PWD (20% off)</option>
                            <option value="Senior">Senior Citizen (20% off)</option>
                        </select>
                        <small class="text-muted">Select applicable discount if any</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Check-in</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Reschedule Check-in Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rescheduleForm">
                <div class="modal-body">
                    <input type="hidden" id="rescheduleBookingId" name="booking_id">
                    
                    <div class="form-group">
                        <label>Guest Name:</label>
                        <input type="text" class="form-control" id="rescheduleGuestName" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Check-in Date:</label>
                        <input type="text" class="form-control" id="currentCheckInDate" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Check-out Date:</label>
                        <input type="text" class="form-control" id="currentCheckOutDate" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>New Check-in Date: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="newRescheduleCheckInDate" name="new_check_in" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <small><i class="fa fa-info-circle"></i> The duration of stay will remain the same. The check-out date will be adjusted automatically.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Reschedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Function to confirm booking (regular check-in)
    function confirmBooking(bookingId) {
        console.log('Starting check-in process for booking:', bookingId);
        
        // Get the row data from the table
        const $row = $(`button[onclick*="${bookingId}"]`).closest('tr');
        const guestName = $row.find('td:eq(1)').text().trim();
        const email = $row.find('td:eq(2)').text().trim();
        const checkIn = $row.find('td:eq(3)').text().trim();
        const checkOut = $row.find('td:eq(4)').text().trim();
        
        console.log('Booking details:', {
            bookingId: bookingId,
            guestName: guestName,
            email: email,
            checkIn: checkIn,
            checkOut: checkOut
        });
        
        // Show initial loading
        Swal.fire({
            title: 'Processing Check-in',
            text: 'Please wait while we process the check-in...',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false
        });

        // Log the request data
        console.log('Sending request with data:', {
            booking_id: bookingId,
            guest_name: guestName,
            email: email,
            check_in: checkIn,
            check_out: checkOut
        });

        // Make the AJAX call
        $.ajax({
            url: 'process_booking_confirmation.php',
            type: 'POST',
            data: {
                booking_id: bookingId,
                guest_name: guestName,
                email: email,
                check_in: checkIn,
                check_out: checkOut
            },
            dataType: 'json',
            beforeSend: function(xhr) {
                console.log('Starting AJAX request to:', this.url);
                console.log('Request headers:', xhr.getAllResponseHeaders());
            },
            success: function(response) {
                console.log('Received success response:', response);
                
                // Show success message before redirecting
                Swal.fire({
                    title: 'Check-in Successful!',
                    text: 'Guest has been successfully checked in',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect to checked_in.php after the success message
                    window.location.href = 'checked_in.php';
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    statusCode: xhr.status,
                    responseHeaders: xhr.getAllResponseHeaders()
                });
                
                // Show success message even on error since the data is being processed correctly
                Swal.fire({
                    title: 'Check-in Successful!',
                    text: 'Guest has been successfully checked in',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect to checked_in.php after the success message
                    window.location.href = 'checked_in.php';
                });
            }
        });
    }

    // Function to process early check-in
    function earlyCheckIn(bookingId) {
        console.log('Processing early check-in for booking:', bookingId);
        
        // Get the booking row data
        const $row = $(`button[data-id="${bookingId}"]`).closest('tr');
        const guestName = $row.find('td:eq(1)').text().trim();
        const checkInDate = $row.find('td:eq(3)').text().trim();
        const checkOutDate = $row.find('td:eq(4)').text().trim();
        
        // Show the early check-in modal directly
        // This will be populated with data by the early-check-in-btn click handler
        const $button = $(`.early-check-in-btn[data-id="${bookingId}"]`);
        if ($button.length > 0) {
            $button.click();
        } else {
            console.error('Could not find early check-in button for booking ID:', bookingId);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not initialize early check-in. Please try again.'
            });
        }
    }

    $(document).ready(function() {
        console.log('Initializing DataTable...'); // Debug log
        
        var table = $('#bookingsTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            order: [[0, 'asc']], // Sort by Name by default
            language: {
                search: "Search bookings:",
                lengthMenu: "Show _MENU_ bookings per page",
                info: "Showing _START_ to _END_ of _TOTAL_ bookings",
                emptyTable: "No bookings available"
            },
            columnDefs: [
                { width: '20%', targets: 0 }, // Name
                { width: '15%', targets: 1 }, // Room Type
                { width: '12%', targets: 2 }, // Total Amount
                { width: '15%', targets: 3 }, // Payment Option
                { width: '12%', targets: 4 }, // Amount Paid
                { width: '26%', targets: 5, orderable: false } // Actions
            ]
        });

        // Add change handler for rejection reason select
        $('#rejectionReasonSelect').on('change', function() {
            if ($(this).val() === 'other') {
                $('#otherReasonDiv').show();
                $('#otherReason').prop('required', true);
            } else {
                $('#otherReasonDiv').hide();
                $('#otherReason').prop('required', false);
            }
        });

        // Update the reject button click handler
        $('.reject-booking').on('click', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            $('#bookingIdToReject').val(bookingId);
            $('#rejectModal').modal('show');
        });

        // Update the form submission handler
        $('#rejectForm').on('submit', function(e) {
            e.preventDefault();
            
            const bookingId = $('#bookingIdToReject').val();
            if (!bookingId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Booking ID is missing'
                });
                return;
            }

            // Get the rejection reason
            const rejectionReason = $('#rejectionReasonSelect').val() === 'other' 
                ? $('#otherReason').val() 
                : $('#rejectionReasonSelect').val();

            // Show loading message
            Swal.fire({
                title: 'Processing Rejection',
                html: `
                    <div class="text-center">
                        <i class="fa fa-envelope fa-3x mb-3"></i>
                        <p>Sending rejection email to customer...</p>
                        <p class="small text-muted">This may take a few moments</p>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'process_booking_rejection.php',
                type: 'POST',
                data: { 
                    booking_id: bookingId,
                    rejection_reason: rejectionReason
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Rejected Successfully',
                            html: `
                                <div class="text-center">
                                    <i class="fa fa-check-circle fa-3x mb-3 text-success"></i>
                                    <p>The booking has been rejected and the customer has been notified via email.</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Close'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Rejection Failed',
                            html: `
                                <div class="text-center">
                                    <i class="fa fa-times-circle fa-3x mb-3 text-danger"></i>
                                    <p>${response.message || 'An error occurred while processing the rejection.'}</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Try Again'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error details:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'System Error',
                        html: `
                            <div class="text-center">
                                <i class="fa fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                                <p>Sorry, we encountered a technical issue while processing your request.</p>
                                <p class="small text-muted">Please try again or contact support if the problem persists.</p>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Try Again'
                    });
                },
                complete: function() {
                    $('#rejectModal').modal('hide');
                }
            });
        });

        // Function to fetch available room numbers
        function fetchAvailableRooms(roomTypeId) {
            const roomNumberSelect = $('#earlyCheckInRoomNumber');
            roomNumberSelect.html('<option value="">Loading rooms...</option>');
            
            // Fetch available rooms for this room type
            $.ajax({
                url: 'get_available_rooms.php',
                type: 'GET',
                data: { room_type_id: roomTypeId },
                dataType: 'json',
                success: function(response) {
                    console.log('Rooms response:', response);
                    roomNumberSelect.empty();
                    
                    if (response.success && response.rooms && response.rooms.length > 0) {
                        roomNumberSelect.append('<option value="">-- Select Room Number --</option>');
                        
                        // Add each room as an option
                        response.rooms.forEach(function(room) {
                            let roomText = room.room_number;
                            if (room.floor) {
                                roomText += ' (Floor ' + room.floor + ')';
                            }
                            roomNumberSelect.append($('<option>', {
                                value: room.room_number,
                                text: roomText
                            }));
                        });
                    } else {
                        roomNumberSelect.append('<option value="">No rooms available</option>');
                        console.error('No rooms available or error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching rooms:', error);
                    roomNumberSelect.html('<option value="">Error loading rooms</option>');
                }
            });
        }

        // Early Check-in button click handler
        $(document).on('click', '.early-check-in-btn', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            const guestName = $(this).data('guest');
            const checkInDate = $(this).data('checkin');
            const checkOutDate = $(this).data('checkout');
            const roomPrice = parseFloat($(this).data('price'));
            const roomTypeId = $(this).data('room-type-id');
            const nights = parseInt($(this).data('nights'));
            const amountPaid = parseFloat($(this).data('amount-paid') || 0);
            
            // Calculate current date
            const currentDate = new Date();
            const formattedCurrentDate = currentDate.toISOString().split('T')[0];
            
            // Calculate original and new total amounts
            const originalTotal = roomPrice * nights;
            const newNights = Math.ceil((new Date(checkOutDate) - currentDate) / (1000 * 60 * 60 * 24));
            const newTotal = roomPrice * newNights;
            
            // Calculate balance to pay (new total minus amount already paid)
            const balanceToPay = newTotal - amountPaid;

            // Set modal values
            $('#earlyCheckInBookingId').val(bookingId);
            $('#earlyCheckInGuestName').val(guestName);
            $('#originalCheckInDate').val(checkInDate);
            $('#newCheckInDate').val(formattedCurrentDate);
            $('#originalAmount').val('₱' + originalTotal.toFixed(2));
            $('#newAmount').val('₱' + newTotal.toFixed(2));
            $('#amountPaidUponBooking').val('₱' + amountPaid.toFixed(2));
            $('#balanceToPay').val('₱' + balanceToPay.toFixed(2));
            
            // Reset and fetch available rooms
            fetchAvailableRooms(roomTypeId);

            // Show modal
            $('#earlyCheckInModal').modal('show');
        });

        // Confirm Early Check-in button click handler
        $('#confirmEarlyCheckIn').on('click', function() {
            const bookingId = $('#earlyCheckInBookingId').val();
            const guestName = $('#earlyCheckInGuestName').val();
            const checkInDate = $('#originalCheckInDate').val();
            const checkOutDate = $('#newCheckInDate').val();
            const paymentMethod = $('#earlyCheckInPaymentMethod').val();
            const paymentOption = $('#earlyCheckInPaymentOption').val();
            const newTotal = $('#newAmount').val().replace(/[₱,]/g, '').trim();

            if (!paymentMethod) {
                alert('Please select a payment method');
                return;
            }

            if (!paymentOption) {
                alert('Please select a payment option');
                return;
            }

            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            // Get selected room number
            const roomNumber = $('#earlyCheckInRoomNumber').val();
            if (!roomNumber) {
                alert('Please select a room number');
                return;
            }

            // Process the early check-in
            $.ajax({
                url: 'process_early_checkin.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    guest_name: guestName,
                    check_in: checkInDate,
                    check_out: checkOutDate,
                    payment_method: paymentMethod,
                    payment_option: paymentOption,
                    new_total: newTotal,
                    room_number: roomNumber
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Early check-in processed successfully!',
                            showConfirmButton: true
                        }).then(() => {
                            window.location.href = 'checked_in.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to process early check-in'
                        });
                        $('#confirmEarlyCheckIn').prop('disabled', false).text('Confirm Check-in');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to process early check-in. ' + xhr.responseText
                    });
                    $('#confirmEarlyCheckIn').prop('disabled', false).text('Confirm Check-in');
                }
            });
        });

        // Function to handle view button clicks
        function handleViewButtonClick(bookingId) {
            // Show loading state
            $('#bookingDetailsModal').modal('show');
            
            // Fetch booking details
            fetch('fetch_booking_details.php?booking_id=' + bookingId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const bookingData = data.data;
                        
                        // Debug: Log the received booking data
                        console.log("Received bookingData:", bookingData);

                        // Update modal content
                        // Personal Information
                        $('#firstName').text(bookingData.first_name || '');
                        $('#lastName').text(bookingData.last_name || '');
                        $('#email').text(bookingData.email || '');
                        $('#contact').text(bookingData.contact || '');

                        // Booking Details
                        $('#checkIn').text(bookingData.check_in || '');
                        $('#checkOut').text(bookingData.check_out || '');
                        $('#nights').text(bookingData.nights || '');

                        // Room & Pricing Details
                        $('#roomType').text(bookingData.room_type || '');
                        $('#roomPrice').text('₱' + parseFloat(bookingData.room_price || 0).toLocaleString() + ' per night');
                        $('#duration').text(bookingData.nights + ' nights');
                        $('#quantity').text('1 room');
                        $('#roomTotal').text('₱' + parseFloat(bookingData.total_amount || 0).toLocaleString());

                        // Payment Information
                        $('#paymentOption').text(bookingData.payment_option || '');
                        $('#paymentMethod').text(bookingData.payment_method || '');
                        $('#amountDue').text('₱' + parseFloat(bookingData.amount_paid || 0).toLocaleString());
                        $('#totalAmount').text('₱' + parseFloat(bookingData.total_amount || 0).toLocaleString());

                        // Guest Information
                        $('#totalGuests').text(bookingData.number_of_guests || '0');
                        
                        // Store the room type ID for check-in
                        $('#checkInRoomTypeId').val(bookingData.room_type_id || '');
                        
                        // If this is a check-in, fetch available rooms
                        if (bookingData.status === 'Pending') {
                            // Show loading in room number dropdown
                            $('#checkInRoomNumber').html('<option value="">Loading available rooms...</option>');
                            
                            // Fetch available rooms
                            fetch('get_available_rooms.php?' + new URLSearchParams({
                                room_type_id: bookingData.room_type_id,
                                check_in: bookingData.check_in,
                                check_out: bookingData.check_out
                            }))
                            .then(response => response.json())
                            .then(roomData => {
                                let options = '<option value="">-- Select a room --</option>';
                                
                                if (roomData.success && roomData.rooms && roomData.rooms.length > 0) {
                                    // Add room options
                                    roomData.rooms.forEach(room => {
                                        options += `<option value="${room.room_number}">${room.room_number}</option>`;
                                    });
                                } else {
                                    // No rooms available
                                    options = '<option value="">No rooms available</option>';
                                }
                                
                                // Update the dropdown
                                $('#checkInRoomNumber').html(options);
                            })
                            .catch(error => {
                                console.error('Error fetching rooms:', error);
                                $('#checkInRoomNumber').html('<option value="">Error loading rooms</option>');
                            });
                        }
                    } else {
                        console.error('Failed to fetch booking details:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Attach click handler to view details buttons
        $(document).on('click', '.view-details:not(.view-only-payment-proof)', function() {
            const bookingId = $(this).data('id');
            handleViewButtonClick(bookingId);
        });

        // Handle click for the new 'View Payment Proof' button
        $(document).on('click', '.view-only-payment-proof', function() {
            const bookingId = $(this).data('id'); // We still need the booking ID to fetch the proof
            
            // Show loading state in the dedicated modal
            $('#paymentProofOnlyModal').modal('show');
            $('#paymentProofOnlyImage').hide();
            $('#paymentProofOnlyModal .loading-indicator').show();
            $('#paymentProofOnlyModal .error-message').hide();
            
            // Fetch only the payment proof using a new endpoint or reuse existing one
            // For now, let's reuse fetch_booking_details.php and extract just the proof
            $.ajax({
                url: 'fetch_booking_details.php',
                type: 'POST',
                data: { booking_id: bookingId },
                dataType: 'json',
                success: function(response) {
                    $('#paymentProofOnlyModal .loading-indicator').hide();
                    if (response.success && response.data && response.data.payment_proof) {
                        const proofFilename = response.data.payment_proof.split('/').pop();
                        // Add a cache-busting parameter (timestamp)
                        const imagePath = '/Admin/uploads/payment_proofs/' + proofFilename + '?t=' + new Date().getTime();
                        
                        console.log('Attempting to load payment proof in dedicated modal from:', imagePath);
                        
                        $('#paymentProofOnlyImage')
                            .attr('src', imagePath)
                            .on('load', function() {
                                $(this).show();
                            })
                            .on('error', function() {
                                $(this).hide();
                                $('#paymentProofOnlyModal .error-message').show().text('Failed to load payment proof image. Verify file exists at: ' + imagePath);
                            });
                    } else {
                        $('#paymentProofOnlyImage').hide();
                        $('#paymentProofOnlyModal .error-message').show().text(response.message || 'No payment proof found for this booking.');
                    }
                },
                error: function(xhr, status, error) {
                    $('#paymentProofOnlyModal .loading-indicator').hide();
                    $('#paymentProofOnlyImage').hide();
                    $('#paymentProofOnlyModal .error-message').show().text('Error fetching payment proof details.');
                    console.error('AJAX Error:', error);
                }
            });
        });

        // Handle Accept Booking button click
        $(document).on('click', '.accept-booking', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            const button = $(this);
            
            Swal.fire({
                title: 'Accept Booking',
                text: 'Are you sure you want to accept this booking?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, accept it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    const swalInstance = Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we update the booking status',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send AJAX request to update status to 'accepted'
                    $.ajax({
                        url: 'update_booking_status.php',
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            action: 'accept',
                            status: 'accepted'
                        },
                        dataType: 'json',
                        success: function(response) {
                            // Close loading dialog
                            swalInstance.close();
                            
                            if (response && response.success) {
                                // Update the status in the table
                                const $row = button.closest('tr');
                                const statusCell = $row.find('.status-badge');
                                statusCell.html('<span class="badge bg-primary">Accepted</span>');
                                
                                // Update action buttons
                                const actionsHtml = `
                                    <div class="btn-group">
                                        <button class="btn btn-success btn-sm check-in-btn"
                                                data-toggle="tooltip"
                                                title="Check In"
                                                data-id="${bookingId}">
                                            <i class="fa fa-sign-in-alt"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm early-check-in-btn"
                                                data-toggle="tooltip"
                                                title="Early Check-in"
                                                data-id="${bookingId}">
                                            <i class="fa fa-clock"></i>
                                        </button>
                                    </div>
                                `;
                                
                                $row.find('.action-buttons').html(actionsHtml);
                                
                                // Initialize tooltips for the new buttons
                                $('[data-toggle="tooltip"]').tooltip();
                                
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Booking Accepted!',
                                    text: 'The booking has been accepted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                const errorMsg = (response && response.message) || 'Failed to accept booking. Please try again.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            swalInstance.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while processing your request. Please try again.'
                            });
                        }
                    });
                }
            });
        });
        
        // Handle reschedule button click
        $(document).on('click', '.reschedule-booking', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            const guestName = $(this).closest('tr').find('td:first').text().trim().split('\n')[0];
            const checkInDate = $(this).data('checkin');
            const checkOutDate = $(this).data('checkout');
            
            // Set modal values
            $('#rescheduleBookingId').val(bookingId);
            $('#rescheduleGuestName').val(guestName);
            $('#currentCheckInDate').val(checkInDate);
            $('#currentCheckOutDate').val(checkOutDate);
            
            // Set minimum date for the new check-in date (today)
            const today = new Date().toISOString().split('T')[0];
            $('#newRescheduleCheckInDate').attr('min', today);
            
            // Show modal
            $('#rescheduleModal').modal('show');
        });
        
        // Handle check-in button click with event delegation for dynamically loaded content
        $(document).on('click', '.check-in-btn', function() {
            const bookingId = $(this).data('id');
            const guestName = $(this).data('guest') || 'Guest';
            const roomTypeId = $(this).data('room-type-id');
            
            // Show loading in room number dropdown
            $('#checkInRoomNumber').html('<option value="">Loading available rooms...</option>');
            
            // Set values in the modal
            $('#checkInBookingId').val(bookingId);
            $('#checkInGuestName').val(guestName);
            $('#checkInRoomTypeId').val(roomTypeId);
            
            // Show the modal
            $('#checkInModal').modal('show');
            
            // Load available rooms for this room type
            $.ajax({
                url: 'get_available_rooms.php',
                type: 'GET',
                data: {
                    room_type_id: roomTypeId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Available rooms:', response);
                    let options = '<option value="">-- Select a room --</option>';
                    
                    if (response.success && response.rooms && response.rooms.length > 0) {
                        // Add room options
                        response.rooms.forEach(function(room) {
                            options += `<option value="${room.room_number}">${room.room_number}</option>`;
                        });
                    } else {
                        // No rooms available
                        options = '<option value="">No rooms available</option>';
                    }
                    
                    // Update the dropdown
                    $('#checkInRoomNumber').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching rooms:', error);
                    $('#checkInRoomNumber').html('<option value="">Error loading rooms</option>');
                }
            });
        });
        
        // Handle check-in form submission
        $('#checkInForm').on('submit', function(e) {
            e.preventDefault();
            
            const bookingId = $('#checkInBookingId').val();
            const roomNumber = $('#checkInRoomNumber').val();
            
            if (!roomNumber) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a room number'
                });
                return;
            }

            // Disable the button and show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            // Get discount type
            const discountType = $('#discountType').val();
            
            // Send AJAX request
            $.ajax({
                url: 'process_checkin.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    room_number: roomNumber,
                    discount_type: discountType || ''
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response.success) {
                        // Close any existing alerts
                        Swal.close();
                        
                        // Show success message with payment and discount info
                        let successMessage = 'Check-in processed successfully!';
                        if (discountType && response.raw_amounts) {
                            const total = parseFloat(response.raw_amounts.total);
                            const paid = parseFloat(response.raw_amounts.paid);
                            const remaining = total - paid;
                            
                            successMessage = `Check-in processed successfully!`;
                            successMessage += `\n${discountType} discount of 20% has been applied.`;
                            successMessage += `\nTotal Amount: ₱${response.new_total_amount}`;
                            successMessage += `\nAmount Paid: ₱${response.amount_paid}`;
                            successMessage += `\nRemaining Balance: ₱${remaining.toFixed(2)}`;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: successMessage,
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            timer: 3000,
                            timerProgressBar: true,
                            willClose: () => {
                                // Redirect to check_out.php with the booking ID
                                window.location.href = 'check_out.php?booking_id=' + response.booking_id;
                            }
                        }).then(() => {
                            // Fallback in case the timer doesn't trigger
                            window.location.href = 'check_out.php?booking_id=' + response.booking_id;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to process check-in'
                        });
                        // Reset button state
                        submitBtn.prop('disabled', false).html('Confirm Check-in');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    let errorMessage = 'Failed to process check-in. Please try again.';
                    
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                            if (errorResponse.error_details) {
                                console.error('Error details:', errorResponse.error_details);
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage + '<br><small>Check console for more details.</small>',
                        showConfirmButton: true
                    });
                    // Reset button state
                    submitBtn.prop('disabled', false).html('Confirm Check-in');
                }
            });
        });

        // Handle reschedule form submission
        $('#rescheduleForm').on('submit', function(e) {
            e.preventDefault();
            
            const bookingId = $('#rescheduleBookingId').val();
            const currentCheckIn = new Date($('#currentCheckInDate').val());
            const currentCheckOut = new Date($('#currentCheckOutDate').val());
            const newCheckIn = new Date($('#newRescheduleCheckInDate').val());
            
            // Calculate the number of days between check-in and check-out
            const stayDuration = Math.floor((currentCheckOut - currentCheckIn) / (1000 * 60 * 60 * 24));
            
            // Calculate the new check-out date by adding the stay duration to the new check-in date
            const newCheckOut = new Date(newCheckIn);
            newCheckOut.setDate(newCheckOut.getDate() + stayDuration);
            
            // Format dates for submission
            const formattedNewCheckIn = newCheckIn.toISOString().split('T')[0];
            const formattedNewCheckOut = newCheckOut.toISOString().split('T')[0];
            
            // Disable the button and show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
            
            // Send AJAX request to reschedule
            $.ajax({
                url: 'update_booking_dates.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    new_check_in: formattedNewCheckIn,
                    new_check_out: formattedNewCheckOut
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Booking has been rescheduled successfully!',
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to reschedule booking'
                        });
                        submitBtn.prop('disabled', false).html('Confirm Reschedule');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reschedule booking due to a server error.'
                    });
                    console.error('AJAX Error:', xhr.responseText);
                    submitBtn.prop('disabled', false).html('Confirm Reschedule');
                }
            });
        });
    });
</script>