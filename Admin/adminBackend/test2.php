<div class="receipt-container">
    <!-- Header -->
    <div class="header">
        <h1>Casa Estela Boutique Hotel & Cafe</h1>
        <p>Gov B Marasigan St, Calapan City, Oriental Mindoro</p>
        <p>Phone: 0908 747 4892 | Email: casaestelaboutiquehotelandcafe@gmail.com</p>
        <p style="margin-top: 15px; font-size: 14px;"><strong>BOOKING RECEIPT</strong></p>
        <p>Reference: <?= htmlspecialchars($booking['booking_reference']) ?></p>
        <p>Date Issued: <?= date("F j, Y") ?></p>
    </div>

    <!-- Guest Information -->
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

    /* ============================
       FETCH LATEST RESCHEDULE
    ============================ */
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

    /* ============================
       NORMALIZE DATES FOR COMPARISON
    ============================ */
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

    <!-- Rest of your code (rooms, amenities, payments, etc.) -->
    <!-- ✅ NO CHANGES NEEDED BELOW THIS LINE -->
</div>