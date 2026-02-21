<?php
// Include database connection
require_once 'db.php';

// Handle export request
try {
    // Get export type from GET parameters
    $exportType = isset($_GET['export']) ? $_GET['export'] : 'csv';
    
    // Get date range from GET parameters
    $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
    $toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';
    
    // Prepare date conditions for queries
    $dateCondition = '';
    if ($fromDate && $toDate) {
        $fromDateTime = $fromDate . ' 00:00:00';
        $toDateTime = $toDate . ' 23:59:59';
        $dateCondition = " AND date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
    } else {
        // Default to last 30 days if no filter
        $dateCondition = " AND date_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }
    
    // Fetch sales data for export
    $sql = "
        SELECT 
            o.order_id,
            o.firstname,
            o.lastname,
            o.total,
            o.status,
            o.date_time,
            COALESCE(ott.table_number, 'N/A') as table_number,
            CASE 
                WHEN o.payment_method IS NOT NULL THEN o.payment_method
                ELSE 'N/A'
            END as payment_method
        FROM orders_table o
        LEFT JOIN orders_table_type ott ON o.order_id = ott.table_booking_fk_id
        WHERE o.status = 'Completed'
        $dateCondition
        ORDER BY o.date_time DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $salesData = $stmt->fetchAll();
    
    // Calculate summary
    $totalSales = array_sum(array_column($salesData, 'total'));
    $totalOrders = count($salesData);
    
    // Handle different export types
    if ($exportType === 'pdf') {
        // Generate PDF as printable HTML
        generatePDFReport($salesData, $fromDate, $toDate, $totalSales, $totalOrders);
    } else {
        // Original CSV export logic
        generateCSVReport($salesData, $fromDate, $toDate, $totalSales, $totalOrders);
    }
    
} catch (Exception $e) {
    if ($exportType === 'pdf') {
        generateErrorPDF($e->getMessage());
    } else {
        generateErrorCSV($e->getMessage());
    }
}

function generatePDFReport($salesData, $fromDate, $toDate, $totalSales, $totalOrders) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sales Report - Casa Estela Boutique Hotel & Cafe</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
            
            @media print {
                body { margin: 0; }
                .no-print { display: none !important; }
                @page { 
                    margin: 15mm;
                    size: A4;
                    @bottom-center {
                        content: counter(page);
                        font-size: 10pt;
                        color: #666;
                    }
                }
            }
            
            * {
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                margin: 0;
                padding: 0;
                color: #1a1a1a;
                background: #ffffff;
                line-height: 1.6;
            }
            
            .report-container {
                max-width: 210mm;
                margin: 0 auto;
                padding: 20mm;
            }
            
            .header {
                text-align: center;
                margin-bottom: 40px;
                position: relative;
            }
            
            .header::after {
                content: '';
                position: absolute;
                bottom: -20px;
                left: 50%;
                transform: translateX(-50%);
                width: 100px;
                height: 3px;
                background: linear-gradient(135deg, #b8860b, #d4af37);
                border-radius: 2px;
            }
            
            .logo-placeholder {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #b8860b, #d4af37);
                border-radius: 50%;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 32px;
                font-weight: 700;
            }
            
            .header h1 {
                margin: 0 0 8px 0;
                font-size: 32px;
                font-weight: 700;
                color: #1a1a1a;
                letter-spacing: -0.5px;
            }
            
            .header .subtitle {
                margin: 0 0 4px 0;
                font-size: 16px;
                color: #666;
                font-weight: 500;
            }
            
            .header .date {
                margin: 0;
                font-size: 14px;
                color: #888;
                font-weight: 400;
            }
            
            .report-meta {
                background: #f8f9fa;
                border-left: 4px solid #b8860b;
                padding: 20px 24px;
                margin: 30px 0;
                border-radius: 0 8px 8px 0;
            }
            
            .report-meta h3 {
                margin: 0 0 12px 0;
                font-size: 14px;
                font-weight: 600;
                color: #1a1a1a;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .report-meta p {
                margin: 4px 0;
                font-size: 14px;
                color: #555;
            }
            
            .summary-section {
                margin: 40px 0;
            }
            
            .summary-title {
                font-size: 18px;
                font-weight: 600;
                color: #1a1a1a;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 24px;
                margin-bottom: 40px;
            }
            
            .summary-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 24px;
                text-align: center;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .summary-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #b8860b, #d4af37);
            }
            
            .summary-card .value {
                font-size: 28px;
                font-weight: 700;
                color: #1a1a1a;
                margin-bottom: 8px;
                line-height: 1.2;
            }
            
            .summary-card .label {
                font-size: 13px;
                color: #666;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .data-section {
                margin: 40px 0;
            }
            
            .section-title {
                font-size: 18px;
                font-weight: 600;
                color: #1a1a1a;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .data-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                margin: 0;
                background: white;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            
            .data-table thead {
                background: linear-gradient(135deg, #1a1a1a, #333);
                color: white;
            }
            
            .data-table th {
                padding: 16px 20px;
                text-align: left;
                font-weight: 600;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border: none;
            }
            
            .data-table td {
                padding: 16px 20px;
                border-bottom: 1px solid #f1f3f4;
                font-size: 14px;
                vertical-align: middle;
            }
            
            .data-table tbody tr:last-child td {
                border-bottom: none;
            }
            
            .data-table tbody tr:nth-child(even) {
                background: #fafbfc;
            }
            
            .data-table tbody tr:hover {
                background: #f0f7ff;
            }
            
            .order-id {
                font-family: 'Courier New', monospace;
                font-weight: 600;
                color: #b8860b;
            }
            
            .customer-name {
                font-weight: 500;
                color: #1a1a1a;
            }
            
            .table-number {
                background: #f0f7ff;
                color: #0066cc;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
                text-align: center;
                display: inline-block;
                min-width: 60px;
            }
            
            .amount {
                text-align: right;
                font-weight: 600;
                color: #10b981;
                font-family: 'Inter', sans-serif;
            }
            
            .payment-method {
                color: #555;
                font-size: 13px;
            }
            
            .status {
                text-align: center;
            }
            
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .status-badge.completed {
                background: #d1fae5;
                color: #065f46;
            }
            
            .date-time {
                color: #666;
                font-size: 13px;
            }
            
            .no-data {
                text-align: center;
                padding: 60px 20px;
                color: #666;
                font-style: italic;
            }
            
            .footer {
                margin-top: 60px;
                padding-top: 30px;
                border-top: 1px solid #e5e7eb;
                text-align: center;
            }
            
            .footer p {
                margin: 8px 0;
                font-size: 12px;
                color: #666;
        }
            
            .footer .report-id {
                font-family: 'Courier New', monospace;
                background: #f8f9fa;
                padding: 4px 8px;
                border-radius: 4px;
                display: inline-block;
                margin-top: 8px;
            }
            
            .no-print {
                margin-bottom: 30px;
                text-align: center;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            
            .btn {
                background: linear-gradient(135deg, #b8860b, #d4af37);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                margin: 0 8px;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }
            
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(184, 134, 11, 0.3);
            }
            
            .btn-secondary {
                background: #6b7280;
            }
            
            .btn-secondary:hover {
                background: #4b5563;
                box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="report-container">
            <div class="no-print">
                <button class="btn" onclick="window.print()">
                    🖨️ Print Report
                </button>
                <button class="btn btn-secondary" onclick="window.close()">
                    ✕ Close
                </button>
            </div>
            
            <header class="header">
                <div class="logo-placeholder">CE</div>
                <h1>Sales Report</h1>
                <p class="subtitle">Casa Estela Boutique Hotel & Cafe</p>
                <p class="date">Generated on <?php echo date('F j, Y \a\t H:i:s'); ?></p>
            </header>
            
            <div class="report-meta">
                <h3>Report Information</h3>
                <p><strong>Report Period:</strong> 
                <?php 
                if ($fromDate && $toDate) {
                    echo date('F j, Y', strtotime($fromDate)) . ' to ' . date('F j, Y', strtotime($toDate));
                } else {
                    echo 'Last 30 Days';
                }
                ?>
                </p>
                <p><strong>Total Records:</strong> <?php echo count($salesData); ?> transactions</p>
                <p><strong>Report Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
            
            <section class="summary-section">
                <h2 class="summary-title">Financial Summary</h2>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="value">₱<?php echo number_format($totalSales, 2); ?></div>
                        <div class="label">Total Sales</div>
                    </div>
                    <div class="summary-card">
                        <div class="value"><?php echo $totalOrders; ?></div>
                        <div class="label">Total Orders</div>
                    </div>
                    <div class="summary-card">
                        <div class="value">₱<?php echo number_format($totalSales / max($totalOrders, 1), 2); ?></div>
                        <div class="label">Average Order Value</div>
                    </div>
                </div>
            </section>
            
            <section class="data-section">
                <h2 class="section-title">Transaction Details</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Table</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($salesData)): ?>
                            <?php foreach ($salesData as $sale): ?>
                            <tr>
                                <td class="order-id">#<?php echo $sale['order_id']; ?></td>
                                <td class="customer-name"><?php echo htmlspecialchars(trim($sale['firstname'] . ' ' . $sale['lastname'])) ?: 'Guest'; ?></td>
                                <td><span class="table-number"><?php echo htmlspecialchars($sale['table_number']); ?></span></td>
                                <td class="amount">₱<?php echo number_format($sale['total'], 2); ?></td>
                                <td class="payment-method"><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                                <td class="status">
                                    <span class="status-badge <?php echo strtolower($sale['status']); ?>">
                                        <?php echo ucfirst($sale['status']); ?>
                                    </span>
                                </td>
                                <td class="date-time"><?php echo date('M j, Y H:i', strtotime($sale['date_time'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-data">
                                    No sales data found for the selected period.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
            
            <footer class="footer">
                <p><strong>Official Sales Report</strong></p>
                <p>Casa Estela Boutique Hotel & Cafe</p>
                <p>This report contains confidential business information and is intended for internal use only.</p>
                <div class="report-id">Report ID: <?php echo uniqid('RPT_'); ?></div>
            </footer>
        </div>
        
        <script>
            // Auto-print when page loads (optional)
            window.onload = function() {
                // Uncomment the next line to automatically print when page loads
                // window.print();
            };
        </script>
    </body>
    </html>
    <?php
    exit();
}

function generateCSVReport($salesData, $fromDate, $toDate, $totalSales, $totalOrders) {
    // If no data found, create empty CSV with message
    if (empty($salesData)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Sales Report']);
        fputcsv($output, ['No sales data found for the selected period']);
        
        if ($fromDate && $toDate) {
            fputcsv($output, ['Report Period', date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate))]);
        } else {
            fputcsv($output, ['Report Period', 'Last 30 Days']);
        }
        
        fclose($output);
        exit();
    }
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'Order ID',
        'Customer Name',
        'Total Amount',
        'Payment Method',
        'Status',
        'Date & Time'
    ]);
    
    // Add data rows
    foreach ($salesData as $sale) {
        $customerName = trim($sale['firstname'] . ' ' . $sale['lastname']);
        if (empty($customerName)) {
            $customerName = 'Guest';
        }
        
        fputcsv($output, [
            $sale['order_id'],
            $customerName,
            number_format($sale['total'], 2),
            $sale['payment_method'],
            $sale['status'],
            date('Y-m-d H:i:s', strtotime($sale['date_time']))
        ]);
    }
    
    // Calculate and add summary row
    fputcsv($output, []);
    fputcsv($output, ['SUMMARY REPORT']);
    fputcsv($output, ['Total Orders', $totalOrders]);
    fputcsv($output, ['Total Sales', '₱' . number_format($totalSales, 2)]);
    fputcsv($output, ['Average Order Value', '₱' . number_format($totalSales / max($totalOrders, 1), 2)]);
    
    if ($fromDate && $toDate) {
        fputcsv($output, ['Report Period', date('M j, Y', strtotime($fromDate)) . ' - ' . date('M j, Y', strtotime($toDate))]);
    } else {
        fputcsv($output, ['Report Period', 'Last 30 Days']);
    }
    
    fclose($output);
    exit();
}

function generateErrorPDF($errorMessage) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Export Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .error { color: #e74c3c; }
        </style>
    </head>
    <body>
        <h1 class="error">Export Error</h1>
        <p><strong>Error Message:</strong> <?php echo htmlspecialchars($errorMessage); ?></p>
        <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </body>
    </html>
    <?php
    exit();
}

function generateErrorCSV($errorMessage) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export_error_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Export Error']);
    fputcsv($output, ['Error Message', $errorMessage]);
    fputcsv($output, ['Time', date('Y-m-d H:i:s')]);
    fclose($output);
    exit();
}
?>
