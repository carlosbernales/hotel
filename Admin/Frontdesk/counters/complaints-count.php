<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $sql = "SELECT * FROM complaint WHERE resolve_status = '0'";
    $query = $con->query($sql);

    if ($query) {
        echo $query->num_rows;
    } else {
        echo "Error: " . $con->error;
    }
?>
