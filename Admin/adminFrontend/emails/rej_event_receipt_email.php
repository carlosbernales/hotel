<?php
/**
 * Rejection email template
 * Expects $booking array and $reason string to be defined in backend before including
 */

return "
<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px;'>

    <div style='background-color: #8c783c; color: #ffffff; padding: 15px 20px; text-align: center;'>
        <h2 style='margin:0;'>Casa Estela Boutique Hotel & Cafe</h2>
        <p style='margin:5px 0 0;'>Event Booking Update</p>
    </div>

    <div style='background-color:#fff; padding:20px;'>

        <p>Dear <strong>{$booking['customer_name']}</strong>,</p>

        <p>We regret to inform you that your event booking has been <strong>rejected</strong>.</p>

        <table style='width:100%; border-collapse:collapse; margin:15px 0;' border='1'>
            <tr>
                <td style='padding:8px;'><strong>Booking Reference</strong></td>
                <td style='padding:8px;'>{$booking['booking_refId']}</td>
            </tr>
            <tr>
                <td style='padding:8px;'><strong>Package</strong></td>
                <td style='padding:8px;'>{$booking['package_name']}</td>
            </tr>
            <tr>
                <td style='padding:8px;'><strong>Event Type</strong></td>
                <td style='padding:8px;'>{$booking['event_type']}</td>
            </tr>
            <tr>
                <td style='padding:8px;'><strong>Guests</strong></td>
                <td style='padding:8px;'>{$booking['number_of_guests']}</td>
            </tr>
            <tr>
                <td style='padding:8px;'><strong>Event Start</strong></td>
                <td style='padding:8px;'>" . date("F j, Y g:i A", strtotime($booking['date_time_start'])) . "</td>
            </tr>
            <tr>
                <td style='padding:8px;'><strong>Event End</strong></td>
                <td style='padding:8px;'>" . date("F j, Y g:i A", strtotime($booking['date_time_end'])) . "</td>
            </tr>
            <tr>
                <td style='padding:8px;'><strong>Rejection Reason</strong></td>
                <td style='padding:8px;'>$reason</td>
            </tr>
        </table>

        <p>We apologize for any inconvenience this may cause. Please feel free to contact us for alternative dates or packages.</p>

        <p>Thank you for considering Casa Estela Boutique Hotel & Cafe.</p>

        <p><strong>Casa Estela Boutique Hotel & Cafe</strong></p>

    </div>

</div>
";
?>