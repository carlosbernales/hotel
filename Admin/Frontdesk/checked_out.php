<?php
require_once 'includes/init.php';
require_once 'db.php';
include 'header.php';
include 'sidebar.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// First, let's check if all required tables exist
$tables_check = "
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME IN ('booking', 'rooms', 'room_types', 'customer')
";

$tables_result = mysqli_query($con, $tables_check);
$existing_tables = [];
while ($table = mysqli_fetch_assoc($tables_result)) {
    $existing_tables[] = $table['TABLE_NAME'];
}

// Debug output
echo "<!-- Existing tables: " . implode(", ", $existing_tables) . " -->";

// Query to get checked-out bookings
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
        ELSE 0
    END as amount_paid,
    b.status,
    rt.room_type,
    rt.price as room_price,
    DATEDIFF(b.check_out, b.check_in) as nights_stayed
FROM bookings b
LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
WHERE b.status = 'Checked Out'
ORDER BY b.check_out DESC";

$result = mysqli_query($con, $sql);
if (!$result) {
    die("Error in query: " . mysqli_error($con));
}

// First check if table exists and has the right columns
$check_columns = mysqli_query($con, "SHOW COLUMNS FROM event_bookings");
if (!$check_columns) {
    die("Error: Table event_bookings does not exist. " . mysqli_error($con));
}

// Debug output
echo "<!-- Debug: Table columns: -->\n";
$columns = [];
while ($row = mysqli_fetch_assoc($check_columns)) {
    $columns[] = $row['Field'];
    echo "<!-- Column: " . $row['Field'] . " Type: " . $row['Type'] . " -->\n";
}
echo "<!-- All columns: " . implode(", ", $columns) . " -->\n";

// Use created_at for now since we know it exists
$event_sql = "SELECT * FROM event_bookings ORDER BY created_at DESC";
$event_result = mysqli_query($con, $event_sql);
if (!$event_result) {
    die("Error in event query: " . mysqli_error($con));
}

// Helper function to safely display values
function safe_display($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper function to safely display dates
function safe_date($value) {
    return $value ? date('M d, Y', strtotime($value)) : '';
}

// Debug: Show first record's data
if ($event_result->num_rows > 0) {
    $first_row = mysqli_fetch_assoc($event_result);
    echo "<!-- First row data: -->\n";
    foreach ($first_row as $key => $value) {
        echo "<!-- $key: $value -->\n";
    }
    mysqli_data_seek($event_result, 0);
}

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active">Checked Out</li>
        </ol>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Checked Out Guests
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="checkedOutTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Guest Name</th>
                            <th>Room Type</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Nights</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['check_in'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['check_out'])); ?></td>
                                <td><?php echo $row['nights_stayed']; ?></td>
                                <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                <td>
                                    <span class="badge bg-success">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-details" 
                                            data-id="<?php echo $row['booking_id']; ?>"
                                            data-bs-toggle="tooltip" 
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-sm archive-booking" 
                                            data-id="<?php echo $row['booking_id']; ?>"
                                            data-bs-toggle="tooltip" 
                                            title="Archive">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Guest Information</h6>
                        <p><strong>Name:</strong> <span id="guestName"></span></p>
                        <p><strong>Email:</strong> <span id="guestEmail"></span></p>
                        <p><strong>Contact:</strong> <span id="guestContact"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Booking Information</h6>
                        <p><strong>Room Type:</strong> <span id="roomType"></span></p>
                        <p><strong>Check In:</strong> <span id="checkInDate"></span></p>
                        <p><strong>Check Out:</strong> <span id="checkOutDate"></span></p>
                        <p><strong>Nights Stayed:</strong> <span id="nightsStayed"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Payment Information</h6>
                        <p><strong>Total Amount:</strong> <span id="totalAmount"></span></p>
                        <p><strong>Payment Method:</strong> <span id="paymentMethod"></span></p>
                        <p><strong>Payment Option:</strong> <span id="paymentOption"></span></p>
                        <p><strong>Amount Paid:</strong> <span id="amountPaid"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#checkedOutTable').DataTable({
        order: [[2, 'desc']] // Sort by check-in date by default
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // View Details Button Click Handler
    $('.view-details').click(function() {
        const bookingId = $(this).data('id');
        const row = $(this).closest('tr');
        const cells = row.find('td');

        // Populate modal with data from the row
        $('#guestName').text(cells.eq(0).text());
        $('#roomType').text(cells.eq(1).text());
        $('#checkInDate').text(cells.eq(2).text());
        $('#checkOutDate').text(cells.eq(3).text());
        $('#nightsStayed').text(cells.eq(4).text());
        $('#totalAmount').text(cells.eq(5).text());
        $('#paymentMethod').text(cells.eq(6).text());

        // Fetch additional details via AJAX
        $.ajax({
            url: 'get_booking_details.php',
            type: 'POST',
            data: { booking_id: bookingId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#guestEmail').text(response.data.email);
                    $('#guestContact').text(response.data.contact);
                    $('#paymentOption').text(response.data.payment_option);
                    $('#amountPaid').text('₱' + parseFloat(response.data.amount_paid).toFixed(2));
                }
            }
        });

        $('#viewDetailsModal').modal('show');
    });

    // Archive Button Click Handler
    $('.archive-booking').click(function() {
        const bookingId = $(this).data('id');
        if (confirm('Are you sure you want to archive this booking?')) {
            $.ajax({
                url: 'update_booking_status.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    action: 'archive'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Booking archived successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error processing request');
                }
            });
        }
    });
});
</script>

<style>
.badge {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
}

.bg-success {
    background-color: #28a745;
    color: white;
}

.bg-warning {
    background-color: #ffc107;
    color: black;
}

.dataTables_wrapper .dt-buttons {
    margin-bottom: 15px;
}

.dt-buttons .btn {
    margin-right: 5px;
}

.table th {
    background-color: #333;
    color: white;
}

.dataTables_filter {
    margin-bottom: 15px;
}

.dataTables_length select {
    margin: 0 5px;
}

.table td {
    vertical-align: middle;
}
</style>
