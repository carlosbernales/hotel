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
    $message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
    $admin_response = isset($_POST['admin_response']) ? trim($_POST['admin_response']) : '';

    if (!$message_id || empty($admin_response)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Update message with admin response
    $update_sql = "UPDATE contact_messages 
                   SET status = 'replied',
                       admin_response = ?,
                       updated_at = CURRENT_TIMESTAMP
                   WHERE id = ?";

    $stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $admin_response, $message_id);

    if (mysqli_stmt_execute($stmt)) {
        // Get customer email to send notification
        $select_sql = "SELECT first_name, last_name, email, message FROM contact_messages WHERE id = ?";
        $select_stmt = mysqli_prepare($con, $select_sql);
        mysqli_stmt_bind_param($select_stmt, "i", $message_id);
        mysqli_stmt_execute($select_stmt);
        $result = mysqli_stmt_get_result($select_stmt);
        $message_data = mysqli_fetch_assoc($result);

        if ($message_data) {
            // Send email notification to customer
            $to = $message_data['email'];
            $subject = "Response from Casa Estela Boutique Hotel";
            
            // Create HTML email body
            $email_body_html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #ffc107; color: #000; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #fff; }
                    .message-box { background-color: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #ffc107; }
                    .footer { text-align: center; padding-top: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Casa Estela Boutique Hotel</h2>
                    </div>
                    <div class='content'>
                        <p>Dear " . htmlspecialchars($message_data['first_name'] . ' ' . $message_data['last_name']) . ",</p>
                        <p>Thank you for contacting Casa Estela Boutique Hotel. Here is our response to your message:</p>
                        
                        <div class='message-box'>
                            <strong>Your original message:</strong><br>
                            " . nl2br(htmlspecialchars($message_data['message'])) . "
                        </div>
                        
                        <div class='message-box'>
                            <strong>Our response:</strong><br>
                            " . nl2br(htmlspecialchars($admin_response)) . "
                        </div>
                        
                        <p>If you have any further questions, please don't hesitate to contact us.</p>
                        
                        <p>Best regards,<br>
                        Casa Estela Boutique Hotel Team</p>
                    </div>
                    <div class='footer'>
                        This is an automated response. Please do not reply to this email.
                    </div>
                </div>
            </body>
            </html>";
            
            // Create plain text version for email clients that don't support HTML
            $email_body_plain = "Dear " . $message_data['first_name'] . ' ' . $message_data['last_name'] . ",\n\n" .
                               "Thank you for contacting Casa Estela Boutique Hotel. Here is our response to your message:\n\n" .
                               "Your original message:\n" .
                               $message_data['message'] . "\n\n" .
                               "Our response:\n" .
                               $admin_response . "\n\n" .
                               "If you have any further questions, please don't hesitate to contact us.\n\n" .
                               "Best regards,\n" .
                               "Casa Estela Boutique Hotel Team";

            // Email headers
            $boundary = md5(time());
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "From: Casa Estela Boutique Hotel <noreply@casaestela.com>\r\n";
            $headers .= "Reply-To: support@casaestela.com\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"" . $boundary . "\"\r\n";
            
            // Email body with both plain text and HTML versions
            $message = "--" . $boundary . "\r\n" .
                      "Content-Type: text/plain; charset=UTF-8\r\n" .
                      "Content-Transfer-Encoding: 7bit\r\n\r\n" .
                      $email_body_plain . "\r\n\r\n" .
                      "--" . $boundary . "\r\n" .
                      "Content-Type: text/html; charset=UTF-8\r\n" .
                      "Content-Transfer-Encoding: 7bit\r\n\r\n" .
                      $email_body_html . "\r\n\r\n" .
                      "--" . $boundary . "--";

            // Send email
            $mail_sent = mail($to, $subject, $message, $headers);
            
            if ($mail_sent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Response sent successfully',
                    'email' => $to
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send email, but response was saved'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Customer information not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save response: ' . mysqli_error($con)
        ]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 