<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db_con.php';
session_start();

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Database Connection Test:</h2>";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful!";
    
    echo "<h2>Users Table Structure:</h2>";
    $stmt = $pdo->query("DESCRIBE users");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
    
    if(isset($_SESSION['userid'])) {
        echo "<h2>Current User Data:</h2>";
        $stmt = $pdo->prepare("SELECT * FROM users WHERE userid = ?");
        $stmt->execute([$_SESSION['userid']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "<h2>No user logged in</h2>";
    }
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?> 