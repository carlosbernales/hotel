<?php
require_once 'db.php';
require_once 'event_packages_data.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

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

// Debug query to check table structure
$structure_sql = "DESCRIBE event_bookings";
$structure_result = mysqli_query($con, $structure_sql);
if ($structure_result) {
    error_log("Event bookings table structure:");
    while ($field = mysqli_fetch_assoc($structure_result)) {
        error_log($field['Field'] . " - " . $field['Type']);
    }
}

// Debug: Check users table structure
$users_structure_sql = "DESCRIBE users";
$users_structure_result = mysqli_query($con, $users_structure_sql);
if ($users_structure_result) {
    error_log("Users table structure:");
    while ($field = mysqli_fetch_assoc($users_structure_result)) {
        error_log($field['Field'] . " - " . $field['Type']);
    }
}

// Handle confirm and archive actions
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $booking_id = mysqli_real_escape_string($con, $_POST['booking_id']);
    
    if ($_POST['action'] == 'confirm') {
        $update_sql = "UPDATE event_bookings SET status = 'Confirmed' WHERE id = '$booking_id'";
        if (mysqli_query($con, $update_sql)) {
            echo "<script>alert('Event booking confirmed successfully!');</script>";
        } else {
            echo "<script>alert('Error confirming event booking: " . mysqli_error($con) . "');</script>";
        }
    } elseif ($_POST['action'] == 'archive') {
        $update_sql = "UPDATE event_bookings SET status = 'Archived' WHERE id = '$booking_id'";
        if (mysqli_query($con, $update_sql)) {
            echo "<script>
                alert('Event booking archived successfully!');
                window.location.href = 'archived.php';
            </script>";
            exit();
        } else {
            echo "<script>alert('Error archiving event booking: " . mysqli_error($con) . "');</script>";
        }
    }
}

// Query to get event bookings
$sql = "SELECT eb.*, 
               COALESCE(ep.name, eb.package_name) as display_package_name,
               ep.menu_items as package_menu,
               CASE 
                   WHEN eb.booking_source = 'walk_in' THEN eb.customer_name
                   ELSE CONCAT(u.firstname, ' ', u.lastname)
               END as customer_name
        FROM event_bookings eb 
        LEFT JOIN event_packages ep ON eb.package_name = ep.name 
        LEFT JOIN users u ON eb.user_id = u.id AND eb.booking_source = 'Regular Booking'
        ORDER BY eb.created_at DESC";

// Debug: Print the SQL query
error_log("Event bookings query: " . $sql);

$result = mysqli_query($con, $sql);

if (!$result) {
    error_log("Error in event query: " . mysqli_error($con));
    die("Error in event query: " . mysqli_error($con));
}

// Debug: Print all rows returned
if ($result) {
    error_log("Rows returned by main query:");
    while ($row = mysqli_fetch_assoc($result)) {
        error_log("Booking ID: " . (isset($row['id']) ? $row['id'] : 'N/A'));
        error_log("Package Name: " . (isset($row['display_package_name']) ? $row['display_package_name'] : 'N/A'));
        error_log("Event Type: " . (isset($row['event_type']) ? $row['event_type'] : 'N/A'));
        error_log("Customer Name: " . (isset($row['customer_name']) ? $row['customer_name'] : 'N/A'));
        error_log("-------------------");
    }
    mysqli_data_seek($result, 0);
}

