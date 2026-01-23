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
        b.booking_id,
        b.booking_reference,
        b.check_in,
        b.check_out,
        b.total_amount,
        GROUP_CONCAT(DISTINCT br.room_type_name SEPARATOR ', ') AS room_types,
        COUNT(DISTINCT br.id) AS booked_rooms,
        GROUP_CONCAT(
            DISTINCT CONCAT(gn.first_name, ' ', gn.last_name, ' (', gn.guest_type, ')')
            SEPARATOR '<br>'
        ) AS guests
    FROM bookings b
    LEFT JOIN booked_rooms br ON br.booking_id = b.booking_id
    LEFT JOIN guest_names gn ON gn.booking_id = b.booking_id
    WHERE b.status = 'finished'
";


if ($fromDate && $toDate) {
    $sql .= " AND (b.check_in BETWEEN '$fromDateTime' AND '$toDateTime' OR b.check_out BETWEEN '$fromDateTime' AND '$toDateTime')";
}

$sql .= " GROUP BY b.booking_id ORDER BY b.check_in DESC";
$result = mysqli_query($conn, $sql);

$totalRevenue = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Sales Receipt</title>
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
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid #ddd;
        }

        /* Serrated bottom edge */
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

        .booking-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .booking-details {
            flex: 1;
        }

        .booking-price {
            font-weight: bold;
            align-self: center;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 15px 0;
        }

        .grand-total {
            font-size: 1.3rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 30px;
            text-transform: uppercase;
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
                max-width: 100%;
                margin: 0;
            }

            .receipt-container::after {
                display: none;
            }
        }

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
            <h3 class="fw-bold mb-1">ROOM REPORT</h3>

            <p class="mb-0">Official Business Record</p>
            <small>
                <?php if ($fromDate && $toDate): ?>
                    Period:
                    <?= date('M d, Y', strtotime($fromDate)) ?> -
                    <?= date('M d, Y', strtotime($toDate)) ?>
                <?php else: ?>
                    All-Time-Record
                <?php endif; ?>
            </small>

        </div>

        <div class="receipt-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $totalRevenue += $row['total_amount'];
                    ?>
                    <div class="booking-row" style="align-items: flex-start;">
                        <div class="booking-details">
                            <strong>REF: <?= htmlspecialchars($row['booking_reference']) ?></strong><br>

                            <span>
                                <?= htmlspecialchars($row['room_types']) ?> (x<?= $row['booked_rooms'] ?>)
                            </span><br>

                            <small class="text-muted">
                                In: <?= date('m/d/y', strtotime($row['check_in'])) ?> |
                                Out: <?= date('m/d/y', strtotime($row['check_out'])) ?>
                            </small>

                            <?php if (!empty($row['guests'])): ?>
                                <div class="mt-2 pt-1" style="border-top: 1px dotted #eee;">
                                    <small class="text-muted"
                                        style="text-transform: uppercase; font-size: 10px; letter-spacing: 1px;">Guests:</small><br>
                                    <span style="font-size: 13px; line-height: 1.2;">
                                        <?= $row['guests'] ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="booking-price">
                            ₱ <?= number_format($row['total_amount'], 2) ?>
                        </div>
                    </div>
                    <div class="divider" style="opacity: 0.3; margin: 10px 0;"></div> <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center py-4">No completed bookings found.</p>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <div class="grand-total">
            <span>TOTAL REVENUE</span>
            <span>₱
                <?= number_format($totalRevenue, 2) ?>
            </span>
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

            pdf.save(`room-sales-${new Date().toISOString().slice(0, 10)}.pdf`);


            setTimeout(() => {
                window.location.href = 'index.php?sales-report';
            }, 500);
        }
    </script>


</body>

</html>