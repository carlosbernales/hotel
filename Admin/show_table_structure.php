<?php
require_once 'db.php';

$sql = "DESCRIBE event_spaces";
$result = $con->query($sql);

if ($result) {
    echo "<h2>Table Structure for event_spaces:</h2>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error getting table structure: " . $con->error;
}
