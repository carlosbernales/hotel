<?php
require_once "db.php";

try {
    // Check reservation_orders table
    $sql = "SELECT * FROM reservation_orders";
    $result = mysqli_query($con, $sql);
    
    echo "<h3>Reservation Orders:</h3>";
    if (mysqli_num_rows($result) > 0) {
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "No orders found in reservation_orders table";
    }

    // Check menu_items table
    $sql = "SELECT * FROM menu_items";
    $result = mysqli_query($con, $sql);
    
    echo "<h3>Menu Items:</h3>";
    if (mysqli_num_rows($result) > 0) {
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "No items found in menu_items table";
    }

    // Check table_reservations table
    $sql = "SELECT * FROM table_reservations";
    $result = mysqli_query($con, $sql);
    
    echo "<h3>Table Reservations:</h3>";
    if (mysqli_num_rows($result) > 0) {
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        echo "No reservations found in table_reservations table";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 