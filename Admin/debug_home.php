<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to capture any errors
ob_start();

// Try to include the parts of home.php in sequence to identify where it breaks
try {
    // Start with session and database connection
    session_start();
    
    echo "Session started successfully<br>";
    
    // Check if db.php exists and can be loaded
    if (file_exists('../../db.php')) {
        echo "db.php file exists<br>";
        require_once '../../db.php';
        echo "db.php loaded successfully<br>";
    } else {
        echo "ERROR: db.php file does not exist<br>";
    }
    
    // Check for PDO connection
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "PDO connection exists<br>";
    } else if (isset($con) && $con instanceof mysqli) {
        echo "mysqli connection exists<br>";
    } else {
        echo "ERROR: No database connection found<br>";
    }
    
    // Check user session
    if (isset($_SESSION['user_id'])) {
        echo "User is logged in with ID: " . $_SESSION['user_id'] . "<br>";
        
        if (isset($_SESSION['user_type'])) {
            echo "User type: " . $_SESSION['user_type'] . "<br>";
        } else {
            echo "ERROR: user_type not set in session<br>";
        }
    } else {
        echo "WARNING: User is not logged in<br>";
    }
    
    // Test a simple database query
    try {
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT DATABASE()");
            $db_name = $stmt->fetchColumn();
            echo "Connected to database: " . $db_name . "<br>";
        } else if (isset($con)) {
            $result = mysqli_query($con, "SELECT DATABASE()");
            $row = mysqli_fetch_row($result);
            echo "Connected to database: " . $row[0] . "<br>";
        }
    } catch (Exception $e) {
        echo "ERROR executing test query: " . $e->getMessage() . "<br>";
    }
    
    // Check if important tables exist
    $tables_to_check = ['users', 'room_types', 'bookings', 'offers', 'facilities'];
    
    echo "<h3>Checking tables:</h3>";
    foreach ($tables_to_check as $table) {
        try {
            if (isset($pdo)) {
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                if ($stmt->rowCount() > 0) {
                    echo "Table '$table' exists<br>";
                } else {
                    echo "ERROR: Table '$table' does not exist<br>";
                }
            } else if (isset($con)) {
                $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
                if (mysqli_num_rows($result) > 0) {
                    echo "Table '$table' exists<br>";
                } else {
                    echo "ERROR: Table '$table' does not exist<br>";
                }
            }
        } catch (Exception $e) {
            echo "ERROR checking table '$table': " . $e->getMessage() . "<br>";
        }
    }
    
    // Now try to load home.php with output buffering to catch any errors
    echo "<h3>Attempting to load home.php:</h3>";
    
    // Get current buffer contents and clean it
    $debug_output = ob_get_clean();
    echo $debug_output;
    
    // Start a new buffer for home.php
    ob_start();
    
    // Include home.php
    include 'home.php';
    
    // Get and display the output
    $home_output = ob_get_clean();
    
    // If we get here, home.php loaded without fatal errors
    echo "<h3>home.php loaded successfully</h3>";
    
} catch (Exception $e) {
    $error_output = ob_get_clean();
    echo $error_output;
    echo "<h2>Exception caught:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?> 