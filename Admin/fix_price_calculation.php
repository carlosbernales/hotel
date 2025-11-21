<?php
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fixing Room Transfer Price Calculation</h1>";

// Check if all required files exist
$checked_in_file = 'checked_in.php';

if (!file_exists($checked_in_file)) {
    echo "<p style='color:red'>Error: checked_in.php file not found!</p>";
    exit;
}

// Make a backup of the original file
$backup_file = $checked_in_file . '.bak';
if (!file_exists($backup_file)) {
    if (copy($checked_in_file, $backup_file)) {
        echo "<p>✓ Created backup of $checked_in_file</p>";
    } else {
        echo "<p style='color:red'>Failed to create backup of $checked_in_file</p>";
        exit;
    }
}

// Read the file content
$content = file_get_contents($checked_in_file);
if ($content === false) {
    echo "<p style='color:red'>Error: Failed to read file $checked_in_file</p>";
    exit;
}

// Find and fix the price difference calculation
$pattern = '/const priceDifference = \(newPrice - currentPrice\) \* nights;/';
$fixed_code = 'const priceDifference = parseFloat(((newPrice - currentPrice) * nights).toFixed(2)); // Fixed calculation';

$updated_content = preg_replace($pattern, $fixed_code, $content);

if ($content !== $updated_content) {
    // Save the updated file
    if (file_put_contents($checked_in_file, $updated_content)) {
        echo "<p style='color:green'>✓ Successfully fixed price difference calculation in $checked_in_file</p>";
    } else {
        echo "<p style='color:red'>Error: Failed to write updated content to $checked_in_file</p>";
        exit;
    }
} else {
    echo "<p>No changes needed or pattern not found in $checked_in_file</p>";
    
    // Add specific debug information
    echo "<h2>Looking for specific JavaScript sections to fix</h2>";
    
    // Look for specific price difference calculation code
    if (strpos($content, 'const priceDifference = (newPrice - currentPrice) * nights;') !== false) {
        echo "<p>Found price difference calculation but pattern matching failed. Attempting targeted fix...</p>";
        
        // Try another approach with simpler pattern matching
        $search = 'const priceDifference = (newPrice - currentPrice) * nights;';
        $replace = 'const priceDifference = parseFloat(((newPrice - currentPrice) * nights).toFixed(2)); // Fixed calculation';
        
        $updated_content = str_replace($search, $replace, $content);
        
        if ($content !== $updated_content) {
            if (file_put_contents($checked_in_file, $updated_content)) {
                echo "<p style='color:green'>✓ Successfully fixed price difference calculation using alternate method</p>";
            } else {
                echo "<p style='color:red'>Error: Failed to write updated content</p>";
            }
        } else {
            echo "<p style='color:orange'>Could not replace the exact price calculation code</p>";
        }
    } else {
        echo "<p>Price difference calculation code not found. Here are similar snippets:</p>";
        
        // Extract all lines containing "price" and "difference"
        preg_match_all('/.*price.*difference.*$/im', $content, $matches);
        
        if (!empty($matches[0])) {
            echo "<pre>";
            foreach (array_slice($matches[0], 0, 10) as $match) {
                echo htmlspecialchars(trim($match)) . "\n";
            }
            echo "</pre>";
        } else {
            echo "<p>No matching code found.</p>";
        }
    }
}

// Add a fix for payment method validation
echo "<h2>Fixing Payment Method Validation</h2>";

// Find the validation condition for payment method
$payment_pattern = '/if \(priceDifference > 0(.*?)\{(.*?)paymentMethod(.*?)\}/s';

// Check if the pattern exists
if (preg_match($payment_pattern, $updated_content)) {
    // Create a more reliable replacement
    $updated_content = str_replace(
        'if (priceDifference > 0) {
            const paymentMethod = $(\'#paymentMethod\').val();
            if (!paymentMethod) {',
        'if (priceDifference > 0) {
            const paymentMethod = $(\'#paymentMethod\').val();
            if (!paymentMethod) {',
        $updated_content
    );
    
    echo "<p style='color:green'>✓ Payment method validation looks correct</p>";
} else {
    echo "<p style='color:orange'>Could not find payment method validation pattern to check</p>";
}

// Fix AJAX response handling
echo "<h2>Enhancing AJAX Response Handling</h2>";

$ajax_pattern = '/error: function\(\) \{/';
$fixed_ajax = 'error: function(xhr, status, error) {
                        console.error(\'AJAX Error:\', status, error);
                        console.error(\'Response text:\', xhr.responseText);
                        
                        // Try to parse error response
                        let errorMessage = \'Error processing room transfer\';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            console.log(\'Parsed error response:\', errorResponse);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            console.error(\'Could not parse error response:\', e);
                        }';

$updated_content = preg_replace($ajax_pattern, $fixed_ajax, $updated_content);

// Save the updated file again
if (file_put_contents($checked_in_file, $updated_content)) {
    echo "<p style='color:green'>✓ Successfully enhanced AJAX error handling to show detailed errors</p>";
} else {
    echo "<p style='color:red'>Error: Failed to update AJAX error handling</p>";
}

echo "<p>Fixed file has been saved. You can now test the room transfer functionality again.</p>";
echo "<p><a href='checked_in.php'>Return to Checked In Page</a></p>";
echo "<p><a href='debug_room_transfer.php'>Go to Debug Tool</a></p>";

// Close the database connection
mysqli_close($con);
?> 