<?php
require_once 'db.php';

// Function to get about content
function getAboutContent() {
    global $con;
    $query = "SELECT * FROM about_content ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        return false;
    }
    
    return mysqli_fetch_assoc($result);
}

// Function to update about content
function updateAboutContent($title, $description) {
    global $con;
    
    $title = mysqli_real_escape_string($con, $title);
    $description = mysqli_real_escape_string($con, $description);
    
    $query = "UPDATE about_content SET 
              title = '$title', 
              description = '$description',
              last_updated = CURRENT_TIMESTAMP 
              WHERE id = 1";
              
    return mysqli_query($con, $query);
}

// Function to insert new about content
function insertAboutContent($title, $description) {
    global $con;
    
    $title = mysqli_real_escape_string($con, $title);
    $description = mysqli_real_escape_string($con, $description);
    
    $query = "INSERT INTO about_content (title, description) 
              VALUES ('$title', '$description')";
              
    return mysqli_query($con, $query);
}
?> 