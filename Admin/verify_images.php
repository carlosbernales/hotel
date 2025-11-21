<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'db.php';

// Function to create directories if they don't exist
function createDirectories() {
    $dirs = [
        'uploads/rooms',
        'assets/img/rooms'
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "Created directory: $dir<br>";
            } else {
                echo "Failed to create directory: $dir<br>";
            }
        } else {
            echo "Directory already exists: $dir<br>";
        }
    }
}

// Function to fix image paths in database
function fixImagePaths($con) {
    $query = "UPDATE room_types 
              SET image = CONCAT('/uploads/rooms/', SUBSTRING_INDEX(image, '/', -1))
              WHERE image LIKE '%../uploads/rooms/%'";
    
    if (mysqli_query($con, $query)) {
        echo "Successfully updated image paths in database<br>";
    } else {
        echo "Error updating image paths: " . mysqli_error($con) . "<br>";
    }
}

// Function to verify images
function verifyImages($con) {
    $query = "SELECT room_type_id, image FROM room_types";
    $result = mysqli_query($con, $query);
    
    echo "<h3>Image Status:</h3>";
    echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
    echo "<tr><th>Room ID</th><th>Image Path</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $imagePath = $row['image'];
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['room_type_id']) . "</td>";
        echo "<td>" . htmlspecialchars($imagePath) . "</td>";
        
        if (file_exists($fullPath)) {
            echo "<td style='color: green;'>File exists</td>";
        } else {
            echo "<td style='color: red;'>File missing</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
}

// Function to check default image
function checkDefaultImage() {
    $defaultImage = 'assets/img/rooms/default.jpg';
    if (file_exists($defaultImage)) {
        echo "Default image exists<br>";
    } else {
        echo "Default image is missing<br>";
    }
}

// Main execution
echo "<h2>Image System Verification</h2>";

echo "<h3>Directory Check:</h3>";
createDirectories();

echo "<h3>Database Update:</h3>";
fixImagePaths($con);

echo "<h3>Default Image Check:</h3>";
checkDefaultImage();

verifyImages($con);

// Add upload form
echo "<h3>Upload Images:</h3>";
?>
<form action="upload_images.php" method="post" enctype="multipart/form-data">
    <p>Upload room images:</p>
    <input type="file" name="images[]" multiple accept="image/*"><br><br>
    <input type="submit" value="Upload Images">
</form>

<p>Required directory structure:</p>
<pre>
public_html/
├── uploads/
│   └── rooms/
│       └── (room images here)
└── assets/
    └── img/
        └── rooms/
            └── default.jpg
</pre> 