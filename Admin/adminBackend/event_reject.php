<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../adminBackend/mydb.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

if (isset($_GET['event_id'], $_GET['reason'])) {
    $event_id = intval($_GET['event_id']);
    $reason = trim($_GET['reason']);

    if ($reason === '') {
        exit("Rejection reason cannot be empty.");
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE event_bookings SET booking_status = 'Rejected', rejection_reason = ? WHERE id = ?");
        if (!$stmt)
            throw new Exception("Failed to prepare statement: " . $conn->error);
        $stmt->bind_param("si", $reason, $event_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No booking updated. It may already be rejected or does not exist.");
        }

        $stmtFetch = $conn->prepare("SELECT * FROM event_bookings WHERE id = ?");
        $stmtFetch->bind_param("i", $event_id);
        $stmtFetch->execute();
        $result = $stmtFetch->get_result();
        $booking = $result->fetch_assoc();

        if (!$booking)
            throw new Exception("Booking not found.");

        $emailTemplatePath = __DIR__ . "/../adminFrontend/emails/rej_event_receipt_email.php";
        if (!file_exists($emailTemplatePath)) {
            throw new Exception("Email template not found at: $emailTemplatePath");
        }
        $emailBody = include $emailTemplatePath;

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
        $mail->Password = 'vcagmikptjlcqqrl';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela Boutique Hotel & Cafe');
        $mail->addAddress($booking['email'], $booking['customer_name']);

        $mail->isHTML(true);
        $mail->Subject = "Event Booking Update - Ref: " . $booking['booking_refId'];
        $mail->Body = $emailBody;

        $mail->send();

        $conn->commit();

        header("Location: ../index.php?event-pend-list");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

} else {
    echo "Invalid request. Event ID and rejection reason are required.";
}
?>