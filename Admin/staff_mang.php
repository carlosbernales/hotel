<?php
require_once 'db.php';  // Include the database connection file

// Add debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#">
            <img src="img/house.png" alt="Home Icon" style="width: 20px; height: 20px;">
            </a></li>
            <li class="active">Manage Staffs</li>
        </ol>
    </div><!--/.row-->

   

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Employee Details:
                    <a href="index.php?add_emp" class="btn btn-primary pull-right" style="border-radius:5px; margin-left: 10px;">
                        <i class="fas fa-user-plus"></i> Add Employee
                    </a>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_GET['error'])) {
                        echo "<div class='alert alert-danger'>
                                <span class='glyphicon glyphicon-info-sign'></span> &nbsp; Error on Shift Change !
                            </div>";
                    }
                    if (isset($_GET['success'])) {
                        echo "<div class='alert alert-success'>
                                <span class='glyphicon glyphicon-info-sign'></span> &nbsp; Shift Successfully Changed!
                            </div>";
                    }
                    ?>
                    <table class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%"
                           id="staffTable">
                        <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Employee Name</th>
                            <th>Staff</th>
                            <th>Shift</th>
                            <th>Joining Date</th>
                            <th>Change Shift</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Updated query to use the new structure with debugging
                        $staff_query = "SELECT s.*, st.staff_type 
                                      FROM staff s 
                                      JOIN staff_type st ON s.staff_type_id = st.staff_type_id 
                                      ORDER BY s.emp_id DESC";
                        
                        // Debug query
                        error_log("Staff Query: " . $staff_query);
                        
                        $staff_result = mysqli_query($con, $staff_query);
                        
                        if (!$staff_result) {
                            error_log("Query Error: " . mysqli_error($con));
                            echo "<tr><td colspan='7' class='text-center'>Error executing query: " . mysqli_error($con) . "</td></tr>";
                        } else {
                            if (mysqli_num_rows($staff_result) > 0) {
                                $sr_no = 1;
                                while ($staff = mysqli_fetch_assoc($staff_result)) {
                                    // Debug data
                                    error_log("Processing staff record: " . print_r($staff, true));
                                    ?>
                                    <tr>
                                        <td><?php echo $sr_no++; ?></td>
                                        <td><?php echo $staff['emp_name']; ?></td>
                                        <td><?php echo $staff['staff_type']; ?></td>
                                        <td><?php 
                                            $startRaw = isset($staff['shift_start']) ? $staff['shift_start'] : null;
                                            $endRaw = isset($staff['shift_end']) ? $staff['shift_end'] : null;
                                            $startDisp = ($startRaw && strtotime($startRaw) !== false) ? date('h:i A', strtotime($startRaw)) : 'N/A';
                                            $endDisp = ($endRaw && strtotime($endRaw) !== false) ? date('h:i A', strtotime($endRaw)) : 'N/A';
                                            echo $startDisp . ' - ' . $endDisp; 
                                        ?></td>
                                        <td><?php echo date('M j, Y', strtotime($staff['joining_date'])); ?></td>
                                        <td>
                                            <button class="btn btn-warning" style="border-radius:0%" data-toggle="modal" 
                                                    data-target="#changeShiftModal<?php echo $staff['emp_id']; ?>">
                                                Change Shift
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-info" style="border-radius:60px;" data-toggle="modal" 
                                                    data-target="#viewModal<?php echo $staff['emp_id']; ?>" title="View Employee">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" style="border-radius:60px;" data-toggle="modal" 
                                                    data-target="#empDetail<?php echo $staff['emp_id']; ?>" title="Edit Employee">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <a href="functionmis.php?empid=<?php echo $staff['emp_id']; ?>" 
                                               class="btn btn-danger" style="border-radius:60px;" 
                                               onclick="return confirm('Are you sure you want to delete this employee?')" 
                                               title="Delete Employee">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No employees found</td></tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
        <p class="back-link">Casa Estella Boutique Hotel & Cafe</p>
        </div>
    </div>

</div>    <!--/.main-->

<!-- Change Shift Modal -->
<?php
$staff_query = "SELECT s.*, st.staff_type 
                FROM staff s 
                JOIN staff_type st ON s.staff_type_id = st.staff_type_id 
                ORDER BY s.emp_id DESC";
$staff_result = mysqli_query($con, $staff_query);

if (mysqli_num_rows($staff_result) > 0) {
    while ($staff = mysqli_fetch_assoc($staff_result)) { ?>
        <div class="modal fade" id="changeShiftModal<?php echo $staff['emp_id']; ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Change Shift - <?php echo $staff['emp_name']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="functionmis.php" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Shift Timing</label>
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="time" class="form-control" name="shift_start" required>
                                    </div>
                                    <div class="col-md-2 text-center" style="padding-top: 8px;">
                                        to
                                    </div>
                                    <div class="col-md-5">
                                        <input type="time" class="form-control" name="shift_end" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="emp_id" value="<?php echo $staff['emp_id']; ?>">
                            <input type="hidden" name="action" value="change_shift">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php }
} ?>

<!-- Edit Employee Modal -->
<?php
$staff_query = "SELECT s.*, st.staff_type 
                FROM staff s 
                JOIN staff_type st ON s.staff_type_id = st.staff_type_id 
                ORDER BY s.emp_id DESC";
$staff_result = mysqli_query($con, $staff_query);

if (mysqli_num_rows($staff_result) > 0) {
    while ($staff = mysqli_fetch_assoc($staff_result)) {
        ?>
        <div id="empDetail<?php echo $staff['emp_id']; ?>" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Employee - <?php echo $staff['emp_name']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="functionmis.php" method="post">
                            <div class="form-group">
                                <label>Staff Type</label>
                                <select class="form-control" name="staff_type_id" required>
                                    <?php
                                    $query = "SELECT * FROM staff_type";
                                    $result = mysqli_query($con, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($staff_type = mysqli_fetch_assoc($result)) {
                                            $selected = ($staff_type['staff_type_id'] == $staff['staff_type_id']) ? 'selected' : '';
                                            echo "<option value='{$staff_type['staff_type_id']}' {$selected}>{$staff_type['staff_type']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="first_name" 
                                       value="<?php echo explode(' ', $staff['emp_name'])[0]; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name" 
                                       value="<?php echo isset(explode(' ', $staff['emp_name'])[1]) ? explode(' ', $staff['emp_name'])[1] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="contact_no" 
                                       value="<?php echo $staff['contact_no']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address" 
                                       value="<?php echo $staff['address']; ?>" required>
                            </div>
                            <input type="hidden" name="emp_id" value="<?php echo $staff['emp_id']; ?>">
                            <input type="hidden" name="submit" value="1">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>

<!-- View Employee Modal -->
<?php
$staff_query = "SELECT s.*, st.staff_type 
                FROM staff s 
                JOIN staff_type st ON s.staff_type_id = st.staff_type_id 
                ORDER BY s.emp_id DESC";
$staff_result = mysqli_query($con, $staff_query);

if (mysqli_num_rows($staff_result) > 0) {
    while ($staff = mysqli_fetch_assoc($staff_result)) {
        ?>
        <div id="viewModal<?php echo $staff['emp_id']; ?>" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">View Employee - <?php echo $staff['emp_name']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo $staff['emp_name']; ?></p>
                                <p><strong>Staff Type:</strong> <?php echo $staff['staff_type']; ?></p>
                                <p><strong>Contact Number:</strong> <?php echo $staff['contact_no']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong> <?php echo $staff['address']; ?></p>
                                <p><strong>Joining Date:</strong> <?php echo date('M j, Y', strtotime($staff['joining_date'])); ?></p>
                                <p><strong>Shift:</strong> <?php 
                                    $startRaw = isset($staff['shift_start']) ? $staff['shift_start'] : null;
                                    $endRaw = isset($staff['shift_end']) ? $staff['shift_end'] : null;
                                    $startDisp = ($startRaw && strtotime($startRaw) !== false) ? date('h:i A', strtotime($startRaw)) : 'N/A';
                                    $endDisp = ($endRaw && strtotime($endRaw) !== false) ? date('h:i A', strtotime($endRaw)) : 'N/A';
                                    echo $startDisp . ' - ' . $endDisp; 
                                ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>

<!-- Add DataTable Initialization -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<script>
$(document).ready(function() {
    $('#staffTable').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "language": {
            "emptyTable": "No employees found"
        }
    });
});
</script>
</body>