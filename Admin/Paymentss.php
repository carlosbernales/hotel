<?php
include 'db.php';
include 'header.php';
include 'sidebar.php';

// Get existing payment methods
$payment_methods = $con->query("SELECT * FROM payment_methods ORDER BY id");
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;"></a></li>
            <li class="active">Announcements & Payment Settings</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Payment System Announcements</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Create New Announcement</div>
                <div class="panel-body">
                    <form id="announcementForm">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control" name="message" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" required>
                                <option value="general">General</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="event">Event</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Valid Until</label>
                            <input type="datetime-local" class="form-control" name="valid_until" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Announcement</button>
                    </form>
                </div>
            </div>

            <!-- Payment Methods Section -->
            <div class="panel panel-default">
                <div class="panel-heading">Payment Methods Management</div>
                <div class="panel-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']); 
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']); 
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Display Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>QR Code</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($method = $payment_methods->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($method['name']); ?></td>
                                    <td><?php echo htmlspecialchars($method['display_name']); ?></td>
                                    <td><?php echo htmlspecialchars($method['account_name']); ?></td>
                                    <td><?php echo htmlspecialchars($method['account_number']); ?></td>
                                    <td>
                                        <?php if ($method['qr_code_image']): ?>
                                            <img src="<?php echo htmlspecialchars($method['qr_code_image']); ?>" alt="QR Code" style="max-width: 100px;">
                                        <?php else: ?>
                                            No QR Code
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $method['is_active'] ? 
                                            '<span class="badge badge-success">Active</span>' : 
                                            '<span class="badge badge-danger">Inactive</span>'; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm edit-payment-method" 
                                                data-id="<?php echo $method['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($method['name']); ?>"
                                                data-display-name="<?php echo htmlspecialchars($method['display_name']); ?>"
                                                data-account-name="<?php echo htmlspecialchars($method['account_name']); ?>"
                                                data-account-number="<?php echo htmlspecialchars($method['account_number']); ?>"
                                                data-active="<?php echo $method['is_active']; ?>">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Active Announcements</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Type</th>
                                    <th>Posted On</th>
                                    <th>Valid Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="announcementsList">
                                <?php
                                $query = "SELECT * FROM announcements WHERE valid_until > NOW() ORDER BY created_at DESC";
                                $result = mysqli_query($con, $query);
                                while($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>".$row['title']."</td>";
                                    echo "<td>".$row['message']."</td>";
                                    echo "<td><span class='badge badge-".($row['type'] == 'gcash' ? 'info' : ($row['type'] == 'maya' ? 'success' : 'warning'))."'>".$row['type']."</span></td>";
                                    echo "<td>".date('M d, Y H:i', strtotime($row['created_at']))."</td>";
                                    echo "<td>".date('M d, Y H:i', strtotime($row['valid_until']))."</td>";
                                    echo "<td><button class='btn btn-danger btn-sm' onclick='deleteAnnouncement(".$row['id'].")'>Delete</button></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Payment Method Modal -->
<div class="modal fade" id="editPaymentMethodModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payment Method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="process_payment_method.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_payment_method">
                    <input type="hidden" name="id" id="edit_payment_id">
                    
                    <div class="form-group">
                        <label>Payment Method Name</label>
                        <input type="text" class="form-control" name="name" id="edit_payment_name" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" class="form-control" name="display_name" id="edit_display_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" class="form-control" name="account_name" id="edit_account_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" class="form-control" name="account_number" id="edit_account_number" required>
                    </div>
                    
                    <div class="form-group">
                        <label>QR Code Image</label>
                        <input type="file" class="form-control" name="qr_code_image" accept="image/*">
                        <small class="text-muted">Leave empty to keep current QR code</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="is_active" id="edit_is_active">
                            <label class="custom-control-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}
.badge-info {
    background-color: #17a2b8;
    color: white;
}
.badge-success {
    background-color: #28a745;
    color: white;
}
.badge-warning {
    background-color: #ffc107;
    color: black;
}
.badge-danger {
    background-color: #dc3545;
    color: white;
}
</style>

<script>
// Announcement form handling
document.getElementById('announcementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('process_announcement.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Announcement posted successfully');
            location.reload();
        } else {
            alert('Error posting announcement');
        }
    });
});

function deleteAnnouncement(id) {
    if(confirm('Are you sure you want to delete this announcement?')) {
        fetch('process_announcement.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Announcement deleted successfully');
                location.reload();
            } else {
                alert('Error deleting announcement');
            }
        });
    }
}

// Payment method handling
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut(500);

    // Edit Payment Method
    $('.edit-payment-method').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var displayName = $(this).data('display-name');
        var accountName = $(this).data('account-name');
        var accountNumber = $(this).data('account-number');
        var active = $(this).data('active');

        $('#edit_payment_id').val(id);
        $('#edit_payment_name').val(name);
        $('#edit_display_name').val(displayName);
        $('#edit_account_name').val(accountName);
        $('#edit_account_number').val(accountNumber);
        $('#edit_is_active').prop('checked', active == 1);

        $('#editPaymentMethodModal').modal('show');
    });
});
</script>
