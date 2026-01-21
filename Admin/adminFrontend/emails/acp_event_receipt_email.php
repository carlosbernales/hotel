<?php
return "
<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px;'>

    <div style='background-color: #8c783c; color: #ffffff; padding: 15px 20px; text-align: center; border-radius: 6px 6px 0 0;'>
        <h2 style='margin: 0;'>Casa Estela Boutique Hotel & Cafe</h2>
        <p style='margin: 5px 0 0;'>Event Booking Accepted</p>
    </div>

    <div style='background-color: #ffffff; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px;'>

        <p>
            Dear <strong style='color:#8c783c;'>{$event['customer_name']}</strong>,
        </p>

        <p>
            We are pleased to inform you that your event booking has been
            <strong>successfully accepted</strong>.
        </p>

        <table style='width:100%; border-collapse:collapse; margin:20px 0; border:1px solid #ddd;'>
            <tbody>

                <tr>
                    <td style='padding:10px; background:#fafafa; width:40%;'><strong>Booking Reference</strong></td>
                    <td style='padding:10px; font-weight:bold;'>{$event['booking_refId']}</td>
                </tr>

                <tr>
                    <td style='padding:10px; background:#fafafa;'><strong>Package</strong></td>
                    <td style='padding:10px;'>{$event['package_name']}</td>
                </tr>

                <tr>
                    <td style='padding:10px; background:#fafafa;'><strong>Event Start</strong></td>
                    <td style='padding:10px;'>" . date("F j, Y g:i A", strtotime($event['date_time_start'])) . "</td>
                </tr>

                <tr>
                    <td style='padding:10px; background:#fafafa;'><strong>Event End</strong></td>
                    <td style='padding:10px;'>" . date("F j, Y g:i A", strtotime($event['date_time_end'])) . "</td>
                </tr>

                <tr>
                    <td style='padding:10px; background:#fafafa;'><strong>Total Amount</strong></td>
                    <td style='padding:10px; font-weight:bold; color:#cc0000;'>
                        ₱" . number_format($event['total_amount'], 2) . "
                    </td>
                </tr>

            </tbody>
        </table>

        <p>
            Your complete receipt with full booking details is attached to this email.
        </p>

        <p>
            Thank you for choosing Casa Estela. We look forward to hosting your event.
        </p>

        <p>
            Warm regards,<br>
            <strong style='color:#8c783c;'>The Casa Estela Team</strong>
        </p>

    </div>

    <div style='text-align:center; font-size:12px; color:#777; padding-top:12px;'>
        <p>&copy; " . date("Y") . " Casa Estela Boutique Hotel & Cafe. All rights reserved.</p>
    </div>

</div>
";
?>