<?php
include 'adminBackend/mydb.php';

if (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);

    $sql = "SELECT * FROM bookings WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
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
            <title>Booking Receipt</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Courier New', monospace;
                    background-color: #f5f5f5;
                    padding: 20px;
                }

                .receipt-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background-color: white;
                    padding: 40px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                .header {
                    text-align: center;
                    border-bottom: 2px dashed #333;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }

                .header h1 {
                    font-size: 24px;
                    margin-bottom: 5px;
                    text-transform: uppercase;
                }

                .header p {
                    font-size: 12px;
                    margin: 3px 0;
                }

                .section-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 20px 0 10px 0;
                    text-transform: uppercase;
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

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                    font-size: 13px;
                }

                table th {
                    text-align: left;
                    border-bottom: 1px solid #333;
                    padding: 8px 5px;
                    font-weight: bold;
                }

                table td {
                    padding: 8px 5px;
                    border-bottom: 1px dotted #ccc;
                }

                .total-section {
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 2px dashed #333;
                }

                .total-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    font-size: 15px;
                }

                .total-row.grand-total {
                    font-size: 18px;
                    font-weight: bold;
                    border-top: 2px solid #333;
                    padding-top: 15px;
                    margin-top: 10px;
                }

                .footer {
                    text-align: center;
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px dashed #333;
                    font-size: 12px;
                }

                .no-data {
                    font-style: italic;
                    color: #666;
                    font-size: 13px;
                }
            </style>
        </head>

        <body>
            <div class="receipt-container">
                <div class="header">
                    <h1>Casa Estela Boutique Hotel & Cafe</h1>
                    <p>Gov B Marasigan St, Calapan City, Oriental Mindoro</p>
                    <p>Phone: 0908 747 4892 | Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                    <p style="margin-top: 15px; font-size: 14px;"><strong>BOOKING RECEIPT</strong></p>
                    <p>Reference: <?= htmlspecialchars($booking['booking_reference']) ?></p>
                    <p>Date Issued: <?= date("F j, Y") ?></p>
                </div>

                <div class="section-title">Guest Information</div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span><?= htmlspecialchars($booking['email']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Contact:</span>
                    <span><?= htmlspecialchars($booking['contact']) ?></span>
                </div>

                <!-- Booking Information -->
                <div class="section-title">Booking Information</div>

                <?php
                /* ============================
                   FETCH ORIGINAL CHECK-IN/OUT
                ============================ */
                $stmt = $conn->prepare("SELECT check_in, check_out FROM booking_check_inout WHERE booking_fk_id = ?");
                $stmt->bind_param("i", $booking_id);
                $stmt->execute();
                $res = $stmt->get_result();

                $original_check_in = $original_check_out = null;
                if ($res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $original_check_in = $row['check_in'];
                    $original_check_out = $row['check_out'];
                }

                ///  FETCH LATEST RESCHEDULE
                $stmt = $conn->prepare("
                    SELECT check_in, check_out 
                    FROM reschedule_bookings 
                    WHERE booking_fk_id = ? 
                    ORDER BY date_resched DESC 
                    LIMIT 1
                ");
                $stmt->bind_param("i", $booking_id);
                $stmt->execute();
                $res = $stmt->get_result();

                $latest_resched_check_in = $latest_resched_check_out = null;
                if ($res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $latest_resched_check_in = $row['check_in'];
                    $latest_resched_check_out = $row['check_out'];
                }

                /////   NORMALIZE DATES FOR COMPARISON
                $actualCheckIn = date('Y-m-d', strtotime($booking['check_in']));
                $actualCheckOut = date('Y-m-d', strtotime($booking['check_out']));

                $bookedCheckIn = $original_check_in ? date('Y-m-d', strtotime($original_check_in)) : null;
                $bookedCheckOut = $original_check_out ? date('Y-m-d', strtotime($original_check_out)) : null;

                $reschedCheckIn = $latest_resched_check_in ? date('Y-m-d', strtotime($latest_resched_check_in)) : null;
                $reschedCheckOut = $latest_resched_check_out ? date('Y-m-d', strtotime($latest_resched_check_out)) : null;
                ?>

                <!-- SHOW BOOKED DATES ONLY IF DIFFERENT -->
                <?php if ($bookedCheckIn && $bookedCheckIn !== $actualCheckIn): ?>
                    <div class="detail-row">
                        <span class="detail-label">Booked Check-in:</span>
                        <span><?= date("F j, Y", strtotime($bookedCheckIn)) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($bookedCheckOut && $bookedCheckOut !== $actualCheckOut): ?>
                    <div class="detail-row">
                        <span class="detail-label">Booked Check-out:</span>
                        <span><?= date("F j, Y", strtotime($bookedCheckOut)) ?></span>
                    </div>
                <?php endif; ?>

                <!-- SHOW RESCHEDULE ONLY IF DIFFERENT -->
                <?php if ($reschedCheckIn && $reschedCheckIn !== $actualCheckIn): ?>
                    <div class="detail-row" style="margin-top:10px;">
                        <span class="detail-label">Reschedule Check-in:</span>
                        <span><?= date("F j, Y", strtotime($reschedCheckIn)) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($reschedCheckOut && $reschedCheckOut !== $actualCheckOut): ?>
                    <div class="detail-row">
                        <span class="detail-label">Reschedule Check-out:</span>
                        <span><?= date("F j, Y", strtotime($reschedCheckOut)) ?></span>
                    </div>
                <?php endif; ?>

                <!-- FINAL CHECK-IN / CHECK-OUT (ALWAYS SHOW) -->
                <div class="detail-row">
                    <span class="detail-label">Check-in:</span>
                    <span>
                        <?= date("F j, Y", strtotime($booking['check_in'])) ?>
                        <?php
                        if ($bookedCheckIn && $actualCheckIn < $bookedCheckIn) {
                            echo "<strong style='color: green;'> (Advance Check-in)</strong>";
                        }
                        ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-out:</span>
                    <span>
                        <?= date("F j, Y", strtotime($booking['check_out'])) ?>
                        <?php
                        if ($bookedCheckOut && $actualCheckOut > $bookedCheckOut) {
                            echo "<strong style='color: orange;'> (Extended Booking)</strong>";
                        }
                        ?>
                    </span>
                </div>


                <div class="detail-row">
                    <span class="detail-label">Number of Nights:</span>
                    <span><?= $booking['nights'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Number of Guests:</span>
                    <span><?= $booking['number_of_guests'] ?> (<?= $booking['num_adults'] ?> Adults,
                        <?= $booking['num_children'] ?> Children)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Room Quantity:</span>
                    <span><?= $booking['room_quantity'] ?></span>
                </div>

                <!-- Booked Rooms -->
                <div class="section-title">Booked Rooms</div>
                <?php
                $rooms_sql = "SELECT room_type_name, price FROM booked_rooms WHERE booking_id = ?";
                $stmt_rooms = $conn->prepare($rooms_sql);
                $stmt_rooms->bind_param("i", $booking_id);
                $stmt_rooms->execute();
                $rooms_result = $stmt_rooms->get_result();

                if ($rooms_result->num_rows > 0) {
                    echo "<table>
                            <tr>
                                <th>Room Type</th>
                                <th style='text-align: right;'>Price</th>
                            </tr>";
                    while ($room = $rooms_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($room['room_type_name']) . "</td>
                                <td style='text-align: right;'>₱" . number_format($room['price'], 2) . "</td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='no-data'>No booked rooms for this booking.</p>";
                }

                // Amenities
                $amenities_sql = "SELECT amenity_name, quantity, price FROM booking_amenities WHERE booking_fk_id = ?";
                $stmt2 = $conn->prepare($amenities_sql);
                $stmt2->bind_param("i", $booking_id);
                $stmt2->execute();
                $amenities_result = $stmt2->get_result();

                echo "<div class='section-title'>Amenities</div>";
                if ($amenities_result->num_rows > 0) {
                    echo "<table>
                            <tr>
                                <th>Amenity Name</th>
                                <th>Quantity</th>
                                <th style='text-align: right;'>Price</th>
                            </tr>";
                    while ($amenity = $amenities_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($amenity['amenity_name']) . "</td>
                                <td>" . $amenity['quantity'] . "</td>
                                <td style='text-align: right;'>₱" . number_format($amenity['price'], 2) . "</td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='no-data'>No amenities added for this booking.</p>";
                }

                // Room Transfers
                $transfers_sql = "SELECT room_type_name, price, transfer_date, reason FROM room_transfers WHERE bookings_fk_id = ?";
                $stmt3 = $conn->prepare($transfers_sql);
                $stmt3->bind_param("i", $booking_id);
                $stmt3->execute();
                $transfers_result = $stmt3->get_result();

                if ($transfers_result->num_rows > 0) {
                    echo "<div class='section-title'>Room Transfers</div>";
                    echo "<table>
                            <tr>
                                <th>Room Type</th>
                                <th>Transfer Date</th>
                                <th>Reason</th>
                                <th style='text-align: right;'>Price</th>
                            </tr>";
                    while ($transfer = $transfers_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($transfer['room_type_name']) . "</td>
                                <td>" . date("F j, Y", strtotime($transfer['transfer_date'])) . "</td>
                                <td>" . htmlspecialchars($transfer['reason']) . "</td>
                                <td style='text-align: right;'>₱" . number_format($transfer['price'], 2) . "</td>
                              </tr>";
                    }
                    echo "</table>";
                }

                // Guest Names
                $guests_sql = "SELECT first_name, last_name, guest_type FROM guest_names WHERE booking_id = ?";
                $stmt4 = $conn->prepare($guests_sql);
                $stmt4->bind_param("i", $booking_id);
                $stmt4->execute();
                $guests_result = $stmt4->get_result();

                if ($guests_result->num_rows > 0) {
                    echo "<div class='section-title'>Guest List</div>";
                    echo "<table>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Guest Type</th>
                            </tr>";
                    while ($guest = $guests_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($guest['first_name']) . "</td>
                                <td>" . htmlspecialchars($guest['last_name']) . "</td>
                                <td>" . htmlspecialchars($guest['guest_type']) . "</td>
                              </tr>";
                    }
                    echo "</table>";
                }

                // Get original check-in/out from booking_check_inout
                $original_sql = "SELECT check_in, check_out FROM booking_check_inout WHERE booking_fk_id = ?";
                $stmt_orig = $conn->prepare($original_sql);
                $stmt_orig->bind_param("i", $booking_id);
                $stmt_orig->execute();
                $orig_result = $stmt_orig->get_result();

                if ($orig_result->num_rows > 0) {
                    $orig = $orig_result->fetch_assoc();
                    $original_check_in = date("F j, Y", strtotime($orig['check_in']));
                    $original_check_out = date("F j, Y", strtotime($orig['check_out']));
                }

                // Reschedule Details
                $reschedule_sql = "SELECT check_in, check_out, date_resched, reason FROM reschedule_bookings WHERE booking_fk_id = ?";
                $stmt5 = $conn->prepare($reschedule_sql);
                $stmt5->bind_param("i", $booking_id);
                $stmt5->execute();
                $reschedule_result = $stmt5->get_result();

                if ($reschedule_result->num_rows > 0) {
                    echo "<div class='section-title'>Reschedule History</div>";
                    while ($resched = $reschedule_result->fetch_assoc()) {
                        $resched_date = date("F j, Y", strtotime($resched['date_resched']));
                        $new_check_in = date("F j, Y", strtotime($resched['check_in']));
                        $new_check_out = date("F j, Y", strtotime($resched['check_out']));
                        $reason = htmlspecialchars($resched['reason']);

                        echo "<p style='font-size: 13px; line-height: 1.6; margin: 10px 0;'>
                            Original booking: <strong>$original_check_in</strong> to <strong>$original_check_out</strong><br>
                            Rescheduled on: <strong>$resched_date</strong><br>
                            New dates: <strong>$new_check_in</strong> to <strong>$new_check_out</strong><br>
                            Reason: <em>$reason</em>
                        </p>";
                    }
                }

                ?>

                <!-- Payment Summary -->
                <div class="total-section">
                    <div class="section-title">Payment Summary</div>
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>₱<?= number_format($booking['total_amount'] + $booking['discount_amount'], 2) ?></span>
                    </div>
                    <?php if ($booking['discount_percentage'] > 0): ?>
                        <div class="total-row">
                            <span>Discount (<?= $booking['discount_percentage'] ?>%):</span>
                            <span>-₱<?= number_format($booking['discount_amount'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="total-row grand-total">
                        <span>TOTAL AMOUNT:</span>
                        <span>₱<?= number_format($booking['total_amount'], 2) ?></span>
                    </div>
                    <div class="total-row" style="font-weight: bold;">
                        <span>Remaining Balance:</span>
                        <span>₱<?= number_format($booking['remaining_balance'], 2) ?></span>
                    </div>
                    <div class="detail-row" style="margin-top: 15px;">
                        <span class="detail-label">Payment Method:</span>
                        <span><?= htmlspecialchars($booking['payment_method']) ?></span>
                    </div>
                </div>

                <div class="footer">
                    <p>Thank you for your booking!</p>
                    <p>For inquiries, please contact us at</p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button id="saveReceiptBtn" style="padding: 10px 20px;">Complete this booking!</button>
            </div>

            <div id="loadingOverlay" style="
                    position: fixed;
                    top: 0; left: 0; 
                    width: 100%; height: 100%;
                    background: rgba(0,0,0,0.6);
                    display: none;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                ">
                <div id="loadingBox" style="text-align:center; color: white; font-size: 22px;">
                    <div class="spinner" style="
                            border: 6px solid #f3f3f3;
                            border-top: 6px solid #ffffff;
                            border-radius: 50%;
                            width: 60px;
                            height: 60px;
                            animation: spin 1s linear infinite;
                            margin: auto;
                            margin-bottom: 20px;
                        ">
                    </div>
                    <div id="loadingText">Please wait... processing your request</div>
                </div>

                <div id="successBox" style="
                            display:none;
                            background:#2ecc71;
                            padding:25px 40px;
                            border-radius:10px;
                            color:white;
                            text-align:center;
                            font-size:22px;
                            animation: fadein 0.4s ease-out;
                        ">
                    <div style="font-size:26px; font-weight:bold; margin-bottom:10px;">
                        Request done!
                    </div>
                    <button id="okBtn" style="
                        background:white;
                        color:#2ecc71;
                        padding:10px 25px;
                        border:none;
                        border-radius:5px;
                        font-size:18px;
                        cursor:pointer;
                        margin-top:15px;
                    ">OK</button>
                </div>
            </div>

            <style>
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
                        transform: scale(0.9);
                    }

                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
            </style>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

            <script>
                document.getElementById('saveReceiptBtn').addEventListener('click', async function () {
                    const btn = this;
                    const overlay = document.getElementById("loadingOverlay");
                    const loadingBox = document.getElementById("loadingBox");
                    const successBox = document.getElementById("successBox");

                    overlay.style.display = "flex";
                    btn.disabled = true;

                    const container = document.querySelector('.receipt-container');
                    const canvas = await html2canvas(container, { scale: 2 });
                    const dataUrl = canvas.toDataURL('image/png');

                    fetch('../Admin/adminBackend/save_receipt_image.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            booking_id: <?= $booking_id ?>,
                            image: dataUrl,
                        }),
                        headers: { 'Content-Type': 'application/json' }
                    })
                        .then(res => res.text())
                        .then(msg => {
                            console.log(msg);

                            loadingBox.style.display = "none";

                            successBox.style.display = "block";
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Failed to save/email receipt.');
                            overlay.style.display = "none";
                            btn.disabled = false;
                        });
                });

                document.addEventListener('click', function (e) {
                    if (e.target.id === "okBtn") {
                        window.location.href = "../Admin/index.php?room_booking_list";
                    }
                });
            </script>



        </html>
        <?php
    } else {
        echo "<h3>No booking found with ID $booking_id</h3>";
    }

} else {
    echo "<h3>No Booking ID provided.</h3>";
}
?>