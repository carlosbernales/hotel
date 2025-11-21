<?php
require_once 'db.php';
include 'header.php';
include 'sidebar.php';

// Debug session data
echo "<!-- Debug: Session Data: " . print_r($_SESSION, true) . " -->";

// First, check if user is logged in
if (!isset($_SESSION['username']) && !isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    // Redirect to login page if no session exists
    header("Location: login.php");
    exit();
}

// First, check if the admin table exists and has data
$check_table = mysqli_query($con, "SELECT * FROM admin WHERE email = 'admin@example.com'");
if (mysqli_num_rows($check_table) == 0) {
    // Create admin table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS admin (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50),
        middle_initial VARCHAR(1),
        last_name VARCHAR(50),
        age INT,
        address TEXT,
        position VARCHAR(50),
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($con, $create_table);
    
    // Insert your specific admin data
    $default_password = password_hash('adminpassword', PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO admin (
        username,
        password,
        first_name,
        middle_initial,
        last_name,
        age,
        address,
        position,
        email
    ) VALUES (
        'admin@example.com',
        ?,
        'Alfred Hendrik',
        'A',
        'Aceveda',
        20,
        'Balite Calapan City Oriental Mindoro',
        'Manager',
        'admin@example.com'
    )";
    
    if ($stmt = $con->prepare($insert_admin)) {
        $stmt->bind_param("s", $default_password);
        $stmt->execute();
    }
}

// Update admin table if needed
$check_columns = mysqli_query($con, "SHOW COLUMNS FROM admin LIKE 'middle_initial'");
if (mysqli_num_rows($check_columns) == 0) {
    // Add new columns
    $alter_table = "ALTER TABLE admin 
        ADD middle_initial VARCHAR(1) AFTER first_name,
        ADD age INT AFTER last_name,
        ADD address TEXT AFTER age,
        ADD position VARCHAR(50) AFTER address";
    mysqli_query($con, $alter_table);
}

// Try to get admin username from various possible session variables
$admin_username = $_SESSION['username'] ?? $_SESSION['admin_username'] ?? 'admin';

// Get admin details
$query = "SELECT * FROM admin WHERE email = 'admin@example.com'";
$result = mysqli_query($con, $query);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("Error: Unable to retrieve admin profile.");
}

// Debug output
echo "<!-- Debug: Admin Data: " . print_r($admin, true) . " -->";

// Get current user's information from the userss table
$current_user_id = $_SESSION['user_id'] ?? null;
$current_email = $_SESSION['email'] ?? null;
$current_user_type = $_SESSION['user_type'] ?? null;

if (!$current_user_id) {
    die("Error: No user is currently logged in.");
}

// Get user details from the userss table
$query = "SELECT * FROM userss WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Error: Unable to retrieve user profile.");
}

// Debug output
echo "<!-- Debug: User Data: " . print_r($user, true) . " -->";
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-home"></i></a></li>
            <li class="active">My Profile</li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">My Profile</h3>
                </div>
                <div class="panel-body">
                    <div class="profile-container">
                        <div class="profile-header">
                            <div class="profile-image">
                                <i class="fa fa-user-circle"></i>
                            </div>
                            <div class="profile-name">
                                <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                                <div class="position"><?php echo htmlspecialchars(ucfirst($user['user_type'])); ?></div>
                            </div>
                        </div>

                        <div class="info-section">
                            <form id="profileForm" method="POST" action="update_profile.php">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-user"></i> First Name</label>
                                            <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fa fa-user"></i> Last Name</label>
                                            <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-envelope"></i> Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-phone"></i> Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-map-marker"></i> Address</label>
                                    <textarea class="form-control" name="address" readonly><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="profile-actions">
                                    <button type="button" class="btn btn-primary" onclick="enableEdit()">
                                        <i class="fa fa-edit"></i> Edit Profile
                                    </button>
                                    <button type="submit" class="btn btn-success" id="saveBtn" style="display: none;">
                                        <i class="fa fa-save"></i> Save Changes
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="showChangePasswordModal()">
                                        <i class="fa fa-key"></i> Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Section -->
    <?php if ($user['user_type'] === 'admin'): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">User Management</h3>
                </div>
                <div class="panel-body">
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">
                        <i class="fa fa-plus"></i> Add New User
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>User Type</th>
                                    <th>Contact Number</th>
                                    <th>Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Update the query to use actual_password
                                $query = "SELECT id, first_name, last_name, email, user_type, contact_number, COALESCE(actual_password, password) as display_password FROM userss";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact_number'] ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="password-cell">
                                            <span class="password-dots">••••••••</span>
                                            <span class="password-text" style="display: none;"><?php echo htmlspecialchars($row['display_password'] ?? ''); ?></span>
                                            <button class="btn btn-sm btn-info toggle-password" onclick="verifyAndTogglePassword(this)">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="resetPassword(<?php echo $row['id']; ?>)">
                                            <i class="fa fa-key"></i> Reset Password
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $row['id']; ?>)">
                                            <i class="fa fa-trash"></i> Delete
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
    <?php endif; ?>
