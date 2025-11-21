<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $menu_item_id = $_POST['menu_item_id'];
        $name = $_POST['addon_name'];
        $price = $_POST['price'];

        // Validate inputs
        if (empty($name) || empty($price)) {
            throw new Exception('Addon name and price are required');
        }

        if (!is_numeric($price) || $price < 0) {
            throw new Exception('Price must be a valid number');
        }

        // Insert into database
        $query = "INSERT INTO menu_items_addons (menu_item_id, name, price) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmt, "isd", $menu_item_id, $name, $price);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error adding addon: " . mysqli_error($con));
        }

        echo json_encode([
            'success' => true,
            'message' => 'Addon added successfully'
        ]);

    } catch (Exception $e) {
        error_log("Error in add_addon.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) {
            mysqli_stmt_close($stmt);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 