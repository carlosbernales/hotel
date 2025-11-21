<?php
require_once 'db.php';

// SQL statements to add status columns
$sql_statements = [
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending'",
    "ALTER TABLE event_bookings ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending'",
    "ALTER TABLE table_bookings ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending'"
];

// Execute each SQL statement
foreach ($sql_statements as $sql) {
    if (mysqli_query($con, $sql)) {
        echo "Successfully executed: $sql<br>";
    } else {
        echo "Error executing: $sql<br>";
        echo "Error message: " . mysqli_error($con) . "<br>";
    }
}

echo "Done adding status columns to all booking tables.";
?>
