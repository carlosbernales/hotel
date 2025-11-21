<?php
// Page for managing discount types
session_start();
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location:login.php');
    exit();
}

// Include header and sidebar
include_once "header.php";
include_once "sidebar.php";

// Create discount_types table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS discount_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$con->query($create_table);

// Insert default discount types if they don't exist
$check_types = "SELECT COUNT(*) as count FROM discount_types";
$result = $con->query($check_types);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $default_types = [
        ['senior', 10.00, 'Senior Citizen Discount'],
        ['pwd', 10.00, 'Person with Disability Discount'],
        ['student', 10.00, 'Student Discount']
    ];

    $insert_query = "INSERT INTO discount_types (name, percentage, description) VALUES (?, ?, ?)";
    $stmt = $con->prepare($insert_query);

    foreach ($default_types as $type) {
        $stmt->bind_param("sds", $type[0], $type[1], $type[2]);
        $stmt->execute();
    }
}

// Fetch guests with their corresponding discount types
$guest_discount_query = "
    SELECT 
        guest_names.id AS guest_id,
        guest_names.first_name,
        guest_names.last_name,
        guest_names.guest_type,
        discount_types.percentage,
        discount_types.description
    FROM 
        guest_names
    JOIN 
        discount_types ON guest_names.guest_type = discount_types.name
";
$guest_discount_result = $con->query($guest_discount_query);
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="index.php">
                <em class="fa fa-home"></em>
            </a></li>
            <li class="active">Settings</li>
            <li class="active">Discount Types</li>
        </ol>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Add New Discount Type</h2>
                </div>
                <div class="panel-body">
                    <form action="process_discount.php" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-md-2">Name:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" required 
                                       placeholder="e.g., senior, pwd, student">
                                <small class="help-block">Use lowercase without spaces</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-2">Percentage:</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="percentage" value="10.00" readonly>
                                <small class="help-block">Standard 10% discount rate</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-2">Description:</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Discount Type
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Discount Types Table -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Manage Discount Types</h2>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Percentage</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM discount_types ORDER BY name ASC";
                                $result = mysqli_query($con, $query);
                                if ($result && mysqli_num_rows($result) > 0):
                                    while ($discount = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo ucfirst($discount['name']); ?></td>
                                        <td><?php echo number_format($discount['percentage'], 2); ?>%</td>
                                        <td><?php echo $discount['description']; ?></td>
                                        <td>
                                            <span class="label label-<?php echo $discount['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $discount['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_discount.php?id=<?php echo $discount['id']; ?>" 
                                               class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="toggle_discount.php?id=<?php echo $discount['id']; ?>" 
                                               class="btn btn-<?php echo $discount['is_active'] ? 'warning' : 'success'; ?> btn-sm"
                                               title="<?php echo $discount['is_active'] ? 'Disable' : 'Enable'; ?>">
                                                <i class="fa fa-<?php echo $discount['is_active'] ? 'ban' : 'check'; ?>"></i>
                                            </a>
                                            <a href="delete_discount.php?id=<?php echo $discount['id']; ?>" 
                                               class="btn btn-danger btn-sm delete-discount" 
                                               data-id="<?php echo $discount['id']; ?>"
                                               title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No discount types found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guests with Discount Info Table -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Guests with Discount Info</h2>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Guest ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Guest Type</th>
                                    <th>Discount %</th>
                                    <th>Discount Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($guest_discount_result && $guest_discount_result->num_rows > 0):
                                    while ($row = $guest_discount_result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $row['guest_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                        <td><?php echo ucfirst($row['guest_type']); ?></td>
                                        <td><?php echo number_format($row['percentage'], 2); ?>%</td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No matching guest discounts found</td>
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

<script>
$(document).ready(function() {
    // Confirm delete
    $('.delete-discount').on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this discount type?')) {
            window.location.href = $(this).attr('href');
        }
    });

    // Auto hide alerts
    $('.alert').delay(4000).fadeOut(350);
});
</script>
