<?php
include_once "db.php";
include_once "header.php";
include_once "sidebar.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch room type statistics
$room_stats_query = "SELECT room_name, COUNT(*) as count 
                    FROM reservation 
                    GROUP BY room_name";
$room_stats_result = mysqli_query($con, $room_stats_query);
if (!$room_stats_result) {
    echo "Room Stats Error: " . mysqli_error($con);
}
$room_stats_data = [];
while($row = mysqli_fetch_assoc($room_stats_result)) {
    $room_stats_data[] = [$row['room_name'], (int)$row['count']];
}

// Fetch monthly revenue data for the current year
$revenue_query = "SELECT MONTH(created_at) as month, SUM(total_price) as revenue 
                 FROM reservation 
                 WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
                 GROUP BY MONTH(created_at)
                 ORDER BY month";
$revenue_result = mysqli_query($con, $revenue_query);
if (!$revenue_result) {
    echo "Revenue Query Error: " . mysqli_error($con);
}
$revenue_data = [];
while($row = mysqli_fetch_assoc($revenue_result)) {
    $month_name = date('F', mktime(0, 0, 0, $row['month'], 10));
    $revenue_data[] = [$month_name, (float)$row['revenue'], "gold"];
}

// Fetch booking data for calendar
$booking_query = "SELECT DATE(created_at) as date, COUNT(*) as count 
                 FROM reservation 
                 WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
                 GROUP BY DATE(created_at)";
$booking_result = mysqli_query($con, $booking_query);
if (!$booking_result) {
    echo "Booking Query Error: " . mysqli_error($con);
}
$booking_data = [];
while($row = mysqli_fetch_assoc($booking_result)) {
    $date = new DateTime($row['date']);
    $booking_data[] = sprintf("[ new Date(%d, %d, %d), %d ]",
        (int)$date->format('Y'),
        (int)$date->format('m') - 1, // JavaScript months are 0-based
        (int)$date->format('d'),
        (int)$row['count']
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales & Statistics Report</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart", "calendar"]});
        
        // Room Type Distribution Chart
        google.charts.setOnLoadCallback(drawRoomChart);
        function drawRoomChart() {
            var data = google.visualization.arrayToDataTable([
                ['Room Type', 'Count'],
                <?php
                if (count($room_stats_data) > 0) {
                    foreach($room_stats_data as $row) {
                        echo sprintf("['%s', %d],", $row[0], $row[1]);
                    }
                } else {
                    echo "['No Data', 0]";
                }
                ?>
            ]);

            var options = {
                title: 'Room Type Distribution',
                is3D: true,
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }

        // Monthly Revenue Chart
        google.charts.setOnLoadCallback(drawRevenueChart);
        function drawRevenueChart() {
            var data = google.visualization.arrayToDataTable([
                ["Month", "Revenue", { role: "style" }],
                <?php
                if (count($revenue_data) > 0) {
                    foreach($revenue_data as $row) {
                        echo sprintf("['%s', %.2f, '%s'],", $row[0], $row[1], $row[2]);
                    }
                } else {
                    echo "['No Data', 0, 'gold']";
                }
                ?>
            ]);

            var view = new google.visualization.DataView(data);
            view.setColumns([0, 1,
                           { calc: "stringify",
                             sourceColumn: 1,
                             type: "string",
                             role: "annotation" },
                           2]);

            var options = {
                title: "Monthly Revenue (Current Year)",
                backgroundColor: 'transparent',
                width: 410,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: { position: "none" },
            };
            var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
            chart.draw(view, options);
        }

        // Booking Calendar Chart
        google.charts.setOnLoadCallback(drawCalendarChart);
        function drawCalendarChart() {
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn({ type: 'date', id: 'Date' });
            dataTable.addColumn({ type: 'number', id: 'Bookings' });
            dataTable.addRows([
                <?php 
                if (count($booking_data) > 0) {
                    echo implode(",\n                ", $booking_data);
                } else {
                    echo sprintf("[ new Date(%d, %d, %d), %d ]",
                        (int)date('Y'), (int)date('m')-1, (int)date('d'), 0);
                }
                ?>
            ]);

            var options = {
                title: "Daily Bookings",
                height: 350,
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.Calendar(document.getElementById('calendar_basic'));
            chart.draw(dataTable, options);
        }
    </script>
    <style>
        .report-container {
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
        }
        .chart-wrapper {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #piechart_3d {
            width: 400px; 
            height: 400px;
            margin-left: 300px;
        }
        #barchart_values {
            width: 400px; 
            height: 400px;
            margin-left: 800px;
            margin-top: -400px;
        }
        #calendar_basic {
            width: 1000px; 
            height: 250px;
            margin-left: 300px;
        }
    </style>
</head>
<body>
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Sales & Statistics Report</h1>
            </div>
        </div>
        <div class="report-container">
            <div class="chart-wrapper">
                <div id="piechart_3d"></div>
            </div>
            <div class="chart-wrapper">
                <div id="barchart_values"></div>
            </div>
            <div class="chart-wrapper">
                <div id="calendar_basic"></div>
            </div>
        </div>
    </div>
</body>
</html>
<?php include_once "footer.php"; ?>