<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['booking_id'], $data['image'])) {
    exit("Invalid data.");
}

$event_id = intval($data['booking_id']);
$image = $data['image'];

$conn->begin_transaction();

try {

    $imageData = str_replace('data:image/png;base64,', '', $image);
    $imageData = str_replace(' ', '+', $imageData);
    $imageData = base64_decode($imageData);

    $imageDir = __DIR__ . "/acc_event_booking_receipt_images/";
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0777, true);
    }

    $imagePath = $imageDir . "receipt_event_$event_id.png";

    if (!file_put_contents($imagePath, $imageData)) {
        throw new Exception("Failed to save receipt image.");
    }

    $stmt = $conn->prepare("
        SELECT *
        FROM event_bookings
        WHERE id = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Event booking not found.");
    }

    $booking = $result->fetch_assoc();

    if ($booking['booking_status'] === 'Accepted') {
        throw new Exception("This booking is already accepted.");
    }

    $updateStmt = $conn->prepare("
        UPDATE event_bookings
        SET booking_status = 'Accepted'
        WHERE id = ?
    ");
    $updateStmt->bind_param("i", $event_id);
    $updateStmt->execute();

    $emailSent = false;

    if (!empty($booking['email']) && filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {

        $emailBody = (function () use ($booking) {
            return include __DIR__ . "/../adminFrontend/emails/acp_event_receipt_email.php";
        })();

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
        $mail->Password = 'vcagmikptjlcqqrl'; // ⚠️ Move to secure config in production
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(
            'casaestelaboutiquehotelandcafe@gmail.com',
            'Casa Estela Boutique Hotel & Cafe'
        );

        $mail->addAddress($booking['email'], $booking['customer_name']);
        $mail->addAttachment($imagePath, "Event_Booking_Receipt.png");

        $mail->isHTML(true);
        $mail->Subject = "Event Booking Accepted - Ref: " . $booking['booking_refId'];
        $mail->Body = $emailBody;

        $mail->send();
        $emailSent = true;
    }

    $conn->commit();

    if ($emailSent) {
        echo "Receipt saved, booking accepted, and email sent successfully!";
    } else {
        echo "Receipt saved and booking accepted successfully! (No email provided)";
    }

} catch (Exception $e) {

    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>