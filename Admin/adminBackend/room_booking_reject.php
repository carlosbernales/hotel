<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

include '../adminBackend/mydb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error";
    exit;
}

$booking_id = $_POST['booking_id'] ?? null;
$reason = trim($_POST['reason'] ?? '');

if (!$booking_id || !$reason) {
    echo "error";
    exit;
}


$stmt = $conn->prepare("
    UPDATE bookings 
    SET status = 'rejected', rejection_reason = ? 
    WHERE booking_id = ?
");
$stmt->bind_param("si", $reason, $booking_id);

if (!$stmt->execute()) {
    echo "error";
    exit;
}
$stmt->close();


$stmt = $conn->prepare("
    SELECT first_name, last_name, email, booking_reference, check_in, check_out 
    FROM bookings 
    WHERE booking_id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "error";
    exit;
}

$booking = $result->fetch_assoc();
$stmt->close();


$emailTemplate = __DIR__ . "/../adminFrontend/emails/room_rejected_email.php";
$emailBody = include $emailTemplate;


$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
    $mail->Password = 'vcagmikptjlcqqrl';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela Boutique Hotel & Cafe');
    $mail->addAddress($booking['email'], $booking['first_name'] . ' ' . $booking['last_name']);

    $mail->isHTML(true);
    $mail->Subject = "Booking Rejected - Reference {$booking['booking_reference']}";
    $mail->Body = $emailBody;

    $mail->send();
    echo "success";

} catch (Exception $e) {
    echo "error";
}

$conn->close();
