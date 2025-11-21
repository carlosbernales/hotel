<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Create notifications table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('booking', 'payment', 'system') NOT NULL,
    customer_name VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($con, $create_table)) {
    die("Error creating table: " . mysqli_error($con));
}

// Mark notification as read
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notification_id = mysqli_real_escape_string($con, $_GET['mark_read']);
    $update_sql = "UPDATE notifications SET is_read = TRUE WHERE id = '$notification_id'";
    mysqli_query($con, $update_sql);
    header("Location: notification.php");
    exit();
}

// Delete notification
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $notification_id = mysqli_real_escape_string($con, $_GET['delete']);
    $delete_sql = "DELETE FROM notifications WHERE id = '$notification_id'";
    mysqli_query($con, $delete_sql);
    header("Location: notification.php");
    exit();
}

// Fetch notifications
$notifications_sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$notifications_result = mysqli_query($con, $notifications_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications - Casa Estela</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .notifications-panel {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .table > tbody > tr > td {
            vertical-align: middle;
        }
        .notification-unread {
            background-color: #fff3cd;
        }
        .notification-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .type-booking {
            background-color: #cce5ff;
            color: #004085;
        }
        .type-payment {
            background-color: #d4edda;
            color: #155724;
        }
        .type-system {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-buttons .btn {
            padding: 4px 8px;
            margin: 0 2px;
        }
        .timestamp {
            font-size: 0.85em;
            color: #6c757d;
        }
        .mark-read {
            color: #28a745;
            cursor: pointer;
        }
        .delete-notification {
            color: #dc3545;
            cursor: pointer;
        }
        /* Modal Styles */
        .modal-content {
            border-radius: 8px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #DAA520;
        }
        .modal-title {
            color: #333;
            font-weight: 600;
        }
        .notification-details label {
            color: #666;
            margin-bottom: 5px;
            display: block;
        }
        .notification-details p {
            color: #333;
            margin-bottom: 15px;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .font-weight-bold {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="active">Notifications</li>
            </ol>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default notifications-panel">
                    <div class="panel-heading">
                        <h2 class="panel-title">
                            Notifications
                            <?php
                            $unread_count = mysqli_num_rows(mysqli_query($con, "SELECT id FROM notifications WHERE is_read = FALSE"));
                            if ($unread_count > 0):
                            ?>
                            <span class="badge badge-warning"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </h2>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="15%">Type</th>
                                        <th width="15%">Customer</th>
                                        <th width="15%">Title</th>
                                        <th width="30%">Message</th>
                                        <th width="15%">Date</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($notifications_result) > 0): ?>
                                        <?php while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                                            <tr class="<?php echo !$notification['is_read'] ? 'notification-unread' : ''; ?>">
                                                <td>
                                                    <span class="notification-type type-<?php echo $notification['type']; ?>">
                                                        <?php echo ucfirst($notification['type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo isset($notification['customer_name']) && $notification['customer_name'] ? htmlspecialchars($notification['customer_name']) : '<em>System</em>'; ?></td>
                                                <td><?php echo htmlspecialchars($notification['title']); ?></td>
                                                <td><?php echo htmlspecialchars($notification['message']); ?></td>
                                                <td>
                                                    <span class="timestamp">
                                                        <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info"
                                                            onclick="viewNotification('<?php echo htmlspecialchars($notification['title']); ?>', '<?php echo htmlspecialchars($notification['message']); ?>', '<?php echo htmlspecialchars($notification['type']); ?>', '<?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>', '<?php echo isset($notification['customer_name']) ? htmlspecialchars($notification['customer_name']) : ''; ?>')"
                                                            title="View details">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <?php if (!$notification['is_read']): ?>
                                                        <a href="?mark_read=<?php echo $notification['id']; ?>" 
                                                           class="btn btn-sm btn-success"
                                                           title="Mark as read">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="?delete=<?php echo $notification['id']; ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Are you sure you want to delete this notification?')"
                                                       title="Delete notification">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No notifications found</td>
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

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="notificationModalLabel">Notification Details</h4>
                </div>
                <div class="modal-body">
                    <div class="notification-details">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Type:</label>
                                <span id="modalType" class="notification-type"></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Customer:</label>
                                <p id="modalCustomer" class="mb-0"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Title:</label>
                                <p id="modalTitle" class="mb-0"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Message:</label>
                                <p id="modalMessage" class="mb-0"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Date:</label>
                                <p id="modalDate" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    function viewNotification(title, message, type, date, customer) {
        // Set modal content
        $('#modalTitle').text(title);
        $('#modalMessage').text(message);
        $('#modalDate').text(date);
        $('#modalCustomer').text(customer || 'System');
        
        // Set notification type with appropriate styling
        $('#modalType').attr('class', 'notification-type type-' + type).text(type.charAt(0).toUpperCase() + type.slice(1));
        
        // Show modal
        $('#notificationModal').modal('show');
    }
    </script>
</body>
</html>


