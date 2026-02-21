<?php
require_once '../Customer/aa/includes/Mailer.php';

/**
 * Cafe Mailer Class
 * Handles sending order information via email for cafe orders
 */
class CafeMailer {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new Mailer();
    }
    
    /**
     * Send order confirmation email to customer
     * 
     * @param array $orderData Order information including:
     *   - customer_name: Customer name
     *   - customer_email: Customer email
     *   - order_id: Order ID
     *   - items: Array of ordered items
     *   - total_amount: Total order amount
     *   - downpayment_amount: Downpayment amount
     *   - remaining_balance: Remaining balance
     *   - payment_method: Payment method (GCash, Cash, etc.)
     *   - payment_type: Payment type (Full Payment, Downpayment)
     *   - order_date: Order date
     *   - estimated_time: Estimated pickup/delivery time
     * 
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendOrderConfirmation($orderData) {
        $subject = "Order Confirmation - #" . $orderData['order_id'];
        $body = $this->generateOrderConfirmationTemplate($orderData);
        
        return $this->mailer->sendEmail($orderData['customer_email'], $subject, $body);
    }
    
    /**
     * Send order notification to admin/cafe staff
     * 
     * @param array $orderData Order information
     * @param string $adminEmail Admin email address
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendOrderNotification($orderData, $adminEmail) {
        $subject = "New Order Received - #" . $orderData['order_id'];
        $body = $this->generateOrderNotificationTemplate($orderData);
        
        return $this->mailer->sendEmail($adminEmail, $subject, $body);
    }
    
    /**
     * Generate HTML template for order confirmation email
     * 
     * @param array $orderData Order information
     * @return string HTML email template
     */
    private function generateOrderConfirmationTemplate($orderData) {
        $itemsHtml = '';
        foreach ($orderData['items'] as $item) {
            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['name']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['quantity']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>₱" . number_format($item['price'], 2) . "</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>₱" . number_format($item['subtotal'], 2) . "</td>
                </tr>
            ";
        }
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #333; margin: 0; }
                .order-info { background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .order-info h3 { margin-top: 0; color: #333; }
                .order-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .order-table th { background-color: #333; color: white; padding: 10px; text-align: left; }
                .order-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .total-row { font-weight: bold; background-color: #f9f9f9; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .payment-info { background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .payment-info h4 { margin-top: 0; color: #2e7d2e; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Order Confirmation</h1>
                    <p>Thank you for your order!</p>
                </div>
                
                <div class='order-info'>
                    <h3>Order Details</h3>
                    <p><strong>Order ID:</strong> #{$orderData['order_id']}</p>
                    <p><strong>Customer Name:</strong> {$orderData['customer_name']}</p>
                    <p><strong>Order Date:</strong> {$orderData['order_date']}</p>
                    <p><strong>Estimated Time:</strong> {$orderData['estimated_time']}</p>
                </div>
                
                <h3>Order Items</h3>
                <table class='order-table'>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style='text-align: center;'>Qty</th>
                            <th style='text-align: right;'>Price</th>
                            <th style='text-align: right;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                    <tfoot>
                        <tr class='total-row'>
                            <td colspan='3' style='text-align: right; padding: 15px 10px;'>Total Amount:</td>
                            <td style='text-align: right; padding: 15px 10px;'>₱" . number_format($orderData['total_amount'], 2) . "</td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class='payment-info'>
                    <h4>Payment Information</h4>
                    <p><strong>Payment Type:</strong> {$orderData['payment_type']}</p>
                    <p><strong>Payment Method:</strong> {$orderData['payment_method']}</p>";
                    
        if ($orderData['payment_type'] === 'Downpayment') {
            $body .= "
                    <p><strong>Downpayment Paid:</strong> ₱" . number_format($orderData['downpayment_amount'], 2) . "</p>
                    <p><strong>Remaining Balance:</strong> ₱" . number_format($orderData['remaining_balance'], 2) . "</p>
                    <p><em>Please pay the remaining balance upon order collection.</em></p>";
        }
        
        $body .= "
                </div>
                
                <div class='footer'>
                    <p>We'll notify you when your order is ready for collection.</p>
                    <p>For inquiries, please contact our cafe staff.</p>
                    <p>Thank you for choosing our cafe! 🍰</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Generate HTML template for order notification email (to admin)
     * 
     * @param array $orderData Order information
     * @return string HTML email template
     */
    private function generateOrderNotificationTemplate($orderData) {
        $itemsHtml = '';
        foreach ($orderData['items'] as $item) {
            $itemsHtml .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item['name']}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['quantity']}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>₱" . number_format($item['subtotal'], 2) . "</td>
                </tr>
            ";
        }
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { color: #d32f2f; margin: 0; }
                .order-info { background-color: #fff3e0; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ff9800; }
                .order-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .order-table th { background-color: #333; color: white; padding: 10px; text-align: left; }
                .order-table td { padding: 8px; border-bottom: 1px solid #ddd; }
                .total-row { font-weight: bold; background-color: #f9f9f9; }
                .urgent { background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; text-align: center; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔔 NEW ORDER RECEIVED</h1>
                </div>
                
                <div class='urgent'>
                    Please process this order immediately
                </div>
                
                <div class='order-info'>
                    <h3>Order Information</h3>
                    <p><strong>Order ID:</strong> #{$orderData['order_id']}</p>
                    <p><strong>Customer:</strong> {$orderData['customer_name']}</p>
                    <p><strong>Email:</strong> {$orderData['customer_email']}</p>
                    <p><strong>Order Date:</strong> {$orderData['order_date']}</p>
                    <p><strong>Estimated Time:</strong> {$orderData['estimated_time']}</p>
                    <p><strong>Payment Type:</strong> {$orderData['payment_type']}</p>
                    <p><strong>Payment Method:</strong> {$orderData['payment_method']}</p>";
                    
        if ($orderData['payment_type'] === 'Downpayment') {
            $body .= "
                    <p><strong>Downpayment:</strong> ₱" . number_format($orderData['downpayment_amount'], 2) . "</p>
                    <p><strong>Balance Due:</strong> ₱" . number_format($orderData['remaining_balance'], 2) . "</p>";
        }
        
        $body .= "
                </div>
                
                <h3>Order Items</h3>
                <table class='order-table'>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style='text-align: center;'>Qty</th>
                            <th style='text-align: right;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                    <tfoot>
                        <tr class='total-row'>
                            <td colspan='2' style='text-align: right; padding: 15px 8px;'>Total:</td>
                            <td style='text-align: right; padding: 15px 8px;'>₱" . number_format($orderData['total_amount'], 2) . "</td>
                        </tr>
                    </tfoot>
                </table>
                
                <p style='text-align: center; color: #666; margin-top: 30px;'>
                    Please update the order status when ready.
                </p>
            </div>
        </body>
        </html>";
    }
}
?>
