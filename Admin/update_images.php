<?php
require_once 'db.php';

// Update image paths for each package
$updates = [
    ['name' => 'Couple', 'image' => 'images/couple.jpg'],
    ['name' => 'Friends', 'image' => 'images/friends.jpg'],
    ['name' => 'Family', 'image' => 'images/family.jpg']
];

foreach ($updates as $update) {
    $name = mysqli_real_escape_string($con, $update['name']);
    $image = mysqli_real_escape_string($con, $update['image']);
    
    $sql = "UPDATE table_packages SET image_path = '$image' WHERE package_name = '$name'";
    if (mysqli_query($con, $sql)) {
        echo "Updated image for {$name} package<br>";
    } else {
        echo "Error updating {$name} package: " . mysqli_error($con) . "<br>";
    }
}

echo "Done updating images!";
?> 