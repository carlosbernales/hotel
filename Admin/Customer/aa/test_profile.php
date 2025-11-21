<?php
require_once 'db_con.php';
session_start();

echo "<pre>";
echo "Session Data:\n";
print_r($_SESSION);
echo "\nDatabase Connection:\n";
var_dump($pdo);
echo "</pre>"; 