</div>

<style>
.profile-container {
    padding: 20px;
}

.profile-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.profile-image {
    font-size: 80px;
    color: #D4AF37;
    margin-right: 30px;
}

.profile-name h2 {
    margin: 0;
    color: #333;
    font-size: 24px;
}

.position {
    color: #666;
    font-size: 16px;
    margin-top: 5px;
}

.info-section {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 8px;
}

.info-item {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.info-item label {
    display: block;
    color: #666;
    font-size: 14px;
    margin-bottom: 5px;
}

.info-item label i {
    margin-right: 10px;
    color: #D4AF37;
}

.info-item span {
    display: block;
    color: #333;
    font-size: 16px;
    padding-left: 24px;
}

.profile-actions {
    margin-top: 30px;
    text-align: center;
}

.profile-actions button {
    margin: 0 10px;
    padding: 8px 20px;
}

.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.role-buttons {
    width: 100%;
    margin-top: 10px;
}

.role-buttons .btn {
    flex: 1;
    padding: 10px;
}

.role-buttons .btn.active {
    background-color: #D4AF37;
    border-color: #D4AF37;
    color: white;
}

.modal-lg {
    max-width: 800px;
}

.role-buttons .btn {
    transition: all 0.3s ease;
}

.role-buttons .btn.active {
    background-color: #D4AF37;
    border-color: #D4AF37;
    color: white;
    font-weight: bold;
}

.btn-hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.role-selection {
    margin-bottom: 20px;
}

.role-selection label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

.message-icon {
    position: relative;
    margin-right: 20px;
}

.unread-count {
    font-size: 0.7em;
    padding: 0.25em 0.6em;
}

.messages-list {
    max-height: 400px;
    overflow-y: auto;
}

.message-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.message-item:hover {
    background-color: #f8f9fa;
}

.message-item.unread {
    background-color: #f0f7ff;
}

.message-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.sender {
    font-weight: bold;
}

.date {
    color: #666;
    font-size: 0.9em;
}

.subject {
    font-weight: 500;
    margin-bottom: 5px;
}

.message-preview {
    color: #666;
    font-size: 0.9em;
}

.message-actions {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.header-right {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
}

.message-icon {
    margin-right: 15px;
}

.message-icon .btn-link {
    color: #fff;
    font-size: 20px;
}

.message-icon .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 10px;
    padding: 3px 6px;
    border-radius: 50%;
}

.message-icon .btn-link:hover {
    color: #D4AF37;
}

.mb-3 {
    margin-bottom: 15px;
}

.table-responsive {
    margin-top: 15px;
}

.btn-sm {
    margin: 2px;
}

.password-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.password-dots, .password-text {
    flex: 1;
}

.toggle-password {
    padding: 2px 6px;
}
</style>

<!-- Add Edit Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <!-- Your edit form modal content here -->
</div>

<!-- Your existing Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="changePasswordForm" method="POST" action="update_password.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this modal after your existing modals -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addUserModalLabel">Add New User</h4>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>User Type</label>
                        <select class="form-control" name="user_type" required>
                            <option value="admin">Admin</option>
                            <option value="frontdesk">Front Desk</option>
                            <option value="cashier">Cashier</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="password" id="password" required>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="generatePassword()">Generate</button>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" class="form-control" name="contact_number">
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitAddUser()">Add User</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal for messages -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Messages</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Message Navigation -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#inbox">Inbox</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#compose">Compose</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Inbox Tab -->
                    <div class="tab-pane fade show active" id="inbox">
                        <div class="messages-list">
                            <?php
                            $messages_query = "SELECT m.*, u.first_name, u.last_name, u.user_type 
                                             FROM messages m 
                                             JOIN userss u ON m.sender_id = u.id 
                                             WHERE m.receiver_id = ? 
                                             ORDER BY m.created_at DESC";
                            $stmt = $con->prepare($messages_query);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $messages = $stmt->get_result();
                            
                            while ($message = $messages->fetch_assoc()):
                            ?>
                            <div class="message-item <?php echo $message['is_read'] ? '' : 'unread'; ?>">
                                <div class="message-header">
                                    <span class="sender"><?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?></span>
                                    <span class="date"><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></span>
                                </div>
                                <div class="subject"><?php echo htmlspecialchars($message['subject']); ?></div>
                                <div class="message-preview"><?php echo substr(htmlspecialchars($message['message']), 0, 100) . '...'; ?></div>
                                <div class="message-actions">
                                    <button class="btn btn-sm btn-primary" onclick="viewMessage(<?php echo $message['id']; ?>)">View</button>
                                    <button class="btn btn-sm btn-info" onclick="replyToMessage(<?php echo $message['id']; ?>)">Reply</button>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Compose Tab -->
                    <div class="tab-pane fade" id="compose">
                        <form id="composeForm" action="send_message.php" method="POST">
                            <div class="form-group">
                                <label>To:</label>
                                <select class="form-control" name="receiver_id" required>
                                    <option value="">Select Recipient</option>
                                    <?php
                                    $users_query = "SELECT id, first_name, last_name, user_type FROM userss WHERE id != ?";
                                    $stmt = $con->prepare($users_query);
                                    $stmt->bind_param("i", $_SESSION['user_id']);
                                    $stmt->execute();
                                    $users = $stmt->get_result();
                                    
                                    while ($user = $users->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['user_type'] . ')'); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Subject:</label>
                                <input type="text" class="form-control" name="subject" required>
                            </div>
                            <div class="form-group">
                                <label>Message:</label>
                                <textarea class="form-control" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="newPassword" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="generatePasswordBtn">
                                <i class="fas fa-sync-alt"></i> Generate
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="copyPasswordBtn">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
                <div id="resetPasswordMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveNewPasswordBtn">Save Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modal -->
<div class="modal fade" id="passwordVerificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Security Check</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="adminPassword">Enter Admin Password</label>
                    <input type="password" class="form-control" id="adminPassword" placeholder="Enter your admin password">
                </div>
                <div id="verificationMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="verifyPasswordBtn">Verify</button>
            </div>
        </div>
    </div>
</div>

<!-- Passwords Display Modal -->
<div class="modal fade" id="passwordsDisplayModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Passwords</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Current Password</th>
                            </tr>
                        </thead>
                        <tbody id="passwordsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function enableEdit() {
    // Enable all form inputs except email
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input:not([name="email"]), textarea');
    inputs.forEach(input => {
        input.readOnly = false;
    });
    
    // Show save button
    document.getElementById('saveBtn').style.display = 'inline-block';
}

