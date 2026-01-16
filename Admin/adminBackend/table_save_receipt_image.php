<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

include '../adminBackend/mydb.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['image'])) {
    exit("Invalid data.");
}

$order_id = (int) $data['id'];
$image = $data['image'];

/* =====================
   SAVE IMAGE
===================== */
$imageData = str_replace('data:image/png;base64,', '', $image);
$imageData = str_replace(' ', '+', $imageData);
$imageData = base64_decode($imageData);

$imagePath = __DIR__ . "/table_booking_receipt_images/receipt_$order_id.png";
if (!file_put_contents($imagePath, $imageData)) {
    exit("Failed to save receipt image.");
}

/* =====================
   UPDATE STATUS
===================== */
$update = $conn->prepare("UPDATE orders_table SET status = 'Accepted' WHERE id = ?");
$update->bind_param("i", $order_id);
$update->execute();

/* =====================
   FETCH ORDER
===================== */
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

/* =====================
   LOAD EMAIL TEMPLATE
===================== */
$emailTemplatePath = __DIR__ . "/../adminFrontend/emails/table_receipt_.php";
$emailBody = include $emailTemplatePath;

/* =====================
   SEND EMAIL (SAME AS WORKING CODE)
===================== */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
    $mail->Password = 'vcagmikptjlcqqrl'; // ✅ SAME AS WORKING
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela Boutique Hotel & Cafe');
    $mail->addAddress(
        $order['email'],
        $order['firstname'] . ' ' . $order['lastname']
    );

    $mail->addAttachment($imagePath, "Table_Booking_Receipt.png");

    $mail->isHTML(true);
    $mail->Subject = "Table Booking Accepted - Order #" . $order['order_id'];
    $mail->Body = $emailBody;

    $mail->send();
    echo "Receipt saved and email sent successfully!";

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
