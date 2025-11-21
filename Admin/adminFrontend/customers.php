<?php 
include '../adminFrontend/header.php'; 
include '../mydb.php';

$sql = "SELECT first_name, last_name, email, contact_number, is_verified FROM userss";
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom">
        <i class="fas fa-home"></i>
        <span>User Information</span>
    </div>

    <div class="info-card">
        <h4>User Information</h4>
        
        <div class="table-responsive">
            <table id="userTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['contact_number']); ?></td>
                        <td>
                            <?php if ($user['is_verified']): ?>
                                <span style="color: green; font-weight: bold;">Verified</span>
                            <?php else: ?>
                                <span style="color: orange; font-weight: bold;">Pending</span>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../adminFrontend/footer.php'; ?>
