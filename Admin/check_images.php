<?php
require_once 'db.php';

// Define the correct image paths
$images = [
    'Couple' => ['path' => 'images/table1.jpg', 'file' => 'table1.jpg'],
    'Friends' => ['path' => 'images/table2.jpg', 'file' => 'table2.jpg'],
    'Family' => ['path' => 'images/table3.jpg', 'file' => 'table3.jpg']
];

// Update image paths
foreach ($images as $package_name => $image_info) {
    $name = mysqli_real_escape_string($con, $package_name);
    $path = mysqli_real_escape_string($con, $image_info['path']);
    
    // Update the path in database
    $sql = "UPDATE table_packages SET image_path = '$path' WHERE package_name = '$name'";
    if (mysqli_query($con, $sql)) {
        echo "Updated image path for $name to $path<br>";
    } else {
        echo "Error updating $name: " . mysqli_error($con) . "<br>";
    }
}

// Display current paths
$sql = "SELECT package_name, image_path FROM table_packages";
$result = mysqli_query($con, $sql);

echo "<h3>Current Image Paths:</h3>";
while ($row = mysqli_fetch_assoc($result)) {
    echo htmlspecialchars($row['package_name']) . ": " . htmlspecialchars($row['image_path']) . "<br>";
}
?> 