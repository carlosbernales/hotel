<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'db.php';

// Function to display table info
function displayTableInfo($con, $tableName) {
    echo "<h2>Table Structure for $tableName</h2>";
    
    // Get table columns
    $columnsQuery = "SHOW COLUMNS FROM $tableName";
    $columnsResult = mysqli_query($con, $columnsQuery);
    
    if (!$columnsResult) {
        echo "<p>Error getting columns: " . mysqli_error($con) . "</p>";
        return;
    }
    
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($column = mysqli_fetch_assoc($columnsResult)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Get a sample of the data
    $sampleQuery = "SELECT * FROM $tableName LIMIT 5";
    $sampleResult = mysqli_query($con, $sampleQuery);
    
    if (!$sampleResult) {
        echo "<p>Error getting sample data: " . mysqli_error($con) . "</p>";
        return;
    }
    
    if (mysqli_num_rows($sampleResult) > 0) {
        echo "<h3>Sample Data</h3>";
        echo "<table border='1'>";
        
        // Table header
        echo "<tr>";
        $fields = mysqli_fetch_fields($sampleResult);
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Table data
        mysqli_data_seek($sampleResult, 0);
        while ($row = mysqli_fetch_assoc($sampleResult)) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No sample data available.</p>";
    }
}

// Display header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Debug Information</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; }
        h2, h3 { margin-top: 30px; }
    </style>
</head>
<body>";

// Test database connection
echo "<h1>Database Connection Test</h1>";
if ($con) {
    echo "<p style='color: green;'>Database connection successful!</p>";
} else {
    echo "<p style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</p>";
    exit;
}

// Show database information
$dbInfo = mysqli_get_server_info($con);
echo "<p><strong>Database Server:</strong> $dbInfo</p>";

// Show table info for userss
displayTableInfo($con, 'userss');

// Test insert query to userss table
echo "<h2>Test INSERT Query</h2>";

// Create a temporary test user
$testFirst = "Test_" . time();
$testLast = "User_" . time();
$testEmail = "testuser_" . time() . "@example.com";
$testPassword = "password123";
$testUserType = "admin";

// Construct the same INSERT query we use in add_user.php
$insertQuery = "INSERT INTO userss (first_name, last_name, email, password, actual_password, 
                contact_number, address, user_type, name) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$insertStmt = $con->prepare($insertQuery);
if (!$insertStmt) {
    echo "<p style='color: red;'>Prepare failed: " . $con->error . "</p>";
} else {
    $contactNumber = "1234567890";
    $address = "Test Address";
    $name = "$testFirst $testLast";
    
    $insertStmt->bind_param("sssssssss", 
        $testFirst, 
        $testLast, 
        $testEmail, 
        $testPassword,
        $testPassword,
        $contactNumber,
        $address,
        $testUserType,
        $name
    );
    
    if ($insertStmt->execute()) {
        $newId = $insertStmt->insert_id;
        echo "<p style='color: green;'>Test INSERT successful! New ID: $newId</p>";
        
        // Clean up - delete the test user
        $deleteQuery = "DELETE FROM userss WHERE id = ?";
        $deleteStmt = $con->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $newId);
        if ($deleteStmt->execute()) {
            echo "<p>Test user deleted successfully.</p>";
        } else {
            echo "<p style='color: orange;'>Warning: Could not delete test user: " . $deleteStmt->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Test INSERT failed: " . $insertStmt->error . "</p>";
        
        // Try to pinpoint the issue
        echo "<p>Debugging information:</p>";
        echo "<ul>";
        echo "<li>Query: " . htmlspecialchars($insertQuery) . "</li>";
        echo "<li>First name: " . htmlspecialchars($testFirst) . "</li>";
        echo "<li>Last name: " . htmlspecialchars($testLast) . "</li>";
        echo "<li>Email: " . htmlspecialchars($testEmail) . "</li>";
        echo "<li>User type: " . htmlspecialchars($testUserType) . "</li>";
        echo "</ul>";
        
        // Show existing record for comparison
        $existingQuery = "SELECT * FROM userss LIMIT 1";
        $existingResult = mysqli_query($con, $existingQuery);
        if ($existingResult && mysqli_num_rows($existingResult) > 0) {
            $existingRow = mysqli_fetch_assoc($existingResult);
            echo "<p>Sample existing record structure:</p>";
            echo "<pre>" . print_r($existingRow, true) . "</pre>";
        }
    }
}

echo "</body></html>";
?> 