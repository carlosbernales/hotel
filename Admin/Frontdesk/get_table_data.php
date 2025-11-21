<?php
require 'db.php';

if (isset($_GET['id'])) {
    $table_id = $_GET['id'];
    
    $query = "SELECT * FROM dining_tables WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($table = $result->fetch_assoc()) {
        echo json_encode($table);
    } else {
        echo json_encode(['error' => 'Table not found']);
    }
} else {
    echo json_encode(['error' => 'No table ID provided']);
}
?>
