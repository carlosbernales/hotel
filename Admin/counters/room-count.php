<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $sql = "SELECT * FROM room WHERE deleteStatus = '0'";
    $query = $con->query($sql);
    echo "$query->num_rows";
?>