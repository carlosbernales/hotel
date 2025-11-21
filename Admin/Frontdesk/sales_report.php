<?php
require_once 'includes/init.php';
require_once 'db.php';
include 'header.php';
include 'sidebar.php';

// Initialize variables
$totalAmount = 0;
$totalDownpayment = 0;
$chartData = [];
$salesData = null;

// Get view type from POST or set default
$rangeType = $_POST['range_type'] ?? 'daily';
$today = date('Y-m-d');

// Set date ranges based on range type
switch($rangeType) {
    case 'daily':
        // Today only
        $startDate = $today;
        $endDate = $today;
        $dateCondition = "DATE(created_at) = '$today'";
        break;
    case 'weekly':
        // Get the start of current week (Sunday)
        $startDate = date('Y-m-d', strtotime('last sunday'));
        // If today is Sunday, use today as start date
        if (date('w') == 0) {
            $startDate = $today;
        }
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        $dateCondition = "DATE(created_at) >= '$startDate' AND DATE(created_at) <= '$endDate'";
        break;
    case 'monthly':
        // Current month (1st to last day)
        $startDate = date('Y-m-01'); // First day of current month
        $endDate = date('Y-m-t');    // Last day of current month
        $dateCondition = "DATE(created_at) >= '$startDate' AND DATE(created_at) <= '$endDate'";
        break;
    case 'yearly':
        // Current year
        $startDate = date('Y-01-01'); // First day of current year
        $endDate = date('Y-12-31');   // Last day of current year
        $dateCondition = "DATE(created_at) >= '$startDate' AND DATE(created_at) <= '$endDate'";
        break;
    default:
        // Fallback to today
        $startDate = $today;
        $endDate = $today;
        $dateCondition = "DATE(created_at) = '$today'";
}

// Get report type from POST or set default
$reportType = isset($_POST['report_type']) ? $_POST['report_type'] : 'all';

