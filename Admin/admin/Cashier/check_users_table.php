<?php
require "db.php";

echo "<h2>Users Table Structure:</h2>";

// Get table structure
$sql = "DESCRIBE users";
$result = $connection->query($sql);

if (!$result) {
    die("Error checking table structure: " . $connection->error);
}

echo "<table border='1'>
<tr>
    <th>Field</th>
    <th>Type</th>
    <th>Null</th>
    <th>Key</th>
    <th>Default</th>
    <th>Extra</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check if there are any records
echo "<h2>Sample Data:</h2>";
$sql = "SELECT * FROM users LIMIT 5";
$result = $connection->query($sql);

if (!$result) {
    die("Error checking data: " . $connection->error);
}

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    
    // Print header row
    $first_row = $result->fetch_assoc();
    echo "<tr>";
    foreach ($first_row as $key => $value) {
        echo "<th>" . htmlspecialchars($key) . "</th>";
    }
    echo "</tr>";
    
    // Print first row
    echo "<tr>";
    foreach ($first_row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
    
    // Print remaining rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No records found in users table";
}

// Check the orders table for user_id values
echo "<h2>Orders Table User IDs:</h2>";
$sql = "SELECT DISTINCT user_id FROM orders";
$result = $connection->query($sql);

if (!$result) {
    die("Error checking orders: " . $connection->error);
}

echo "User IDs found in orders table: ";
$userIds = [];
while ($row = $result->fetch_assoc()) {
    $userIds[] = $row['user_id'];
}
echo implode(", ", $userIds);
?> 