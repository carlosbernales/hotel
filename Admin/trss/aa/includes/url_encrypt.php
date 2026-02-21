<?php
// Encryption key - make sure this is kept secret
// In production, store this in your environment variables or config file
if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', 'Capstone@2025'); // Change this to a strong secret key
}

// Encryption method
if (!defined('ENCRYPTION_METHOD')) {
    define('ENCRYPTION_METHOD', 'AES-256-CBC');
}

function encryptURL($data) {
    $ivlen = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt(
        json_encode($data),
        ENCRYPTION_METHOD,
        ENCRYPTION_KEY,
        $options=OPENSSL_RAW_DATA,
        $iv
    );
    $hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
    $encrypted = base64_encode($iv.$hmac.$ciphertext_raw);
    return rtrim(strtr($encrypted, '+/', '-_'), '=');
}

function decryptURL($encrypted) {
    $encrypted = str_replace(array('-', '_'), array('+', '/'), $encrypted);
    $c = base64_decode($encrypted);
    $ivlen = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $original_plaintext = openssl_decrypt(
        $ciphertext_raw,
        ENCRYPTION_METHOD,
        ENCRYPTION_KEY,
        $options=OPENSSL_RAW_DATA,
        $iv
    );
    $calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, $as_binary=true);
    if (!hash_equals($hmac, $calcmac)) {
        return null; // HMAC verification failed
    }
    return json_decode($original_plaintext, true);
}

// Function to generate encrypted URL with full path support
function getEncryptedURL($path, $params = array()) {
    // Remove any leading slashes and .php extension
    $path = ltrim($path, '/');
    if (substr($path, -4) === '.php') {
        $path = substr($path, 0, -4);
    }
    
    // Prepare data array
    $data = array('path' => $path);
    if (!empty($params)) {
        $data = array_merge($data, $params);
    }
    
    // Encrypt and return the full URL
    $encrypted = encryptURL($data);
    return 'e/' . $encrypted;
}

// Function to get the current path for encryption
function getCurrentPath() {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $path = ltrim(dirname($script_name), '/');
    $filename = basename($script_name);
    if ($filename === 'index.php' || $filename === 'index') {
        return $path . '/';
    }
    return $path . '/' . $filename;
}

function processEncryptedURL() {
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path_parts = explode('/', trim($request_uri, '/'));
    
    if (count($path_parts) >= 2 && $path_parts[0] === 'e') {
        $encrypted = $path_parts[1];
        $data = decryptURL($encrypted);
        
        if ($data && isset($data['path'])) {
            $path = $data['path'];
            unset($data['p']);
            
            // Set GET parameters
            foreach ($data as $key => $value) {
                $_GET[$key] = $value;
            }
            
            // Include the requested page
            $page_path = __DIR__ . '/../' . $page . '.php';
            if (file_exists($page_path)) {
                include($page_path);
                exit();
            }
        }
        
        // If we get here, decryption or page loading failed
        header('HTTP/1.0 404 Not Found');
        include('404.php');
        exit();
    }
}
