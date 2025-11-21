<?php
session_start();
require_once 'db_con.php';

try {
    $db = Database::getInstance();
    $pdo = $db->connect();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7fc;
            color: #333;
        }

        .notification-container {
            max-width: 1050px;
            margin: 2rem auto;
            padding: 0;
        }

        .notification-header {
            background: #fff;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            color: #1a1f36;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .page-title i {
            color: #4e73df;
            font-size: 1.8rem;
        }

        .notification-filters {
            display: flex;
            gap: 1rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #e1e5ee;
            border-radius: 8px;
            color: #6b7280;
            background: white;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .filter-btn:hover, .filter-btn.active {
            background: #4e73df;
            color: white;
            border-color: #4e73df;
        }

        .notification-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }

        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .notification-card.unread {
            background: linear-gradient(to right, #fff, #f8faff);
            border-left: 4px solid #4e73df;
        }

        .notification-card.unread::after {
            content: '';
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 8px;
            height: 8px;
            background: #4e73df;
            border-radius: 50%;
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.8rem;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background: #f0f4ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4e73df;
        }

        .notification-title {
            font-weight: 600;
            color: #1a1f36;
            margin: 0;
            font-size: 1.1rem;
            flex-grow: 1;
        }

        .notification-time {
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .notification-content {
            color: #4a5568;
            margin: 0.8rem 0;
            line-height: 1.6;
            font-size: 0.95rem;
            padding-left: 3.5rem;
        }

        .notification-actions {
            display: flex;
            gap: 0.8rem;
            margin-top: 1.2rem;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
            padding-left: 3.5rem;
            justify-content: flex-end;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #4e73df;
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: #2e59d9;
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            border: 1px solid #dc3545;
            color: #dc3545;
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-1px);
        }

        .btn-view {
            background: #f8faff;
            border: 1px solid #4e73df;
            color: #4e73df;
        }

        .btn-view:hover {
            background: #4e73df;
            color: white;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #4e73df;
            opacity: 0.5;
        }

        .empty-state h4 {
            color: #1a1f36;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .notification-container {
                margin: 1rem;
            }

            .notification-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .notification-filters {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }

            .notification-content,
            .notification-actions {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container notification-container">
        <div class="notification-header">
            <h2 class="page-title">
                <i class="fas fa-bell"></i>
                Notifications
            </h2>
            <div class="notification-filters">
                <button class="filter-btn active">All</button>
                <button class="filter-btn">Unread</button>
                <button class="filter-btn">Orders</button>
                <button class="filter-btn">Bookings</button>
            </div>
        </div>

        <?php
        // Fetch notifications from database
        $user_id = $_SESSION['user_id'] ?? 0;
        $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$user_id]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($notifications) > 0) {
                foreach ($notifications as $notification) {
                    $unread_class = $notification['is_read'] ? '' : 'unread';
                    ?>
                    <div class="notification-card <?php echo $unread_class; ?>">
                        <div class="notification-meta">
                            <div class="notification-icon">
                                <i class="<?php echo $notification['icon'] ?? 'fas fa-bell'; ?>"></i>
                            </div>
                            <h5 class="notification-title">
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </h5>
                            <span class="notification-time">
                                <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                            </span>
                        </div>
                        <div class="notification-content">
                            <?php echo htmlspecialchars($notification['message']); ?>
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-view view-notification" 
                                    data-id="<?php echo $notification['id']; ?>" 
                                    data-url="<?php echo $notification['url'] ?? '#'; ?>">
                                <i class="fas fa-eye"></i>
                                View
                            </button>
                            <?php if (!$notification['is_read']): ?>
                                <button class="btn btn-primary mark-as-read" 
                                        data-id="<?php echo $notification['id']; ?>">
                                    <i class="fas fa-check"></i>
                                    Mark as Read
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-outline-danger delete-notification" 
                                    data-id="<?php echo $notification['id']; ?>">
                                <i class="fas fa-trash-alt"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h4>No Notifications</h4>
                    <p>You're all caught up! Check back later for new notifications.</p>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mark notification as read
            $('.mark-as-read').click(function() {
                const notificationId = $(this).data('id');
                const button = $(this);
                const card = button.closest('.notification-card');
                
                $.ajax({
                    url: 'ajax/mark_notification_read.php',
                    type: 'POST',
                    data: { notification_id: notificationId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Remove unread class and dot indicator
                            card.removeClass('unread');
                            // Remove only the mark as read button
                            button.fadeOut(300, function() {
                                $(this).remove();
                            });
                            // Update notification counter
                            updateNotificationCount();
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Marked as read',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to mark notification as read',
                            confirmButtonColor: '#4e73df'
                        });
                    }
                });
            });

            // View notification
            $('.view-notification').click(function() {
                const notificationId = $(this).data('id');
                const url = $(this).data('url');
                const card = $(this).closest('.notification-card');
                
                // Mark as read when viewing
                if (card.hasClass('unread')) {
                    $.ajax({
                        url: 'ajax/mark_notification_read.php',
                        type: 'POST',
                        data: { notification_id: notificationId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                card.removeClass('unread');
                                card.find('.mark-as-read').fadeOut(300, function() {
                                    $(this).remove();
                                });
                                updateNotificationCount();
                            }
                        }
                    });
                }
                
                // Navigate to the notification URL if it exists
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });

            // Delete notification
            $('.delete-notification').click(function() {
                if (!confirm('Are you sure you want to delete this notification?')) {
                    return;
                }
                
                const notificationId = $(this).data('id');
                const card = $(this).closest('.notification-card');
                
                $.post('ajax/delete_notification.php', {
                    notification_id: notificationId
                }, function(response) {
                    if (response.success) {
                        card.fadeOut(300, function() {
                            $(this).remove();
                            if ($('.notification-card').length === 0) {
                                location.reload();
                            }
                        });
                    }
                });
            });
        });

        function updateNotificationCount() {
            $.get('get_notification_count.php', function(data) {
                const count = parseInt(data.count);
                const badges = document.querySelectorAll('.notification-badge');
                badges.forEach(badge => {
                    if (count > 0) {
                        badge.style.display = 'inline-block';
                        badge.textContent = count;
                    } else {
                        badge.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
