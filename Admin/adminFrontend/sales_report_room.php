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
    $sql .= " AND (b.check_in BETWEEN '$fromDateTime' AND '$toDateTime' OR b.check_out BETWEEN '$fromDateTime' AND '$toDateTime')";
}

$sql .= " GROUP BY b.booking_id ORDER BY b.check_in DESC";
$result = mysqli_query($conn, $sql);
$totalRevenue = 0;
$totalBookings = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Report | Casa Estela Boutique Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --primary-color: #1a2a3a;
            --accent-color: #8e735b;
            /* Gold/Tan accent for boutique feel */
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
                <h4 class="fw-bold text-dark">SALES REVENUE REPORT</h4>
                <p class="text-muted small">
                    <?php if ($fromDate && $toDate): ?>
                        Date Range: <strong><?= date('M d, Y', strtotime($fromDate)) ?></strong> to
                        <strong><?= date('M d, Y', strtotime($toDate)) ?></strong>
                    <?php else: ?>
                        Statement: All-Time Cumulative Records
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
                    <small class="text-muted text-uppercase">Total Completed Bookings</small>
                    <h3 class="mb-0 fw-bold"><?= $totalBookings ?></h3>
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
                    <th>Ref. ID</th>
                    <th>Guest Stay & Room Details</th>
                    <th class="text-center">Units</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalBookings > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)):
                        $totalRevenue += $row['total_amount']; ?>
                        <tr>
                            <td class="align-middle fw-bold text-secondary">#<?= htmlspecialchars($row['booking_reference']) ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['room_types']) ?></div>
                                <div class="small text-muted">
                                    Period: <?= date('M d', strtotime($row['check_in'])) ?> -
                                    <?= date('M d, Y', strtotime($row['check_out'])) ?>
                                </div>
                            </td>
                            <td class="text-center align-middle"><?= $row['booked_rooms'] ?></td>
                            <td class="text-end align-middle fw-bold amount-col">₱<?= number_format($row['total_amount'], 2) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="50"
                                class="mb-3 opacity-25">
                            <p class="text-muted">No transactions found for the specified period.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-end align-middle py-3">GRAND TOTAL REVENUE</td>
                    <td class="text-end align-middle py-3 fw-bold">₱<?= number_format($totalRevenue, 2) ?></td>
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
        document.getElementById('revenueHeader').innerText = "₱<?= number_format($totalRevenue, 2) ?>";

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
            pdf.save(`Sales_Report_<?= date('Y-m-d') ?>.pdf`);

            window.location.href = 'index.php?sales-report';
        }

    </script>
</body>

</html>