<?php
require_once "db.php";

// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Create verification_types table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS verification_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    is_enabled BOOLEAN DEFAULT 1,
    disable_message TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
mysqli_query($con, $create_table_sql);

// Create disable_reasons table if it doesn't exist
$create_reasons_table_sql = "CREATE TABLE IF NOT EXISTS disable_reasons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reason VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($con, $create_reasons_table_sql);

// Insert default reasons if they don't exist
$check_reasons_sql = "SELECT COUNT(*) as count FROM disable_reasons";
$result = mysqli_query($con, $check_reasons_sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $default_reasons = [
        'Service temporarily unavailable',
        'System maintenance in progress',
        'Technical issues with the service',
        'Service provider connection error',
        'Upgrading service features'
    ];
    
    foreach ($default_reasons as $reason) {
        $reason = mysqli_real_escape_string($con, $reason);
        $insert_reason_sql = "INSERT INTO disable_reasons (reason) VALUES ('$reason')";
        mysqli_query($con, $insert_reason_sql);
    }
}

// Insert default verification types if they don't exist
$check_types_sql = "SELECT COUNT(*) as count FROM verification_types";
$result = mysqli_query($con, $check_types_sql);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $insert_types_sql = "INSERT INTO verification_types (type, is_enabled, disable_message) VALUES 
        ('SMS', 1, NULL),
        ('Email', 1, NULL)";
    mysqli_query($con, $insert_types_sql);
}

// Fetch current verification types
$sql = "SELECT * FROM verification_types ORDER BY type";
$result = mysqli_query($con, $sql);

