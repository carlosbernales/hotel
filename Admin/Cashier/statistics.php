<?php
require_once('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Casa Estela</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="../../assets/css/daterangepicker.css" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>

    <div class="container-fluid mt-4">
        <h2>Sales Report</h2>
        
        <!-- Date Filter -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date Range:</label>
                    <input type="text" class="form-control" id="daterange" name="daterange">
                </div>
            </div>
        </div>

        <!-- Sales Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daily Sales</h5>
                        <h3 class="card-text" id="dailySales">₱ 0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Sales</h5>
                        <h3 class="card-text" id="monthlySales">₱ 0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Annual Sales</h5>
                        <h3 class="card-text" id="annualSales">₱ 0.00</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Details Table -->
        <div class="table-responsive">
            <table class="table table-bordered" id="salesTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/jquery.dataTables.min.js"></script>
    <script src="../../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../assets/js/moment.min.js"></script>
    <script src="../../assets/js/daterangepicker.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize date range picker
            $('#daterange').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')]
                }
            });

            // Initialize DataTable
            var salesTable = $('#salesTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'get_sales_data.php',
                    type: 'GET',
                    data: function(d) {
                        var dates = $('#daterange').val().split(' - ');
                        return {
                            start_date: dates[0],
                            end_date: dates[1]
                        };
                    }
                },
                columns: [
                    { data: 'order_id' },
                    { data: 'order_date' },
                    { data: 'customer_name' },
                    { data: 'items' },
                    { data: 'total_amount' },
                    { data: 'payment_method' }
                ]
            });

            // Update sales data when date range changes
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                updateSalesSummary(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
                salesTable.ajax.reload();
            });

            function updateSalesSummary(startDate, endDate) {
                $.ajax({
                    url: 'get_sales_summary.php',
                    method: 'POST',
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);
                            $('#dailySales').text('₱ ' + data.daily_sales);
                            $('#monthlySales').text('₱ ' + data.monthly_sales);
                            $('#annualSales').text('₱ ' + data.annual_sales);
                        } catch(e) {
                            console.error('Error parsing response:', e);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', error);
                    }
                });
            }

            // Initial load
            var initialDates = $('#daterange').val().split(' - ');
            updateSalesSummary(initialDates[0], initialDates[1]);
        });
    </script>
</body>
</html>
