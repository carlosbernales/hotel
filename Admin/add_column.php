<?php
require_once 'db.php';

// Check if column exists
$check_column = "SHOW COLUMNS FROM table_type_names LIKE 'is_disabled'";
$result = mysqli_query($con, $check_column);

if (mysqli_num_rows($result) == 0) {
    // Column doesn't exist, so add it
    $add_column = "ALTER TABLE table_type_names ADD COLUMN is_disabled TINYINT(1) DEFAULT 0";
    if (mysqli_query($con, $add_column)) {
        echo "Column 'is_disabled' added successfully!";
    } else {
        echo "Error adding column: " . mysqli_error($con);
    }
} else {
    echo "Column 'is_disabled' already exists!";
}

$con->close();
?> 