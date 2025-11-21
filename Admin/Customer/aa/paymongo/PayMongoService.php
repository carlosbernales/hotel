<?php
class PayMongoService {
    private $secretKey;
    private $publicKey;
    private $apiUrl = 'https://api.paymongo.com/v1';
    
    public function __construct($isTestMode = true) {
        if ($isTestMode) {
            $this->secretKey = getenv('PAYMONGO_TEST_SECRET_KEY') ?: 'sk_test_xxxxxxxxxxxxxxxx';
            $this->publicKey = getenv('PAYMONGO_TEST_PUBLIC_KEY') ?: 'pk_test_xxxxxxxxxxxxxxxx';
        } else {
            $this->secretKey = getenv('PAYMONGO_LIVE_SECRET_KEY') ?: 'sk_live_xxxxxxxxxxxxxxxx';
            $this->publicKey = getenv('PAYMONGO_LIVE_PUBLIC_KEY') ?: 'pk_live_xxxxxxxxxxxxxxxx';
        }
        
        if (empty($this->secretKey) || strpos($this->secretKey, 'xxxx') !== false || 
            empty($this->publicKey) || strpos($this->publicKey, 'xxxx') !== false) {
            throw new Exception('PayMongo API keys are not properly configured. Please set the PAYMONGO_TEST_SECRET_KEY, PAYMONGO_TEST_PUBLIC_KEY environment variables.');
        }
    }
    
    public function createPaymentIntent($amount, $description, $metadata = []) {
        $url = $this->apiUrl . '/payment_intents';
        
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount * 100, // Convert to centavos
                    'payment_method_allowed' => ['card', 'gcash', 'grab_pay'],
                    'payment_method_options' => [
                        'card' => [
                            'request_three_d_secure' => 'any'
                        ]
                    ],
                    'currency' => 'PHP',
                    'description' => $description,
                    'metadata' => $metadata
                ]
            ]
        ];
        
        return $this->makeRequest('POST', $url, $data);
    }
    
    public function createPaymentMethod($type, $details) {
        $url = $this->apiUrl . '/payment_methods';
        
        $data = [
            'data' => [
                'attributes' => [
                    'type' => $type,
                    'details' => $details
                ]
            ]
        ];
        
        return $this->makeRequest('POST', $url, $data);
    }
    
    public function attachPaymentMethod($paymentIntentId, $paymentMethodId, $returnUrl) {
        $url = $this->apiUrl . '/payment_intents/' . $paymentIntentId . '/attach';
        
        $data = [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'return_url' => $returnUrl
                ]
            ]
        ];
        
        return $this->makeRequest('POST', $url, $data);
    }
    
    public function retrievePaymentIntent($id) {
        $url = $this->apiUrl . '/payment_intents/' . $id;
        return $this->makeRequest('GET', $url);
    }
    
    private function makeRequest($method, $url, $data = null) {
        $ch = curl_init($url);
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->secretKey . ':')
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('PayMongo cURL Error: ' . $error);
            throw new Exception('Payment processing error. Please try again later.');
        }
        
        if (empty($response)) {
            error_log('PayMongo API returned empty response. HTTP Code: ' . $httpCode);
            throw new Exception('Empty response from payment processor. Please try again.');
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('PayMongo JSON decode error: ' . json_last_error_msg() . ' - Response: ' . $response);
            throw new Exception('Invalid response from payment processor. Please try again.');
        }
        
        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = $result['errors'][0]['detail'] ?? 'Payment processing failed';
            error_log('PayMongo API Error (' . $httpCode . '): ' . $errorMsg);
            throw new Exception($errorMsg);
        }
        
        if ($httpCode >= 400) {
            $errorMsg = isset($result['errors'][0]['detail']) ? 
                       $result['errors'][0]['detail'] : 'Unknown error occurred';
            throw new Exception('PayMongo API Error: ' . $errorMsg);
        }
        
        return $result;
    }
}

        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('PayMongo JSON decode error: ' . json_last_error_msg() . ' - Response: ' . $response);
            throw new Exception('Invalid response from payment processor. Please try again.');
        }
        
        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = $result['errors'][0]['detail'] ?? 'Payment processing failed';
            error_log('PayMongo API Error (' . $httpCode . '): ' . $errorMsg);
            throw new Exception($errorMsg);
        }
        
        if ($httpCode >= 400) {
            $errorMsg = isset($result['errors'][0]['detail']) ? 
                       $result['errors'][0]['detail'] : 'Unknown error occurred';
            throw new Exception('PayMongo API Error: ' . $errorMsg);
        }
        
        return $result;
    }
}
?>
