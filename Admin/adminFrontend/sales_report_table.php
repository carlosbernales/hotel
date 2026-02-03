<?php
include 'adminBackend/mydb.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

if ($fromDate && $toDate) {
    $fromDateTime = $fromDate . " 00:00:00";
    $toDateTime = $toDate . " 23:59:59";
}

$sql = "
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
    $sql .= " AND ot.date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
}

$sql .= " GROUP BY ot.id ORDER BY ot.date_time DESC";
$result = mysqli_query($conn, $sql);

$grandTotal = 0;
$totalOrders = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Table Sales Report | Casa Estela Boutique Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --primary-color: #1a2a3a;
            --accent-color: #8e735b;
        }

        body {
            background-color: #e9ecef;
            font-family: 'Inter', -apple-system, sans-serif;
            color: #2d3436;
        }

        .report-container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 60px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .brand-header {
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 25px;
            margin-bottom: 40px;
        }

        .hotel-name {
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--primary-color);
            font-size: 2.2rem;
        }

        .summary-card {
            background: #f8f9fa;
            border-left: 4px solid var(--accent-color);
            padding: 15px 20px;
            margin-bottom: 30px;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            padding: 15px;
        }

        .amount-col {
            background-color: #fdfcfb;
            font-family: 'Courier New', Courier, monospace;
        }

        .total-row {
            background-color: var(--primary-color) !important;
            color: white;
            font-size: 1.2rem;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none;
            }

            .report-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="container text-center mt-4 no-print">
        <button onclick="downloadReceiptPDF()" class="btn btn-primary shadow-sm px-4 me-2">Download PDF Report</button>
    </div>

    <div class="report-container" id="reportContent">
        <div class="brand-header d-flex justify-content-between align-items-end">
            <div>
                <h1 class="hotel-name mb-0">CASA ESTELA</h1>
                <p class="text-uppercase tracking-widest small text-muted mb-0">Boutique Hotel & Cafe</p>
                <p class="small mb-0">Gov B Marasigan St, Calapan City, Oriental Mindoro</p>
            </div>
            <div class="text-end">
                <p class="mb-0 small"><strong>Phone:</strong> 0908 747 4892</p>
                <p class="mb-0 small"><strong>Email:</strong> casaestelaboutiquehotelandcafe@gmail.com</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h4 class="fw-bold text-dark">TABLE SALES REPORT</h4>
                <p class="text-muted small">
                    <?php if ($fromDate && $toDate): ?>
                        Period: <strong><?= date('M d, Y', strtotime($fromDate)) ?></strong> to
                        <strong><?= date('M d, Y', strtotime($toDate)) ?></strong>
                    <?php else: ?>
                        Statement: All-Time Sales
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-6 text-end">
                <div class="p-2 border rounded bg-light d-inline-block text-start">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.6rem;">Generated On</small>
                    <span class="fw-bold small"><?= date('F d, Y | h:i A') ?></span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="summary-card">
                    <small class="text-muted text-uppercase">Total Orders Completed</small>
                    <h3 class="mb-0 fw-bold"><?= $totalOrders ?></h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card">
                    <small class="text-muted text-uppercase">Reported Revenue</small>
                    <h3 class="mb-0 fw-bold text-success" id="revenueHeader">₱0.00</h3>
                </div>
            </div>
        </div>

        <table class="table table-hover border">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Tables & Date/Time</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalOrders > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)):
                        $grandTotal += $row['total']; ?>
                        <tr>
                            <td class="align-middle fw-bold text-secondary">#<?= htmlspecialchars($row['order_id']) ?></td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['table_names']) ?></div>
                                <div class="small text-muted"><?= date('M d, Y h:i A', strtotime($row['date_time'])) ?></div>
                            </td>
                            <td class="text-end align-middle fw-bold amount-col">₱<?= number_format($row['total'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="50"
                                class="mb-3 opacity-25">
                            <p class="text-muted">No transactions found for the specified period.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-end align-middle py-3">GRAND TOTAL REVENUE</td>
                    <td class="text-end align-middle py-3 fw-bold">₱<?= number_format($grandTotal, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 pt-4 border-top">
            <div class="row small text-muted">
                <div class="col-6">
                    <p class="mb-4">Verified By:</p>
                    <div style="border-bottom: 1px solid #ccc; width: 200px;"></div>
                    <p class="mt-1">Authorized Signature</p>
                </div>
                <div class="col-6 text-end align-self-end">
                    <p class="fst-italic">Casa Estela Management System Internal Document</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('revenueHeader').innerText = "₱<?= number_format($grandTotal, 2) ?>";

        async function downloadReceiptPDF() {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('reportContent');

            const canvas = await html2canvas(element, {
                scale: 3,
                useCORS: true,
                logging: false
            });

            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const margin = 10;
            const imgWidth = pageWidth - (margin * 2);
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            pdf.addImage(imgData, 'PNG', margin, margin, imgWidth, imgHeight);
            pdf.save(`Table_Sales_Report_<?= date('Y-m-d') ?>.pdf`);

            window.location.href = 'index.php?sales-report';
        }
    </script>

</body>

</html>