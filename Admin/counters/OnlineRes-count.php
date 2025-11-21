<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $sql = "SELECT * FROM booking WHERE status = 'pending'";
    $query = $con->query($sql);
    echo "$query->num_rows";
?>