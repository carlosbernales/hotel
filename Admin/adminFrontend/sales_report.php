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
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $roomTotal += $row['total_amount'];
    }
    mysqli_data_seek($result, 0);
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
    WHERE ot.status = 'Completed'
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
<link rel="stylesheet" href="../Admin/adminFrontend/css/sales_report.css">


<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center p-3">
        <i class="fas fa-home"> Sales Report</i>
    </div>

    <!-- DATE RANGE FILTER -->
    <div class="filter-section">
        <form method="GET" id="reportForm">
            <input type="hidden" name="from_date" id="fromDateInput" value="<?= htmlspecialchars($fromDate) ?>">
            <input type="hidden" name="to_date" id="toDateInput" value="<?= htmlspecialchars($toDate) ?>">
            <input type="hidden" name="sales_type" id="salesTypeInput" value="Room">

            <div class="date-filter-group">
                <div class="date-input-wrapper">
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" value="<?= htmlspecialchars($fromDate) ?>">
                </div>

                <div class="date-input-wrapper">
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" value="<?= htmlspecialchars($toDate) ?>">
                </div>

                <div class="filter-buttons">
                    <div>
                        <button type="button" class="btn btn-success" onclick="submitFilter()">
                            Check Report
                        </button>
                    </div>
                    <button type="button" class="btn-generate" onclick="generateReport()">
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="totals-grid">
        <div class="total-card room">
            <h6>Room </h6>
            <p class="amount">₱<?= number_format($roomTotal, 2) ?></p>
        </div>
        <div class="total-card table">
            <h6>Cafe </h6>
            <p class="amount">₱<?= number_format($tableTotal, 2) ?></p>
        </div>
        <div class="total-card event">
            <h6>Event </h6>
            <p class="amount">₱<?= number_format($eventTotal, 2) ?></p>
        </div>
    </div>

    <!-- SALES TYPE TABS -->
    <div class="sales-type-tabs">
        <button class="tab-btn active" onclick="setSalesType('Room')" id="roomTab">Room Bookings</button>
        <button class="tab-btn" onclick="setSalesType('Table')" id="tableTab">Cafe Bookings</button>
        <button class="tab-btn" onclick="setSalesType('Event')" id="eventTab">Event Bookings</button>
    </div>

    <!-- ROOM BOOKINGS TABLE -->
    <div class="data-table-container" id="roomSection">
        <h5>Room Bookings</h5>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
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
                                <td><?= htmlspecialchars($row['booking_reference']) ?></td>
                                <td>
                                    <?= date('M d, Y', strtotime($row['check_in'])) ?> →
                                    <?= date('M d, Y', strtotime($row['check_out'])) ?>
                                </td>
                                <td><?= htmlspecialchars($row['room_types']) ?></td>
                                <td><?= $row['booked_rooms'] ?></td>
                                <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No finished bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABLE BOOKINGS -->
    <div class="data-table-container d-none" id="tableSection">
        <h5>Table Bookings</h5>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
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
                                <td><?= $row['order_id'] ?></td>
                                <td><?= date('M d, Y h:i A', strtotime($row['date_time'])) ?></td>
                                <td><?= htmlspecialchars($row['table_names']) ?></td>
                                <td>₱<?= number_format($row['total'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">No finished table bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- EVENT BOOKINGS -->
    <div class="data-table-container d-none" id="eventSection">
        <h5>Event Bookings</h5>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
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
                                <td><?= htmlspecialchars($row['booking_refId']) ?></td>
                                <td>
                                    <?= date('M d, Y h:i A', strtotime($row['date_time_start'])) ?> →
                                    <?= date('M d, Y h:i A', strtotime($row['date_time_end'])) ?>
                                </td>
                                <td><?= htmlspecialchars($row['event_type']) ?></td>
                                <td><?= htmlspecialchars($row['place']) ?></td>
                                <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No finished event bookings found.</td>
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
        document.getElementById('roomTab').classList.remove('active');
        document.getElementById('tableTab').classList.remove('active');
        document.getElementById('eventTab').classList.remove('active');

        if (type === 'Room') document.getElementById('roomTab').classList.add('active');
        if (type === 'Table') document.getElementById('tableTab').classList.add('active');
        if (type === 'Event') document.getElementById('eventTab').classList.add('active');

        document.getElementById('salesTypeInput').value = type;
        document.getElementById('roomSection').classList.add('d-none');
        document.getElementById('tableSection').classList.add('d-none');
        document.getElementById('eventSection').classList.add('d-none');

        if (type === 'Room') document.getElementById('roomSection').classList.remove('d-none');
        if (type === 'Table') document.getElementById('tableSection').classList.remove('d-none');
        if (type === 'Event') document.getElementById('eventSection').classList.remove('d-none');
    }

    function submitFilter() {
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;

        window.location.href = `index.php?sales-report&from_date=${from}&to_date=${to}`;
    }

    function generateReport() {
        const type = document.getElementById('salesTypeInput').value;
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;

        let url = '';

        if (type === 'Room') url = 'index.php?generate-report-room';
        if (type === 'Event') url = 'index.php?generate-report-event';
        if (type === 'Table') url = 'index.php?generate-report-table';

        url += `&from_date=${from}&to_date=${to}`;

        window.location.href = url;
    }
</script>