<?php
require_once 'db.php';
include 'header.php';
include 'sidebar.php';

// Get current date and time
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

// Function to get revenue for current month
function getCurrentMonthRevenue($con) {
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');
    $query = "SELECT SUM(total_amount) as total FROM booking 
              WHERE (status = 'checked_in' OR status = 'checked_out') 
              AND checkin BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Function to get today's expected check-ins
function getTodayCheckIns($con) {
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM booking WHERE checkin = '$today' AND status = 'reserved'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Function to get today's expected check-outs
function getTodayCheckOuts($con) {
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM booking WHERE checkout = '$today' AND status = 'checked_in'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <!-- Breadcrumb -->
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i></a></li>
            <li class="active">Dashboard</li>
        </ol>
    </div>

    <!-- Welcome Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="welcome-card">
                <h2>Welcome to Casa Estela Admin</h2>
                <p class="date-time"><?php echo date('l, F j, Y'); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row">
        <!-- Current Occupancy -->
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-card-inner">
                    <i class="fa fa-bed stat-icon"></i>
                    <div class="stat-content">
                        <h3><?php include 'counters/checkedin-count.php'?></h3>
                        <p>Current Occupancy</p>
                    </div>
                </div>
                <div class="stat-footer" onclick="window.location='checked_in.php'">
                    View Details <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-card-inner">
                    <i class="fa fa-money stat-icon"></i>
                    <div class="stat-content">
                        <h3>₱<?php echo number_format(getCurrentMonthRevenue($con), 2); ?></h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
                <div class="stat-footer" onclick="window.location='sales_report.php'">
                    View Details <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- Expected Check-ins -->
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-card-inner">
                    <i class="fa fa-sign-in stat-icon"></i>
                    <div class="stat-content">
                        <h3><?php echo getTodayCheckIns($con); ?></h3>
                        <p>Expected Check-ins Today</p>
                    </div>
                </div>
                <div class="stat-footer" onclick="window.location='booking_status.php'">
                    View Details <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- Expected Check-outs -->
        <div class="col-md-3">
            <div class="stat-card danger">
                <div class="stat-card-inner">
                    <i class="fa fa-sign-out stat-icon"></i>
                    <div class="stat-content">
                        <h3><?php echo getTodayCheckOuts($con); ?></h3>
                        <p>Expected Check-outs Today</p>
                    </div>
                </div>
                <div class="stat-footer" onclick="window.location='checked_in.php'">
                    View Details <i class="fa fa-arrow-right"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings and Room Status -->
    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Recent Bookings
                    <span class="pull-right">
                        <a href="BookingList.php" class="btn btn-xs btn-primary">View All</a>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest Name</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM booking ORDER BY id DESC LIMIT 5";
                                $result = mysqli_query($con, $query);
                                while($row = mysqli_fetch_array($result)) {
                                    $statusClass = '';
                                    switch($row['status']) {
                                        case 'reserved': $statusClass = 'label-warning'; break;
                                        case 'checked_in': $statusClass = 'label-success'; break;
                                        case 'checked_out': $statusClass = 'label-default'; break;
                                        default: $statusClass = 'label-info';
                                    }
                                    echo "<tr>";
                                    echo "<td>#".$row['id']."</td>";
                                    echo "<td>".$row['name']."</td>";
                                    echo "<td>".$row['room_type']."</td>";
                                    echo "<td>".date('M d, Y', strtotime($row['checkin']))."</td>";
                                    echo "<td><span class='label ".$statusClass."'>".ucfirst(str_replace('_', ' ', $row['status']))."</span></td>";
                                    echo "<td><a href='booking_status.php?id=".$row['id']."' class='btn btn-xs btn-info'>View</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Status Overview -->
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Room Status Overview
                    <span class="pull-right">
                        <a href="room_management.php" class="btn btn-xs btn-primary">Manage Rooms</a>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="room-status-chart">
                        <canvas id="roomStatusChart"></canvas>
                    </div>
                    <div class="room-status-legend">
                        <div class="legend-item">
                            <span class="legend-color bg-success"></span>
                            <span class="legend-label">Available</span>
                            <span class="legend-value"><?php include 'counters/avrooms-count.php'?></span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color bg-warning"></span>
                            <span class="legend-label">Reserved</span>
                            <span class="legend-value"><?php include 'counters/reserve-count.php'?></span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color bg-danger"></span>
                            <span class="legend-label">Occupied</span>
                            <span class="legend-value"><?php include 'counters/checkedin-count.php'?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Dashboard Styles */
.welcome-card {
    background: linear-gradient(135deg, #4CAF50, #2196F3);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.welcome-card h2 {
    margin: 0;
    font-size: 24px;
}

.date-time {
    margin: 5px 0 0;
    opacity: 0.9;
}

.stat-card {
    background: white;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.primary { border-top: 4px solid #2196F3; }
.stat-card.success { border-top: 4px solid #4CAF50; }
.stat-card.warning { border-top: 4px solid #FFC107; }
.stat-card.danger { border-top: 4px solid #F44336; }

.stat-card-inner {
    padding: 20px;
    display: flex;
    align-items: center;
}

.stat-icon {
    font-size: 40px;
    margin-right: 15px;
    opacity: 0.7;
}

.stat-content h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.stat-content p {
    margin: 5px 0 0;
    color: #666;
}

.stat-footer {
    padding: 10px 20px;
    background: rgba(0, 0, 0, 0.03);
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    cursor: pointer;
    transition: background 0.2s;
}

.stat-footer:hover {
    background: rgba(0, 0, 0, 0.05);
}

.panel {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.panel-heading {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.room-status-chart {
    padding: 15px;
    height: 200px;
}

.room-status-legend {
    padding: 15px;
    border-top: 1px solid #eee;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.legend-color {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    margin-right: 10px;
}

.legend-label {
    flex: 1;
}

.bg-success { background-color: #4CAF50; }
.bg-warning { background-color: #FFC107; }
.bg-danger { background-color: #F44336; }

.label {
    padding: 5px 10px;
    border-radius: 15px;
    font-weight: normal;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Room Status Chart
    var ctx = document.getElementById('roomStatusChart').getContext('2d');
    var available = parseInt(document.querySelector('.legend-item:nth-child(1) .legend-value').textContent);
    var reserved = parseInt(document.querySelector('.legend-item:nth-child(2) .legend-value').textContent);
    var occupied = parseInt(document.querySelector('.legend-item:nth-child(3) .legend-value').textContent);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Reserved', 'Occupied'],
            datasets: [{
                data: [available, reserved, occupied],
                backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
