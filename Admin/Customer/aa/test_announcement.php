<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_con.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px;'>";

try {
    // Check database connection
    if (!isset($pdo)) {
        die("<p style='color: red;'>Database connection failed. Check your db_con.php file.</p>");
    }
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if the announcement table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'announcement'");
    $tableExists = $tableCheck->rowCount() > 0;
    
    if (!$tableExists) {
        die("<h2>Database Test</h2><p style='color: red;'>The 'announcement' table does not exist in the database.</p>");
    }
    
    echo "<h2>Database Connection Test</h2>";
    echo "<p style='color: green;'>✓ Connected to database successfully!</p>";
    echo "<p>Announcement table exists: <span style='color: green;'>✓ Yes</span></p>";
    
    // Get current date for reference
    $currentDate = date('Y-m-d H:i:s');
    echo "<p>Current server time: {$currentDate}</p>";
    
    // First, show all announcements for reference
    $allAnnouncements = $pdo->query("SELECT id, message, is_active, start_date, end_date, text_color, background_color, speed FROM announcement")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>All Announcements in Database:</h3>";
    if (empty($allAnnouncements)) {
        echo "<p>No announcements found in the database.</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
        echo "<tr><th>ID</th><th>Message</th><th>Active</th><th>Start Date</th><th>End Date</th><th>Text Color</th><th>BG Color</th><th>Speed</th></tr>";
        foreach ($allAnnouncements as $ann) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($ann['id']) . "</td>";
            echo "<td>" . htmlspecialchars($ann['message']) . "</td>";
            echo "<td>" . ($ann['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . $ann['start_date'] . "</td>";
            echo "<td>" . $ann['end_date'] . "</td>";
            echo "<td style='background-color: " . ($ann['text_color'] ?: '#ffffff') . "'>" . ($ann['text_color'] ?: 'default') . "</td>";
            echo "<td style='background-color: " . ($ann['background_color'] ?: '#c62828') . "'>" . ($ann['background_color'] ?: 'default') . "</td>";
            echo "<td>" . ($ann['speed'] ?: 'default') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Now check for active announcements
    $sql = "SELECT * FROM announcement 
            WHERE is_active = 1 
            AND (start_date IS NULL OR start_date <= :currentDate)
            AND (end_date IS NULL OR end_date >= :currentDate)
            ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Active Announcements (Matching Current Date):</h3>";
    if (empty($announcements)) {
        echo "<p style='color: orange;'>No active announcements found that match the current date range.</p>";
    } else {
        echo "<p style='color: green;'>Found " . count($announcements) . " active announcement(s).</p>";
        echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        foreach ($announcements as $ann) {
            $style = "";
            if ($ann['background_color']) {
                $style .= "background-color: {$ann['background_color']}; ";
            }
            if ($ann['text_color']) {
                $style .= "color: {$ann['text_color']}; ";
            }
            echo "<div style='padding: 10px; margin: 5px 0; {$style}'>";
            echo htmlspecialchars($ann['message']);
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<h3>Active Announcements:</h3>";
        echo "<pre>";
        print_r($announcements);
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
?>
