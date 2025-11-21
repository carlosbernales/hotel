<?php
// Disable displaying errors to prevent JSON corruption in production, but ensure they are logged.
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Ensure errors are logged to this file

require_once 'db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

try {
    if (!isset($con)) {
        throw new Exception('Database connection not established.');
    }

    if (isset($_GET['booking_id'])) {
        $booking_id = mysqli_real_escape_string($con, $_GET['booking_id']);

        $sql = "SELECT 
                    b.booking_id,
                    CONCAT(b.first_name, ' ', b.last_name) as guest_name,
                    b.first_name,
                    b.last_name,
                    b.email,
                    b.contact,
                    b.number_of_guests,
                    b.num_adults,
                    b.num_children,
                    b.check_in,
                    b.check_out,
                    DATEDIFF(b.check_out, b.check_in) as nights,
                    rt.room_type,
                    rt.description as room_description,
                    rt.price as room_price,
                    rt.beds,
                    rn.room_number,
                    b.total_amount,
                    b.amount_paid,
                    b.remaining_balance,
                    b.payment_method,
                    b.discount_type,
                    b.discount_amount,
                    b.extra_charges,
                    b.payment_option,
                    b.downpayment_amount,
                    CASE 
                        WHEN b.payment_option IN ('Partial Payment', 'Custom Payment') THEN b.downpayment_amount
                        WHEN b.payment_option IN ('Full Payment', 'Full') THEN b.amount_paid
                        ELSE 0.00
                    END as amount_paid_display
                FROM bookings b
                LEFT JOIN room_types rt ON b.room_type_id = rt.room_type_id
                LEFT JOIN room_numbers rn ON b.room_number = rn.room_number
                WHERE b.booking_id = ?";

        $stmt = $con->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $booking_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $response['success'] = true;
                    $response['data'] = $result->fetch_assoc();
                } else {
                    $response['message'] = 'Booking not found.';
                }
            } else {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare statement: ' . $con->error);
        }
    } else {
        throw new Exception('Booking ID not provided.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Error in get_booking_details.php: " . $e->getMessage());
}

echo json_encode($response);

// Close database connection (optional, as PHP script will terminate and close it automatically)
// mysqli_close($con);
?>