include('header.php');
include('sidebar.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i></a></li>
            <li class="active">Event Bookings</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Event Bookings</h3>
                    <div class="booking-dropdown">
                        <button class="booking-dropbtn">
                            <span>Bookings</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="booking-dropdown-content">
                            <a href="index.php?booking_status"><i class="fa fa-bed"></i> Room Booking</a>
                            <a href="tableData.php"><i class="fa fa-cutlery"></i> Table Booking</a>
                            <a href="eventData.php"><i class="fa fa-calendar"></i> Event Booking</a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="eventBookingsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Package Name</th>
                                    <th>Event Type</th>
                                    <th>Customer Name</th>
                                    <th>Event Date</th>
                                    <th>Time</th>
                                    <th>Number of Guests</th>
                                    <th>Total Amount</th>
                                    <th>Reference Number</th>
                                    <th>Payment Proof</th>
                                    <th>Payment Status</th>
                                    <th>Booking Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['display_package_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($row['event_date'])); ?></td>
                                            <td><?php echo $row['start_time'] . ' - ' . $row['end_time']; ?></td>
                                            <td><?php echo htmlspecialchars($row['number_of_guests']); ?></td>
                                            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($row['reference_number'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php if (!empty($row['payment_proof'])): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info view-payment-proof" 
                                                            data-toggle="modal" 
                                                            data-target="#paymentProofModal"
                                                            data-proof="<?php echo htmlspecialchars($row['payment_proof']); ?>"
                                                            data-reference="<?php echo htmlspecialchars($row['reference_number'] ?? 'N/A'); ?>">
                                                        <i class="fa fa-image"></i> View
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">No proof uploaded</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['payment_status'] ?? 'Pending'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $row['booking_status'] == 'pending' ? 'warning' : ($row['booking_status'] == 'confirmed' ? 'success' : 'danger'); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($row['booking_status'])); ?>
                                                </span>
                                            </td>
                                            <td class="action-buttons">
                                                <button type="button" class="btn btn-info btn-sm view-event-details" 
                                                        data-id="<?php echo $row['id']; ?>"
                                                        title="View event details">
                                                    <i class="fa fa-eye"></i>
                                                </button>

                                                <?php if ($row['booking_status'] == 'pending'): ?>
                                                    <button class="btn btn-success btn-sm"
                                                            data-toggle="tooltip"
                                                            title="Confirm event"
                                                            onclick="confirmEvent('<?php echo $row['id']; ?>')">
                                                        <i class="fa fa-check"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger btn-sm reject-event" 
                                                            data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                                            title="Reject Event">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="12" class="text-center">No event bookings found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="eventDetailsModalLabel">Event Booking Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modalLoadingState" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading event details...</p>
                    </div>
                    <div id="modalErrorState" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="errorMessage">Error loading event details</span>
                    </div>
                    <div id="modalContent" class="row">
                        <div class="col-md-6">
                            <h5>Event Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Package Name:</th>
                                    <td id="package_name"></td>
                                </tr>
                                <tr>
                                    <th>Package Menu:</th>
                                    <td id="package_menu"></td>
                                </tr>
                                <tr>
                                    <th>Event Type:</th>
                                    <td id="event_type"></td>
                                </tr>
                                <tr>
                                    <th>Customer Name:</th>
                                    <td id="customer_name"></td>
                                </tr>
                                <tr>
                                    <th>Event Date:</th>
                                    <td id="event_date"></td>
                                </tr>
                                <tr>
                                    <th>Time:</th>
                                    <td id="event_time"></td>
                                </tr>
                                <tr>
                                    <th>Number of Guests:</th>
                                    <td id="number_of_guests"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Payment Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Package Price:</th>
                                    <td>₱<span id="package_price"></span></td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>₱<span id="total_amount"></span></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td id="payment_method"></td>
                                </tr>
                                <tr>
                                    <th>Payment Type:</th>
                                    <td id="payment_type"></td>
                                </tr>
                                <tr>
                                    <th>Reference Number:</th>
                                    <td id="reference_number"></td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td id="payment_status"></td>
                                </tr>
                                <tr>
                                    <th>Booking Status:</th>
                                    <td id="booking_status"></td>
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

    <!-- Payment Proof Modal -->
    <div class="modal fade" id="paymentProofModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Proof</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <p><strong>Reference Number: </strong><span id="proofReference"></span></p>
                    <img id="paymentProofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#eventBookingsTable').DataTable({
        responsive: true,
        scrollX: true,
        autoWidth: false,
        order: [[3, 'desc']], // Sort by Event Date by default
        language: {
            search: "Search event bookings:",
            lengthMenu: "Show _MENU_ events per page",
            info: "Showing _START_ to _END_ of _TOTAL_ events",
            emptyTable: "No event bookings available"
        }
    });

    // Handle payment proof view
    $('.view-payment-proof').on('click', function() {
        var proofUrl = $(this).data('proof');
        var reference = $(this).data('reference');
        
        // Update modal content
        $('#paymentProofImage').attr('src', 'uploads/payment_proofs/' + proofUrl);
        $('#proofReference').text(reference);
    });

    // Handle event details view
    $(document).on('click', '.view-event-details', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var eventId = $(this).data('id');
        
        // Show loading state
        $('#modalContent').addClass('d-none');
        $('#modalErrorState').addClass('d-none');
        $('#modalLoadingState').removeClass('d-none');
        $('#eventDetailsModal').modal('show');
        
        // Fetch event details
        $.ajax({
            url: 'get_event_details.php',
            type: 'POST',
            data: { event_id: eventId },
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response); // Debug log
                $('#modalLoadingState').addClass('d-none');
                
                if (response.success && response.details) {
                    $('#modalContent').removeClass('d-none');
                    var data = response.details;
                    
                    // Function to safely display text
                    function displaySafe(value, defaultValue = 'N/A') {
                        return value || defaultValue;
                    }
                    
                    // Function to format currency
                    function formatCurrency(amount) {
                        return parseFloat(amount || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                    
                    // Update modal fields with safe display
                    $('#package_name').text(displaySafe(data.package_name));
                    $('#package_menu').text(displaySafe(data.package_menu));
                    $('#event_type').text(displaySafe(data.event_type));
                    $('#customer_name').text(displaySafe(data.customer_name));
                    $('#event_date').text(displaySafe(data.event_date));
                    $('#event_time').text(displaySafe(data.time));
                    $('#number_of_guests').text(displaySafe(data.number_of_guests, '0'));
                    
                    // Format currency fields
                    $('#package_price').text(formatCurrency(data.package_price));
                    $('#total_amount').text(formatCurrency(data.total_amount));
                    
                    // Update other fields
                    $('#payment_method').text(displaySafe(data.payment_method));
                    $('#payment_type').text(displaySafe(data.payment_type));
                    $('#reference_number').text(displaySafe(data.reference_number));
                    $('#payment_status').text(displaySafe(data.payment_status, 'Pending'));
                    $('#booking_status').text(displaySafe(data.booking_status));
                } else {
                    // Show error state
                    $('#modalErrorState').removeClass('d-none')
                        .find('#errorMessage')
                        .text(response.message || 'Failed to load event details');
                }
            },
            error: function(xhr, status, error) {
                $('#modalLoadingState').addClass('d-none');
                $('#modalErrorState').removeClass('d-none')
                    .find('#errorMessage')
                    .text('Network error occurred while loading event details');
                console.error('Ajax Error:', error);
            }
        });
    });

    // Handle modal close
    $('#eventDetailsModal').on('hidden.bs.modal', function () {
        // Clear modal content when closed
        $('#modalContent').empty();
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Function to confirm event
function confirmEvent(eventId) {
    Swal.fire({
        title: 'Confirm Event Booking',
        text: 'Are you sure you want to confirm this event booking?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, confirm it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'update_event_status.php',
                type: 'POST',
                data: {
                    event_id: eventId,
                    action: 'confirm'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', 'Event booking confirmed successfully', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message || 'Failed to confirm event booking', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to process request', 'error');
                }
            });
        }
    });
}

