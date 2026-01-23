<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

// Date range
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';

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
    LEFT JOIN booked_rooms br ON br.booking_id = b.booking_id
    WHERE b.status = 'finished'
";

if ($fromDate && $toDate) {
    $sql .= " AND (
        b.check_in BETWEEN '$fromDateTime' AND '$toDateTime'
        OR b.check_out BETWEEN '$fromDateTime' AND '$toDateTime'
    )";
}

$sql .= " GROUP BY b.booking_id ORDER BY b.check_in DESC";
$result = mysqli_query($conn, $sql);

// Total Room Sales
$roomTotal = 0;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $roomTotal += $row['total_amount'];
    }
    mysqli_data_seek($result, 0);
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
                    <button type="button" class="btn btn-success" onclick="submitFilter()">
                        Check Report
                    </button>
                    <button type="button" class="btn-generate" onclick="generateReport()">
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
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
                            <td colspan="5" class="no-data">
                                No finished room bookings found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'adminFrontend/footer.php'; ?>

<script>
    function submitFilter() {
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;

        window.location.href =
            `index.php?room-report&from_date=${from}&to_date=${to}`;
    }

    function generateReport() {
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;

        window.location.href =
            `index.php?generate-report-room-det&from_date=${from}&to_date=${to}`;
    }
</script>