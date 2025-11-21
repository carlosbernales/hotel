<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="card-title">Event Booking #<?php echo isset($booking['id']) ? $booking['id'] : 'N/A'; ?></h6>
                <small class="text-muted">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?php echo isset($booking['reservation_date']) ? date('F d, Y', strtotime($booking['reservation_date'])) : 'N/A'; ?>
                </small>
            </div>
            <span class="badge bg-<?php echo isset($booking['booking_status']) && $booking['booking_status'] === 'finished' ? 'success' : 'danger'; ?>">
                <?php echo isset($booking['booking_status']) ? ucfirst($booking['booking_status']) : 'N/A'; ?>
            </span>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Event Type:</strong> <?php echo isset($booking['event_type']) ? htmlspecialchars($booking['event_type']) : 'N/A'; ?></p>
                <p><strong>Number of Guests:</strong> <?php echo isset($booking['number_of_guests']) ? $booking['number_of_guests'] : 'N/A'; ?></p>
                <p><strong>Time:</strong> 
                    <?php 
                    if (isset($booking['start_time']) && isset($booking['end_time'])) {
                        echo date('g:i A', strtotime($booking['start_time'])) . ' - ' . date('g:i A', strtotime($booking['end_time']));
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Amount:</strong> 
                    <?php echo isset($booking['total_amount']) ? '₱' . number_format($booking['total_amount'], 2) : 'N/A'; ?>
                </p>
                <p><strong>Payment Status:</strong> 
                    <?php 
                    if (isset($booking['booking_status']) && $booking['booking_status'] === 'finished') {
                        echo '<span class="text-success">Paid</span>';
                    } else {
                        echo isset($booking['payment_status']) ? ucfirst($booking['payment_status']) : 'N/A';
                    }
                    ?>
                </p>
                <?php if (isset($booking['payment_method']) && !empty($booking['payment_method'])): ?>
                    <p><strong>Payment Method:</strong> <?php echo strtoupper($booking['payment_method']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($booking['special_requests']) && !empty($booking['special_requests'])): ?>
            <div class="mt-3">
                <p><strong>Special Requests:</strong></p>
                <p class="text-muted"><?php echo htmlspecialchars($booking['special_requests']); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>