function addUser() {
    $('#addUserModal').modal('show');
}

function showChangePasswordModal() {
    $('#changePasswordModal').modal('show');
}

// Preview image before upload
document.getElementById('profile_image').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-img').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Role selection handling
function selectRole(role) {
    $('.role-buttons .btn').removeClass('active');
    $(`.role-buttons .btn:contains('${role}')`).addClass('active');
    $('#selectedRole').val(role);
    console.log('Role selected:', role); // Debug line
}

function submitUserForm() {
    const form = document.getElementById('addUserForm');
    const password = form.querySelector('[name="password"]').value;
    const confirmPassword = form.querySelector('[name="confirm_password"]').value;
    const selectedRole = $('#selectedRole').val();
    
    console.log('Current selected role:', selectedRole); // Debug line
    
    // Validate role selection
    if (!selectedRole || selectedRole.trim() === '') {
        alert('Please select a role');
        return;
    }
    
    // Validate password match
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }
    
    // For debugging - remove in production
    console.log('Form data before submission:', {
        role: selectedRole,
        firstName: form.querySelector('[name="first_name"]').value,
        email: form.querySelector('[name="email"]').value,
        hasPassword: password.length > 0
    });
    
    // Submit the form
    form.submit();
}

// When the modal opens, clear the previous selection
$('#addUserModal').on('show.bs.modal', function () {
    $('#selectedRole').val('');
    $('.role-buttons .btn').removeClass('active');
});

// Initialize tooltips if you're using Bootstrap
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add visual feedback for role selection
    $('.role-buttons .btn').hover(
        function() { $(this).addClass('btn-hover'); },
        function() { $(this).removeClass('btn-hover'); }
    );
});

function showMessageModal() {
    $('#messageModal').modal('show');
}

