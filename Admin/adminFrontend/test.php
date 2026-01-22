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

                .section-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 15px 0 10px;
                    border-bottom: 1px solid #333;
                }

                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                }

                .total-section {
                    margin-top: 20px;
                    border-top: 2px dashed #333;
                    padding-top: 15px;
                }

                .spinner {
                    border: 6px solid #f3f3f3;
                    border-top: 6px solid white;
                    border-radius: 50%;
                    width: 60px;
                    height: 60px;
                    animation: spin 1s linear infinite;
                    margin: auto;
                }

                @keyframes spin {
                    0% {
                        transform: rotate(0deg);
                    }

                    100% {
                        transform: rotate(360deg);
                    }
                }

                @keyframes fadein {
                    from {
                        opacity: 0;
                        transform: scale(.9);
                    }

                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
            </style>
        </head>

        <body>

            <div class="receipt-container">
                <div class="header">
                    <h1>Casa Estela Boutique Hotel & Cafe</h1>
                    <p>Gov B Marasigan St, Calapan City, Oriental Mindoro</p>
                    <p>0908 747 4892 | casaestelaboutiquehotelandcafe@gmail.com</p>
                    <p><strong>ACCEPTED EVENT BOOKING RECEIPT</strong></p>
                    <p>Reference: <?= htmlspecialchars($booking['booking_refId']) ?></p>
                    <p>Date Issued: <?= date("F j, Y") ?></p>
                </div>

                <div class="section-title">Customer Information</div>
                <div class="detail-row"><span
                        class="detail-label">Name:</span><span><?= htmlspecialchars($booking['customer_name']) ?></span></div>
                <div class="detail-row"><span
                        class="detail-label">Email:</span><span><?= htmlspecialchars($booking['email'] ?? 'N/A') ?></span></div>
                <div class="detail-row"><span
                        class="detail-label">Guests:</span><span><?= $booking['number_of_guests'] ?></span></div>

                <div class="section-title">Event Information</div>
                <div class="detail-row"><span class="detail-label">Event
                        Type:</span><span><?= htmlspecialchars($booking['event_type']) ?></span></div>
                <div class="detail-row"><span class="detail-label">Event
                        Place:</span><span><?= htmlspecialchars($booking['place'] ?? 'N/A') ?></span></div>
                <div class="detail-row"><span class="detail-label">Reservation
                        Type:</span><span><?= htmlspecialchars($booking['reserve_type']) ?></span></div>

                <div class="detail-row"><span class="detail-label">Start:</span>
                    <span><?= date("F j, Y h:i A", strtotime($booking['date_time_start'])) ?></span>
                </div>

                <div class="detail-row"><span class="detail-label">End:</span>
                    <span><?= date("F j, Y h:i A", strtotime($booking['date_time_end'])) ?></span>
                </div>

                <div class="section-title">Package Details</div>
                <div class="detail-row"><span
                        class="detail-label">Package:</span><span><?= htmlspecialchars($booking['package_name']) ?></span></div>
                <div class="detail-row"><span class="detail-label">Max
                        Guests:</span><span><?= $booking['max_guest'] ?? 'N/A' ?></span></div>
                <div class="detail-row"><span class="detail-label">Package
                        Price:</span><span>₱<?= number_format($booking['package_price'], 2) ?></span></div>

                <?php if ($booking['overtime_hours'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Overtime:</span>
                        <span><?= $booking['overtime_hours'] ?> hrs - ₱<?= number_format($booking['overtime_charge'], 2) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($booking['extra_guests'] > 0): ?>
                    <div class="detail-row">
                        <span class="detail-label">Extra Guests:</span>
                        <span><?= $booking['extra_guests'] ?> - ₱<?= number_format($booking['extra_guest_charge'], 2) ?></span>
                    </div>
                <?php endif; ?>

                <div class="total-section">
                    <div class="section-title">Payment Summary</div>
                    <div class="total-row"><span>Total
                            Amount:</span><span>₱<?= number_format($booking['total_amount'], 2) ?></span></div>
                    <div class="total-row"><span>Paid
                            Amount:</span><span>₱<?= number_format($booking['paid_amount'] ?? 0, 2) ?></span></div>
                    <div class="total-row grand-total"><span>Remaining Balance:</span>
                        <span>₱<?= number_format($booking['remaining_balance'] ?? 0, 2) ?></span>
                    </div>
                </div>

                <div class="footer">
                    <p>Thank you for booking your event with us!</p>
                </div>
            </div>

            <div style="text-align:center;margin-top:20px">
                <button id="saveReceiptBtn" style="padding:10px 20px">Accept this booking!</button>
            </div>

            <!-- OVERLAY -->
            <div id="loadingOverlay" style="
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:rgba(0,0,0,.6);
display:none;
justify-content:center;
align-items:center;
z-index:9999;
">

                <!-- LOADING -->
                <div id="loadingBox" style="text-align:center;color:white;">
                    <div class="spinner"></div>
                    <p>Please wait...</p>
                </div>

                <!-- SUCCESS -->
                <div id="successBox" style="
        display:none;
        background:#2ecc71;
        padding:30px;
        border-radius:10px;
        color:white;
        text-align:center;
        animation:fadein .4s;
    ">
                    <h3>Event booking accepted!</h3>
                    <button id="okBtn" style="
            background:white;
            color:#2ecc71;
            padding:10px 25px;
            border:none;
            border-radius:5px;
            font-size:18px;
            cursor:pointer;
        ">OK</button>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

            <script>
                document.addEventListener("DOMContentLoaded", () => {

                    const saveBtn = document.getElementById("saveReceiptBtn");
                    const overlay = document.getElementById("loadingOverlay");
                    const loadingBox = document.getElementById("loadingBox");
                    const successBox = document.getElementById("successBox");
                    const okBtn = document.getElementById("okBtn");

                    saveBtn.addEventListener("click", async () => {
                        saveBtn.disabled = true;
                        overlay.style.display = "flex";

                        const canvas = await html2canvas(document.querySelector(".receipt-container"), { scale: 2 });

                        await fetch("../Admin/adminBackend/event_accepted_save_receipt.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                booking_id: <?= $booking['id'] ?>,
                                image: canvas.toDataURL("image/png")
                            })
                        });

                        loadingBox.style.display = "none";
                        successBox.style.display = "block";
                    });

                    okBtn.addEventListener("click", () => {
                        overlay.style.display = "none";
                        window.location.href = "event_bookings.php"; // CHANGE IF NEEDED
                    });

                });
            </script>

        </body>

        </html>

        <?php
    }
}
?>