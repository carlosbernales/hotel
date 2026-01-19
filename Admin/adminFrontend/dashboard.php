<style>
    :root {
        --gold: #d4af37;
        --gold-light: #f4e7c3;
        --gold-dark: #b8941f;
        --dark-bg: #2c2c2c;
        --sidebar-width: 250px;
        --room-color: #667eea;
        --cafe-color: #f5576c;
        --event-color: #00f2fe;
        --shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        color: #333;
        line-height: 1.6;
    }

    .main-content {
        padding: 20px;
        min-height: 100vh;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Breadcrumb */
    .breadcrumb-custom {
        background: white;
        padding: 18px 25px;
        border-radius: 16px;
        margin-bottom: 25px;
        box-shadow: var(--shadow);
        font-weight: 600;
        color: var(--dark-bg);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .breadcrumb-custom i {
        color: var(--gold);
        margin-right: 10px;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .action-btn {
        background: white;
        border: none;
        border-radius: 16px;
        padding: 25px 20px;
        text-align: center;
        text-decoration: none;
        color: #444;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 0;
    }

    .action-btn:hover::before {
        opacity: 1;
    }

    .action-btn i,
    .action-btn span {
        position: relative;
        z-index: 1;
        transition: color 0.3s ease;
    }

    .action-btn i {
        font-size: 2rem;
        color: var(--gold);
        margin-bottom: 12px;
        display: block;
    }

    .action-btn:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .action-btn:hover i,
    .action-btn:hover span {
        color: white;
    }

    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: var(--gold);
        transition: width 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card:hover::before {
        width: 100%;
        opacity: 0.05;
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .stat-info h3 {
        font-size: 0.75rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .stat-main-val .stat-label {
        font-size: 0.7rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--dark-bg);
        margin-top: 8px;
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-icon.room {
        background: linear-gradient(135deg, #667eea, #764ba2);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .stat-icon.cafe {
        background: linear-gradient(135deg, #f5576c, #f093fb);
        box-shadow: 0 8px 20px rgba(245, 87, 108, 0.3);
    }

    .stat-icon.event {
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        box-shadow: 0 8px 20px rgba(79, 172, 254, 0.3);
    }

    .stat-icon.revenue {
        background: linear-gradient(135deg, #d4af37, #b8941f);
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
    }

    .stat-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #eee, transparent);
        margin: 20px 0;
    }

    .stat-overall {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .stat-overall .stat-label {
        font-size: 0.65rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .overall-val {
        font-weight: 700;
        font-size: 1.1rem;
        color: #555;
    }

    .stat-change {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .stat-change.positive {
        background: linear-gradient(135deg, #e6fcf5, #d4f8e8);
        color: #0ca678;
    }

    .stat-change.negative {
        background: linear-gradient(135deg, #fff5f5, #ffe8e8);
        color: #fa5252;
    }

    /* Charts Section */
    .charts-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 25px;
    }

    .chart-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: var(--shadow-hover);
    }

    .chart-card h4 {
        margin-bottom: 30px;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        color: var(--dark-bg);
    }

    .chart-card h4 i {
        color: var(--gold);
        margin-right: 8px;
    }

    /* Legend */
    .chart-legend {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: #666;
        font-weight: 600;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    /* Weekly Bar Chart */
    .simple-chart.weekly-3bars {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 280px;
        padding-bottom: 40px;
        border-bottom: 2px solid #f0f0f0;
        gap: 10px;
    }

    .chart-group {
        display: flex;
        align-items: flex-end;
        gap: 5px;
        height: 100%;
        position: relative;
        flex: 1;
        max-width: 80px;
    }

    .chart-bar {
        flex: 1;
        min-width: 12px;
        border-radius: 6px 6px 2px 2px;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .chart-bar:hover {
        transform: scaleY(1.08);
        filter: brightness(1.15);
    }

    .chart-bar.room {
        background: linear-gradient(180deg, #667eea, #764ba2);
    }

    .chart-bar.cafe {
        background: linear-gradient(180deg, #f5576c, #f093fb);
    }

    .chart-bar.event {
        background: linear-gradient(180deg, #4facfe, #00f2fe);
    }

    .chart-bar-count {
        position: absolute;
        top: -22px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 11px;
        font-weight: 800;
        color: #333;
    }

    .chart-bar-value {
        position: absolute;
        top: -38px;
        left: 50%;
        transform: translateX(-50%) scale(0);
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 10px;
        white-space: nowrap;
        transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 5;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .chart-bar-value::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #333;
    }

    .chart-bar:hover .chart-bar-value {
        transform: translateX(-50%) scale(1);
    }

    .chart-bar-label {
        position: absolute;
        bottom: -30px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        font-weight: 700;
        color: #888;
        white-space: nowrap;
    }

    /* Distribution Chart */
    .simple-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 280px;
        gap: 20px;
        padding-bottom: 40px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .charts-container {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 15px;
        }

        .breadcrumb-custom {
            padding: 15px 20px;
            font-size: 0.9rem;
        }

        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .action-btn {
            padding: 20px 15px;
        }

        .action-btn i {
            font-size: 1.5rem;
        }

        .action-btn span {
            font-size: 0.85rem;
        }

        .stats-container {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .stat-card {
            padding: 20px;
        }

        .stat-number {
            font-size: 1.5rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        .charts-container {
            gap: 20px;
        }

        .chart-card {
            padding: 20px;
        }

        .chart-card h4 {
            font-size: 1rem;
            flex-direction: column;
            align-items: flex-start;
        }

        .chart-group {
            gap: 3px;
            max-width: 60px;
        }

        .chart-bar {
            min-width: 10px;
        }

        .simple-chart.weekly-3bars {
            height: 220px;
            gap: 5px;
        }

        .simple-chart {
            height: 220px;
        }
    }

    @media (max-width: 480px) {
        .quick-actions {
            grid-template-columns: 1fr;
        }

        .stat-card-header {
            flex-direction: column;
            gap: 15px;
        }

        .stat-icon {
            align-self: flex-end;
        }

        .chart-bar-label {
            font-size: 10px;
        }

        .legend-item {
            font-size: 0.7rem;
        }
    }
</style>

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

?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div><i class="fas fa-home"></i> Dashboard Overview</div>
    </div>

    <div class="quick-actions">
        <a href="#" class="action-btn"><i class="fas fa-bed"></i><span>New Room Booking</span></a>
        <a href="#" class="action-btn"><i class="fas fa-utensils"></i><span>New Cafe Booking</span></a>
        <a href="#" class="action-btn"><i class="fas fa-calendar-alt"></i><span>New Event Booking</span></a>
        <a href="#" class="action-btn"><i class="fas fa-users"></i><span>View Customers</span></a>
    </div>

    <div class="stats-container">
        <!-- ROOM -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Room Bookings</h3>
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
                    <span class="stat-label">OVERALL TOTAL</span>
                    <div class="overall-val">₱<?= number_format($roomOverall, 2) ?></div>
                </div>
                <span class="stat-change <?= $roomChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $roomChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($roomChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- CAFE -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Cafe Bookings</h3>
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
                    <span class="stat-label">OVERALL TOTAL</span>
                    <div class="overall-val">₱<?= number_format($cafeOverall, 2) ?></div>
                </div>
                <span class="stat-change <?= $cafeChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $cafeChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($cafeChange, 1)) ?>%
                </span>
            </div>
        </div>

        <!-- EVENT -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Event Bookings</h3>
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
                    <span class="stat-label">OVERALL TOTAL</span>
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
                    <span class="stat-label">OVERALL TOTAL</span>
                    <div class="overall-val">₱<?= number_format($totalOverallRevenue, 2) ?></div>
                </div>
                <span class="stat-change <?= $revenueChange >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-arrow-<?= $revenueChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs(round($revenueChange, 1)) ?>%
                </span>
            </div>
        </div>
    </div>

    <!-- WEEKLY BOOKINGS CHART -->
    <div class="charts-container">
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

        <div class="chart-card">
            <h4><span><i class="fas fa-chart-pie"></i> Booking Distribution</span></h4>
            <div class="simple-chart" style="gap: 20px; padding-bottom: 40px;">
                <div class="chart-bar room" style="height: <?= ($roomTotalCount / $maxOverall) * 100 ?>%; width: 40px;">
                    <span class="chart-bar-count"><?= $roomTotalCount ?></span>
                    <span class="chart-bar-label">Rooms</span>
                    <span class="chart-bar-value">Total: <?= $roomTotalCount ?></span>
                </div>
                <div class="chart-bar cafe" style="height: <?= ($cafeTotalCount / $maxOverall) * 100 ?>%; width: 40px;">
                    <span class="chart-bar-count"><?= $cafeTotalCount ?></span>
                    <span class="chart-bar-label">Cafe</span>
                    <span class="chart-bar-value">Total: <?= $cafeTotalCount ?></span>
                </div>
                <div class="chart-bar event"
                    style="height: <?= ($eventTotalCount / $maxOverall) * 100 ?>%; width: 40px;">
                    <span class="chart-bar-count"><?= $eventTotalCount ?></span>
                    <span class="chart-bar-label">Events</span>
                    <span class="chart-bar-value">Total: <?= $eventTotalCount ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'adminFrontend/footer.php'; ?>