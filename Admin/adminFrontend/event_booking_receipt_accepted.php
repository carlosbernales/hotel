<?php
include 'adminBackend/mydb.php';

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);

    $sql = "SELECT * FROM event_bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Event Booking Receipt</title>
            <style>
                body {
                    font-family: 'Courier New', monospace;
                    background-color: #f5f5f5;
                    padding: 20px;
                }

                .receipt-container {
                    max-width: 700px;
                    margin: auto;
                    background: #fff;
                    padding: 30px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                .header {
                    text-align: center;
                    border-bottom: 2px dashed #333;
                    padding-bottom: 20px;
                    margin-bottom: 20px;
                }

                .header h1 {
                    font-size: 24px;
                    text-transform: uppercase;
                    margin-bottom: 5px;
                }

                .header p {
                    font-size: 12px;
                    margin: 3px 0;
                }

                .section-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 15px 0 10px;
                    border-bottom: 1px solid #333;
                    padding-bottom: 5px;
                }

                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                    font-size: 14px;
                }

                .detail-label {
                    font-weight: bold;
                }

                .total-section {
                    margin-top: 20px;
                    border-top: 2px dashed #333;
                    padding-top: 15px;
                }

                .total-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                    font-size: 15px;
                }

                .total-row.grand-total {
                    font-weight: bold;
                    font-size: 18px;
                    border-top: 2px solid #333;
                    padding-top: 10px;
                    margin-top: 5px;
                }

                .footer {
                    text-align: center;
                    margin-top: 30px;
                    font-size: 12px;
                    border-top: 2px dashed #333;
                    padding-top: 10px;
                }
            </style>
        </head>

        <body>
            <div class="receipt-container">
                <div class="header">
                    <h1>Casa Estela Boutique Hotel & Cafe</h1>
                    <p>Gov B Marasigan St, Calapan City, Oriental Mindoro</p>
                    <p>Phone: 0908 747 4892 | Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                    <p style="margin-top: 15px; font-size: 14px;"><strong>ACCEPTED EVENT BOOKING RECEIPT</strong></p>
                    <p>Reference: <?= htmlspecialchars($booking['booking_refId']) ?></p>
                    <p>Date Issued: <?= date("F j, Y") ?></p>
                </div>

                <div class="section-title">Customer Information</div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span><?= htmlspecialchars($booking['customer_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Number of Guests:</span>
                    <span><?= htmlspecialchars($booking['number_of_guests']) ?></span>
                </div>

                <div class="section-title">Event Information</div>
                <div class="detail-row">
                    <span class="detail-label">Event Type:</span>
                    <span><?= htmlspecialchars($booking['event_type']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Package:</span>
                    <span><?= htmlspecialchars($booking['package_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Package Price:</span>
                    <span>₱<?= number_format($booking['package_price'], 2) ?></span>
                </div>
                <?php if ($booking['overtime_hours'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Overtime (hours):</span>
                        <span><?= htmlspecialchars($booking['overtime_hours']) ?> -
                            ₱<?= number_format($booking['overtime_charge'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($booking['extra_guests'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Extra Guests:</span>
                        <span><?= htmlspecialchars($booking['extra_guests']) ?> -
                            ₱<?= number_format($booking['extra_guest_charge'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="detail-label">Event Start:</span>
                    <span><?= date("F j, Y, h:i A", strtotime($booking['date_time_start'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Event End:</span>
                    <span><?= date("F j, Y, h:i A", strtotime($booking['date_time_end'])) ?></span>
                </div>

                <!-- Payment Summary -->
                <div class="total-section">
                    <div class="section-title">Payment Summary</div>
                    <div class="total-row">
                        <span>Total Amount:</span>
                        <span>₱<?= number_format($booking['total_amount'], 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Paid Amount:</span>
                        <span>₱<?= number_format($booking['paid_amount'], 2) ?></span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Remaining Balance:</span>
                        <span>₱<?= number_format($booking['remaining_balance'], 2) ?></span>
                    </div>
                    <div class="detail-row" style="margin-top: 10px;">
                        <span class="detail-label">Payment Method:</span>
                        <span><?= htmlspecialchars($booking['payment_method']) ?></span>
                    </div>
                </div>

                <div class="footer">
                    <p>Thank you for booking your event with us!</p>
                    <p>For inquiries, please contact us at the details above.</p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <!-- Changed from <a> to <button> with id -->
                <button id="saveReceiptBtn" class="btn btn-gold-action" style="padding: 10px 20px;">
                    Accept this booking!
                </button>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
            <script>
                document.getElementById('saveReceiptBtn').addEventListener('click', async function () {
                    const btn = this;
                    btn.disabled = true;

                    const container = document.querySelector('.receipt-container');

                    try {
                        const canvas = await html2canvas(container, { scale: 2 });
                        const dataUrl = canvas.toDataURL('image/png');

                        const response = await fetch('../Admin/adminBackend/event_accepted_save_receipt.php', {
                            method: 'POST',
                            body: JSON.stringify({
                                booking_id: <?= $booking['id'] ?>,
                                image: dataUrl
                            }),
                            headers: { 'Content-Type': 'application/json' }
                        });

                        const msg = await response.text();
                        alert(msg);
                        window.location.href = "index.php?pending_event_bookings";
                    } catch (err) {
                        console.error(err);
                        alert('Failed to save/email receipt.');
                        btn.disabled = false;
                    }
                });
            </script>



        </body>

        </html>
        <?php
    } else {
        echo "<h3>No event booking found with ID $event_id</h3>";
    }
} else {
    echo "<h3>No Event ID provided.</h3>";
}
?>