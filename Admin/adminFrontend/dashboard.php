<link rel="stylesheet" href="../Admin/adminFrontend/css/dashboard.css">

<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

/* =========================
   TIMEZONE
========================= */
date_default_timezone_set('Asia/Manila');

/* =========================
   DATE RANGES
========================= */
// This Month
$thisMonthStart = date('Y-m-01 00:00:00');
$thisMonthEnd = date('Y-m-t 23:59:59');

// Last Month
$lastMonthStart = date('Y-m-01 00:00:00', strtotime('first day of last month'));
$lastMonthEnd = date('Y-m-t 23:59:59', strtotime('last day of last month'));

/* =========================
   HELPER FUNCTIONS
========================= */
function getSum($conn, $sql, $params = [])
{
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function percentChange($current, $previous)
{
    if ($previous == 0 && $current > 0)
        return 100;
    if ($previous == 0)
        return 0;
    return (($current - $previous) / $previous) * 100;
}

function getWeeklyCounts($conn, $table, $dateColumn, $statusColumn, $statusValue, $weekStart, $weekEnd)
{
    $counts = array_fill(0, 7, 0);
    $sql = "
        SELECT WEEKDAY(DATE($dateColumn)) AS wd, COUNT(*) AS total
        FROM $table
        WHERE $statusColumn = ?
          AND DATE($dateColumn) BETWEEN ? AND ?
        GROUP BY wd
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $statusValue, $weekStart, $weekEnd);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $counts[(int) $r['wd']] = (int) $r['total'];
    }
    return $counts;
}


/* =========================
   MONTHLY BOOKINGS & REVENUE
========================= */

