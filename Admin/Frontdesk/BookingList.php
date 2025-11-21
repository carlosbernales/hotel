<?php
require "db.php"; // Include the database connection file

// Handle status updates if submitted
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['new_status'];
    $update_sql = "UPDATE reservation SET status = ? WHERE reservation_id = ?";
    $update_stmt = $connection->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $booking_id);
    $update_stmt->execute();
}

// Handle filters
$where_clause = "1=1";
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $_GET['status'];
    $where_clause .= " AND status = '$status'";
}
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $date_from = $_GET['date_from'];
    $where_clause .= " AND checkin >= '$date_from'";
}
if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $date_to = $_GET['date_to'];
    $where_clause .= " AND checkout <= '$date_to'";
}

// Query to fetch customer information with filters
$sql = "SELECT * FROM reservation WHERE $where_clause ORDER BY reservation_id DESC";
$stmt = $connection->query($sql);
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
            <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Booking List</li>
        </ol>
    </div><!--/.row-->

    <br>

    <div class="row">
        <div class="col-lg-12">
            <div id="success"></div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Filters</h3>
        </div>
        <div class="panel-body">
            <form method="GET" class="form-inline">
                <div class="form-group mx-2">
                    <label>Status:</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo isset($_GET['status']) && $_GET['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?php echo isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="form-group mx-2">
                    <label>Check-in From:</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>">
                </div>
                <div class="form-group mx-2">
                    <label>Check-in To:</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="BookingList.php" class="btn btn-default">Reset</a>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Booking List
                    <span class="pull-right">
                        <button class="btn btn-success btn-sm" onclick="window.location.href='reservation.php'">
                            <i class="fa fa-plus"></i> New Booking
                        </button>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="bookings">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Room</th>
                                <th>Guest Name</th>
                                <th>Price</th>
                                <th>Payment Details</th>
                                <th>Check In/Out</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($stmt->num_rows > 0) {
                                foreach ($stmt as $data) {
                                    $status_class = '';
                                    switch($data['status']) {
                                        case 'confirmed': $status_class = 'success'; break;
                                        case 'pending': $status_class = 'warning'; break;
                                        case 'cancelled': $status_class = 'danger'; break;
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data["reservation_id"]); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($data["room_name"]); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($data["firstname"] . " " . $data["lastname"]); ?><br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($data["email"]); ?>
                                            </small>
                                        </td>
                                        <td>₱<?php echo number_format($data["price"], 2); ?></td>
                                        <td>
                                            Method: <?php echo htmlspecialchars($data["payment_method"]); ?><br>
                                            Option: <?php echo htmlspecialchars($data["payment_option"]); ?><br>
                                            Balance: ₱<?php echo number_format($data["remaining_balance"], 2); ?>
                                        </td>
                                        <td>
                                            Check-in: <?php echo date('M j, Y', strtotime($data["checkin"])); ?><br>
                                            Check-out: <?php echo date('M j, Y', strtotime($data["checkout"])); ?><br>
                                            Arrival: <?php echo date('g:i A', strtotime($data["time_arrival"])); ?>
                                        </td>
                                        <td>
                                            <span class="label label-<?php echo $status_class; ?>">
                                                <?php echo ucfirst($data["status"]); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <a href="#" onclick="updateStatus(<?php echo $data['reservation_id']; ?>, 'confirmed')">
                                                            <i class="fa fa-check"></i> Confirm
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" onclick="updateStatus(<?php echo $data['reservation_id']; ?>, 'cancelled')">
                                                            <i class="fa fa-times"></i> Cancel
                                                        </a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <a href="#" onclick="viewDetails(<?php echo $data['reservation_id']; ?>)">
                                                            <i class="fa fa-eye"></i> View Details
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No bookings found</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form (Hidden) -->
<form id="statusForm" method="POST" style="display: none;">
    <input type="hidden" name="booking_id" id="status_booking_id">
    <input type="hidden" name="new_status" id="new_status">
    <input type="hidden" name="update_status" value="1">
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('#bookings').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 25
    });
});

function updateStatus(bookingId, newStatus) {
    if (confirm('Are you sure you want to ' + newStatus + ' this booking?')) {
        document.getElementById('status_booking_id').value = bookingId;
        document.getElementById('new_status').value = newStatus;
        document.getElementById('statusForm').submit();
    }
}

function viewDetails(bookingId) {
    // Implement view details functionality
    window.location.href = 'view_booking.php?id=' + bookingId;
}
</script>

<style>
.form-inline .form-group {
    margin-right: 15px;
}
.table > tbody > tr > td {
    vertical-align: middle;
}
.label {
    display: inline-block;
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 3px;
    text-transform: uppercase;
}
.dropdown-menu {
    min-width: 120px;
}
.dropdown-menu > li > a {
    padding: 6px 12px;
}
</style>