// Function to handle event rejection
$(document).on('click', '.reject-event', function() {
    const eventId = $(this).data('id');
    Swal.fire({
        title: 'Reject Event Booking',
        text: 'Are you sure you want to reject this event booking?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reject it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'update_event_status.php',
                type: 'POST',
                data: {
                    event_id: eventId,
                    action: 'reject'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', 'Event booking rejected successfully', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message || 'Failed to reject event booking', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to process request', 'error');
                }
            });
        }
    });
});
</script>

<style>
.booking-dropdown {
    position: relative;
    display: inline-block;
    float: right;
}

.booking-dropbtn {
    background-color: #337ab7;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.booking-dropbtn:hover {
    background-color: #286090;
}

.booking-dropbtn i {
    transition: transform 0.3s ease;
}

.booking-dropdown:hover .booking-dropbtn i {
    transform: rotate(180deg);
}

.booking-dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: #f9f9f9;
    min-width: 200px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    border-radius: 4px;
    z-index: 1000;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.booking-dropdown:hover .booking-dropdown-content {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

.booking-dropdown-content a {
    color: #333;
    padding: 12px 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s ease;
}

.booking-dropdown-content a:hover {
    background-color: #f1f1f1;
    color: #337ab7;
    padding-left: 20px;
}

.booking-dropdown-content a i {
    width: 20px;
    text-align: center;
}

.table-responsive {
    width: 100%;
    margin: 0;
    overflow-x: scroll;
    border: none;
}

#eventBookingsTable {
    width: 100%;
    min-width: 1500px;
    margin-bottom: 0;
    background: white;
}

#eventBookingsTable th, 
#eventBookingsTable td {
    white-space: nowrap;
    padding: 12px 15px;
}

.panel-body {
    padding: 0;
}

.action-buttons {
    min-width: 120px;
}
</style>
