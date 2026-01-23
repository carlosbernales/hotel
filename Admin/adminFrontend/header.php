<?php
$currentPage = basename($page);
?>

<?php
include '../Admin/adminBackend/mydb.php';

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

$userId = intval($_SESSION['user_id']);

$stmt = $conn->prepare("
    SELECT first_name, last_name, email, contact_number, address, profile_photo
    FROM userss
    WHERE id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch Notifications
$notifStmt = $conn->prepare("
    SELECT id, title, message, type, is_read, created_at 
    FROM notifications 
    WHERE user_id = ? 
    ORDER BY is_read ASC, created_at DESC
    LIMIT 10
");

$notifStmt->bind_param("i", $userId);
$notifStmt->execute();
$notifications = $notifStmt->get_result();

// Count unread for the badge
$unreadCountStmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0");
$unreadCountStmt->bind_param("i", $userId);
$unreadCountStmt->execute();
$unreadCount = $unreadCountStmt->get_result()->fetch_assoc()['unread'];
?>
<?php
$uploadDir = "../Admin/adminBackend/user_photo/";
$profileImg = !empty($user['profile_photo'])
    ? $uploadDir . $user['profile_photo']
    : $uploadDir . "default.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Estela Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/header.css">
</head>


<style>
    /* Casa Estela Profile Theme */
    .modal-content.casa-estela-theme {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-header-banner {
        background: #1e1e1e;
        /* Matches your sidebar */
        height: 120px;
        position: relative;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .profile-avatar-wrapper {
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
    }

    .profile-avatar-wrapper img,
    .profile-avatar-wrapper .placeholder-icon {
        width: 100px;
        height: 100px;
        border: 4px solid #fff;
        border-radius: 50%;
        background: #d4af37;
        /* Gold accent */
        object-fit: cover;
    }

    .modal-body-custom {
        padding: 60px 30px 20px 30px;
    }

    .form-label-gold {
        color: #d4af37;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .casa-input {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 10px;
        transition: all 0.3s;
    }

    .casa-input:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }

    .btn-save-gold {
        background-color: #d4af37;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-save-gold:hover {
        background-color: #b8962e;
        color: white;
    }
</style>

<style>
    .user-dropdown {
        position: relative;
        display: inline-block;
    }

    .user-icon-link {
        cursor: pointer;
        color: inherit;
        text-decoration: none;
    }

    .dropdown-menu-custom {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 10px;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        border-radius: 4px;
        overflow: hidden;
    }

    .user-dropdown:hover .dropdown-menu-custom,
    .dropdown-menu-custom:hover {
        display: block;
    }

    .dropdown-item-custom {
        color: #333;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        transition: background-color 0.3s;
    }

    .dropdown-item-custom:hover {
        background-color: #f1f1f1;
        color: #333;
    }

    .dropdown-item-custom i {
        margin-right: 8px;
        width: 16px;
    }
</style>
<style>
    /* --- CASA ESTELA THEME: CONSOLIDATED STYLES --- */

    /* 1. Modal Design - Aligned with Dashboard Cards */
    .notification-modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        background-color: #ffffff;
        overflow: hidden;
    }

    .notification-modal-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f1f1f1;
        padding: 1.5rem;
    }

    .notification-modal-header .modal-title {
        display: flex;
        align-items: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #2c2c2c;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .notification-modal-header .modal-title i {
        color: #d4af37;
        /* Gold Accent from Dashboard */
        margin-right: 12px;
    }

    /* 2. Detail Sections & Content */
    .detail-section {
        margin-bottom: 20px;
    }

    .detail-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #999;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-label i {
        color: #d4af37;
    }

    .detail-content {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #eee;
        color: #444;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* 3. Dropdown & Item Styling */
    .notification-dropdown {
        min-width: 350px;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 0;
    }

    .notification-item {
        position: relative;
        padding: 15px 15px 15px 45px;
        border-bottom: 1px solid #f8f9fa;
        transition: 0.2s ease;
    }

    .notification-item.unread {
        background-color: #fffef5;
        border-left: 4px solid #d4af37;
    }

    .unread-indicator {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 10px;
        height: 10px;
        background: #d4af37;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    /* 4. Badges & Buttons */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4d4d;
        /* Bright red from screenshot */
        color: white;
        border-radius: 50%;
        font-size: 0.7rem;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: 2px solid white;
        animation: pulse 2s infinite;
    }

    .badge-custom {
        background-color: #f1f4f9;
        color: #555;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .btn-theme-close {
        background: #2c2c2c;
        color: #fff;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-theme-close:hover {
        background: #d4af37;
        color: #fff;
    }

    /* 5. Utility */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .notification-list::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #d4af37;
        border-radius: 10px;
    }
