<?php
require_once 'db.php';

// Define the correct image paths using existing images
$updates = [
    'Couple' => 'images/couple.jpg',
    'Friends' => 'images/friends.jpg',
    'Family' => 'images/family.jpg',
    'Family Table' => 'images/family.jpg'  // Using the same family image for Family Table
];

foreach ($updates as $package_name => $image_path) {
    $name = mysqli_real_escape_string($con, $package_name);
    $path = mysqli_real_escape_string($con, $image_path);
    
    $sql = "UPDATE table_packages SET image_path = '$path' WHERE package_name = '$name'";
    if (mysqli_query($con, $sql)) {
        echo "Updated image for $name to $path<br>";
    } else {
        echo "Error updating $name: " . mysqli_error($con) . "<br>";
    }
}

// Display current settings
$sql = "SELECT package_name, image_path FROM table_packages";
$result = mysqli_query($con, $sql);

echo "<h3>Current Image Settings:</h3>";
while ($row = mysqli_fetch_assoc($result)) {
    echo htmlspecialchars($row['package_name']) . ": " . htmlspecialchars($row['image_path']) . "<br>";
}
?> 