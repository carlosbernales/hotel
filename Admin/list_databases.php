<?php
// Database connection
$con = mysqli_connect("localhost", "root", "");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// List all databases
$result = mysqli_query($con, "SHOW DATABASES");
if ($result) {
    echo "Available databases:\n";
    while ($row = mysqli_fetch_row($result)) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "Error listing databases: " . mysqli_error($con) . "\n";
}

mysqli_close($con);
?>
