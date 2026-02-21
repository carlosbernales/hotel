<?php
require_once 'db_con.php';

header('Content-Type: application/json');

try {
    // Fetch active terms and conditions ordered by display_order
    $query = "SELECT title, rule_text FROM terms_and_conditions WHERE is_active = 1 ORDER BY display_order ASC";
    $result = $con->query($query);
    
    if (!$result) {
        throw new Exception("Error fetching terms and conditions: " . $con->error);
    }
    
    $terms = [];
    while ($row = $result->fetch_assoc()) {
        $terms[] = [
            'title' => $row['title'],
            'content' => $row['rule_text']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $terms
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
