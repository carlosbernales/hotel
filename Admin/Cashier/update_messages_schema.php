<?php
require_once 'db.php';

try {
    // Update the messages table to include 'cashier' as a valid sender_type
    $sql = "    
    $pdo->exec($sql);
    
    echo "Successfully updated messages table to include 'cashier' as sender_type";
    
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
