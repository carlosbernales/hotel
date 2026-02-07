<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$bookings = [];

$sqlBookings = "SELECT * FROM bookings WHERE status = 'finished'";
$resBookings = $conn->query($sqlBookings);

while ($row = $resBookings->fetch_assoc()) {
    $id = $row['booking_id'];
    $bookings[$id] = [
        "booking" => $row,
        "rooms" => [],
        "guests" => []
    ];
}


$sqlRooms = "
    SELECT 
        br.booking_id,
        br.room_type_id,
        br.room_type_name,
        br.price,
        br.room_number_fk_id,
        rn.room_number,
        rn.floor_number
    FROM booked_rooms br
    LEFT JOIN room_numbers rn 
        ON br.room_number_fk_id = rn.room_number_id
";
$resRooms = $conn->query($sqlRooms);

while ($r = $resRooms->fetch_assoc()) {
    $id = $r['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['rooms'][] = $r;
    }
}

$sqlGuests = "
    SELECT booking_id, first_name, last_name, guest_type
    FROM guest_names
";
$resGuests = $conn->query($sqlGuests);

while ($g = $resGuests->fetch_assoc()) {
    $id = $g['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['guests'][] = $g;
    }
}
?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Finished Room Bookings</i>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Fullname</th>
                        <th>Contact</th>
                        <th>Schedule</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $id => $data): ?>
                        <?php $b = $data['booking']; ?>
                        <tr>
                            <td><?= $b['booking_reference'] ?></td>
                            <td><?= $b['first_name'] . ' ' . $b['last_name'] ?></td>
                            <td><?= $b['contact'] ?></td>
                            <td>
                                <?= date("F j, Y", strtotime($b['check_in'])) ?> -
                                <?= date("F j, Y", strtotime($b['check_out'])) ?>
                            </td>


                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#viewModal<?= $id ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#receiptModal<?= $id ?>">
                                    <i class="bi bi-receipt"></i> Receipt
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
<style>
    /* ===== RECEIPT BASE STYLE ===== */
    .receipt-modal,
    .receipt-print {
        font-family: 'Courier New', monospace;
        color: #000;
    }

    .receipt-container {
        max-width: 800px;
        margin: auto;
        background: #fff;
        padding: 40px;
    }

    /* Header */
    .receipt-container .header {
        text-align: center;
        border-bottom: 2px dashed #333;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .receipt-container .header h1 {
        font-size: 24px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .receipt-container .header p {
        font-size: 12px;
        margin: 3px 0;
    }

    /* Sections */
    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin: 25px 0 10px;
        border-bottom: 1px solid #333;
        padding-bottom: 4px;
        text-transform: uppercase;
    }

    /* Rows */
    .detail-row {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        padding: 4px 0;
    }

    .detail-label {
        font-weight: bold;
    }

    /* Tables */
    .receipt-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 10px;
    }

    .receipt-container th {
        border-bottom: 1px solid #333;
        text-align: left;
        padding: 6px;
    }

    .receipt-container td {
        border-bottom: 1px dotted #ccc;
        padding: 6px;
    }

    /* Totals */
    .total-section {
        margin-top: 30px;
        border-top: 2px dashed #333;
        padding-top: 15px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 15px;
        padding: 6px 0;
    }

    .total-row.grand-total {
        font-size: 18px;
        font-weight: bold;
        border-top: 2px solid #000;
        padding-top: 10px;
    }

    /* Footer */
    .footer {
        text-align: center;
        font-size: 12px;
        margin-top: 30px;
        border-top: 2px dashed #333;
        padding-top: 15px;
    }

    /* ===== PRINT FIX ===== */
    @media print {
        body {
            margin: 0;
        }

        .modal,
        .modal-header,
        .btn,
        .btn-close {
            display: none !important;
        }

        .receipt-container {
            padding: 0;
            box-shadow: none;
        }
    }
</style>


