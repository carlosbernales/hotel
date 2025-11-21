<?php
require_once "db.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Tables Check</h1>";

try {
    // Check if orders table exists
    $result = mysqli_query($con, "SHOW TABLES LIKE 'orders'");
    if (mysqli_num_rows($result) > 0) {
        echo "<h3>Orders Table Structure:</h3>";
        $result = mysqli_query($con, "DESCRIBE orders");
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
        
        // Check orders data
        $sql = "SELECT * FROM orders WHERE status = 'finished' ORDER BY order_date DESC LIMIT 10";
        $result = mysqli_query($con, $sql);
        
        echo "<h3>Recent Finished Orders (max 10):</h3>";
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1' cellpadding='5'>";
            // Table header
            $fields = mysqli_fetch_fields($result);
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>" . $field->name . "</th>";
            }
            echo "</tr>";
            
            // Table data
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
            // Count total finished orders
            $count_result = mysqli_query($con, "SELECT COUNT(*) as total FROM orders WHERE status = 'finished'");
            $count = mysqli_fetch_assoc($count_result);
            echo "<p>Total finished orders: " . $count['total'] . "</p>";
        } else {
            echo "No finished orders found in the orders table";
        }
    } else {
        echo "<h3>Error: The 'orders' table does not exist in the database.</h3>";
    }
    
    echo "<hr>";
    
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
    
    echo "<hr>";

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
    
    echo "<hr>";

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
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
}

// Debug: Show all tables in the database
echo "<hr><h3>All Tables in Database:</h3>";
$result = mysqli_query($con, "SHOW TABLES");
if ($result) {
    echo "<ul>";
    while ($row = mysqli_fetch_row($result)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Could not list tables: " . mysqli_error($con);
}
?>