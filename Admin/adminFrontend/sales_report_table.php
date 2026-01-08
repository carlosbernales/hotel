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
    WHERE ot.status = 'Finished'
";

if ($fromDate && $toDate) {
    $sql .= " AND ot.date_time BETWEEN '$fromDateTime' AND '$toDateTime'";
}

$sql .= " GROUP BY ot.id ORDER BY ot.date_time DESC";
$result = mysqli_query($conn, $sql);

$grandTotal = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Sales Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
        }

        .receipt-container {
            max-width: 450px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid #ddd;
        }

        /* Zig-zag bottom edge */
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
            padding-bottom: 10px;
        }

        .receipt-header h4 {
            margin: 0;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .order-entry {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            line-height: 1.2;
        }

        .order-info {
            flex: 1;
            padding-right: 10px;
        }

        .order-amount {
            font-weight: bold;
            white-space: nowrap;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 15px 0;
        }

        .total-box {
            font-size: 1.25rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            margin-top: 25px;
            border-top: 1px solid #eee;
            pt-2;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 100%;
                margin: 0;
            }

            .receipt-container::after {
                display: none;
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
            <h4>TABLE SALES REPORT</h4>
            <small>Generated on: <?= date('M d, Y h:i A') ?></small>
            <?php if ($fromDate && $toDate): ?>
                <div class="mt-1 small">
                    RANGE: <?= date('m/d/Y', strtotime($fromDate)) ?> - <?= date('m/d/Y', strtotime($toDate)) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="receipt-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $grandTotal += $row['total'];
                    ?>
                    <div class="order-entry">
                        <div class="order-info">
                            <strong>ORD #<?= $row['order_id'] ?></strong><br>
                            <small>Table: <?= htmlspecialchars($row['table_names']) ?></small><br>
                            <small class="text-muted"><?= date('M d, Y h:i A', strtotime($row['date_time'])) ?></small>
                        </div>
                        <div class="order-amount">
                            ₱<?= number_format($row['total'], 2) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center italic my-4">No transactions found.</p>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <div class="total-box">
            <span>GRAND TOTAL</span>
            <span>₱<?= number_format($grandTotal, 2) ?></span>
        </div>

        <div class="divider"></div>

        <div class="footer-text">
            <p>*** END OF SALES REPORT ***</p>
            <small>Keep this for your internal accounting records.</small>
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

            pdf.save(`table-sales-${new Date().toISOString().slice(0, 10)}.pdf`);


            setTimeout(() => {
                window.location.href = 'index.php?sales-report';
            }, 500);
        }
    </script>



</body>

</html>