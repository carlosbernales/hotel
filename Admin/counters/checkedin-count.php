<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as count FROM booking WHERE DATE(check_in) = '$today'";
    $query = $con->query($sql);
    if ($query) {
        $result = $query->fetch_assoc();
        echo $result['count'];
    } else {
        echo "0";
        error_log("Error in checkedin-count.php: " . $con->error);
    }
?>