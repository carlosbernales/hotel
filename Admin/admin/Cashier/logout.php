<?php
// Start the session
session_start();

// Check if the user has confirmed to logout
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // Unset session variables and end the session
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    session_abort();
    
    // Redirect to login page
    header('Location: ../../login.php');
    exit();
} else {
    // Prompt user with confirmation before logout
    echo "<script>
    var logoutConfirmed = confirm('Are you sure you want to logout?');
    if (logoutConfirmed) {
        window.location.href = '?confirm=yes'; // Reload with confirmation
    } else {
        window.history.back(); // Go back to the previous page
    }
</script>";
}
?>
