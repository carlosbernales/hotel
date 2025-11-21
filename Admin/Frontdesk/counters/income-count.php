<?php 
    require_once dirname(__FILE__) . '/../db.php';
    $sql = "SELECT SUM(total_price) as total FROM booking WHERE payment_status = '1'";
    $query = $con->query($sql);
    $result = $query->fetch_assoc();
    echo number_format($result['total']);
?>