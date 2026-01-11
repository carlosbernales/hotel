<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$users = [];
$result = $conn->query("SELECT * FROM userss");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

?>
<link rel="stylesheet" href="../Admin/adminFrontend/css/users.css">


<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> <span>Users</span></i>

        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addUsersModal">+ User
            </a>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>User Type</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $userss): ?>
                        <tr>
                            <td><?php echo $userss['first_name'] . ' ' . $userss['last_name']; ?></td>
                            <td><?php echo $userss['email']; ?></td>
                            <td><?php echo $userss['contact_number']; ?></td>
                            <td><?php echo $userss['address']; ?></td>
                            <?php
                            $badge_classes = [
                                'customer' => 'bg-info text-dark',
                                'frontdesk' => 'bg-primary',
                                'cashier' => 'bg-success',
                                'admin' => 'bg-danger'
                            ];

                            $raw_type = trim($userss['user_type']);
                            $lookup_type = strtolower($raw_type);

                            $badge_color = $badge_classes[$lookup_type] ?? 'bg-secondary';

                            $display_type = ucfirst($lookup_type);
                            ?>

                            <td>
                                <span class="badge <?php echo $badge_color; ?>">
                                    <?php echo htmlspecialchars($display_type); ?>
                                </span>
                            </td>
                            <td><?php echo $userss['is_verified']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm table-action-btn table-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal<?php echo $userss['id']; ?>"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form method="POST"
                                    action="../Admin/adminBackend/amenity_delete.php?id=<?php echo $userss['id']; ?>"
                                    style="display:inline-block;">
                                    <button type="submit" class="btn btn-sm table-action-btn table-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this amenity type?')"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $userss['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $userss['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/update_users.php?id=<?php echo $userss['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold" id="editModalLabel<?php echo $userss['id']; ?>">
                                                Edit User Information
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">First Name</label>
                                                    <input type="text" class="form-control package-input" name="first_name"
                                                        value="<?php echo $userss['first_name']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label package-label">Last Name</label>
                                                    <input type="text" class="form-control package-input" name="last_name"
                                                        value="<?php echo $userss['last_name']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Email</label>
                                                <input type="email" class="form-control package-input"
                                                    id="email_edit_<?php echo $userss['id']; ?>" name="email"
                                                    value="<?php echo $userss['email']; ?>" required>
                                                <small id="emailFeedbackEdit_<?php echo $userss['id']; ?>"
                                                    class="feedback-text"></small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Contact Number</label>
                                                <input type="text" class="form-control package-input" name="contact_number"
                                                    maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                                    value="<?php echo $userss['contact_number']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Address</label>
                                                <textarea class="form-control package-input" name="address" rows="2"
                                                    required><?php echo $userss['address']; ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Password</label>
                                                <div class="position-relative">

                                                    <input type="password" class="form-control package-input"
                                                        id="password_edit_<?php echo $userss['id']; ?>"
                                                        placeholder="Leave empty to keep old password">
                                                    <span
                                                        class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                                        onclick="togglePassword('password_edit_<?php echo $userss['id']; ?>', this)"
                                                        style="z-index: 10;">
                                                        👁️
                                                    </span>

                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Confirm Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control package-input"
                                                        id="confirm_password_edit_<?php echo $userss['id']; ?>"
                                                        placeholder="Leave empty to keep old password">
                                                    <span
                                                        class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                                        onclick="togglePassword('confirm_password_edit_<?php echo $userss['id']; ?>', this)"
                                                        style="z-index: 10;">
                                                        👁️
                                                    </span>
                                                </div>
                                                <small id="passwordFeedback_<?php echo $userss['id']; ?>"
                                                    class="feedback-text"></small>
                                            </div>



                                            <div class="mb-4">
                                                <label class="form-label package-label">User Type</label>
                                                <select class="form-select package-input" name="user_type" required>
                                                    <option value="" disabled <?php echo empty($userss['user_type']) ? 'selected' : ''; ?>>Select user type</option>
                                                    <option value="admin" <?php echo ($userss['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="cashier" <?php echo ($userss['user_type'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                                                    <option value="frontdesk" <?php echo ($userss['user_type'] == 'frontdesk') ? 'selected' : ''; ?>>FrontDesk</option>
                                                    <option value="customer" <?php echo ($userss['user_type'] == 'customer') ? 'selected' : ''; ?>>User</option>
                                                </select>

                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label package-label">Account Status</label>
                                                <select class="form-select package-input" name="is_verified" required>
                                                    <option value="" disabled <?php echo empty($userss['is_verified']) ? 'selected' : ''; ?>>Select Status</option>
                                                    <option value="1" <?php echo ($userss['is_verified'] == '1') ? 'selected' : ''; ?>>Verified</option>
                                                    <option value="0" <?php echo ($userss['is_verified'] == '0') ? 'selected' : ''; ?>>Unverified</option>
                                                </select>

                                            </div>

                                            <button type="submit" id="updateUserBtn_<?php echo $userss['id']; ?>"
                                                class="btn package-btn-save">Update Account</button>

                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



    <div class="modal fade" id="addUsersModal" tabindex="-1" aria-labelledby="addUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content package-modal">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="addUsersModalLabel">Add User</h5>
                    <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body package-modal-body">


                    <form id="addUserForm" method="POST" action="../Admin/adminBackend/user_add.php"
                        class="p-4 shadow-sm bg-white rounded">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label package-label">First Name</label>
                                <input type="text" class="form-control package-input" name="first_name"
                                    placeholder="John" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label package-label">Last Name</label>
                                <input type="text" class="form-control package-input" name="last_name" placeholder="Doe"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Email</label>
                            <input type="email" class="form-control package-input" id="email" name="email"
                                placeholder="email@example.com" required>
                            <small id="emailFeedback" class="feedback-text"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Contact Number</label>
                            <input type="text" class="form-control package-input" name="contact_number" maxlength="11"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="09123456789"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Address</label>
                            <textarea class="form-control package-input" name="address" rows="2"
                                placeholder="Full Address" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control package-input" id="password" name="password"
                                    required style="padding-right: 40px;">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                    onclick="togglePassword('password', this)" style="z-index: 10;">
                                    👁️
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label package-label">Confirm Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control package-input" id="confirm_password" required
                                    style="padding-right: 40px;">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                    onclick="togglePassword('confirm_password', this)" style="z-index: 10;">
                                    👁️
                                </span>
                            </div>
                            <!-- Fixed small tag for JS -->
                            <small id="passwordFeedback" class="feedback-text"></small>
                        </div>


                        <div class="mb-4">
                            <label class="form-label package-label">User Type</label>
                            <select class="form-select package-input" name="user_type" required>
                                <option value="" disabled selected>Select user type</option>
                                <option value="admin">Admin</option>
                                <option value="cashier">Cashier</option>
                                <option value="frontdesk">FrontDesk</option>
                                <option value="customer">User</option>
                            </select>
                        </div>

                        <button type="submit" id="saveUserBtn" class="btn package-btn-save">
                            Create Account
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>


</div>



<?php include 'adminFrontend/footer.php'; ?>
<script>
    /* ===== Toggle Password Visibility ===== */
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            el.textContent = "🙈"; // show as "hide"
        } else {
            input.type = "password";
            el.textContent = "👁️"; // show as "show"
        }
    }

    /* ===== Add User Modal Logic ===== */
    const addPwdInput = document.getElementById("password");
    const addConfirmInput = document.getElementById("confirm_password");
    const addSaveBtn = document.getElementById("saveUserBtn");
    const addPwdFeedback = document.getElementById("passwordFeedback");

    function validateAddUserForm() {
        const pwd = addPwdInput.value.trim();
        const confirm = addConfirmInput.value.trim();
        const passwordsMatch = pwd && pwd === confirm;

        if (!pwd && !confirm) {
            addPwdFeedback.textContent = "";
        } else if (!passwordsMatch) {
            addPwdFeedback.textContent = "Passwords do not match";
            addPwdFeedback.style.color = "#dc3545";
        } else {
            addPwdFeedback.textContent = "Passwords match";
            addPwdFeedback.style.color = "#198754";
        }

        addSaveBtn.disabled = !passwordsMatch;
    }

    addPwdInput.addEventListener("input", validateAddUserForm);
    addConfirmInput.addEventListener("input", validateAddUserForm);

    /* ===== Edit User Modal Logic ===== */
    document.querySelectorAll('[id^="password_edit_"]').forEach(pwdInput => {
        const userId = pwdInput.id.split('_').pop();
        const confirmInput = document.getElementById('confirm_password_edit_' + userId);
        const feedback = document.getElementById('passwordFeedback_' + userId);
        const updateBtn = document.getElementById('updateUserBtn_' + userId);

        function validateEditForm() {
            const pwd = pwdInput.value.trim();
            const confirm = confirmInput.value.trim();

            // If both password fields are empty, allow update
            if (!pwd && !confirm) {
                feedback.textContent = "";
                updateBtn.disabled = false;
                return;
            }

            if (pwd !== confirm) {
                feedback.textContent = "Passwords do not match";
                feedback.style.color = "#dc3545";
                updateBtn.disabled = true;
            } else {
                feedback.textContent = "Passwords match";
                feedback.style.color = "#198754";
                updateBtn.disabled = false;
            }
        }

        pwdInput.addEventListener('input', validateEditForm);
        confirmInput.addEventListener('input', validateEditForm);
    });

    /* ===== Email Validation ===== */
    // Add User Modal
    const addEmailInput = document.getElementById("email");
    const addEmailFeedback = document.getElementById("emailFeedback");

    addEmailInput.addEventListener('blur', function () {
        const email = this.value.trim();
        if (!email) return;

        fetch("../Admin/adminBackend/check_email.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "email=" + encodeURIComponent(email)
        })
            .then(res => res.text())
            .then(data => {
                if (data === "exists") {
                    addEmailFeedback.textContent = "This email is already registered.";
                    addEmailFeedback.style.color = "#dc3545";
                    this.classList.add('is-invalid');
                    addSaveBtn.disabled = true;
                } else {
                    addEmailFeedback.textContent = "Email is available.";
                    addEmailFeedback.style.color = "#198754";
                    this.classList.remove('is-invalid');
                    validateAddUserForm(); // re-check password to enable button
                }
            });
    });

    // Edit User Modals
    document.querySelectorAll('[id^="email_edit_"]').forEach(emailInput => {
        const userId = emailInput.id.split('_').pop();
        const feedback = document.getElementById('emailFeedbackEdit_' + userId);
        const updateBtn = document.getElementById('updateUserBtn_' + userId);

        emailInput.addEventListener('blur', function () {
            const email = this.value.trim();
            if (!email) return;

            fetch("../Admin/adminBackend/check_email_edit.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "email=" + encodeURIComponent(email) + "&id=" + encodeURIComponent(userId)
            })
                .then(res => res.text())
                .then(data => {
                    if (data === "exists") {
                        feedback.textContent = "This email is already registered.";
                        feedback.style.color = "#dc3545";
                        updateBtn.disabled = true;
                    } else {
                        feedback.textContent = "Email is available.";
                        feedback.style.color = "#198754";
                        // Re-validate password to enable button
                        const pwd = document.getElementById('password_edit_' + userId).value;
                        const confirm = document.getElementById('confirm_password_edit_' + userId).value;
                        if (!pwd && !confirm || pwd === confirm) {
                            updateBtn.disabled = false;
                        }
                    }
                });
        });
    });
</script>