<?php foreach ($bookings as $id => $data): ?>
    <?php $booking = $data['booking']; ?>
    <div class="modal fade" id="receiptModal<?= $id ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="width: auto; max-width: 800px;">
            <div class="modal-content receipt-modal">
                <div class="modal-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold m-0">
                        <i class="bi bi-receipt"></i> Booking Receipt – <?= $booking['booking_reference'] ?>
                    </h5>

                    <div class="d-flex gap-2">
                        <!-- PRINT BUTTON -->
                        <button type="button" class="btn btn-light btn-sm"
                            onclick="printReceipt('receipt-content-<?= $id ?>')">
                            <i class="bi bi-printer"></i> Print
                        </button>

                        <!-- CLOSE BUTTON -->
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="receipt-container" id="receipt-content-<?= $id ?>">

                        <!-- HEADER -->
                        <div class="header">
                            <h1>Casa Estela Boutique Hotel & Cafe</h1>
                            <p>Gov B Marasigan St, Calapan City, Oriental Mindoro</p>
                            <p>Phone: 0908 747 4892 | Email: casaestelaboutiquehotelandcafe@gmail.com</p>
                            <p style="margin-top: 15px; font-size: 14px;"><strong>BOOKING RECEIPT</strong></p>
                            <p>Reference: <?= htmlspecialchars($booking['booking_reference']) ?></p>
                            <p>Date Issued: <?= date("F j, Y") ?></p>
                        </div>

                        <!-- GUEST INFORMATION -->
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

                        <!-- BOOKING INFORMATION -->
                        <div class="section-title">Booking Information</div>
                        <?php
                        $stmt = $conn->prepare("SELECT check_in, check_out FROM booking_check_inout WHERE booking_fk_id = ?");
                        $stmt->bind_param("i", $booking['booking_id']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $original_check_in = $original_check_out = null;
                        if ($res->num_rows > 0) {
                            $row = $res->fetch_assoc();
                            $original_check_in = $row['check_in'];
                            $original_check_out = $row['check_out'];
                        }

                        // Latest reschedule
                        $stmt = $conn->prepare("SELECT check_in, check_out FROM reschedule_bookings WHERE booking_fk_id = ? ORDER BY date_resched DESC LIMIT 1");
                        $stmt->bind_param("i", $booking['booking_id']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $reschedCheckIn = $reschedCheckOut = null;
                        if ($res->num_rows > 0) {
                            $row = $res->fetch_assoc();
                            $reschedCheckIn = $row['check_in'];
                            $reschedCheckOut = $row['check_out'];
                        }

                        $actualCheckIn = date('Y-m-d', strtotime($booking['check_in']));
                        $actualCheckOut = date('Y-m-d', strtotime($booking['check_out']));
                        $bookedCheckIn = $original_check_in ? date('Y-m-d', strtotime($original_check_in)) : null;
                        $bookedCheckOut = $original_check_out ? date('Y-m-d', strtotime($original_check_out)) : null;
                        ?>

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
                        <?php if ($reschedCheckIn && $reschedCheckIn !== $actualCheckIn): ?>
                            <div class="detail-row">
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

                        <div class="detail-row">
                            <span class="detail-label">Check-in:</span>
                            <span>
                                <?= date("F j, Y", strtotime($booking['check_in'])) ?>
                                <?php if ($bookedCheckIn && $actualCheckIn < $bookedCheckIn)
                                    echo "<strong style='color:green;'>(Advance Check-in)</strong>"; ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Check-out:</span>
                            <span>
                                <?= date("F j, Y", strtotime($booking['check_out'])) ?>
                                <?php if ($bookedCheckOut && $actualCheckOut > $bookedCheckOut)
                                    echo "<strong style='color:orange;'>(Extended Booking)</strong>"; ?>
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

                        <!-- BOOKED ROOMS -->
                        <div class="section-title">Booked Rooms</div>
                        <?php if (!empty($data['rooms'])): ?>
                            <table>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Room Number</th>
                                    <th>Floor</th>
                                    <th style="text-align:right;">Price</th>
                                </tr>
                                <?php foreach ($data['rooms'] as $room): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($room['room_type_name']) ?></td>
                                        <td><?= htmlspecialchars($room['room_number'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($room['floor_number'] ?? 'N/A') ?></td>
                                        <td style="text-align:right;">₱<?= number_format($room['price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <p class="no-data">No rooms booked</p>
                        <?php endif; ?>

                        <!-- AMENITIES -->
                        <div class="section-title">Amenities</div>
                        <?php if (!empty($data['amenities'])): ?>
                            <table>
                                <tr>
                                    <th>Amenity</th>
                                    <th>Quantity</th>
                                    <th style="text-align:right;">Price</th>
                                </tr>
                                <?php foreach ($data['amenities'] as $amenity): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($amenity['amenity_name']) ?></td>
                                        <td><?= $amenity['quantity'] ?></td>
                                        <td style="text-align:right;">₱<?= number_format($amenity['price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <p class="no-data">No amenities added</p>
                        <?php endif; ?>

                        <!-- ROOM TRANSFERS -->
                        <?php if (!empty($data['transfers'])): ?>
                            <div class="section-title">Room Transfers</div>
                            <table>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Transfer Date</th>
                                    <th>Reason</th>
                                    <th style="text-align:right;">Price</th>
                                </tr>
                                <?php foreach ($data['transfers'] as $transfer): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($transfer['room_type_name']) ?></td>
                                        <td><?= date("F j, Y", strtotime($transfer['transfer_date'])) ?></td>
                                        <td><?= htmlspecialchars($transfer['reason']) ?></td>
                                        <td style="text-align:right;">₱<?= number_format($transfer['price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>

                        <!-- GUEST LIST -->
                        <div class="section-title">Guest List</div>
                        <?php if (!empty($data['guests'])): ?>
                            <table>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Guest Type</th>
                                </tr>
                                <?php foreach ($data['guests'] as $guest): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($guest['first_name']) ?></td>
                                        <td><?= htmlspecialchars($guest['last_name']) ?></td>
                                        <td><?= htmlspecialchars($guest['guest_type']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <p class="no-data">No guests added</p>
                        <?php endif; ?>

                        <!-- RESCHEDULE HISTORY -->
                        <?php if (!empty($data['reschedules'])): ?>
                            <div class="section-title">Reschedule History</div>
                            <?php foreach ($data['reschedules'] as $resched): ?>
                                <p style="font-size:13px; line-height:1.6; margin:10px 0;">
                                    Rescheduled on <strong><?= date("F j, Y", strtotime($resched['date_resched'])) ?></strong>:
                                    from <?= date("F j, Y", strtotime($resched['check_in'])) ?> -
                                    <?= date("F j, Y", strtotime($resched['check_out'])) ?>.
                                    Reason: <em><?= htmlspecialchars($resched['reason']) ?></em>
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- PAYMENT SUMMARY -->
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
                                <span>Total Amount:</span>
                                <span>₱<?= number_format($booking['total_amount'], 2) ?></span>
                            </div>
                            <div class="total-row" style="font-weight:bold;">
                                <span>Remaining Balance:</span>
                                <span>₱<?= number_format($booking['remaining_balance'], 2) ?></span>
                            </div>
                            <div class="detail-row" style="margin-top:15px;">
                                <span class="detail-label">Payment Method:</span>
                                <span><?= htmlspecialchars($booking['payment_method']) ?></span>
                            </div>
                        </div>

                        <div class="footer">
                            <p>Thank you for your booking!</p>
                            <p>For inquiries, please contact us at casaestelaboutiquehotelandcafe@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>



<?php
function getRoomInfo($conn, $room_number_fk_id)
{
    if (!$room_number_fk_id)
        return null;
    $sql = "SELECT room_number, floor_number FROM room_numbers WHERE room_number_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_number_fk_id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0 ? $res->fetch_assoc() : null;
}
?>

<?php foreach ($bookings as $id => $data): ?>
    <?php $b = $data['booking']; ?>

    <?php
    // Fetch transfer rooms for this booking
    $transfers_res = null;
    if (!empty($b['booking_id'])) {
        $stmt = $conn->prepare("SELECT * FROM room_transfers WHERE bookings_fk_id = ? ORDER BY transfer_date ASC");
        $stmt->bind_param("i", $b['booking_id']);
        $stmt->execute();
        $transfers_res = $stmt->get_result();
    }
    ?>

    <!-- MODAL HERE -->
    <div class="modal fade" id="viewModal<?= $id ?>" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-file-text"></i> Booking Details – <?= $b['booking_reference'] ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    <!-- GUEST INFORMATION -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-dark text-warning">
                            <h6 class="mb-0"><i class="bi bi-person-fill"></i> Guest Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="text-muted small">First Name</label>
                                    <p class="mb-0 fw-semibold"><?= $b['first_name'] ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Last Name</label>
                                    <p class="mb-0 fw-semibold"><?= $b['last_name'] ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Email</label>
                                    <p class="mb-0 fw-semibold"><?= $b['email'] ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Contact</label>
                                    <p class="mb-0 fw-semibold"><?= $b['contact'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOOKING DETAILS -->
                    <!-- BOOKING DETAILS -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-dark text-warning">
                            <h6 class="mb-0"><i class="bi bi-calendar-check"></i> Booking Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <?php
                                // Fetch original check-in/out from booking_check_inout
                                $original_check_in = $original_check_out = null;
                                if (!empty($b['booking_id'])) {
                                    $stmt_chk = $conn->prepare("SELECT check_in, check_out FROM booking_check_inout WHERE booking_fk_id = ?");
                                    $stmt_chk->bind_param("i", $b['booking_id']);
                                    $stmt_chk->execute();
                                    $chk_res = $stmt_chk->get_result();
                                    if ($chk_res->num_rows > 0) {
                                        $chk_row = $chk_res->fetch_assoc();
                                        $original_check_in = $chk_row['check_in'];
                                        $original_check_out = $chk_row['check_out'];
                                    }
                                }

                                $current_check_in = $b['check_in'];
                                $current_check_out = $b['check_out'];
                                ?>

                                <div class="col-md-3">
                                    <label class="text-muted small">Booked Check-in</label>
                                    <p class="mb-0 fw-semibold">
                                        <?= $original_check_in ? date("M d, Y", strtotime($original_check_in)) : 'N/A' ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Booked Check-out</label>
                                    <p class="mb-0 fw-semibold">
                                        <?= $original_check_out ? date("M d, Y", strtotime($original_check_out)) : 'N/A' ?>
                                    </p>
                                </div>

                                <div class="col-md-3">
                                    <label class="text-muted small"> Check-in</label>
                                    <p class="mb-0 fw-semibold">
                                        <?= date("M d, Y", strtotime($current_check_in)) ?>
                                        <?php if ($original_check_in && strtotime($current_check_in) < strtotime($original_check_in)): ?>
                                            <span class="text-success fw-bold">(Advance Check-in)</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="col-md-3">
                                    <label class="text-muted small"> Check-out</label>
                                    <p class="mb-0 fw-semibold">
                                        <?= date("M d, Y", strtotime($current_check_out)) ?>
                                        <?php if ($original_check_out && strtotime($current_check_out) > strtotime($original_check_out)): ?>
                                            <span class="text-warning fw-bold">(Extended Booking)</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="col-md-2">
                                    <label class="text-muted small">Nights</label>
                                    <p class="mb-0 fw-semibold"><?= $b['nights'] ?></p>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-muted small">Adults</label>
                                    <p class="mb-0 fw-semibold"><?= $b['num_adults'] ?></p>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-muted small">Children</label>
                                    <p class="mb-0 fw-semibold"><?= $b['num_children'] ?></p>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-muted small">Total Guests</label>
                                    <p class="mb-0 fw-semibold"><?= $b['number_of_guests'] ?></p>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-muted small">Room Quantity</label>
                                    <p class="mb-0 fw-semibold"><?= $b['room_quantity'] ?></p>
                                </div>
                                <div class="col-md-2">
                                    <label class="text-muted small">Payment Method</label>
                                    <p class="mb-0 fw-semibold"><?= $b['payment_method'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT INFORMATION -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-dark text-warning">
                            <h6 class="mb-0"><i class="bi bi-cash-stack"></i> Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="text-muted small">Total Amount</label>
                                    <p class="mb-0 fw-bold text-success">
                                        ₱<?= number_format($b['total_amount'], 2) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Downpayment</label>
                                    <p class="mb-0 fw-semibold">
                                        ₱<?= number_format($b['downpayment_amount'], 2) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Remaining Balance</label>
                                    <p class="mb-0 fw-bold text-danger">
                                        ₱<?= number_format($b['remaining_balance'], 2) ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small">Discount</label>
                                    <p class="mb-0 fw-semibold">₱<?= number_format($b['discount_amount'], 2) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DISCOUNT INFORMATION -->
                    <?php if ($b['discount_type'] != 'None' && $b['discount_percentage'] > 0): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-dark text-warning">
                                <h6 class="mb-0"><i class="bi bi-percent"></i> Discount Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="text-muted small">Discount Type</label>
                                        <p class="mb-0 fw-semibold"><?= $b['discount_type'] ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-muted small">Discount Percentage</label>
                                        <p class="mb-0 fw-semibold"><?= $b['discount_percentage'] ?>%</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-muted small">Discount Amount</label>
                                        <p class="mb-0 fw-semibold text-success">
                                            ₱<?= number_format($b['discount_amount'], 2) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($transfers_res && $transfers_res->num_rows > 0): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-dark text-warning">
                                <h6 class="mb-0"><i class="fas fa-bed"></i> Transfer Rooms (Old Rooms)</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Room Type</th>
                                                <th>Room</th>
                                                <th>Floor</th>
                                                <th>Price</th>
                                                <th>Transfer Date</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($t = $transfers_res->fetch_assoc()): ?>
                                                <?php
                                                $roomInfo = getRoomInfo($conn, $t['room_number_fk_id']);
                                                $roomNumber = $roomInfo['room_number'] ?? '-';
                                                $floorNumber = $roomInfo['floor_number'] ?? '-';
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($t['room_type_name']) ?></td>
                                                    <td><?= htmlspecialchars($roomNumber) ?></td>
                                                    <td><?= htmlspecialchars($floorNumber) ?></td>
                                                    <td>₱<?= number_format($t['price'], 2) ?></td>
                                                    <td><?= date("F j, Y g:i A", strtotime($t['transfer_date'])) ?></td>
                                                    <td><?= htmlspecialchars($t['reason']) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-dark text-warning">
                            <h6 class="mb-0"><i class="bi bi-door-open"></i> Amenities</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Amenity</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($b['booking_id'])) {
                                            $stmt = $conn->prepare("
                                                            SELECT amenity_name, quantity, price
                                                            FROM booking_amenities
                                                            WHERE booking_fk_id = ?
                                                        ");
                                            $stmt->bind_param("i", $b['booking_id']);
                                            $stmt->execute();
                                            $amenities_res = $stmt->get_result();

                                            if ($amenities_res->num_rows > 0) {
                                                while ($amenity = $amenities_res->fetch_assoc()) {
                                                    echo "<tr>
                                                                        <td>" . htmlspecialchars($amenity['amenity_name']) . "</td>
                                                                        <td>" . htmlspecialchars($amenity['quantity']) . "</td>
                                                                        <td>₱" . number_format($amenity['price'], 2) . "</td>
                                                                    </tr>";
                                                }
                                            } else {
                                                echo '<tr><td colspan="3" class="text-center text-muted py-3">No amenities found</td></tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <?php
                    // Fetch reschedules safely
                    $resched_res = null;
                    if (!empty($b['booking_id'])) {
                        $stmt = $conn->prepare("SELECT * FROM reschedule_bookings WHERE booking_fk_id = ? ORDER BY date_resched ASC");
                        $stmt->bind_param("i", $b['booking_id']);
                        $stmt->execute();
                        $resched_res = $stmt->get_result();
                    }

                    if ($resched_res && $resched_res->num_rows > 0):
                        $newCI = date("F j, Y", strtotime($b['check_in']));
                        $newCO = date("F j, Y", strtotime($b['check_out']));
                        ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-dark text-warning">
                                <h6 class="mb-0"><i class="fas fa-concierge-bell"></i> Reschedule Details</h6>
                            </div>
                            <div class="card-body p-0">
                                <textarea class="form-control border-0" rows="3"
                                    readonly> <?php
                                    while ($r = $resched_res->fetch_assoc()) {
                                        $oldCI = date("F j, Y", strtotime($r['check_in']));
                                        $oldCO = date("F j, Y", strtotime($r['check_out']));
                                        $dateRes = date("F j, Y g:i A", strtotime($r['date_resched']));
                                        $reason = $r['reason'];

                                        echo "On $dateRes, the guest requested a reschedule, changing the stay from $oldCI - $oldCO to $newCI - $newCO due to the reason: \"$reason\".\n";
                                    }
                                    ?>
                                                                                                                                                                                                                                                                                                                        </textarea>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- BOOKED ROOMS TABLE -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-dark text-warning">
                            <h6 class="mb-0"><i class="bi bi-door-open"></i> Booked Rooms</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Room Type</th>
                                            <th>Price</th>
                                            <th>Room Number</th>
                                            <th>Floor Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($data['rooms'])): ?>
                                            <?php foreach ($data['rooms'] as $room): ?>
                                                <tr>
                                                    <td class="fw-semibold"><?= $room['room_type_name'] ?></td>
                                                    <td>₱<?= number_format($room['price'], 2) ?></td>
                                                    <td><?= $room['room_number'] ?? 'N/A' ?></td>
                                                    <td><?= $room['floor_number'] ?? 'N/A' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No rooms found
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- GUEST LIST TABLE -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark text-warning">
                            <h6 class="mb-0"><i class="bi bi-people-fill"></i> Guest List</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Guest Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($data['guests'])): ?>
                                            <?php foreach ($data['guests'] as $guest): ?>
                                                <tr>
                                                    <td><?= $guest['first_name'] ?></td>
                                                    <td><?= $guest['last_name'] ?></td>
                                                    <td><span class="badge bg-secondary"><?= ucfirst($guest['guest_type']) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">No guests found
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<script>
    function printReceipt(elementId) {
        const receiptHTML = document.getElementById(elementId).outerHTML;
        const styles = document.querySelector("style").innerHTML;

        const printWindow = window.open("", "", "width=900,height=700");

        printWindow.document.write(`
        <html>
        <head>
            <title>Receipt</title>
            <style>
                ${styles}
            </style>
        </head>
        <body class="receipt-print">
            ${receiptHTML}
        </body>
        </html>
    `);

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
</script>



<?php include 'adminFrontend/footer.php'; ?>