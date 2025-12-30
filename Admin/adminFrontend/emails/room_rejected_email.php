<?php
return "
<div style='font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;'>
    <div style='max-width:600px; margin:auto; background:#ffffff; border-radius:6px; overflow:hidden;'>

        <div style='background:#8c783c; color:#fff; padding:15px; text-align:center;'>
            <h2 style='margin:0;'>Casa Estela Boutique Hotel & Cafe</h2>
        </div>

        <div style='padding:20px; color:#333;'>
            <p>Dear <strong>{$booking['first_name']} {$booking['last_name']}</strong>,</p>

            <p>We regret to inform you that your booking request has been <strong style='color:#c0392b;'>rejected</strong>.</p>

            <table style='width:100%; border-collapse:collapse; margin:20px 0;'>
                <tr>
                    <td style='padding:8px; background:#f9f9f9;'><strong>Booking Reference:</strong></td>
                    <td style='padding:8px;'>{$booking['booking_reference']}</td>
                </tr>
                <tr>
                    <td style='padding:8px; background:#f9f9f9;'><strong>Check-in:</strong></td>
                    <td style='padding:8px;'>" . date("F j, Y", strtotime($booking['check_in'])) . "</td>
                </tr>
                <tr>
                    <td style='padding:8px; background:#f9f9f9;'><strong>Check-out:</strong></td>
                    <td style='padding:8px;'>" . date("F j, Y", strtotime($booking['check_out'])) . "</td>
                </tr>
            </table>

            <p><strong>Reason for rejection:</strong></p>
            <p style='background:#f8f8f8; padding:12px; border-left:4px solid #c0392b;'>
                {$reason}
            </p>

            <p>If you have any questions or would like assistance with a future booking, feel free to contact us.</p>

            <p>Kind regards,<br>
            <strong style='color:#8c783c;'>Casa Estela Team</strong></p>
        </div>

        <div style='text-align:center; font-size:12px; color:#777; padding:10px;'>
            © " . date("Y") . " Casa Estela Boutique Hotel & Cafe
        </div>

    </div>
</div>
";
