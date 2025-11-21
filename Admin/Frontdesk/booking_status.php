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
    b.number_of_guests,
    rt.room_type,
    rt.price as room_price,
    b.total_amount,
    b.payment_option,
    b.downpayment_amount,
    b.payment_method,
    b.discount_type,
    b.status,
    DATEDIFF(b.check_out, b.check_in) as nights
FROM bookings b
LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
WHERE b.status IN ('pending', 'Rejected')
ORDER BY b.created_at DESC";

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
    #bookingsTable th:nth-child(16), #bookingsTable td:nth-child(16) { min-width: 100px; width: 100px; } /* Actions */

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
    #bookingsTable th:nth-child(16), #bookingsTable td:nth-child(16) { min-width: 100px; width: 100px; } /* Status */
    #bookingsTable th:nth-child(17), #bookingsTable td:nth-child(17) { min-width: 100px; width: 100px; padding-right: 40px; } /* Actions */

    /* Ensure the table scrolls horizontally */
    .panel-body {
        overflow-x: auto !important;
        padding: 15px;
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
                                <th>Booking ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Nights</th>
                                <th>Room Type</th>
                                <th>Room Price</th>
                                <th>Total Amount</th>
                                <th>Payment Option</th>
                                <th>Amount Paid</th>
                                <th>Payment Method</th>
                                <th>Discount Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($room_result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($room_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($row['check_in'])); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($row['check_out'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['nights']); ?></td>
                                        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                        <td>₱<?php echo number_format($row['room_price'], 2); ?></td>
                                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td><?php echo ucfirst($row['payment_option']); ?></td>
                                        <td>₱<?php echo number_format($row['downpayment_amount'], 2); ?></td>
                                        <td><?php echo ucfirst($row['payment_method']); ?></td>
                                        <td><?php echo !empty($row['discount_type']) ? ucfirst($row['discount_type']) : 'Regular'; ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($row['status'] == 'Rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-buttons">
                                            <?php if ($row['status'] == 'pending'): ?>
                                                <button class="btn btn-success btn-sm"
                                                        data-toggle="tooltip"
                                                        title="Check in"
                                                        onclick="confirmBooking(<?php echo (int)$row['booking_id']; ?>)">
                                                    <i class="fa fa-check"></i>
                                                </button>

                                                <button type="button" class="btn btn-danger btn-sm reject-booking" 
                                                        data-id="<?php echo htmlspecialchars($row['booking_id']); ?>"
                                                        title="Reject Booking">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                
                                                <button class="btn btn-warning btn-sm early-check-in-btn"
                                                        data-toggle="tooltip"
                                                        title="Early check in"
                                                        data-id="<?php echo htmlspecialchars($row['booking_id']); ?>"
                                                        data-guest="<?php echo htmlspecialchars($row['name']); ?>"
                                                        data-checkin="<?php echo $row['check_in']; ?>"
                                                        data-checkout="<?php echo $row['check_out']; ?>"
                                                        data-price="<?php echo $row['downpayment_amount']; ?>"
                                                        data-nights="<?php echo floor((strtotime($row['check_out']) - strtotime($row['check_in'])) / (60 * 60 * 24)); ?>"
                                                        data-amount-paid="<?php echo $row['downpayment_amount']; ?>">
                                                    <i class="fa fa-clock-o"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-info btn-sm view-details"
                                                    data-toggle="tooltip"
                                                    title="View Details"
                                                    data-id="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
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
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div style="margin-bottom: 20px;">
                            <div style="margin-bottom: 10px;">
                                <strong>Reference Number:</strong> <span id="paymentReference"></span>
                            </div>
                            <div style="border: 1px solid #ddd; padding: 10px; text-align: center;">
                                <img id="paymentProofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto;">
                                <div id="noProofMessage" style="padding: 20px; color: #666; display: none;">
                                    No payment proof available
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Booking Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td>Booking ID:</td>
                                <td id="bookingId"></td>
                            </tr>
                            <tr>
                                <td>Check In:</td>
                                <td id="checkIn"></td>
                            </tr>
                            <tr>
                                <td>Check Out:</td>
                                <td id="checkOut"></td>
                            </tr>
                            <tr>
                                <td>Nights:</td>
                                <td id="nights"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Room Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td>Room Type:</td>
                                <td id="roomType"></td>
                            </tr>
                            <tr>
                                <td>Room Price:</td>
                                <td id="roomPrice"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Payment Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td>Total Amount:</td>
                                <td id="totalAmount"></td>
                            </tr>
                            <tr>
                                <td>Payment Option:</td>
                                <td id="paymentOption"></td>
                            </tr>
                            <tr>
                                <td>Amount Paid:</td>
                                <td id="amountPaid"></td>
                            </tr>
                            <tr>
                                <td>Payment Method:</td>
                                <td id="paymentMethod"></td>
                            </tr>
                            <tr>
                                <td>Discount Type:</td>
                                <td id="discountType"></td>
                            </tr>
                            <tr>
                                <td>Status:</td>
                                <td id="bookingStatus"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
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
        console.log('Processing regular check-in for booking:', bookingId);
        
        Swal.fire({
            title: 'Confirm Check-in',
            text: 'Are you sure you want to check in this booking?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, check in',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process the check-in.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send AJAX request
                $.ajax({
                    url: 'update_booking_status.php',
                    type: 'POST',
                    data: {
                        booking_id: bookingId,
                        action: 'confirm'
                    },
                    success: function(response) {
                        console.log('Server response:', response);
                        
                        try {
                            // Parse the response if it's a string
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message || 'Check-in processed successfully.',
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.href = 'checked_in.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Failed to process check-in'
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to process check-in'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process check-in. Please try again.'
                        });
                    }
                });
            }
        });
    }

    // Function to process early check-in
    function earlyCheckIn(bookingId) {
        console.log('Processing early check-in for booking:', bookingId);
        
        Swal.fire({
            title: 'Early Check-in',
            text: 'Are you sure you want to process early check-in?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, check in',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process the early check-in.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: 'update_booking_status.php',
                    type: 'POST',
                    data: {
                        booking_id: bookingId,
                        action: 'early_checkin'
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Early check-in response:', response);
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Early check-in processed successfully.',
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href = response.redirect || 'checked_in.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to process early check-in'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });
                        
                        let errorMessage = 'Failed to process early check-in.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage + ' Please try again.'
                        });
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        console.log('Initializing DataTable...'); // Debug log
        
        var table = $('#bookingsTable').DataTable({
            responsive: true,
            scrollX: true,
            autoWidth: false,
            order: [[4, 'desc']], // Sort by Check In date by default
            language: {
                search: "Search bookings:",
                lengthMenu: "Show _MENU_ bookings per page",
                info: "Showing _START_ to _END_ of _TOTAL_ bookings",
                emptyTable: "No bookings available"
            }
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

        // Early Check-in button click handler
        $(document).on('click', '.early-check-in-btn', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            const guestName = $(this).data('guest');
            const checkInDate = $(this).data('checkin');
            const checkOutDate = $(this).data('checkout');
            const roomPrice = parseFloat($(this).data('price'));
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

            // Show modal
            $('#earlyCheckInModal').modal('show');
        });

        // Confirm Early Check-in button click handler
        $('#confirmEarlyCheckIn').on('click', function() {
            const bookingId = $('#earlyCheckInBookingId').val();
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

            // Process the early check-in
            $.ajax({
                url: 'process_early_checkin.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    payment_method: paymentMethod,
                    payment_option: paymentOption,
                    new_total: newTotal
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to process early check-in'
                    });
                    $('#confirmEarlyCheckIn').prop('disabled', false).text('Confirm Check-in');
                }
            });
        });

        // View Details button click handler
        $(document).on('click', '.view-details', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('id');
            
            // Show loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching booking details',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch booking details
            $.ajax({
                url: 'fetch_booking_details.php',
                type: 'POST',
                data: { booking_id: bookingId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Update modal with booking details
                        $('#guestName').text(data.name);
                        $('#guestEmail').text(data.email);
                        $('#guestContact').text(data.contact);
                        $('#bookingId').text(data.booking_id);
                        $('#checkIn').text(data.check_in);
                        $('#checkOut').text(data.check_out);
                        $('#nights').text(data.nights);
                        $('#roomType').text(data.room_type);
                        $('#roomPrice').text('₱' + parseFloat(data.room_price).toFixed(2));
                        $('#totalAmount').text('₱' + parseFloat(data.total_amount).toFixed(2));
                        $('#paymentOption').text(data.payment_option);
                        $('#amountPaid').text('₱' + parseFloat(data.amount_paid).toFixed(2));
                        $('#paymentMethod').text(data.payment_method);
                        $('#paymentReference').text(data.payment_reference || 'N/A');
                        // Handle payment proof image
                        if (data.payment_proof) {
                            // Ensure the path includes payment_proofs directory
                            const imagePath = data.payment_proof.includes('payment_proofs/') 
                                ? data.payment_proof 
                                : 'uploads/payment_proofs/' + data.payment_proof.replace('uploads/', '');
                            
                            console.log('Loading image from:', imagePath);
                            
                            $('#paymentProofImage')
                                .attr('src', imagePath)
                                .css({
                                    'max-width': '300px',
                                    'height': 'auto',
                                    'margin': '10px 0',
                                    'border': '1px solid #ddd',
                                    'border-radius': '4px',
                                    'padding': '5px'
                                })
                                .on('load', function() {
                                    console.log('Image loaded successfully');
                                    $(this).show();
                                    $('#noProofMessage').hide();
                                })
                                .on('error', function() {
                                    console.error('Failed to load image:', imagePath);
                                    $(this).hide();
                                    $('#noProofMessage')
                                        .html(`Failed to load payment proof image<br>
                                              <small class="text-muted">Path: ${imagePath}</small><br>
                                              <small class="text-muted">Original path: ${data.payment_proof}</small>`)
                                        .show();
                                });
                        } else {
                            $('#paymentProofImage').hide();
                            $('#noProofMessage')
                                .text('No payment proof available')
                                .show();
                        }
                        $('#discountType').text(data.discount_type || 'Regular');
                        $('#bookingStatus').text(data.status);
                        
                        // Close loading and show modal
                        Swal.close();
                        $('#bookingDetailsModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fetch booking details'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch booking details'
                    });
                }
            });
        });

        // Handle check-in button click
        $('.check-in-btn').on('click', function() {
            if (!confirm('Are you sure you want to check in this booking?')) {
                return;
            }

            const bookingId = $(this).data('id');
            const btn = $(this);

            // Disable the button and show loading state
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            // Send AJAX request
            $.ajax({
                url: 'update_booking_status.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    action: 'confirm'
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response.success) {
                        alert('Check-in processed successfully!');
                        // Redirect to checked_in.php
                        window.location.href = 'checked_in.php';
                    } else {
                        alert('Error: ' + (response.message || 'Failed to process check-in'));
                        // Reset button state
                        btn.prop('disabled', false).html('<i class="fa fa-sign-in"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMessage = 'Failed to process check-in';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    
                    alert('Error: ' + errorMessage);
                    // Reset button state
                    btn.prop('disabled', false).html('<i class="fa fa-sign-in"></i>');
                }
            });
        });
    });
</script>