<?php
require_once 'includes/url_encrypt.php';

// Process the encrypted URL
$data = processEncryptedURL();

if ($data) {
    // Get the path from the decrypted data
    $path = $data['path'] ?? 'index';
    unset($data['path']);
    
    // Set any additional parameters as GET variables
    if (!empty($data)) {
        $_GET = array_merge($_GET, $data);
    }
    
    // Build the full path to the requested file
    $base_path = rtrim(__DIR__, '/');
    $path = ltrim($path, '/');
    
    // Check for .php extension
    $has_extension = (substr($path, -4) === '.php');
    $path_without_ext = $has_extension ? substr($path, 0, -4) : $path;
    
    // Clean up the path to prevent directory traversal
    $path_parts = [];
    foreach (explode('/', $path_without_ext) as $part) {
        if ($part === '..' || $part === '.') continue;
        if (empty($part)) continue;
        $path_parts[] = $part;
    }
    
    $clean_path = implode('/', $path_parts);
    $full_path = $base_path . '/' . $clean_path . '.php';
    
    // Check if the file exists and is within the document root for security
    $real_base = realpath($base_path);
    $real_path = realpath(dirname($full_path));
    
    if ($real_path === false || strpos($real_path, $real_base) !== 0) {
        // Path traversal attempt or file doesn't exist
        header('HTTP/1.0 404 Not Found');
        include $base_path . '/404.php';
        exit();
    }
    
    // Check if the file exists and is a PHP file
    if (is_file($full_path) && pathinfo($full_path, PATHINFO_EXTENSION) === 'php') {
        // Start output buffering
        ob_start();
        
        // Include the requested file
        include $full_path;
        
        // Flush the output buffer
        ob_end_flush();
    } else {
        // File not found or not a PHP file
        header('HTTP/1.0 404 Not Found');
        if (file_exists(__DIR__ . '/404.php')) {
            include __DIR__ . '/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>The requested page could not be found.</p>';
        }
        exit();
    }
} else {
    // Invalid or missing encrypted data
    header('HTTP/1.0 404 Not Found');
    if (file_exists(__DIR__ . '/404.php')) {
        include __DIR__ . '/404.php';
    } else {
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The requested page could not be found.</p>';
    }
    exit();
}
?>
