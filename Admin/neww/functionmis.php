<?php
include_once 'db.php';


session_start();


if (isset($_POST['login'])) {
    $email = $_POST['email']; // User input email
    $password = $_POST['password']; // User input password

    // Prepare query to get user info including the hashed password
    $query = "SELECT * FROM userss WHERE email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $email);  // 's' means the parameter is a string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify the password
    if ($result->num_rows > 0) {
        $userdetails = $result->fetch_assoc();
        $hashed_password = $userdetails['password']; // The hashed password from the database
        $user_type = $userdetails['user_type'];

        // Verify the entered password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Store user information in session
            $_SESSION['user_id'] = $userdetails['id'];
            $_SESSION['email'] = $userdetails['email'];
            $_SESSION['user_type'] = $user_type;

            // Redirect based on user type
            if ($user_type == 'admin') {
                header('Location: index.php?dashboard');
            } elseif ($user_type == 'frontdesk') {
                header('Location: casaAdmin/FrontDesk & Cashier/FrontDesk/dashboard.php');
            } elseif ($user_type == 'cashier') {
                header('Location: casaAdmin/FrontDesk & Cashier/Cashier/dashboard.php');
            } else {
                // Redirect to an error page if user type is invalid
                header('Location: login.php?error=invalid_user_type');
            }
            exit;
        } else {
            // Invalid password
            echo "<script>alert('Invalid email or password!');</script>";
        }
    } else {
        // No user found with that email
        echo "<script>alert('Invalid email or password!');</script>";
    }
}




if (isset($_POST['submit'])) {

    $emp_id = $_POST['emp_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $staff_type_id = $_POST['staff_type_id'];
    $shift_id= $_POST['shift_id'];
    $id_card_type = $_POST['id_card_type'];
    $id_card_no = $_POST['id_card_no'];
    $address = $_POST['address'];
    $contact_no = $_POST['contact_no'];
    $joining_date = strtotime($_POST['joining_date']);

    $salary = $_POST['salary'];

    $query="UPDATE staff
SET emp_name='$first_name $last_name', staff_type_id='$staff_type_id', shift_id='$shift_id', id_card_type=$id_card_type,
id_card_no='$id_card_no',address='$address',contact_no='$contact_no',joining_date='$joining_date',salary='$salary'
WHERE emp_id=$emp_id ";
//echo $query;
    if (mysqli_query($connection, $query)) {
        header('Location: index.php?staff_mang');
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }


}

if (isset($_GET['empid'])!="")
{
   $emp_id=$_GET['empid'];
    $deleteQuery = "DELETE FROM staff WHERE emp_id=$emp_id";
    if (mysqli_query($connection, $deleteQuery)) {
        header('Location: index.php?staff_mang');
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}

?>