<?php 
    include './db.php';
    $sql = "SELECT * FROM feedback";
    $query = $connection->query($sql);

    echo "$query->num_rows";

?>