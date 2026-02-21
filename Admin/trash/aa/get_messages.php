<?php
session_start();
include 'db_con.php';

$sql = "SELECT m.*, u.username 
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        ORDER BY m.created_at ASC 
        LIMIT 50";
$result = $pdo->query($sql);

$output = '';
while($row = $result->fetch_assoc()) {
    $messageClass = ($row['user_id'] == $_SESSION['user_id']) ? 'sent' : 'received';
    $time = date('H:i', strtotime($row['created_at']));
    
    $output .= '
    <div class="message '.$messageClass.'">
        <div class="message-content">'.htmlspecialchars($row['message']).'</div>
        <div class="message-time">'.$time.'</div>
    </div>';
}
echo $output;
?> 