function viewMessage(messageId) {
    // Mark message as read
    $.post('mark_message_read.php', { message_id: messageId }, function() {
        // Load full message content
        $.get('get_message.php', { message_id: messageId }, function(response) {
            // Create and show message view modal
            const message = JSON.parse(response);
            const viewModal = `
                <div class="modal fade" id="viewMessageModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${message.subject}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p><strong>From:</strong> ${message.sender_name}</p>
                                <p><strong>Date:</strong> ${message.created_at}</p>
                                <hr>
                                <div class="message-content">${message.message}</div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-info" onclick="replyToMessage(${messageId})">Reply</button>
                                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $(viewModal).modal('show');
        });
    });
}

function replyToMessage(messageId) {
    // Load original message details
    $.get('get_message.php', { message_id: messageId }, function(response) {
        const message = JSON.parse(response);
        // Switch to compose tab and pre-fill fields
        $('.nav-tabs a[href="#compose"]').tab('show');
        $('select[name="receiver_id"]').val(message.sender_id);
        $('input[name="subject"]').val('Re: ' + message.subject);
        $('textarea[name="message"]').val('\n\n---Original Message---\n' + message.message);
    });
}

function generatePassword() {
    // Make an AJAX call to generate_password.php
    $.ajax({
        url: 'generate_password.php',
        method: 'GET',
        success: function(response) {
            $('#password').val(response.password);
        },
        error: function() {
            alert('Error generating password');
        }
    });
}

function submitAddUser() {
    var formData = $('#addUserForm').serialize();
    $.ajax({
        url: 'add_user.php',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('User added successfully');
                location.reload();
            } else {
                alert(response.message || 'Error adding user');
            }
        },
        error: function() {
            alert('Error adding user');
        }
    });
}

function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password?')) {
        // First generate a new password
        $.ajax({
            url: 'generate_password.php',
            method: 'POST',
            dataType: 'json',
            success: function(genResponse) {
                if (genResponse.success) {
                    // Now reset the password with the generated one
                    $.ajax({
                        url: 'reset_password.php',
                        method: 'POST',
                        data: {
                            user_id: userId,
                            new_password: genResponse.password
                        },
                        success: function(response) {
                            try {
                                response = typeof response === 'string' ? JSON.parse(response) : response;
                                if (response.success) {
                                    alert('New password: ' + genResponse.password + '\nPlease save this password.');
                                    location.reload();
                                } else {
                                    alert(response.message || 'Error resetting password');
                                }
                            } catch (e) {
                                alert('Error processing server response');
                            }
                        },
                        error: function() {
                            alert('Error connecting to server');
                        }
                    });
                } else {
                    alert('Error generating password');
                }
            },
            error: function() {
                alert('Error generating password');
            }
        });
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: 'delete_user.php',
            method: 'POST',
            data: { user_id: userId },
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully');
                    location.reload();
                } else {
                    alert(response.message || 'Error deleting user');
                }
            },
            error: function() {
                alert('Error deleting user');
            }
        });
    }
}

$(document).ready(function() {
    let currentUserId = null;

    // Generate new password
    function generateNewPassword() {
        $.ajax({
            url: 'generate_password.php',
            method: 'POST',
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#newPassword').val(data.password);
                    } else {
                        $('#resetPasswordMessage').html('<div class="alert alert-danger">Error generating password</div>');
                    }
                } catch (e) {
                    $('#newPassword').val(response);
                }
            },
            error: function() {
                $('#resetPasswordMessage').html('<div class="alert alert-danger">Error generating password</div>');
            }
        });
    }

    // Generate password button click
    $('#generatePasswordBtn').on('click', function() {
        generateNewPassword();
    });

    // Copy password button click
    $('#copyPasswordBtn').on('click', function() {
        const passwordField = document.getElementById('newPassword');
        passwordField.select();
        document.execCommand('copy');
        
        // Show feedback
        const originalText = $(this).html();
        $(this).html('<i class="fas fa-check"></i> Copied!');
        setTimeout(() => {
            $(this).html(originalText);
        }, 2000);
    });

    // Save new password
    $('#saveNewPasswordBtn').on('click', function() {
        const newPassword = $('#newPassword').val();
        if (!newPassword) {
            $('#resetPasswordMessage').html('<div class="alert alert-danger">Please generate a password first</div>');
            return;
        }

        $.ajax({
            url: 'reset_password.php',
            method: 'POST',
            data: {
                user_id: currentUserId,
                new_password: newPassword
            },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#resetPasswordMessage').html('<div class="alert alert-success">' + data.message + '</div>');
                        setTimeout(() => {
                            $('#resetPasswordModal').modal('hide');
                            $('#resetPasswordMessage').html('');
                        }, 2000);
                    } else {
                        $('#resetPasswordMessage').html('<div class="alert alert-danger">' + data.message + '</div>');
                    }
                } catch (e) {
                    $('#resetPasswordMessage').html('<div class="alert alert-danger">Error processing response</div>');
                }
            },
            error: function() {
                $('#resetPasswordMessage').html('<div class="alert alert-danger">An error occurred while resetting the password</div>');
            }
        });
    });

    // Direct event binding for the verify button
    $('#verifyPasswordBtn').on('click', function(e) {
        e.preventDefault();
        var adminPassword = $('#adminPassword').val();
        
        if (!adminPassword) {
            $('#verificationMessage').html('<div class="alert alert-danger">Please enter your password</div>');
            return;
        }
        
        // Show loading message
        $('#verificationMessage').html('<div class="alert alert-info">Verifying...</div>');
        
        // Verify admin password using AJAX
        $.ajax({
            url: 'verify_admin_password.php',
            type: 'POST',
            data: {
                password: adminPassword
            },
            success: function(response) {
                try {
                    response = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (response.success) {
                        // Show success message
                        $('#verificationMessage').html(
                            '<div class="alert alert-success">Verification successful!</div>'
                        );
                        
                        // Wait briefly then show the password
                        setTimeout(function() {
                            $('#passwordVerificationModal').modal('hide');
                            $('#adminPassword').val('');
                            $('#verificationMessage').html('');
                            
                            // Show the password if we have elements stored
                            if (window.currentPasswordElements) {
                                window.currentPasswordElements.dots.style.display = 'none';
                                window.currentPasswordElements.text.style.display = 'inline';
                                window.currentPasswordElements.icon.classList.remove('fa-eye');
                                window.currentPasswordElements.icon.classList.add('fa-eye-slash');
                                window.currentPasswordElements = null;
                            }
                        }, 1000);
                    } else {
                        $('#verificationMessage').html(
                            '<div class="alert alert-danger">' + 
                            (response.message || 'Incorrect password') + 
                            '</div>'
                        );
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    $('#verificationMessage').html(
                        '<div class="alert alert-danger">Error processing server response</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#verificationMessage').html(
                    '<div class="alert alert-danger">Error connecting to server</div>'
                );
            }
        });
    });

    // Handle Enter key press in password input
    $('#adminPassword').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            $('#verifyPasswordBtn').click();
        }
    });

    // Clear previous input and messages when modal is shown
    $('#passwordVerificationModal').on('show.bs.modal', function() {
        $('#adminPassword').val('');
        $('#verificationMessage').html('');
    });

    // Add this CSS
    $('head').append(`
        <style>
            .password-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .password-dots, .password-text {
                flex: 1;
            }
            .toggle-password {
                padding: 2px 6px;
            }
        </style>
    `);
});

function fetchUserPasswords() {
    $.ajax({
        url: 'get_user_passwords.php',
        method: 'POST',
        success: function(response) {
            try {
                response = typeof response === 'string' ? JSON.parse(response) : response;
                if (response.success) {
                    // Populate the passwords table
                    var html = '';
                    response.users.forEach(function(user) {
                        html += '<tr>';
                        html += '<td>' + user.name + '</td>';
                        html += '<td>' + user.email + '</td>';
                        html += '<td>' + user.user_type + '</td>';
                        html += '<td>' + user.password + '</td>';
                        html += '</tr>';
                    });
                    $('#passwordsTableBody').html(html);
                    $('#passwordsDisplayModal').modal('show');
                } else {
                    alert(response.message || 'Error fetching user passwords');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Error processing response');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Error connecting to server');
        }
    });
}

function verifyAndTogglePassword(button) {
    // Get the password cell elements
    const passwordCell = button.closest('.password-cell');
    const passwordDots = passwordCell.querySelector('.password-dots');
    const passwordText = passwordCell.querySelector('.password-text');
    const icon = button.querySelector('i');
    
    // Toggle password visibility
    if (passwordText.style.display === 'inline') {
        // Hide password
        passwordDots.style.display = 'inline';
        passwordText.style.display = 'none';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        // Show password
        passwordDots.style.display = 'none';
        passwordText.style.display = 'inline';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script> 