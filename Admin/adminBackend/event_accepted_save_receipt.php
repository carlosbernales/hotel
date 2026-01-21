<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Mail/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/Mail/phpmailer/phpmailer/src/SMTP.php';

include '../adminBackend/mydb.php';

// Get JSON data from frontend
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['booking_id'], $data['image'])) {
    $event_id = intval($data['booking_id']); // event booking ID
    $image = $data['image'];

    // Decode base64 image
    $imageData = str_replace('data:image/png;base64,', '', $image);
    $imageData = str_replace(' ', '+', $imageData);
    $imageData = base64_decode($imageData);

    $imageDir = __DIR__ . "/acc_event_booking_receipt_images/";
    if (!is_dir($imageDir))
        mkdir($imageDir, 0777, true);

    $imagePath = $imageDir . "receipt_event_$event_id.png";
    if (!file_put_contents($imagePath, $imageData)) {
        exit("Failed to save receipt image.");
    }

    // Fetch event booking info
    $stmt = $conn->prepare("SELECT customer_name, email, booking_refId, event_type, package_name, package_price, overtime_hours, overtime_charge, extra_guests, extra_guest_charge, total_amount, paid_amount, remaining_balance, payment_method, date_time_start, date_time_end, number_of_guests FROM event_bookings WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0)
        exit("Event booking not found.");

    $booking = $result->fetch_assoc();

    // Email body template
    $emailTemplatePath = __DIR__ . "/../adminFrontend/emails/acp_event_receipt_email.php";
    $emailBody = include $emailTemplatePath; // Make sure this template echoes/returns HTML

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaestelaboutiquehotelandcafe@gmail.com';
        $mail->Password = 'vcagmikptjlcqqrl'; // Make sure this is app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('casaestelaboutiquehotelandcafe@gmail.com', 'Casa Estela Boutique Hotel & Cafe');
        $mail->addAddress($booking['email'], $booking['customer_name']);

        $mail->addAttachment($imagePath, "Event_Booking_Receipt.png");

        $mail->isHTML(true);
        $mail->Subject = "Event Booking Receipt - Reference: " . $booking['booking_refId'];
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