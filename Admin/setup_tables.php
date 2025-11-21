<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read and execute SQL file
$sql = file_get_contents('create_tables.sql');

// Split SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

// Execute each statement
foreach ($statements as $statement) {
    if (!empty($statement)) {
        if ($con->query($statement)) {
            echo "Successfully executed: " . substr($statement, 0, 50) . "...\n";
        } else {
            echo "Error executing statement: " . $con->error . "\n";
            echo "Statement was: " . $statement . "\n";
        }
    }
}

echo "\nAll done! Checking tables...\n\n";

// Verify event_packages table
$result = $con->query("SELECT * FROM event_packages");
if ($result) {
    echo "event_packages table exists with " . $result->num_rows . " rows\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['package_name'] . " (₱" . number_format($row['price'], 2) . ")\n";
    }
} else {
    echo "Error checking event_packages table: " . $con->error . "\n";
}

// Verify event_images table
$result = $con->query("SELECT * FROM event_images");
if ($result) {
    echo "\nevent_images table exists with " . $result->num_rows . " rows\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['image_path'] . " (" . $row['caption'] . ")\n";
    }
} else {
    echo "Error checking event_images table: " . $con->error . "\n";
} 