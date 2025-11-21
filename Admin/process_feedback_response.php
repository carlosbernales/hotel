<?php
require_once 'db.php';
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_id = isset($_POST['feedback_id']) ? intval($_POST['feedback_id']) : 0;
    $admin_response = isset($_POST['admin_response']) ? $_POST['admin_response'] : '';

    if (!$feedback_id || !$admin_response) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Update feedback status and add admin response
    $update_sql = "UPDATE feedback 
                   SET status = 'resolved',
                       admin_response = ?,
                       resolved_at = CURRENT_TIMESTAMP,
                       resolve_status = TRUE
                   WHERE id = ?";

    $stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $admin_response, $feedback_id);

    if (mysqli_stmt_execute($stmt)) {
        // Get the feedback details to send email
        $select_sql = "SELECT first_name, last_name, email, message FROM feedback WHERE id = ?";
        $select_stmt = mysqli_prepare($con, $select_sql);
        mysqli_stmt_bind_param($select_stmt, "i", $feedback_id);
        mysqli_stmt_execute($select_stmt);
        $result = mysqli_stmt_get_result($select_stmt);
        $feedback = mysqli_fetch_assoc($result);

        if ($feedback) {
            // Send email to customer
            $to = $feedback['email'];
            $subject = "Response to Your Feedback - Casa Estela Boutique Hotel";
            
            $message = "Dear " . htmlspecialchars($feedback['first_name']) . " " . htmlspecialchars($feedback['last_name']) . ",\n\n";
            $message .= "Thank you for your feedback. Here is our response to your message:\n\n";
            $message .= "Your original message:\n";
            $message .= htmlspecialchars($feedback['message']) . "\n\n";
            $message .= "Our response:\n";
            $message .= htmlspecialchars($admin_response) . "\n\n";
            $message .= "Best regards,\nCasa Estela Boutique Hotel Team";
            
            $headers = "From: Casa Estela Boutique Hotel <noreply@casaestela.com>\r\n";
            $headers .= "Reply-To: support@casaestela.com\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            // Attempt to send email (don't block the response on email success)
            @mail($to, $subject, $message, $headers);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to update feedback: ' . mysqli_error($con)
        ]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 