// Fetch active reasons
$reasons_sql = "SELECT * FROM disable_reasons WHERE is_active = 1 ORDER BY reason";
$reasons_result = mysqli_query($con, $reasons_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Type Settings - Casa Estela</title>
    
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    
    <style>
        .verification-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .verification-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .verification-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }
        .status-enabled {
            background-color: #d4edda;
            color: #155724;
        }
        .status-disabled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .toggle-button {
            min-width: 100px;
        }
        .disable-message {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }
        .disable-message.hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h2>Verification Type Settings</h2>
                    <p class="text-muted">Manage customer registration verification methods</p>
                </div>
            </div>

            <!-- Add Manage Reasons section -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Manage Disable Reasons</h5>
                            <button class="btn btn-primary btn-sm" onclick="$('#addReasonModal').modal('show')">
                                <i class="fa fa-plus"></i> Add New Reason
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Reset pointer and fetch all reasons (including inactive)
                                        mysqli_data_seek($reasons_result, 0);
                                        while ($reason = mysqli_fetch_assoc($reasons_result)): 
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reason['reason']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $reason['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $reason['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('F j, Y g:i A', strtotime($reason['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm <?php echo $reason['is_active'] ? 'btn-danger' : 'btn-success'; ?>"
                                                        onclick="toggleReasonStatus(<?php echo $reason['id']; ?>, <?php echo $reason['is_active']; ?>)">
                                                    <?php echo $reason['is_active'] ? 'Deactivate' : 'Activate'; ?>
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
                <?php while ($type = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6">
                    <div class="verification-card">
                        <div class="verification-title">
                            <i class="fa fa-<?php echo strtolower($type['type']) == 'sms' ? 'mobile' : 'envelope'; ?>"></i>
                            <?php echo htmlspecialchars($type['type']); ?> Verification
                        </div>
                        <div class="verification-status">
                            <span class="status-badge <?php echo $type['is_enabled'] ? 'status-enabled' : 'status-disabled'; ?>">
                                <?php echo $type['is_enabled'] ? 'Enabled' : 'Disabled'; ?>
                            </span>
                            <button class="btn <?php echo $type['is_enabled'] ? 'btn-danger' : 'btn-success'; ?> toggle-button"
                                    onclick="toggleVerificationType(<?php echo $type['id']; ?>, <?php echo $type['is_enabled']; ?>)">
                                <i class="fa fa-<?php echo $type['is_enabled'] ? 'times' : 'check'; ?>"></i>
                                <?php echo $type['is_enabled'] ? 'Disable' : 'Enable'; ?>
                            </button>
                        </div>
                        <div class="disable-message <?php echo (!$type['is_enabled'] && $type['disable_message']) ? '' : 'hidden'; ?>" id="message-<?php echo $type['id']; ?>">
                            <strong>Reason for disabling:</strong><br>
                            <?php echo htmlspecialchars($type['disable_message'] ?? ''); ?>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Last updated: <?php echo date('F j, Y g:i A', strtotime($type['last_updated'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Disable Message Modal -->
    <div class="modal fade" id="disableMessageModal" tabindex="-1" role="dialog" aria-labelledby="disableMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableMessageModalLabel">Disable Verification Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="disableMessageForm">
                        <div class="form-group">
                            <label for="disableReason">Select reason for disabling:</label>
                            <select class="form-control" id="disableReason" required>
                                <option value="">Select a reason</option>
                                <?php while ($reason = mysqli_fetch_assoc($reasons_result)): ?>
                                <option value="<?php echo htmlspecialchars($reason['reason']); ?>">
                                    <?php echo htmlspecialchars($reason['reason']); ?>
                                </option>
                                <?php endwhile; ?>
                                <option value="other">Other (specify)</option>
                            </select>
                        </div>
                        <div class="form-group mt-3" id="otherReasonGroup" style="display: none;">
                            <label for="otherReason">Please specify the reason:</label>
                            <textarea class="form-control" id="otherReason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveDisableMessage">Save & Disable</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Reason Modal -->
    <div class="modal fade" id="addReasonModal" tabindex="-1" role="dialog" aria-labelledby="addReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReasonModalLabel">Add New Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addReasonForm">
                        <div class="form-group">
                            <label for="newReason">Reason:</label>
                            <input type="text" class="form-control" id="newReason" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNewReason">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    let currentTypeId = null;

    function toggleVerificationType(id, currentStatus) {
        currentTypeId = id;
        const newStatus = currentStatus ? 0 : 1;
        
        if (currentStatus) {
            // If currently enabled, show modal to get disable message
            $('#disableReason').val('');
            $('#otherReason').val('');
            $('#otherReasonGroup').hide();
            $('#disableMessageModal').modal('show');
        } else {
            // If currently disabled, just enable it
            updateVerificationStatus(id, newStatus, '');
        }
    }

    // Handle reason dropdown change
    $('#disableReason').change(function() {
        if ($(this).val() === 'other') {
            $('#otherReasonGroup').show();
            $('#otherReason').prop('required', true);
        } else {
            $('#otherReasonGroup').hide();
            $('#otherReason').prop('required', false);
        }
    });

    $('#saveDisableMessage').click(function() {
        const reasonSelect = $('#disableReason');
        const selectedReason = reasonSelect.val();
        let finalMessage = '';

        if (!selectedReason) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a reason'
            });
            return;
        }

        if (selectedReason === 'other') {
            finalMessage = $('#otherReason').val().trim();
            if (!finalMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please specify the reason'
                });
                return;
            }
        } else {
            finalMessage = selectedReason;
        }
        
        $('#disableMessageModal').modal('hide');
        updateVerificationStatus(currentTypeId, 0, finalMessage);
    });

    function updateVerificationStatus(id, newStatus, message) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                action: 'toggle_verification_type',
                id: id,
                status: newStatus,
                message: message
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update verification type'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to communicate with the server'
                });
            }
        });
    }

    // Add new reason
    $('#saveNewReason').click(function() {
        const reason = $('#newReason').val().trim();
        if (!reason) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a reason'
            });
            return;
        }

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                action: 'add_disable_reason',
                reason: reason
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to communicate with the server'
                });
            }
        });
    });

    // Toggle reason status
    function toggleReasonStatus(id, currentStatus) {
        const newStatus = currentStatus ? 0 : 1;
        const action = currentStatus ? 'deactivate' : 'activate';

        Swal.fire({
            title: 'Confirm Action',
            text: `Are you sure you want to ${action} this reason?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: {
                        action: 'toggle_reason_status',
                        id: id,
                        status: newStatus
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: result.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while processing your request'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to communicate with the server'
                        });
                    }
                });
            }
        });
    }
    </script>
</body>
</html> 