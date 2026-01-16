<?php
return "
<div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>

    <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 6px; overflow: hidden;'>

        <!-- HEADER -->
        <div style='background-color: #8c783c; color: #ffffff; text-align: center; padding: 15px;'>
            <h2 style='margin: 0;'>Casa Estela Boutique Hotel & Cafe</h2>
        </div>

        <!-- BODY -->
        <div style='padding: 25px; color: #333;'>
            <p>Dear <strong style='color:#8c783c;'>{$order['firstname']} {$order['lastname']}</strong>,</p>

            <p>
                We regret to inform you that your table booking has been <strong style='color:#e74c3c;'>REJECTED</strong>. ❌
            </p>

            <p><strong>Reason:</strong> {$reason}</p>

            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <tr style='background-color: #f7f7f7;'>
                    <th colspan='2' style='padding: 12px; text-align: left; border-bottom: 2px solid #8c783c;'>Booking Details</th>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #eee; width: 40%; background-color: #fafafa;'><strong>Order ID</strong></td>
                    <td style='padding: 10px; border: 1px solid #eee;'>{$order['order_id']}</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #eee; background-color: #fafafa;'><strong>Booking Date</strong></td>
                    <td style='padding: 10px; border: 1px solid #eee;'>" . date("F j, Y h:i A", strtotime($order['date_time'])) . "</td>
                </tr>
            </table>

            <p>
                Please contact us if you have any questions or would like to reschedule.
            </p>

            <p style='margin-bottom:0;'>
                Best regards,<br>
                <strong style='color:#8c783c;'>The Casa Estela Team</strong>
            </p>
        </div>

        <!-- FOOTER -->
        <div style='text-align:center; font-size:12px; color:#777; padding:10px;'>
            &copy; " . date("Y") . " Casa Estela Boutique Hotel & Cafe. All rights reserved.
        </div>

    </div>
</div>
";
?>