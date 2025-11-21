<?php
// Only start session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Check if database connection is successful
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare query to get user info including the hashed password
    $query = "SELECT * FROM userss WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify the password
    if ($result->num_rows > 0) {
        $userdetails = $result->fetch_assoc();
        $hashed_password = $userdetails['password'];
        $user_type = $userdetails['user_type'];

        // Verify the entered password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Store user information in session
            $_SESSION['user_id'] = $userdetails['id'];
            $_SESSION['email'] = $userdetails['email'];
            $_SESSION['user_type'] = $user_type;

            // Redirect based on user type
            switch ($user_type) {
                case 'admin':
                    header('Location: index.php?dashboard');
                    break;
                case 'frontdesk':
                    header('Location: casaAdmin/FrontDesk & Cashier/FrontDesk/dashboard.php');
                    break;
                case 'cashier':
                    header('Location: casaAdmin/FrontDesk & Cashier/Cashier/dashboard.php');
                    break;
                case 'customer':
                    header('Location: casaAdmin/capstone/home.php');
                    break;
                default:
                    header('Location: login.php?error=invalid_user_type');
            }
            exit;
        }
    }
    
    // Invalid login
    echo "<script>alert('Invalid email or password!');</script>";
}

if (isset($_POST['submit'])) {
    $required_fields = ['emp_id', 'first_name', 'staff_type_id', 'contact_no', 'address'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missing_fields[] = str_replace('_', ' ', ucfirst($field));
        }
    }
    
    if (!empty($missing_fields)) {
        $_SESSION['error'] = 'Required fields missing: ' . implode(', ', $missing_fields);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $emp_id = (int)$_POST['emp_id'];
    $first_name = mysqli_real_escape_string($con, trim($_POST['first_name']));
    $staff_type_id = (int)$_POST['staff_type_id'];
    $contact_no = mysqli_real_escape_string($con, trim($_POST['contact_no']));
    $address = mysqli_real_escape_string($con, trim($_POST['address']));

    try {
        mysqli_begin_transaction($con);

        $sql = "UPDATE staff SET 
                emp_name = ?,
                staff_type_id = ?,
                contact_no = ?,
                address = ?
                WHERE emp_id = ?";

        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param('sissi', 
            $first_name,
            $staff_type_id,
            $contact_no,
            $address,
            $emp_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update employee: " . $stmt->error);
        }

        mysqli_commit($con);
        $_SESSION['success'] = 'Employee updated successfully';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($con);
        $_SESSION['error'] = 'Error updating employee: ' . $e->getMessage();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

if (isset($_GET['empid']) && !empty($_GET['empid'])) {
    try {
        $emp_id = (int)$_GET['empid'];
        
        // Start transaction
        mysqli_begin_transaction($con);

        try {
            // Check if employee exists
            $check_sql = "SELECT emp_id FROM staff WHERE emp_id = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param('i', $emp_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows === 0) {
                throw new Exception('Employee not found');
            }

            // Delete employee
            $sql = "DELETE FROM staff WHERE emp_id = ?";
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $con->error);
            }

            $stmt->bind_param('i', $emp_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete employee: " . $stmt->error);
            }

            mysqli_commit($con);
            header('Location: index.php?staff_mang&success=2');
            exit;

        } catch (Exception $e) {
            mysqli_rollback($con);
            throw $e;
        }

    } catch (Exception $e) {
        header('Location: index.php?staff_mang&error=' . urlencode($e->getMessage()));
        exit;
    }
}

// Handle shift change
if (isset($_POST['action']) && $_POST['action'] === 'change_shift') {
    try {
        // Validate required fields
        if (!isset($_POST['emp_id']) || !isset($_POST['shift_start']) || !isset($_POST['shift_end'])) {
            throw new Exception('Shift+Id+is+required');
        }

        $emp_id = (int)$_POST['emp_id'];
        
        // Validate and format the time inputs
        $shift_start = $_POST['shift_start'];
        $shift_end = $_POST['shift_end'];
        
        if (!$shift_start || !$shift_end) {
            throw new Exception('Shift+time+is+required');
        }

        // Convert times to 24-hour format for database
        $shift_start = date('H:i:s', strtotime($shift_start));
        $shift_end = date('H:i:s', strtotime($shift_end));

        // Start transaction
        mysqli_begin_transaction($con);

        try {
            // Check if employee exists
            $check_sql = "SELECT emp_id FROM staff WHERE emp_id = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param('i', $emp_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows === 0) {
                throw new Exception('Employee+not+found');
            }

            // Update shift timing
            $sql = "UPDATE staff SET shift_start = ?, shift_end = ? WHERE emp_id = ?";
            $stmt = $con->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Prepare+failed:+" . $con->error);
            }

            $stmt->bind_param('ssi', $shift_start, $shift_end, $emp_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed+to+update+shift:+" . $stmt->error);
            }

            mysqli_commit($con);
            header('Location: index.php?staff_mang&success=1');
            exit;

        } catch (Exception $e) {
            mysqli_rollback($con);
            throw $e;
        }

    } catch (Exception $e) {
        header('Location: index.php?staff_mang&error=' . str_replace(' ', '+', $e->getMessage()));
        exit;
    }
}
?>