<?php
require_once 'includes/init.php';

header('Content-Type: application/json');

if (isset($_POST['discount_id'])) {
    $discount_id = (int)$_POST['discount_id'];
    
    $sql = "SELECT * FROM discount_types WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $discount_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'data' => [
                'name' => $row['name'],
                'percentage' => $row['percentage'],
                'description' => $row['description']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Discount type not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No discount ID provided'
    ]);
}
?> 