function getAdminSales($dateCondition) {
    global $con;
    
    // Get total amounts
    $totalQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                  FROM bookings 
                  WHERE $dateCondition AND user_types = 'admin'";
    
    $totalStmt = mysqli_prepare($con, $totalQuery);
    if (!$totalStmt) {
        die("Error preparing total statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($totalStmt);
    $totalResult = mysqli_stmt_get_result($totalStmt);
    $totals = mysqli_fetch_assoc($totalResult);
    
    // Get detailed booking data
    $query = "SELECT 
        booking_id,
        first_name,
        last_name,
        booking_type,
        email,
        contact,
        check_in,
        check_out,
        arrival_time,
        number_of_guests,
        payment_option,
        payment_method,
        discount_type,
        discount_percentage,
        total_amount,
        status,
        created_at
    FROM bookings 
    WHERE $dateCondition AND user_types = 'admin'
    ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($stmt);
    $details = mysqli_stmt_get_result($stmt);
    
    return [
        'details' => $details,
        'totals' => $totals
    ];
}

function getFrontDeskSales($dateCondition) {
    global $con;
    
    // Get total amounts
    $totalQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                  FROM bookings 
                  WHERE $dateCondition AND user_types = 'frontdesk'";
    
    $totalStmt = mysqli_prepare($con, $totalQuery);
    if (!$totalStmt) {
        die("Error preparing total statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($totalStmt);
    $totalResult = mysqli_stmt_get_result($totalStmt);
    $totals = mysqli_fetch_assoc($totalResult);
    
    // Get detailed booking data
    $query = "SELECT 
        booking_id,
        first_name,
        last_name,
        booking_type,
        email,
        contact,
        check_in,
        check_out,
        arrival_time,
        number_of_guests,
        payment_option,
        payment_method,
        discount_type,
        discount_percentage,
        total_amount,
        status,
        created_at
    FROM bookings 
    WHERE $dateCondition AND user_types = 'frontdesk'
    ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($stmt);
    $details = mysqli_stmt_get_result($stmt);
    
    return [
        'details' => $details,
        'totals' => $totals
    ];
}

function getCashierSales($dateCondition) {
    global $con;
    
    // Convert the date condition to work with orders table
    $dateCondition = str_replace('created_at', 'order_date', $dateCondition);
    
    // Get total amounts from orders table
    $totalQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                  FROM orders 
                  WHERE $dateCondition";
    
    $totalStmt = mysqli_prepare($con, $totalQuery);
    if (!$totalStmt) {
        die("Error preparing total statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($totalStmt);
    $totalResult = mysqli_stmt_get_result($totalStmt);
    $totals = mysqli_fetch_assoc($totalResult);
    
    // Get detailed order data with exact field names from orders table
    $query = "SELECT 
        id as booking_id,
        customer_name,
        contact_number as contact,
        order_type,
        payment_method,
        payment_status,
        total_amount,
        discount_type,
        discount_amount,
        status,
        order_date as created_at
    FROM orders 
    WHERE $dateCondition
    ORDER BY order_date DESC";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($stmt);
    $details = mysqli_stmt_get_result($stmt);
    
    return [
        'details' => $details,
        'totals' => $totals
    ];
}

function getAllSales($dateCondition) {
    global $con;
    
    // Get total amounts
    $totalQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                  FROM bookings 
                  WHERE $dateCondition";
    
    $totalStmt = mysqli_prepare($con, $totalQuery);
    if (!$totalStmt) {
        die("Error preparing total statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($totalStmt);
    $totalResult = mysqli_stmt_get_result($totalStmt);
    $totals = mysqli_fetch_assoc($totalResult);
    
    // Get detailed booking data
    $query = "SELECT 
        booking_id,
        first_name,
        last_name,
        booking_type,
        email,
        contact,
        check_in,
        check_out,
        arrival_time,
        number_of_guests,
        payment_option,
        payment_method,
        discount_type,
        discount_percentage,
        total_amount,
        status,
        created_at
    FROM bookings 
    WHERE $dateCondition
    ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($con));
    }
    
    mysqli_stmt_execute($stmt);
    $details = mysqli_stmt_get_result($stmt);
    
    return [
        'details' => $details,
        'totals' => $totals
    ];
}

// Function to get sales data based on date range
function getSalesData($dateCondition) {
    global $reportType;
    
    switch($reportType) {
        case 'admin':
            return getAdminSales($dateCondition);
        case 'frontdesk':
            return getFrontDeskSales($dateCondition);
        case 'cashier':
            return getCashierSales($dateCondition);
        case 'all':
            return getAllSales($dateCondition);
        default:
            return getAllSales($dateCondition);
    }
}

// Get sales data
if ($_SERVER['REQUEST_METHOD'] === 'POST' || !isset($_POST['range_type'])) {
    try {
        $result = getSalesData($dateCondition);
        if (is_array($result)) {
            $salesData = $result['details'];
            $totals = $result['totals'];
            $totalAmount = $totals['total_revenue'] ?? 0;
            
            // Process results for chart data
            if ($salesData) {
                $chartData = [];
                $chartLabels = [];
                mysqli_data_seek($salesData, 0);
                while ($row = mysqli_fetch_assoc($salesData)) {
                    // Get the appropriate date field based on report type
                    $dateField = isset($row['created_at']) ? $row['created_at'] : 
                               (isset($row['order_date']) ? $row['order_date'] : date('Y-m-d'));
                    
                    $date = date('M d', strtotime($dateField));
                    if (!isset($chartData[$date])) {
                        $chartData[$date] = 0;
                        $chartLabels[] = $date;
                    }
                    $chartData[$date] += floatval($row['total_amount']);
                }
                mysqli_data_seek($salesData, 0);
            }
        } else {
            $salesData = $result;
            // Process old format for non-admin reports
            if ($salesData) {
                $chartData = [];
                $chartLabels = [];
                while ($row = mysqli_fetch_assoc($salesData)) {
                    $totalAmount += floatval($row['total_amount']);
                    // Get the appropriate date field based on report type
                    $dateField = isset($row['created_at']) ? $row['created_at'] : 
                               (isset($row['order_date']) ? $row['order_date'] : date('Y-m-d'));
                    
                    $date = date('M d', strtotime($dateField));
                    if (!isset($chartData[$date])) {
                        $chartData[$date] = 0;
                        $chartLabels[] = $date;
                    }
                    $chartData[$date] += floatval($row['total_amount']);
                }
                mysqli_data_seek($salesData, 0);
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><em class="fa fa-home"></em></a></li>
            <li class="active">Sales Report</li>
        </ol>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Sales Report
            <div class="pull-right">
                <button class="btn btn-success" onclick="exportCurrentReport()">
                    <i class="fa fa-download"></i> Export Current Report
                </button>
            </div>
        </div>
        <div class="panel-body">
            <!-- Filters -->
            <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>View Type:</label>
                    <select class="form-control" name="range_type" id="rangeType">
                        <option value="daily" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'daily' ? 'selected' : ''; ?>>Daily </option>
                        <option value="weekly" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'weekly' ? 'selected' : ''; ?>>Weekly </option>
                        <option value="monthly" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'monthly' ? 'selected' : ''; ?>>Monthly </option>
                        <option value="yearly" <?php echo isset($_POST['range_type']) && $_POST['range_type'] == 'yearly' ? 'selected' : ''; ?>>Yearly </option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Report Type:</label>
                    <select class="form-control" name="report_type" id="reportType">
                        <option value="all" <?php echo $reportType == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="admin" <?php echo $reportType == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="frontdesk" <?php echo $reportType == 'frontdesk' ? 'selected' : ''; ?>>Front Desk</option>
                        <option value="cashier" <?php echo $reportType == 'cashier' ? 'selected' : ''; ?>>Cashier</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-refresh"></i> Update Report
                    </button>
                </div>
            </div>
            </form>

            <!-- Summary Cards -->
            <div class="row summary-cards mb-4">
                <div class="col-md-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>
                            <?php 
                            switch($rangeType) {
                                case 'daily':
                                    echo "Today's Revenue (" . date('F j, Y') . ")";
                                    break;
                                case 'weekly':
                                    echo "Weekly Revenue (" . date('M d', strtotime($startDate)) . " - " . date('M d', strtotime($endDate)) . ")";
                                    break;
                                case 'monthly':
                                    echo "Monthly Revenue (" . date('F Y') . ")";
                                    break;
                                case 'yearly':
                                    echo "Yearly Revenue (" . date('Y') . ")";
                                    break;
                            }
                            ?>
                            </h5>
                            <h3>₱<?php echo number_format($totalAmount, 2); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown Table -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <?php 
                        switch($rangeType) {
                            case 'daily':
                                echo "Today's Bookings";
                                break;
                            case 'weekly':
                                echo "This Week's Bookings";
                                break;
                            case 'monthly':
                                echo "This Month's Bookings";
                                break;
                            case 'yearly':
                                echo "This Year's Bookings";
                                break;
                        }
                        ?>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <?php if ($reportType == 'cashier'): ?>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Contact</th>
                                    <th>Payment Method</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th>Discount</th>
                                    <th class="text-right">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $runningTotal = 0;
                                if ($salesData && mysqli_num_rows($salesData) > 0): 
                                    while ($row = mysqli_fetch_assoc($salesData)): 
                                        $runningTotal += floatval($row['total_amount']);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($row['discount_type']) && $row['discount_type'] != 'none' && !empty($row['discount_amount']) && $row['discount_amount'] > 0) {
                                            echo htmlspecialchars($row['discount_type'] . ': ' . $row['discount_amount']);
                                        } else {
                                            echo 'None';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-right">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <tr class="table-info font-weight-bold">
                                    <td colspan="6" class="text-right">Total Revenue</td>
                                    <td class="text-right">₱<?php echo number_format($runningTotal, 2); ?></td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No orders found for this period</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php else: ?>
                            <!-- Original booking table structure -->
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest Name</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Discount</th>
                                    <th class="text-right">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $runningTotal = 0;
                                if ($salesData && mysqli_num_rows($salesData) > 0): 
                                    while ($row = mysqli_fetch_assoc($salesData)): 
                                        $runningTotal += floatval($row['total_amount']);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['check_in'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['check_out'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($row['discount_type']) && $row['discount_type'] != 'none') {
                                            echo htmlspecialchars($row['discount_type']);
                                            if (!empty($row['discount_percentage'])) {
                                                echo ': ' . $row['discount_percentage'] . '%';
                                            }
                                        } else {
                                            echo 'None';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-right">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <tr class="table-info font-weight-bold">
                                    <td colspan="7" class="text-right">Total Revenue</td>
                                    <td class="text-right">₱<?php echo number_format($runningTotal, 2); ?></td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No bookings found for this period</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Revenue Trend</h5>
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Status Distribution</h5>
                            <canvas id="distributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function exportCurrentReport() {
    const reportType = document.getElementById('reportType').value;
    const rangeType = document.getElementById('rangeType').value;
    window.location.href = `export_report.php?report_type=${reportType}&range_type=${rangeType}`;
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if data exists
    <?php if (!empty($chartData)): ?>
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($chartData)); ?>,
            datasets: [{
                label: <?php echo $reportType == 'cashier' ? "'Daily Orders Revenue'" : "'Revenue by Booking Date'"; ?>,
                data: <?php echo json_encode(array_values($chartData)); ?>,
                borderColor: '#DAA520',
                backgroundColor: 'rgba(218, 165, 32, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Status Distribution Chart
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    <?php if ($reportType == 'cashier'): ?>
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Completed'],
            datasets: [{
                data: [
                    <?php 
                        $pendingTotal = 0;
                        $processingTotal = 0;
                        $completedTotal = 0;
                        
                        if ($salesData) {
                            mysqli_data_seek($salesData, 0);
                            while ($row = mysqli_fetch_assoc($salesData)) {
                                if (strtolower($row['status']) == 'completed') {
                                    $completedTotal += floatval($row['total_amount']);
                                } else if (strtolower($row['status']) == 'processing') {
                                    $processingTotal += floatval($row['total_amount']);
                                } else {
                                    $pendingTotal += floatval($row['total_amount']);
                                }
                            }
                            echo $pendingTotal . ', ' . $processingTotal . ', ' . $completedTotal;
                            mysqli_data_seek($salesData, 0);
                        } else {
                            echo '0, 0, 0';
                        }
                    ?>
                ],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ₱${context.raw.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    <?php else: ?>
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Checked In', 'Checked Out'],
            datasets: [{
                data: [
                    <?php 
                        $pendingTotal = 0;
                        $checkedInTotal = 0;
                        $checkedOutTotal = 0;
                        
                        if ($salesData) {
                            mysqli_data_seek($salesData, 0);
                            while ($row = mysqli_fetch_assoc($salesData)) {
                                if ($row['status'] == 'Checked Out') {
                                    $checkedOutTotal += floatval($row['total_amount']);
                                } else if ($row['status'] == 'Checked In') {
                                    $checkedInTotal += floatval($row['total_amount']);
                                } else {
                                    $pendingTotal += floatval($row['total_amount']);
                                }
                            }
                            echo $pendingTotal . ', ' . $checkedInTotal . ', ' . $checkedOutTotal;
                            mysqli_data_seek($salesData, 0);
                        } else {
                            echo '0, 0, 0';
                        }
                    ?>
                ],
                backgroundColor: ['#ffc107', '#007bff', '#28a745']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ₱${context.raw.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
    <?php endif; ?>
});
</script>

<style>
.card {
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
    margin-bottom: 1rem;
}

.card-body {
    padding: 1.25rem;
}

.card-title {
    margin-bottom: 1rem;
    font-size: 1.25rem;
}

.bg-primary { background-color: #DAA520 !important; }

.text-white {
    color: white !important;
}

.summary-cards h3 {
    font-size: 2.5rem;
    margin-bottom: 0;
}

.summary-cards h5 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.badge {
    padding: 0.4em 0.6em;
    font-size: 85%;
    font-weight: 500;
    border-radius: 0.25rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.text-right {
    text-align: right !important;
}

.font-weight-bold {
    font-weight: bold !important;
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    .main {
        margin-left: 0 !important;
        width: 100% !important;
    }
    
    .card {
        break-inside: avoid;
    }
}

/* Add these styles for better chart display */
.card {
    margin-bottom: 20px;
}

#revenueChart {
    height: 300px !important;
}

#distributionChart {
    height: 300px !important;
}

.card-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
}
</style>