<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "hotelms");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Test menu_categories table
$sql = "SHOW TABLES LIKE 'menu_categories'";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) == 0) {
    // Create menu_categories table
    $sql = "CREATE TABLE menu_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        display_name VARCHAR(50) NOT NULL
    )";
    mysqli_query($con, $sql);
    
    // Insert default categories
    $sql = "INSERT INTO menu_categories (name, display_name) VALUES 
        ('small-plates', 'SMALL PLATES'),
        ('soup-salad', 'SOUP & SALAD'),
        ('pasta', 'PASTA'),
        ('sandwiches', 'SANDWICHES'),
        ('coffee', 'COFFEE'),
        ('tea', 'TEA'),
        ('ice-blended', 'ICE BLENDED'),
        ('smoothie', 'SMOOTHIE')";
    mysqli_query($con, $sql);
}

// Test menu_items table
$sql = "SHOW TABLES LIKE 'menu_items'";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) == 0) {
    // Create menu_items table
    $sql = "CREATE TABLE menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image_path VARCHAR(255),
        FOREIGN KEY (category_id) REFERENCES menu_categories(id)
    )";
    mysqli_query($con, $sql);
    
    // Insert default items
    $sql = "INSERT INTO menu_items (category_id, name, price, image_path) VALUES 
        (1, 'Hand-cut Potato Fries', 160.00, 'images/menu/fries.jpg'),
        (1, 'Mozzarella Stick', 150.00, 'images/menu/mozzarella.jpg'),
        (1, 'Chicken Wings', 180.00, 'images/menu/wings.jpg'),
        (2, 'Garden Salad', 200.00, 'images/menu/salad.jpg'),
        (2, 'Coconut Salad', 200.00, 'images/menu/coconut_salad.jpg'),
        (3, 'Spaghetti', 270.00, 'images/menu/spaghetti.jpg'),
        (3, 'Carbonara', 250.00, 'images/menu/carbonara.jpg')";
    mysqli_query($con, $sql);
}

// Test if data exists
echo "<h2>Menu Categories:</h2>";
$sql = "SELECT * FROM menu_categories";
$result = mysqli_query($con, $sql);
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . " - Name: " . $row['display_name'] . "<br>";
}

echo "<h2>Menu Items:</h2>";
$sql = "SELECT mi.*, mc.display_name as category_name 
        FROM menu_items mi 
        JOIN menu_categories mc ON mi.category_id = mc.id";
$result = mysqli_query($con, $sql);
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . " - Category: " . $row['category_name'] . " - Name: " . $row['name'] . " - Price: ₱" . $row['price'] . "<br>";
}

mysqli_close($con);
?> 