</style>

<!-- Enhanced Styles -->
<style>
    /* Notification Modal Styles */
    .notification-modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .notification-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 24px;
        border-bottom: none;
    }

    .notification-modal-header .modal-title {
        display: flex;
        align-items: center;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .notification-modal-header .modal-title i {
        font-size: 1.2rem;
    }

    .notification-modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }

    .notification-modal-header .btn-close:hover {
        opacity: 1;
    }

    /* Notification Bell Badge */
    .notification-bell {
        position: relative;
        display: inline-block;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border-radius: 10px;
        font-size: 0.65rem;
        padding: 2px 6px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        border: 2px solid white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* Notification Dropdown */
    .notification-dropdown {
        min-width: 320px;
        max-width: 90vw;
        width: 380px;
        max-height: 500px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0;
    }

    @media (max-width: 576px) {
        .notification-dropdown {
            width: calc(100vw - 32px);
            max-width: 380px;
        }
    }

    .notification-dropdown .dropdown-header {
        padding: 12px 16px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .notification-list {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0;
    }

    /* Notification Item */
    .notification-item {
        position: relative;
        padding: 12px 16px 12px 40px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        cursor: pointer;
        display: block;
        word-wrap: break-word;
        overflow-wrap: break-word;
        background-color: white;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-item.unread {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    .notification-item.unread:hover {
        background-color: #ffe69c;
    }

    /* Unread Indicator Dot */
    .unread-indicator {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 10px;
        height: 10px;
        background: #ffc107;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
        animation: blink 2s infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Notification Content */
    .notification-content {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }

    .notif-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #212529;
        margin-bottom: 4px;
        line-height: 1.3;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .notification-item.unread .notif-title {
        color: #856404;
    }

    .notif-message {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 6px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-wrap: break-word;
        overflow-wrap: break-word;
        text-overflow: ellipsis;
    }

    .notif-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 6px;
        gap: 8px;
        flex-wrap: wrap;
    }

    .notif-type {
        font-size: 0.75rem;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 60%;
    }

    .notif-time {
        font-size: 0.75rem;
        white-space: nowrap;
    }

    /* Modal Detail Sections */
    .notification-detail {
        padding: 10px 0;
    }

    .detail-section {
        margin-bottom: 20px;
    }

    .detail-section:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .detail-label i {
        color: #667eea;
        width: 16px;
    }

    .detail-content {
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #667eea;
        color: #212529;
        line-height: 1.6;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    /* Scrollbar Styling */
    .notification-list::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .notification-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Ensure no horizontal overflow */
    .notification-dropdown * {
        box-sizing: border-box;
    }
</style>

<body>
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="index.php?messages"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative notification-bell" id="notifDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="notification-badge">
                            <?= $unreadCount > 99 ? '99+' : $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notifDropdown">
                    <li class="dropdown-header">
                        <strong>Notifications</strong>
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-primary ms-2"><?= $unreadCount ?> new</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <div class="notification-list">
                        <?php if ($notifications->num_rows > 0): ?>
                            <?php while ($notif = $notifications->fetch_assoc()): ?>
                                <li>
                                    <a class="dropdown-item notification-item <?= $notif['is_read'] == 0 ? 'unread' : '' ?>"
                                        href="#" data-notif-id="<?= $notif['id'] ?>"
                                        data-notif-title="<?= htmlspecialchars($notif['title'], ENT_QUOTES) ?>"
                                        data-notif-message="<?= htmlspecialchars($notif['message'], ENT_QUOTES) ?>"
                                        data-notif-type="<?= htmlspecialchars($notif['type'], ENT_QUOTES) ?>"
                                        data-notif-time="<?= htmlspecialchars($notif['created_at'], ENT_QUOTES) ?>"
                                        data-notif-isread="<?= $notif['is_read'] ?>"
                                        onclick="showNotificationModal(this); return false;">
                                        <?php if ($notif['is_read'] == 0): ?>
                                            <div class="unread-indicator"></div>
                                        <?php endif; ?>
                                        <div class="notification-content">
                                            <div class="notif-title"><?= htmlspecialchars($notif['title']) ?></div>
                                            <div class="notif-message"><?= htmlspecialchars($notif['message']) ?></div>
                                            <div class="notif-footer">
                                                <small class="notif-type"><i class="fas fa-tag"></i>
                                                    <?= htmlspecialchars($notif['type']) ?></small>
                                                <small class="notif-time text-muted">
                                                    <?php
                                                    $time = strtotime($notif['created_at']);
                                                    $diff = time() - $time;
                                                    if ($diff < 60)
                                                        echo 'Just now';
                                                    elseif ($diff < 3600)
                                                        echo floor($diff / 60) . 'm ago';
                                                    elseif ($diff < 86400)
                                                        echo floor($diff / 3600) . 'h ago';
                                                    elseif ($diff < 604800)
                                                        echo floor($diff / 86400) . 'd ago';
                                                    else
                                                        echo date('M j', $time);
                                                    ?>
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="dropdown-item text-center text-muted py-4">
                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                <p class="mb-0">No notifications</p>
                            </li>
                        <?php endif; ?>
                    </div>

                </ul>

                <div class="user-dropdown">
                    <a href="#" class="user-icon-link"><i class="fas fa-user"></i></a>
                    <div class="dropdown-menu-custom">
                        <a href="javascript:void(0)" class="dropdown-item-custom" data-bs-toggle="modal"
                            data-bs-target="#profileModal">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <a href="logout.php" class="dropdown-item-custom">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content notification-modal-content">
                <div class="modal-header notification-modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="fas fa-bell"></i>
                        <span id="modalNotifTitle">Notification Detail</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="notification-detail">
                        <div class="detail-section">
                            <label class="detail-label"><i class="fas fa-align-left"></i> Message</label>
                            <div class="detail-content" id="modalNotifMessage"></div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="detail-section">
                                    <label class="detail-label"><i class="fas fa-tag"></i> Type</label>
                                    <span class="badge-custom" id="modalNotifType"></span>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="detail-section">
                                    <label class="detail-label"><i class="far fa-clock"></i> Time Received</label>
                                    <div class="text-muted small" id="modalNotifTime"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-theme-close" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Enhanced JavaScript -->
    <script>
        function showNotificationModal(element) {
            const title = element.getAttribute('data-notif-title');
            const message = element.getAttribute('data-notif-message');
            const type = element.getAttribute('data-notif-type');
            const time = element.getAttribute('data-notif-time');
            const notifId = element.getAttribute('data-notif-id');
            const isRead = element.getAttribute('data-notif-isread');

            // Update modal content
            document.getElementById('modalNotifTitle').textContent = title;
            document.getElementById('modalNotifMessage').textContent = message;
            document.getElementById('modalNotifType').textContent = type;

            // Format time
            const date = new Date(time);
            const formattedTime = date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            document.getElementById('modalNotifTime').textContent = formattedTime;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
            modal.show();

            // Mark as read if unread
            if (isRead == '0') {
                markAsRead(notifId, element);
            }
        }

        function markAsRead(notifId, element) {
            fetch('../Admin/adminBackend/mark_notification_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'notif_id=' + notifId
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove unread highlight
                        element.classList.remove('unread');

                        // Remove unread indicator dot
                        const indicator = element.querySelector('.unread-indicator');
                        if (indicator) {
                            indicator.remove();
                        }

                        // Update data attribute
                        element.setAttribute('data-notif-isread', '1');

                        // Update badge counts
                        updateBadgeCount();
                    }
                })
                .catch(err => console.error('Error marking notification as read:', err));
        }

        function updateBadgeCount() {
            const badge = document.querySelector('.notification-badge');
            const headerBadge = document.querySelector('.dropdown-header .badge');

            if (!badge) return;

            let count = document.querySelectorAll('.notification-item.unread').length;

            if (count === 0) {
                badge.remove();
                if (headerBadge) headerBadge.remove();
            } else {
                badge.textContent = count > 99 ? '99+' : count;
                if (headerBadge) headerBadge.textContent = count + ' new';
            }
        }

    </script>
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content casa-estela-theme">
                <div class="modal-header-banner">
                    <button type="button" class="btn-close btn-close-white p-3 float-end"
                        data-bs-dismiss="modal"></button>

                    <div class="profile-avatar-wrapper">
                        <?php if ($profileImg): ?>
                            <img src="<?= htmlspecialchars($profileImg) ?>" class="rounded-circle mb-2" width="120"
                                height="120" alt="Profile Photo">
                        <?php else: ?>
                            <div class="placeholder-icon d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-tie fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-body modal-body-custom">
                    <form id="profileUpdateForm" action="../Admin/adminBackend/update_profile.php" method="POST"
                        enctype="multipart/form-data">

                        <h5 class="text-center fw-bold mb-4">Edit Profile</h5>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label-gold">Full Name</label>
                                <input type="text" name="fullname" class="form-control casa-input"
                                    value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>"
                                    required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-gold">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control casa-input"
                                    value="<?= htmlspecialchars($user['email']) ?>" required>
                                <small id="emailMsg" class="text-danger"></small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-gold">Contact Number</label>
                                <input type="text" name="contact" class="form-control casa-input"
                                    value="<?= htmlspecialchars($user['contact_number']) ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-gold">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="form-control casa-input">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePwd('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-gold">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control casa-input">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="togglePwd('confirm_password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small id="pwdMsg" class="text-danger"></small>
                            </div>

                            <div class="col-md-12 text-center mb-3">
                                <label class="form-label-gold d-block">Profile Photo</label>
                                <input type="file" name="profile_photo" class="form-control mt-2" accept="image/*">
                            </div>


                        </div>

                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="save_profile" id="saveBtn"
                                class="btn btn-save-gold rounded-pill px-4">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const emailInput = document.getElementById('email');
        const emailMsg = document.getElementById('emailMsg');
        const pwd = document.getElementById('password');
        const confirmPwd = document.getElementById('confirm_password');
        const pwdMsg = document.getElementById('pwdMsg');
        const saveBtn = document.getElementById('saveBtn');

        let emailError = false;
        let pwdError = false;

        function togglePwd(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        function updateSaveBtn() {
            saveBtn.disabled = emailError || pwdError;
        }

        emailInput.addEventListener('input', () => {
            fetch('../Admin/adminBackend/profile_check_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(emailInput.value)
            })
                .then(r => r.json())
                .then(d => {
                    emailError = d.exists;
                    emailMsg.textContent = d.exists ? 'Email already exists' : '';
                    updateSaveBtn();
                })
                .catch(() => {
                    emailError = false;
                    updateSaveBtn();
                });
        });

        function checkPwdMatch() {
            if (pwd.value && pwd.value !== confirmPwd.value) {
                pwdError = true;
                pwdMsg.textContent = 'Passwords do not match';
            } else {
                pwdError = false;
                pwdMsg.textContent = '';
            }
            updateSaveBtn();
        }

        pwd.addEventListener('input', checkPwdMatch);
        confirmPwd.addEventListener('input', checkPwdMatch);

        updateSaveBtn();
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userIcon = document.querySelector('.user-icon-link');
            const dropdown = document.querySelector('.dropdown-menu-custom');

            userIcon.addEventListener('click', function (e) {
                e.preventDefault();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.user-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });
        });
    </script>

    <button class="toggle-sidebar shifted" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <?php
            // Use user profile photo if available, otherwise a default
            $sidebarProfile = !empty($user['profile_photo'])
                ? "../Admin/adminBackend/user_photo/" . htmlspecialchars($user['profile_photo'])
                : "../Admin/adminBackend/user_photo/default.png";
            ?>
            <img src="<?= $sidebarProfile ?>" alt="Profile Photo" class="rounded-circle" width="80" height="80">
            <h5><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
        </div>

        <div class="sidebar-menu">

            <a href="index.php?admin-dashboard"
                class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['room_report.php', 'sales_report.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-chart-bar"></i> Reports</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>

                <div class="sidebar-submenu">
                    <a href="index.php?sales-report"
                        class="<?php echo ($currentPage == 'sales_report.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i> Sales Report
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?room-report"
                        class="<?php echo ($currentPage == 'room_report.php') ? 'active' : ''; ?>">
                        <i class="fas fa-door-open"></i> Room Reports
                    </a>
                </div>
            </div>


            <a href="index.php?users" class="<?php echo ($currentPage == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>

            <a href="index.php?feedbacks" class="<?php echo ($currentPage == 'feedback.php') ? 'active' : ''; ?>">
                <i class="fas fa-star"></i> Reviews
            </a>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['rejected_room_bookings_list.php', 'pending_room_bookings_list.php', 'accepted_room_bookings_list.php', 'room_booking_list.php', 'finished_room_bookings_list.php', 'checkInDetails_room_booking.php', 'accepted_room_bookDetails.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-bed"></i> Room Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?pending_room_bookings_list"
                        class="<?php echo ($currentPage == 'pending_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> Pending
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?accepted_room_bookings_list"
                        class="<?php echo ($currentPage == 'accepted_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Accepted
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?room_booking_list"
                        class="<?php echo ($currentPage == 'room_booking_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> Check Ins
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?finished_room_bookings_list"
                        class="<?php echo ($currentPage == 'finished_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-double"></i> Finished
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?rejected-room-bookings"
                        class="<?php echo ($currentPage == 'rejected_room_bookings_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-exclamation-circle"></i>
                        Rejected
                    </a>
                </div>
            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['table_booking_completed.php', 'table_booking_rejected.php', 'table_booking_pending.php', 'table_booking_accepted.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-chair"></i> Table Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>

                <div class="sidebar-submenu">
                    <a href="index.php?table-booking-pend"
                        class="<?php echo ($currentPage == 'table_booking_pending.php') ? 'active' : ''; ?>">
                        <i class="fas fa-hourglass-half"></i> Pending
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?table-booking-acptd"
                        class="<?php echo ($currentPage == 'table_booking_accepted.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Accepted
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?table-booking-completed"
                        class="<?php echo ($currentPage == 'table_booking_completed.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-check"></i> Completed
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?table-booking-rejected"
                        class="<?php echo ($currentPage == 'table_booking_rejected.php') ? 'active' : ''; ?>">
                        <i class="fas fa-times-circle"></i> Rejected
                    </a>
                </div>

            </div>

            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['event_rejected_list.php', 'event_pending_booking.php', 'event_accepted_booking.php', 'event_ongoing_booking.php', 'event_completed_booking.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-calendar-alt"></i> Event Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>

                <div class="sidebar-submenu">
                    <a href="index.php?event-pend-list"
                        class="<?php echo ($currentPage == 'event_pending_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-hourglass-start"></i> Pending
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?event-acp-list"
                        class="<?php echo ($currentPage == 'event_accepted_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> Accepted
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?event-ongoing"
                        class="<?php echo ($currentPage == 'event_ongoing_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-spinner"></i> Ongoing
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?event-complete-list"
                        class="<?php echo ($currentPage == 'event_completed_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-check-double"></i> Completed
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?event-rej-list"
                        class="<?php echo ($currentPage == 'event_rejected_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-times-circle"></i> Rejected
                    </a>
                </div>
            </div>



            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['room_booking.php', 'table_booking.php', 'event_booking.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-book-open"></i> Bookings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?room_booking"
                        class="<?php echo ($currentPage == 'room_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-bed"></i> Room Booking
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?table-booking"
                        class="<?php echo ($currentPage == 'table_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chair"></i> Table Booking
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?event-booking"
                        class="<?php echo ($currentPage == 'event_booking.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i> Event Booking
                    </a>
                </div>
            </div>

            <a href="index.php?staff-management" class="<?php echo ($currentPage == 'staffs.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Staff Management
            </a>


            <div
                class="sidebar-dropdown <?php echo in_array($currentPage, ['offers.php', 'contact_management.php', 'room_management.php', 'table_management.php', 'cafe_management.php', 'event_management.php', 'amenity_list.php', 'facilities_management.php']) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <span><i class="fas fa-cogs"></i> Settings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <a href="index.php?room_management"
                        class="<?php echo ($currentPage == 'room_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-bed"></i> Room Management
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?amenity_list"
                        class="<?php echo ($currentPage == 'amenity_list.php') ? 'active' : ''; ?>">
                        <i class="fas fa-concierge-bell"></i> Amenities
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?table_management"
                        class="<?php echo ($currentPage == 'table_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chair"></i> Table Management
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?cafe_management"
                        class="<?php echo ($currentPage == 'cafe_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-coffee"></i> Cafe Management
                    </a>
                </div>
                <div class="sidebar-submenu">
                    <a href="index.php?event_management"
                        class="<?php echo ($currentPage == 'event_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i> Event Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?facilities"
                        class="<?php echo ($currentPage == 'facilities_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-building"></i> Facility Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?contact_management"
                        class="<?php echo ($currentPage == 'contact_management.php') ? 'active' : ''; ?>">
                        <i class="fas fa-address-book"></i> Contact and About Us Management
                    </a>
                </div>

                <div class="sidebar-submenu">
                    <a href="index.php?offers" class="<?php echo ($currentPage == 'offers.php') ? 'active' : ''; ?>">
                        <i class="fas fa-address-book"></i> Offers
                    </a>
                </div>

            </div>
        </div>
    </div>