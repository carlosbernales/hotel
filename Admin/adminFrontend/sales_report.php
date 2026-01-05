<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

// Get date range from GET
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Prepare full-day datetime strings
if ($fromDate && $toDate) {
    $fromDateTime = $fromDate . " 00:00:00";
    $toDateTime = $toDate . " 23:59:59";
}

// ROOM BOOKINGS QUERY
$sql = "
    SELECT 
        b.booking_id,
        b.booking_reference,
        b.check_in,
        b.check_out,
        b.total_amount,
        GROUP_CONCAT(DISTINCT br.room_type_name SEPARATOR ', ') AS room_types,
        COUNT(br.id) AS booked_rooms
    FROM bookings b
    LEFT JOIN booked_rooms br 
        ON br.booking_id = b.booking_id
    WHERE b.status = 'finished'
";

if ($fromDate && $toDate) {
    $sql .= " AND (b.check_in BETWEEN '$fromDateTime' AND '$toDateTime' 
                  OR b.check_out BETWEEN '$fromDateTime' AND '$toDateTime')";
}

$sql .= " GROUP BY b.booking_id ORDER BY b.check_in DESC";
$result = mysqli_query($conn, $sql);

// Total for Room Bookings
$roomTotal = 0;
if (mysqli_num_rows($result) > 0) {
    mysqli_data_seek($result, 0); // Reset pointer
    while ($row = mysqli_fetch_assoc($result)) {
        $roomTotal += $row['total_amount'];
    }
    mysqli_data_seek($result, 0); // Reset again for table display
}

// TABLE BOOKINGS QUERY
$tableSql = "
    SELECT
        ot.id AS order_id,
        ot.date_time,
        ot.total,
        GROUP_CONCAT(ott.table_name SEPARATOR ', ') AS table_names
    FROM orders_table ot
    LEFT JOIN orders_table_type ott
        ON ott.table_booking_fk_id = ot.id
    WHERE ot.status = 'Finished'
";

if ($fromDate && $toDate) {
    $tableSql .= " AND ot.date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
}

$tableSql .= " GROUP BY ot.id, ot.date_time, ot.total ORDER BY ot.date_time DESC";
$tableResult = mysqli_query($conn, $tableSql);

// Total for Table Bookings
$tableTotal = 0;
if (mysqli_num_rows($tableResult) > 0) {
    mysqli_data_seek($tableResult, 0);
    while ($row = mysqli_fetch_assoc($tableResult)) {
        $tableTotal += $row['total'];
    }
    mysqli_data_seek($tableResult, 0);
}

// EVENT BOOKINGS QUERY
$eventSql = "
    SELECT *
    FROM event_bookings eb
    WHERE eb.booking_status = 'Finished'
";

if ($fromDate && $toDate) {
    $eventSql .= " AND (eb.date_time_start BETWEEN '$fromDateTime' AND '$toDateTime' 
                      OR eb.date_time_end BETWEEN '$fromDateTime' AND '$toDateTime')";
}

$eventSql .= " ORDER BY eb.date_time_start DESC";
$eventResult = mysqli_query($conn, $eventSql);

// Total for Event Bookings
$eventTotal = 0;
if (mysqli_num_rows($eventResult) > 0) {
    mysqli_data_seek($eventResult, 0);
    while ($row = mysqli_fetch_assoc($eventResult)) {
        $eventTotal += $row['total_amount'];
    }
    mysqli_data_seek($eventResult, 0);
}
?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center mb-3">
        <div>
            <i class="fas fa-home"></i> Sales Report
        </div>

        <!-- Dropdown -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="salesTypeDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                Room
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="salesTypeDropdown">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setSalesType('Room')">Room</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setSalesType('Event')">Event</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="setSalesType('Table')">Table</a></li>
            </ul>
        </div>

    </div>

    <!-- DATE RANGE FILTER -->
    <form method="GET" class="mb-4 d-flex align-items-end gap-2" action="index.php">
        <input type="hidden" name="sales-report" value="">
        <div>
            <label for="from_date" class="form-label">From:</label>
            <input type="date" name="from_date" id="from_date" class="form-control"
                value="<?= htmlspecialchars($fromDate) ?>">
        </div>
        <div>
            <label for="to_date" class="form-label">To:</label>
            <input type="date" name="to_date" id="to_date" class="form-control"
                value="<?= htmlspecialchars($toDate) ?>">
        </div>
        <div>
            <button type="submit" class="btn btn-success">Check Report</button>
        </div>
    </form>

    <!-- SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h6 class="card-title">Room Total</h6>
                    <h4>₱<?= number_format($roomTotal, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h6 class="card-title">Table Total</h6>
                    <h4>₱<?= number_format($tableTotal, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h6 class="card-title">Event Total</h6>
                    <h4>₱<?= number_format($eventTotal, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="info-card" id="roomSection" style="margin-bottom: 40px;">
        <h5>Room Bookings</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Booking Reference</th>
                        <th>Schedule</th>
                        <th>Room Type(s)</th>
                        <th>Booked Room(s)</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($row['booking_reference']) ?>
                                        </td>
                                        <td>
                                            <?= date('M d, Y', strtotime($row['check_in'])) ?> →
                                            <?= date('M d, Y', strtotime($row['check_out'])) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['room_types']) ?>
                                        </td>
                                        <td>
                                            <?= $row['booked_rooms'] ?>
                                        </td>
                                        <td>₱
                                            <?= number_format($row['total_amount'], 2) ?>
                                        </td>
                                    </tr>
                            <?php endwhile; ?>
                    <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No finished bookings found.</td>
                            </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABLE BOOKINGS -->
    <div class="info-card d-none" id="tableSection" style="margin-bottom: 40px;">
        <h5>Table Bookings</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Date & Time</th>
                        <th>Table Name(s)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($tableResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($tableResult)): ?>
                                    <tr>
                                        <td>
                                            <?= $row['order_id'] ?>
                                        </td>
                                        <td>
                                            <?= date('M d, Y h:i A', strtotime($row['date_time'])) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['table_names']) ?>
                                        </td>
                                        <td>₱
                                            <?= number_format($row['total'], 2) ?>
                                        </td>
                                    </tr>
                            <?php endwhile; ?>
                    <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No finished table bookings found.</td>
                            </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- EVENT BOOKINGS -->
    <div class="info-card d-none" id="eventSection" style="margin-bottom: 40px;">
        <h5>Event Bookings</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Booking Reference</th>
                        <th>Schedule</th>
                        <th>Event Type</th>
                        <th>Place</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($eventResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($eventResult)): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($row['booking_refId']) ?>
                                        </td>
                                        <td>
                                            <?= date('M d, Y h:i A', strtotime($row['date_time_start'])) ?> →
                                            <?= date('M d, Y h:i A', strtotime($row['date_time_end'])) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['event_type']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['place']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['total_amount']) ?>
                                        </td>
                                    </tr>
                            <?php endwhile; ?>
                    <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No finished event bookings found.</td>
                            </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


</div>
<?php include 'adminFrontend/footer.php'; ?>

<script>
    function setSalesType(type) {
        document.getElementById('salesTypeDropdown').innerText = type;
        document.getElementById('roomSection').classList.add('d-none');
        document.getElementById('tableSection').classList.add('d-none');
        document.getElementById('eventSection').classList.add('d-none');

        if (type === 'Room') document.getElementById('roomSection').classList.remove('d-none');
        if (type === 'Table') document.getElementById('tableSection').classList.remove('d-none');
        if (type === 'Event') document.getElementById('eventSection').classList.remove('d-none');
    }
</script>