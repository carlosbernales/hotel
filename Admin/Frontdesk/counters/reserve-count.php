<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $sql = "SELECT COUNT(*) as count FROM booking WHERE payment_status = 0";
    $query = $con->query($sql);
    if ($query) {
        $result = $query->fetch_assoc();
        echo $result['count'];
    } else {
        echo "0";
        error_log("Error in reserve-count.php: " . $con->error);
    }
?>