<?php
// This script fixes the path in index.php
echo "<h1>Fixing index.php</h1>";

// Path to the index.php file
$indexFile = "index.php";

if (file_exists($indexFile)) {
    // Read the content of the file
    $content = file_get_contents($indexFile);
    
    // Check if the incorrect path exists
    if (strpos($content, "require_once 'Admin/session_check.php'") !== false) {
        // Replace incorrect path with correct one
        $content = str_replace(
            "require_once 'Admin/session_check.php'", 
            "require_once 'session_check.php'", 
            $content
        );
        
        // Save the file
        if (file_put_contents($indexFile, $content)) {
            echo "<p style='color: green;'>✅ Successfully updated index.php. The path to session_check.php has been fixed.</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update index.php. Make sure the file is writable.</p>";
        }
    } else {
        echo "<p>Looking for alternative require statement...</p>";
        
        // Check for other possible ways the require might be written
        if (strpos($content, "require_once(\"Admin/session_check.php\")") !== false) {
            $content = str_replace(
                "require_once(\"Admin/session_check.php\")", 
                "require_once(\"session_check.php\")", 
                $content
            );
            
            if (file_put_contents($indexFile, $content)) {
                echo "<p style='color: green;'>✅ Successfully updated index.php. The path to session_check.php has been fixed.</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update index.php. Make sure the file is writable.</p>";
            }
        }
        else if (strpos($content, "require_once('Admin/session_check.php')") !== false) {
            $content = str_replace(
                "require_once('Admin/session_check.php')", 
                "require_once('session_check.php')", 
                $content
            );
            
            if (file_put_contents($indexFile, $content)) {
                echo "<p style='color: green;'>✅ Successfully updated index.php. The path to session_check.php has been fixed.</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update index.php. Make sure the file is writable.</p>";
            }
        }
        else {
            echo "<p style='color: orange;'>⚠️ Could not find the incorrect path in index.php.</p>";
            echo "<p>Current content of index.php:</p>";
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Could not find index.php in the current directory.</p>";
}

echo "<p><a href='index.php'>Go back to index.php</a></p>";
?> 