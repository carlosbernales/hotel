<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="card-title">Room Booking #<?php echo $booking['booking_id']; ?></h6>
                <small class="text-muted">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?php echo date('F d, Y', strtotime($booking['check_in'])) . ' - ' . 
                          date('F d, Y', strtotime($booking['check_out'])); ?>
                </small>
            </div>
            <?php
            $statusClass = match($booking['status']) {
                'finished', 'check-out' => 'bg-success',
                'rejected' => 'bg-danger',
                default => 'bg-secondary'
            };
            ?>
            <span class="badge <?php echo $statusClass; ?>">
                <?php echo ucfirst($booking['status']); ?>
            </span>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Guest Name:</strong> 
                    <?php echo htmlspecialchars($booking['guest_first_name'] . ' ' . $booking['guest_last_name']); ?>
                </p>
                <p><strong>Guest Type:</strong> <?php echo ucfirst($booking['guest_type']); ?></p>
                <?php if (!empty($booking['id_number'])): ?>
                    <p><strong>ID Number:</strong> <?php echo htmlspecialchars($booking['id_number']); ?></p>
                <?php endif; ?>
                <p><strong>Number of Nights:</strong> <?php echo $booking['number_of_nights']; ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Amount:</strong> ₱<?php echo number_format($booking['total_amount'], 2); ?></p>
                <p><strong>Payment Type:</strong> 
                    <?php 
                    if (isset($booking['payment_type'])) {
                        switch($booking['payment_type']) {
                            case 'downpayment':
                                echo '<span class="text-warning">Downpayment</span>';
                                break;
                            case 'fully_paid':
                            case 'paid':
                                echo '<span class="text-success">Fully Paid</span>';
                                break;
                            default:
                                echo ucfirst($booking['payment_type']);
                        }
                    } else {
                        echo 'Paid';
                    }
                    ?>
                </p>
                <?php if (!empty($booking['payment_method'])): ?>
                    <p><strong>Payment Method:</strong> <?php echo strtoupper($booking['payment_method']); ?></p>
                <?php endif; ?>
                <?php if (!empty($booking['payment_proof'])): ?>
                    <p>
                        <strong>Payment Proof:</strong>
                        <button class="btn btn-link text-warning p-0 ms-2" onclick="showFullImage('../../uploads/payment_proofs/<?php echo htmlspecialchars($booking['payment_proof']); ?>')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($booking['special_requests'])): ?>
            <div class="mt-3">
                <p><strong>Special Requests:</strong></p>
                <p class="text-muted"><?php echo htmlspecialchars($booking['special_requests']); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>