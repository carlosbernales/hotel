<?php
require 'db.php';

// Get tables from database
$query = "SELECT * FROM dining_tables WHERE status = 'available' ORDER BY table_type";
$result = $con->query($query);
$tableDetails = array();

if ($result) {
    while ($table = $result->fetch_assoc()) {
        if (!isset($tableDetails[$table['table_type']])) {
            $tableDetails[$table['table_type']] = array(
                'price' => $table['price'],
                'available' => 1,
                'capacity' => $table['capacity'],
                'image' => $table['image_path'] ? $table['image_path'] : 'img/default-table.jpg'
            );
        } else {
            $tableDetails[$table['table_type']]['available']++;
        }
    }
    $result->close();
}
?>

<style>
body {
    padding-top: 60px;
}

.main-content {
    position: relative;
    margin-left: 240px;
    padding: 20px;
    min-height: calc(100vh - 60px);
    background: #fff;
    width: calc(100% - 240px);
}

.table-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.table-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.table-details {
    padding: 15px;
}

.table-details h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.price {
    font-size: 1.2em;
    font-weight: bold;
    color: #333;
    margin: 10px 0;
}

.table-meta {
    margin-top: 10px;
}

.status-available {
    color: #28a745;
}

.status-unavailable {
    color: #dc3545;
}

.table-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn {
    padding: 8px 15px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}

.btn-warning {
    background: #f0ad4e;
    color: #fff;
}

.btn-default {
    background: #f8f9fa;
    color: #333;
}
</style>

<div class="main-content">
    <div class="table-grid">
        <?php foreach ($tableDetails as $tableType => $details): ?>
        <div class="table-card">
            <img src="<?php echo htmlspecialchars($details['image']); ?>" alt="<?php echo htmlspecialchars($tableType); ?>" class="table-image">
            <div class="table-details">
                <h3><?php echo htmlspecialchars($tableType); ?></h3>
                <div class="price">₱<?php echo number_format($details['price'], 2); ?></div>
                <div class="table-meta">
                    <?php if ($details['available'] > 0): ?>
                        <div class="status-available">
                            <i class="fa fa-check-circle"></i>
                            Only <?php echo $details['available']; ?> table<?php echo $details['available'] > 1 ? 's' : ''; ?> left
                        </div>
                    <?php else: ?>
                        <div class="status-unavailable">
                            <i class="fa fa-times-circle"></i>
                            NOT AVAILABLE
                        </div>
                    <?php endif; ?>
                    <div class="capacity">
                        <i class="fa fa-users"></i>
                        Max capacity: <?php echo $details['capacity']; ?> guests
                    </div>
                </div>
                <div class="table-actions">
                    <button class="btn btn-default" data-toggle="modal" data-target="#viewTableModal<?php echo str_replace(' ', '', $tableType); ?>">VIEW DETAILS</button>
                    <?php if ($details['available'] > 0): ?>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#bookTableModal<?php echo str_replace(' ', '', $tableType); ?>">BOOK NOW</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- View Table Modal -->
<?php foreach ($tableDetails as $tableType => $details): ?>
<div class="modal fade" id="viewTableModal<?php echo str_replace(' ', '', $tableType); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo htmlspecialchars($tableType); ?> Table</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="<?php echo htmlspecialchars($details['image']); ?>" alt="<?php echo htmlspecialchars($tableType); ?>" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h5>Table Details</h5>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($tableType); ?></p>
                        <p><strong>Capacity:</strong> <?php echo $details['capacity']; ?> guests</p>
                        <p><strong>Price:</strong> ₱<?php echo number_format($details['price'], 2); ?></p>
                        <p><strong>Availability:</strong> <?php echo $details['available']; ?> table(s) available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Book Table Modal -->
<div class="modal fade" id="bookTableModal<?php echo str_replace(' ', '', $tableType); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Book <?php echo htmlspecialchars($tableType); ?> Table</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bookTableForm<?php echo str_replace(' ', '', $tableType); ?>" method="POST" action="process_table_booking.php">
                    <input type="hidden" name="table_type" value="<?php echo htmlspecialchars($tableType); ?>">
                    <input type="hidden" name="price" value="<?php echo $details['price']; ?>">
                    
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" class="form-control" name="booking_time" required>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests</label>
                        <input type="number" class="form-control" name="guests" min="1" max="<?php echo $details['capacity']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="tel" class="form-control" name="contact" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Special Requests</label>
                        <textarea class="form-control" name="special_requests" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="bookTableForm<?php echo str_replace(' ', '', $tableType); ?>" class="btn btn-warning">Book Now</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
