<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

include '../adminBackend/mydb.php';

if (!isset($_POST['order_id'], $_POST['reason'])) {
    exit("Invalid data.");
}

$order_id = (int) $_POST['order_id'];
$reason = trim($_POST['reason']);

$update = $conn->prepare("UPDATE orders_table SET status = 'Rejected', reject_reason = ? WHERE id = ?");
$update->bind_param("si", $reason, $order_id);
$update->execute();

$stmt = $conn->prepare("
    SELECT firstname, lastname, email, order_id, date_time, total
    FROM orders_table
    WHERE id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("Order not found.");
}

$order = $result->fetch_assoc();

$emailTemplatePath = __DIR__ . "/../adminFrontend/emails/table_receipt_rejected.php";
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

    $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela Boutique Hotel & Cafe');
    $mail->addAddress($order['email'], $order['firstname'] . ' ' . $order['lastname']);

    $mail->isHTML(true);
    $mail->Subject = "Table Booking Rejected - Order #" . $order['order_id'];
    $mail->Body = $emailBody;

    $mail->send();

    header("Location: ../index.php?table-booking-pend");
    exit;

} catch (Exception $e) {
    header("Location: ../index.php?table-booking-pend&error=mailfail");
    exit;
}

?>