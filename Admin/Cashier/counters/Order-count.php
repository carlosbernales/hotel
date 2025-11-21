<?php 
    include './db.php';
    $sql = "SELECT * FROM orders";
    $query = $connection->query($sql);

    echo "$query->num_rows";

?>