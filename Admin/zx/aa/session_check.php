<?php
session_start();
echo "<h2>Current Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Database Check:</h2>";
require_once 'db_con.php';

try {
    $stmt = $pdo->query("DESCRIBE users");
    echo "<h3>Table Structure:</h3>";
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";

    if(isset($_SESSION['userid'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE userid = ?");
        $stmt->execute([$_SESSION['userid']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h3>User Data:</h3>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 