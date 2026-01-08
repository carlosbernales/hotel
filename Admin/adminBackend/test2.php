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

<style>
    .sales-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sales-header h4 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

    .date-filter-group {
        display: flex;
        gap: 15px;
        align-items: end;
        flex-wrap: wrap;
    }

    .date-input-wrapper {
        flex: 1;
        min-width: 200px;
    }

    .date-input-wrapper label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: 500;
        font-size: 14px;
    }

    .date-input-wrapper input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-check {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-check:hover {
        background: #218838;
    }

    .btn-generate {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-generate:hover {
        background: #0056b3;
    }

    .totals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .total-card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
    }

    .total-card.room {
        border-left-color: #007bff;
    }

    .total-card.table {
        border-left-color: #28a745;
    }

    .total-card.event {
        border-left-color: #ffc107;
    }

    .total-card h6 {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 14px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .total-card .amount {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .sales-type-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .tab-btn {
        padding: 10px 25px;
        border: none;
        background: #f0f0f0;
        color: #555;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }

    .tab-btn.active {
        background: #007bff;
        color: white;
    }

    .tab-btn:hover:not(.active) {
        background: #e0e0e0;
    }

    .data-table-container {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .data-table-container h5 {
        margin: 0 0 20px 0;
        color: #2c3e50;
        font-weight: 600;
        font-size: 18px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        background: #2c3e50;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: 500;
        font-size: 14px;
    }

    .data-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
        color: #555;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .no-data {
        text-align: center;
        color: #999;
        padding: 30px;
        font-style: italic;
    }

    .d-none {
        display: none !important;
    }
</style>

<div class="main-content" id="mainContent">
    <div class="sales-header">
        <h4>📊 Sales Report</h4>
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
            <h6>Room Total</h6>
            <p class="amount">₱<?= number_format($roomTotal, 2) ?></p>
        </div>
        <div class="total-card table">
            <h6>Table Total</h6>
            <p class="amount">₱<?= number_format($tableTotal, 2) ?></p>
        </div>
        <div class="total-card event">
            <h6>Event Total</h6>
            <p class="amount">₱<?= number_format($eventTotal, 2) ?></p>
        </div>
    </div>

    <!-- SALES TYPE TABS -->
    <div class="sales-type-tabs">
        <button class="tab-btn active" onclick="setSalesType('Room')" id="roomTab">Room Bookings</button>
        <button class="tab-btn" onclick="setSalesType('Table')" id="tableTab">Table Bookings</button>
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
        // Update tabs
        document.getElementById('roomTab').classList.remove('active');
        document.getElementById('tableTab').classList.remove('active');
        document.getElementById('eventTab').classList.remove('active');

        if (type === 'Room') document.getElementById('roomTab').classList.add('active');
        if (type === 'Table') document.getElementById('tableTab').classList.add('active');
        if (type === 'Event') document.getElementById('eventTab').classList.add('active');

        // Update sections
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