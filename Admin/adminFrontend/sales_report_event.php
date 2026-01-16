<?php
include 'adminBackend/mydb.php';

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

if ($fromDate && $toDate) {
    $fromDateTime = $fromDate . " 00:00:00";
    $toDateTime = $toDate . " 23:59:59";
}

$sql = "SELECT booking_refId, date_time_start, date_time_end, event_type, place, total_amount
        FROM event_bookings
        WHERE booking_status = 'Finished'";

if ($fromDate && $toDate) {
    $sql .= " AND (date_time_start BETWEEN '$fromDateTime' AND '$toDateTime' OR date_time_end BETWEEN '$fromDateTime' AND '$toDateTime')";
}

$sql .= " ORDER BY date_time_start DESC";
$result = mysqli_query($conn, $sql);

$totalSales = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Courier New', Courier, monospace;
        }

        /* Receipt Styling */
        .receipt-container {
            max-width: 500px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid #ddd;
        }

        /* Jagged Edge Effect */
        .receipt-container::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(-45deg, transparent 5px, #fff 5px),
                linear-gradient(45deg, transparent 5px, #fff 5px);
            background-size: 10px 10px;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 15px;
        }

        .receipt-header h3 {
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .item-details {
            flex: 1;
        }

        .item-price {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 15px 0;
        }

        .total-section {
            font-size: 1.2rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .footer-note {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            font-style: italic;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="container text-center mt-3 no-print">
        <button onclick="downloadReceiptPDF()" class="btn btn-outline-dark shadow-sm">
            Download PDF
        </button>

    </div>

    <div class="receipt-container">
        <div class="receipt-header">
            <h3>Event Sales Report</h3>
            <p class="mb-0">Official Business Record</p>
            <small>
                <?php if ($fromDate && $toDate): ?>
                    Period: <?= date('M d, Y', strtotime($fromDate)) ?> - <?= date('M d, Y', strtotime($toDate)) ?>
                <?php else: ?>
                    All-Time Sales
                <?php endif; ?>
            </small>
        </div>

        <div class="receipt-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $totalSales += $row['total_amount'];
                    ?>
                    <div class="item-row">
                        <div class="item-details">
                            <strong>#<?= htmlspecialchars($row['booking_refId']) ?></strong><br>
                            <small><?= htmlspecialchars($row['event_type']) ?> @
                                <?= htmlspecialchars($row['place']) ?></small><br>
                            <small class="text-muted"><?= date('M d, Y', strtotime($row['date_time_start'])) ?></small>
                        </div>
                        <div class="item-price align-self-center">
                            ₱<?= number_format($row['total_amount'], 2) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center py-4">No records found.</p>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <div class="total-section">
            <span>TOTAL SALES</span>
            <span>₱<?= number_format($totalSales, 2) ?></span>
        </div>

        <div class="divider"></div>

        <div class="footer-note">
            <p>Report Generated:
                <?= date('F d, Y h:i A') ?><br>
                *** End of Report ***
            </p>
        </div>

    </div>


    <script>
        async function downloadReceiptPDF() {
            const { jsPDF } = window.jspdf;
            const receipt = document.querySelector('.receipt-container');

            const canvas = await html2canvas(receipt, {
                scale: 2,
                useCORS: true
            });

            const imgData = canvas.toDataURL('image/png');

            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const imgWidth = pageWidth;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            let position = 0;
            let heightLeft = imgHeight;

            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save(`event-sales-${new Date().toISOString().slice(0, 10)}.pdf`);


            setTimeout(() => {
                window.location.href = 'index.php?sales-report';
            }, 500);
        }
    </script>


</body>

</html>