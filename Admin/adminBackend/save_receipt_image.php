<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['booking_id'], $data['image'])) {
    $booking_id = intval($data['booking_id']);
    $image = $data['image'];

    $imageData = str_replace('data:image/png;base64,', '', $image);
    $imageData = str_replace(' ', '+', $imageData);
    $imageData = base64_decode($imageData);

    $imagePath = __DIR__ . "/room_booking_receipt_images/receipt_$booking_id.png";
    if (!file_put_contents($imagePath, $imageData)) {
        exit("Failed to save receipt image.");
    }

    $stmt = $conn->prepare("SELECT first_name, last_name, email, booking_reference, check_in, check_out, total_amount FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0)
        exit("Booking not found.");
    $booking = $result->fetch_assoc();

    $emailTemplatePath = __DIR__ . "/../adminFrontend/emails/receipt_email.php";
    $emailBody = include $emailTemplatePath;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
        $mail->Password = 'vcagmikptjlcqqrl';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'Casa Estela Boutique Hotel & Cafe');
        $mail->addAddress($booking['email'], $booking['first_name'] . ' ' . $booking['last_name']);

        $mail->addAttachment($imagePath, "Booking_Receipt.png");

        $mail->isHTML(true);
        $mail->Subject = "Booking Receipt - Reference: " . $booking['booking_reference'];
        $mail->Body = $emailBody;

        $mail->send();
        echo "Receipt saved and email sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    echo "Invalid data.";
}
?>