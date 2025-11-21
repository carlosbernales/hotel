<?php
// Start session at the very beginning
session_start();

// Basic PHP file for editing discount types
require_once 'db.php';
include('header.php');
include('sidebar.php');

// Initialize variables
$error_message = '';
$id = '';
$name = '';
$percentage = '';
$description = '';
$status = 'active'; // Default value

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Get discount data
    $query = "SELECT * FROM discount_types WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $discount = mysqli_fetch_assoc($result);
        $name = $discount['name'];
        $percentage = $discount['percentage'];
        $description = $discount['description'];
        $status = $discount['status'];
    } else {
        $error_message = "Discount type not found.";
    }
} else {
    $error_message = "No discount ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $percentage = mysqli_real_escape_string($con, $_POST['percentage']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $status = isset($_POST['status']) && $_POST['status'] === '1' ? 'active' : 'inactive';
    
    if (empty($name) || empty($percentage)) {
        $error_message = "Name and percentage are required.";
    } else {
        // Start transaction
        mysqli_begin_transaction($con);
        
        try {
            // Get the old name before updating
            $old_name_query = "SELECT name FROM discount_types WHERE id = '$id'";
            $old_name_result = mysqli_query($con, $old_name_query);
            $old_name_row = mysqli_fetch_assoc($old_name_result);
            $old_name = $old_name_row['name'];
            
            // Update discount type
            $update_query = "UPDATE discount_types 
                            SET name = '$name', 
                                percentage = '$percentage', 
                                description = '$description', 
                                status = '$status'
                            WHERE id = '$id'";
            
            if (mysqli_query($con, $update_query)) {
                // If name has changed, update guest_names table
                if ($old_name !== $name) {
                    $update_guest_types = "UPDATE guest_names 
                                         SET guest_type = '$name' 
                                         WHERE guest_type = '$old_name'";
                    mysqli_query($con, $update_guest_types);
                }
                
                // Commit transaction
                mysqli_commit($con);
                
                $_SESSION['message'] = "Discount type updated successfully.";
                $_SESSION['message_type'] = 'success';
                
                // Use JavaScript redirect instead of header()
                echo "<script>window.location.href = 'discount_settings.php';</script>";
                exit;
            } else {
                throw new Exception("Error updating discount type: " . mysqli_error($con));
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($con);
            $error_message = $e->getMessage();
        }
    }
}
?>

<div class="main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <li><a href="discount_settings.php">Discount Settings</a></li>
            <li class="active">Edit Discount Type</li>
        </ol>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Edit Discount Type</h3>
                </div>
                <div class="panel-body">
                    <form action="edit_discount.php?id=<?php echo $id; ?>" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-md-2">Name:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" required 
                                       value="<?php echo htmlspecialchars($name); ?>"
                                       placeholder="e.g., senior, pwd, student">
                                <small class="help-block">Use lowercase without spaces (e.g., 'senior', 'pwd', 'student')</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2">Percentage (%):</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="percentage" required min="0" max="100" step="0.01"
                                       value="<?php echo htmlspecialchars($percentage); ?>"
                                       placeholder="e.g., 10.00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2">Description:</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="description" rows="3" 
                                         placeholder="Description of the discount type"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2">Status:</label>
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="status" value="1" 
                                               <?php echo ($status === 'active') ? 'checked' : ''; ?>>
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Update Discount Type
                                </button>
                                <a href="discount_settings.php" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?> 