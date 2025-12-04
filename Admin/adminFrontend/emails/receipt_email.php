<?php
return "
<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px;'>
    
    <div style='background-color: #8c783c; color: white; padding: 10px 20px; text-align: center; border-radius: 5px 5px 0 0;'>
        <h2 style='margin: 0;'>Casa Estela Boutique Hotel & Cafe</h2>
    </div>

    <div style='background-color: white; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px;'>

        <p>Dear <strong style='color: #8c783c;'>{$booking['first_name']} {$booking['last_name']}</strong>,</p>

        <p>Thank you for choosing Casa Estela. This is your receipt</p>

        <table style='width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #ddd;'>
            <thead>
                <tr style='background-color: #f0f0f0;'>
                    <th colspan='2' style='padding: 10px; text-align: left; border-bottom: 2px solid #8c783c;'>Reservation Summary</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='padding: 10px; border: 1px solid #eee; width: 40%; background-color: #fafafa;'><strong>Booking Reference:</strong></td>
                    <td style='padding: 10px; border: 1px solid #eee; font-weight: bold;'>{$booking['booking_reference']}</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #eee; width: 40%; background-color: #fafafa;'><strong>Check-in Date:</strong></td>
                    <td style='padding: 10px; border: 1px solid #eee;'>" . date("F j, Y", strtotime($booking['check_in'])) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #eee; width: 40%; background-color: #fafafa;'><strong>Check-out Date:</strong></td>
                    <td style='padding: 10px; border: 1px solid #eee;'>" . date("F j, Y", strtotime($booking['check_out'])) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #eee; width: 40%; background-color: #fafafa;'><strong>Total Amount Due:</strong></td>
                    <td style='padding: 10px; border: 1px solid #eee; color: #cc0000; font-weight: bold;'>₱" . number_format($booking['total_amount'], 2) . "</td>
                </tr>
            </tbody>
        </table>

        <p style='margin-top: 30px;'>Please find your detailed receipt attached to this email.</p>
        
        <p>Thank you and please come again!.</p>

        <p>Best regards,<br>
        <strong style='color: #8c783c;'>The Casa Estela Team</strong></p>

    </div>

    <div style='text-align: center; font-size: 12px; color: #777; padding-top: 10px;'>
        <p>&copy; " . date("Y") . " Casa Estela Boutique Hotel & Cafe. All rights reserved.</p>
    </div>
</div>
";
?>