/* CAFE BOOKINGS */
$cafeMonth = getSum($conn, "
    SELECT COALESCE(SUM(total),0) AS total
    FROM orders_table
    WHERE status='Completed'
      AND date_time BETWEEN ? AND ?
", [$thisMonthStart, $thisMonthEnd]);

$cafeLast = getSum($conn, "
    SELECT COALESCE(SUM(total),0) AS total
    FROM orders_table
    WHERE status='Completed'
      AND date_time BETWEEN ? AND ?
", [$lastMonthStart, $lastMonthEnd]);

$cafeOverall = getSum($conn, "
    SELECT COALESCE(SUM(total),0) AS total
    FROM orders_table
    WHERE status='Completed'
");

/* ROOM BOOKINGS */
$roomMonth = getSum($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM bookings
    WHERE status='finished'
      AND check_out BETWEEN ? AND ?
", [$thisMonthStart, $thisMonthEnd]);

$roomLast = getSum($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM bookings
    WHERE status='finished'
      AND check_out BETWEEN ? AND ?
", [$lastMonthStart, $lastMonthEnd]);

$roomOverall = getSum($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM bookings
    WHERE status='finished'
");

/* EVENT BOOKINGS */
$eventMonth = getSum($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM event_bookings
    WHERE booking_status='Finished'
      AND date_time_end BETWEEN ? AND ?
", [$thisMonthStart, $thisMonthEnd]);

$eventLast = getSum($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM event_bookings
    WHERE booking_status='Finished'
      AND date_time_end BETWEEN ? AND ?
", [$lastMonthStart, $lastMonthEnd]);

$eventOverall = getSum($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM event_bookings
    WHERE booking_status='Finished'
");

/* TOTAL REVENUE */
$totalMonthRevenue = $roomMonth + $cafeMonth + $eventMonth;
$totalLastRevenue = $roomLast + $cafeLast + $eventLast;
$totalOverallRevenue = $roomOverall + $cafeOverall + $eventOverall;

/* PERCENT CHANGES */
$roomChange = percentChange($roomMonth, $roomLast);
$cafeChange = percentChange($cafeMonth, $cafeLast);
$eventChange = percentChange($eventMonth, $eventLast);
$revenueChange = percentChange($totalMonthRevenue, $totalLastRevenue);

/* =========================
   WEEKLY BOOKINGS
========================= */
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));


$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

$weeklyRooms = getWeeklyCounts($conn, 'bookings', 'check_out', 'status', 'finished', $weekStart, $weekEnd);
$weeklyCafe = getWeeklyCounts($conn, 'orders_table', 'date_time', 'status', 'Completed', $weekStart, $weekEnd);
$weeklyEvents = getWeeklyCounts($conn, 'event_bookings', 'date_time_end', 'booking_status', 'Finished', $weekStart, $weekEnd);

/* Scaling for charts */
$maxWeekly = max(max($weeklyRooms), max($weeklyCafe), max($weeklyEvents), 1);

/* =========================
   OVERALL DISTRIBUTION
========================= */
$roomTotalCount = getSum($conn, "SELECT COUNT(*) total FROM bookings WHERE status='finished'");
$cafeTotalCount = getSum($conn, "SELECT COUNT(*) total FROM orders_table WHERE status='Completed'");
$eventTotalCount = getSum($conn, "SELECT COUNT(*) total FROM event_bookings WHERE booking_status='Finished'");
$maxOverall = max($roomTotalCount, $cafeTotalCount, $eventTotalCount, 1);


/* =========================
   BOOKING COUNTS (ROWS)
========================= */

// THIS MONTH COUNTS
$roomCountMonth = getSum($conn, "
    SELECT COUNT(*) total FROM bookings
    WHERE status='finished'
      AND check_out BETWEEN ? AND ?
", [$thisMonthStart, $thisMonthEnd]);

$cafeCountMonth = getSum($conn, "
    SELECT COUNT(*) total FROM orders_table
    WHERE status='Completed'
      AND date_time BETWEEN ? AND ?
", [$thisMonthStart, $thisMonthEnd]);

$eventCountMonth = getSum($conn, "
    SELECT COUNT(*) total FROM event_bookings
    WHERE booking_status='Finished'
      AND date_time_end BETWEEN ? AND ?
", [$thisMonthStart, $thisMonthEnd]);

// OVERALL COUNTS
$roomCountOverall = getSum($conn, "SELECT COUNT(*) total FROM bookings WHERE status='finished'");
$cafeCountOverall = getSum($conn, "SELECT COUNT(*) total FROM orders_table WHERE status='Completed'");
$eventCountOverall = getSum($conn, "SELECT COUNT(*) total FROM event_bookings WHERE booking_status='Finished'");


/* =========================
   COUNT PERCENT CHANGES
========================= */

// LAST MONTH COUNTS (for comparison)
$roomCountLast = getSum($conn, "
    SELECT COUNT(*) total FROM bookings 
    WHERE status='finished' 
      AND check_out BETWEEN ? AND ?
", [$lastMonthStart, $lastMonthEnd]);

$cafeCountLast = getSum($conn, "
    SELECT COUNT(*) total FROM orders_table 
    WHERE status='Completed' 
      AND date_time BETWEEN ? AND ?
", [$lastMonthStart, $lastMonthEnd]);

$eventCountLast = getSum($conn, "
    SELECT COUNT(*) total FROM event_bookings 
    WHERE booking_status='Finished' 
      AND date_time_end BETWEEN ? AND ?
", [$lastMonthStart, $lastMonthEnd]);

// CALCULATE PERCENTAGE CHANGE FOR COUNTS
$roomCountChange = percentChange($roomCountMonth, $roomCountLast);
$cafeCountChange = percentChange($cafeCountMonth, $cafeCountLast);
$eventCountChange = percentChange($eventCountMonth, $eventCountLast);

// Fetch Active Room Numbers Count
$activeRoomsCount = getSum($conn, "
    SELECT COUNT(room_number_id) as total 
    FROM room_numbers 
    WHERE status = 'active'
");
?>



<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom">
        <i class="fas fa-home"> Dashboard Overview </i>
    </div>

    <div class="stats-container">
        <!-- ROOM BOOKINGS COUNT -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Room Bookings</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number"><?= $roomCountMonth ?></div>
                    </div>
                </div>
                <div class="stat-icon room"><i class="fas fa-bed"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val"><?= $roomCountOverall ?></div>
                </div>
                <span class="stat-change <?= $roomCountChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $roomCountChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($roomCountChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- CAFE ORDERS COUNT -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Cafe Orders</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number"><?= $cafeCountMonth ?></div>
                    </div>
                </div>
                <div class="stat-icon cafe"><i class="fas fa-utensils"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val"><?= $cafeCountOverall ?></div>
                </div>
                <span class="stat-change <?= $cafeCountChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $cafeCountChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($cafeCountChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- EVENT BOOKINGS COUNT -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Event Bookings</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number"><?= $eventCountMonth ?></div>
                    </div>
                </div>
                <div class="stat-icon event"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val"><?= $eventCountOverall ?></div>
                </div>
                <span class="stat-change <?= $eventCountChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $eventCountChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($eventCountChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- ROOM STATUS -->
        <div class="stat-card availability-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <div class="status-indicator">
                        <span class="pulse-dot"></span>
                        <h3>Room Status</h3>
                    </div>
                    <div class="stat-main-val">
                        <span class="stat-label">ACTIVE ROOMS</span>
                        <div class="stat-number"><?= $activeRoomsCount ?></div>
                    </div>
                </div>
                <div class="stat-icon availability">
                    <i class="fas fa-door-open"></i>
                </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div class="status-tag">
                    <i class="fas fa-check-circle"></i> <?= $activeRoomsCount ?> Operational
                </div>
            </div>
        </div>

        <!-- ROOM REVENUE -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Room Revenue</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number">₱<?= number_format($roomMonth, 2) ?></div>
                    </div>
                </div>
                <div class="stat-icon room"><i class="fas fa-bed"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val">₱<?= number_format($roomOverall, 2) ?></div>
                </div>
                <span class="stat-change <?= $roomChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $roomChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($roomChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- CAFE REVENUE -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Cafe Revenue</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number">₱<?= number_format($cafeMonth, 2) ?></div>
                    </div>
                </div>
                <div class="stat-icon cafe"><i class="fas fa-utensils"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val">₱<?= number_format($cafeOverall, 2) ?></div>
                </div>
                <span class="stat-change <?= $cafeChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $cafeChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($cafeChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- EVENT REVENUE -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Event Revenue</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number">₱<?= number_format($eventMonth, 2) ?></div>
                    </div>
                </div>
                <div class="stat-icon event"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val">₱<?= number_format($eventOverall, 2) ?></div>
                </div>
                <span class="stat-change <?= $eventChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $eventChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($eventChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- TOTAL REVENUE -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Total Revenue</h3>
                    <div class="stat-main-val">
                        <span class="stat-label">THIS MONTH</span>
                        <div class="stat-number">₱<?= number_format($totalMonthRevenue, 2) ?></div>
                    </div>
                </div>
                <div class="stat-icon revenue"><i class="fas fa-coins"></i></div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-overall">
                <div>
                    <span class="stat-label">OVERALL</span>
                    <div class="overall-val">₱<?= number_format($totalOverallRevenue, 2) ?></div>
                </div>
                <span class="stat-change <?= $revenueChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $revenueChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($revenueChange, 1)) ?>%
                </span>
            </div>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="charts-container">
        <!-- WEEKLY BOOKINGS CHART -->
        <div class="chart-card">
            <h4>
                <span><i class="fas fa-chart-bar"></i> Weekly Bookings Overview</span>
                <div class="chart-legend">
                    <div class="legend-item"><span class="legend-dot" style="background: #667eea"></span> Room</div>
                    <div class="legend-item"><span class="legend-dot" style="background: #f5576c"></span> Cafe</div>
                    <div class="legend-item"><span class="legend-dot" style="background: #00f2fe"></span> Event</div>
                </div>
            </h4>
            <div class="simple-chart weekly-3bars">
                <?php foreach ($days as $i => $day): ?>
                    <div class="chart-group">
                        <div class="chart-bar room" style="height: <?= ($weeklyRooms[$i] / $maxWeekly) * 100 ?>%;">
                            <span class="chart-bar-count"><?= $weeklyRooms[$i] > 0 ? $weeklyRooms[$i] : '' ?></span>
                            <span class="chart-bar-value">Rooms: <?= $weeklyRooms[$i] ?></span>
                        </div>

                        <div class="chart-bar cafe" style="height: <?= ($weeklyCafe[$i] / $maxWeekly) * 100 ?>%;">
                            <span class="chart-bar-count"><?= $weeklyCafe[$i] > 0 ? $weeklyCafe[$i] : '' ?></span>
                            <span class="chart-bar-value">Cafe: <?= $weeklyCafe[$i] ?></span>
                        </div>

                        <div class="chart-bar event" style="height: <?= ($weeklyEvents[$i] / $maxWeekly) * 100 ?>%;">
                            <span class="chart-bar-count"><?= $weeklyEvents[$i] > 0 ? $weeklyEvents[$i] : '' ?></span>
                            <span class="chart-bar-value">Events: <?= $weeklyEvents[$i] ?></span>
                        </div>

                        <span class="chart-bar-label"><?= $day ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- BOOKING DISTRIBUTION CHART -->
        <div class="chart-card">
            <h4><span><i class="fas fa-chart-pie"></i> Booking Distribution</span></h4>
            <div class="simple-chart" style="gap: 3rem; padding-bottom: 50px;">
                <div class="chart-bar room" style="height: <?= ($roomTotalCount / $maxOverall) * 100 ?>%; width: 60px;">
                    <span class="chart-bar-count"><?= $roomTotalCount ?></span>
                    <span class="chart-bar-label">Rooms</span>
                    <span class="chart-bar-value">Total: <?= $roomTotalCount ?></span>
                </div>
                <div class="chart-bar cafe" style="height: <?= ($cafeTotalCount / $maxOverall) * 100 ?>%; width: 60px;">
                    <span class="chart-bar-count"><?= $cafeTotalCount ?></span>
                    <span class="chart-bar-label">Cafe</span>
                    <span class="chart-bar-value">Total: <?= $cafeTotalCount ?></span>
                </div>
                <div class="chart-bar event"
                    style="height: <?= ($eventTotalCount / $maxOverall) * 100 ?>%; width: 60px;">
                    <span class="chart-bar-count"><?= $eventTotalCount ?></span>
                    <span class="chart-bar-label">Events</span>
                    <span class="chart-bar-value">Total: <?= $eventTotalCount ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'adminFrontend/footer.php'; ?>