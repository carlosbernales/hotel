# PayPal Integration for Cafe Ordering System

## Overview
This integration allows customers to pay for their cafe orders using PayPal instead of PayMongo. The system will first attempt to process payments through PayPal, and if that fails, it will fall back to PayMongo as a backup.

## Files Modified/Created

### 1. `cafe_payment_information.php` (Modified)
- Updated the "Proceed to Payment" button to use PayPal as the primary payment gateway
- Added fallback to PayMongo if PayPal fails
- Enhanced error handling and user feedback

### 2. `cafe_paypal_checkout.php` (New)
- Handles PayPal API integration
- Creates PayPal orders and redirects users to PayPal for payment
- Processes PayPal's API responses

## Setup Instructions

### PayPal Developer Account Setup
1. Go to [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/)
2. Create a new application or use an existing one
3. Get your Client ID and Client Secret

### Configuration
1. Open `cafe_paypal_checkout.php`
2. Replace the placeholder credentials:
   ```php
   $clientId = 'YOUR_PAYPAL_CLIENT_ID';
   $clientSecret = 'YOUR_PAYPAL_CLIENT_SECRET';
   ```

### Testing (Sandbox Mode)
- The integration is configured to use PayPal's sandbox environment by default
- Use PayPal sandbox test accounts for testing
- No real money will be transacted during testing

### Production Deployment
To switch to live mode:
1. Change the API endpoint from sandbox to live:
   ```php
   $tokenUrl = 'https://api-m.paypal.com/v1/oauth2/token';
   $orderUrl = 'https://api-m.paypal.com/v2/checkout/orders';
   ```
2. Use your live PayPal Client ID and Secret

## Payment Flow
1. User clicks "Proceed to Payment"
2. System attempts to create a PayPal order
3. If successful, user is redirected to PayPal for payment
4. After payment, user returns to the success page
5. If PayPal fails, system falls back to PayMongo

## Features
- **Primary Gateway**: PayPal (with fallback to PayMongo)
- **Error Handling**: Comprehensive error messages and fallback mechanisms
- **Session Management**: Tracks payment references and providers
- **Currency Support**: Configured for PHP (Philippine Peso)
- **Security**: Uses PayPal's secure API endpoints

## Troubleshooting
- Check PayPal API credentials are correct
- Ensure server can reach PayPal API endpoints
- Verify PayPal account is properly configured
- Check error logs for detailed error messages

## Notes
- The system maintains backward compatibility with PayMongo
- All existing order processing remains unchanged
- Payment method selection in the UI still shows the original payment method (Maya/GCash) but processes through PayPal
