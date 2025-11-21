<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers first to ensure proper content type
header('Content-Type: application/json');

// Function to send JSON response and exit
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Function to log debug information
function logDebug($message, $data = null) {
    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    if ($data !== null) {
        $logMessage .= 'Data: ' . print_r($data, true) . "\n";
    }
    error_log($logMessage, 3, __DIR__ . '/paymongo_debug.log');
}

try {
    // Check if the request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
    }

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['success' => false, 'error' => 'Invalid JSON data'], 400);
    }
    
    if (empty($input) || !isset($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
        sendJsonResponse(['success' => false, 'error' => 'Invalid amount provided'], 400);
    }

    // Check if Composer's autoloader exists
    $autoloadPath = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        $errorMsg = 'Composer dependencies not found. Please run "composer install" first.';
        logDebug($errorMsg);
        sendJsonResponse([
            'success' => false,
            'error' => $errorMsg,
            'debug' => ['autoload_path' => $autoloadPath]
        ], 500);
    }

    // Log that we're about to include the autoloader
    logDebug('Including autoloader', ['path' => $autoloadPath]);
    
    try {
        require_once $autoloadPath;
        logDebug('Autoloader included successfully');
    } catch (\Exception $e) {
        $errorMsg = 'Failed to include autoloader: ' . $e->getMessage();
        logDebug($errorMsg, ['exception' => $e->getMessage()]);
        sendJsonResponse([
            'success' => false,
            'error' => 'Failed to initialize payment system',
            'debug' => $errorMsg
        ], 500);
    }

    // Set your PayMongo API keys (in production, use environment variables)
    $publicKey = 'pk_test_xZijiyCoEn7YiX4jBJnQo2xw';
    $secretKey = 'sk_test_MFRCBoj9hzSZbeBWrSDSRTEF';
    
    // Function to create PayMongo checkout using direct cURL
    function createPaymongoCheckoutDirect($amount, $description, $referenceNumber, $publicKey, $secretKey) {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        
        // Get order data from POST or GET
        $orderData = isset($_POST['order_data']) ? $_POST['order_data'] : (isset($_GET['order_data']) ? $_GET['order_data'] : '');
        
        // Create success URL with all necessary parameters
        $successUrl = $baseUrl . '/Admin/Customer/aa/payment_information.php?payment=success';
        if (!empty($orderData)) {
            $successUrl .= '&order_data=' . urlencode($orderData);
        }
        
        // Create cancel URL
        $cancelUrl = $baseUrl . '/Admin/Customer/aa/payment_information.php?payment=cancelled';
        if (!empty($orderData)) {
            $cancelUrl .= '&order_data=' . urlencode($orderData);
        }
        
        $data = [
            'data' => [
                'attributes' => [
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true,
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'description' => $description,
                    'line_items' => [
                        [
                            'amount' => (int)round((float)$amount * 100), // Convert to centavos here
                            'currency' => 'PHP',
                            'name' => 'Cafe Order',
                            'quantity' => 1,
                            'description' => 'Payment for your cafe order'
                        ]
                    ],
                    'payment_method_types' => ['gcash', 'card', 'grab_pay'],
                    'reference_number' => $referenceNumber,
                    'statement_descriptor' => 'CAFE ORDER'
                ]
            ]
        ];
        
        $ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($secretKey . ':')
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMsg = $responseData['errors'][0]['detail'] ?? 'Failed to create checkout session';
            throw new \Exception('PayMongo API Error: ' . $errorMsg);
        }
        
        return $responseData['data']['attributes']['checkout_url'] ?? null;
    }

    $referenceNumber = 'ORD-' . uniqid();
    $amount = (float)$input['amount']; // This is now in pesos from the frontend
    $description = $input['description'] ?? 'Cafe Order Payment';
    
    // Log the payment details for debugging
    logDebug('Processing payment', [
        'reference' => $referenceNumber,
        'amount_received' => $amount,
        'amount_in_centavos' => (int)round($amount * 100),
        'description' => $description
    ]);
    
    // Get item details from order data if available
    $orderData = json_decode($input['order_data'] ?? '[]', true);
    $itemName = 'Cafe Order';
    
    if (!empty($orderData) && is_array($orderData)) {
        // Get the first item name if available
        $firstItem = $orderData[0] ?? [];
        if (isset($firstItem['name'])) {
            $itemName = $firstItem['name'];
        } elseif (isset($orderData['items']) && is_array($orderData['items']) && !empty($orderData['items'])) {
            $firstItem = $orderData['items'][0] ?? [];
            $itemName = $firstItem['name'] ?? $itemName;
        }
        
        // If we have multiple items, append "and X more" to the description
        $itemCount = count($orderData);
        if ($itemCount > 1) {
            $itemName .= ' and ' . ($itemCount - 1) . ' more';
        }
    }
    
    try {
        // Always use the direct cURL method for better reliability
        logDebug('Creating PayMongo checkout with direct cURL method');
        try {
            $checkoutUrl = createPaymongoCheckoutDirect($amount, $description, $referenceNumber, $publicKey, $secretKey);
            
            if (empty($checkoutUrl)) {
                throw new Exception('Failed to create checkout URL');
            }
            
            // Return the checkout URL in the response
            echo json_encode([
                'status' => 'success',
                'checkout_url' => $checkoutUrl,
                'message' => 'Redirecting to payment gateway...'
            ]);
            exit;
            
        } catch (Exception $e) {
            logDebug('Error in createPaymongoCheckoutDirect: ' . $e->getMessage());
            
            // Fall back to SDK method if direct method fails
            logDebug('Direct cURL method failed, falling back to SDK method');
            
            // Check if SDK classes exist before trying to use them
            if (!class_exists('Paymongo\\PaymongoClient') || !class_exists('Paymongo\\Services\\CheckoutService')) {
                throw new Exception('PayMongo SDK classes not found');
            }
            
            // Initialize PayMongo client
            $client = new \Paymongo\PaymongoClient($secretKey);
            $checkout = new \Paymongo\Services\CheckoutService($client);
            
            // Create a checkout session
            $checkoutSession = $checkout->create([
                'data' => [
                    'attributes' => [
                        'send_email_receipt' => true,
                        'show_description' => true,
                        'show_line_items' => true,
                        'success_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/Admin/Customer/aa/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/Admin/Customer/aa/payment_cancel.php',
                        'description' => $description,
                        'line_items' => [
                            [
                                'amount' => (int)round($amount * 100),
                                'currency' => 'PHP',
                                'name' => $itemName,
                                'quantity' => 1,
                                'description' => 'Payment for your cafe order'
                            ]
                        ],
                        'payment_method_types' => ['gcash', 'card', 'grab_pay'],
                        'reference_number' => $referenceNumber,
                        'statement_descriptor' => 'CAFE ORDER'
                    ]
                ]
            ]);
            
            // Get the checkout URL from the response
            $checkoutUrl = $checkoutSession['data']['attributes']['checkout_url'] ?? null;
        }
        
        if (empty($checkoutUrl)) {
            throw new Exception('Failed to get checkout URL from PayMongo');
        }

        // Return success response with checkout URL
        sendJsonResponse([
            'success' => true,
            'checkout_url' => $checkoutUrl,
            'reference_number' => $referenceNumber
        ]);
        
    } catch (\Paymongo\Exceptions\BadRequestException $e) {
        error_log('PayMongo Bad Request: ' . $e->getMessage());
        sendJsonResponse([
            'success' => false,
            'error' => 'Invalid request to payment processor: ' . $e->getMessage()
        ], 400);
    } catch (\Paymongo\Exceptions\AuthenticationException $e) {
        error_log('PayMongo Authentication Error: ' . $e->getMessage());
        sendJsonResponse([
            'success' => false,
            'error' => 'Payment authentication failed. Please contact support.'
        ], 401);
    } catch (\Exception $e) {
        throw $e; // Let the outer catch handle other exceptions
    }

} catch (\Exception $e) {
    // Log the error for debugging
    error_log('PayMongo Checkout Error: ' . $e->getMessage());
    
    // Return a generic error message to the client
    sendJsonResponse([
        'success' => false,
        'error' => 'Failed to process payment. Please try again later.',
        'debug' => $e->getMessage() // Only include in development
    ], 500);
}
