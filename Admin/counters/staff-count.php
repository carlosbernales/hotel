<?php 
    require_once dirname(__FILE__) . '/../db.php';
    // First, let's just count all staff without any conditions
    $sql = "SELECT COUNT(*) as count FROM staff WHERE shift_id IS NOT NULL";
    $query = $con->query($sql);
    if ($query !== false) {
        $result = $query->fetch_assoc();
        echo $result['count'];
    } else {
        echo "0";
        // Add more detailed error logging
        error_log("Database query failed in staff-count.php: " . $con->error);
    }
?>