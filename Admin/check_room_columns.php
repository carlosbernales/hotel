<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Checking room_types table structure...\n";
// Get column info for room_types
$result = mysqli_query($con, "SHOW COLUMNS FROM room_types");
if ($result) {
    echo "\nroom_types columns:\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error getting room_types columns: " . mysqli_error($con) . "\n";
}

// Get sample data from room_types
echo "\nSample room_types data:\n";
$result = mysqli_query($con, "SELECT * FROM room_types LIMIT 3");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "\n";
    }
} else {
    echo "Error getting room_types data: " . mysqli_error($con) . "\n";
}

// Check rooms table structure
echo "\n\nChecking rooms table structure...\n";
$result = mysqli_query($con, "SHOW COLUMNS FROM rooms");
if ($result) {
    echo "\nrooms columns:\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error getting rooms columns: " . mysqli_error($con) . "\n";
}

// Get sample data from rooms
echo "\nSample rooms data:\n";
$result = mysqli_query($con, "SELECT * FROM rooms LIMIT 3");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "\n";
    }
} else {
    echo "Error getting rooms data: " . mysqli_error($con) . "\n";
}

mysqli_close($con);
?>

// Check room_types table structure
echo "=== ROOM_TYPES TABLE STRUCTURE ===\n";
$result = mysqli_query($con, "DESCRIBE room_types");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " (" . $row['Type'] . ") " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

// Check room_numbers table structure
echo "\n=== ROOM_NUMBERS TABLE STRUCTURE ===\n";
$result = mysqli_query($con, "DESCRIBE room_numbers");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " (" . $row['Type'] . ") " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

// Show sample data
echo "\n=== SAMPLE ROOM TYPES ===\n";
$result = mysqli_query($con, "SELECT * FROM room_types LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
    echo "\n";
}

mysqli_close($con);
?>
