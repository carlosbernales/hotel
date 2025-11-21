<?php
session_start();
require 'db_con.php';
require 'vendor/autoload.php'; // Make sure you have TCPDF installed

if (!isset($_SESSION['userid'])) {
    header('location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('location:reservations.php');
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['userid'];

try {
    // Get booking details with room information
    $stmt = $pdo->prepare("
        SELECT b.*, GROUP_CONCAT(r.room_name) as room_names,
        u.firstname, u.lastname, u.email, u.phone
        FROM bookings b
        LEFT JOIN booking_rooms br ON b.booking_id = br.booking_id
        LEFT JOIN rooms r ON br.room_id = r.room_id
        LEFT JOIN users u ON b.userid = u.userid
        WHERE b.booking_id = ? AND b.userid = ?
        GROUP BY b.booking_id
    ");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        $_SESSION['error'] = "Invalid booking or unauthorized access.";
        header('location:reservations.php');
        exit();
    }

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Casa Estela');
    $pdf->SetAuthor('Casa Estela');
    $pdf->SetTitle('Booking Confirmation #' . $booking_id);

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add content
    $html = '
    <h1>Casa Estela Booking Confirmation</h1>
    <h2>Booking #' . $booking_id . '</h2>
    
    <h3>Guest Information</h3>
    <p>
    Name: ' . htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']) . '<br>
    Email: ' . htmlspecialchars($booking['email']) . '<br>
    Phone: ' . htmlspecialchars($booking['phone']) . '
    </p>

    <h3>Booking Details</h3>
    <p>
    Room(s): ' . htmlspecialchars($booking['room_names']) . '<br>
    Check-in: ' . date('F j, Y', strtotime($booking['check_in'])) . '<br>
    Check-out: ' . date('F j, Y', strtotime($booking['check_out'])) . '<br>
    Total Amount: ₱' . number_format($booking['total_price'], 2) . '<br>
    Status: ' . ucfirst($booking['status']) . '<br>
    Booking Date: ' . date('F j, Y g:i A', strtotime($booking['created_at'])) . '
    </p>

    <h3>Terms and Conditions</h3>
    <p>
    1. Check-in time is 2:00 PM and check-out time is 12:00 PM.<br>
    2. Early check-in and late check-out are subject to availability.<br>
    3. Cancellation policy applies as per our terms of service.<br>
    4. Valid ID is required upon check-in.<br>
    5. Payment must be settled upon check-in.
    </p>

    <p>Thank you for choosing Casa Estela!</p>
    ';

    // Write HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('Casa_Estela_Booking_' . $booking_id . '.pdf', 'D');

} catch (PDOException $e) {
    $_SESSION['error'] = "Error generating PDF: " . $e->getMessage();
    header('location:reservations.php');
    exit();
}
