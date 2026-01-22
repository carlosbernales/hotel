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

<body>
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">CASA ESTELA BOUTIQUE HOTEL & CAFE</span>
            <div class="nav-icons">
                <a href="index.php?messages"><i class="fas fa-envelope"></i></a>
                <a href="#" class="position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </a>
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

            <a href="index.php?sales-report"
                class="<?php echo ($currentPage == 'sales_report.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Sales Report
            </a>

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