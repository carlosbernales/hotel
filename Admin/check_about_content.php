<?php
require_once 'db.php';

// Check if table exists
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'about_content'");
if (mysqli_num_rows($table_check) == 0) {
    // Create table if it doesn't exist
    $create_table = "CREATE TABLE about_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($con, $create_table)) {
        die("Error creating table: " . mysqli_error($con));
    }
    echo "Table created successfully<br>";
}

// Check existing content
$check_content = mysqli_query($con, "SELECT * FROM about_content");
echo "Current content in database:<br>";
while ($row = mysqli_fetch_assoc($check_content)) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Title: " . $row['title'] . "<br>";
    echo "Description: " . $row['description'] . "<br>";
    echo "Last Updated: " . $row['last_updated'] . "<br><br>";
}

// Update content
$update_sql = "UPDATE about_content SET 
               title = 'About Us',
               description = 'Welcome to Casa Estela, where comfort meets elegance.

Located in the heart of the city, our boutique hotel offers a tranquil retreat with modern amenities. Whether you\'re here for business or leisure, our team is dedicated to making your stay unforgettable. Experience warm hospitality and enjoy a home away from home.'
               WHERE id = 2";

if (mysqli_query($con, $update_sql)) {
    echo "Content updated successfully<br>";
} else {
    echo "Error updating content: " . mysqli_error($con) . "<br>";
}

// If no content with id=2 exists, insert it
$insert_sql = "INSERT INTO about_content (id, title, description) 
               SELECT 2, 'About Us', 'Welcome to Casa Estela, where comfort meets elegance.

Located in the heart of the city, our boutique hotel offers a tranquil retreat with modern amenities. Whether you\'re here for business or leisure, our team is dedicated to making your stay unforgettable. Experience warm hospitality and enjoy a home away from home.'
               WHERE NOT EXISTS (SELECT 1 FROM about_content WHERE id = 2)";

if (mysqli_query($con, $insert_sql)) {
    echo "New content inserted successfully<br>";
} else {
    echo "Error inserting content: " . mysqli_error($con) . "<br>";
}

// Show final content
$final_check = mysqli_query($con, "SELECT * FROM about_content");
echo "<br>Final content in database:<br>";
while ($row = mysqli_fetch_assoc($final_check)) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Title: " . $row['title'] . "<br>";
    echo "Description: " . $row['description'] . "<br>";
    echo "Last Updated: " . $row['last_updated'] . "<br><br>";
}
?> 