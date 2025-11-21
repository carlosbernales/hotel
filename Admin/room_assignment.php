<?php
require_once 'db.php';
include 'header.php';
include 'sidebar.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Process form submission for assigning room numbers
if (isset($_POST['update_room_numbers'])) {
    if (isset($_POST['booking_id']) && isset($_POST['room_number'])) {
        $booking_ids = $_POST['booking_id'];
        $room_numbers = $_POST['room_number'];
        $success_count = 0;
        $error_count = 0;
        
        // Start transaction
        mysqli_begin_transaction($con);
        
        try {
            foreach ($booking_ids as $index => $booking_id) {
                if (!empty($room_numbers[$index])) {
                    $room_number = mysqli_real_escape_string($con, $room_numbers[$index]);
                    $booking_id = intval($booking_id);
                    
                    $update_query = "UPDATE bookings SET room_number = '$room_number' WHERE booking_id = $booking_id";
                    if (mysqli_query($con, $update_query)) {
                        $success_count++;
                    } else {
                        $error_count++;
                        echo "<div class='alert alert-danger'>Error updating booking ID $booking_id: " . mysqli_error($con) . "</div>";
                    }
                }
            }
            
            // Commit the transaction if everything is successful
            mysqli_commit($con);
            
            echo "<div class='alert alert-success'>
                <h4>Update Results</h4>
                <p>Successfully updated $success_count bookings.</p>";
            if ($error_count > 0) {
                echo "<p>Failed to update $error_count bookings.</p>";
            }
            echo "</div>";
            
        } catch (Exception $e) {
            // Rollback the transaction on error
            mysqli_rollback($con);
            echo "<div class='alert alert-danger'>Transaction failed: " . $e->getMessage() . "</div>";
        }
    }
}

// Check if a specific status was requested
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$status_condition = "";

if ($status_filter === 'pending') {
    $status_condition = "WHERE b.status = 'Pending'";
} elseif ($status_filter === 'checked_in') {
    $status_condition = "WHERE b.status = 'Checked in'";
} elseif ($status_filter === 'no_room') {
    $status_condition = "WHERE (b.room_number IS NULL OR b.room_number = '')";
} elseif ($status_filter === 'with_room') {
    $status_condition = "WHERE b.room_number IS NOT NULL AND b.room_number != ''";
}
?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Room Number Assignment</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="panel-title">Assign Room Numbers to Bookings</h3>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="btn-group">
                                <a href="room_assignment.php" class="btn btn-default <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
                                <a href="room_assignment.php?status=pending" class="btn btn-default <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                                <a href="room_assignment.php?status=checked_in" class="btn btn-default <?php echo $status_filter === 'checked_in' ? 'active' : ''; ?>">Checked In</a>
                                <a href="room_assignment.php?status=no_room" class="btn btn-default <?php echo $status_filter === 'no_room' ? 'active' : ''; ?>">No Room #</a>
                                <a href="room_assignment.php?status=with_room" class="btn btn-default <?php echo $status_filter === 'with_room' ? 'active' : ''; ?>">With Room #</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <?php
                    // Get bookings with room type information
                    $query = "SELECT b.booking_id, b.first_name, b.last_name, b.room_number, 
                              b.check_in, b.check_out, b.status, rt.room_type, rt.room_type_id
                              FROM bookings b 
                              LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id 
                              $status_condition
                              ORDER BY b.check_in DESC 
                              LIMIT 100";

                    $result = mysqli_query($con, $query);

                    if (!$result) {
                        echo "<div class='alert alert-danger'>Error retrieving bookings: " . mysqli_error($con) . "</div>";
                    } elseif (mysqli_num_rows($result) > 0) {
                        // Display form for adding/editing room numbers
                        echo "<form method='post' action=''>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-bordered table-striped'>";
                        echo "<thead><tr>
                                <th>Booking ID</th>
                                <th>Guest Name</th>
                                <th>Room Type</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Room Number</th>
                              </tr></thead>";
                        echo "<tbody>";
                        
                        while ($booking = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$booking['booking_id']}</td>";
                            echo "<td>{$booking['first_name']} {$booking['last_name']}</td>";
                            echo "<td>{$booking['room_type']} (ID: {$booking['room_type_id']})</td>";
                            echo "<td>{$booking['check_in']}</td>";
                            echo "<td>{$booking['check_out']}</td>";
                            echo "<td>{$booking['status']}</td>";
                            echo "<td>";
                            echo "<input type='text' class='form-control' name='room_number[]' value='{$booking['room_number']}' placeholder='Enter room #'>";
                            echo "<input type='hidden' name='booking_id[]' value='{$booking['booking_id']}'>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody></table>";
                        echo "</div>";
                        echo "<div class='form-group'>";
                        echo "<button type='submit' name='update_room_numbers' class='btn btn-primary'>Update Room Numbers</button>";
                        echo "</div>";
                        echo "</form>";
                        
                        // Show guidance for room number format
                        echo "<div class='panel panel-info'>";
                        echo "<div class='panel-heading'><h4 class='panel-title'>Room Number Format Guidance</h4></div>";
                        echo "<div class='panel-body'>";
                        echo "<p>When assigning room numbers, follow a consistent format such as:</p>";
                        echo "<ul>";
                        echo "<li><strong>Floor + Room Number:</strong> 101, 102, 201, 202, etc.</li>";
                        echo "<li><strong>Wing + Number:</strong> A101, B202, etc.</li>";
                        echo "<li><strong>Building + Floor + Room:</strong> M101, H202, etc.</li>";
                        echo "</ul>";
                        echo "<p>Choose a format that makes sense for your property layout.</p>";
                        echo "</div>";
                        echo "</div>";
                    } else {
                        echo "<div class='alert alert-info'>No bookings found with the selected filter.</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 