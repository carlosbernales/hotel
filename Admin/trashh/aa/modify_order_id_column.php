<?php
require 'db_con.php';

try {
    // Start transaction
    $pdo->beginTransaction();

    echo "<pre>Starting database modifications...\n";
    
    // 1. First, drop the foreign key constraint if it exists
    $fkCheck = $pdo->query("SELECT * FROM information_schema.TABLE_CONSTRAINTS 
                           WHERE TABLE_SCHEMA = DATABASE() 
                           AND TABLE_NAME = 'table_bookings' 
                           AND CONSTRAINT_NAME = 'fk_table_bookings_order'");
    
    if ($fkCheck->rowCount() > 0) {
        echo "Dropping foreign key constraint 'fk_table_bookings_order'...\n";
        $pdo->exec("ALTER TABLE table_bookings DROP FOREIGN KEY fk_table_bookings_order");
        echo "✓ Foreign key constraint dropped successfully.\n";
    } else {
        echo "No foreign key constraint found.\n";
    }
    
    // 2. Modify the order_id column to allow NULL
    echo "Modifying order_id column to allow NULL...\n";
    $pdo->exec("ALTER TABLE table_bookings MODIFY COLUMN order_id INT(11) NULL DEFAULT NULL");
    echo "✓ order_id column now allows NULL values.\n";
    
    // 3. Set any existing NULL values to NULL (to clear any default 0 values)
    echo "Updating any existing NULL values...\n";
    $pdo->exec("UPDATE table_bookings SET order_id = NULL WHERE order_id = 0");
    
    // Commit the transaction
    $pdo->commit();
    
    echo "\n✓ All operations completed successfully!\n";
    echo "\nYou can now make reservations without requiring an order_id.\n";
    
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("\nError: " . $e->getMessage() . "\n");
}

echo "</pre>";
?>
