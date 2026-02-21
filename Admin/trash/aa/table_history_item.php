<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="card-title">Table Reservation #<?php echo $booking['id']; ?></h6>
                <small class="text-muted">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?php echo date('F d, Y', strtotime($booking['booking_date'])); ?>
                </small>
            </div>
            <span class="badge bg-<?php echo $booking['status'] === 'completed' ? 'success' : 'danger'; ?>">
                <?php echo ucfirst($booking['status']); ?>
            </span>
        </div>
        <!-- Add more table booking details as needed -->
    </div>
</div>