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
    <link rel="stylesheet" href="../Admin/adminFrontend/css/alerts.css">
    <link rel="stylesheet" href="../Admin/adminFrontend/css/header1.css">
</head>


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
        function showNotificationModal(element) {
            const title = element.getAttribute('data-notif-title');
            const message = element.getAttribute('data-notif-message');
            const type = element.getAttribute('data-notif-type');
            const time = element.getAttribute('data-notif-time');
            const notifId = element.getAttribute('data-notif-id');
            const isRead = element.getAttribute('data-notif-isread');

            document.getElementById('modalNotifTitle').textContent = title;
            document.getElementById('modalNotifMessage').textContent = message;
            document.getElementById('modalNotifType').textContent = type;

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

            const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
            modal.show();

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
                        element.classList.remove('unread');

                        const indicator = element.querySelector('.unread-indicator');
                        if (indicator) {
                            indicator.remove();
                        }

                        element.setAttribute('data-notif-isread', '1');

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
                class="sidebar-dropdown <?php echo in_array($currentPage, ['terms_and_condition.php', 'offers.php', 'contact_management.php', 'room_management.php', 'table_management.php', 'cafe_management.php', 'event_management.php', 'amenity_list.php', 'facilities_management.php']) ? 'active' : ''; ?>">
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

                <div class="sidebar-submenu">
                    <a href="index.php?terms-condition"
                        class="<?php echo ($currentPage == 'terms_and_condition.php') ? 'active' : ''; ?>">
                        <i class="fas fa-address-book"></i> Terms and Condition
                    </a>
                </div>

            </div>
        </div>
    </div>